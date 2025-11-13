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
                                                            <th>Payment Status</th>
                                                            <th>Appointment Status</th>
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
                                                                $isUpcoming = !$isPast && !$isToday;
                                                                $canJoinMeeting = $appointment->status == 'confirmed' && 
                                                                                 ($isToday || $isPast) && 
                                                                                 $appointment->payment_status == 'paid';
                                                            @endphp
                                                            <tr class="text-center">
                                                                <td class="text-start">
                                                                    <strong>Dr. {{ $appointment->doctor_name }}</strong>
                                                                    @if($appointment->specialization)
                                                                        <br><small class="text-muted">{{ $appointment->specialization }}</small>
                                                                    @endif
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
                                                                    @if($appointment->payment_status == 'paid')
                                                                        <span class="badge bg-success">Paid</span>
                                                                        @if($appointment->payment_method)
                                                                            <br><small class="text-muted">via {{ ucfirst($appointment->payment_method) }}</small>
                                                                        @endif
                                                                    @elseif($appointment->payment_status == 'pending')
                                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                                    @elseif($appointment->payment_status == 'failed')
                                                                        <span class="badge bg-danger">Failed</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ ucfirst($appointment->payment_status) }}</span>
                                                                    @endif
                                                                </td>
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
                                                                        
                                                                        {{-- Pay Button for Pending Payment --}}
                                                                        @if(in_array($appointment->status, ['confirmed', 'pending']) && $appointment->payment_status == 'pending')
                                                                            <button type="button" class="btn btn-sm btn-outline-success pay-btn"
                                                                                    data-appointment-id="{{ $appointment->id }}"
                                                                                    data-fee="{{ $appointment->fee }}"
                                                                                    data-doctor-name="{{ $appointment->doctor_name }}"
                                                                                    title="Pay Now">
                                                                                <i class="fa fa-credit-card"></i>
                                                                            </button>
                                                                        @endif

                                                                        {{-- Join Meeting Button --}}
                                                                        @if($canJoinMeeting)
                                                                            <button type="button" class="btn btn-sm btn-outline-info join-meeting-btn"
                                                                                    data-appointment-id="{{ $appointment->id }}"
                                                                                    title="Join Meeting">
                                                                                <i class="fa fa-video"></i>
                                                                            </button>
                                                                        @endif

                                                                        {{-- Cancel Button --}}
                                                                        @if(in_array($appointment->status, ['pending', 'confirmed']) && !$isPast)
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fa fa-credit-card me-2 text-success"></i>Complete Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('patient.process.payment') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="appointment_id" id="payment_appointment_id">
                <input type="hidden" name="stripe_payment_intent_id" id="payment_stripe_payment_intent_id">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2">Payment Details</h6>
                        <p class="mb-1"><strong>Appointment ID:</strong> <span id="paymentAppointmentId">-</span></p>
                        <p class="mb-1"><strong>Amount:</strong> Rs. <span id="paymentAmount">-</span></p>
                        <p class="mb-0"><strong>Doctor:</strong> <span id="paymentDoctorName">-</span></p>
                    </div>
                    
                    <!-- Payment Method Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payment Method</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="stripePayment" value="stripe" checked>
                            <label class="form-check-label" for="stripePayment">
                                <i class="fab fa-cc-stripe me-1"></i> Credit/Debit Card (Stripe)
                            </label>
                        </div>
                        {{-- <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypalPayment" value="paypal">
                            <label class="form-check-label" for="paypalPayment">
                                <i class="fab fa-paypal me-1"></i> PayPal
                            </label>
                        </div> --}}
                    </div>

                    <!-- Stripe Payment Element -->
                    <div id="stripePaymentSection">
                        <div class="mb-3">
                            <label class="form-label">Payment Details</label>
                            <div id="payment-element" class="p-3 border rounded">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading payment form...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">Loading payment form...</p>
                                </div>
                            </div>
                            <div id="payment-errors" class="text-danger mt-2 small"></div>
                        </div>
                    </div>

                    <!-- PayPal Section -->
                    <div id="paypalPaymentSection" style="display: none;">
                        <div class="alert alert-warning">
                            <small>
                                <i class="fa fa-info-circle me-1"></i>
                                You will be redirected to PayPal to complete your payment.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submitPaymentBtn" disabled>
                        <i class="fa fa-credit-card me-1"></i>Complete Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 + Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Initialize Stripe
const stripe = Stripe('{{ config('services.stripe.key') }}');
let elements;
let isStripeInitialized = false;

document.addEventListener('DOMContentLoaded', function() {
    // Success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 5000,
            showConfirmButton: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 5000,
            showConfirmButton: true
        });
    @endif

    // View Appointment Details
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            let appointmentId = this.dataset.appointmentId;
            Swal.fire({
                title: 'Appointment Details',
                text: 'Detailed view will be implemented here.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        });
    });

    // Pay Button Handler - OPTIMIZED
    document.querySelectorAll('.pay-btn').forEach(button => {
        button.addEventListener('click', function() {
            let appointmentId = this.dataset.appointmentId;
            let fee = this.dataset.fee;
            let doctorName = this.dataset.doctorName;
            
            // Set basic info immediately
            $('#payment_appointment_id').val(appointmentId);
            $('#paymentAppointmentId').text(appointmentId);
            $('#paymentAmount').text(fee);
            $('#paymentDoctorName').text('Dr. ' + doctorName);
            
            // Reset form state
            $('#submitPaymentBtn').prop('disabled', true);
            $('#payment-errors').text('');
            $('#payment-element').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading payment form...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted">Loading payment form...</p>
                </div>
            `);
            
            // Show modal immediately
            $('#paymentModal').modal('show');
            
            // Initialize payment after modal is shown
            initializePaymentForAppointment(appointmentId, fee);
        });
    });

    // Join Meeting Button Handler
    document.querySelectorAll('.join-meeting-btn').forEach(button => {
        button.addEventListener('click', function() {
            let appointmentId = this.dataset.appointmentId;
            Swal.fire({
                title: 'Join Meeting?',
                text: 'You will be redirected to the video consultation room.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#0dcaf0',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Join Meeting',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/meeting/${appointmentId}`;
                }
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
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to cancel appointment'
                        });
                    });
                }
            });
        });
    });

    // Payment method toggle
    $('input[name="payment_method"]').on('change', function() {
        if ($(this).val() === 'stripe') {
            $('#stripePaymentSection').show();
            $('#paypalPaymentSection').hide();
            $('#submitPaymentBtn').prop('disabled', !isStripeInitialized);
        } else {
            $('#stripePaymentSection').hide();
            $('#paypalPaymentSection').show();
            $('#submitPaymentBtn').prop('disabled', false);
        }
    });

    // Payment form submission
    $('#paymentForm').on('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitPaymentBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fa fa-spinner fa-spin me-1"></i>Processing Payment...');

        const paymentMethod = $('input[name="payment_method"]:checked').val();

        try {
            if (paymentMethod === 'stripe') {
                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ url("/payment-success") }}',
                    },
                    redirect: 'if_required'
                });

                if (error) {
                    throw new Error(error.message);
                }
            }

            // Submit the form
            this.submit();

        } catch (error) {
            console.error('Payment error:', error);
            $('#payment-errors').text(error.message);
            submitBtn.prop('disabled', false);
            submitBtn.html(originalText);
        }
    });

    // Reset modal when closed
    $('#paymentModal').on('hidden.bs.modal', function () {
        isStripeInitialized = false;
        $('#submitPaymentBtn').prop('disabled', true);
        $('#payment-errors').text('');
        if (elements) {
            elements.unmount();
        }
    });
});

// Optimized payment initialization
async function initializePaymentForAppointment(appointmentId, amount) {
    try {
        const response = await fetch('/create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                amount: amount
            })
        });

        const data = await response.json();

        if (data.success) {
            $('#payment_stripe_payment_intent_id').val(data.paymentIntentId);

            // Initialize Stripe Elements
            elements = stripe.elements({
                clientSecret: data.clientSecret,
                appearance: {
                    theme: 'stripe',
                },
            });

            // Create and mount the Payment Element
            const paymentElement = elements.create('payment');
            
            // Clear loading state and mount
            $('#payment-element').html('');
            paymentElement.mount('#payment-element');
            
            // Enable submit button
            isStripeInitialized = true;
            $('#submitPaymentBtn').prop('disabled', false);
            
        } else {
            throw new Error(data.message || 'Failed to initialize payment');
        }
    } catch (error) {
        console.error('Payment initialization error:', error);
        $('#payment-element').html(`
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle me-2"></i>
                Failed to load payment form: ${error.message}
            </div>
        `);
        $('#submitPaymentBtn').prop('disabled', true);
    }
}

// Join Meeting Button Handler - UPDATED
document.querySelectorAll('.join-meeting-btn').forEach(button => {
    button.addEventListener('click', function() {
        let appointmentId = this.dataset.appointmentId;
        
        Swal.fire({
            title: 'Join Meeting?',
            text: 'You will be redirected to the Zoom meeting room.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#0dcaf0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Join Meeting',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    const response = await fetch(`/meeting/${appointmentId}`);
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        throw new Error('Failed to join meeting');
                    }
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
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

.btn-group .btn {
    margin: 0 2px;
}

/* Loading animation */
.spinner-border {
    width: 2rem;
    height: 2rem;
}

/* Modal optimization */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

#payment-element {
    min-height: 120px;
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .btn-group .btn {
        margin: 0;
    }
    
    .modal-dialog {
        margin: 20px 10px;
    }
}
</style>
@endsection