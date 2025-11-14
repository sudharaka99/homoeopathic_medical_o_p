@extends('front.layouts.app')

@section('main')

{{-- Hero Section --}}
<section 
  class="section-0 lazy d-flex bg-image-style dark align-items-center" 
  data-bg="{{ asset('assets/images/contact-hero-bg.jpg') }}"
  style="min-height: 50vh; background-size: cover; background-position: center; color: #fff;"
>
  <div class="container text-center">
    <div class="row">
      <div class="col-12">
        <h1 class="display-4 fw-bold">Get In Touch With Us</h1>
        <p class="lead mt-3">
          We're here to help and answer any questions you might have.
          @auth
          <br><small>Welcome back, {{ Auth::user()->name }}! Your details are pre-filled below.</small>
          @endauth
        </p>
      </div>
    </div>
  </div>
</section>

{{-- Contact Form & Info Section --}}
<section class="section-1 py-5 bg-light">
  <div class="container">
    @include('front.message')

    <div class="row g-5">
      {{-- Contact Form --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Send Us a Message</h3>
            @auth
            <span class="badge bg-success"><i class="fas fa-user me-1"></i>Logged In</span>
            @else
            <span class="badge bg-warning"><i class="fas fa-user me-1"></i>Guest</span>
            @endauth
          </div>
          
          <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
            @csrf

            <div class="row g-3">
              {{-- Name --}}
              <div class="col-md-6">
                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control @error('name') is-invalid @enderror" 
                  id="name" 
                  name="name"
                  value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}"
                  placeholder="Enter your full name"
                  required
                  {{ auth()->check() ? 'readonly' : '' }}>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @auth
                <div class="form-text text-info"><i class="fas fa-info-circle me-1"></i>Pre-filled from profile</div>
                @endauth
              </div>

              {{-- Email --}}
              <div class="col-md-6">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input 
                  type="email" 
                  class="form-control @error('email') is-invalid @enderror" 
                  id="email" 
                  name="email"
                  value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                  placeholder="Enter your email"
                  required
                  {{ auth()->check() ? 'readonly' : '' }}>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @auth
                <div class="form-text text-info"><i class="fas fa-info-circle me-1"></i>Pre-filled from profile</div>
                @endauth
              </div>

              {{-- Phone --}}
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <input 
                  type="tel" 
                  class="form-control @error('phone') is-invalid @enderror" 
                  id="phone" 
                  name="phone"
                  value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}"
                  placeholder="076 637 9328">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @auth
                @if(auth()->user()->phone)
                <div class="form-text text-info"><i class="fas fa-info-circle me-1"></i>Pre-filled from profile</div>
                @endif
                @endauth
              </div>

              {{-- Subject --}}
              <div class="col-md-6">
                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                <select class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                  <option value="">Select a subject</option>
                  @foreach(['General Inquiry','Appointment Help','Technical Support','Feedback','Other'] as $opt)
                  <option value="{{ $opt }}" {{ old('subject') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                  @endforeach
                </select>
                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              {{-- Message - MIN 3 CHARS --}}
              <div class="col-12">
                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea 
                  class="form-control @error('message') is-invalid @enderror" 
                  id="message" 
                  name="message"
                  rows="6"
                  placeholder="Please describe your inquiry in detail (at least 3 characters)..."
                  required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                
                <div class="form-text text-end">
                  <span id="charCount">0</span> / 1000 characters 
                  <small class="text-muted">(min 3)</small>
                </div>
              </div>

              {{-- Submit --}}
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                  <i class="fas fa-paper-plane me-2"></i>
                  @auth Send as {{ Auth::user()->name }} @else Send Message @endauth
                </button>
                <button type="reset" class="btn btn-outline-secondary btn-lg ms-2">
                  <i class="fas fa-redo me-2"></i>Reset
                </button>
                @guest
                <div class="mt-3">
                  <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    <a href="{{ route('login') }}">Login</a> to auto-fill details
                  </small>
                </div>
                @endguest
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Contact Info --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow p-4 h-100">
          <h3 class="mb-4">Contact Information</h3>
          
          @auth
          <div class="alert alert-success py-2 mb-4">
            <strong>{{ auth()->user()->name }}</strong><br>
            <small class="text-muted">{{ auth()->user()->email }}</small>
          </div>
          @endauth

          <div class="d-flex mb-4">
            <div class="flex-shrink-0"><i class="fas fa-phone text-primary fa-lg"></i></div>
            <div class="ms-3">
              <h5>Phone</h5>
              <p class="mb-0"><a href="tel:0766379328">076 637 9328</a></p>
            </div>
          </div>

          <div class="d-flex mb-4">
            <div class="flex-shrink-0"><i class="fas fa-envelope text-primary fa-lg"></i></div>
            <div class="ms-3">
              <h5>Email</h5>
              <p class="mb-0">
                <a href="mailto:info@homeocare.com">info@homeocare.com</a><br>
                <a href="mailto:support@homeocare.com">support@homeocare.com</a>
              </p>
            </div>
          </div>

          <div class="d-flex mb-4">
            <div class="flex-shrink-0"><i class="fas fa-clock text-primary fa-lg"></i></div>
            <div class="ms-3">
              <h5>Hours</h5>
              <p class="mb-0">24/7</p>
            </div>
          </div>

          <div class="d-flex">
            <div class="flex-shrink-0"><i class="fas fa-rocket text-primary fa-lg"></i></div>
            <div class="ms-3">
              <h5>Response</h5>
              <p class="mb-0">Within 24 Hours</p>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top">
            <h5 class="mb-3">Follow Us</h5>
            <div class="d-flex gap-3">
              <a href="#" class="text-primary fs-5"><i class="fab fa-facebook"></i></a>
              <a href="#" class="text-primary fs-5"><i class="fab fa-twitter"></i></a>
              <a href="#" class="text-primary fs-5"><i class="fab fa-instagram"></i></a>
              <a href="#" class="text-primary fs-5"><i class="fab fa-linkedin"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- FAQ Section --}}
