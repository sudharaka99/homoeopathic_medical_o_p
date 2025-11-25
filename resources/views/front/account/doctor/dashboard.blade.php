@extends('front.layouts.app')

@section('main')

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Doctor Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('front.account.doctor.slidebar')
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                @include('front.message')

                <!-- Welcome Message -->
                <div class="card border-0 bg-primary  shadow mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">Welcome, Dr. {{ Auth::user()->name }}!</h4>
                                <p class="mb-0 opacity-75">Manage your appointments, patients, and availability from your dashboard.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="bg-white bg-opacity-25 rounded p-2 d-inline-block">
                                    <small class="d-block">Today's Date</small>
                                    <strong>{{ \Carbon\Carbon::now()->format('F j, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <h5 class="mb-1">{{ $todayAppointmentsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Today's Appointments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="mb-1">{{ $totalPatientsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Total Patients</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h5 class="mb-1">{{ $pendingAppointmentsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h5 class="mb-1">{{ $upcomingSlotsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Available Slots</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Today's Appointments -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold text-primary">
                                    <i class="fas fa-calendar-day me-2"></i>Today's Appointments
                                </h5>
                                <div>
                                    <a href="{{ route('doctor.appointments') }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-list me-1"></i>View All
                                    </a>
                                    <a href="{{ route('doctor.addAvailability') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add Slot
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(isset($todayAppointments) && $todayAppointments->count() > 0)
                                    @foreach($todayAppointments->take(5) as $appointment)
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <div class="bg-light rounded p-2">
                                                        <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</div>
                                                        <small class="text-muted">Token #{{ $appointment->token_number }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $appointment->patient_name }}</h6>
                                                    <p class="text-muted mb-1 small">{{ $appointment->patient_email }}</p>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <span class="badge 
                                                            @if($appointment->status == 'pending') bg-warning text-dark
                                                            @elseif($appointment->status == 'confirmed') bg-success text-white
                                                            @elseif($appointment->status == 'cancelled') bg-danger text-white
                                                            @elseif($appointment->status == 'completed') bg-secondary text-white
                                                            @else bg-info text-white @endif">
                                                            @if($appointment->status == 'pending')
                                                                <i class="fas fa-clock me-1"></i>
                                                            @elseif($appointment->status == 'confirmed')
                                                                <i class="fas fa-check me-1"></i>
                                                            @elseif($appointment->status == 'cancelled')
                                                                <i class="fas fa-times me-1"></i>
                                                            @elseif($appointment->status == 'completed')
                                                                <i class="fas fa-check-circle me-1"></i>
                                                            @endif
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                        @if($appointment->payment_status == 'paid')
                                                            <span class="badge bg-success text-white">
                                                                <i class="fas fa-check-circle me-1"></i>Paid
                                                            </span>
                                                        @endif
                                                        @if($appointment->zoom_meeting_id && $appointment->status == 'confirmed')
                                                            <span class="badge bg-primary text-white">
                                                                <i class="fas fa-video me-1"></i>Virtual
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('doctor.appointments.details', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>Details
                                                        </a>
                                                        @if($appointment->status == 'confirmed' && $appointment->zoom_join_url)
                                                            <a href="{{ $appointment->zoom_join_url }}" target="_blank" class="btn btn-sm btn-success">
                                                                <i class="fas fa-video me-1"></i>Join
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-calendar-times fa-3x text-warning"></i>
                                        </div>
                                        <h6 class="text-muted">No appointments today</h6>
                                        <p class="text-muted small">Your schedule is clear for today</p>
                                        <a href="{{ route('doctor.addAvailability') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Add Availability
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Recent Patients -->
                    <div class="col-lg-4 mb-4">
                        <!-- Quick Actions -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-success">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('doctor.addAvailability') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded p-2 me-3">
                                            <i class="fas fa-calendar-plus"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Add Availability</h6>
                                            <small class="text-muted">Create new time slots</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('doctor.appointments') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-info text-white rounded p-2 me-3">
                                            <i class="fas fa-list-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Manage Appointments</h6>
                                            <small class="text-muted">View all appointments</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('doctor.patients') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-success text-white rounded p-2 me-3">
                                            <i class="fas fa-user-injured"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">My Patients</h6>
                                            <small class="text-muted">View patient records</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('doctor.profile') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-warning text-white rounded p-2 me-3">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Update Profile</h6>
                                            <small class="text-muted">Edit your information</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Patients -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-user-injured me-2"></i>Recent Patients
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($recentPatients) && $recentPatients->count() > 0)
                                    @foreach($recentPatients->take(4) as $patient)
                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                            @if($patient->patient_photo)
                                                <img src="{{ asset('profile_pic/thumb/' . $patient->patient_photo) }}" 
                                                     class="rounded-circle me-3 object-fit-cover" width="45" height="45" alt="{{ $patient->patient_name }}">
                                            @else
                                                <img src="{{ asset('front/img/patient-default.jpg') }}" 
                                                     class="rounded-circle me-3 object-fit-cover" width="45" height="45" alt="{{ $patient->patient_name }}">
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-dark">{{ $patient->patient_name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar text-success me-1"></i>
                                                    {{ \Carbon\Carbon::parse($patient->last_appointment)->format('M j') }}
                                                </small>
                                            </div>
                                            <a href="{{ route('doctor.patient.medical-details', $patient->patient_id) }}" class="btn btn-sm btn-outline-info" title="View Medical Details">
                                                <i class="fas fa-file-medical"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                    <div class="text-center mt-3">
                                        <a href="{{ route('doctor.patients') }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-users me-1"></i>View All Patients
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-2">
                                            <i class="fas fa-user-injured fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-muted">No recent patients</h6>
                                        <p class="text-muted small">Patients will appear here after appointments</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Availability & Quick Stats -->
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-warning">
                                    <i class="fas fa-clock me-2"></i>Upcoming Availability
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($upcomingAvailability) && $upcomingAvailability->count() > 0)
                                    @foreach($upcomingAvailability->take(4) as $slot)
                                        <div class="border-bottom pb-2 mb-2">
                                            <div class="row align-items-center">
                                                <div class="col-8">
                                                    <h6 class="mb-1 fw-bold text-dark">
                                                        {{ \Carbon\Carbon::parse($slot->date)->format('M j, Y') }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock text-primary me-1"></i>
                                                        {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }} - 
                                                        {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                                    </small>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-ticket-alt me-1"></i>{{ $slot->number_of_tokens }} slots
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="text-center mt-3">
                                        <a href="{{ route('doctor.addAvailability') }}" class="btn btn-sm btn-outline-warning w-100">
                                            <i class="fas fa-calendar-plus me-1"></i>Manage Availability
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-2">
                                            <i class="fas fa-calendar-times fa-2x text-warning"></i>
                                        </div>
                                        <h6 class="text-muted">No upcoming availability</h6>
                                        <p class="text-muted small">Add time slots to start receiving appointments</p>
                                        <a href="{{ route('doctor.addAvailability') }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-plus me-1"></i>Add Slots
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-secondary">
                                    <i class="fas fa-chart-bar me-2"></i>Appointment Statistics
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-primary mb-2">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $completedAppointmentsCount ?? 0 }}</h5>
                                            <small class="text-muted">Completed</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-clock fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $pendingAppointmentsCount ?? 0 }}</h5>
                                            <small class="text-muted">Pending</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-success mb-2">
                                                <i class="fas fa-calendar-check fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $confirmedAppointmentsCount ?? 0 }}</h5>
                                            <small class="text-muted">Confirmed</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-danger mb-2">
                                                <i class="fas fa-times-circle fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $cancelledAppointmentsCount ?? 0 }}</h5>
                                            <small class="text-muted">Cancelled</small>
                                        </div>
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

@endsection

@section('customJS')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('bg-primary')) {
                    this.style.transform = 'translateY(-2px)';
                    this.style.transition = 'transform 0.2s ease';
                }
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection