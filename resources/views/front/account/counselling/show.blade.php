@extends('front.layouts.app')

@section('main')

<section class="section-3 py-5 bg-2">
    <div class="container">     
        <div class="row">
            <div class="col-6 col-md-10 ">
                <h2>All Counselors</h2>  
            </div>
            <div class="col-6 col-md-2">
                <div class="align-end">
                    <select name="sort" id="sort" class="form-control">
                        <option value="1" {{ (Request::get('sort') == '1') ? 'selected' : '' }}>Latest</option>
                        <option value="0" {{ (Request::get('sort') == '0') ? 'selected' : '' }}>Oldest</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row pt-5">
            <div class="col-md-4 col-lg-3 sidebar mb-4">
                <form action="" name="searchForm" id="searchForm">
                    <div class="card border-0 shadow p-4">
                        <div class="mb-4">
                            <h2>Search by Name</h2>
                            <input value="{{ Request::get('name') }}" type="text" name="name" id="name" placeholder="Counselor Name" class="form-control">
                        </div>

                        <div class="mb-4">
                            <h2>Qualification</h2>
                            <input value="{{ Request::get('qualification') }}" type="text" name="qualification" id="qualification" placeholder="Qualification" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('CounselorShow') }}" class="btn btn-secondary mt-3">Reset</a>
                    </div>
                </form>
            </div>
            <div class="col-md-8 col-lg-9 ">
                <div class="counselor_listing_area">                    
                    <div class="counselor_lists">
                        <div class="row">

                        @if($counselors->isNotEmpty())
                            @foreach ($counselors as $counselor)
                            <div class="col-md-4">
                                <div class="card border-0 p-3 shadow mb-4">
                                    <div class="card-body text-center">
                                        @if($counselor->image)
                                            <img src="{{ asset('profile_pic/thumb/' . $counselor->image) }}" alt="{{ $counselor->name }}'s Profile Image" class="img-fluid rounded-circle mb-3" style="width: 150px;">
                                        @else
                                            <img src="{{ asset('assets/images/avatar7.png') }}" alt="Default Profile" class="img-fluid rounded-circle mb-3" style="width: 150px;">
                                        @endif
                                        
                                        <h3 class="border-0 fs-5 pb-2 mb-0">{{ $counselor->name }}</h3>
                                        
                                        <!-- Highlighting bio with word limit -->
                                        <div class="bio-highlight">
                                            <strong>Bio:</strong> {!! nl2br(Str::words(strip_tags($counselor->counselorDetails->bio ?? 'Bio not available.'), 10, '...')) !!}
                                        </div>
                                        
                                        <!-- Highlighting qualifications with word limit -->
                                        <div class="qualification-highlight">
                                            <strong>Qualifications:</strong> {!! nl2br(Str::words(strip_tags($counselor->counselorDetails->qualification ?? 'Qualifications not available.'), 10, '...')) !!}
                                        </div>

                                        <div class="d-grid mt-3">
                                            <a href="{{route('CounselorDetails',['id' =>$counselor->id]) }})  }}" class="btn btn-primary btn-lg">View Profile</a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div class="col-md-12">
                                {{-- {{ $counselor->withQueryString()->links() }} --}}
                            </div>
                        @else
                            <div class="col-md-12">Counselors not found</div>
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

        var url = '{{ route("CounselorShow") }}?';
        var name = $("#name").val();
        var qualification = $("#qualification").val();
        var sort = $("#sort").val();

        if (name != "") {
            url += 'name=' + encodeURIComponent(name) + '&';
        }

        if (qualification != "") {
            url += 'qualification=' + encodeURIComponent(qualification) + '&';
        }

        url += 'sort=' + sort;

        window.location.href = url;
    });

    $("#sort").change(function(){
        $("#searchForm").submit();
    });
</script>
@endsection

<style>
    .bio-highlight {
        background-color: #e9f7fe; /* Light blue background */
        border: 1px solid #aeeeee; /* Border color */
        padding: 5px; /* Reduced padding */
        border-radius: 5px;
        margin: 5px 0; /* Reduced margin */
        text-align: left; /* Align text to the left */
        font-size: 0.9em; /* Smaller font size */
    }

    .qualification-highlight {
        background-color: #fff3cd; /* Light yellow background */
        border: 1px solid #ffeeba; /* Border color */
        padding: 5px; /* Reduced padding */
        border-radius: 5px;
        margin: 5px 0; /* Reduced margin */
        text-align: left; /* Align text to the left */
        font-size: 0.9em; /* Smaller font size */
    }
</style>
