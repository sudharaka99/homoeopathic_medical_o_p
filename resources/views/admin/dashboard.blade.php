@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('admin.slidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')

                <!-- Set default values if variables are not set -->
                @php
                    $patientCount = $patientCount ?? 0;
                    $doctorCount = $doctorCount ?? 0;
                    $approvedDoctors = $approvedDoctors ?? 0;
                    $pendingDoctors = $pendingDoctors ?? 0;
                    $totalAppointments = $totalAppointments ?? 0;
                    $todayAppointments = $todayAppointments ?? 0;
                    $recentPendingDoctors = $recentPendingDoctors ?? collect();
                    $recentAppointments = $recentAppointments ?? collect();
                    $months = $months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                    $patientRegistrations = $patientRegistrations ?? [0, 0, 0, 0, 0, 0];
                    $doctorRegistrations = $doctorRegistrations ?? [0, 0, 0, 0, 0, 0];
                @endphp

                <!-- Welcome Card -->
                <div class="card border-0 bg-primary  shadow mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">Welcome, Administrator!</h4>
                                <p class="mb-0 opacity-75">Manage your medical platform efficiently</p>
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
                                    <i class="fas fa-users"></i>
                                </div>
                                <h5 class="mb-1">{{ $patientCount }}</h5>
                                <p class="text-muted small mb-0">Total Patients</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <h5 class="mb-1">{{ $doctorCount }}</h5>
                                <p class="text-muted small mb-0">Total Doctors</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h5 class="mb-1">{{ $approvedDoctors }}</h5>
                                <p class="text-muted small mb-0">Approved Doctors</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h5 class="mb-1">{{ $pendingDoctors }}</h5>
                                <p class="text-muted small mb-0">Pending Approvals</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Quick Actions & Pending Approvals -->
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
                                    <a href="{{ route('admin.pendingDoctors') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-warning text-white rounded p-2 me-3">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Review Doctors</h6>
                                            <small class="text-muted">Approve doctor applications</small>
                                            @if($pendingDoctors > 0)
                                                <span class="badge bg-danger ms-2">{{ $pendingDoctors }} pending</span>
                                            @endif
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.doctorslist') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-info text-white rounded p-2 me-3">
                                            <i class="fas fa-list-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Manage Doctors</h6>
                                            <small class="text-muted">View all doctors</small>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded p-2 me-3">
                                            <i class="fas fa-user-injured"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">Manage Patients</h6>
                                            <small class="text-muted">View all patients</small>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <div class="bg-success text-white rounded p-2 me-3">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">All Appointments</h6>
                                            <small class="text-muted">View appointment history</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Stats -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-chart-bar me-2"></i>Appointment Stats
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-primary mb-2">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $totalAppointments }}</h5>
                                            <small class="text-muted">Total</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $todayAppointments }}</h5>
                                            <small class="text-muted">Today</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold text-primary">
                                    <i class="fas fa-chart-line me-2"></i>Monthly Registrations
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="registrationChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Data Section -->
                <div class="row">
                    <!-- Pending Approvals -->
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold text-warning">
                                    <i class="fas fa-clock me-2"></i>Pending Doctor Approvals
                                </h5>
                                <a href="{{ route('admin.pendingDoctors') }}" class="btn btn-sm btn-outline-warning">View All</a>
                            </div>
                            <div class="card-body">
                                @if($recentPendingDoctors->count() > 0)
                                    @foreach($recentPendingDoctors->take(5) as $doctor)
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <div class="bg-light rounded p-2">
                                                        <div class="fw-bold text-primary">
                                                            <i class="fas fa-user-md"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $doctor->name }}</h6>
                                                    <p class="text-muted mb-1 small">{{ $doctor->specialization_name ?? 'General Practitioner' }}</p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ \Carbon\Carbon::parse($doctor->created_at)->format('M j, Y') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <a href="{{ route('admin.pendingDoctors') }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-eye me-1"></i>Review
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-check-circle fa-3x text-success"></i>
                                        </div>
                                        <h6 class="text-muted">No pending approvals</h6>
                                        <p class="text-muted small">All doctor applications are processed</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Appointments -->
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-calendar-alt me-2"></i>Recent Appointments
                                </h5>
                                <a href="#" class="btn btn-sm btn-outline-info">View All</a>
                            </div>
                            <div class="card-body">
                                @if($recentAppointments->count() > 0)
                                    @foreach($recentAppointments->take(5) as $appointment)
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <div class="bg-light rounded p-2">
                                                        <div class="fw-bold text-primary">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $appointment->patient_name }} → Dr. {{ $appointment->doctor_name }}</h6>
                                                    <p class="text-muted mb-1 small">
                                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}
                                                    </p>
                                                    <span class="badge 
                                                        @if($appointment->status == 'confirmed') bg-success
                                                        @elseif($appointment->status == 'pending') bg-warning text-dark
                                                        @elseif($appointment->status == 'cancelled') bg-danger
                                                        @else bg-secondary @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($appointment->created_at)->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-calendar-times fa-3x text-muted"></i>
                                        </div>
                                        <h6 class="text-muted">No recent appointments</h6>
                                        <p class="text-muted small">Appointments will appear here</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Overview -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 fw-bold text-secondary">
                                    <i class="fas fa-tachometer-alt me-2"></i>System Overview
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-primary mb-2">
                                                <i class="fas fa-users fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $patientCount }}</h5>
                                            <small class="text-muted">Patients</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-success mb-2">
                                                <i class="fas fa-user-md fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $doctorCount }}</h5>
                                            <small class="text-muted">Doctors</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-info mb-2">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $approvedDoctors }}</h5>
                                            <small class="text-muted">Approved</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-clock fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $pendingDoctors }}</h5>
                                            <small class="text-muted">Pending</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-primary mb-2">
                                                <i class="fas fa-calendar-alt fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $totalAppointments }}</h5>
                                            <small class="text-muted">Total Appts</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-calendar-day fa-2x"></i>
                                            </div>
                                            <h5 class="mb-1">{{ $todayAppointments }}</h5>
                                            <small class="text-muted">Today's Appts</small>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Registration Chart
        var ctx = document.getElementById('registrationChart').getContext('2d');
        var registrationChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Patients',
                    data: @json($patientRegistrations),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Doctors',
                    data: @json($doctorRegistrations),
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    labels: {
                            color: '#333'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#666'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });

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