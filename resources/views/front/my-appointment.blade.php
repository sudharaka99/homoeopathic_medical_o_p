@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col-lg-3">
                        @include('front.account.slidebar')
                    </div>

                    <div class="col-lg-9">
                        <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <ol class="breadcrumb mb-2">
                                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                        <li class="breadcrumb-item active">My Appointments</li>
                                    </ol>
                                </div>
                                <div>
                                    <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-calendar-plus me-1"></i> Book New Appointment
                                    </a>
                                </div>
                            </div>
                        </nav>

                        <div class="row">
                            <div class="col-lg-12">
                                @include('front.message')

                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0 text-primary">
                                                <i class="fa fa-calendar-check me-2"></i>
                                                My Appointments
                                            </h5>
                                            <span class="badge bg-primary">
                                                Total: {{ $appointments->count() }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if($appointments->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle">
                                                    <thead class="table-light">
                                                        <tr class="text-center">
                                                            <th>Doctor</th>
                                                            <th>Date & Time</th>
                                                            <th>Token No</th>
                                                            <th>Duration</th>
                                                            <th>Fee (Rs)</th>
                                                            <th>Status</th>
                                                            <th>Booked On</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($appointments as $appointment)
                                                            @php
                                                                $start = \Carbon\Carbon::parse($appointment->start_time);
                                                                $end = \Carbon\Carbon::parse($appointment->end_time);
                                                                $duration = $start->diffInMinutes($end);
                                                                $appointmentDate = \Carbon\Carbon::parse($appointment->appointment_date);
                                                                $isToday = $appointmentDate->isToday();
                                                                $isPast = $appointmentDate->isPast();
                                                            @endphp
                                                            <tr class="text-center">
                                                                <td class="text-start">
                                                                    <strong>Dr. {{ $appointment->doctor_name }}</strong>
                                                                    {{-- @if($appointment->specialization)
                                                                        <br><small class="text-muted">{{ $appointment->specialization }}</small>
                                                                    @endif
                                                                    @if($appointment->clinic_name)
                                                                        <br><small class="text-muted"><i class="fa fa-hospital me-1"></i>{{ $appointment->clinic_name }}</small>
                                                                    @endif --}}
                                                                </td>
                                                                <td>
                                                                    <div class="fw-bold">
                                                                        {{ $appointmentDate->format('M d, Y') }}
                                                                        @if($isToday)
                                                                            <span class="badge bg-info ms-1">Today</span>
                                                                        @elseif($isPast)
                                                                            <span class="badge bg-secondary ms-1">Past</span>
                                                                        @else
                                                                            <span class="badge bg-success ms-1">Upcoming</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-muted small">
                                                                        {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if($appointment->token_number)
                                                                        <div class="token-number-display">
                                                                            <span class="token-badge">#{{ $appointment->token_number }}</span>
                                                                        </div>
                                                                    @else
                                                                        <span class="badge bg-secondary">N/A</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $duration }} min</td>
                                                                <td><strong>Rs. {{ number_format($appointment->fee, 2) }}</strong></td>
                                                                <td>
                                                                    @if($appointment->status == 'pending')
                                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                                    @elseif($appointment->status == 'confirmed')
                                                                        <span class="badge bg-success">Confirmed</span>
                                                                    @elseif($appointment->status == 'completed')
                                                                        <span class="badge bg-info">Completed</span>
                                                                    @elseif($appointment->status == 'cancelled')
                                                                        <span class="badge bg-danger">Cancelled</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <small>{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y h:i A') }}</small>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-outline-primary view-btn" 
                                                                                data-appointment-id="{{ $appointment->id }}"
                                                                                title="View Details">
                                                                            <i class="fa fa-eye"></i>
                                                                        </button>
                                                                        @if($appointment->status == 'pending' && !$isPast)
                                                                        <button type="button" class="btn btn-sm btn-outline-danger cancel-btn"
                                                                                data-appointment-id="{{ $appointment->id }}"
                                                                                title="Cancel Appointment">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Appointments Found</h5>
                                                <p class="text-muted">You haven't booked any appointments yet.</p>
                                                <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary">
                                                    <i class="fa fa-calendar-plus me-1"></i> Book Your First Appointment
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SweetAlert2 + Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Success message with token number
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 5000,
            showConfirmButton: true
        });
    @endif

    // View Appointment Details
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            let appointmentId = this.dataset.appointmentId;
            
            // Here you can implement view details functionality
            Swal.fire({
                title: 'Appointment Details',
                text: 'View details functionality will be implemented here.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        });
    });

    // Cancel Appointment
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            let appointmentId = this.dataset.appointmentId;
            
            Swal.fire({
                title: 'Cancel Appointment?',
                text: 'Are you sure you want to cancel this appointment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Cancel it!',
                cancelButtonText: 'Keep Appointment'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implement cancel appointment functionality
                    fetch(`/appointments/${appointmentId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelled!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: data.message
                            });
                        }
                    });
                }
            });
        });
    });
});
</script>

<style>
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

/* Alternative style options - choose one */

/* Style 2: Green gradient */
.token-badge.green {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: 2px solid #3aa0ff;
}

/* Style 3: Orange gradient */
.token-badge.orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: 2px solid #e6687c;
}

/* Style 4: Purple gradient */
.token-badge.purple {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
    border: 2px solid #9ad4d1;
}
</style>
@endsection