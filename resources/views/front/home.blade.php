@extends('front.layouts.app')

@section('main')

{{-- Hero Section --}}
<section 
  class="section-0 lazy d-flex bg-image-style dark align-items-center" 
  data-bg="{{ asset('assets/images/Homeopathy-Medicine.jpg') }}"
  style="min-height: 90vh; background-size: cover; background-position: center; color: #fff;"
>
  <div class="container text-start">
    <div class="row">
      <div class="col-12 col-xl-8">
        <h1 class="display-4 fw-bold">Your Trusted Homoeopathic Care, Online</h1>
        <p class="lead mt-3">
          Book appointments, manage patient records, and share feedback — all in one place.
        </p>
        <div class="banner-btn mt-5">
          <a href="#appointments" class="btn btn-primary btn-lg me-3">Book Appointment</a>
          <a href="#doctors" class="btn btn-outline-light btn-lg">Find a Doctor</a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Doctor Search Section --}}
<section class="section-1 py-5"> 
  <div class="container">
    <div class="card border-0 shadow p-5">
      <form action="{{ route('home') }}" method="GET">
        <div class="row g-3">
          {{-- Keyword / Doctor Name --}}
          <div class="col-md-3">
            <input 
              type="text" 
              class="form-control" 
              name="keyword" 
              placeholder="Doctor name or keyword" 
              value="{{ request('keyword') }}">
          </div>

          {{-- Specialization --}}
          <div class="col-md-3">
            <select name="specialization" class="form-control">
              <option value="">Select Specialization</option>
              @foreach($specializations as $spec)
                <option value="{{ $spec->id }}" {{ request('specialization') == $spec->id ? 'selected' : '' }}>
                  {{ $spec->specialization_name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Clinic / District --}}
          <div class="col-md-3">
            <input 
              type="text" 
              class="form-control" 
              name="district" 
              placeholder="Clinic or District" 
              value="{{ request('district') }}">
          </div>

          {{-- Search Button --}}
          <div class="col-md-3">
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-search"></i> Search
              </button>
            </div>
          </div>
        </div> 
      </form>           
    </div>
  </div>
</section>

{{-- Popular Specializations --}}
<section class="section-2 bg-2 py-5" id="specializations">
  <div class="container">
    <h2 class="fw-bold mb-4">Popular Specializations</h2>
    <div class="row pt-3">
      @if ($specializations->isNotEmpty())
        @foreach($specializations as $spec)
          <div class="col-lg-4 col-xl-3 col-md-6 mb-4">
            <div class="single_catagory text-center p-4 shadow-sm rounded bg-white">
              <h4 class="pb-2">{{ $spec->specialization_name }}</h4>
              <p class="mb-0 text-muted"> 
                <span class="fw-bold text-primary">{{ $spec->doctor_count }}</span> Available Doctors
              </p>
            </div>
          </div>
        @endforeach
      @else
        <p>No specializations found.</p>
      @endif
    </div>
  </div>
</section>

{{-- Featured Doctors --}}
<section class="section-3 py-5" id="doctors">
  <div class="container">
    <h2 class="fw-bold mb-4">Featured Doctors</h2>
    <div class="row pt-3">
      @if($featuredDoctors->isNotEmpty())
        @foreach ($featuredDoctors as $doctor)
          <div class="col-md-4">
            <div class="card border-0 p-3 shadow mb-4">
              <div class="card-body text-center">
                <img 
                  src="{{ $doctor->image ? asset($doctor->image) : asset('assets/images/doctor.png') }}" 
                  class="rounded-circle mb-3" width="90" height="90" alt="Doctor Image">

                <h4 class="mb-1">{{ $doctor->doctor_name }}</h4>
                <p class="text-muted mb-1">{{ $doctor->specialization_name }}</p>
                <p class="small mb-2">
                  <strong>Clinic:</strong> {{ $doctor->clinic_name ?? 'N/A' }}
                </p>
                <p class="small text-success mb-2">{{ $doctor->years_experience }} years experience</p>

                {{-- ✅ View profile route fixed --}}
                <a href="{{ route('doctor.details', $doctor->id) }}" class="btn btn-primary btn-sm">
                  View Profile
                </a>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <p>No featured doctors available right now.</p>
      @endif
    </div>
  </div>
</section>

{{-- Latest Doctors --}}
<section class="section-3 bg-2 py-5">
  <div class="container">
    <h2 class="fw-bold mb-4">Recently Joined Doctors</h2>
    <div class="row pt-3">
      @if($latestDoctors->isNotEmpty())
        @foreach ($latestDoctors as $doctor)
          <div class="col-md-4">
            <div class="card border-0 p-3 shadow mb-4">
              <div class="card-body text-center">
                <img 
                  src="{{ $doctor->image ? asset($doctor->image) : asset('assets/images/doctor.png') }}" 
                  class="rounded-circle mb-3" width="90" height="90" alt="Doctor Image">

                <h4 class="mb-1">{{ $doctor->doctor_name }}</h4>
                <p class="text-muted mb-1">{{ $doctor->specialization_name }}</p>
                <p class="small mb-2">
                  <strong>Clinic:</strong> {{ $doctor->clinic_name ?? 'N/A' }}
                </p>
                <p class="small text-success mb-2">{{ $doctor->years_experience }} years experience</p>

                {{-- ✅ View profile route fixed --}}
                <a href="{{ route('doctor.details', $doctor->id) }}" class="btn btn-outline-primary btn-sm">
                  View Profile
                </a>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <p>No new doctors registered yet.</p>
      @endif
    </div>
  </div>
</section>


@endsection
