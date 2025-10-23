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

                <!-- Welcome Card with Stats -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 bg-primary shadow">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h4 class="mb-1">Welcome back, <strong>{{ Auth::user()->name }}</strong>!</h4>
                                        <p class="mb-0 opacity-75">Here's a summary of your health activities.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Upcoming Appointments -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold">Upcoming Appointments</h5>
                                <a href="" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if(isset($appointments) && $appointments->count() > 0)
                                    @foreach($appointments->take(3) as $appointment)
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <div class="bg-light rounded p-2">
                                                        <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</div>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fw-bold">{{ $appointment->doctor->doctor_name ?? 'Doctor Name' }}</h6>
                                                    <p class="text-muted mb-1 small">{{ $appointment->doctor->specialization_name ?? 'General Practitioner' }}</p>
                                                    <span class="badge 
                                                        @if($appointment->status == 'pending') bg-warning text-dark
                                                        @elseif($appointment->status == 'confirmed') bg-success 
                                                        @elseif($appointment->status == 'cancelled') bg-danger
                                                        @else bg-secondary @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <a href="{{ route('appointment.view', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-calendar-times fa-3x"></i>
                                        </div>
                                        <h6 class="text-muted">No appointments scheduled</h6>
                                        <p class="text-muted small">Book your first appointment with a doctor</p>
                                        <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary btn-sm">Find Doctors</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Doctors -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold">Recent Doctors</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($recentDoctors) && $recentDoctors->count() > 0)
                                    @foreach($recentDoctors->take(3) as $doctor)
                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                            <img src="{{ $doctor->license_image ? asset('Uploads/doctors/' . $doctor->license_image) : asset('front/img/doctor-default.jpg') }}" 
                                                 class="rounded-circle me-3 object-fit-cover" width="50" height="50" alt="{{ $doctor->doctor_name }}">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $doctor->doctor_name }}</h6>
                                                <small class="text-muted">{{ $doctor->specialization_name ?? 'General Practitioner' }}</small>
                                            </div>
                                            <a href="{{ route('doctor.show', $doctor->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                    <div class="text-center mt-3">
                                        <a href="{{ route('patient.findDoctors') }}" class="btn btn-sm btn-outline-primary w-100">Browse All Doctors</a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <h6 class="text-muted">No recent doctors</h6>
                                        <p class="text-muted small">Start by booking an appointment</p>
                                        <a href="{{ route('patient.findDoctors') }}" class="btn btn-primary btn-sm">Find Doctors</a>
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
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold">Health Tips</h5>
                            </_`xaiArtifact artifact_id="c0c653f5-1758-47e8-8ebe-551b6cd0aeb6" title="dashboard.blade.php" contentType="text/php">

                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100">
                                            <div class="card-body p-3">
                                                <h6 class="mb-1 fw-semibold">Stay Hydrated</h6>
                                                <p class="text-muted small mb-3">Drink 8-10 glasses of water daily to maintain optimal health.</p>
                                                <a href="{{ route('health-tips.hydration') }}" class="btn btn-outline-primary btn-sm fw-medium">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100">
                                            <div class="card-body p-3">
                                                <h6 class="mb-1 fw-semibold">Daily Exercise</h6>
                                                <p class="text-muted small mb-3">Engage in 30 minutes of physical activity daily.</p>
                                                <a href="{{ route('health-tips.exercise') }}" class="btn btn-outline-primary btn-sm fw-medium">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100">
                                            <div class="card-body p-3">
                                                <h6 class="mb-1 fw-semibold">Proper Sleep</h6>
                                                <p class="text-muted small mb-3">Aim for 7-9 hours of quality sleep per night.</p>
                                                <a href="{{ route('health-tips.sleep') }}" class="btn btn-outline-primary btn-sm fw-medium">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="card text-center border-0 bg-light h-100">
                                            <div class="card-body p-3">
                                                <h6 class="mb-1 fw-semibold">Balanced Diet</h6>
                                                <p class="text-muted small mb-3">Incorporate a variety of fruits and vegetables.</p>
                                                <a href="{{ route('health-tips.diet') }}" class="btn btn-outline-primary btn-sm fw-medium">Read More</a>
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
