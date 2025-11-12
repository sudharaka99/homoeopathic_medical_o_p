@extends('front.layouts.app')

@section('main')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('front.account.doctor.slidebar')
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-calendar-check text-primary me-2"></i>Manage Appointments
                </h2>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary fs-6">Total: {{ $appointments->count() }}</span>
                </div>
            </div>

            @include('front.message')

            <!-- ========== PENDING APPOINTMENTS ========== -->
            <div class="card border-0 shadow-sm mb-4" id="cardPendingAppointments">
                <div class="card-header bg-warning bg-opacity-10 border-warning">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Pending Appointments
                        <span class="badge bg-warning ms-3">{{ $pendingAppointments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($pendingAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date & Time</th>
                                        <th>Token</th>
                                        <th>Fee</th>
                                        <th>Booked On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingAppointments as $appointment)
                                    <tr id="pending-row-{{ $appointment->id }}">
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ $appointment->patient_name }}</strong>
                                                <small class="text-muted">{{ $appointment->patient_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="token-number-display">
                                                <span class="token-badge">#{{ $appointment->token_number }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>Rs. {{ number_format($appointment->fee, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, h:i A') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info view-details-btn" 
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-success confirm-btn"
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        title="Confirm Appointment">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-danger cancel-btn"
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        title="Cancel Appointment">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No pending appointments.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========== CONFIRMED APPOINTMENTS ========== -->
            <div class="card border-0 shadow-sm mb-4" id="cardConfirmedAppointments">
                <div class="card-header bg-success bg-opacity-10 border-success">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Confirmed Appointments
                        <span class="badge bg-success ms-3">{{ $confirmedAppointments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($confirmedAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-success">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date & Time</th>
                                        <th>Token</th>
                                        <th>Fee</th>
                                        <th>Confirmed On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($confirmedAppointments as $appointment)
                                    @php
                                        $appointmentDate = \Carbon\Carbon::parse($appointment->appointment_date);
                                        $isToday = $appointmentDate->isToday();
                                        $isUpcoming = $appointmentDate->isFuture();
                                    @endphp
                                    <tr id="confirmed-row-{{ $appointment->id }}">
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ $appointment->patient_name }}</strong>
                                                <small class="text-muted">{{ $appointment->patient_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ $appointmentDate->format('M d, Y') }}
                                                @if($isToday)
                                                    <span class="badge bg-info ms-1">Today</span>
                                                @elseif($isUpcoming)
                                                    <span class="badge bg-success ms-1">Upcoming</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="token-number-display">
                                                <span class="token-badge">#{{ $appointment->token_number }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>Rs. {{ number_format($appointment->fee, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, h:i A') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info view-details-btn" 
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-primary complete-btn"
                                                        data-appointment-id="{{ $appointment->id }}"
                                                        title="Mark as Completed">
                                                    <i class="fas fa-flag-checkered"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No confirmed appointments.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========== COMPLETED APPOINTMENTS ========== -->
            <div class="card border-0 shadow-sm mb-4" id="cardCompletedAppointments">
                <div class="card-header bg-info bg-opacity-10 border-info">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-flag-checkered text-info me-2"></i>
                        Completed Appointments
                        <span class="badge bg-info ms-3">{{ $completedAppointments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($completedAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-info">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date & Time</th>
                                        <th>Token</th>
                                        <th>Fee</th>
                                        <th>Completed On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedAppointments as $appointment)
                                    <tr id="completed-row-{{ $appointment->id }}">
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ $appointment->patient_name }}</strong>
                                                <small class="text-muted">{{ $appointment->patient_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="token-number-display">
                                                <span class="token-badge">#{{ $appointment->token_number }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>Rs. {{ number_format($appointment->fee, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, h:i A') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm view-details-btn" 
                                                    data-appointment-id="{{ $appointment->id }}"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No completed appointments.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========== CANCELLED APPOINTMENTS ========== -->
            <div class="card border-0 shadow-sm" id="cardCancelledAppointments">
                <div class="card-header bg-danger bg-opacity-10 border-danger">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Cancelled Appointments
                        <span class="badge bg-danger ms-3">{{ $cancelledAppointments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($cancelledAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Date & Time</th>
                                        <th>Token</th>
                                        <th>Fee</th>
                                        <th>Cancelled On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cancelledAppointments as $appointment)
                                    <tr id="cancelled-row-{{ $appointment->id }}">
                                        <td>
                                            <div>
                                                <strong class="d-block">{{ $appointment->patient_name }}</strong>
                                                <small class="text-muted">{{ $appointment->patient_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="token-number-display">
                                                <span class="token-badge">#{{ $appointment->token_number }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>Rs. {{ number_format($appointment->fee, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, h:i A') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm view-details-btn" 
                                                    data-appointment-id="{{ $appointment->id }}"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-ban fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No cancelled appointments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailsModalLabel">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="appointmentDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Token Badge Styling
const tokenBadgeStyle = `
.token-number-display {
    display: inline-block;
}
.token-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 2px solid #5a6fd8;
    display: inline-block;
    min-width: 50px;
    text-align: center;
}
`;

// Add styles to head
const styleSheet = document.createElement("style");
styleSheet.innerText = tokenBadgeStyle;
document.head.appendChild(styleSheet);

document.addEventListener('DOMContentLoaded', function() {
    // View Appointment Details
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            loadAppointmentDetails(appointmentId);
        });
    });

    // Confirm Appointment
    document.querySelectorAll('.confirm-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            confirmAppointmentStatus(appointmentId, 'confirmed', 'Are you sure you want to confirm this appointment?');
        });
    });

    // Cancel Appointment
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            confirmAppointmentStatus(appointmentId, 'cancelled', 'Are you sure you want to cancel this appointment?');
        });
    });

    // Complete Appointment
    document.querySelectorAll('.complete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            confirmAppointmentStatus(appointmentId, 'completed', 'Are you sure you want to mark this appointment as completed?');
        });
    });

    // Success/Error Messages
    @if(session('success'))
        Swal.fire('Success!', '{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        Swal.fire('Error!', '{{ session('error') }}', 'error');
    @endif
});

function loadAppointmentDetails(appointmentId) {
    Swal.fire({ 
        title: 'Loading...', 
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); }
    });

    // Fixed URL construction
    const url = `/appointments/${appointmentId}/details`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success && data.appointment) {
            const apt = data.appointment;
            const modalContent = document.getElementById('appointmentDetailsContent');
            
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Patient Information</h6>
                        <p><strong>Name:</strong> ${apt.patient_name}</p>
                        <p><strong>Email:</strong> ${apt.patient_email}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Appointment Details</h6>
                        <p><strong>Date:</strong> ${new Date(apt.appointment_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        <p><strong>Time:</strong> ${apt.start_time} - ${apt.end_time}</p>
                        <p><strong>Token:</strong> <span class="token-badge">#${apt.token_number}</span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Fee & Status</h6>
                        <p><strong>Fee:</strong> Rs. ${parseFloat(apt.fee).toLocaleString()}</p>
                        <p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(apt.status)}">${apt.status.charAt(0).toUpperCase() + apt.status.slice(1)}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Timestamps</h6>
                        <p><strong>Booked On:</strong> ${new Date(apt.created_at).toLocaleString()}</p>
                        <p><strong>Last Updated:</strong> ${new Date(apt.updated_at).toLocaleString()}</p>
                    </div>
                </div>
                ${apt.notes ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-muted">Notes</h6>
                        <div class="alert alert-light">${apt.notes}</div>
                    </div>
                </div>
                ` : ''}
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
            modal.show();
        } else {
            Swal.fire('Error', data.message || 'Failed to load appointment details', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'Could not load appointment details. Please try again.', 'error');
    });
}

function confirmAppointmentStatus(appointmentId, status, message) {
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getStatusButtonColor(status),
        cancelButtonColor: '#6c757d',
        confirmButtonText: getStatusButtonText(status),
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            updateAppointmentStatus(appointmentId, status);
        }
    });
}

function updateAppointmentStatus(appointmentId, status) {
    Swal.fire({ 
        title: 'Updating...', 
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); }
    });

    // Fixed URL construction
    const url = `/appointments/${appointmentId}/update-status`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'Network error. Please try again.', 'error');
    });
}

function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'bg-warning text-dark',
        'confirmed': 'bg-success',
        'completed': 'bg-info',
        'cancelled': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function getStatusButtonColor(status) {
    const colors = {
        'confirmed': '#28a745',
        'cancelled': '#dc3545',
        'completed': '#007bff'
    };
    return colors[status] || '#007bff';
}

function getStatusButtonText(status) {
    const texts = {
        'confirmed': 'Yes, Confirm!',
        'cancelled': 'Yes, Cancel!',
        'completed': 'Yes, Complete!'
    };
    return texts[status] || 'Yes, Update!';
}
</script>
@endsection