<section class="section-3 py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Frequently Asked Questions</h2>
      <p class="text-muted">Quick answers to common questions</p>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion" id="faqAccordion">
          @php
            $faqs = [
              ['How quickly can I expect a response?', 'We respond within 24 hours. Call <strong>076 637 9328</strong> for urgent matters.'],
              ['Can I book appointments here?', 'Use our dedicated booking system for faster scheduling.'],
              ['Do you provide emergency services?', 'No. For emergencies, contact local services immediately.'],
              ['What should I include?', 'Name, contact, detailed inquiry. Avoid sensitive health data.']
            ];
          @endphp
          @foreach($faqs as $i => $faq)
          <div class="accordion-item border-0 shadow-sm mb-3">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i+1 }}">
                {{ $faq[0] }}
              </button>
            </h2>
            <div id="faq{{ $i+1 }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">{!! $faq[1] !!}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@section('customJS')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const message = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    // Character Counter
    const updateCharCount = () => {
        const len = message.value.length;
        charCount.textContent = len;

        const parent = charCount.parentElement;
        parent.classList.remove('text-danger', 'text-warning', 'text-success');
        
        if (len > 1000) {
            parent.classList.add('text-danger');
        } else if (len > 0 && len < 3) {
            parent.classList.add('text-warning');
        } else if (len >= 3) {
            parent.classList.add('text-success');
        }
    };
    message.addEventListener('input', updateCharCount);
    updateCharCount();

    // Phone Formatting
    const phone = document.getElementById('phone');
    if (phone) {
        phone.addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').substring(0, 10);
            if (v) v = v.match(/.{1,3}/g).join(' ');
            e.target.value = v;
        });
    }

    // Reset Button
    const resetBtn = document.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (@json(auth()->check())) {
                document.getElementById('subject').selectedIndex = 0;
                message.value = '';
                updateCharCount();
                document.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
            }
        });
    }

    // SweetAlert Functions
    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            confirmButtonColor: '#198754',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true,
            customClass: {
                popup: 'border-success'
            }
        });
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Try Again',
            customClass: {
                popup: 'border-danger'
            }
        });
    }

    function showValidationErrors(errors) {
        let errorMessages = '';
        
        if (typeof errors === 'object') {
            errorMessages = Object.values(errors).flat().join('<br>');
        } else {
            errorMessages = errors || 'Please check the form for errors.';
        }

        Swal.fire({
            icon: 'error',
            title: 'Validation Failed',
            html: errorMessages,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Fix Errors'
        });
    }

    // AJAX Form Submission
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const originalHTML = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                // If response is not OK, try to parse as JSON for validation errors
                return response.json().then(errorData => {
                    throw errorData;
                }).catch(() => {
                    throw new Error('Network response was not ok');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                
                // Reset form but preserve auth user data
                form.reset();
                updateCharCount();
                
                @auth
                document.getElementById('name').value = '{{ Auth::user()->name }}';
                document.getElementById('email').value = '{{ Auth::user()->email }}';
                @if(auth()->user()->phone)
                document.getElementById('phone').value = '{{ auth()->user()->phone }}';
                @endif
                @endauth
                
            } else {
                throw new Error(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            if (error.errors) {
                // Laravel validation errors
                showValidationErrors(error.errors);
                
                // Highlight fields with errors
                Object.keys(error.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = error.errors[field][0];
                        }
                    }
                });
            } else {
                // General error
                showError(error.message || 'Something went wrong. Please try again.');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        });
    });
});
</script>

<style>
.form-control[readonly] {
    background-color: #f8f9fa !important;
    opacity: 1;
}
.form-control[readonly]:focus {
    box-shadow: none;
    border-color: #ced4da;
}
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,.1) !important;
}
.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0d6efd;
}
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-success { color: #198754 !important; }
</style>
@endsection