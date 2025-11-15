@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Patients</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.doctor.slidebar')
            </div>

            <div class="col-lg-9">
                @include('front.message')

                <div class="card border-0 shadow mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fs-4 mb-0">
                                <i class="fas fa-user-injured text-primary me-2"></i>My Patients
                            </h3>
                            <span class="badge bg-primary">
                                {{ count($patients) }} Patients
                            </span>
                        </div>

                        @if(count($patients) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Contact Info</th>
                                            <th>Medical Summary</th>
                                            <th>Appointments</th>
                                            <th>Last Visit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($patients as $patientId => $patient)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($patient['patient_photo'])
                                                        <img src="{{ asset('storage/'.$patient['patient_photo']) }}" 
                                                             alt="{{ $patient['patient_name'] }}" 
                                                             class="rounded-circle me-3" 
                                                             width="50" height="50">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 50px; height: 50px;">
                                                            {{ substr($patient['patient_name'], 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1">{{ $patient['patient_name'] }}</h6>
                                                        <small class="text-muted">Patient ID: #{{ $patientId }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i>{{ $patient['patient_email'] }}
                                                    </div>
                                                    @if($patient['patient_phone'])
                                                    <div class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>{{ $patient['patient_phone'] }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    @if($patient['medical_history'])
                                                        <span class="badge bg-info mb-1">Has Medical History</span>
                                                    @endif
                                                    @if($patient['current_medications'])
                                                        <span class="badge bg-warning mb-1">On Medications</span>
                                                    @endif
                                                    @if($patient['allergies'])
                                                        <span class="badge bg-danger mb-1">Has Allergies</span>
                                                    @endif
                                                    @if(!$patient['medical_history'] && !$patient['current_medications'] && !$patient['allergies'])
                                                        <span class="text-muted">No medical info</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $patient['appointments_count'] }} visits
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($patient['last_appointment'])->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('doctor.patient.medical-details', $patientId) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-medical me-1"></i>View Medical Profile
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-user-injured fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">No Patients Yet</h4>
                                <p class="text-muted mb-4">You don't have any patients with confirmed appointments.</p>
                                <a href="{{ route('doctor.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-calendar me-2"></i>View Appointments
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