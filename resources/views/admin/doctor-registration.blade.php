@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Doctor Approvals</li>
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
                            <h2 class="mb-0">Doctor Registration Approvals</h2>
                            <span class="badge bg-primary">{{ $pendingDoctors->total() }} Pending</span>
                        </div>

                        @if($pendingDoctors->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Doctor Info</th>
                                        <th>License & Qualification</th>
                                        <th>Specialization</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingDoctors as $doctor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                @if($doctor->profile_photo_path)
                                                    <img src="{{ asset('storage/'.$doctor->profile_photo_path) }}" 
                                                         alt="{{ $doctor->name }}" 
                                                         class="rounded-circle me-3" 
                                                         width="60" height="60">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fa fa-user-md"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">{{ $doctor->name }}</h6>
                                                    <p class="text-muted mb-1 small">
                                                        <i class="fa fa-envelope me-1"></i>{{ $doctor->email }}
                                                    </p>
                                                    <p class="text-muted mb-1 small">
                                                        <i class="fa fa-phone me-1"></i>{{ $doctor->mobile ?? 'N/A' }}
                                                    </p>
                                                    @if($doctor->clinic_name)
                                                    <p class="text-muted mb-0 small">
                                                        <i class="fa fa-hospital me-1"></i>{{ $doctor->clinic_name }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($doctor->license_number)
                                            <div class="mb-2">
                                                <strong>License No:</strong>
                                                <span class="text-muted d-block">{{ $doctor->license_number }}</span>
                                            </div>
                                            @endif
                                            
                                            @if($doctor->license_image)
                                            <div class="mb-2">
                                                <strong>License Image:</strong>
                                                <div class="mt-1">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary view-license-image"
                                                            data-image="{{ asset('storage/doctor_licenses/' . basename($doctor->license_image)) }}"
                                                            data-doctor-name="{{ $doctor->name }}">
                                                        <i class="fa fa-eye me-1"></i>View License
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if($doctor->qualification)
                                            <div>
                                                <strong>Qualification:</strong>
                                                <span class="text-muted d-block small">{{ $doctor->qualification }}</span>
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($doctor->specialization_name)
                                            <span class="badge bg-info">
                                                {{ $doctor->specialization_name }}
                                            </span>
                                            @else
                                            <span class="text-muted">Not specified</span>
                                            @endif
                                            
                                            @if($doctor->years_experience)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fa fa-briefcase me-1"></i>{{ $doctor->years_experience }} years exp.
                                                </small>
                                            </div>
                                            @endif
                                            
                                            @if($doctor->appointment_fee)
                                            <div class="mt-1">
                                                <small class="text-primary">
                                                    <i class="fa fa-money-bill me-1"></i>Rs. {{ $doctor->appointment_fee }}
                                                </small>
                                            </div>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="badge bg-warning">
                                                <i class="fa fa-clock me-1"></i>Pending
                                            </span>
                                            <div class="mt-1 small text-muted">
                                                {{ \Carbon\Carbon::parse($doctor->created_at)->format('M d, Y') }}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item text-success approve-doctor" 
                                                           href="#" 
                                                           data-doctor-id="{{ $doctor->id }}"
                                                           data-doctor-name="{{ $doctor->name }}">
                                                            <i class="fa fa-check-circle text-success me-2"></i>Approve
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger reject-doctor" 
                                                           href="#" 
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#rejectModal"
                                                           data-doctor-id="{{ $doctor->id }}"
                                                           data-doctor-name="{{ $doctor->name }}">
                                                            <i class="fa fa-times-circle text-danger me-2"></i>Reject
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item view-doctor-details" 
                                                           href="#" 
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#doctorDetailsModal"
                                                           data-doctor-id="{{ $doctor->id }}">
                                                            <i class="fa fa-eye text-primary me-2"></i>View Details
                                                        </a>
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
                                Showing {{ $pendingDoctors->firstItem() }} to {{ $pendingDoctors->lastItem() }} of {{ $pendingDoctors->total() }} requests
                            </div>
                            <div>
                                {{ $pendingDoctors->links() }}
                            </div>
                        </div>
                        
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="fa fa-user-md text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Pending Approvals</h4>
                            <p class="text-muted mb-4">All doctor registration requests have been processed.</p>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                <i class="fa fa-dashboard me-2"></i>Go to Dashboard
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- License Image Modal -->
<div class="modal fade" id="licenseImageModal" tabindex="-1" aria-labelledby="licenseImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="licenseImageModalLabel">License Image - <span id="licenseDoctorName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="licenseImage" src="" alt="License Image" class="img-fluid rounded" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadLicense" href="#" class="btn btn-primary" download>
                    <i class="fa fa-download me-1"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Doctor Details Modal -->
<div class="modal fade" id="doctorDetailsModal" tabindex="-1" aria-labelledby="doctorDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="doctorDetailsModalLabel">
                    <i class="fa fa-user-md me-2"></i>Doctor Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="doctorDetailsContent">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading doctor details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fa fa-times-circle me-2"></i>Reject Doctor Registration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectDoctorForm">
                @csrf
                <input type="hidden" name="doctor_id" id="reject_doctor_id">
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">You are rejecting: <strong id="rejectDoctorName"></strong></p>
                    
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label fw-semibold">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="reject_reason" 
                                  name="reject_reason" 
                                  rows="4" 
                                  placeholder="Please provide specific reason for rejection..."
                                  required></textarea>
                        <div class="form-text">This reason will be communicated to the doctor.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle me-2"></i>Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script>
$(document).ready(function() {
    // View License Image
    $('.view-license-image').on('click', function() {
        const imageUrl = $(this).data('image');
        const doctorName = $(this).data('doctor-name');
        
        console.log('Loading license image:', imageUrl);
        
        $('#licenseImage').attr('src', imageUrl);
        $('#licenseDoctorName').text(doctorName);
        $('#downloadLicense').attr('href', imageUrl);
        $('#licenseImageModal').modal('show');
    });

    // View Doctor Details
    $('.view-doctor-details').on('click', function() {
        const doctorId = $(this).data('doctor-id');
        const doctorDetailsContent = $('#doctorDetailsContent');
        
        // Show loading
        doctorDetailsContent.html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading doctor details...</p>
            </div>
        `);
        
        $.ajax({
            url: '{{ route("admin.doctorDetails") }}',
            type: 'GET',
            data: { doctor_id: doctorId },
            success: function(response) {
                doctorDetailsContent.html(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                doctorDetailsContent.html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Failed to load doctor details. Please try again.
                    </div>
                `);
            }
        });
    });

    // Approve Doctor
    $('.approve-doctor').on('click', function(e) {
        e.preventDefault();
        const doctorId = $(this).data('doctor-id');
        const doctorName = $(this).data('doctor-name');
        
        Swal.fire({
            title: 'Approve Doctor?',
            text: `Are you sure you want to approve ${doctorName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approve!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.approveDoctor") }}',
                    type: 'POST',
                    data: {
                        doctor_id: doctorId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Approved!',
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
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    // Reject Doctor Modal Setup
    $('#rejectModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const doctorId = button.data('doctor-id');
        const doctorName = button.data('doctor-name');
        
        $('#reject_doctor_id').val(doctorId);
        $('#rejectDoctorName').text(doctorName);
    });

    // Reject Doctor Form Submission
    $('#rejectDoctorForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.rejectDoctor") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    Swal.fire({
                        title: 'Rejected!',
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
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Reset reject form when modal is hidden
    $('#rejectModal').on('hidden.bs.modal', function() {
        $('#rejectDoctorForm')[0].reset();
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

.view-license-image {
    font-size: 0.8rem;
}

.modal-body img {
    max-width: 100%;
    height: auto;
}
</style>
@endsection