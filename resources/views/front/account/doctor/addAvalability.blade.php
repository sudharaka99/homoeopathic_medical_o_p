@extends('front.layouts.app')

@section('main')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('front.account.doctor.slidebar')
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-calendar-plus text-primary me-2"></i>Manage Availability
                </h2>
                <button id="btnAddNewSlot" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                    <i class="fas fa-plus me-2"></i>Add New Slot
                </button>
            </div>

            @include('front.message')

            <!-- ========== TODAY'S AVAILABILITY ========== -->
            <div class="card border-0 shadow-sm mb-4" id="cardTodayAvailability">
                <div class="card-header bg-warning bg-opacity-10 border-warning">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-sun text-warning me-2"></i>
                        Today's Availability
                        <span class="badge bg-warning ms-3">{{ $todayAvailabilities->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($todayAvailabilities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Time Slot</th>
                                        <th>Duration</th>
                                        <th>Tokens</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAvailabilities as $slot)
                                    <tr id="today-row-{{ $slot->id }}">
                                        <td class="fw-bold">
                                            {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                        </td>
                                        <td>
                                            @php
                                                $duration = \Carbon\Carbon::parse($slot->start_time_slot)
                                                    ->diffInMinutes(\Carbon\Carbon::parse($slot->end_time_slot));
                                            @endphp
                                            <span class="text-muted">{{ $duration }} min</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $slot->number_of_tokens }}</span>
                                        </td>
                                        <td>
                                            @if($slot->status == 'available')
                                                <span class="badge bg-success">Available</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Booked</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                @if($slot->status == 'available')
                                                    <button class="btn btn-info" 
                                                            onclick="editSlot({{ $slot->id }})" 
                                                            title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-danger" 
                                                        onclick="confirmDelete({{ $slot->id }})" 
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-day fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No availability slots for today.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========== FUTURE AVAILABILITY ========== -->
            <div class="card border-0 shadow-sm mb-4" id="cardFutureAvailability">
                <div class="card-header bg-success bg-opacity-10 border-success">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-calendar-alt text-success me-2"></i>
                        Future Availability
                        <span class="badge bg-success ms-3">{{ $futureAvailabilities->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($futureAvailabilities->count() > 0)
                        <div class="accordion" id="accordionFuture">
                            @php
                                $futureGrouped = $futureAvailabilities->groupBy('date');
                            @endphp
                            @foreach($futureGrouped as $date => $slots)
                                @php
                                    $day = \Carbon\Carbon::parse($date);
                                    $isTomorrow = $day->isTomorrow();
                                @endphp
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header" id="heading-future-{{ $date }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} py-3" 
                                                type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse-future-{{ $date }}">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $day->format('F d, Y') }}</strong>
                                                    <span class="text-muted ms-2">({{ $day->format('l') }})</span>
                                                    @if($isTomorrow)
                                                        <span class="badge bg-info ms-2">Tomorrow</span>
                                                    @endif
                                                </div>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $slots->count() }} {{ Str::plural('slot', $slots->count()) }}
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-future-{{ $date }}" 
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         data-bs-parent="#accordionFuture">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Time Slot</th>
                                                            <th>Duration</th>
                                                            <th>Tokens</th>
                                                            <th>Status</th>
                                                            <th class="text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($slots as $slot)
                                                        <tr id="future-row-{{ $slot->id }}">
                                                            <td class="fw-bold">
                                                                {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }} - 
                                                                {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $duration = \Carbon\Carbon::parse($slot->start_time_slot)
                                                                        ->diffInMinutes(\Carbon\Carbon::parse($slot->end_time_slot));
                                                                @endphp
                                                                <span class="text-muted">{{ $duration }} min</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-secondary">{{ $slot->number_of_tokens }}</span>
                                                            </td>
                                                            <td>
                                                                @if($slot->status == 'available')
                                                                    <span class="badge bg-success">Available</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">Booked</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group btn-group-sm">
                                                                    @if($slot->status == 'available')
                                                                        <button class="btn btn-info" 
                                                                                onclick="editSlot({{ $slot->id }})" 
                                                                                title="Edit">
                                                                            <i class="bi bi-pencil-square"></i>
                                                                        </button>
                                                                    @endif
                                                                    <button class="btn btn-danger" 
                                                                            onclick="confirmDelete({{ $slot->id }})" 
                                                                            title="Delete">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No future availability slots.</p>
                            <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                                <i class="fas fa-plus me-1"></i>Add Slots
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========== PAST AVAILABILITY ========== -->
            <div class="card border-0 shadow-sm" id="cardPastAvailability">
                <div class="card-header bg-secondary bg-opacity-10 border-secondary">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Past Availability
                        <span class="badge bg-secondary ms-3">{{ $pastAvailabilities->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($pastAvailabilities->count() > 0)
                        <div class="accordion" id="accordionPast">
                            @php
                                // Group by year-month for better organization
                                $pastGrouped = $pastAvailabilities->groupBy(function($item) {
                                    return \Carbon\Carbon::parse($item->date)->format('Y-m'); // 2024-01, 2024-02, etc.
                                });
                            @endphp
                            
                            @foreach($pastGrouped as $month => $monthSlots)
                                @php
                                    $monthName = \Carbon\Carbon::parse($month . '-01')->format('F Y');
                                    $monthSlotsGrouped = $monthSlots->groupBy('date');
                                    $totalSlotsInMonth = $monthSlots->count();
                                    $totalDaysInMonth = $monthSlotsGrouped->count();
                                @endphp
                                
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header" id="heading-past-{{ $month }}">
                                        <button class="accordion-button collapsed text-muted py-3" 
                                                type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse-past-{{ $month }}">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $monthName }}</strong>
                                                    <span class="text-muted ms-2">({{ $totalSlotsInMonth }} slots across {{ $totalDaysInMonth }} days)</span>
                                                </div>
                                                <div>
                                                    <span class="badge bg-dark rounded-pill me-2">
                                                        {{ $totalDaysInMonth }} {{ Str::plural('day', $totalDaysInMonth) }}
                                                    </span>
                                                    <i class="fas fa-chevron-down"></i>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-past-{{ $month }}" 
                                         class="accordion-collapse collapse"
                                         data-bs-parent="#accordionPast">
                                        <div class="accordion-body p-0">
                                            <div class="accordion" id="innerAccordionPast-{{ $month }}">
                                                @foreach($monthSlotsGrouped as $date => $slots)
                                                    @php
                                                        $day = \Carbon\Carbon::parse($date);
                                                        $dayName = $day->format('l');
                                                        $totalBooked = $slots->where('status', 'booked')->count();
                                                        $totalAvailable = $slots->where('status', 'available')->count();
                                                    @endphp
                                                    <div class="accordion-item border-0 border-bottom">
                                                        <h3 class="accordion-header" id="inner-heading-{{ $date }}">
                                                            <button class="accordion-button collapsed py-2" 
                                                                    type="button" data-bs-toggle="collapse" 
                                                                    data-bs-target="#inner-collapse-{{ $date }}">
                                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong>{{ $day->format('d') }}</strong>
                                                                        <span class="text-muted ms-2">{{ $dayName }}</span>
                                                                        @if($totalBooked > 0)
                                                                            <span class="badge bg-success ms-2">{{ $totalBooked }} booked</span>
                                                                        @endif
                                                                        @if($totalAvailable > 0)
                                                                            <span class="badge bg-secondary ms-1">{{ $totalAvailable }} available</span>
                                                                        @endif
                                                                    </div>
                                                                    <span class="badge bg-primary rounded-pill">
                                                                        {{ $slots->count() }} {{ Str::plural('slot', $slots->count()) }}
                                                                    </span>
                                                                </div>
                                                            </button>
                                                        </h3>
                                                        <div id="inner-collapse-{{ $date }}" 
                                                             class="accordion-collapse collapse"
                                                             data-bs-parent="#innerAccordionPast-{{ $month }}">
                                                            <div class="accordion-body p-3">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-hover mb-0">
                                                                        <thead>
                                                                            <tr class="table-secondary">
                                                                                <th>Time Slot</th>
                                                                                <th>Duration</th>
                                                                                <th>Tokens</th>
                                                                                <th>Status</th>
                                                                                <th>Patient</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($slots as $slot)
                                                                            <tr id="past-row-{{ $slot->id }}">
                                                                                <td>
                                                                                    {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }} - 
                                                                                    {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                                                                </td>
                                                                                <td>
                                                                                    @php
                                                                                        $duration = \Carbon\Carbon::parse($slot->start_time_slot)
                                                                                            ->diffInMinutes(\Carbon\Carbon::parse($slot->end_time_slot));
                                                                                    @endphp
                                                                                    {{ $duration }} min
                                                                                </td>
                                                                                <td><span class="badge bg-dark">{{ $slot->number_of_tokens }}</span></td>
                                                                                <td>
                                                                                    @if($slot->status == 'available')
                                                                                        <span class="badge bg-success">Available</span>
                                                                                    @else
                                                                                        <span class="badge bg-warning text-dark">Booked</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if($slot->status == 'booked' && $slot->patient_name)
                                                                                        <small class="text-primary fw-semibold">{{ $slot->patient_name }}</small>
                                                                                    @else
                                                                                        <span class="text-muted">—</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-check fa-2x opacity-50 mb-2"></i>
                            <p class="mb-0">No past availability records.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Availability Modal -->
<div class="modal fade" id="addAvailabilityModal" tabindex="-1" aria-labelledby="addAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAvailabilityModalLabel">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Add Availability Slot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('doctor.availability.store') }}" method="POST" id="addAvailabilityForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="number_of_tokens" class="form-label">Tokens <span class="text-danger">*</span></label>
                            <input type="number" id="number_of_tokens" name="number_of_tokens" class="form-control" min="1" max="10" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="start_time_slot" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" id="start_time_slot" name="start_time_slot" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time_slot" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" id="end_time_slot" name="end_time_slot" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any special notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Availability Modal -->
<div class="modal fade" id="editAvailabilityModal" tabindex="-1" aria-labelledby="editAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAvailabilityModalLabel">
                    <i class="fas fa-edit text-warning me-2"></i>Edit Availability Slot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAvailabilityForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_slot_id" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="edit_date" name="date" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_number_of_tokens" class="form-label">Tokens <span class="text-danger">*</span></label>
                            <input type="number" id="edit_number_of_tokens" name="number_of_tokens" class="form-control" min="1" max="10" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_start_time_slot" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_start_time_slot" name="start_time_slot" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_end_time_slot" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_end_time_slot" name="end_time_slot" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Update Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script>
// Delete Confirmation
function confirmDelete(slotId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This slot will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('doctor.availability.delete', ':id') }}".replace(':id', slotId);
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            form.submit();
        }
    });
}

// Edit Slot
function editSlot(slotId) {
    Swal.fire({ title: 'Loading slot...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    fetch(`/doctor/get-availability/${slotId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.ok ? r.json() : Promise.reject(r))
    .then(data => {
        Swal.close();
        if (data.success && data.slot) {
            const s = data.slot;
            document.getElementById('edit_slot_id').value = s.id;
            document.getElementById('edit_date').value = s.date;
            document.getElementById('edit_start_time_slot').value = s.start_time_slot?.slice(0,5) || '';
            document.getElementById('edit_end_time_slot').value = s.end_time_slot?.slice(0,5) || '';
            document.getElementById('edit_number_of_tokens').value = s.number_of_tokens;
            document.getElementById('edit_notes').value = s.notes || '';
            document.getElementById('editAvailabilityForm').action = `{{ route('doctor.availability.update', ':id') }}`.replace(':id', s.id);
            new bootstrap.Modal(document.getElementById('editAvailabilityModal')).show();
        } else {
            Swal.fire('Error', data.message || 'Failed to load slot', 'error');
        }
    })
    .catch(() => {
        Swal.close();
        Swal.fire('Error', 'Could not load slot data', 'error');
    });
}

// Form Auto-fill & Validation
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    ['date', 'edit_date'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.min = today;
    });

    // Auto-set end time +1 hour
    ['start_time_slot', 'edit_start_time_slot'].forEach(id => {
        const start = document.getElementById(id);
        if (start) {
            start.addEventListener('change', function() {
                const [h, m] = this.value.split(':');
                let endH = parseInt(h) + 1;
                if (endH > 23) endH = 23;
                const endId = id === 'start_time_slot' ? 'end_time_slot' : 'edit_end_time_slot';
                const end = document.getElementById(endId);
                if (end) end.value = `${endH.toString().padStart(2,'0')}:${m}`;
            });
        }
    });

    // Validation
    ['addAvailabilityForm', 'editAvailabilityForm'].forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                const prefix = formId.includes('add') ? '' : 'edit_';
                const start = document.getElementById(prefix + 'start_time_slot').value;
                const end = document.getElementById(prefix + 'end_time_slot').value;
                const date = document.getElementById(prefix + 'date').value;

                if (date < today) {
                    e.preventDefault();
                    Swal.fire('Invalid Date', 'Please select today or a future date', 'error');
                } else if (start && end && start >= end) {
                    e.preventDefault();
                    Swal.fire('Invalid Time', 'End time must be after start time', 'error');
                }
            });
        }
    });

    // Success/Error Messages
    @if(session('success'))
        Swal.fire('Success!', '{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        Swal.fire('Error!', '{{ session('error') }}', 'error');
    @endif
});
</script>
@endsection