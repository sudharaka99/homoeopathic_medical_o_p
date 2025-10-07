@extends('front.layouts.app')

@section('main')

<section class="section-4 bg-2">    
    <div class="container pt-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('CounselorShow')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Counselors</a></li>
                    </ol>
                </nav>
            </div>
        </div> 
    </div>
    <div class="container job_details_area">
        <div class="row pb-5">
            <div class="col-md-8">
            @include('front.message')

                <div class="card shadow border-0">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">
                                
                                <div class="jobs_conetent">
                                    <a href="#">
                                        <h4>{{$counselor->name}}</h4>
                                    </a>
                                    <div class="links_locat d-flex align-items-center">
                                        <div class="location">
                                            <p> <i class="fa fa-phone"></i> {{$counselor->mobile}}</p>
                                        </div>
                                        <div class="location">
                                            <p> <i class="fa fa-envelope"></i> {{$counselor->email}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap"> @if(!empty($counselor->counselorDetails->bio))
                            <div class="single_wrap">
                            <h4>counselor-description</h4>
                            {!! nl2br ($counselor->counselorDetails->bio) !!}                            </div>
                            @endif
                        </div>
                            @if(!empty($counselor->counselorDetails->qualification))
                            <div class="single_wrap">
                            <h4>Qualification</h4>
                            {!! nl2br ($counselor->counselorDetails->qualification) !!}
                            </div>
                            @endif
                       
                        
                            @if(!empty($counselor->counselorDetails->experience))
                            <div class="single_wrap">
                            <h4>Experience</h4>
                            {!! nl2br ($counselor->counselorDetails->experience) !!}
                            </div>
                            @endif

                        
                        
                            @if(!empty($counselor->counselorDetails->specialties))
                            <div class="single_wrap">
                            <h4>Specialties</h4>
                            {!! nl2br ($counselor->counselorDetails->specialties) !!}
                            </div>
                            @endif
                            <div class="border-bottom"></div>
                            <div class="pt-3 text-end">
                                @if(Auth::check())
                                    <a href="{{ route('appointments.showAvailability', ['id' => $counselor->id]) }}" class="btn btn-primary d-inline-block me-2">Book a Session</a>
                                    <a href="{{ route('viewSession', ['id' => $counselor->id]) }}" class="btn btn-primary d-inline-block">Session View</a>
                                    @else
                                    <a href="{{ route('login') }}" class="btn btn-primary d-inline-block me-2">Login to Book</a>
                                    <a href="{{ route('login') }}" class="btn btn-primary d-inline-block">Login to View Session</a>
                                @endif
                                <div id="booking-error" class="text-danger pt-2"></div> <!-- Error message for booking -->
                            </div>
                            
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="card shadow border-0">
                    <div class="job_sumary">
                        <div class="summery_header pb-1 pt-4">
                            <h3>Counselor Details</h3>
                        </div>
                        <div class="job_content pt-3">
                            @if(!empty($counselor->image))
                                <img src="{{ asset('profile_pic/thumb/' . $counselor->image) }}" alt="{{ $counselor->name }}'s Profile Image" class="img-fluid rounded-circle mb-3 mx-auto d-block" style="width: 150px;">
                            @else
                                <img src="{{ asset('assets/images/avatar7.png') }}" alt="Default Profile" class="img-fluid rounded-circle mb-3 mx-auto d-block" style="width: 150px;">
                            @endif
                        
                            <ul>
                                <li>Create account: <span>{{\Carbon\Carbon::parse($counselor->created_at)->format('d M, Y')}}</span></li>

                                @if(!empty($counselor->designation))
                                <li>Designation: <span>{{$counselor->designation}}</span></li>
                                @endif
                                @if(!empty($counselor->mobile))
                                <li>Mobile: <span>{{$counselor->mobile}}</span></li>
                                @endif
                                @if(!empty($counselor->email))
                                <li>Email: <span>{{$counselor->email}}</span></li>
                                @endif

                                
                            </ul>
                        </>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@section('customJS')

<script type="text/javascript">
</script>


@endsection
