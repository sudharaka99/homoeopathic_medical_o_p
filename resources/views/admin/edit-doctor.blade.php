@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.doctorslist') }}">Manage Doctors</a></li>
                        <li class="breadcrumb-item active">Edit Doctor</li>
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

                <div class="card border-0 shadow">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2 text-primary"></i>
                            Edit Doctor: Dr. {{ $doctor->doctor_name }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.doctor.update', $doctor->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Personal Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 text-primary">
                                        <i class="fas fa-user-md me-2"></i>Personal Information
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="doctor_name" class="form-label">Doctor Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('doctor_name') is-invalid @enderror" 
                                           id="doctor_name" name="doctor_name" 
                                           value="{{ old('doctor_name', $doctor->doctor_name) }}" required>
                                    @error('doctor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', $doctor->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                           id="mobile" name="mobile" 
                                           value="{{ old('mobile', $doctor->mobile) }}" required>
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                           id="license_number" name="license_number" 
                                           value="{{ old('license_number', $doctor->license_number) }}" required>
                                    @error('license_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Professional Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 text-primary">
                                        <i class="fas fa-briefcase me-2"></i>Professional Information
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="qualification" class="form-label">Qualification <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('qualification') is-invalid @enderror" 
                                           id="qualification" name="qualification" 
                                           value="{{ old('qualification', $doctor->qualification) }}" required>
                                    @error('qualification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                                    <select class="form-select @error('specialization') is-invalid @enderror" 
                                            id="specialization" name="specialization" required>
                                        <option value="">Select Specialization</option>
                                        @foreach($specializations as $spec)
                                            <option value="{{ $spec->id }}" 
                                                {{ old('specialization', $doctor->specialization) == $spec->id ? 'selected' : '' }}>
                                                {{ $spec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="appointment_fee" class="form-label">Appointment Fee ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('appointment_fee') is-invalid @enderror" 
                                           id="appointment_fee" name="appointment_fee" 
                                           value="{{ old('appointment_fee', $doctor->appointment_fee) }}" required>
                                    @error('appointment_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="years_experience" class="form-label">Years of Experience <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                                           id="years_experience" name="years_experience" 
                                           value="{{ old('years_experience', $doctor->years_experience) }}" required>
                                    @error('years_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Clinic Information Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 text-primary">
                                        <i class="fas fa-hospital me-2"></i>Clinic Information
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="clinic_name" class="form-label">Clinic Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('clinic_name') is-invalid @enderror" 
                                           id="clinic_name" name="clinic_name" 
                                           value="{{ old('clinic_name', $doctor->clinic_name) }}" required>
                                    @error('clinic_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                           id="district" name="district" 
                                           value="{{ old('district', $doctor->district) }}" required>
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- License Image Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 text-primary">
                                        <i class="fas fa-file-certificate me-2"></i>License Information
                                    </h5>
                                    
                                    @if($doctor->license_image)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Current License Image:</label>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $doctor->license_image) }}" 
                                                 class="img-thumbnail mb-2" 
                                                 style="max-height: 200px; cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#licenseModal"
                                                 onclick="showLicenseImage('{{ asset('storage/' . $doctor->license_image) }}', '{{ $doctor->doctor_name }}')"
                                                 alt="Current License">
                                            <div class="small text-muted">Click to view full image</div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="license_image" class="form-label">
                                            Update License Image 
                                            <span class="text-muted small">(Leave empty to keep current image)</span>
                                        </label>
                                        <input type="file" class="form-control @error('license_image') is-invalid @enderror" 
                                               id="license_image" name="license_image" 
                                               accept="image/*">
                                        @error('license_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Accepted formats: JPG, JPEG, PNG. Max size: 2MB
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Image Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 text-primary">
                                        <i class="fas fa-image me-2"></i>Profile Image
                                    </h5>
                                    
                                    @if($doctor->profile_image)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Current Profile Image:</label>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $doctor->profile_image) }}" 
                                                 class="rounded-circle mb-2 object-fit-cover"
                                                 style="width: 120px; height: 120px; cursor: pointer;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#profileImageModal"
                                                 onclick="showProfileImage('{{ asset('storage/' . $doctor->profile_image) }}', '{{ $doctor->doctor_name }}')"
                                                 alt="Current Profile Image">
                                            <div class="small text-muted">Click to view full image</div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">
                                            Update Profile Image 
                                            <span class="text-muted small">(Leave empty to keep current image)</span>
                                        </label>
                                        <input type="file" class="form-control @error('profile_image') is-invalid @enderror" 
                                               id="profile_image" name="profile_image" 
                                               accept="image/*">
                                        @error('profile_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Recommended: Square image, minimum 200x200 pixels. Max size: 2MB
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Status Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 text-primary">
                                        <i class="fas fa-cog me-2"></i>Account Status
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <label for="is_admin_confirmed" class="form-label">Account Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('is_admin_confirmed') is-invalid @enderror" 
                                                id="is_admin_confirmed" name="is_admin_confirmed" required>
                                            <option value="0" {{ old('is_admin_confirmed', $doctor->is_admin_confirmed) == 0 ? 'selected' : '' }}>
                                                Pending Approval
                                            </option>
                                            <option value="1" {{ old('is_admin_confirmed', $doctor->is_admin_confirmed) == 1 ? 'selected' : '' }}>
                                                Approved
                                            </option>
                                            <option value="2" {{ old('is_admin_confirmed', $doctor->is_admin_confirmed) == 2 ? 'selected' : '' }}>
                                                Disabled
                                            </option>
                                        </select>
                                        @error('is_admin_confirmed')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" id="rejectReasonContainer" 
                                         style="{{ old('is_admin_confirmed', $doctor->is_admin_confirmed) == 2 ? '' : 'display: none;' }}">
                                        <label for="reject_reason" class="form-label">Reason for Disabling Account</label>
                                        <textarea class="form-control @error('reject_reason') is-invalid @enderror" 
                                                  id="reject_reason" name="reject_reason" 
                                                  rows="3" placeholder="Optional: Provide reason for disabling the account...">{{ old('reject_reason', $doctor->reject_reason) }}</textarea>
                                        @error('reject_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            This reason will be visible to the doctor if provided.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.doctorslist') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Back to List
                                        </a>
                                        <div>
                                            <button type="reset" class="btn btn-outline-danger me-2">
                                                <i class="fas fa-undo me-2"></i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Update Doctor
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- License Image Modal -->
<div class="modal fade" id="licenseModal" tabindex="-1" aria-labelledby="licenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="licenseModalLabel">License Document - Dr. {{ $doctor->doctor_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="licenseImage" src="" alt="License Document" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <a id="downloadLicense" href="#" class="btn btn-success" download>
                    <i class="fas fa-download me-2"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Profile Image Modal -->
<div class="modal fade" id="profileImageModal" tabindex="-1" aria-labelledby="profileImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileImageModalLabel">Profile Image - Dr. {{ $doctor->doctor_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="profileImage" src="" alt="Profile Image" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <a id="downloadProfileImage" href="#" class="btn btn-success" download>
                    <i class="fas fa-download me-2"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script>
$(document).ready(function() {
    // Show/hide reject reason based on status selection
    $('#is_admin_confirmed').on('change', function() {
        if ($(this).val() == '2') {
            $('#rejectReasonContainer').slideDown();
        } else {
            $('#rejectReasonContainer').slideUp();
        }
    });

    // File input preview (optional enhancement)
    $('#license_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileName = file.name;
            $(this).next('.form-text').html(`Selected file: <strong>${fileName}</strong>`);
        }
    });

    $('#profile_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileName = file.name;
            $(this).next('.form-text').html(`Selected file: <strong>${fileName}</strong>`);
        }
    });
});

// License Image Modal Function
function showLicenseImage(imageSrc, doctorName) {
    document.getElementById('licenseImage').src = imageSrc;
    document.getElementById('downloadLicense').href = imageSrc;
    document.getElementById('downloadLicense').download = 'license_' + doctorName.replace(/\s+/g, '_') + '.jpg';
}

// Profile Image Modal Function
function showProfileImage(imageSrc, doctorName) {
    document.getElementById('profileImage').src = imageSrc;
    document.getElementById('downloadProfileImage').href = imageSrc;
    document.getElementById('downloadProfileImage').download = 'profile_' + doctorName.replace(/\s+/g, '_') + '.jpg';
}
</script>

<style>
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.border-bottom {
    border-color: #dee2e6 !important;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.img-thumbnail, .rounded-circle {
    transition: transform 0.2s ease-in-out;
}

.img-thumbnail:hover, .rounded-circle:hover {
    transform: scale(1.05);
    cursor: pointer;
}

.invalid-feedback {
    display: block;
}

.form-text {
    font-size: 0.875rem;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}
</style>
@endsection