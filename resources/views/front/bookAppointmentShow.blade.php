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
                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <ol class="breadcrumb mb-2">
                                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                        <li class="breadcrumb-item active">Book Appointment</li>
                                    </ol>
                                </div>
                            </div>
                        </nav>

                        <!-- Session Booking Table -->
                        <div class="row">
                            <div class="col-lg-12">
                                @include('front.message')

                                <div class="card">
                                    <div class="card-header bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="fa fa-calendar-alt me-2 text-primary"></i>
                                                @foreach ($doctors as $doctor)
                                                Available Time Slots for Dr. {{ $doctor->doctor_name ?? 'Doctor' }}
                                                @endforeach
                                            </h5>
                                            <a href="" class="btn btn-primary btn-sm">
                                                <i class="fa fa-calendar-check me-1"></i> My Appointments
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($avalabilityList) && $avalabilityList->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Start Time</th>
                                                            <th>End Time</th>
                                                            <th>Duration</th>
                                                            <th>Available Tokens</th>
                                                            <th>Fee</th>
                                                            <th>Status</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($avalabilityList as $availability)
                                                            @php
                                                                $doctor = $doctors->first();
                                                            @endphp
                                                            <tr>
                                                                <td class="fw-bold">
                                                                    {{ \Carbon\Carbon::parse($availability->date)->format('M d, Y') }}
                                                                    @if(\Carbon\Carbon::parse($availability->date)->isToday())
                                                                        <span class="badge bg-info ms-1">Today</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($availability->start_time_slot)->format('h:i A') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($availability->end_time_slot)->format('h:i A') }}</td>
                                                                <td>
                                                                    @php
                                                                        $start = \Carbon\Carbon::parse($availability->start_time_slot);
                                                                        $end = \Carbon\Carbon::parse($availability->end_time_slot);
                                                                        $duration = $start->diffInMinutes($end);
                                                                    @endphp
                                                                    {{ $duration }} min
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-secondary">{{ $availability->number_of_tokens }}</span>
                                                                </td>
                                                                <td>
                                                                    <strong>RS {{ $doctor->appointment_fee ?? '0' }}</strong>
                                                                </td>
                                                                <td>
                                                                    @if($availability->status == 'available')
                                                                        <span class="badge bg-success">Available</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Booked</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($availability->status == 'available' && $availability->number_of_tokens > 0)
                                                                        <button type="button" class="btn btn-primary btn-sm book-btn" 
                                                                                data-availability-id="{{ $availability->id }}"
                                                                                title="Book Appointment">
                                                                            <i class="fa fa-calendar-plus me-1"></i>Book
                                                                        </button>
                                                                    @else
                                                                        <span class="badge bg-secondary">Not Available</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Available Time Slots</h5>
                                                <p class="text-muted">This doctor doesn't have any available slots at the moment.</p>
                                                <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary">
                                                    <i class="fa fa-arrow-left me-1"></i> Choose Another Doctor
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

<!-- Book Token Modal -->
<div class="modal fade" id="bookTokenModal" tabindex="-1" aria-labelledby="bookTokenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookTokenModalLabel">
                    <i class="fa fa-calendar-check me-2 text-primary"></i>Book Appointment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('patient.book.appointment') }}" method="POST" id="bookTokenForm">
                @csrf
                <input type="hidden" name="availability_id" id="availability_id">
                <input type="hidden" name="doctor_id" id="doctor_id">
                <input type="hidden" name="appointment_fee" id="appointment_fee">
                <input type="hidden" name="stripe_payment_intent_id" id="stripe_payment_intent_id">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2">Appointment Details</h6>
                        <p class="mb-1"><strong>Doctor:</strong> <span id="doctorName">Loading...</span></p>
                        <p class="mb-1"><strong>Date:</strong> <span id="slotDate">Loading...</span></p>
                        <p class="mb-1"><strong>Time:</strong> <span id="slotTime">Loading...</span></p>
                        <p class="mb-0"><strong>Fee:</strong> RS <span id="feeAmount">Loading...</span></p>
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
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypalPayment" value="paypal">
                            <label class="form-check-label" for="paypalPayment">
                                <i class="fab fa-paypal me-1"></i> PayPal
                            </label>
                        </div>
                    </div>

                    <!-- Stripe Payment Element -->
                    <div id="stripePaymentSection">
                        <div class="mb-3">
                            <label class="form-label">Payment Details</label>
                            <div id="payment-element" class="p-3 border rounded">
                                <!-- Stripe Payment Element will be inserted here -->
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
                    
                    <div class="mb-3">
                        <label for="patient_notes" class="form-label">Notes for Doctor (Optional)</label>
                        <textarea name="patient_notes" id="patient_notes" class="form-control" rows="3" 
                                  placeholder="Describe your symptoms, concerns, or any specific requirements..."></textarea>
                        <div class="form-text">This information will help the doctor prepare for your appointment.</div>
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <i class="fa fa-info-circle me-1"></i>
                            Please arrive 10 minutes before your scheduled time. Late arrivals may result in rescheduling.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBookingBtn">
                        <i class="fa fa-credit-card me-1"></i>Pay & Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script src="https://js.stripe.com/v3/"></script>
<script>
// Initialize Stripe
const stripe = Stripe('{{ config('services.stripe.key') }}');
let elements;

$(document).ready(function() {
    console.log('Document ready - book appointment page loaded');

    // Payment method toggle
    $('input[name="payment_method"]').on('change', function() {
        if ($(this).val() === 'stripe') {
            $('#stripePaymentSection').show();
            $('#paypalPaymentSection').hide();
        } else {
            $('#stripePaymentSection').hide();
            $('#paypalPaymentSection').show();
        }
    });

    // Book button click handler
    $(document).on('click', '.book-btn', async function() {
        console.log('Book button clicked');
        
        const availabilityId = $(this).data('availability-id');
        console.log('Availability ID:', availabilityId);
        
        if (!availabilityId) {
            alert('Error: No availability ID found');
            return;
        }

        // Show loading state in modal
        $('#doctorName').text('Loading...');
        $('#slotDate').text('Loading...');
        $('#slotTime').text('Loading...');
        $('#feeAmount').text('Loading...');
        
        const submitBtn = $('#submitBookingBtn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fa fa-spinner fa-spin me-1"></i>Loading...');

        // Show modal immediately
        $('#bookTokenModal').modal('show');

        try {
            // Fetch availability details
            const availabilityResponse = await $.ajax({
                url: '/patient/get-availability-details/' + availabilityId,
                type: 'GET'
            });

            console.log('Availability response:', availabilityResponse);

            if (availabilityResponse.success) {
                const availability = availabilityResponse.availability;
                const doctor = availabilityResponse.doctor;

                // Set form values
                $('#availability_id').val(availability.id);
                $('#doctor_id').val(availability.doctor_id);
                $('#appointment_fee').val(doctor.appointment_fee);
                
                // Display values in modal
                $('#doctorName').text('Dr. ' + doctor.doctor_name);
                $('#slotDate').text(availability.formatted_date);
                $('#slotTime').text(availability.formatted_time);
                $('#feeAmount').text(doctor.appointment_fee);

                // Initialize Stripe Payment Element
                await initializeStripePayment(availability.id, doctor.appointment_fee);

                // Enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html('<i class="fa fa-credit-card me-1"></i>Pay & Confirm Booking');
                
            } else {
                throw new Error(availabilityResponse.message || 'Failed to load availability details');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
            $('#bookTokenModal').modal('hide');
        }
    });

    // Initialize Stripe Payment Element
    async function initializeStripePayment(availabilityId, amount) {
        try {
            // Create payment intent
            const response = await $.ajax({
                url: '{{ route("patient.create.payment.intent") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    availability_id: availabilityId,
                    amount: amount
                }
            });

            console.log('Payment intent response:', response);

            if (response.success) {
                // Store payment intent ID
                $('#stripe_payment_intent_id').val(response.paymentIntentId);

                // Initialize Stripe Elements
                elements = stripe.elements({
                    clientSecret: response.clientSecret,
                    appearance: {
                        theme: 'stripe',
                    },
                });

                // Create and mount the Payment Element
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');

                console.log('Stripe Payment Element mounted successfully');
            } else {
                throw new Error(response.message || 'Failed to initialize payment');
            }
        } catch (error) {
            console.error('Stripe initialization error:', error);
            throw new Error('Payment initialization failed: ' + error.message);
        }
    }

    // Form submission handler
    $('#bookTokenForm').on('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBookingBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fa fa-spinner fa-spin me-1"></i>Processing Payment...');

        const paymentMethod = $('input[name="payment_method"]:checked').val();

        try {
            if (paymentMethod === 'stripe') {
                // Confirm Stripe payment
                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ url("/booking-success") }}',
                    },
                    redirect: 'if_required'
                });

                if (error) {
                    throw new Error(error.message);
                }

                console.log('Stripe payment confirmed successfully');
            }

            // Submit the form
            console.log('Submitting booking form...');
            this.submit();

        } catch (error) {
            console.error('Payment error:', error);
            $('#payment-errors').text(error.message);
            submitBtn.prop('disabled', false);
            submitBtn.html(originalText);
        }
    });

    // Debug info
    console.log('Book buttons found:', $('.book-btn').length);
    console.log('Stripe key:', '{{ config('services.stripe.key') ? "Set" : "Not set" }}');
});
</script>
@endsection