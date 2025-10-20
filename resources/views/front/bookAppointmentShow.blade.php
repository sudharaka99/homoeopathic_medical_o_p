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
                                                            <th>Status</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($avalabilityList as $availability)
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
                                                                    @if($availability->status == 'available')
                                                                        <span class="badge bg-success">Available</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Booked</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($availability->status == 'available' && $availability->number_of_tokens > 0)
                                                                        <button class="btn btn-primary btn-sm book-btn" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#bookTokenModal"
                                                                                data-availability-id="{{ $availability->id }}"
                                                                                data-date="{{ \Carbon\Carbon::parse($availability->date)->format('M d, Y') }}"
                                                                                data-time="{{ \Carbon\Carbon::parse($availability->start_time_slot)->format('h:i A') }} - {{ \Carbon\Carbon::parse($availability->end_time_slot)->format('h:i A') }}"
                                                                                data-tokens="{{ $availability->number_of_tokens }}">
                                                                            <i class="fa fa-calendar-plus me-1"></i> Book
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
            <form action="" method="POST" id="bookTokenForm">
                @csrf
                <input type="hidden" name="availability_id" id="availability_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2">Appointment Details</h6>
                        <p class="mb-1"><strong>Doctor:</strong> Dr. {{ $doctor->doctor_name ?? 'Doctor' }}</p>
                        <p class="mb-1" id="slotDate"></p>
                        <p class="mb-0" id="slotTime"></p>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check me-1"></i>Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection