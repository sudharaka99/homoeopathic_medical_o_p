@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manage Availability</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                @include('front.account.doctor.slidebar')
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                @include('front.message')

                <div class="card border-0 shadow mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fs-4 mb-0">
                                <i class="fas fa-calendar-plus text-primary me-2"></i>Manage Availability
                            </h3>
                            <div class="d-flex gap-2">
                                <span class="badge bg-primary fs-6" id="totalSlots">Total: {{ $availabilities->count() }}</span>
                                <button id="btnAddNewSlot" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                                    <i class="fas fa-plus me-2"></i>Add New Slot
                                </button>
                            </div>
                        </div>

                        <!-- Client-side search/filter section -->
                        <div id="filterForm" class="row g-3 align-items-end mb-4">
                            <div class="col-md-2">
                                <label class="form-label">Show</label>
                                <div class="d-flex align-items-center">
                                    <select class="form-select" id="perPageSelect" style="width: auto; min-width: 70px;">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="all">All</option>
                                    </select>
                                    <span class="ms-2">entries</span>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="dateFrom" class="form-label">From Date</label>
                                <input type="date" id="dateFrom" name="date_from" class="form-control">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="dateTo" class="form-label">To Date</label>
                                <input type="date" id="dateTo" name="date_to" class="form-control">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select id="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="booked">Booked</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">Clear</button>
                                    <div id="resultsCounter" class="badge bg-primary align-self-center"></div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="availabilityTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Slot</th>
                                        <th>Duration</th>
                                        <th>Tokens</th>
                                        <th>Status</th>
                                        <th>Created On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($availabilities as $slot)
                                        @php
                                            $slotDate = \Carbon\Carbon::parse($slot->date);
                                            $isToday = $slotDate->isToday();
                                            $isPast = $slotDate->isPast() && !$isToday;
                                            $isFuture = $slotDate->isFuture();
                                            
                                            $duration = \Carbon\Carbon::parse($slot->start_time_slot)
                                                ->diffInMinutes(\Carbon\Carbon::parse($slot->end_time_slot));
                                        @endphp
                                        <tr data-date="{{ $slot->date }}" data-status="{{ $slot->status }}">
                                            <td>
                                                <div class="fw-bold">
                                                    {{ $slotDate->format('M d, Y') }}
                                                    @if($isToday)
                                                        <span class="badge bg-info ms-1">Today</span>
                                                    @elseif($isFuture)
                                                        <span class="badge bg-success ms-1">Upcoming</span>
                                                    @elseif($isPast)
                                                        <span class="badge bg-secondary ms-1">Past</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">{{ $slotDate->format('l') }}</div>
                                            </td>
                                            <td>
                                                <strong>
                                                    {{ \Carbon\Carbon::parse($slot->start_time_slot)->format('h:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($slot->end_time_slot)->format('h:i A') }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $duration }} min</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark">{{ $slot->number_of_tokens }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $slot->status == 'available' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ ucfirst($slot->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($slot->created_at)->format('M d, h:i A') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    @if($slot->status == 'available' && !$isPast)
                                                        <button class="btn btn-info edit-slot-btn" 
                                                                data-slot-id="{{ $slot->id }}"
                                                                title="Edit Slot">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    @if(!$isPast)
                                                        <button class="btn btn-danger delete-slot-btn"
                                                                data-slot-id="{{ $slot->id }}"
                                                                title="Delete Slot">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No availability slots found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Container -->
                        <div id="tablePagination" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - starting table initialization');
    
    // Initialize filtering and pagination
    initializeTableFiltering();

    // Edit Slot
    document.querySelectorAll('.edit-slot-btn').forEach(button => {
        button.addEventListener('click', function() {
            const slotId = this.dataset.slotId;
            editSlot(slotId);
        });
    });

    // Delete Slot
    document.querySelectorAll('.delete-slot-btn').forEach(button => {
        button.addEventListener('click', function() {
            const slotId = this.dataset.slotId;
            confirmDelete(slotId);
        });
    });

    // Form Auto-fill & Validation
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

function initializeTableFiltering() {
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const statusFilter = document.getElementById('statusFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const resultsCounter = document.getElementById('resultsCounter');
    const totalSlots = document.getElementById('totalSlots');
    const tableRows = document.querySelectorAll('#availabilityTable tbody tr');

    console.log('Table rows found:', tableRows.length);

    let currentPage = 1;
    let filteredRows = [];

    if (dateFrom && dateTo && tableRows.length > 0) {
        // Set default to today's date for both From and To dates
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        dateFrom.value = todayStr;
        dateTo.value = todayStr;

        console.log('Default dates set to:', todayStr);

        const filterTable = () => {
            const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
            const toDate = dateTo.value ? new Date(dateTo.value) : null;
            const selectedStatus = statusFilter.value;

            console.log('Filtering with:', {
                fromDate: dateFrom.value,
                toDate: dateTo.value,
                status: selectedStatus
            });

            // If no dates are selected, show all rows
            if (!dateFrom.value && !dateTo.value) {
                filteredRows = Array.from(tableRows);
                console.log('No date filters - showing all rows:', filteredRows.length);
            } else {
                filteredRows = Array.from(tableRows).filter(row => {
                    const dateValue = row.getAttribute('data-date');
                    const statusValue = row.getAttribute('data-status');
                    
                    console.log('Checking row:', { dateValue, statusValue });

                    if (!dateValue) {
                        console.log('Row has no date value, skipping');
                        return false;
                    }

                    const rowDate = new Date(dateValue);
                    
                    // Date range filter
                    let matchesDateRange = true;
                    if (fromDate && toDate) {
                        // Set hours to 0 for proper date comparison
                        const fromDateOnly = new Date(fromDate);
                        fromDateOnly.setHours(0, 0, 0, 0);
                        const toDateOnly = new Date(toDate);
                        toDateOnly.setHours(23, 59, 59, 999); // End of day
                        const rowDateOnly = new Date(rowDate);
                        rowDateOnly.setHours(0, 0, 0, 0);

                        if (rowDateOnly < fromDateOnly || rowDateOnly > toDateOnly) {
                            matchesDateRange = false;
                            console.log('Row date outside range:', dateValue);
                        }
                    }

                    // Status filter
                    const matchesStatus = !selectedStatus || statusValue === selectedStatus;

                    const shouldShow = matchesDateRange && matchesStatus;
                    console.log('Row result:', { 
                        dateValue, 
                        matchesDateRange, 
                        matchesStatus, 
                        shouldShow 
                    });

                    return shouldShow;
                });
            }

            console.log('Filtered rows count:', filteredRows.length);

            // Update total count
            if (totalSlots) {
                totalSlots.textContent = `Total: ${filteredRows.length}`;
            }
        };

        const paginateResults = () => {
            const perPage = perPageSelect.value === 'all' ? filteredRows.length : parseInt(perPageSelect.value);
            const totalRows = filteredRows.length;
            
            console.log('Paginating:', { perPage, totalRows, currentPage });

            // Hide all rows first
            tableRows.forEach(row => row.style.display = 'none');

            if (perPage === totalRows || perPageSelect.value === 'all') {
                // Show all filtered rows
                filteredRows.forEach(row => row.style.display = '');
                updateResultsCounter(totalRows, totalRows);
                console.log('Showing all rows');
            } else {
                const totalPages = Math.ceil(totalRows / perPage);
                if (currentPage > totalPages) currentPage = 1;
                if (currentPage < 1) currentPage = 1;

                const startIndex = (currentPage - 1) * perPage;
                const endIndex = Math.min(startIndex + perPage, totalRows);

                console.log('Showing rows:', startIndex, 'to', endIndex);

                // Show only the rows for the current page
                for (let i = startIndex; i < endIndex; i++) {
                    if (filteredRows[i]) {
                        filteredRows[i].style.display = '';
                    }
                }

                updateResultsCounter(endIndex - startIndex, totalRows);
                updatePagination(currentPage, totalPages);
            }
        };

        const updateResultsCounter = (showing, total) => {
            if (resultsCounter) {
                resultsCounter.textContent = `${showing}/${total}`;
                console.log('Results counter updated:', showing, '/', total);
            }
        };

        const updatePagination = (page, totalPages) => {
            const existingPagination = document.getElementById('tablePagination');
            if (existingPagination) existingPagination.remove();
            if (totalPages <= 1) return;

            const paginationContainer = document.createElement('div');
            paginationContainer.id = 'tablePagination';
            paginationContainer.className = 'pagination-container mt-3 d-flex justify-content-between align-items-center';

            const paginationInfo = document.createElement('div');
            paginationInfo.textContent = `Page ${page} of ${totalPages}`;

            const paginationControls = document.createElement('div');
            paginationControls.className = 'btn-group';

            const prevBtn = document.createElement('button');
            prevBtn.className = `btn btn-outline-primary btn-sm ${page === 1 ? 'disabled' : ''}`;
            prevBtn.textContent = '← Previous';
            prevBtn.onclick = () => {
                if (page > 1) {
                    currentPage--;
                    paginateResults();
                }
            };

            const nextBtn = document.createElement('button');
            nextBtn.className = `btn btn-outline-primary btn-sm ${page === totalPages ? 'disabled' : ''}`;
            nextBtn.textContent = 'Next →';
            nextBtn.onclick = () => {
                if (page < totalPages) {
                    currentPage++;
                    paginateResults();
                }
            };

            paginationControls.appendChild(prevBtn);
            paginationControls.appendChild(nextBtn);
            paginationContainer.appendChild(paginationInfo);
            paginationContainer.appendChild(paginationControls);

            document.getElementById('availabilityTable').parentNode.insertBefore(paginationContainer, document.getElementById('availabilityTable').nextSibling);
        };

        const filterAndPaginate = () => {
            currentPage = 1;
            filterTable();
            paginateResults();
        };

        const clearAllFilters = () => {
            // Clear all filters - show all data
            dateFrom.value = '';
            dateTo.value = '';
            statusFilter.value = '';
            perPageSelect.value = '25';
            filterAndPaginate();
        };

        // Initialize with today's data
        filterAndPaginate();
        
        dateFrom.addEventListener('change', filterAndPaginate);
        dateTo.addEventListener('change', filterAndPaginate);
        statusFilter.addEventListener('change', filterAndPaginate);
        perPageSelect.addEventListener('change', filterAndPaginate);
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    } else {
        console.log('Required elements not found or no table rows');
    }
}

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
</script>
@endsection