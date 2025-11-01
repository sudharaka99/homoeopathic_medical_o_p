@extends('front.layouts.app')

@section('main')
<section class="section-4 bg-light">    
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 bg-white shadow-sm">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('patient.findDoctors') }}" class="text-primary">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Doctors
                            </a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div> 
    </div>

    <div class="container doctor_details_area py-4">
        <div class="row">
            {{-- Left side --}}
            <div class="col-md-8">
                @include('front.message')

                <!-- Doctor Profile Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row align-items-center mb-4">
                            <img 
                                src="{{ $doctor->image ? asset('profile_pic/thumb/' . $doctor->image) : asset('assets/images/doctor.png') }}" 
                                class="rounded-circle me-sm-3 mb-3 mb-sm-0 border" 
                                width="120" 
                                height="120" 
                                alt="{{ $doctor->doctor_name }}'s Profile Image"
                            >
                            <div class="text-center text-sm-start flex-grow-1">
                                <h3 class="mb-1">{{ $doctor->doctor_name }}</h3>
                                <p class="mb-1 text-muted">{{ $doctor->specialization_name }}</p>
                                <p class="mb-0 text-success small">
                                    <i class="fa fa-briefcase-medical me-1"></i> {{ $doctor->years_experience }} years of experience
                                </p>
                                @if($doctor->is_admin_confirmed)
                                    <small class="badge bg-success mt-2">
                                        <i class="fa fa-check-circle me-1"></i> Verified Doctor
                                    </small>
                                @else
                                    <small class="badge bg-warning mt-2">
                                        <i class="fa fa-clock me-1"></i> Pending Verification
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Doctor Details Grid -->
                        <div class="row mb-4">
                            @if(!empty($doctor->qualification))
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-dark fw-bold">Qualifications</h6>
                                    <p class="text-muted mb-0">{{ $doctor->qualification }}</p>
                                </div>
                            @endif

                            @if(!empty($doctor->clinic_name))
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-dark fw-bold">Clinic</h6>
                                    <p class="text-muted mb-0">{{ $doctor->clinic_name }}</p>
                                </div>
                            @endif

                            @if(!empty($doctor->license_number))
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-dark fw-bold">License Number</h6>
                                    <p class="text-muted mb-0">{{ $doctor->license_number }}</p>
                                </div>
                            @endif

                            @if(!is_null($doctor->appointment_fee))
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-dark fw-bold">Appointment Fee</h6>
                                    <p class="text-success fw-bold mb-0">Rs. {{ $doctor->appointment_fee }}</p>
                                </div>
                            @endif
                        </div>

                        @if(!empty($doctor->license_image))
                            <div class="mb-4">
                                <h6 class="text-dark fw-bold">License Image</h6>
                                <a href="{{ asset('storage/' . $doctor->license_image) }}" target="_blank" class="d-inline-block">
                                    <img src="{{ asset('storage/' . $doctor->license_image) }}" class="img-fluid rounded shadow-sm" width="200" alt="License Image">
                                </a>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-start justify-content-sm-end pt-3 border-top">
                            @if(Auth::check())
                                {{-- Book Appointment Button for Logged-in Users --}}
                                <button type="button" class="btn btn-success" onclick="bookAppointment({{ $doctor->doctor_id  }})">
                                    <i class="fa fa-calendar-check me-1"></i> Book Appointment
                                </button>

                                {{-- Save Doctor Button with Dynamic Styling --}}
                                @if($doctor->is_saved)
                                    <button class="btn btn-danger" disabled title="This doctor is already saved to your list">
                                        <i class="fa fa-heart me-1"></i> Saved Doctor
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#saveDoctorModal" title="Add this doctor to your saved list">
                                        <i class="fa fa-heart me-1"></i> Save Doctor
                                    </button>
                                @endif
                            @else
                                {{-- Login Buttons for Non-Logged-in Users --}}
                                <a href="{{ route('account.login', ['redirect' => url()->current()]) }}" class="btn btn-success">
                                    <i class="fa fa-calendar-check me-1"></i> Book Appointment
                                </a>
                                <a href="{{ route('account.login', ['redirect' => url()->current()]) }}" class="btn btn-outline-primary" aria-label="Login to save doctor profile">
                                    <i class="fa fa-heart me-1"></i> Save Doctor
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Feedback and Ratings Section --}}
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0 text-dark">
                            <i class="fa fa-star text-warning me-2"></i> Patient Feedback & Ratings
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if($doctor->feedback->isNotEmpty())
                            <div class="feedback-summary mb-4">
                                @php
                                    $avgRating = $doctor->feedback->avg('rating');
                                    $totalFeedback = $doctor->feedback->count();
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <h5 class="mb-1">{{ number_format($avgRating, 1) }} <span class="text-warning"><i class="fa fa-star"></i></span></h5>
                                        <small class="text-muted">{{ $totalFeedback }} {{ $totalFeedback === 1 ? 'review' : 'reviews' }}</small>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach($doctor->feedback as $feedback)
                                    <li class="list-group-item border-0 py-3 px-0">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <strong class="d-block">{{ $feedback->user_name }}</strong>
                                                <div class="d-flex align-items-center mt-1">
                                                    <span class="badge bg-success me-2">
                                                        <i class="fa fa-star me-1"></i> {{ $feedback->rating }}/5
                                                    </span>
                                                    <span class="text-muted small">
                                                        {{ \Carbon\Carbon::parse($feedback->created_at)->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-muted mb-0">{{ $feedback->feedback }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-comments text-muted" style="font-size: 2.5rem;"></i>
                                <p class="text-muted mt-3 mb-0">No feedback available yet. Be the first to share your experience!</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit Feedback Section --}}
                @if(Auth::check())
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0 text-dark">
                            <i class="fa fa-pen me-2"></i> Submit Your Feedback
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="feedbackForm" action="{{ route('doctor.feedback', $doctor->id) }}" method="POST" novalidate>
                            @csrf
                            <div class="form-group mb-3">
                                <label for="rating" class="form-label fw-bold">Rate Your Experience <span class="text-danger">*</span></label>
                                <div class="star-rating" role="group" aria-labelledby="rating-label">
                                    <span id="rating-label" class="d-none">Select your rating</span>
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" required>
                                        <label for="rating-{{ $i }}" class="fa fa-star" aria-label="Rate {{ $i }} stars" title="Rate {{ $i }} stars"></label>
                                    @endfor
                                </div>
                                <small class="text-muted d-block mt-2">Click on stars to rate</small>
                                @error('rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="feedback" class="form-label fw-bold">Your Feedback</label>
                                <textarea name="feedback" id="feedback" class="form-control" rows="4" placeholder="Share your experience with this doctor... (Optional)"></textarea>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted"><span id="charCount">0</span>/500 characters</small>
                                </div>
                                @error('feedback')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 w-sm-auto" id="submitFeedbackBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                <span class="btn-text">Submit Feedback</span>
                            </button>
                            <div id="feedbackMessage" class="mt-3 d-none alert alert-dismissible fade show" role="alert">
                                <span id="messageText"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right side - Doctor Details Sidebar --}}
            <div class="col-md-4">
                <!-- Quick Info Card -->
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="mb-3 text-dark fw-bold">
                            <i class="fa fa-info-circle me-2"></i> Quick Details
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fa fa-graduation-cap text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Experience</small>
                                    <strong>{{ $doctor->years_experience }} years</strong>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fa fa-stethoscope text-success me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Specialization</small>
                                    <strong>{{ $doctor->specialization_name }}</strong>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fa fa-hospital text-info me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Clinic</small>
                                    <strong>{{ $doctor->clinic_name ?? 'N/A' }}</strong>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fa fa-id-card text-warning me-2"></i>
                                <div>
                                    <small class="text-muted d-block">License No</small>
                                    <strong>{{ $doctor->license_number ?? 'N/A' }}</strong>
                                </div>
                            </li>
                            @if(!is_null($doctor->appointment_fee))
                            <li class="mb-0 d-flex align-items-center pt-3 border-top">
                                <i class="fa fa-rupee-sign text-success me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Appointment Fee</small>
                                    <strong class="text-success h6">Rs. {{ $doctor->appointment_fee }}</strong>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Contact Card -->
                @if(!empty($doctor->mobile))
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="mb-3 text-dark fw-bold">
                            <i class="fa fa-phone me-2"></i> Contact Information
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <small class="text-muted d-block mb-1">Phone</small>
                                <a href="tel:{{ $doctor->mobile }}" class="text-primary text-decoration-none fw-bold">
                                    <i class="fa fa-phone me-1"></i> {{ $doctor->mobile }}
                                </a>
                            </li>
                            @if($doctor->email)
                            <li class="mb-0">
                                <small class="text-muted d-block mb-1">Email</small>
                                <a href="mailto:{{ $doctor->email }}" class="text-primary text-decoration-none fw-bold">
                                    <i class="fa fa-envelope me-1"></i> {{ $doctor->email }}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Save Doctor Modal --}}
    <div class="modal fade" id="saveDoctorModal" tabindex="-1" aria-labelledby="saveDoctorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="saveDoctorModalLabel">
                        <i class="fa fa-heart text-danger me-2"></i> Save Doctor to Your List
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="saveDoctorForm">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                        
                        <div class="mb-4">
                            <label for="save_reason" class="form-label fw-bold">
                                Why do you want to save this doctor? 
                                <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="save_reason" 
                                name="save_reason" 
                                rows="4" 
                                placeholder="e.g., Great specialist for my condition, Planning to consult soon, Recommended by friend, etc."
                                maxlength="500"
                            ></textarea>
                            <div class="form-text mt-2">
                                <span id="charCount">0</span>/500 characters
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label fw-bold mb-3">Save as:</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="save_category" id="category_favorite" value="favorite" checked>
                                <label class="form-check-label" for="category_favorite">
                                    <i class="fa fa-heart text-danger me-2"></i> Favorite
                                </label>
                                <small class="text-muted d-block ms-4">For doctors you prefer</small>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="save_category" id="category_consult" value="consult_later">
                                <label class="form-check-label" for="category_consult">
                                    <i class="fa fa-calendar text-primary me-2"></i> Plan to Consult
                                </label>
                                <small class="text-muted d-block ms-4">For future consultations</small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="save_category" id="category_reference" value="reference">
                                <label class="form-check-label" for="category_reference">
                                    <i class="fa fa-bookmark text-warning me-2"></i> For Reference
                                </label>
                                <small class="text-muted d-block ms-4">For keeping as reference</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSaveDoctor">
                        <i class="fa fa-heart me-1"></i> Save Doctor
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customCSS')
<style>
/* Star Rating Styles */
.star-rating {
    display: flex;
    direction: rtl;
    justify-content: flex-start;
    gap: 0.5rem;
}
.star-rating input {
    display: none;
}
.star-rating label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffc107;
    transform: scale(1.1);
}
.star-rating label:active {
    transform: scale(0.95);
}

