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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                    <i class="fas fa-plus me-2"></i>Add New Slot
                </button>
            </div>

            @include('front.message')

            <!-- Availability List -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Your Availability Slots</h5>
                </div>
                <div class="card-body">
                    @if($availabilities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Slot</th>
                                        <th>Duration</th>
                                        <th>Tokens</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availabilities as $slot)
                                    <tr>
                                        <td class="fw-bold">
                                            {{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }}
                                            @if(\Carbon\Carbon::parse($slot->date)->isToday())
                                                <span class="badge bg-info ms-1">Today</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                        </td>
                                        <td>
                                            @php
                                                $start = \Carbon\Carbon::parse($slot->start_time_slot);
                                                $end = \Carbon\Carbon::parse($slot->end_time_slot);
                                                $duration = $start->diffInMinutes($end);
                                            @endphp
                                            {{ $duration }} min
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $slot->number_of_tokens }}</span>
                                        </td>
                                        <td>
                                            @if($slot->status == 'available')
                                                <span class="badge bg-success">Available</span>
                                            @else
                                                <span class="badge bg-warning">Booked</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                @if($slot->status == 'available')
                                                    <button class="btn btn-sm btn-info editRqnBtn" 
                                                            onclick="editSlot({{ $slot->id }})"
                                                            title="Edit Slot">
                                                        <i class="bi bi-pencil-square"></i> 
                                                    </button>
                                                @endif
                                                
                                                <button class="btn btn-sm btn-danger delete-btn" 
                                                        onclick="confirmDelete({{ $slot->id }})"
                                                        title="Delete Slot">
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
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Availability Slots</h5>
                            <p class="text-muted">You haven't added any availability slots yet.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                                <i class="fas fa-plus me-2"></i>Add Your First Slot
                            </button>
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
                    <i class="fas fa-plus-circle me-2 text-primary"></i>Add Availability Slot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('doctor.availability.store') }}" method="POST" id="addAvailabilityForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="number_of_tokens" class="form-label">Tokens <span class="text-danger">*</span></label>
                            <input type="number" id="number_of_tokens" name="number_of_tokens" 
                                   class="form-control" min="1" max="10" value="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time_slot" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" id="start_time_slot" name="start_time_slot" 
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time_slot" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" id="end_time_slot" name="end_time_slot" 
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Any special notes..."></textarea>
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
                    <i class="fas fa-edit me-2 text-warning"></i>Edit Availability Slot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAvailabilityForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_slot_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" id="edit_date" name="date" class="form-control" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_number_of_tokens" class="form-label">Tokens <span class="text-danger">*</span></label>
                            <input type="number" id="edit_number_of_tokens" name="number_of_tokens" 
                                   class="form-control" min="1" max="10" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_time_slot" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_start_time_slot" name="start_time_slot" 
                                   class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_time_slot" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" id="edit_end_time_slot" name="end_time_slot" 
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
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
// Delete Slot Function with SweetAlert
function confirmDelete(slotId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('doctor.availability.delete', ':id') }}".replace(':id', slotId);
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the slot.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the form
            form.submit();
        }
    });
}

// Edit Slot Function - Fetches data via AJAX
function editSlot(slotId) {
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        text: 'Please wait while we load slot data.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Fetch slot data via AJAX
    fetch(`/doctor/get-availability/${slotId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        Swal.close(); // Close loading dialog
        
        if (data.success && data.slot) {
            const slot = data.slot;
            
            console.log('Slot data loaded:', slot); // Debug log
            
            // Populate edit form with the fetched data
            document.getElementById('edit_slot_id').value = slot.id;
            document.getElementById('edit_date').value = slot.date;
            // Trim seconds if present (DB may store times as HH:MM:SS). HTML time inputs and server validation expect H:i (HH:MM).
            const startVal = slot.start_time_slot ? slot.start_time_slot.slice(0,5) : '';
            const endVal = slot.end_time_slot ? slot.end_time_slot.slice(0,5) : '';
            const editStartEl = document.getElementById('edit_start_time_slot');
            const editEndEl = document.getElementById('edit_end_time_slot');
            if (editStartEl) editStartEl.value = startVal;
            if (editEndEl) editEndEl.value = endVal;
            document.getElementById('edit_number_of_tokens').value = slot.number_of_tokens;
            document.getElementById('edit_notes').value = slot.notes || '';
            
            // Set form action for update
            const editForm = document.getElementById('editAvailabilityForm');
            const editUrl = "{{ route('doctor.availability.update', ':id') }}".replace(':id', slotId);
            editForm.action = editUrl;
            
            // Show edit modal
            const editModal = new bootstrap.Modal(document.getElementById('editAvailabilityModal'));
            editModal.show();
            
        } else {
            throw new Error(data.message || 'Failed to load slot data');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error loading slot data:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load slot data: ' + error.message,
            confirmButtonText: 'OK'
        });
    });
}

// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    const dateEl = document.getElementById('date');
    const editDateEl = document.getElementById('edit_date');
    if (dateEl) dateEl.min = today;
    if (editDateEl) editDateEl.min = today;

    // Auto-set end time when start time changes (for add form)
    const startEl = document.getElementById('start_time_slot');
    if (startEl) {
        startEl.addEventListener('change', function() {
            const startTime = this.value;
            if (startTime) {
                const [hours, minutes] = startTime.split(':');
                let endHours = parseInt(hours) + 1;
                if (endHours > 23) endHours = 23;
                const endTime = `${endHours.toString().padStart(2, '0')}:${minutes}`;
                const endEl = document.getElementById('end_time_slot');
                if (endEl) endEl.value = endTime;
            }
        });
    }

    // Auto-set end time when start time changes (for edit form)
    const editStartEl = document.getElementById('edit_start_time_slot');
    if (editStartEl) {
        editStartEl.addEventListener('change', function() {
            const startTime = this.value;
            if (startTime) {
                const [hours, minutes] = startTime.split(':');
                let endHours = parseInt(hours) + 1;
                if (endHours > 23) endHours = 23;
                const endTime = `${endHours.toString().padStart(2, '0')}:${minutes}`;
                const editEndEl = document.getElementById('edit_end_time_slot');
                if (editEndEl) editEndEl.value = endTime;
            }
        });
    }

    // Add form validation
    document.getElementById('addAvailabilityForm').addEventListener('submit', function(e) {
        const startTime = document.getElementById('start_time_slot').value;
        const endTime = document.getElementById('end_time_slot').value;
        const date = document.getElementById('date').value;
        
        if (date < today) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: 'Please select a future date'
            });
            return;
        }
        
        if (startTime && endTime && startTime >= endTime) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Time',
                text: 'End time must be after start time'
            });
            return;
        }
    });

    // Edit form validation
    document.getElementById('editAvailabilityForm').addEventListener('submit', function(e) {
        const startTime = document.getElementById('edit_start_time_slot').value;
        const endTime = document.getElementById('edit_end_time_slot').value;
        const date = document.getElementById('edit_date').value;
        
        if (date < today) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: 'Please select a future date'
            });
            return;
        }
        
        if (startTime && endTime && startTime >= endTime) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Time',
                text: 'End time must be after start time'
            });
            return;
        }
    });

    // Show success/error messages with SweetAlert
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        timer: 4000
    });
    @endif

    // Reset add form when modal is closed
    document.getElementById('addAvailabilityModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('addAvailabilityForm').reset();
        document.getElementById('date').min = today;
    });

    // Reset edit form when modal is closed
    document.getElementById('editAvailabilityModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('edit_date').min = today;
    });
});
</script>
@endsection