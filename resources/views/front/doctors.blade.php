@extends('front.layouts.app')

@section('main')

<section class="section-3 py-5 bg-2 ">
    <div class="container">     
        <div class="row">
            @include('front.message')
            <div class="col-6 col-md-10">
                <h2>Find Doctors</h2>  
            </div>
            <div class="col-6 col-md-2">
                <div class="align-end">
                    <select name="sort" id="sort" class="form-control">
                        <option value="1" {{(Request::get('sort')== '1')? 'selected': ''}}>Latest</option>
                        <option value="0" {{(Request::get('sort')== '0')? 'selected': ''}}>Oldest</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row pt-5">
            <div class="col-md-4 col-lg-3 sidebar mb-4">
                <form action="" name="searchForm" id="searchForm">
                    <div class="card border-0 shadow p-4">
                        <div class="mb-4">
                            <h2>Doctor Name</h2>
                            <input value="{{Request::get('name')}}" type="text" name="name" id="name" placeholder="Doctor Name" class="form-control">
                        </div>

                        <div class="mb-4">
                            <h2>Specialization</h2>
                            <select name="specialization" id="specialization" class="form-control">
                                <option value="">Select Specialization</option>
                                @if ($specializations)
                                    @foreach ($specializations as $specialization)
                                    <option {{(Request::get('specialization')== $specialization->id)? 'selected' : ''}} value="{{$specialization->id}}">{{$specialization->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- District Filter Added Here -->
                        <div class="mb-4">
                            <h2>District</h2>
                            <select name="district" id="district" class="form-control">
                                <option value="">Select District</option>
                                @if ($districts->isNotEmpty())
                                    @foreach ($districts as $district)
                                    <option {{(Request::get('district')== $district)? 'selected' : ''}} value="{{$district}}">{{$district}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>                   

                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{route("patient.findDoctors")}}" class="btn btn-secondary mt-3">Reset</a>                  
                    </div>
                </form>
            </div>
            <div class="col-md-8 col-lg-9 ">
                <div class="doctor_listing_area">                    
                    <div class="doctor_lists">
                        <div class="row">
                            @if($doctors->isNotEmpty())
                                @foreach ($doctors as $doctor)
                                <div class="col-md-6">
                                    <div class="card border-0 p-3 shadow mb-4">
                                        <div class="card-body text-center">
                                            <!-- Doctor Profile Image -->
                                            @if($doctor->profile_picture)
                                                <img src="{{ asset('profile_pic/thumb/' . $doctor->profile_picture) }}" 
                                                     alt="{{ $doctor->doctor_name }}'s Profile Image" 
                                                     class="img-fluid rounded-circle mb-3" 
                                                     style="width: 120px; height: 120px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('assets/images/avatar7.png') }}" 
                                                     alt="Default Profile" 
                                                     class="img-fluid rounded-circle mb-3" 
                                                     style="width: 120px; height: 120px; object-fit: cover;">
                                            @endif
                                            
                                            <h3 class="border-0 fs-5 pb-2 mb-0">{{$doctor->doctor_name}}</h3>
                                            
                                            <!-- Specialization -->
                                            <div class="specialization-highlight mb-2">
                                                <strong>Specialization:</strong> {{$doctor->specialization_name ?? 'General Practitioner'}}
                                            </div>

                                            <!-- Qualification -->
                                            <div class="qualification-highlight mb-2">
                                                <strong>Qualification:</strong> {{$doctor->qualification ?? 'Not specified'}}
                                            </div>

                                            <!-- District Information Added Here -->
                                            @if($doctor->district)
                                            <div class="district-highlight mb-2">
                                                <strong>District:</strong> {{$doctor->district}}
                                            </div>
                                            @endif
                                            
                                            <div class="bg-light p-3 border rounded">
                                                <p class="mb-2">
                                                    <span class="fw-bolder"><i class="fa fa-briefcase text-success"></i></span>
                                                    <span class="ps-1">{{$doctor->years_experience ?? 'N/A'}} years experience</span>
                                                </p>
                                                @if(!is_null($doctor->fee))
                                                <p class="mb-0">
                                                    <span class="fw-bolder"><i class="fa fa-money text-warning"></i></span>
                                                    <span class="ps-1">Appointment Fee: Rs. {{$doctor->fee}}</span>
                                                </p>
                                                @endif

                                                @if($doctor->license_number)
                                                <p class="mb-0 mt-2">
                                                    <span class="fw-bolder"><i class="fa fa-id-card text-info"></i></span>
                                                    <span class="ps-1 small">License: {{$doctor->license_number}}</span>
                                                </p>
                                                @endif

                                                @if($doctor->clinic_name)
                                                <p class="mb-0 mt-2">
                                                    <span class="fw-bolder"><i class="fa fa-hospital text-primary"></i></span>
                                                    <span class="ps-1 small">Clinic: {{$doctor->clinic_name}}</span>
                                                </p>
                                                @endif
                                            </div>

                                            <div class="d-grid mt-3">
                                                <a href="{{route('doctor.details',$doctor->id)}}" class="btn btn-primary btn-lg">View Profile</a>
                                                @if(Auth::check())
                                                    @if($doctor->is_saved)
                                                        <button class="btn btn-success mt-2" disabled>
                                                            <i class="fa fa-heart me-1"></i> Saved Doctor
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-primary mt-2" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#saveDoctorModal"
                                                                data-doctor-id="{{ $doctor->id }}"
                                                                data-doctor-name="{{ $doctor->doctor_name }}">
                                                            <i class="fa fa-heart me-1"></i> Save Doctor
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('account.login', ['redirect' => url()->current()]) }}" class="btn btn-outline-primary mt-2">
                                                        <i class="fa fa-heart me-1"></i> Login to Save
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <div>
                                    {{ $doctors->links() }}
                                </div>
                                

                            @else
                                <div class="col-md-12">
                                    <div class="card border-0 p-5 text-center">
                                        <i class="fa fa-user-md text-muted mb-3" style="font-size: 4rem;"></i>
                                        <h4 class="text-muted">No Doctors Found</h4>
                                        <p class="text-muted mb-4">Try adjusting your search criteria</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Save Doctor Modal -->
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
                    <input type="hidden" name="doctor_id" id="modal_doctor_id">
                    
                    <div class="mb-4">
                        <p class="text-muted mb-3">You are saving: <strong id="doctorNameDisplay"></strong></p>
                        
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

@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $("#searchForm").submit(function(e){
        e.preventDefault();

        var url = '{{route("patient.findDoctors")}}?';
        var name = $("#name").val();
        var specialization = $("#specialization").val();
        var district = $("#district").val(); // District added
        var sort = $("#sort").val();

        if (name != "") {
            url += 'name=' + encodeURIComponent(name) + '&';
        }

        if (specialization != "") {
            url += 'specialization=' + encodeURIComponent(specialization) + '&';
        }

        if (district != "") { // District condition added
            url += 'district=' + encodeURIComponent(district) + '&';
        }

        url += 'sort=' + sort;

        window.location.href = url;
    });

    $("#sort").change(function(){
        $("#searchForm").submit();
    });

    // Auto-submit for district filter
    $("#district").change(function(){
        $("#searchForm").submit();
    });

    // Save Doctor Modal Functionality
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

        // Set doctor data when modal is shown
        $('#saveDoctorModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const doctorId = button.data('doctor-id');
            const doctorName = button.data('doctor-name');
            
            $('#modal_doctor_id').val(doctorId);
            $('#doctorNameDisplay').text(doctorName);
        });

        // Reset modal when closed
        $('#saveDoctorModal').on('hidden.bs.modal', function() {
            $('#save_reason').val('');
            $('#charCount').text('0');
            $('input[name="save_category"][value="favorite"]').prop('checked', true);
            $('#confirmSaveDoctor').prop('disabled', false).html('<i class="fa fa-heart me-1"></i> Save Doctor');
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
    });
</script>

<style>
    .specialization-highlight {
        background-color: #e3f2fd;
        border: 1px solid #bbdefb;
        padding: 8px;
        border-radius: 5px;
        margin: 5px 0;
        text-align: left;
        font-size: 0.9em;
    }

    .qualification-highlight {
        background-color: #fff3e0;
        border: 1px solid #ffe0b2;
        padding: 8px;
        border-radius: 5px;
        margin: 5px 0;
        text-align: left;
        font-size: 0.9em;
    }

    .district-highlight {
        background-color: #e8f5e8;
        border: 1px solid #c8e6c9;
        padding: 8px;
        border-radius: 5px;
        margin: 5px 0;
        text-align: left;
        font-size: 0.9em;
    }

    .doctor_listing_area .card {
        transition: transform 0.2s ease-in-out;
    }

    .doctor_listing_area .card:hover {
        transform: translateY(-5px);
    }

    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #007bff;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    /* Modal Styles */
    .modal-header {
        background-color: #f8f9fa;
    }
    
    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endsection