/* Card Styles */
.card {
    border-radius: 10px;
    overflow: hidden;
}
.card-header {
    background-color: #f8f9fa !important;
}

/* Button Styles */
.btn {
    transition: all 0.3s ease;
    font-weight: 500;
}
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
    transform: translateY(-2px);
}
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-2px);
}
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}
.btn-outline-primary:hover {
    transform: translateY(-2px);
}

/* Form Styles */
.form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.3s ease;
}
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.form-check-input {
    cursor: pointer;
    transition: all 0.2s ease;
}
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

/* Feedback Styles */
.feedback-summary {
    background: linear-gradient(135deg, #fff9e6 0%, #fff 100%);
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

/* Responsive Design */
@media (max-width: 768px) {
    .col-md-4 {
        margin-top: 2rem;
    }
    .sticky-top {
        position: static !important;
    }
    .d-flex.flex-column.flex-sm-row.gap-2 {
        gap: 0.5rem;
    }
    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
}
@media (max-width: 576px) {
    .d-flex.align-items-center.mb-4 img {
        width: 80px;
        height: 80px;
    }
    h3 {
        font-size: 1.5rem;
    }
    .star-rating label {
        font-size: 1.75rem;
    }
    .btn-group-vertical .btn {
        width: 100%;
    }
    .text-center.text-sm-start {
        text-align: center;
    }
}

/* Loading Spinner */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Focus States for Accessibility */
a:focus, button:focus, input:focus, textarea:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}
</style>
@endsection


@section('customJS')
<script type="text/javascript">
// Define bookAppointment function globally (outside document.ready)
// Define bookAppointment function globally (outside document.ready)
function bookAppointment(doctorId) {
    console.log('Booking appointment for doctor ID:', doctorId);
    
    Swal.fire({
        title: 'Book Appointment',
        text: 'Would you like to book an appointment with this doctor?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Book Now',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Redirecting...',
                text: 'Please wait',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1500
            });
            
            // Redirect to booking page - FIXED ROUTE
            setTimeout(() => {
                window.location.href = '{{ route("patient.bookAppointment", ":id") }}'.replace(':id', doctorId);
            }, 1000);
        }
    });
}

$(document).ready(function() {
    // Character counter for save reason
    $('#save_reason').on('input', function() {
        const length = $(this).val().length;
        $('#charCount').text(length);
        if (length > 500) {
            $('#charCount').addClass('text-danger fw-bold');
        } else {
            $('#charCount').removeClass('text-danger fw-bold');
        }
    });

    // Character counter for feedback
    $('#feedback').on('input', function() {
        const length = $(this).val().length;
        $('#charCount').text(length);
    });

    // Save Doctor Function with Modal
    $('#confirmSaveDoctor').on('click', function() {
        const saveBtn = $(this);
        const formData = new FormData(document.getElementById('saveDoctorForm'));
        const modal = bootstrap.Modal.getInstance(document.getElementById('saveDoctorModal'));

        saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: '{{ route("patient.saveDoctor") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                modal.hide();
                
                if (response.status === 'success' || response.status === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Doctor saved successfully!',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: response.message || 'Failed to save doctor.',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                    });
                    saveBtn.prop('disabled', false).html('<i class="fa fa-heart me-1"></i> Save Doctor');
                }
            },
            error: function(xhr) {
                modal.hide();
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                });
                saveBtn.prop('disabled', false).html('<i class="fa fa-heart me-1"></i> Save Doctor');
            }
        });
    });

    // Feedback Form Submission
    $('#feedbackForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#submitFeedbackBtn');
        const spinner = submitBtn.find('.spinner-border');
        const messageDiv = $('#feedbackMessage');
        const messageText = $('#messageText');
        const feedbackText = $('#feedback').val().trim();
        const rating = $('input[name="rating"]:checked').val();

        // Remove the minimum character requirement validation
        if (!rating) {
            messageText.text('Please select a rating.');
            messageDiv.addClass('alert-danger d-block').removeClass('d-none alert-success');
            return;
        }

        spinner.removeClass('d-none');
        submitBtn.prop('disabled', true);
        messageDiv.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                messageText.text(response.message || 'Feedback submitted successfully!');
                messageDiv.addClass('alert-success d-block').removeClass('d-none alert-danger');
                form[0].reset();
                $('.star-rating input').prop('checked', false);
                $('.star-rating label').css('color', '#ddd');
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Your feedback has been submitted successfully!',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to submit feedback. Please try again.';
                messageText.text(errorMsg);
                messageDiv.addClass('alert-danger d-block').removeClass('d-none alert-success');
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMsg,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                });
            },
            complete: function() {
                spinner.addClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Reset modal when closed
    $('#saveDoctorModal').on('hidden.bs.modal', function() {
        $('#save_reason').val('');
        $('#charCount').text('0');
        $('input[name="save_category"][value="favorite"]').prop('checked', true);
        $('#confirmSaveDoctor').prop('disabled', false).html('<i class="fa fa-heart me-1"></i> Save Doctor');
    });

    // Keyboard navigation for star rating
    $('.star-rating').on('keydown', 'label', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });

    // Enhanced Form Validation
    $('form').on('submit', function() {
        $(this).find('input, textarea, select').each(function() {
            if (!$(this).val() && $(this).prop('required')) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    // Add visual feedback on form input
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });

    // Smooth scroll to feedback section
    $('#submitFeedbackBtn').on('click', function() {
        if ($('#feedbackForm')[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            $([document.documentElement, document.body]).animate({
                scrollTop: $('#feedbackForm').offset().top - 100
            }, 800);
        }
    });

    // Close alert messages
    $('.alert-dismissible').on('click', '.btn-close', function() {
        $(this).parent().fadeOut(300);
    });

    // Add loading state to all buttons during AJAX
    $(document).ajaxStart(function() {
        $('button[type="submit"]').prop('disabled', true).css('opacity', '0.6');
    }).ajaxStop(function() {
        $('button[type="submit"]').prop('disabled', false).css('opacity', '1');
    });

    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
