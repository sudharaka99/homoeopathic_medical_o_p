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
                                                    <button onclick="saveDoctor({{ $doctor->id }})" class="btn btn-outline-primary mt-2" id="saveDoctorBtn-{{ $doctor->id }}">
                                                        <i class="fa fa-heart me-1"></i> Save Doctor
                                                    </button>
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

@endsection

@section('customJS')
<script>
    $("#searchForm").submit(function(e){
        e.preventDefault();

        var url = '{{route("patient.findDoctors")}}?';
        var name = $("#name").val();
        var specialization = $("#specialization").val();
        var sort = $("#sort").val();

        if (name != "") {
            url += 'name=' + encodeURIComponent(name) + '&';
        }

        if (specialization != "") {
            url += 'specialization=' + encodeURIComponent(specialization) + '&';
        }

        url += 'sort=' + sort;

        window.location.href = url;
    });

    $("#sort").change(function(){
        $("#searchForm").submit();
    });

    // Save Doctor Function
    function saveDoctor(doctorId) {
        const saveBtn = $(`#saveDoctorBtn-${doctorId}`);
        const saveIcon = saveBtn.find('.fa-heart');
        const saveText = saveBtn.find('span');

        saveIcon.addClass('fa-spin');
        saveBtn.prop('disabled', true);

        $.ajax({
            url: '{{ route("patient.saveDoctor") }}',
            type: 'POST',
            data: { 
                doctor_id: doctorId, 
                _token: '{{ csrf_token() }}' 
            },
            dataType: 'json',
            success: function(response) {
                saveIcon.removeClass('fa-spin fa-heart').addClass('fa-heart text-danger');
                saveText.text(' Saved');
                saveBtn.removeClass('btn-outline-primary').addClass('btn-success');
                alert(response.message || 'Doctor saved successfully!');
            },
            error: function(xhr) {
                saveIcon.removeClass('fa-spin');
                saveBtn.prop('disabled', false);
                alert(xhr.responseJSON?.message || 'Failed to save doctor. Please try again.');
            }
        });
    }
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
</style>
@endsection