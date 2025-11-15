@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('doctor.patients') }}">My Patients</a></li>
                        <li class="breadcrumb-item active">{{ $patient->name }} - Medical Profile</li>
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

                <!-- Patient Header -->
                <div class="card border-0 shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @if($patient->profile_photo_path)
                                    <img src="{{ asset('storage/'.$patient->profile_photo_path) }}" 
                                         alt="{{ $patient->name }}" 
                                         class="rounded-circle me-4" 
                                         width="80" height="80">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-4" 
                                         style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ substr($patient->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h3 class="mb-1">{{ $patient->name }}</h3>
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-envelope me-2"></i>{{ $patient->email }}
                                    </p>
                                    @if($patient->phone)
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-phone me-2"></i>{{ $patient->phone }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">
                                    {{ $appointments->count() }} Appointments
                                </span>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Last visit: 
                                        @if($appointments->count() > 0)
                                            {{ \Carbon\Carbon::parse($appointments->first()->appointment_date)->format('M d, Y') }}
                                        @else
                                            Never
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information Tabs -->
                <ul class="nav nav-tabs mb-4" id="medicalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                            <i class="fas fa-chart-pie me-2"></i>Medical Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                            <i class="fas fa-file-medical me-2"></i>Medical Documents
                            @php
                                $totalDocuments = ($bloodTestReports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $bloodTestReports->total() : $bloodTestReports->count()) +
                                                ($prescriptions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $prescriptions->total() : $prescriptions->count()) +
                                                ($medicalReports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $medicalReports->total() : $medicalReports->count()) +
                                                ($insuranceDocuments instanceof \Illuminate\Pagination\LengthAwarePaginator ? $insuranceDocuments->total() : $insuranceDocuments->count());
                            @endphp
                            <span class="badge bg-primary ms-1">{{ $totalDocuments }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button">
                            <i class="fas fa-calendar me-2"></i>Appointment History
                            <span class="badge bg-primary ms-1">{{ $appointments->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="medicalTabContent">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row">
                            <!-- Medical History -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Medical History</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($medicalInfo->medical_history)
                                            <p class="card-text">{{ $medicalInfo->medical_history }}</p>
                                        @else
                                            <p class="text-muted">No medical history recorded.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Current Medications -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-pills me-2"></i>Current Medications</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($medicalInfo->current_medications)
                                            <p class="card-text">{{ $medicalInfo->current_medications }}</p>
                                        @else
                                            <p class="text-muted">No current medications recorded.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Allergies -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0"><i class="fas fa-allergies me-2"></i>Allergies</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($medicalInfo->allergies)
                                            <p class="card-text">{{ $medicalInfo->allergies }}</p>
                                        @else
                                            <p class="text-muted">No known allergies.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($medicalInfo->emergency_contact_name)
                                            <h6 class="card-title">{{ $medicalInfo->emergency_contact_name }}</h6>
                                            <p class="card-text mb-1">
                                                <strong>Relation:</strong> {{ $medicalInfo->emergency_contact_relation ?? 'Not specified' }}
                                            </p>
                                            <p class="card-text mb-0">
                                                <strong>Phone:</strong> {{ $medicalInfo->emergency_contact_phone }}
                                            </p>
                                        @else
                                            <p class="text-muted">No emergency contact information.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Blood Test Results -->
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-tint me-2"></i>Blood Test Results</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <h6 class="text-primary">Hemoglobin</h6>
                                                <p class="fs-5">{{ $medicalInfo->hemoglobin ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h6 class="text-primary">RBC Count</h6>
                                                <p class="fs-5">{{ $medicalInfo->rbc_count ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h6 class="text-primary">WBC Count</h6>
                                                <p class="fs-5">{{ $medicalInfo->wbc_count ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h6 class="text-primary">Platelet Count</h6>
                                                <p class="fs-5">{{ $medicalInfo->platelet_count ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6 text-center">
                                                <h6 class="text-primary">Blood Sugar</h6>
                                                <p class="fs-5">{{ $medicalInfo->blood_sugar ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <h6 class="text-primary">Cholesterol</h6>
                                                <p class="fs-5">{{ $medicalInfo->cholesterol ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane fade" id="documents" role="tabpanel">

                        <div class="row">
                            <!-- Blood Test Reports -->
                            <div class="col-12 mb-4 document-section" data-type="blood-test">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><i class="fas fa-tint me-2"></i>Blood Test Reports</h6>
                                            <small class="opacity-75" id="bloodTestInfo"></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark me-2" id="bloodTestCount">
                                                {{ $bloodTestReports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $bloodTestReports->total() : $bloodTestReports->count() }}
                                            </span>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#bloodTestCollapse">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="bloodTestCollapse">
                                        <div class="card-body">
                                            @if(($bloodTestReports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $bloodTestReports->total() : $bloodTestReports->count()) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th width="50%">File Name</th>
                                                                <th width="20%">Upload Date</th>
                                                                <th width="15%">File Size</th>
                                                                <th width="15%">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($bloodTestReports as $report)
                                                            <tr class="document-row">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                                        <div>
                                                                            <div class="fw-semibold">{{ basename($report->file_path) }}</div>
                                                                            <small class="text-muted">Blood Test Report</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $filePath = "medical-documents/tbl_blood_test_reports/{$patient->id}/{$report->file_path}";
                                                                        if (Storage::disk('public')->exists($filePath)) {
                                                                            $bytes = Storage::disk('public')->size($filePath);
                                                                            if ($bytes >= 1048576) {
                                                                                $size = number_format($bytes / 1048576, 2) . ' MB';
                                                                            } elseif ($bytes >= 1024) {
                                                                                $size = number_format($bytes / 1024, 2) . ' KB';
                                                                            } else {
                                                                                $size = $bytes . ' bytes';
                                                                            }
                                                                        } else {
                                                                            $size = 'N/A';
                                                                        }
                                                                    @endphp
                                                                    <span class="badge bg-secondary">{{ $size }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <a href="{{ route('doctor.patient.document.view', [$patient->id, 'blood-test-report', $report->id]) }}" 
                                                                           class="btn btn-outline-primary" target="_blank" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('doctor.patient.document.download', [$patient->id, 'blood-test-report', $report->id]) }}" 
                                                                           class="btn btn-outline-success" title="Download">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Pagination -->
                                                @if($bloodTestReports instanceof \Illuminate\Pagination\LengthAwarePaginator && $bloodTestReports->hasPages())
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div class="text-muted">
                                                        Showing {{ $bloodTestReports->firstItem() }} to {{ $bloodTestReports->lastItem() }} of {{ $bloodTestReports->total() }} results
                                                    </div>
                                                    {{ $bloodTestReports->links() }}
                                                </div>
                                                @endif
                                                
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-file-medical-alt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No blood test reports found.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prescriptions -->
                            <div class="col-12 mb-4 document-section" data-type="prescription">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><i class="fas fa-prescription me-2"></i>Prescriptions</h6>
                                            <small class="opacity-75" id="prescriptionsInfo"></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark me-2" id="prescriptionsCount">
                                                {{ $prescriptions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $prescriptions->total() : $prescriptions->count() }}
                                            </span>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#prescriptionsCollapse">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="prescriptionsCollapse">
                                        <div class="card-body">
                                            @if(($prescriptions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $prescriptions->total() : $prescriptions->count()) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th width="50%">File Name</th>
                                                                <th width="20%">Upload Date</th>
                                                                <th width="15%">File Size</th>
                                                                <th width="15%">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($prescriptions as $prescription)
                                                            <tr class="document-row">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-prescription text-primary me-2"></i>
                                                                        <div>
                                                                            <div class="fw-semibold">{{ basename($prescription->file_path) }}</div>
                                                                            <small class="text-muted">Prescription</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($prescription->created_at)->format('M d, Y') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $filePath = "medical-documents/tbl_prescriptions/{$patient->id}/{$prescription->file_path}";
                                                                        if (Storage::disk('public')->exists($filePath)) {
                                                                            $bytes = Storage::disk('public')->size($filePath);
                                                                            if ($bytes >= 1048576) {
                                                                                $size = number_format($bytes / 1048576, 2) . ' MB';
                                                                            } elseif ($bytes >= 1024) {
                                                                                $size = number_format($bytes / 1024, 2) . ' KB';
                                                                            } else {
                                                                                $size = $bytes . ' bytes';
                                                                            }
                                                                        } else {
                                                                            $size = 'N/A';
                                                                        }
                                                                    @endphp
                                                                    <span class="badge bg-secondary">{{ $size }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <a href="{{ route('doctor.patient.document.view', [$patient->id, 'prescription', $prescription->id]) }}" 
                                                                           class="btn btn-outline-primary" target="_blank" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('doctor.patient.document.download', [$patient->id, 'prescription', $prescription->id]) }}" 
                                                                           class="btn btn-outline-success" title="Download">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Pagination -->
                                                @if($prescriptions instanceof \Illuminate\Pagination\LengthAwarePaginator && $prescriptions->hasPages())
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div class="text-muted">
                                                        Showing {{ $prescriptions->firstItem() }} to {{ $prescriptions->lastItem() }} of {{ $prescriptions->total() }} results
                                                    </div>
                                                    {{ $prescriptions->links() }}
                                                </div>
                                                @endif
                                                
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-prescription-bottle-alt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No prescriptions found.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Reports -->
                            <div class="col-12 mb-4 document-section" data-type="medical-report">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Reports</h6>
                                            <small class="opacity-75" id="medicalReportsInfo"></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark me-2" id="medicalReportsCount">
                                                {{ $medicalReports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $medicalReports->total() : $medicalReports->count() }}
                                            </span>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#medicalReportsCollapse">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="medicalReportsCollapse">
                                        <div class="card-body">
                                            @if(($medicalReports instanceof \Illuminate\Pagination\LengthAwarePaginator ? $medicalReports->total() : $medicalReports->count()) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th width="50%">File Name</th>
                                                                <th width="20%">Upload Date</th>
                                                                <th width="15%">File Size</th>
                                                                <th width="15%">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($medicalReports as $report)
                                                            <tr class="document-row">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-medical text-warning me-2"></i>
                                                                        <div>
                                                                            <div class="fw-semibold">{{ basename($report->file_path) }}</div>
                                                                            <small class="text-muted">Medical Report</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $filePath = "medical-documents/tbl_medical_reports/{$patient->id}/{$report->file_path}";
                                                                        if (Storage::disk('public')->exists($filePath)) {
                                                                            $bytes = Storage::disk('public')->size($filePath);
                                                                            if ($bytes >= 1048576) {
                                                                                $size = number_format($bytes / 1048576, 2) . ' MB';
                                                                            } elseif ($bytes >= 1024) {
                                                                                $size = number_format($bytes / 1024, 2) . ' KB';
                                                                            } else {
                                                                                $size = $bytes . ' bytes';
                                                                            }
                                                                        } else {
                                                                            $size = 'N/A';
                                                                        }
                                                                    @endphp
                                                                    <span class="badge bg-secondary">{{ $size }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <a href="{{ route('doctor.patient.document.view', [$patient->id, 'medical-report', $report->id]) }}" 
                                                                           class="btn btn-outline-primary" target="_blank" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('doctor.patient.document.download', [$patient->id, 'medical-report', $report->id]) }}" 
                                                                           class="btn btn-outline-success" title="Download">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Pagination -->
                                                @if($medicalReports instanceof \Illuminate\Pagination\LengthAwarePaginator && $medicalReports->hasPages())
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div class="text-muted">
                                                        Showing {{ $medicalReports->firstItem() }} to {{ $medicalReports->lastItem() }} of {{ $medicalReports->total() }} results
                                                    </div>
                                                    {{ $medicalReports->links() }}
                                                </div>
                                                @endif
                                                
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No medical reports found.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance Documents -->
                            <div class="col-12 document-section" data-type="insurance">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Insurance Documents</h6>
                                            <small class="opacity-75" id="insuranceInfo"></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark me-2" id="insuranceCount">
                                                {{ $insuranceDocuments instanceof \Illuminate\Pagination\LengthAwarePaginator ? $insuranceDocuments->total() : $insuranceDocuments->count() }}
                                            </span>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#insuranceCollapse">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="insuranceCollapse">
                                        <div class="card-body">
                                            @if(($insuranceDocuments instanceof \Illuminate\Pagination\LengthAwarePaginator ? $insuranceDocuments->total() : $insuranceDocuments->count()) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th width="50%">File Name</th>
                                                                <th width="20%">Upload Date</th>
                                                                <th width="15%">File Size</th>
                                                                <th width="15%">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($insuranceDocuments as $document)
                                                            <tr class="document-row">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-invoice text-info me-2"></i>
                                                                        <div>
                                                                            <div class="fw-semibold">{{ basename($document->file_path) }}</div>
                                                                            <small class="text-muted">Insurance Document</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $filePath = "medical-documents/tbl_insurance_documents/{$patient->id}/{$document->file_path}";
                                                                        if (Storage::disk('public')->exists($filePath)) {
                                                                            $bytes = Storage::disk('public')->size($filePath);
                                                                            if ($bytes >= 1048576) {
                                                                                $size = number_format($bytes / 1048576, 2) . ' MB';
                                                                            } elseif ($bytes >= 1024) {
                                                                                $size = number_format($bytes / 1024, 2) . ' KB';
                                                                            } else {
                                                                                $size = $bytes . ' bytes';
                                                                            }
                                                                        } else {
                                                                            $size = 'N/A';
                                                                        }
                                                                    @endphp
                                                                    <span class="badge bg-secondary">{{ $size }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group btn-group-sm">
                                                                        <a href="{{ route('doctor.patient.document.view', [$patient->id, 'insurance-document', $document->id]) }}" 
                                                                           class="btn btn-outline-primary" target="_blank" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('doctor.patient.document.download', [$patient->id, 'insurance-document', $document->id]) }}" 
                                                                           class="btn btn-outline-success" title="Download">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Pagination -->
                                                @if($insuranceDocuments instanceof \Illuminate\Pagination\LengthAwarePaginator && $insuranceDocuments->hasPages())
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <div class="text-muted">
                                                        Showing {{ $insuranceDocuments->firstItem() }} to {{ $insuranceDocuments->lastItem() }} of {{ $insuranceDocuments->total() }} results
                                                    </div>
                                                    {{ $insuranceDocuments->links() }}
                                                </div>
                                                @endif
                                                
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No insurance documents found.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Tab -->
                    <div class="tab-pane fade" id="appointments" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                @if($appointments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                    <th>Fee</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($appointments as $appointment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}</td>
                                                    <td>
                                                        <span class="badge 
                                                            @if($appointment->status == 'confirmed') bg-success
                                                            @elseif($appointment->status == 'completed') bg-primary
                                                            @elseif($appointment->status == 'cancelled') bg-danger
                                                            @else bg-secondary @endif">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                    <td>Rs. {{ number_format($appointment->fee, 2) }}</td>
                                                    <td>{{ $appointment->notes ?? 'No notes' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No appointment history found.</p>
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

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="documentFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Document viewer functionality
    const documentViewerModal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
    const documentFrame = document.getElementById('documentFrame');
    
    // Handle document view links
    document.querySelectorAll('a[href*="document.view"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            documentFrame.src = this.href;
            documentViewerModal.show();
        });
    });

    // Document type filter functionality
    const typeFilter = document.getElementById('documentTypeFilter');
    const expandAll = document.getElementById('expandAll');
    const collapseAll = document.getElementById('collapseAll');
    const documentSections = document.querySelectorAll('.document-section');

    function filterDocuments() {
        const selectedType = typeFilter.value;
        console.log('Selected filter:', selectedType); // Debug log

        documentSections.forEach(section => {
            const sectionType = section.getAttribute('data-type');
            console.log('Section type:', sectionType); // Debug log
            
            // Show/hide entire sections based on filter
            if (selectedType === 'all') {
                section.style.display = 'block';
            } else if (selectedType === sectionType) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        updateSectionInfo();
    }

    function updateSectionInfo() {
        const selectedType = typeFilter.value;
        
        documentSections.forEach(section => {
            const isVisible = section.style.display !== 'none';
            
            if (isVisible) {
                const tbody = section.querySelector('tbody');
                const rows = tbody ? tbody.querySelectorAll('tr.document-row') : [];
                const visibleCount = rows.length;
                
                // Update count badge
                const countBadge = section.querySelector('[id$="Count"]');
                if (countBadge) {
                    countBadge.textContent = visibleCount;
                }
                
                // Update info text
                const infoElement = section.querySelector('.opacity-75');
                if (infoElement) {
                    if (visibleCount > 0) {
                        infoElement.textContent = `${visibleCount} document(s)`;
                    } else {
                        infoElement.textContent = 'No documents';
                    }
                }
            }
        });
    }

    function expandAllSections() {
        documentSections.forEach(section => {
            // Only expand visible sections
            if (section.style.display !== 'none') {
                const collapse = section.querySelector('.collapse');
                if (collapse && !collapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(collapse) || new bootstrap.Collapse(collapse, { toggle: false });
                    bsCollapse.show();
                }
            }
        });
    }

    function collapseAllSections() {
        documentSections.forEach(section => {
            // Only collapse visible sections
            if (section.style.display !== 'none') {
                const collapse = section.querySelector('.collapse');
                if (collapse && collapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(collapse) || new bootstrap.Collapse(collapse, { toggle: false });
                    bsCollapse.hide();
                }
            }
        });
    }

    // Event listeners
    typeFilter.addEventListener('change', filterDocuments);
    expandAll.addEventListener('click', expandAllSections);
    collapseAll.addEventListener('click', collapseAllSections);

    // Initialize on page load
    updateSectionInfo();
});
</script>
@endsection