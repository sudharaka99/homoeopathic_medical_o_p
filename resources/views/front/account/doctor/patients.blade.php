@extends('front.layouts.app')

@section('main')
<style>
    /* Custom spacing adjustments */
    .section-5 {
        padding-top: 30px !important;
        padding-bottom: 30px !important;
    }
    
    /* Compact table styling */
    .compact-table td, .compact-table th {
        padding: 8px 12px !important;
        vertical-align: middle !important;
    }
    
    /* Patient image size */
    .patient-avatar {
        width: 50px !important;
        height: 50px !important;
        object-fit: cover;
    }
</style>

<section class="section-5 bg-2">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('doctor.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">My Patients</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('front.account.doctor.slidebar')
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                @include('front.message')

                <div class="card border-0 shadow mb-4">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fs-4 mb-0">
                                <i class="fas fa-user-injured text-primary me-2"></i>
                                My Patients
                            </h3>
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                {{ count($patients) }} Patients
                            </span>
                        </div>

                        @if(count($patients) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle compact-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Contact Info</th>
                                            <th>Medical Summary</th>
                                            <th>Visits</th>
                                            <th>Last Visit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($patients as $patientId => $patient)
                                        <tr>
                                            <!-- Patient Column -->
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($patient['patient_photo'])
                                                        <img src="{{ asset('profile_pic/thumb/' . $patient['patient_photo']) }}"
                                                             class="rounded-circle patient-avatar">
                                                    @else
                                                        <img src="{{ asset('assets/images/avatar7.png') }}"
                                                             class="rounded-circle patient-avatar">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">
                                                            {{ $patient['patient_name'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Contact Info -->
                                            <td>
                                                <div class="small">
                                                    <div class="text-muted mb-1">
                                                        <i class="fas fa-envelope me-1"></i>
                                                        {{ $patient['patient_email'] }}
                                                    </div>
                                                    @if($patient['patient_phone'])
                                                        <div class="text-muted">
                                                            <i class="fas fa-phone me-1"></i>
                                                            {{ $patient['patient_phone'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Medical Summary -->
                                            <td>
                                                <div class="d-flex flex-wrap gap-1 small">
                                                    @if($patient['medical_history'])
                                                        <span class="badge bg-info">History</span>
                                                    @endif
                                                    @if($patient['current_medications'])
                                                        <span class="badge bg-warning">Medications</span>
                                                    @endif
                                                    @if($patient['allergies'])
                                                        <span class="badge bg-danger">Allergies</span>
                                                    @endif
                                                    @if(!$patient['medical_history'] && !$patient['current_medications'] && !$patient['allergies'])
                                                        <span class="text-muted">No info</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Visits -->
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $patient['appointments_count'] }} visits
                                                </span>
                                            </td>

                                            <!-- Last Visit -->
                                            <td>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($patient['last_appointment'])->format('M d, Y') }}
                                                </small>
                                            </td>

                                            <!-- Actions -->
                                            <td>
                                                <a href="{{ route('doctor.patient.medical-details', $patientId) }}"
                                                   class="btn btn-sm btn-primary">
                                                   <i class="fas fa-file-medical me-1"></i>
                                                   View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <i class="fas fa-user-injured fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No Patients Yet</h4>
                                <p class="text-muted mb-4">
                                    You don't have any patients with confirmed appointments.
                                </p>
                                <a href="{{ route('doctor.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-calendar me-2"></i> View Appointments
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection