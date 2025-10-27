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
          <a href="#doctors" class="btn btn-primary btn-lg me-3">Find Doctors</a>
          <a href="#specializations" class="btn btn-outline-light btn-lg">View Specializations</a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Doctor Search Section --}}
<section class="section-1 py-5"> 
  <div class="container">
    <div class="card border-0 shadow p-5">
      <form action="{{ route('home') }}" method="GET" id="searchForm">
        <div class="row g-3">
          {{-- Keyword / Doctor Name --}}
          <div class="col-md-4">
            <label class="form-label">Search Doctors</label>
            <input 
              type="text" 
              class="form-control" 
              name="keyword" 
              id="keyword"
              placeholder="Doctor name, specialization or clinic..." 
              value="{{ request('keyword') }}"
              autocomplete="off">
          </div>

          {{-- Specialization --}}
          <div class="col-md-4">
            <label class="form-label">Specialization</label>
            <select name="specialization" class="form-control" id="specialization">
              <option value="">All Specializations</option>
              @foreach($specializations as $spec)
                <option value="{{ $spec->id }}" {{ request('specialization') == $spec->id ? 'selected' : '' }}>
                  {{ $spec->specialization_name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- District --}}
          <div class="col-md-4">
            <label class="form-label">District</label>
            <input 
              type="text" 
              class="form-control" 
              name="district" 
              id="district"
              placeholder="Enter district name..." 
              value="{{ request('district') }}"
              list="districtSuggestions"
              autocomplete="off">
            <datalist id="districtSuggestions">
              @foreach($availableDistricts as $district)
                <option value="{{ $district }}">
              @endforeach
            </datalist>
          </div>
        </div>

        {{-- Active Filters Display --}}
        @if(request()->hasAny(['keyword', 'specialization', 'district']))
        <div class="row mt-3">
          <div class="col-12">
            <div class="alert alert-info py-2">
              <small class="fw-bold">Active Filters:</small>
              @if(request('keyword'))
                <span class="badge bg-primary me-1">Search: {{ request('keyword') }}</span>
              @endif
              @if(request('specialization'))
                @php
                  $selectedSpec = $specializations->firstWhere('id', request('specialization'));
                @endphp
                @if($selectedSpec)
                  <span class="badge bg-success me-1">Specialization: {{ $selectedSpec->specialization_name }}</span>
                @endif
              @endif
              @if(request('district'))
                <span class="badge bg-warning me-1">District: {{ request('district') }}</span>
              @endif
              <a href="{{ route('home') }}" class="badge bg-danger text-decoration-none">Clear All</a>
            </div>
          </div>
        </div>
        @endif
      </form>           
    </div>
  </div>
</section>

{{-- Rest of your sections remain the same --}}
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Featured Doctors</h2>
      <small class="text-muted">Showing {{ $featuredDoctors->count() }} doctors</small>
    </div>
    <div class="row pt-3">
      @if($featuredDoctors->isNotEmpty())
        @foreach ($featuredDoctors as $doctor)
          <div class="col-md-4 mb-4">
            <div class="card border-0 p-3 shadow h-100">
              <div class="card-body text-center">
                <img 
                  src="{{ $doctor->image ? asset('storage/'.$doctor->image) : asset('assets/images/doctor.png') }}" 
                  class="rounded-circle mb-3" width="90" height="90" alt="Doctor Image">

                <h5 class="mb-1">{{ $doctor->doctor_name }}</h5>
                <p class="text-muted mb-1">{{ $doctor->specialization_name }}</p>
                <p class="small mb-1">
                  <i class="fas fa-map-marker-alt text-primary"></i> 
                  {{ $doctor->district ?? 'N/A' }}
                </p>
                <p class="small mb-2">
                  <strong>Clinic:</strong> {{ $doctor->clinic_name ?? 'N/A' }}
                </p>
                <p class="small text-success mb-2">
                  <i class="fas fa-award"></i> {{ $doctor->years_experience }} years experience
                </p>

                <a href="{{ route('doctor.details', $doctor->id) }}" class="btn btn-primary btn-sm">
                  View Profile
                </a>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div class="col-12 text-center py-5">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <h4 class="text-muted">No doctors found</h4>
          <p class="text-muted">Try adjusting your search filters</p>
          <a href="{{ route('home') }}" class="btn btn-primary">Clear Filters</a>
        </div>
      @endif
    </div>
  </div>
</section>

{{-- Latest Doctors --}}
<section class="section-3 bg-2 py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Recently Joined Doctors</h2>
      <small class="text-muted">Showing {{ $latestDoctors->count() }} doctors</small>
    </div>
    <div class="row pt-3">
      @if($latestDoctors->isNotEmpty())
        @foreach ($latestDoctors as $doctor)
          <div class="col-md-4 mb-4">
            <div class="card border-0 p-3 shadow h-100">
              <div class="card-body text-center">
                <img 
                  src="{{ $doctor->image ? asset('storage/'.$doctor->image) : asset('assets/images/doctor.png') }}" 
                  class="rounded-circle mb-3" width="90" height="90" alt="Doctor Image">

                <h5 class="mb-1">{{ $doctor->doctor_name }}</h5>
                <p class="text-muted mb-1">{{ $doctor->specialization_name }}</p>
                <p class="small mb-1">
                  <i class="fas fa-map-marker-alt text-primary"></i> 
                  {{ $doctor->district ?? 'N/A' }}
                </p>
                <p class="small mb-2">
                  <strong>Clinic:</strong> {{ $doctor->clinic_name ?? 'N/A' }}
                </p>
                <p class="small text-success mb-2">
                  <i class="fas fa-award"></i> {{ $doctor->years_experience }} years experience
                </p>

                <a href="{{ route('doctor.details', $doctor->id) }}" class="btn btn-outline-primary btn-sm">
                  View Profile
                </a>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div class="col-12 text-center py-5">
          <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
          <h4 class="text-muted">No recent doctors</h4>
          <p class="text-muted">Check back later for new doctor registrations</p>
        </div>
      @endif
    </div>
  </div>
</section>

@endsection

@section('customJS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;
    
    // Auto-submit for all inputs
    const inputs = ['keyword', 'specialization', 'district'];
    
    inputs.forEach(inputId => {
        const element = document.getElementById(inputId);
        
        if (element.tagName === 'SELECT') {
            // For select dropdowns - submit immediately
            element.addEventListener('change', function() {
                searchForm.submit();
            });
        } else {
            // For text inputs - debounced submit
            element.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.trim().length >= 2 || this.value.trim().length === 0) {
                        searchForm.submit();
                    }
                }, 500); // Wait 500ms after typing stops
            });
        }
    });
    
    // Also submit when user presses Enter in any field
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });
    });
});
</script>

<style>
.single_catagory {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.single_catagory:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
}
</style>
@endsection