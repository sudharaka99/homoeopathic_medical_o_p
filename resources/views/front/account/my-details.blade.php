@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">My Medical Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('front.account.slidebar')
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                @include('front.message')

                <!-- Medical Information Form -->
                <div class="card border border-light shadow mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="fas fa-heartbeat me-2"></i>My Medical Profile</h4>
                        <p class="mb-0 mt-1 small">Keep your health information organized and accessible</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('account.medicalInfo.store') }}" method="POST" id="patientMedicalForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs mb-4" id="medicalTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                        <i class="fas fa-history me-2"></i>Health History
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                                        <i class="fas fa-chart-line me-2"></i>Test Results
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                        <i class="fas fa-file-medical me-2"></i>My Documents
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab">
                                        <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="medicalTabContent">
                                <!-- Medical History Tab -->
                                <div class="tab-pane fade show active" id="history" role="tabpanel">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This information helps doctors provide you with the best possible care. Please be accurate and complete.
                                    </div>

                                    <div class="mb-4">
                                        <label for="medical_history" class="form-label fw-semibold">
                                            <i class="fas fa-notes-medical text-primary me-2"></i>
                                            Medical Conditions & History
                                        </label>
                                        <textarea class="form-control @error('medical_history') is-invalid @enderror" 
                                                  id="medical_history" name="medical_history" rows="4" 
                                                  placeholder="Please list any past or current medical conditions, surgeries, hospitalizations, or chronic illnesses. For example: 'Appendectomy in 2020, Diagnosed with hypertension in 2018, Asthma since childhood'">{{ old('medical_history', $medicalInfo->medical_history ?? '') }}</textarea>
                                        @error('medical_history')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Include dates if possible for better tracking</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="current_medications" class="form-label fw-semibold">
                                            <i class="fas fa-pills text-success me-2"></i>
                                            Current Medications & Supplements
                                        </label>
                                        <textarea class="form-control @error('current_medications') is-invalid @enderror" 
                                                  id="current_medications" name="current_medications" rows="3" 
                                                  placeholder="List all prescription medications, over-the-counter drugs, vitamins, and supplements you are currently taking. For example: 'Aspirin 100mg daily, Metformin 500mg twice daily, Vitamin D 1000IU daily'">{{ old('current_medications', $medicalInfo->current_medications ?? '') }}</textarea>
                                        @error('current_medications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Include dosage and frequency for each medication</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="allergies" class="form-label fw-semibold">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            Allergies & Reactions
                                        </label>
                                        <textarea class="form-control @error('allergies') is-invalid @enderror" 
                                                  id="allergies" name="allergies" rows="2" 
                                                  placeholder="List any allergies to medications, foods, environmental factors, or other substances. For example: 'Penicillin (causes rash), Peanuts (severe reaction), Latex (skin irritation)'">{{ old('allergies', $medicalInfo->allergies ?? '') }}</textarea>
                                        @error('allergies')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-danger">
                                            <i class="fas fa-shield-alt me-1"></i>This information is critical for your safety during medical treatment
                                        </div>
                                    </div>
                                </div>

                                <!-- Blood Reports Tab -->
                                <div class="tab-pane fade" id="reports" role="tabpanel">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Enter your latest lab test results. This helps track your health over time. Leave blank if you don't have recent results.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <label for="hemoglobin" class="form-label fw-semibold">Hemoglobin Level</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.1" class="form-control @error('hemoglobin') is-invalid @enderror" 
                                                               id="hemoglobin" name="hemoglobin" value="{{ old('hemoglobin', $medicalInfo->hemoglobin ?? '') }}" 
                                                               placeholder="Enter value">
                                                        <span class="input-group-text">g/dL</span>
                                                    </div>
                                                    @error('hemoglobin')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Normal range: 12-16 g/dL (Women), 14-18 g/dL (Men)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <label for="rbc_count" class="form-label fw-semibold">Red Blood Cells</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control @error('rbc_count') is-invalid @enderror" 
                                                               id="rbc_count" name="rbc_count" value="{{ old('rbc_count', $medicalInfo->rbc_count ?? '') }}" 
                                                               placeholder="Enter value">
                                                        <span class="input-group-text">M/μL</span>
                                                    </div>
                                                    @error('rbc_count')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Normal range: 4.2-5.4 M/μL (Women), 4.7-6.1 M/μL (Men)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <label for="wbc_count" class="form-label fw-semibold">White Blood Cells</label>
                                                    <div class="input-group">
                                                        <input type="number" step="1" class="form-control @error('wbc_count') is-invalid @enderror" 
                                                               id="wbc_count" name="wbc_count" value="{{ old('wbc_count', $medicalInfo->wbc_count ?? '') }}" 
                                                               placeholder="Enter value">
                                                        <span class="input-group-text">/μL</span>
                                                    </div>
                                                    @error('wbc_count')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Normal range: 4,000-11,000 /μL
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <label for="platelet_count" class="form-label fw-semibold">Platelet Count</label>
                                                    <div class="input-group">
                                                        <input type="number" step="1" class="form-control @error('platelet_count') is-invalid @enderror" 
                                                               id="platelet_count" name="platelet_count" value="{{ old('platelet_count', $medicalInfo->platelet_count ?? '') }}" 
                                                               placeholder="Enter value">
                                                        <span class="input-group-text">/μL</span>
                                                    </div>
                                                    @error('platelet_count')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Normal range: 150,000-400,000 /μL
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <label for="blood_sugar" class="form-label fw-semibold">Blood Sugar (Fasting)</label>
                                                    <div class="input-group">
                                                        <input type="number" step="1" class="form-control @error('blood_sugar') is-invalid @enderror" 
                                                               id="blood_sugar" name="blood_sugar" value="{{ old('blood_sugar', $medicalInfo->blood_sugar ?? '') }}" 
                                                               placeholder="Enter value">
                                                        <span class="input-group-text">mg/dL</span>
                                                    </div>
                                                    @error('blood_sugar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Normal range: 70-100 mg/dL (fasting)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <label for="cholesterol" class="form-label fw-semibold">Cholesterol Level</label>
                                                    <div class="input-group">
                                                        <input type="number" step="1" class="form-control @error('cholesterol') is-invalid @enderror" 
                                                               id="cholesterol" name="cholesterol" value="{{ old('cholesterol', $medicalInfo->cholesterol ?? '') }}" 
                                                               placeholder="Enter value">
                                                        <span class="input-group-text">mg/dL</span>
                                                    </div>
                                                    @error('cholesterol')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text text-success">
                                                        <i class="fas fa-check-circle me-1"></i>Healthy level: Below 200 mg/dL
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Tab -->
                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Upload your medical documents for easy access. Supported format: PDF files only. Maximum file size: 5MB per file.
                                    </div>

                                    <!-- File Upload Sections -->
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary">
                                                        <i class="fas fa-vial me-2"></i>Blood Test Reports
                                                    </h6>
                                                    <input type="file" class="form-control @error('blood_test_reports') is-invalid @enderror" 
                                                           name="blood_test_reports[]" multiple accept=".pdf">
                                                    @error('blood_test_reports')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Upload lab results and blood work reports</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">
                                                        <i class="fas fa-prescription me-2"></i>Prescriptions
                                                    </h6>
                                                    <input type="file" class="form-control @error('prescriptions') is-invalid @enderror" 
                                                           name="prescriptions[]" multiple accept=".pdf">
                                                    @error('prescriptions')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Upload doctor's prescriptions</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info">
                                                        <i class="fas fa-x-ray me-2"></i>Scan Reports
                                                    </h6>
                                                    <input type="file" class="form-control @error('medical_reports') is-invalid @enderror" 
                                                           name="medical_reports[]" multiple accept=".pdf">
                                                    @error('medical_reports')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Upload X-Ray, MRI, CT scan reports</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">
                                                        <i class="fas fa-shield-alt me-2"></i>Insurance Documents
                                                    </h6>
                                                    <input type="file" class="form-control @error('insurance_documents') is-invalid @enderror" 
                                                           name="insurance_documents[]" multiple accept=".pdf">
                                                    @error('insurance_documents')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Upload insurance cards and documents</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Existing Documents -->
                                    @if($bloodTestReports->isNotEmpty() || $prescriptions->isNotEmpty() || $medicalReports->isNotEmpty() || $insuranceDocuments->isNotEmpty())
                                    <div class="mt-4">
                                        <h5 class="mb-3 text-primary">
                                            <i class="fas fa-folder-open me-2"></i>My Uploaded Documents
                                        </h5>
                                        
                                        @if($bloodTestReports->isNotEmpty())
                                        <div class="mb-4">
                                            <h6 class="text-primary mb-3">
                                                <i class="fas fa-vial me-2"></i>Blood Test Reports
                                            </h6>
                                            <div class="list-group">
                                                @foreach($bloodTestReports as $report)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                        <span>{{ $report->file_path }}</span>
                                                        <small class="text-muted ms-2">
                                                            Uploaded: {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                                onclick="viewDocument('{{ route('account.medicalInfo.viewDocument', ['blood-test-report', $report->id]) }}', '{{ $report->file_path }}', 'blood-test-report', '{{ $report->id }}')">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                        <a href="{{ route('account.medicalInfo.downloadDocument', ['blood-test-report', $report->id]) }}" 
                                                           class="btn btn-sm btn-outline-success me-1">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                                                data-id="{{ $report->id }}" 
                                                                data-type="blood-test-report"
                                                                data-filename="{{ $report->file_path }}">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        @if($prescriptions->isNotEmpty())
                                        <div class="mb-4">
                                            <h6 class="text-success mb-3">
                                                <i class="fas fa-prescription me-2"></i>Prescriptions
                                            </h6>
                                            <div class="list-group">
                                                @foreach($prescriptions as $prescription)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                        <span>{{ $prescription->file_path }}</span>
                                                        <small class="text-muted ms-2">
                                                            Uploaded: {{ \Carbon\Carbon::parse($prescription->created_at)->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                                onclick="viewDocument('{{ route('account.medicalInfo.viewDocument', ['prescription', $prescription->id]) }}', '{{ $prescription->file_path }}', 'prescription', '{{ $prescription->id }}')">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                        <a href="{{ route('account.medicalInfo.downloadDocument', ['prescription', $prescription->id]) }}" 
                                                           class="btn btn-sm btn-outline-success me-1">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                                                data-id="{{ $prescription->id }}" 
                                                                data-type="prescription"
                                                                data-filename="{{ $prescription->file_path }}">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        @if($medicalReports->isNotEmpty())
                                        <div class="mb-4">
                                            <h6 class="text-info mb-3">
                                                <i class="fas fa-x-ray me-2"></i>Scan Reports
                                            </h6>
                                            <div class="list-group">
                                                @foreach($medicalReports as $report)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                        <span>{{ $report->file_path }}</span>
                                                        <small class="text-muted ms-2">
                                                            Uploaded: {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                                onclick="viewDocument('{{ route('account.medicalInfo.viewDocument', ['medical-report', $report->id]) }}', '{{ $report->file_path }}', 'medical-report', '{{ $report->id }}')">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                        <a href="{{ route('account.medicalInfo.downloadDocument', ['medical-report', $report->id]) }}" 
                                                           class="btn btn-sm btn-outline-success me-1">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                                                data-id="{{ $report->id }}" 
                                                                data-type="medical-report"
                                                                data-filename="{{ $report->file_path }}">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        @if($insuranceDocuments->isNotEmpty())
                                        <div class="mb-4">
                                            <h6 class="text-warning mb-3">
                                                <i class="fas fa-shield-alt me-2"></i>Insurance Documents
                                            </h6>
                                            <div class="list-group">
                                                @foreach($insuranceDocuments as $document)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                        <span>{{ $document->file_path }}</span>
                                                        <small class="text-muted ms-2">
                                                            Uploaded: {{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                                onclick="viewDocument('{{ route('account.medicalInfo.viewDocument', ['insurance-document', $document->id]) }}', '{{ $document->file_path }}', 'insurance-document', '{{ $document->id }}')">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                        <a href="{{ route('account.medicalInfo.downloadDocument', ['insurance-document', $document->id]) }}" 
                                                           class="btn btn-sm btn-outline-success me-1">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                                                data-id="{{ $document->id }}" 
                                                                data-type="insurance-document"
                                                                data-filename="{{ $document->file_path }}">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No documents uploaded yet</h5>
                                        <p class="text-muted">Upload your medical documents to keep them organized and accessible</p>
                                    </div>
                                    @endif
                                </div>

                                <!-- Emergency Contact Tab -->
                                <div class="tab-pane fade" id="emergency" role="tabpanel">
                                    <div class="alert alert-warning mb-4">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Important:</strong> This contact will be notified in case of medical emergencies. Please ensure the information is current and accurate.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="emergency_contact_name" class="form-label fw-semibold">
                                                <i class="fas fa-user me-2 text-primary"></i>Contact Full Name
                                            </label>
                                            <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                                   id="emergency_contact_name" name="emergency_contact_name" 
                                                   value="{{ old('emergency_contact_name', $medicalInfo->emergency_contact_name ?? '') }}" 
                                                   placeholder="Enter full name">
                                            @error('emergency_contact_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="emergency_contact_relation" class="form-label fw-semibold">
                                                <i class="fas fa-users me-2 text-success"></i>Relationship
                                            </label>
                                            <select class="form-select @error('emergency_contact_relation') is-invalid @enderror" 
                                                    id="emergency_contact_relation" name="emergency_contact_relation">
                                                <option value="">Select Relationship</option>
                                                <option value="Spouse" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Spouse' ? 'selected' : '' }}>Spouse/Partner</option>
                                                <option value="Parent" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                                <option value="Sibling" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                                <option value="Child" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Child' ? 'selected' : '' }}>Child</option>
                                                <option value="Friend" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Friend' ? 'selected' : '' }}>Friend</option>
                                                <option value="Other" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('emergency_contact_relation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="emergency_contact_phone" class="form-label fw-semibold">
                                                <i class="fas fa-phone me-2 text-warning"></i>Phone Number
                                            </label>
                                            <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                                   id="emergency_contact_phone" name="emergency_contact_phone" 
                                                   value="{{ old('emergency_contact_phone', $medicalInfo->emergency_contact_phone ?? '') }}" 
                                                   placeholder="Enter phone number with country code">
                                            @error('emergency_contact_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Include country code (e.g., +1 for US/Canada)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg px-5" id="submitMedicalForm">
                                    <i class="fas fa-save me-2"></i>Save Medical Information
                                </button>
                                <a href="{{ route('account.profile') }}" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="documentViewerModalLabel">
                    <i class="fas fa-file-pdf me-2"></i>Document Viewer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light border-bottom p-3 d-flex justify-content-between align-items-center">
                    <span id="documentFileName" class="fw-semibold">document.pdf</span>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" id="documentDownload">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                        <button class="btn btn-sm btn-outline-danger me-1" id="documentDelete">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Close
                        </button>
                    </div>
                </div>
                <div class="text-center py-5" id="documentLoading">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading document...</span>
                    </div>
                    <p class="text-muted">Loading your document...</p>
                </div>
                <iframe id="documentIframe" src="" style="display: none; width: 100%; height: 70vh; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// View document function with proper parameters
function viewDocument(url, filename, documentType, documentId) {
    const modal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
    const iframe = document.getElementById('documentIframe');
    const loading = document.getElementById('documentLoading');
    const fileNameElement = document.getElementById('documentFileName');
    const downloadBtn = document.getElementById('documentDownload');
    const deleteBtn = document.getElementById('documentDelete');
    
    // Set filename
    fileNameElement.textContent = filename;
    
    // Set download button
    downloadBtn.onclick = function() {
        window.open(url.replace('/view/', '/download/'), '_blank');
    };
    
    // Set delete button with proper data
    deleteBtn.setAttribute('data-id', documentId);
    deleteBtn.setAttribute('data-type', documentType);
    deleteBtn.setAttribute('data-filename', filename);
    
    // Show loading
    loading.style.display = 'block';
    iframe.style.display = 'none';
    
    // Set iframe source
    iframe.src = url;
    
    // When iframe loads, hide loading
    iframe.onload = function() {
        loading.style.display = 'none';
        iframe.style.display = 'block';
    };
    
    modal.show();
}

// Delete document functionality - FIXED
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete buttons outside modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-document')) {
            e.preventDefault();
            const button = e.target;
            const documentId = button.getAttribute('data-id');
            const documentType = button.getAttribute('data-type');
            const filename = button.getAttribute('data-filename');
            
            deleteDocument(documentId, documentType, filename, button);
        }
    });
    
    // Handle delete button inside modal
    document.getElementById('documentDelete').addEventListener('click', function() {
        const documentId = this.getAttribute('data-id');
        const documentType = this.getAttribute('data-type');
        const filename = this.getAttribute('data-filename');
        
        deleteDocument(documentId, documentType, filename, this);
    });
});

function deleteDocument(documentId, documentType, filename, button) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${filename}". This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            const originalHTML = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
            button.disabled = true;
            
            // Send delete request
            fetch('{{ route("account.medicalInfo.deleteDocument") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id: documentId,
                    document_type: documentType
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Close modal if open
                    const modal = bootstrap.Modal.getInstance(document.getElementById('documentViewerModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Remove the document element from the list
                    const documentElement = document.querySelector(`[data-id="${documentId}"][data-type="${documentType}"]`).closest('.list-group-item');
                    if (documentElement) {
                        documentElement.remove();
                        
                        // Check if category is now empty
                        const category = documentElement.closest('.mb-4');
                        const remainingItems = category.querySelectorAll('.list-group-item');
                        if (remainingItems.length === 0) {
                            category.remove();
                        }
                    }
                    
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Your document has been deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete document');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete document. Please try again.',
                    icon: 'error'
                });
                
                // Reset button
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }
    });
}

// Form submission handling
document.getElementById('patientMedicalForm').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    submitButton.disabled = true;
});
</script>
@endsection