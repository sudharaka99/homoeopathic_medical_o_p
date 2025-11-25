@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Manage Patients</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('admin.slidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')

                <div class="card border-0 shadow mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="mb-0">Manage Patients</h2>
                                <p class="text-muted mb-0">
                                    Total Patients: {{ $patients->total() }}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Search Form -->
                                <form method="GET" action="{{ route('admin.patients') }}" class="d-flex">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control form-control-sm" 
                                               placeholder="Search patients..." value="{{ request('search') }}">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Filter Active Indicator -->
                        @if(request('search'))
                        <div class="alert alert-info py-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-search me-2"></i>
                                    Showing results for "<strong>{{ request('search') }}</strong>"
                                    <span class="badge bg-primary ms-2">{{ $patients->count() }} patients</span>
                                </div>
                                <a href="{{ route('admin.patients') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-times me-1"></i>Clear Search
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($patients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Contact</th>
                                        <th>Designation</th>
                                        <th>Registered</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patients as $patient)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                {{-- @if($patient->image)
                                                    <img src="{{ asset('storage/' . $patient->image) }}" 
                                                         class="rounded-circle"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold; font-size: 1.2rem;">
                                                        {{ substr(trim($patient->name), 0, 1) }}
                                                    </div>
                                                @endif --}}
                                                    @if($patient->image)
                                                        <img src="{{ asset('profile_pic/thumb/' . $patient->image) }}"
                                                             class="rounded-circle patient-avatar" style="width: 50px; height: 50px; object-fit: cover;>
                                                    @else
                                                        <img src="{{ asset('assets/images/avatar7.png') }}"
                                                             class="rounded-circle patient-avatar" style="width: 50px; height: 50px; object-fit: cover;>
                                                    @endif

                                                <div>
                                                    <h6 class="mb-1 fw-semibold">{{ $patient->name }}</h6>
                                                    <p class="text-muted mb-0 small">{{ $patient->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="small">
                                                @if($patient->mobile)
                                                    <div><i class="fas fa-phone me-2 text-success"></i>{{ $patient->mobile }}</div>
                                                @else
                                                    <div class="text-muted">No phone</div>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="fw-semibold">{{ $patient->designation ?? 'Not specified' }}</span>
                                        </td>
                                        
                                        <td>
                                            <div class="small">
                                                <div class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($patient->created_at)->format('M d, Y') }}
                                                </div>
                                                <div class="text-muted">
                                                    {{ \Carbon\Carbon::parse($patient->created_at)->format('h:i A') }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" 
                                                           href="{{ route('admin.patients.edit', $patient->id) }}">
                                                            <i class="fas fa-edit text-primary me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger delete-patient-btn" 
                                                                data-patient-id="{{ $patient->id }}"
                                                                data-patient-name="{{ $patient->name }}">
                                                            <i class="fas fa-trash text-danger me-2"></i>Delete
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} results
                            </div>
                            <nav>
                                {{ $patients->appends(request()->query())->links() }}
                            </nav>
                        </div>
                        
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="fas fa-user-injured text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">
                                @if(request('search'))
                                    No Patients Found
                                @else
                                    No Patients Registered
                                @endif
                            </h4>
                            <p class="text-muted mb-4">
                                @if(request('search'))
                                    No patients found matching your search criteria.
                                @else
                                    There are no patients registered in the system yet.
                                @endif
                            </p>
                            @if(request('search'))
                            <a href="{{ route('admin.patients') }}" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>View All Patients
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Delete Patient
    $('.delete-patient-btn').on('click', function() {
        const patientId = $(this).data('patient-id');
        const patientName = $(this).data('patient-name');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete patient ${patientName}. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/patients') }}/" + patientId,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete patient.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });
});
</script>

<style>
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.table > :not(caption) > * > * {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.empty-state-icon {
    opacity: 0.6;
}
</style>
@endsection