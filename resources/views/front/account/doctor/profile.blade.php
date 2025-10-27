@extends('front.layouts.app')

@section('main')

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Doctor Profile</li>
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

                <!-- Profile Form -->
                <div class="card border-0 shadow mb-4">
                    <form action="" method="POST" id="profileForm" name="profileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-4">My Profile</h3>
                            
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="mb-2">Full Name*</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="email" class="mb-2">Email*</label>
                                    <input type="text" name="email" id="email" class="form-control" value="{{ $user->email }}">
                                    <p></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="mobile" class="mb-2">Mobile*</label>
                                    <input type="text" name="mobile" id="mobile" class="form-control" value="{{ $user->mobile }}">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="designation" class="mb-2">Designation</label>
                                    <input type="text" name="designation" id="designation" class="form-control" value="{{ $user->designation }}">
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="license_number" class="mb-2">License Number*</label>
                                    <input type="text" name="license_number" id="license_number" class="form-control" value="{{ $user->license_number }}">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="qualification" class="mb-2">Qualification*</label>
                                    <input type="text" name="qualification" id="qualification" class="form-control" value="{{ $user->qualification }}">
                                    <p></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="specialization" class="mb-2">Specialization*</label>
                                    <select name="specialization" id="specialization" class="form-control">
                                        <option value="">-- Select Specialization --</option>
                                        @foreach ($specializations as $specialization)
                                            <option value="{{ $specialization->id }}" {{ $user->specialization == $specialization->id ? 'selected' : '' }}>
                                                {{ $specialization->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="district" class="mb-2">Practice District*</label>
                                    <select name="district" id="district" class="form-control">
                                        <option value="">-- Select District --</option>
                                        <!-- Western Province -->
                                        <optgroup label="Western Province">
                                            <option value="Colombo" {{ $user->district == 'Colombo' ? 'selected' : '' }}>Colombo</option>
                                            <option value="Gampaha" {{ $user->district == 'Gampaha' ? 'selected' : '' }}>Gampaha</option>
                                            <option value="Kalutara" {{ $user->district == 'Kalutara' ? 'selected' : '' }}>Kalutara</option>
                                        </optgroup>
                                        <!-- Central Province -->
                                        <optgroup label="Central Province">
                                            <option value="Kandy" {{ $user->district == 'Kandy' ? 'selected' : '' }}>Kandy</option>
                                            <option value="Matale" {{ $user->district == 'Matale' ? 'selected' : '' }}>Matale</option>
                                            <option value="Nuwara Eliya" {{ $user->district == 'Nuwara Eliya' ? 'selected' : '' }}>Nuwara Eliya</option>
                                        </optgroup>
                                        <!-- Southern Province -->
                                        <optgroup label="Southern Province">
                                            <option value="Galle" {{ $user->district == 'Galle' ? 'selected' : '' }}>Galle</option>
                                            <option value="Matara" {{ $user->district == 'Matara' ? 'selected' : '' }}>Matara</option>
                                            <option value="Hambantota" {{ $user->district == 'Hambantota' ? 'selected' : '' }}>Hambantota</option>
                                        </optgroup>
                                        <!-- Northern Province -->
                                        <optgroup label="Northern Province">
                                            <option value="Jaffna" {{ $user->district == 'Jaffna' ? 'selected' : '' }}>Jaffna</option>
                                            <option value="Kilinochchi" {{ $user->district == 'Kilinochchi' ? 'selected' : '' }}>Kilinochchi</option>
                                            <option value="Mannar" {{ $user->district == 'Mannar' ? 'selected' : '' }}>Mannar</option>
                                            <option value="Mullaitivu" {{ $user->district == 'Mullaitivu' ? 'selected' : '' }}>Mullaitivu</option>
                                            <option value="Vavuniya" {{ $user->district == 'Vavuniya' ? 'selected' : '' }}>Vavuniya</option>
                                        </optgroup>
                                        <!-- Eastern Province -->
                                        <optgroup label="Eastern Province">
                                            <option value="Ampara" {{ $user->district == 'Ampara' ? 'selected' : '' }}>Ampara</option>
                                            <option value="Batticaloa" {{ $user->district == 'Batticaloa' ? 'selected' : '' }}>Batticaloa</option>
                                            <option value="Trincomalee" {{ $user->district == 'Trincomalee' ? 'selected' : '' }}>Trincomalee</option>
                                        </optgroup>
                                        <!-- North Western Province -->
                                        <optgroup label="North Western Province">
                                            <option value="Kurunegala" {{ $user->district == 'Kurunegala' ? 'selected' : '' }}>Kurunegala</option>
                                            <option value="Puttalam" {{ $user->district == 'Puttalam' ? 'selected' : '' }}>Puttalam</option>
                                        </optgroup>
                                        <!-- North Central Province -->
                                        <optgroup label="North Central Province">
                                            <option value="Anuradhapura" {{ $user->district == 'Anuradhapura' ? 'selected' : '' }}>Anuradhapura</option>
                                            <option value="Polonnaruwa" {{ $user->district == 'Polonnaruwa' ? 'selected' : '' }}>Polonnaruwa</option>
                                        </optgroup>
                                        <!-- Uva Province -->
                                        <optgroup label="Uva Province">
                                            <option value="Badulla" {{ $user->district == 'Badulla' ? 'selected' : '' }}>Badulla</option>
                                            <option value="Monaragala" {{ $user->district == 'Monaragala' ? 'selected' : '' }}>Monaragala</option>
                                        </optgroup>
                                        <!-- Sabaragamuwa Province -->
                                        <optgroup label="Sabaragamuwa Province">
                                            <option value="Kegalle" {{ $user->district == 'Kegalle' ? 'selected' : '' }}>Kegalle</option>
                                            <option value="Ratnapura" {{ $user->district == 'Ratnapura' ? 'selected' : '' }}>Ratnapura</option>
                                        </optgroup>
                                    </select>
                                    <p></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="years_experience" class="mb-2">Years of Experience</label>
                                    <input type="number" name="years_experience" id="years_experience" class="form-control" value="{{ $user->years_experience }}">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="clinic_name" class="mb-2">Clinic / Hospital Name</label>
                                    <input type="text" name="clinic_name" id="clinic_name" class="form-control" value="{{ $user->clinic_name }}">
                                    <p></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="appointment_fee" class="mb-2">Appointment Fee (LKR)</label>
                                    <input type="number" name="appointment_fee" id="appointment_fee" class="form-control" value="{{ $user->appointment_fee }}">
                                    <p></p>
                                </div>
                            </div>

                            <!-- License Image -->
                            <div class="mb-4">
                                <label for="license_image" class="mb-2">Medical License Certificate</label>
                                
                                @if($user->license_image)
                                    <div class="mb-3">
                                        <p class="text-success">Current License File:</p>
                                        @if(pathinfo($user->license_image, PATHINFO_EXTENSION) === 'pdf')
                                            <div class="alert alert-info">
                                                <i class="fas fa-file-pdf text-danger"></i> 
                                                <a href="{{ asset('storage/'.$user->license_image) }}" target="_blank" class="ms-2">
                                                    View Current License PDF
                                                </a>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/'.$user->license_image) }}" alt="License Preview" class="img-thumbnail" style="max-height: 200px;">
                                        @endif
                                    </div>
                                @endif

                                <input type="file" name="license_image" id="license_image" class="form-control" accept="image/*,.pdf">
                                <small class="form-text text-muted">Upload license certificate (JPG, PNG, PDF - Max 2MB)</small>
                                <p></p>
                            </div>
                        </div>
                        <div class="card-footer p-4">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Section -->
                <div class="card border-0 shadow mb-4">
                    <form action="{{ route('account.updatePassword') }}" method="POST" id="changePasswordForm" name="changePasswordForm">
                        @csrf
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">Change Password</h3>
                            <div class="mb-4">
                                <label for="old_password" class="mb-2">Old Password*</label>
                                <input type="password" name="old_password" id="old_password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="new_password" class="mb-2">New Password*</label>
                                <input type="password" name="new_password" id="new_password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="mb-2">Confirm Password*</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                                <p></p>
                            </div>                        
                        </div>
                        <div class="card-footer p-4">
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
$(document).ready(function() {
    // Profile Form Submission
    $("#profileForm").submit(function(e){
        e.preventDefault();
        
        var formData = new FormData(this);
        
        // Add _method field for PUT request
        formData.append('_method', 'PUT');
        
        $.ajax({
            url: '{{ route("doctor.updateProfile") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.status == true) {
                    // SweetAlert Success
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('doctor.profile') }}";
                    });
                } else {
                    handleErrors(response.errors);
                    // SweetAlert Error for validation
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form for errors',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                // SweetAlert Error for server error
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                });
            }
        });
    });

    // Change Password Form
    $("#changePasswordForm").submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '{{ route("account.updatePassword") }}',
            type: 'POST',
            dataType: 'json',
            data: $("#changePasswordForm").serializeArray(),
            success: function(response) {
                if(response.status == true) {
                    // SweetAlert Success
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('doctor.profile') }}";
                    });
                } else {
                    handleErrors(response.errors);
                    // SweetAlert Error for validation
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form for errors',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                // SweetAlert Error for server error
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                });
            }
        });
    });

    function handleErrors(errors) {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        for (let key in errors) {
            if (errors.hasOwnProperty(key)) {
                let inputField = $("#" + key);
                inputField.addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback')
                    .html(errors[key]);
            }
        }
    }

    // License image preview functionality
    $('#license_image').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size must be less than 2MB'
                });
                $(this).val('');
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload a JPG, PNG, or PDF file'
                });
                $(this).val('');
                return;
            }

            // Show preview for images
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_img').attr('src', e.target.result);
                    $('#license_image_preview').show();
                }
                reader.readAsDataURL(file);
            }
        }
    });
});
</script>
@endsection