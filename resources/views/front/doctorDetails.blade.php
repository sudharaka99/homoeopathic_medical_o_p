@extends('front.layouts.app')

@section('main')
<section class="section-4 bg-light">    
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 bg-white shadow-sm">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="" class="text-primary">
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
                {{-- @include('front.message') --}}

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img 
                                src="{{ $doctor->image ? asset($doctor->image) : asset('assets/images/doctor.png') }}" 
                                class="rounded-circle me-3 border" 
                                width="120" 
                                height="120" 
                                alt="{{ $doctor->doctor_name }}'s Profile Image"
                            >
                            <div>
                                <h3 class="mb-1">{{ $doctor->doctor_name }}</h3>
                                <p class="mb-1 text-muted">{{ $doctor->specialization_name }}</p>
                                <p class="mb-0 text-success small">
                                    <i class="fa fa-briefcase-medical me-1"></i> {{ $doctor->years_experience }} years of experience
                                </p>
                            </div>
                        </div>

                        @if(!empty($doctor->qualification))
                            <div class="mb-4">
                                <h5 class="text-dark">Qualifications</h5>
                                <p class="text-muted mb-0">{{ $doctor->qualification }}</p>
                            </div>
                        @endif

                        @if(!empty($doctor->clinic_name))
                            <div class="mb-4">
                                <h5 class="text-dark">Clinic</h5>
                                <p class="text-muted mb-0">{{ $doctor->clinic_name }}</p>
                            </div>
                        @endif

                        @if(!empty($doctor->license_number))
                            <div class="mb-4">
                                <h5 class="text-dark">License Number</h5>
                                <p class="text-muted mb-0">{{ $doctor->license_number }}</p>
                            </div>
                        @endif

                        @if(!empty($doctor->license_image))
                            <div class="mb-4">
                                <h5 class="text-dark">License Image</h5>
                                <a href="{{ asset($doctor->license_image) }}" target="_blank" class="d-inline-block">
                                    <img src="{{ asset($doctor->license_image) }}" class="img-fluid rounded shadow-sm" width="200" alt="License Image">
                                </a>
                            </div>
                        @endif

                        <div class="text-end">
                            @if(Auth::check())
                                @php
                                    $isSaved = false;
                                    $savedNote = '';
                                    // Check if doctor is already saved (you need to implement this logic)
                                @endphp
                                @if($isSaved)
                                    <button class="btn btn-success" disabled>
                                        <i class="fa fa-heart me-1"></i> Saved
                                    </button>
                                    <small class="d-block text-muted mt-1">Reason: {{ $savedNote ?: 'No reason provided' }}</small>
                                @else
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#saveDoctorModal">
                                        <i class="fa fa-heart me-1"></i> Save Doctor
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('account.login', ['redirect' => url()->current()]) }}" class="btn btn-outline-primary" aria-label="Login to save doctor profile">
                                    <i class="fa fa-heart me-1"></i> Login to Save
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Feedback and Ratings --}}
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-dark">Patient Feedback & Ratings</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($doctor->feedback && $doctor->feedback->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach($doctor->feedback as $feedback)
                                    <li class="list-group-item border-0 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $feedback->user->name }}</strong>
                                                <span class="badge bg-success ms-2">{{ $feedback->rating }} / 5</span>
                                            </div>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($feedback->created_at)->format('d M, Y') }}
                                            </div>
                                        </div>
                                        <p class="mt-2 text-muted">{{ $feedback->feedback }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No feedback available yet. Be the first to share your experience!</p>
                        @endif
                    </div>
                </div>

                {{-- Submit Feedback --}}
                @if(Auth::check())
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-dark">Submit Your Feedback</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="feedbackForm" action="{{ route('doctor.feedback', $doctor->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="rating" class="form-label">Rating (out of 5)</label>
                                <div class="star-rating">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" required>
                                        <label for="rating-{{ $i }}" class="fa fa-star" aria-label="Rate {{ $i }} stars"></label>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="feedback" class="form-label">Your Feedback</label>
                                <textarea name="feedback" id="feedback" class="form-control" rows="4" placeholder="Share your experience..." required minlength="10"></textarea>
                                <small class="text-muted">Minimum 10 characters</small>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitFeedbackBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Submit Feedback
                            </button>
                            <div id="feedbackMessage" class="mt-3 d-none alert"></div>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right side --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h3 class="mb-3 text-dark">Doctor Details</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Experience:</strong> <span>{{ $doctor->years_experience }} years</span></li>
                            <li class="mb-2"><strong>Specialization:</strong> <span>{{ $doctor->specialization_name }}</span></li>
                            <li class="mb-2"><strong>Clinic:</strong> <span>{{ $doctor->clinic_name ?? 'N/A' }}</span></li>
                            <li class="mb-2"><strong>License No:</strong> <span>{{ $doctor->license_number ?? 'N/A' }}</span></li>
                            <li class="mb-2">
                                <strong>Status:</strong> 
                                <span class="{{ $doctor->is_admin_confirmed ? 'text-success' : 'text-danger' }}">
                                    {{ $doctor->is_admin_confirmed ? 'Verified' : 'Pending Verification' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                @if(!empty($doctor->mobile))
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="mb-3 text-dark">Contact Information</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Phone:</strong> <a href="tel:{{ $doctor->mobile }}">{{ $doctor->mobile }}</a></li>
                            @if($doctor->email)
                            <li class="mb-2"><strong>Email:</strong> <a href="mailto:{{ $doctor->email }}">{{ $doctor->email }}</a></li>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="saveDoctorModalLabel">Save Doctor to Your List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="saveDoctorForm">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                        
                        <div class="mb-3">
                            <label for="save_reason" class="form-label">Why do you want to save this doctor? <span class="text-muted">(Optional)</span></label>
                            <textarea 
                                class="form-control" 
                                id="save_reason" 
                                name="save_reason" 
                                rows="4" 
                                placeholder="e.g., Great specialist for my condition, Planning to consult soon, Recommended by friend, etc."
                                maxlength="500"
                            ></textarea>
                            <div class="form-text">
                                <span id="charCount">0</span>/500 characters
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Save as:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="save_category" id="category_favorite" value="favorite" checked>
                                <label class="form-check-label" for="category_favorite">
                                    <i class="fa fa-heart text-danger me-1"></i> Favorite
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="save_category" id="category_consult" value="consult_later">
                                <label class="form-check-label" for="category_consult">
                                    <i class="fa fa-calendar text-primary me-1"></i> Plan to Consult
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="save_category" id="category_reference" value="reference">
                                <label class="form-check-label" for="category_reference">
                                    <i class="fa fa-bookmark text-warning me-1"></i> For Reference
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSaveDoctor">
                        <i class="fa fa-heart me-1"></i> Save Doctor
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="saveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>
</section>
@endsection

@section('customCSS')
<style>
.star-rating {
    display: flex;
    direction: rtl;
    justify-content: flex-start;
}
.star-rating input {
    display: none;
}
.star-rating label {
    font-size: 1.5rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffc107;
}
.card {
    border-radius: 10px;
}
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    transition: background-color 0.3s;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}
@media (max-width: 576px) {
    .d-flex.align-items-center.mb-4 img {
        width: 80px;
        height: 80px;
    }
    h3 {
        font-size: 1.5rem;
    }
}
</style>
@endsection

@section('customJS')
<script type="text/javascript">
$(document).ready(function() {
    // Character counter for save reason
    $('#save_reason').on('input', function() {
        const length = $(this).val().length;
        $('#charCount').text(length);
        if (length > 500) {
            $('#charCount').addClass('text-danger');
        } else {
            $('#charCount').removeClass('text-danger');
        }
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
                        confirmButtonText: 'OK'
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
        const feedbackText = $('#feedback').val().trim();

        if (feedbackText.length < 10) {
            messageDiv.text('Feedback must be at least 10 characters long.').addClass('alert-danger d-block').removeClass('alert-success');
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
                messageDiv.text(response.message || 'Feedback submitted successfully!').addClass('alert-success d-block');
                form[0].reset();
                $('.star-rating input').prop('checked', false);
                $('.star-rating label').css('color', '#ddd');
            },
            error: function(xhr) {
                messageDiv.text(xhr.responseJSON?.message || 'Failed to submit feedback. Please try again.').addClass('alert-danger d-block');
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
});
</script>
@endsection