@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">    
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.doctor.slidebar')
            </div>
            
            <div class="col-lg-9">
                @include('front.message')
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-primary text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $availabilities->count() }}</h4>
                                        <small>Total Slots</small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-calendar fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-success text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $availabilities->where('status', 'available')->count() }}</h4>
                                        <small>Available</small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-warning text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $availabilities->where('status', 'booked')->count() }}</h4>
                                        <small>Booked</small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-users fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Availability Form -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-dark">
                            <i class="fa fa-plus-circle me-2 text-primary"></i>Add New Availability Slot
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('doctor.availability.store') }}" method="POST" id="availabilityForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label fw-bold">Select Date <span class="text-danger">*</span></label>
                                    <input type="date" id="date" name="date" class="form-control" 
                                           min="{{ date('Y-m-d') }}" 
                                           value="{{ old('date') }}" 
                                           required>
                                    <div class="form-text">Select a future date for your availability</div>
                                    @error('date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="number_of_tokens" class="form-label fw-bold">Number of Appointments <span class="text-danger">*</span></label>
                                    <input type="number" id="number_of_tokens" name="number_of_tokens" 
                                           class="form-control" min="1" max="10" 
                                           value="{{ old('number_of_tokens', 1) }}" 
                                           required>
                                    <div class="form-text">How many patients can book this time slot? (Max: 10)</div>
                                    @error('number_of_tokens')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_time_slot" class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" id="start_time_slot" name="start_time_slot" 
                                           class="form-control" 
                                           value="{{ old('start_time_slot') }}" 
                                           required>
                                    <div class="form-text">When does your consultation start?</div>
                                    @error('start_time_slot')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_time_slot" class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                                    <input type="time" id="end_time_slot" name="end_time_slot" 
                                           class="form-control" 
                                           value="{{ old('end_time_slot') }}" 
                                           required>
                                    <div class="form-text">When does your consultation end?</div>
                                    @error('end_time_slot')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label fw-bold">Additional Notes <span class="text-muted">(Optional)</span></label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                          placeholder="Any special instructions or notes for patients...">{{ old('notes') }}</textarea>
                                <div class="form-text">Max 500 characters</div>
                                @error('notes')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-add-slot">
                                    <i class="fa fa-calendar-plus me-2"></i> Add Availability Slot
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fa fa-refresh me-2"></i> Reset Form
                                </button>
                                <a href="{{ route('doctor.manageAvailability') }}" class="btn btn-outline-info">
                                    <i class="fa fa-list me-2"></i> Manage Slots
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Current Availability Slots -->
                @if($availabilities->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">
                            <i class="fa fa-calendar-check me-2 text-success"></i>Your Upcoming Availability
                        </h5>
                        <span class="badge bg-primary">{{ $availabilities->count() }} Slots</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Time Slot</th>
                                        <th>Duration</th>
                                        <th>Appointments</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availabilities as $slot)
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">
                                                {{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }}
                                                @if(\Carbon\Carbon::parse($slot->date)->isToday())
                                                    <span class="badge bg-info ms-1">Today</span>
                                                @elseif(\Carbon\Carbon::parse($slot->date)->isTomorrow())
                                                    <span class="badge bg-warning ms-1">Tomorrow</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }}
                                                </span>
                                                <span class="text-muted mx-1">to</span>
                                                <span class="badge bg-light text-dark border">
                                                    {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $start = \Carbon\Carbon::parse($slot->start_time_slot);
                                                    $end = \Carbon\Carbon::parse($slot->end_time_slot);
                                                    $duration = $start->diffInMinutes($end);
                                                @endphp
                                                <span class="text-muted">{{ $duration }} minutes</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fa fa-users me-1"></i> {{ $slot->number_of_tokens }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($slot->status == 'available')
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-check-circle me-1"></i> Available
                                                    </span>
                                                @elseif($slot->status == 'booked')
                                                    <span class="badge bg-warning">
                                                        <i class="fa fa-users me-1"></i> Booked
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fa fa-times-circle me-1"></i> Cancelled
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    @if($slot->status == 'available')
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="editSlot({{ $slot->id }})"
                                                                title="Edit Slot">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteSlot({{ $slot->id }})"
                                                            title="Delete Slot">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($slot->notes)
                                        <tr>
                                            <td colspan="6" class="bg-light">
                                                <small class="text-muted">
                                                    <i class="fa fa-sticky-note me-1"></i> 
                                                    <strong>Note:</strong> {{ $slot->notes }}
                                                </small>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <!-- Empty State -->
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Availability Slots Added</h4>
                        <p class="text-muted mb-4">You haven't added any availability slots yet. Start by adding your first slot above.</p>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('date').focus()">
                            <i class="fa fa-plus me-2"></i> Add Your First Slot
                        </button>
                    </div>
                </div>
                @endif

                <!-- Quick Tips -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <h6 class="text-dark mb-3">
                            <i class="fa fa-lightbulb me-2 text-warning"></i>Best Practices for Availability
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-muted">
                                    <li class="mb-2">
                                        <i class="fa fa-check-circle text-success me-2"></i> Add slots 1-2 weeks in advance
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-clock text-primary me-2"></i> Keep 15-30 min gaps between slots
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-users text-info me-2"></i> Adjust tokens based on consultation type
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-muted">
                                    <li class="mb-2">
                                        <i class="fa fa-bell text-warning me-2"></i> Update slots regularly
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-calendar text-success me-2"></i> Plan for holidays in advance
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-notes-medical text-danger me-2"></i> Add notes for special sessions
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i> Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this availability slot?</p>
                <p class="text-muted small">This action cannot be undone and may affect scheduled appointments.</p>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash me-2"></i> Delete Slot
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customCSS')
<style>
.stat-card {
    border-radius: 10px;
    border: none;
    transition: transform 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.card {
    border-radius: 10px;
    border: none;
}
.card-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #e9ecef;
}
.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}
.btn-add-slot {
    padding: 10px 20px;
}
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}
.form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.3s ease;
}
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}
</style>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#date').attr('min', today);

    // Auto-set end time when start time changes (1 hour duration by default)
    $('#start_time_slot').on('change', function() {
        const startTime = $(this).val();
        if (startTime) {
            const [hours, minutes] = startTime.split(':');
            let endHours = parseInt(hours) + 1;
            if (endHours > 23) endHours = 23;
            const endTime = `${endHours.toString().padStart(2, '0')}:${minutes}`;
            $('#end_time_slot').val(endTime);
        }
    });

    // Form validation
    $('#availabilityForm').on('submit', function(e) {
        const startTime = $('#start_time_slot').val();
        const endTime = $('#end_time_slot').val();
        const date = $('#date').val();
        
        if (date < today) {
            e.preventDefault();
            Swal.fire({
                title: 'Invalid Date!',
                text: 'Please select a future date.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
            });
            return;
        }
        
        if (startTime && endTime && startTime >= endTime) {
            e.preventDefault();
            Swal.fire({
                title: 'Invalid Time!',
                text: 'End time must be after start time.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        // Show loading state
        $('.btn-add-slot').html('<i class="fa fa-spinner fa-spin me-2"></i> Adding...').prop('disabled', true);
    });

    // Real-time validation for end time
    $('#end_time_slot').on('change', function() {
        const startTime = $('#start_time_slot').val();
        const endTime = $(this).val();
        
        if (startTime && endTime && startTime >= endTime) {
            $(this).addClass('is-invalid');
            $('#timeError').remove();
            $(this).after('<div class="invalid-feedback" id="timeError">End time must be after start time</div>');
        } else {
            $(this).removeClass('is-invalid');
            $('#timeError').remove();
        }
    });
});

function resetForm() {
    document.getElementById('availabilityForm').reset();
    $('#date').attr('min', new Date().toISOString().split('T')[0]);
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.btn-add-slot').html('<i class="fa fa-calendar-plus me-2"></i> Add Availability Slot').prop('disabled', false);
}

function deleteSlot(slotId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/doctor/availability/delete/${slotId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function editSlot(slotId) {
    // Redirect to edit page or show edit modal
    window.location.href = `/doctor/manage-availability?edit=${slotId}`;
}

// Quick time slot buttons
function addQuickSlot(hours) {
    const now = new Date();
    const startTime = `${now.getHours().toString().padStart(2, '0')}:00`;
    const endTime = `${(now.getHours() + hours).toString().padStart(2, '0')}:00`;
    
    $('#start_time_slot').val(startTime);
    $('#end_time_slot').val(endTime);
}
</script>
@endsection