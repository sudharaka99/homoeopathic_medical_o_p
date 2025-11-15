@extends('front.layouts.app')

@section('main')

{{-- Hero Section --}}
<section 
  class="section-0 lazy d-flex bg-image-style dark align-items-center" 
  data-bg="{{ asset('assets/images/Homeopathy-Medicine.jpg') }}"
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

        {{-- Message History Section --}}
        @auth
        <div class="mt-5">
          <div class="card border-0 shadow">
            <div class="card-header bg-light">
              <h4 class="mb-0"><i class="fas fa-history me-2"></i>Your Message History</h4>
            </div>
            <div class="card-body p-0">
              @if($contactMessages->count() > 0)
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead class="table-light">
                      <tr>
                        <th width="25%">Subject</th>
                        <th width="15%">Date</th>
                        <th width="15%">Status</th>
                        <th width="35%">Preview</th>
                        <th width="10%" class="text-center">View</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($contactMessages as $message)
                        <tr class="message-row">
                          <td>
                            <div class="d-flex align-items-center">
                              <i class="fas fa-envelope text-primary me-2"></i>
                              <strong>{{ Str::limit($message->subject, 20) }}</strong>
                            </div>
                          </td>
                          <td>
                            <small class="text-muted">
                              {{ \Carbon\Carbon::parse($message->created_at)->format('M j, Y') }}
                            </small>
                          </td>
                          <td>
                            <span class="badge 
                              @if($message->status == 'resolved') bg-success
                              @elseif($message->status == 'in_progress') bg-info
                              @else bg-secondary @endif">
                              {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                            </span>
                          </td>
                          <td>
                            <span class="text-muted">
                              {{ Str::limit($message->message, 40) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary view-message" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#messageDetails{{ $message->id }}"
                                    data-message-id="{{ $message->id }}">
                              <i class="fas fa-eye"></i>
                            </button>
                          </td>
                        </tr>
                        {{-- Expandable Details Row --}}
                        <tr class="detail-row">
                          <td colspan="5" class="p-0 border-0">
                            <div class="collapse" id="messageDetails{{ $message->id }}">
                              <div class="card-body bg-light border-top">
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="card border-0 h-100">
                                      <div class="card-header bg-white py-2">
                                        <h6 class="mb-0 text-primary">
                                          <i class="fas fa-user me-1"></i>Your Message
                                        </h6>
                                      </div>
                                      <div class="card-body">
                                        <p class="mb-2"><strong>Subject:</strong> {{ $message->subject }}</p>
                                        <p class="mb-2"><strong>Sent:</strong> 
                                          {{ \Carbon\Carbon::parse($message->created_at)->format('M j, Y \a\t g:i A') }}
                                        </p>
                                        <div class="message-content mt-3 p-3 bg-white rounded border">
                                          <p class="mb-0">{{ $message->message }}</p>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="card border-0 h-100">
                                      <div class="card-header bg-white py-2">
                                        <h6 class="mb-0 text-success">
                                          <i class="fas fa-user-shield me-1"></i>Admin Response
                                        </h6>
                                      </div>
                                      <div class="card-body">
                                        @if($message->reply_message)
                                          <p class="mb-2"><strong>Replied:</strong> 
                                            @if($message->replied_at)
                                              {{ \Carbon\Carbon::parse($message->replied_at)->format('M j, Y \a\t g:i A') }}
                                            @else
                                              <span class="text-muted">Not specified</span>
                                            @endif
                                          </p>
                                          @if($message->replied_by && $message->admin)
                                            <p class="mb-2"><strong>By:</strong> {{ $message->admin->name }}</p>
                                          @endif
                                          <div class="reply-content mt-3 p-3 bg-success bg-opacity-10 rounded border border-success">
                                            <p class="mb-0">{{ $message->reply_message }}</p>
                                          </div>
                                        @else
                                          <div class="text-center py-4">
                                            <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No response yet. We'll get back to you soon.</p>
                                          </div>
                                        @endif
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                {{-- Pagination --}}
                {{-- @if($contactMessages->hasPages())
                  <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                      <small class="text-muted">
                        Showing {{ $contactMessages->firstItem() }} to {{ $contactMessages->lastItem() }} of {{ $contactMessages->total() }} messages
                      </small>
                      {{ $contactMessages->links() }}
                    </div>
                  </div>
                @endif --}}
              @else
                <div class="text-center py-5">
                  <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                  <h5>No Messages Yet</h5>
                  <p class="text-muted">You haven't sent any messages yet.</p>
                </div>
              @endif
            </div>
          </div>
        </div>
        @endauth
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
            <h2 class="accordion-header" id="faqHeading{{ $i+1 }}">
              <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" 
                      type="button" 
                      data-bs-toggle="collapse" 
                      data-bs-target="#faqCollapse{{ $i+1 }}" 
                      aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" 
                      aria-controls="faqCollapse{{ $i+1 }}">
                {{ $faq[0] }}
              </button>
            </h2>
            <div id="faqCollapse{{ $i+1 }}" 
                 class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" 
                 aria-labelledby="faqHeading{{ $i+1 }}" 
                 data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                {!! $faq[1] !!}
              </div>
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

    // Message History Table Functionality
    const viewButtons = document.querySelectorAll('.view-message');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const messageId = this.getAttribute('data-message-id');
            const target = this.getAttribute('data-bs-target');
            const collapseElement = document.querySelector(target);
            
            // Toggle button text and icon
            if (collapseElement.classList.contains('show')) {
                this.innerHTML = '<i class="fas fa-eye"></i>';
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            } else {
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            }
        });
    });

    // Auto-close other expanded rows when opening a new one
    document.addEventListener('show.bs.collapse', function (e) {
        const allCollapses = document.querySelectorAll('.collapse');
        allCollapses.forEach(collapse => {
            if (collapse !== e.target && collapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(collapse, {
                    toggle: false
                });
                bsCollapse.hide();
                
                // Reset button states
                const button = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                if (button) {
                    button.innerHTML = '<i class="fas fa-eye"></i>';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-outline-primary');
                }
            }
        });
    });

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
        }).then((result) => {
            if (result.isConfirmed || result.isDismissed) {
                // Refresh page to show updated message history
                location.reload();
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
            } else {
                throw new Error(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            if (error.errors) {
                showValidationErrors(error.errors);
                
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

/* Message History Table Styles */
.message-row:hover {
    background-color: #f8f9fa !important;
}

.detail-row {
    background-color: transparent !important;
}

.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.message-content, .reply-content {
    font-size: 0.9rem;
    line-height: 1.5;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

/* Responsive design */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table th, .table td {
        padding: 0.5rem 0.25rem;
    }
    
    .btn-sm {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    .message-content, .reply-content {
        font-size: 0.8rem;
    }
    
    .card-body .row {
        flex-direction: column;
    }
    
    .card-body .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection