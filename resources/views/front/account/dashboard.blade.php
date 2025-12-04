@extends('front.layouts.app')

@section('main')

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">Patient Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('front.account.slidebar')
            </div>

            <!-- Dashboard Content -->
            <div class="col-lg-9">
                @include('front.message')

                <!-- Welcome Card -->
                <div class="card border-0 bg-light shadow mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">Welcome back, <strong>{{ Auth::user()->name }}</strong>!</h4>
                                <p class="mb-0 text-muted">Here's your health summary for today.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="bg-primary rounded p-2 d-inline-block">
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
                                <h5 class="mb-1">{{ $upcomingAppointmentsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Upcoming Appointments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <h5 class="mb-1">{{ $doctorsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">My Doctors</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <h5 class="mb-1">{{ $medicalDocumentsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Medical Documents</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-bookmark"></i>
                                </div>
                                <h5 class="mb-1">{{ $savedDoctorsCount ?? 0 }}</h5>
                                <p class="text-muted small mb-0">Saved Doctors</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Upcoming Appointments -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>Upcoming Appointments
                                </h5>
                                <div>
                                    <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-list me-1"></i>View All
                                    </a>
                                    <a href="{{ route('patient.findDoctors') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Book New
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(isset($appointments) && $appointments->count() > 0)
                                    @foreach($appointments->take(3) as $appointment)
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <div class="border rounded p-2 bg-light">
                                                        <div class="fw-bold text-primary fs-5">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</div>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</small>
                                                        <div class="small text-muted">{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $appointment->doctor_name ?? 'Doctor Name' }}</h6>
                                                    <p class="text-muted mb-1 small">
                                                        <i class="fas fa-hospital text-info me-1"></i>{{ $appointment->clinic_name ?? 'Medical Center' }}
                                                    </p>
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
                                                            @endif
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                        @if($appointment->payment_status == 'paid')
                                                            <span class="badge bg-success text-white">
                                                                <i class="fas fa-check-circle me-1"></i>Paid
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Pending Payment
                                                            </span>
                                                        @endif
                                                        @if($appointment->zoom_meeting_id && $appointment->status == 'confirmed')
                                                            <span class="badge bg-primary text-white">
                                                                <i class="fas fa-video me-1"></i>Virtual
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($appointment->token_number)
                                                        <small class="text-muted">
                                                            <i class="fas fa-ticket-alt text-success me-1"></i>Token #{{ $appointment->token_number }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('patient.appointment.details', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>Details
                                                        </a>
                                                        @if($appointment->status == 'confirmed' && $appointment->zoom_join_url)
                                                            <a href="{{ route('patient.join.meeting', $appointment->id) }}" target="_blank" class="btn btn-sm btn-success">
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
                                        <h6 class="text-muted">No appointments scheduled</h6>
                                        <p class="text-muted small">Book your first appointment with a doctor</p>
                                        <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>Find Doctors
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Recent Doctors -->
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
                                    <a href="{{ route('patient.findDoctors') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded p-2 me-3">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Find Doctors</h6>
                                            <small class="text-muted">Book a new appointment</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('account.savedDoctors', Auth::id()) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-warning text-white rounded p-2 me-3">
                                            <i class="fas fa-bookmark"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Saved Doctors</h6>
                                            <small class="text-muted">View your favorite doctors</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('patient.myDetails') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-info text-white rounded p-2 me-3">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Medical Profile</h6>
                                            <small class="text-muted">Update your health information</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('patient.appointments') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded p-2 me-3">
                                            <i class="fas fa-history"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Appointment History</h6>
                                            <small class="text-muted">View past appointments</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Doctors -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-user-md me-2"></i>Recent Doctors
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($recentDoctors) && $recentDoctors->count() > 0)
                                    @foreach($recentDoctors->take(3) as $doctor)
                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                            <img src="{{ $doctor->profile_picture ? asset('Uploads/doctors/' . $doctor->profile_picture) : asset('front/img/doctor-default.jpg') }}" 
                                                 class="rounded-circle me-3 object-fit-cover" width="50" height="50" alt="{{ $doctor->doctor_name }}">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-dark">{{ $doctor->doctor_name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-stethoscope text-success me-1"></i>{{ $doctor->specialization_name ?? 'General Practitioner' }}
                                                </small>
                                            </div>

                                        </div>
                                    @endforeach
                                    <div class="text-center mt-3">
                                        <a href="{{ route('patient.findDoctors') }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-search me-1"></i>Browse All Doctors
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-2">
                                            <i class="fas fa-user-md fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-muted">No recent doctors</h6>
                                        <p class="text-muted small">Start by booking an appointment</p>
                                        <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search me-1"></i>Find Doctors
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Tips Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold text-danger">
                                    <i class="fas fa-heartbeat me-2"></i>Health & Wellness Tips
                                </h5>
                                <small class="text-muted">Stay informed for better health</small>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100 hover-shadow">
                                            <div class="card-body p-3">
                                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-tint"></i>
                                                </div>
                                                <h6 class="mb-1 fw-semibold text-dark">Stay Hydrated</h6>
                                                <p class="text-muted small mb-3">Drink 8-10 glasses of water daily to maintain optimal health.</p>
                                                <a href="{{ route('health-tips.hydration') }}" class="btn btn-outline-primary btn-sm fw-medium">
                                                    <i class="fas fa-book-open me-1"></i>Read More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100 hover-shadow">
                                            <div class="card-body p-3">
                                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-running"></i>
                                                </div>
                                                <h6 class="mb-1 fw-semibold text-dark">Daily Exercise</h6>
                                                <p class="text-muted small mb-3">Engage in 30 minutes of physical activity daily.</p>
                                                <a href="{{ route('health-tips.exercise') }}" class="btn btn-outline-success btn-sm fw-medium">
                                                    <i class="fas fa-book-open me-1"></i>Read More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100 hover-shadow">
                                            <div class="card-body p-3">
                                                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-bed"></i>
                                                </div>
                                                <h6 class="mb-1 fw-semibold text-dark">Proper Sleep</h6>
                                                <p class="text-muted small mb-3">Aim for 7-9 hours of quality sleep per night.</p>
                                                <a href="{{ route('health-tips.sleep') }}" class="btn btn-outline-info btn-sm fw-medium">
                                                    <i class="fas fa-book-open me-1"></i>Read More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100 hover-shadow">
                                            <div class="card-body p-3">
                                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-apple-alt"></i>
                                                </div>
                                                <h6 class="mb-1 fw-semibold text-dark">Balanced Diet</h6>
                                                <p class="text-muted small mb-3">Incorporate a variety of fruits and vegetables.</p>
                                                <a href="{{ route('health-tips.diet') }}" class="btn btn-outline-warning btn-sm fw-medium">
                                                    <i class="fas fa-book-open me-1"></i>Read More
                                                </a>
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
    </div>
</section>

@endsection

@section('customJs')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to cards
        const cards = document.querySelectorAll('.hover-shadow');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'transform 0.3s ease';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection