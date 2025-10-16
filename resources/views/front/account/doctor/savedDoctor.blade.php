@extends('front.layouts.app')

@section('main')

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.slidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Saved Doctors</h3>
                                <p class="text-muted mb-0">Your personalized healthcare providers list</p>
                            </div>
                            <div class="text-muted">
                                Total Saved: {{ $savedDoctors->total() }}
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Doctor Information</th>
                                        <th scope="col">Specialization</th>
                                        <th scope="col">Qualification</th>
                                        <th scope="col">Experience</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @if($savedDoctors->isNotEmpty())
                                        @foreach($savedDoctors as $savedDoctor)
                                        <tr class="active">
                                            <td>
                                                <div class="doctor-info">
                                                    <div class="doctor-name fw-500 text-dark mb-1">{{ $savedDoctor->doctor_name }}</div>
                                                    <div class="clinic-info text-muted small mb-2">
                                                        <i class="fa fa-hospital me-1"></i>{{ $savedDoctor->clinic_name ?? 'Private Practice' }}
                                                    </div>
                                                    @if($savedDoctor->save_reason)
                                                        <div class="save-reason-container bg-light rounded p-2 border">
                                                            <div class="d-flex align-items-start">
                                                                <i class="fa fa-sticky-note text-primary mt-1 me-2"></i>
                                                                <div>
                                                                    <small class="fw-500 text-dark">Why I saved:</small>
                                                                    <p class="mb-0 text-muted small">{{ $savedDoctor->save_reason }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="no-reason text-muted small">
                                                            <i class="fa fa-info-circle me-1"></i>No reason provided
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="specialization-badge">{{ $savedDoctor->specialization ?? 'General' }}</span>
                                            </td>
                                            <td>
                                                <div class="qualification-text">
                                                    {{ $savedDoctor->qualification ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="experience-info">
                                                    @if($savedDoctor->years_experience)
                                                        <span class="text-success fw-500">{{ $savedDoctor->years_experience }} years</span>
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge category-badge bg-{{ $savedDoctor->save_category == 'favorite' ? 'danger' : ($savedDoctor->save_category == 'consult_later' ? 'primary' : 'warning') }}">
                                                    <i class="fa fa-{{ $savedDoctor->save_category == 'favorite' ? 'heart' : ($savedDoctor->save_category == 'consult_later' ? 'calendar-check' : 'bookmark') }} me-1"></i>
                                                    {{ $savedDoctor->save_category == 'favorite' ? 'Favorite' : ($savedDoctor->save_category == 'consult_later' ? 'Plan to Consult' : 'Reference') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-dots">
                                                    <button href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('doctor.details', $savedDoctor->doctor_id) }}">
                                                                <i class="fa fa-eye text-primary me-2"></i> View Profile
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="bookAppointment({{ $savedDoctor->doctor_id }})">
                                                                <i class="fa fa-calendar text-success me-2"></i> Book Appointment
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" onclick="removeDoctor({{ $savedDoctor->saved_id }})">
                                                                <i class="fa fa-trash me-2"></i> Remove
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fa fa-heart text-muted mb-4" style="font-size: 4rem;"></i>
                                                    <h4 class="text-muted mb-3">No Saved Doctors Yet</h4>
                                                    <p class="text-muted mb-4">Start building your healthcare network by saving doctors you're interested in.</p>
                                                    <a href="{{ route('doctors.list') }}" class="btn btn-primary btn-lg">
                                                        <i class="fa fa-search me-2"></i> Browse Doctors
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted small">
                                Showing {{ $savedDoctors->firstItem() }} to {{ $savedDoctors->lastItem() }} of {{ $savedDoctors->total() }} results
                            </div>
                            <div>
                                {{ $savedDoctors->links() }}
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</section>

@endsection

@section('customJS')
{{-- <script type="text/javascript">
function removeDoctor(id) {
    if(confirm("Are you sure you want to remove this doctor from your saved list?")){
        $.ajax({
            url : '{{ route("account.removeSavedDoctor") }}',
            type: 'post',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            datatype: 'json',
            success: function(response){
                if(response.status) {
                    window.location.href = '{{ route("account.savedDoctors") }}';
                } else {
                    alert(response.message || 'Failed to remove doctor. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    }
}

function bookAppointment(doctorId) {
    window.location.href = '{{ route("book.appointment") }}?doctor_id=' + doctorId;
}
</script> --}}

<style>
.empty-state {
    text-align: center;
    padding: 2rem 0;
}

.doctor-info {
    min-width: 250px;
}

.doctor-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.clinic-info {
    font-size: 0.85rem;
}

.save-reason-container {
    border-left: 3px solid #007bff !important;
}

.save-reason-container small.fw-500 {
    font-size: 0.8rem;
}

.no-reason {
    font-size: 0.8rem;
    font-style: italic;
}

.specialization-badge {
    background: #e8f5e8;
    color: #2e7d32;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
}

.qualification-text {
    font-size: 0.9rem;
    color: #495057;
}

.experience-info {
    font-size: 0.9rem;
}

.category-badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

.badge.bg-danger {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52) !important;
}

.badge.bg-primary {
    background: linear-gradient(45deg, #4facfe, #00f2fe) !important;
}

.badge.bg-warning {
    background: linear-gradient(45deg, #f6d365, #fda085) !important;
    color: #fff !important;
}

.action-dots .dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 8px;
}

.action-dots .dropdown-item {
    padding: 0.6rem 1rem;
    border-radius: 4px;
    margin: 2px 8px;
    width: auto;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.action-dots .dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.table > :not(caption) > * > * {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection