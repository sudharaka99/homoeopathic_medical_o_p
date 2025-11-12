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

                        <!-- Availability Table -->
                        <div class="row">
                            <div class="col-lg-12">
                                @include('front.message')

                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0 text-primary">
                                                <i class="fa fa-calendar-alt me-2"></i>
                                                @if(isset($doctors) && $doctors->isNotEmpty())
                                                    Available Time Slots for Dr. {{ $doctors->first()->doctor_name ?? 'Doctor' }}
                                                @else
                                                    Available Time Slots
                                                @endif
                                            </h5>
                                            <a href="{{ route('patient.appointments') }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-calendar-check me-1"></i> My Appointments
                                            </a>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if(isset($avalabilityList) && $avalabilityList->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle">
                                                    <thead class="table-light">
                                                        <tr class="text-center">
                                                            <th>Date</th>
                                                            <th>Start Time</th>
                                                            <th>End Time</th>
                                                            <th>Duration</th>
                                                            <th>Tokens</th>
                                                            <th>Fee (Rs)</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($avalabilityList as $availability)
                                                            @php
                                                                // Get the specific doctor for this page
                                                                $doctor = $doctors->first() ?? null;
                                                                $start = \Carbon\Carbon::parse($availability->start_time_slot);
                                                                $end = \Carbon\Carbon::parse($availability->end_time_slot);
                                                                $duration = $start->diffInMinutes($end);
                                                                
                                                                // Use the specific doctor's fee
                                                                $fee = $doctor ? ($doctor->appointment_fee ?? 0) : 0;
                                                                
                                                                // Calculate estimated token number for display in confirmation
                                                                $totalSlots = $availability->number_of_tokens + 1; // Estimate total slots
                                                                $estimatedTokenNumber = $totalSlots - $availability->number_of_tokens;
                                                            @endphp
                                                            <tr class="text-center">
                                                                <td class="fw-bold">
                                                                    {{ \Carbon\Carbon::parse($availability->date)->format('M d, Y') }}
                                                                    @if(\Carbon\Carbon::parse($availability->date)->isToday())
                                                                        <span class="badge bg-info ms-1">Today</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $start->format('h:i A') }}</td>
                                                                <td>{{ $end->format('h:i A') }}</td>
                                                                <td>{{ $duration }} min</td>
                                                                <td>
                                                                    <span class="badge {{ $availability->number_of_tokens > 0 ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ $availability->number_of_tokens }}
                                                                    </span>
                                                                </td>
                                                                <td><strong>Rs. {{ number_format($fee, 2) }}</strong></td>
                                                                <td>
                                                                    @if($availability->status == 'available')
                                                                        <span class="badge bg-success">Available</span>
                                                                    @elseif($availability->status == 'booked')
                                                                        <span class="badge bg-warning text-dark">Booked</span>
                                                                    @elseif($availability->status == 'completed')
                                                                        <span class="badge bg-info">Completed</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ ucfirst($availability->status) }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($availability->status == 'available' && $availability->number_of_tokens > 0)
                                                                        <button type="button" 
                                                                                class="btn btn-sm btn-primary book-btn" 
                                                                                data-availability-id="{{ $availability->id }}"
                                                                                data-fee="{{ $fee }}"
                                                                                data-doctor-name="{{ $doctor->doctor_name ?? 'Doctor' }}"
                                                                                data-estimated-token="{{ $estimatedTokenNumber }}"
                                                                                title="Book Appointment">
                                                                            <i class="fa fa-calendar-plus me-1"></i> Book
                                                                        </button>
                                                                    @elseif($availability->status == 'booked')
                                                                        <span class="badge bg-warning text-dark">Fully Booked</span>
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
                                                <p class="text-muted">This doctor doesn't have any available slots right now.</p>
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

<!-- SweetAlert2 + Booking Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.book-btn').forEach(button => {
        button.addEventListener('click', function() {
            let availabilityId = this.dataset.availabilityId;
            let fee = this.dataset.fee;
            let doctorName = this.dataset.doctorName;
            let estimatedToken = this.dataset.estimatedToken;

            // Confirmation Dialog WITH Token Number
            Swal.fire({
                title: 'Confirm Booking',
                html: `<div class="text-start">
                         <p>Book appointment with <strong>Dr. ${doctorName}</strong>?</p>
                         <p><strong>Consultation Fee:</strong> Rs. ${parseFloat(fee).toLocaleString()}</p>
                         <div class="my-3 p-2 bg-light rounded">
                             <p class="mb-1 text-muted small">Your Token Number Will Be</p>
                             <h4 class="text-primary mb-0">#${estimatedToken}</h4>
                         </div>
                         <p class="text-muted small">Click "Confirm Booking" to proceed with this token number.</p>
                       </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm Booking',
                cancelButtonText: 'Cancel',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing Booking...',
                        html: `Booking appointment with <strong>Dr. ${doctorName}</strong><br>
                               <small>Token Number: #${estimatedToken}</small>`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make API request
                    fetch("{{ route('front.bookAppointment') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            availability_id: availabilityId 
                        })
                    })
                    .then(async (response) => {
                        const data = await response.json();
                        
                        if (data.success) {
                            // Success Dialog - Show actual token number
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Confirmed!',
                                html: `<div class="text-center">
                                         <div class="mb-3">
                                             <i class="fa fa-check-circle text-success fa-3x"></i>
                                         </div>
                                         <p class="fw-bold fs-5">Appointment Booked Successfully!</p>
                                         <p class="mb-3">With <strong>Dr. ${data.doctor_name}</strong></p>
                                         
                                         <div class="my-4 p-3 bg-success text-white rounded-3">
                                             <p class="mb-2 small">Your Confirmed Token Number</p>
                                             <h1 class="display-6 fw-bold mb-0">#${data.token_number}</h1>
                                         </div>
                                         
                                         <p class="text-muted mb-0">
                                             <i class="fa fa-info-circle me-1"></i>
                                             Please remember your token number for your visit
                                         </p>
                                       </div>`,
                                showConfirmButton: true,
                                confirmButtonText: 'OK, Got It!',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Booking Failed',
                                text: data.message || 'Something went wrong! Please try again.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Booking Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'Please check your internet connection and try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        });
    });
});
</script>
@endsection