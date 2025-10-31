@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
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
                <div class="card border-0 shadow mb-4">
                    <div class="card-body text-center py-4">
                        <h3 class="mb-2 text-primary">Welcome, Administrator!</h3>
                        <p class="text-muted mb-0">Manage your medical platform efficiently</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <!-- Total Patients -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-center text-white p-4">
                                <div class="mb-3">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <h4 class="mb-1">{{ $patientCount }}</h4>
                                <p class="mb-0">Total Patients</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Doctors -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body text-center text-white p-4">
                                <div class="mb-3">
                                    <i class="fas fa-user-md fa-2x"></i>
                                </div>
                                <h4 class="mb-1">{{ $doctorCount }}</h4>
                                <p class="mb-0">Total Doctors</p>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Doctors -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-center text-white p-4">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h4 class="mb-1">{{ $approvedDoctors }}</h4>
                                <p class="mb-0">Approved Doctors</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row Statistics -->
                <div class="row">
                    <!-- Pending Approvals -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                            <div class="card-body text-center text-white p-4">
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h4 class="mb-1">{{ $pendingDoctors }}</h4>
                                <p class="mb-0">Pending Approvals</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Appointments -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                </div>
                                <h4 class="mb-1 text-dark">{{ $totalAppointments }}</h4>
                                <p class="mb-0 text-muted">Total Appointments</p>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Appointments -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-day fa-2x text-warning"></i>
                                </div>
                                <h4 class="mb-1 text-dark">{{ $todayAppointments }}</h4>
                                <p class="mb-0 text-muted">Today's Appointments</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Quick Actions</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <a href="{{ route('admin.pendingDoctors') }}" class="btn btn-primary w-100 py-2">
                                            <i class="fas fa-user-md me-2"></i>Review Doctor Applications
                                            @if($pendingDoctors > 0)
                                                <span class="badge bg-warning ms-2">{{ $pendingDoctors }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <a href="#" class="btn btn-success w-100 py-2">
                                            <i class="fas fa-calendar me-2"></i>Manage Appointments
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Recent Data -->
                <div class="row">
                    <!-- Registration Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Monthly Registrations</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="registrationChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Pending Approvals</h5>
                                <a href="{{ route('admin.pendingDoctors') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if($recentPendingDoctors->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($recentPendingDoctors as $doctor)
                                        <div class="list-group-item px-0 border-0">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $doctor->name }}</h6>
                                                    <small class="text-muted">{{ $doctor->specialization_name ?? 'General Practitioner' }}</small>
                                                </div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($doctor->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <p class="text-muted mb-0">No pending approvals</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Recent Appointments</h5>
                            </div>
                            <div class="card-body">
                                @if($recentAppointments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Patient</th>
                                                    <th>Doctor</th>
                                                    <th>Date & Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentAppointments as $appointment)
                                                <tr>
                                                    <td>{{ $appointment->patient_name }}</td>
                                                    <td>{{ $appointment->doctor_name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $appointment->status == 'confirmed' ? 'success' : ($appointment->status == 'pending' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No recent appointments</p>
                                    </div>
                                @endif
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
    // Registration Chart
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection