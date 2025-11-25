@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Manage Doctors</li>
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
                                <h2 class="mb-0">Manage Doctors</h2>
                                <p class="text-muted mb-0 small">
                                    Total: {{ $totalDoctors }} | 
                                    Approved: {{ $approvedDoctors }} | 
                                    Pending: {{ $pendingDoctors }} |
                                    Disabled: {{ $disabledDoctors }}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Search Form -->
                                <form method="GET" action="{{ route('admin.doctorslist') }}" class="d-flex">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control form-control-sm" 
                                               placeholder="Search doctors..." value="{{ request('search') }}">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>

                                <!-- Filter Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-filter me-1"></i>
                                        @if(request('status') == 'approved')
                                            Approved
                                        @elseif(request('status') == 'pending')
                                            Pending
                                        @elseif(request('status') == 'disabled')
                                            Disabled
                                        @else
                                            All Status
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item {{ !request('status') ? 'active' : '' }}" 
                                               href="{{ route('admin.doctorslist') }}">
                                                <i class="fas fa-list me-2"></i>All Doctors
                                                <span class="badge bg-secondary float-end">{{ $totalDoctors }}</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item {{ request('status') == 'approved' ? 'active' : '' }}" 
                                               href="{{ route('admin.doctorslist', ['status' => 'approved']) }}">
                                                <i class="fas fa-check-circle me-2 text-success"></i>Approved
                                                <span class="badge bg-success float-end">{{ $approvedDoctors }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}" 
                                               href="{{ route('admin.doctorslist', ['status' => 'pending']) }}">
                                                <i class="fas fa-clock me-2 text-warning"></i>Pending
                                                <span class="badge bg-warning float-end">{{ $pendingDoctors }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item {{ request('status') == 'disabled' ? 'active' : '' }}" 
                                               href="{{ route('admin.doctorslist', ['status' => 'disabled']) }}">
                                                <i class="fas fa-times-circle me-2 text-danger"></i>Disabled
                                                <span class="badge bg-danger float-end">{{ $disabledDoctors }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Active Indicator -->
                        @if(request('search') || request('status'))
                        <div class="alert alert-info py-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-filter me-2"></i>
                                    Showing 
                                    @if(request('search'))
                                        results for "<strong>{{ request('search') }}</strong>"
                                    @endif
                                    @if(request('search') && request('status')) and @endif
                                    @if(request('status'))
                                        <strong>{{ ucfirst(request('status')) }} Doctors</strong>
                                    @endif
                                    <span class="badge bg-primary ms-2">{{ $doctors->count() }} doctors</span>
                                </div>
                                <a href="{{ route('admin.doctorslist') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($doctors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Specialization</th>
                                        <th>Contact</th>
                                        <th>License Image</th>
                                        <th>Clinic</th>
                                        <th>Fee</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctors as $doctor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($doctor->profile_image)
                                                    <img src="{{ asset('profile_pic/thumb/' . $doctor->profile_image) }}" 
                                                         class="rounded-circle doctor-avatar"
                                                         alt="{{ $doctor->doctor_name }}"
                                                         style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #e9ecef;">
                                                @else
                                                    <div class="rounded-circle doctor-avatar-placeholder d-flex align-items-center justify-content-center"
                                                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold; font-size: 1.2rem; border: 3px solid #e9ecef;">
                                                        {{ substr(trim($doctor->doctor_name), 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold">{{ $doctor->doctor_name }}</h6>
                                                    <p class="text-muted mb-1 small">
                                                        <i class="fas fa-graduation-cap me-1 text-primary"></i>{{ $doctor->qualification }}
                                                    </p>
                                                    <p class="text-muted mb-0 small">
                                                        <i class="fas fa-briefcase me-1 text-info"></i>{{ $doctor->years_experience }} years exp.
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $doctor->specialization_name ?? 'N/A' }}</span>
                                        </td>
                                        
                                        <td>
                                            <div class="small">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="fas fa-envelope me-2 text-primary" style="width: 16px;"></i>
                                                    <span class="text-truncate" style="max-width: 150px;" title="{{ $doctor->email }}">
                                                        {{ $doctor->email }}
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-phone me-2 text-success" style="width: 16px;"></i>
                                                    <span>{{ $doctor->mobile }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- License Image Column -->
                                        <td>
                                            @if($doctor->license_image)
                                                <div class="text-center">
                                                    <img src="{{ asset('storage/' . $doctor->license_image) }}" 
                                                         class="img-thumbnail license-image" 
                                                         width="60" 
                                                         height="60" 
                                                         alt="License"
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#licenseModal"
                                                         onclick="showLicenseImage('{{ asset('storage/' . $doctor->license_image) }}', '{{ $doctor->doctor_name }}')">
                                                    <div class="small text-muted mt-1">Click to View</div>
                                                </div>
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-file-image fa-2x opacity-50"></i>
                                                    <div class="small mt-1">No License</div>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="small">
                                                <div class="fw-semibold text-dark">{{ $doctor->clinic_name }}</div>
                                                <div class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $doctor->district }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="fw-bold text-success">RS.{{ number_format($doctor->appointment_fee, 2) }}</span>
                                        </td>
                                        
                                        <td>
                                            <span class="badge status-badge 
                                                @if($doctor->is_admin_confirmed == 1) bg-success
                                                @elseif($doctor->is_admin_confirmed == 0) bg-warning text-dark
                                                @elseif($doctor->is_admin_confirmed == 2) bg-danger
                                                @endif">
                                                @if($doctor->is_admin_confirmed == 1)
                                                    <i class="fas fa-check me-1"></i>Approved
                                                @elseif($doctor->is_admin_confirmed == 0)
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                @elseif($doctor->is_admin_confirmed == 2)
                                                    <i class="fas fa-times me-1"></i>Disabled
                                                @endif
                                            </span>
                                            @if($doctor->reject_reason && $doctor->is_admin_confirmed == 2)
                                                <small class="d-block text-muted mt-1" title="{{ $doctor->reject_reason }}">
                                                    <i class="fas fa-info-circle me-1"></i>{{ Str::limit($doctor->reject_reason, 30) }}
                                                </small>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog me-1"></i>Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" 
                                                           href="{{ route('admin.doctor.edit', $doctor->id) }}">
                                                            <i class="fas fa-edit text-primary me-2"></i>Edit Profile
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item toggle-status-btn" 
                                                                data-doctor-id="{{ $doctor->id }}"
                                                                data-current-status="{{ $doctor->is_admin_confirmed }}">
                                                            @if($doctor->is_admin_confirmed == 1)
                                                                <i class="fas fa-times text-warning me-2"></i>Disable Account
                                                            @elseif($doctor->is_admin_confirmed == 0)
                                                                <i class="fas fa-check text-success me-2"></i>Approve Account
                                                            @elseif($doctor->is_admin_confirmed == 2)
                                                                <i class="fas fa-check text-success me-2"></i>Enable Account
                                                            @endif
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger delete-doctor-btn" 
                                                                data-doctor-id="{{ $doctor->id }}"
                                                                data-doctor-name="{{ $doctor->doctor_name }}">
                                                            <i class="fas fa-trash text-danger me-2"></i>Delete Permanently
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
                            <div class="text-muted small">
                                Showing {{ $doctors->firstItem() }} to {{ $doctors->lastItem() }} of {{ $doctors->total() }} results
                            </div>
                            <nav>
                                {{ $doctors->appends(request()->query())->links() }}
                            </nav>
                        </div>
                        
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="fas fa-user-md text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">
                                @if(request('search') || request('status'))
                                    No Doctors Found
                                @else
                                    No Doctors Registered
                                @endif
                            </h4>
                            <p class="text-muted mb-4">
                                @if(request('search'))
                                    No doctors found matching your search criteria.
                                @elseif(request('status'))
                                    There are no {{ request('status') }} doctors at the moment.
                                @else
                                    There are no doctors registered in the system yet.
                                @endif
                            </p>
                            @if(request('search') || request('status'))
                            <a href="{{ route('admin.doctorslist') }}" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>View All Doctors
                            </a>
                            @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-dashboard me-2"></i>Go to Dashboard
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

<!-- License Image Modal -->
<div class="modal fade" id="licenseModal" tabindex="-1" aria-labelledby="licenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="licenseModalLabel">License Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="licenseImage" src="" alt="License Document" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <a id="downloadLicense" href="#" class="btn btn-success" download>
                    <i class="fas fa-download me-2"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Toggle Doctor Status
    $('.toggle-status-btn').on('click', function() {
        const doctorId = $(this).data('doctor-id');
        const currentStatus = $(this).data('current-status');
        const doctorName = $(this).closest('tr').find('h6').text();
        
        const actionText = currentStatus == 1 ? 'disable' : 'enable';
        
        Swal.fire({
            title: `Are you sure?`,
            text: `You are about to ${actionText} Dr. ${doctorName}.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${actionText} it!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/doctor') }}/" + doctorId + "/toggle-status",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
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
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update doctor status.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    // Delete Doctor
    $('.delete-doctor-btn').on('click', function() {
        const doctorId = $(this).data('doctor-id');
        const doctorName = $(this).data('doctor-name');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete Dr. ${doctorName}. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/doctor') }}/" + doctorId,
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
                        let errorMessage = 'Failed to delete doctor.';
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

// License Image Modal Function
function showLicenseImage(imageSrc, doctorName) {
    document.getElementById('licenseImage').src = imageSrc;
    document.getElementById('downloadLicense').href = imageSrc;
    document.getElementById('downloadLicense').download = 'license_' + doctorName.replace(/\s+/g, '_') + '.jpg';
}
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
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.btn-group .dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
}

.empty-state-icon {
    opacity: 0.6;
}

.bg-warning { 
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important; 
    color: #000 !important;
}
.bg-success { 
    background: linear-gradient(135deg, #198754 0%, #157347 100%) !important; 
}
.bg-danger { 
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important; 
}

.dropdown-item.active {
    background-color: #0d6efd;
    color: white;
}

.license-image {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid #dee2e6;
}

.license-image:hover {
    transform: scale(1.1);
    border-color: #0d6efd;
    box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.15);
}

.doctor-avatar {
    transition: all 0.3s ease;
    border: 3px solid #e9ecef;
}

.doctor-avatar:hover {
    border-color: #0d6efd;
    transform: scale(1.05);
}

.doctor-avatar-placeholder {
    transition: all 0.3s ease;
}

.doctor-avatar-placeholder:hover {
    transform: scale(1.05);
}

.status-badge {
    font-size: 0.7rem;
    padding: 0.35rem 0.65rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .doctor-avatar,
    .doctor-avatar-placeholder {
        width: 45px !important;
        height: 45px !important;
    }
    
    .license-image {
        width: 45px !important;
        height: 45px !important;
    }
}

/* Custom scrollbar for table */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection