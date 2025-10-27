@extends('front.layouts.app')

@section('main')

<section class="section-5">
    <div class="container my-5">
        <div class="py-lg-2">&nbsp;</div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 p-5">
                    <h1 class="h3">Register</h1>
                    <form action="" name="registrationForm" id="registrationForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="mb-2">Name*</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="mb-2">Email*</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="mb-2">Password*</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password">
                            <p></p>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="mb-2">Confirm Password*</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Enter Confirm Password">
                            <p></p>
                        </div>
                        
                        <!-- Doctor Registration Checkbox -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_doctor" name="is_doctor">
                            <label class="form-check-label" for="is_doctor">I am a Doctor</label>
                        </div>
                        
                        <!-- Doctor-specific fields (initially hidden) -->
                        <div id="doctor_fields" style="display: none;">
                            <div class="mb-3">
                                <label for="license_number" class="mb-2">Doctor License Number / Registration ID*</label>
                                <input type="text" name="license_number" id="license_number" class="form-control" placeholder="Enter License Number">
                                <p></p>
                            </div>
                            
                            <!-- License Image Upload -->
                            <div class="mb-3">
                                <label for="license_image" class="mb-2">Upload License Certificate*</label>
                                <input type="file" name="license_image" id="license_image" class="form-control" accept="image/*,.pdf">
                                <small class="form-text text-muted">Upload a clear image of your medical license/certificate (JPG, PNG, PDF - Max 2MB)</small>
                                <div id="license_image_preview" class="mt-2" style="display: none;">
                                    <img id="preview_img" src="#" alt="License Preview" class="img-thumbnail" style="max-height: 150px;">
                                    <button type="button" id="remove_image" class="btn btn-sm btn-danger mt-1">Remove Image</button>
                                </div>
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="qualification" class="mb-2">Qualification*</label>
                                <input type="text" name="qualification" id="qualification" class="form-control" placeholder="e.g., MBBS, MD, MS, DNB">
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="specialization" class="mb-2">Specialization*</label>
                                <select name="specialization" id="specialization" class="form-control">
                                    <option value="">-- Select Specialization --</option>
                                    @foreach ($specializations as $specialization)
                                        <option value="{{ $specialization->id }}">{{ $specialization->name }}</option>
                                    @endforeach
                                </select>
                                <p></p>
                            </div>

                            <!-- Practice District Field - Sri Lanka Districts -->
                            <div class="mb-3">
                                <label for="practice_district" class="mb-2">District of medicine*</label>
                                <select name="practice_district" id="practice_district" class="form-control">
                                    <option value="">-- Select District --</option>
                                    <!-- Western Province -->
                                    <optgroup label="Western Province">
                                        <option value="Colombo">Colombo</option>
                                        <option value="Gampaha">Gampaha</option>
                                        <option value="Kalutara">Kalutara</option>
                                    </optgroup>
                                    <!-- Central Province -->
                                    <optgroup label="Central Province">
                                        <option value="Kandy">Kandy</option>
                                        <option value="Matale">Matale</option>
                                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                                    </optgroup>
                                    <!-- Southern Province -->
                                    <optgroup label="Southern Province">
                                        <option value="Galle">Galle</option>
                                        <option value="Matara">Matara</option>
                                        <option value="Hambantota">Hambantota</option>
                                    </optgroup>
                                    <!-- Northern Province -->
                                    <optgroup label="Northern Province">
                                        <option value="Jaffna">Jaffna</option>
                                        <option value="Kilinochchi">Kilinochchi</option>
                                        <option value="Mannar">Mannar</option>
                                        <option value="Mullaitivu">Mullaitivu</option>
                                        <option value="Vavuniya">Vavuniya</option>
                                    </optgroup>
                                    <!-- Eastern Province -->
                                    <optgroup label="Eastern Province">
                                        <option value="Ampara">Ampara</option>
                                        <option value="Batticaloa">Batticaloa</option>
                                        <option value="Trincomalee">Trincomalee</option>
                                    </optgroup>
                                    <!-- North Western Province -->
                                    <optgroup label="North Western Province">
                                        <option value="Kurunegala">Kurunegala</option>
                                        <option value="Puttalam">Puttalam</option>
                                    </optgroup>
                                    <!-- North Central Province -->
                                    <optgroup label="North Central Province">
                                        <option value="Anuradhapura">Anuradhapura</option>
                                        <option value="Polonnaruwa">Polonnaruwa</option>
                                    </optgroup>
                                    <!-- Uva Province -->
                                    <optgroup label="Uva Province">
                                        <option value="Badulla">Badulla</option>
                                        <option value="Monaragala">Monaragala</option>
                                    </optgroup>
                                    <!-- Sabaragamuwa Province -->
                                    <optgroup label="Sabaragamuwa Province">
                                        <option value="Kegalle">Kegalle</option>
                                        <option value="Ratnapura">Ratnapura</option>
                                    </optgroup>
                                </select>
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="years_experience" class="mb-2">Years of Experience</label>
                                <input type="number" name="years_experience" id="years_experience" class="form-control" placeholder="Enter years of experience">
                                <p></p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="clinic_name" class="mb-2">Clinic / Hospital Name</label>
                                <input type="text" name="clinic_name" id="clinic_name" class="form-control" placeholder="Enter clinic or hospital name">
                                <p></p>
                            </div>
                        </div>

                        <button class="btn btn-primary mt-2">Register</button>
                    </form>                    
                </div>
                <div class="mt-4 text-center">
                    <p>Have an account? <a href="{{ route('account.login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJS')
<script>
$(document).ready(function() {
    // Show/hide doctor fields based on checkbox
    $('#is_doctor').change(function() {
        if(this.checked) {
            $('#doctor_fields').show();
            // Make doctor fields required
            $('#license_number, #qualification, #specialization, #practice_district, #license_image').prop('required', true);
        } else {
            $('#doctor_fields').hide();
            // Remove required attribute and clear file input
            $('#license_number, #qualification, #specialization, #practice_district, #license_image').prop('required', false);
            $('#license_image').val('');
            $('#license_image_preview').hide();
        }
    });

    // License image preview functionality
    $('#license_image').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                $(this).val('');
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a JPG, PNG, or PDF file');
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
            } else {
                // For PDF files, show a different preview
                $('#preview_img').attr('src', '');
                $('#preview_img').attr('alt', 'PDF Document');
                $('#preview_img').after('<div class="mt-1"><i class="fas fa-file-pdf text-danger"></i> PDF Document</div>');
                $('#license_image_preview').show();
            }
        }
    });

    // Remove image preview
    $('#remove_image').click(function() {
        $('#license_image').val('');
        $('#license_image_preview').hide();
        $('#preview_img').next('div').remove(); // Remove PDF text if exists
    });

    $("#registrationForm").submit(function(e){
        e.preventDefault();

        // Create FormData object to handle file upload
        var formData = new FormData(this);

        $.ajax({
            url: '{{ route("account.processRegistration") }}',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response){
                if(response.status == false) {
                    var errors = response.errors;
                    
                    // Handle Name Error
                    if(errors.name){
                        $("#name").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.name);
                    } else {
                        $("#name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    // Handle Email Error
                    if(errors.email){
                        $("#email").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.email);
                    } else {
                        $("#email").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    // Handle Password Error
                    if(errors.password){
                        $("#password").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.password);
                    } else {
                        $("#password").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    // Handle Confirm Password Error
                    if(errors.confirm_password){
                        $("#confirm_password").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.confirm_password);
                    } else {
                        $("#confirm_password").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    // Handle Doctor Fields Errors
                    if(errors.license_number){
                        $("#license_number").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.license_number);
                    } else {
                        $("#license_number").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    if(errors.license_image){
                        $("#license_image").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.license_image);
                    } else {
                        $("#license_image").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    if(errors.qualification){
                        $("#qualification").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.qualification);
                    } else {
                        $("#qualification").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    if(errors.specialization){
                        $("#specialization").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.specialization);
                    } else {
                        $("#specialization").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    // Handle Practice District Error
                    if(errors.practice_district){
                        $("#practice_district").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.practice_district);
                    } else {
                        $("#practice_district").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                } else {
                    // Clear all errors
                    $("#name, #email, #password, #confirm_password, #license_number, #qualification, #specialization, #practice_district, #license_image").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');
                    
                    // Redirect to login page
                    window.location.href = '{{ route("account.login") }}';
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred during registration. Please try again.');
            }
        });
    });
});
</script>
@endsection