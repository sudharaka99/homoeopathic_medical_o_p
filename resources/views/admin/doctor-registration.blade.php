@extends('front.layouts.app')

@section('main')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Doctor Registration Approvals</h5>
                            <p class="text-muted mb-0">Review and manage doctor registration requests</p>
                        </div>
                        <div class="text-muted">
                            <span class="badge bg-primary">{{ $pendingDoctors->total() }} Pending</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    {{-- @include('admin.message') --}}
                    
                    @if($pendingDoctors->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 ps-4">Doctor Information</th>
                                    <th class="border-0">License & Qualification</th>
                                    <th class="border-0">Specialization</th>
                                    <th class="border-0">Experience & Fee</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingDoctors as $doctor)
                                <tr class="border-bottom">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0">
                                                @if($doctor->profile_photo_path)
                                                    <img src="{{ asset('storage/'.$doctor->profile_photo_path) }}" 
                                                         alt="{{ $doctor->name }}" 
                                                         class="rounded-circle" 
                                                         width="60" height="60">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fa fa-user-md fa-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1 fw-bold text-dark">{{ $doctor->name }}</h6>
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
                                        <div class="license-info">
                                            @if($doctor->license_number)
                                            <div class="mb-2">
                                                <strong class="text-dark d-block">License No:</strong>
                                                <span class="text-muted">{{ $doctor->license_number }}</span>
                                            </div>
                                            @endif
                                            
                                            @if($doctor->license_image)
                                            <div class="mb-2">
                                                <strong class="text-dark d-block">License Image:</strong>
                                                <a href="{{ asset('storage/'.$doctor->license_image) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye me-1"></i>View
                                                </a>
                                            </div>
                                            @endif
                                            
                                            @if($doctor->qualification)
                                            <div>
                                                <strong class="text-dark d-block">Qualification:</strong>
                                                <span class="text-muted small">{{ $doctor->qualification }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @if($doctor->specialization_name)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                            {{ $doctor->specialization_name }}
                                        </span>
                                        @else
                                        <span class="text-muted">Not specified</span>
                                        @endif
                                        
                                        @if($doctor->district)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fa fa-map-marker-alt me-1"></i>{{ $doctor->district }}
                                            </small>
                                        </div>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div class="experience-fee">
                                            @if($doctor->years_experience)
                                            <div class="mb-2">
                                                <strong class="text-dark d-block">Experience:</strong>
                                                <span class="text-success fw-medium">{{ $doctor->years_experience }} years</span>
                                            </div>
                                            @endif
                                            
                                            @if($doctor->appointment_fee)
                                            <div>
                                                <strong class="text-dark d-block">Appointment Fee:</strong>
                                                <span class="text-primary fw-medium">Rs. {{ $doctor->appointment_fee }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25">
                                            <i class="fa fa-clock me-1"></i>Pending Review
                                        </span>
                                        <div class="mt-1 small text-muted">
                                            Registered: {{ $doctor->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-cog me-1"></i>Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a class="dropdown-item view-doctor-details" 
                                                       href="#" 
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#doctorDetailsModal"
                                                       data-doctor-id="{{ $doctor->id }}">
                                                        <i class="fa fa-eye text-primary me-2"></i>View Details
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
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
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $pendingDoctors->firstItem() }} to {{ $pendingDoctors->lastItem() }} of {{ $pendingDoctors->total() }} requests
                            </div>
                            <div>
                                {{ $pendingDoctors->links() }}
                            </div>
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

<!-- Doctor Details Modal -->
<div class="modal fade" id="doctorDetailsModal" tabindex="-1" aria-labelledby="doctorDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="doctorDetailsModalLabel">
                    <i class="fa fa-user-md me-2"></i>Doctor Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="doctorDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="rejectModalLabel">
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
                        <label for="reject_reason" class="form-label fw-semibold text-dark">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control border-2" 
                                  id="reject_reason" 
                                  name="reject_reason" 
                                  rows="4" 
                                  placeholder="Please provide specific reason for rejection..."
                                  required></textarea>
                        <div class="form-text">This reason will be communicated to the doctor.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
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
    // View Doctor Details
    $('.view-doctor-details').on('click', function() {
        const doctorId = $(this).data('doctor-id');
        
        $.ajax({
            url: 'admin.doctorDetails',
            type: 'GET',
            data: { doctor_id: doctorId },
            success: function(response) {
                $('#doctorDetailsContent').html(response);
            },
            error: function() {
                $('#doctorDetailsContent').html(`
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
.empty-state-icon {
    opacity: 0.6;
}

.license-info strong {
    font-size: 0.85rem;
}

.experience-fee strong {
    font-size: 0.85rem;
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

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn-group .dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 8px;
}

.btn-group .dropdown-item {
    padding: 0.6rem 1rem;
    border-radius: 4px;
    margin: 2px 8px;
    width: auto;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.btn-group .dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.modal-header {
    border-bottom: 2px solid rgba(0,0,0,0.1);
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.1);
}
</style>
@endsection