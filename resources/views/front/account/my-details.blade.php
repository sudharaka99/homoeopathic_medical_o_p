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

                <!-- Medical Profile Card -->
                <div class="card border-0 shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fs-4 mb-0">
                                <i class="fas fa-heartbeat text-primary me-2"></i>My Medical Profile
                            </h3>
                            <span class="badge bg-secondary">
                                <i class="fas fa-calendar me-1"></i>
                                Last updated: 
                                @if($medicalInfo && $medicalInfo->updated_at)
                                    {{ \Carbon\Carbon::parse($medicalInfo->updated_at)->format('M j, Y') }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>

                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                             <div class="card bg-secondary text-light">
                                    <div class="card-body text-center py-3">
                                        <i class="fas fa-file-medical fa-2x mb-2"></i>
                                        <h5 class="mb-1">{{ $bloodTestReports->count() + $prescriptions->count() + $medicalReports->count() + $insuranceDocuments->count() }}</h5>
                                        <small class="opacity-75">Total Documents</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center py-3">
                                        <i class="fas fa-pills fa-2x mb-2"></i>
                                        <h5 class="mb-1">
                                            @if($medicalInfo && $medicalInfo->current_medications)
                                                {{ substr_count($medicalInfo->current_medications, ',') + 1 }}
                                            @else
                                                0
                                            @endif
                                        </h5>
                                        <small class="opacity-75">Medications</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center py-3">
                                        <i class="fas fa-allergies fa-2x mb-2"></i>
                                        <h5 class="mb-1">
                                            @if($medicalInfo && $medicalInfo->allergies)
                                                {{ substr_count($medicalInfo->allergies, ',') + 1 }}
                                            @else
                                                0
                                            @endif
                                        </h5>
                                        <small class="opacity-75">Allergies</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center py-3">
                                        <i class="fas fa-phone-alt fa-2x mb-2"></i>
                                        <h5 class="mb-1">{{ $medicalInfo && $medicalInfo->emergency_contact_name ? 1 : 0 }}</h5>
                                        <small class="opacity-75">Emergency Contact</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information Tabs -->
                        <ul class="nav nav-tabs mb-4" id="medicalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                                    <i class="fas fa-chart-pie me-2"></i>Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                                    <i class="fas fa-file-medical me-2"></i>Documents
                                    <span class="badge bg-primary ms-1">{{ $bloodTestReports->count() + $prescriptions->count() + $medicalReports->count() + $insuranceDocuments->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="medicalTabContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="row">
                                    <!-- Medical History -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-history text-primary me-2"></i>Medical History
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if($medicalInfo && $medicalInfo->medical_history)
                                                    <p class="text-muted">{{ \Illuminate\Support\Str::limit($medicalInfo->medical_history, 150) }}</p>
                                                @else
                                                    <p class="text-muted">No medical history recorded</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Medications -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-pills text-success me-2"></i>Current Medications
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if($medicalInfo && $medicalInfo->current_medications)
                                                    <p class="text-muted">{{ \Illuminate\Support\Str::limit($medicalInfo->current_medications, 150) }}</p>
                                                @else
                                                    <p class="text-muted">No medications recorded</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Allergies -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-allergies text-danger me-2"></i>Allergies
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if($medicalInfo && $medicalInfo->allergies)
                                                    <p class="text-muted">{{ $medicalInfo->allergies }}</p>
                                                @else
                                                    <p class="text-muted">No allergies recorded</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Emergency Contact -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-phone-alt text-warning me-2"></i>Emergency Contact
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if($medicalInfo && $medicalInfo->emergency_contact_name)
                                                    <p class="mb-1"><strong>{{ $medicalInfo->emergency_contact_name }}</strong></p>
                                                    <p class="mb-1 text-muted">{{ $medicalInfo->emergency_contact_relation }}</p>
                                                    <p class="mb-0 text-muted">{{ $medicalInfo->emergency_contact_phone }}</p>
                                                @else
                                                    <p class="text-muted">No emergency contact set</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Tab -->
                            <div class="tab-pane fade" id="documents" role="tabpanel">
                                <!-- Enhanced Filter Section -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-filter me-2"></i>Filter Documents
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small" id="activeFilterText">Showing all documents</span>
                                            <button class="btn btn-sm btn-outline-dark" id="clearFilters">
                                                <i class="fas fa-times me-1"></i>Clear
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Search Box -->
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0">
                                                        <i class="fas fa-search text-muted"></i>
                                                    </span>
                                                    <input type="text" class="form-control border-start-0 ps-0" id="documentSearch" placeholder="Search by document name...">
                                                </div>
                                            </div>
                                            
                                            <!-- Document Type Filter -->
                                            <div class="col-md-6">
                                                <select class="form-select" id="documentTypeFilter">
                                                    <option value="all">All Document Types</option>
                                                    <option value="blood-test-report">Blood Test Reports</option>
                                                    <option value="prescription">Prescriptions</option>
                                                    <option value="medical-report">Scan Reports</option>
                                                    <option value="insurance-document">Insurance Documents</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Filter Chips -->
                                        <div class="mt-3">
                                            <div class="d-flex flex-wrap gap-2" id="quickFilters">
                                                <button class="btn btn-sm btn-primary" data-filter="all">
                                                    <i class="fas fa-list me-1"></i>All
                                                    <span class="badge bg-light text-dark ms-1">{{ $bloodTestReports->count() + $prescriptions->count() + $medicalReports->count() + $insuranceDocuments->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-dark" data-filter="blood-test-report">
                                                    <i class="fas fa-vial me-1"></i>Blood Tests
                                                    <span class="badge bg-secondary ms-1">{{ $bloodTestReports->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-dark" data-filter="prescription">
                                                    <i class="fas fa-prescription me-1"></i>Prescriptions
                                                    <span class="badge bg-success ms-1">{{ $prescriptions->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-dark" data-filter="medical-report">
                                                    <i class="fas fa-x-ray me-1"></i>Scan Reports
                                                    <span class="badge bg-info ms-1">{{ $medicalReports->count() }}</span>
                                                </button>
                                                <button class="btn btn-sm btn-outline-dark" data-filter="insurance-document">
                                                    <i class="fas fa-shield-alt me-1"></i>Insurance
                                                    <span class="badge bg-warning ms-1">{{ $insuranceDocuments->count() }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Table -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-files me-2"></i>Medical Documents
                                            <small class="text-muted ms-2" id="documentCount">
                                                ({{ $bloodTestReports->count() + $prescriptions->count() + $medicalReports->count() + $insuranceDocuments->count() }} files)
                                            </small>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small" id="filterResults"></span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="border-0 ps-4">Document Name</th>
                                                        <th class="border-0">Type</th>
                                                        <th class="border-0">Upload Date</th>
                                                        <th class="border-0 pe-4 text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="documentsTableBody">
                                                    @php
                                                        $allDocuments = collect([]);
                                                        
                                                        // Add blood test reports
                                                        if($bloodTestReports) {
                                                            foreach($bloodTestReports as $report) {
                                                                $allDocuments->push([
                                                                    'type' => 'blood-test-report',
                                                                    'data' => $report,
                                                                    'badge' => 'secondary',
                                                                    'label' => 'Blood Test',
                                                                    'icon' => 'fas fa-vial'
                                                                ]);
                                                            }
                                                        }
                                                        
                                                        // Add prescriptions
                                                        if($prescriptions) {
                                                            foreach($prescriptions as $prescription) {
                                                                $allDocuments->push([
                                                                    'type' => 'prescription',
                                                                    'data' => $prescription,
                                                                    'badge' => 'success',
                                                                    'label' => 'Prescription',
                                                                    'icon' => 'fas fa-prescription'
                                                                ]);
                                                            }
                                                        }
                                                        
                                                        // Add medical reports
                                                        if($medicalReports) {
                                                            foreach($medicalReports as $report) {
                                                                $allDocuments->push([
                                                                    'type' => 'medical-report',
                                                                    'data' => $report,
                                                                    'badge' => 'info',
                                                                    'label' => 'Scan Report',
                                                                    'icon' => 'fas fa-x-ray'
                                                                ]);
                                                            }
                                                        }
                                                        
                                                        // Add insurance documents
                                                        if($insuranceDocuments) {
                                                            foreach($insuranceDocuments as $document) {
                                                                $allDocuments->push([
                                                                    'type' => 'insurance-document',
                                                                    'data' => $document,
                                                                    'badge' => 'warning',
                                                                    'label' => 'Insurance',
                                                                    'icon' => 'fas fa-shield-alt'
                                                                ]);
                                                            }
                                                        }
                                                    @endphp

                                                    @if($allDocuments->count() > 0)
                                                        @foreach($allDocuments as $doc)
                                                        @php $document = $doc['data']; @endphp
                                                        <tr class="document-row" data-type="{{ $doc['type'] }}" data-name="{{ strtolower($document->file_path) }}">
                                                            <td class="ps-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="fas fa-file-pdf text-danger fa-lg me-3"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="fw-medium text-dark document-name">{{ $document->file_path }}</div>
                                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y • g:i A') }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $doc['badge'] }} text-white border-0">
                                                                    <i class="{{ $doc['icon'] }} me-1"></i>{{ $doc['label'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y') }}</span>
                                                            </td>
                                                            <td class="pe-4 text-end">
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument('{{ route('account.medicalInfo.viewDocument', [$doc['type'], $document->id]) }}', '{{ $document->file_path }}')">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <a href="{{ route('account.medicalInfo.downloadDocument', [$doc['type'], $document->id]) }}" class="btn btn-sm btn-outline-success">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument({{ $document->id }}, '{{ $doc['type'] }}', '{{ $document->file_path }}')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @else
                                                        <tr id="noDocumentsRow">
                                                            <td colspan="4" class="text-center py-5">
                                                                <div class="py-4">
                                                                    <i class="fas fa-folder-open fa-4x text-light mb-3"></i>
                                                                    <h5 class="text-muted mb-2">No documents uploaded yet</h5>
                                                                    <p class="text-muted mb-3">Start by uploading your medical documents</p>
                                                                    <button class="btn btn-primary" data-bs-toggle="tab" data-bs-target="#edit">
                                                                        <i class="fas fa-upload me-2"></i>Upload Documents
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Profile Tab -->
                            <div class="tab-pane fade" id="edit" role="tabpanel">
                                <form action="{{ route('account.medicalInfo.store') }}" method="POST" id="patientMedicalForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-history text-primary me-2"></i>Medical History
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <textarea class="form-control" name="medical_history" rows="4" placeholder="Describe your medical history, conditions, surgeries, etc.">{{ old('medical_history', $medicalInfo->medical_history ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-pills text-success me-2"></i>Current Medications
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <textarea class="form-control" name="current_medications" rows="3" placeholder="List your current medications and dosages">{{ old('current_medications', $medicalInfo->current_medications ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-allergies text-danger me-2"></i>Allergies
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <textarea class="form-control" name="allergies" rows="3" placeholder="List any allergies or reactions">{{ old('allergies', $medicalInfo->allergies ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- File Upload Section -->
                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-upload me-2"></i>Upload Medical Documents
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold">Blood Test Reports</label>
                                                            <input type="file" class="form-control" name="blood_test_reports[]" multiple accept=".pdf">
                                                            <div class="form-text">Upload lab results and blood work reports (PDF only, max 5MB each)</div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold">Prescriptions</label>
                                                            <input type="file" class="form-control" name="prescriptions[]" multiple accept=".pdf">
                                                            <div class="form-text">Upload doctor's prescriptions (PDF only, max 5MB each)</div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold">Scan Reports</label>
                                                            <input type="file" class="form-control" name="medical_reports[]" multiple accept=".pdf">
                                                            <div class="form-text">Upload X-Ray, MRI, CT scan reports (PDF only, max 5MB each)</div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold">Insurance Documents</label>
                                                            <input type="file" class="form-control" name="insurance_documents[]" multiple accept=".pdf">
                                                            <div class="form-text">Upload insurance cards and documents (PDF only, max 5MB each)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-phone-alt text-warning me-2"></i>Emergency Contact
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Full Name</label>
                                                            <input type="text" class="form-control" name="emergency_contact_name" placeholder="Full Name" value="{{ old('emergency_contact_name', $medicalInfo->emergency_contact_name ?? '') }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Relationship</label>
                                                            <select class="form-select" name="emergency_contact_relation">
                                                                <option value="">Select Relationship</option>
                                                                <option value="Spouse" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Spouse' ? 'selected' : '' }}>Spouse/Partner</option>
                                                                <option value="Parent" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                                                <option value="Sibling" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                                                <option value="Child" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Child' ? 'selected' : '' }}>Child</option>
                                                                <option value="Friend" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Friend' ? 'selected' : '' }}>Friend</option>
                                                                <option value="Other" {{ old('emergency_contact_relation', $medicalInfo->emergency_contact_relation ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Phone Number</label>
                                                            <input type="tel" class="form-control" name="emergency_contact_phone" placeholder="Phone Number" value="{{ old('emergency_contact_phone', $medicalInfo->emergency_contact_phone ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i>Save All Changes
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="tab" data-bs-target="#overview">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf me-2"></i><span id="documentFileName">document.pdf</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="text-center py-5" id="documentLoading">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted">Loading document...</p>
                </div>
                <iframe id="documentIframe" src="" style="width: 100%; height: 70vh; border: none; display: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fixed viewDocument function
function viewDocument(url, filename) {
    const modal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
    const iframe = document.getElementById('documentIframe');
    const loading = document.getElementById('documentLoading');
    const fileNameElement = document.getElementById('documentFileName');
    
    fileNameElement.textContent = filename;
    loading.style.display = 'block';
    iframe.style.display = 'none';
    iframe.src = url;
    
    iframe.onload = function() {
        loading.style.display = 'none';
        iframe.style.display = 'block';
    };
    
    modal.show();
}

// Fixed deleteDocument function
function deleteDocument(id, type, filename) {
    Swal.fire({
        title: 'Delete Document?',
        html: `Are you sure you want to delete<br><strong>"${filename}"</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, delete it!',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '{{ route("account.medicalInfo.deleteDocument") }}',
                type: 'DELETE',
                data: {
                    id: id,
                    document_type: type,
                    _token: '{{ csrf_token() }}'
                }
            }).then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Failed to delete document');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message || error.statusText}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleted!',
                text: 'Document has been deleted successfully.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Enhanced Document filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const documentRows = document.querySelectorAll('.document-row');
    const searchInput = document.getElementById('documentSearch');
    const typeFilter = document.getElementById('documentTypeFilter');
    const quickFilters = document.getElementById('quickFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const documentCount = document.getElementById('documentCount');
    const filterResults = document.getElementById('filterResults');
    const activeFilterText = document.getElementById('activeFilterText');
    const noDocumentsRow = document.getElementById('noDocumentsRow');

    let currentTypeFilter = 'all';
    let currentSearchTerm = '';

    function updateFilterText() {
        const visibleCount = document.querySelectorAll('.document-row:not([style*="display: none"])').length;
        const totalCount = documentRows.length;
        
        let filterText = '';
        if (currentTypeFilter !== 'all' || currentSearchTerm !== '') {
            filterText = `Showing ${visibleCount} of ${totalCount} documents`;
            
            let typeText = '';
            const typeLabels = {
                'all': 'All Documents',
                'blood-test-report': 'Blood Test Reports',
                'prescription': 'Prescriptions',
                'medical-report': 'Scan Reports',
                'insurance-document': 'Insurance Documents'
            };
            
            if (currentTypeFilter !== 'all') {
                typeText = typeLabels[currentTypeFilter];
            }
            
            if (currentSearchTerm !== '') {
                activeFilterText.textContent = `Search: "${currentSearchTerm}"${typeText ? ` • ${typeText}` : ''}`;
            } else {
                activeFilterText.textContent = typeText || 'Showing all documents';
            }
        } else {
            filterText = `All ${totalCount} documents`;
            activeFilterText.textContent = 'Showing all documents';
        }
        
        filterResults.textContent = filterText;
    }

    function filterDocuments() {
        let visibleCount = 0;

        documentRows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const fileName = row.getAttribute('data-name');
            const typeMatch = currentTypeFilter === 'all' || rowType === currentTypeFilter;
            const searchMatch = fileName.includes(currentSearchTerm);
            
            if (typeMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update quick filter buttons
        document.querySelectorAll('#quickFilters .btn').forEach(btn => {
            const filterType = btn.getAttribute('data-filter');
            if (filterType === currentTypeFilter) {
                // Active button - make it solid
                btn.classList.remove('btn-outline-dark');
                if (filterType === 'all') {
                    btn.classList.add('btn-primary');
                } else if (filterType === 'blood-test-report') {
                    btn.classList.add('btn-secondary');
                } else if (filterType === 'prescription') {
                    btn.classList.add('btn-success');
                } else if (filterType === 'medical-report') {
                    btn.classList.add('btn-info');
                } else if (filterType === 'insurance-document') {
                    btn.classList.add('btn-warning');
                }
            } else {
                // Inactive button - make it outline
                btn.classList.remove('btn-primary', 'btn-secondary', 'btn-success', 'btn-info', 'btn-warning');
                btn.classList.add('btn-outline-dark');
            }
        });

        // Update counts and text
        documentCount.textContent = `(${visibleCount} files)`;
        updateFilterText();

        // Show/hide no documents message
        if (noDocumentsRow) {
            if (visibleCount === 0 && documentRows.length > 0) {
                noDocumentsRow.style.display = '';
            } else {
                noDocumentsRow.style.display = 'none';
            }
        }
    }

    // Event listeners
    searchInput.addEventListener('input', function() {
        currentSearchTerm = this.value.toLowerCase();
        filterDocuments();
    });

    typeFilter.addEventListener('change', function() {
        currentTypeFilter = this.value;
        filterDocuments();
    });

    quickFilters.addEventListener('click', function(e) {
        if (e.target.closest('.btn')) {
            const button = e.target.closest('.btn');
            currentTypeFilter = button.getAttribute('data-filter');
            typeFilter.value = currentTypeFilter;
            filterDocuments();
        }
    });

    clearFiltersBtn.addEventListener('click', function() {
        currentTypeFilter = 'all';
        currentSearchTerm = '';
        typeFilter.value = 'all';
        searchInput.value = '';
        filterDocuments();
    });

    // Initial filter
    filterDocuments();

    // Form submission handling
    const form = document.getElementById('patientMedicalForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const originalHTML = submitButton.innerHTML;
            
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            submitButton.disabled = true;
        });
    }
    
    // Check for success message in session
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#28a745',
            timer: 3000,
            showConfirmButton: true
        }).then(() => {
            const overviewTab = document.getElementById('overview-tab');
            if (overviewTab) {
                bootstrap.Tab.getOrCreateInstance(overviewTab).show();
            }
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    @endif
});
</script> 
@endsection