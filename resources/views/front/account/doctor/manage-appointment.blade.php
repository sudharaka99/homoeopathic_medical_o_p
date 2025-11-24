@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manage Appointments</li>
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
                                <i class="fas fa-calendar-check text-primary me-2"></i>Manage Appointments
                            </h3>
                            <div class="d-flex gap-2">
                                <span class="badge bg-primary fs-6" id="totalAppointments">Total: {{ $appointments->count() }}</span>
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
                            
                            <div class="col-md-2">
                                <label for="dateFrom" class="form-label">From Date</label>
                                <input type="date" id="dateFrom" name="date_from" class="form-control">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="dateTo" class="form-label">To Date</label>
                                <input type="date" id="dateTo" name="date_to" class="form-control">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="searchInput" class="form-label">Search Patient</label>
                                <input type="text" id="searchInput" name="search" class="form-control" placeholder="Patient Name/Email">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select id="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
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
                            <table class="table table-bordered table-striped" id="appointmentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Date & Time</th>
                                        <th>Token</th>
                                        <th>Fee</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Booked On</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($appointments as $appointment)
                                        @php
                                            $appointmentDate = \Carbon\Carbon::parse($appointment->appointment_date);
                                            $isToday = $appointmentDate->isToday();
                                            $isUpcoming = $appointmentDate->isFuture();
                                            $isPast = $appointmentDate->isPast();
                                            
                                            // Check if meeting can be joined - ONLY WHEN PAID
                                            $canJoinMeeting = $appointment->status == 'confirmed' && 
                                                             $appointment->payment_status == 'paid' &&
                                                             !empty($appointment->zoom_join_url) &&
                                                             ($isToday || $isPast);
                                        @endphp
                                        <tr data-date="{{ $appointment->appointment_date }}" data-status="{{ $appointment->status }}" data-patient="{{ strtolower($appointment->patient_name) }} {{ strtolower($appointment->patient_email) }}">
                                            <td>{{ $appointment->id }}</td>
                                            <td>
                                                <div>
                                                    <strong class="d-block">{{ $appointment->patient_name }}</strong>
                                                    <small class="text-muted">{{ $appointment->patient_email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">
                                                    {{ $appointmentDate->format('M d, Y') }}
                                                    @if($isToday)
                                                        <span class="badge bg-info ms-1">Today</span>
                                                    @elseif($isUpcoming)
                                                        <span class="badge bg-success ms-1">Upcoming</span>
                                                    @elseif($isPast)
                                                        <span class="badge bg-secondary ms-1">Past</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="token-number-display">
                                                    <span class="token-badge">#{{ $appointment->token_number }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Rs. {{ number_format($appointment->fee, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($appointment->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                    @if($appointment->payment_method)
                                                        <br><small class="text-muted">via {{ ucfirst($appointment->payment_method) }}</small>
                                                    @endif
                                                @elseif($appointment->payment_status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($appointment->payment_status == 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($appointment->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $appointment->status == 'pending' ? 'bg-warning text-dark' : ($appointment->status == 'confirmed' ? 'bg-success' : ($appointment->status == 'completed' ? 'bg-info' : ($appointment->status == 'cancelled' ? 'bg-danger' : 'bg-secondary'))) }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, h:i A') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-info view-details-btn" 
                                                            data-appointment-id="{{ $appointment->id }}"
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- ZOOM JOIN BUTTON -->
                                                    @if($canJoinMeeting)
                                                        <button class="btn btn-primary join-meeting-btn"
                                                                data-appointment-id="{{ $appointment->id }}"
                                                                data-join-url="{{ $appointment->zoom_join_url }}"
                                                                data-meeting-id="{{ $appointment->zoom_meeting_id }}"
                                                                title="Join Zoom Meeting">
                                                            <i class="fas fa-video"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($appointment->status == 'pending')
                                                        <button class="btn btn-success confirm-btn"
                                                                data-appointment-id="{{ $appointment->id }}"
                                                                title="Confirm Appointment">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-danger cancel-btn"
                                                                data-appointment-id="{{ $appointment->id }}"
                                                                title="Cancel Appointment">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($appointment->status == 'confirmed')
                                                        <button class="btn btn-primary complete-btn"
                                                                data-appointment-id="{{ $appointment->id }}"
                                                                title="Mark as Completed">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No appointments found.</td>
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

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailsModalLabel">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="appointmentDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJS')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Token Badge Styling
const tokenBadgeStyle = `
.token-number-display {
    display: inline-block;
}
.token-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 2px solid #5a6fd8;
    display: inline-block;
    min-width: 50px;
    text-align: center;
}
.btn-group .btn {
    margin: 0 2px;
    border-radius: 6px;
}
.zoom-meeting-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    padding: 20px;
    color: white;
    margin: 15px 0;
}
.zoom-link {
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 10px 15px;
    word-break: break-all;
    margin: 10px 0;
    border: 1px solid rgba(255,255,255,0.3);
}
.zoom-link small {
    color: white;
    font-family: monospace;
}
`;

// Add styles to head
const styleSheet = document.createElement("style");
styleSheet.innerText = tokenBadgeStyle;
document.head.appendChild(styleSheet);

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing appointments table');
    
    // Initialize filtering and pagination
    initializeTableFiltering();

    // View Appointment Details
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            loadAppointmentDetails(appointmentId);
        });
    });

    // Join Meeting Button for Doctor
    document.querySelectorAll('.join-meeting-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            const joinUrl = this.dataset.joinUrl;
            const meetingId = this.dataset.meetingId;
            
            Swal.fire({
                title: 'Join Zoom Meeting?',
                html: `
                    <div class="text-start">
                        <p>You are about to join the Zoom meeting for this appointment.</p>
                        <p><strong>Meeting ID:</strong> ${meetingId}</p>
                        <p class="text-success"><i class="fas fa-check-circle me-1"></i>Patient has paid for this consultation</p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-video me-1"></i> Join Meeting',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Open Zoom meeting in new tab
                    window.open(joinUrl, '_blank', 'noopener,noreferrer');
                    
                    Swal.fire({
                        title: 'Meeting Launched!',
                        text: 'Zoom meeting has been opened in a new tab.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        });
    });

    // Confirm Appointment
    document.querySelectorAll('.confirm-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            confirmAppointmentStatus(appointmentId, 'confirmed', 'Are you sure you want to confirm this appointment?');
        });
    });

    // Cancel Appointment
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            confirmAppointmentStatus(appointmentId, 'cancelled', 'Are you sure you want to cancel this appointment?');
        });
    });

    // Complete Appointment
    document.querySelectorAll('.complete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            confirmAppointmentStatus(appointmentId, 'completed', 'Are you sure you want to mark this appointment as completed?');
        });
    });

    // Success/Error Messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: true
        });
    @endif
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 5000,
            showConfirmButton: true
        });
    @endif
});

function initializeTableFiltering() {
    const searchInput = document.getElementById('searchInput');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const statusFilter = document.getElementById('statusFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const resultsCounter = document.getElementById('resultsCounter');
    const tableRows = document.querySelectorAll('#appointmentsTable tbody tr');
    const totalAppointments = document.getElementById('totalAppointments');

    console.log('Table rows found:', tableRows.length);

    let currentPage = 1;
    let filteredRows = [];

    if (searchInput && dateFrom && dateTo && tableRows.length > 0) {
        // Set default dates to show TODAY only
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        
        dateFrom.value = todayStr;
        dateTo.value = todayStr;

        console.log('Default dates set to TODAY:', todayStr);

        const filterTable = () => {
            const searchTerm = searchInput.value.trim().toLowerCase();
            const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
            const toDate = dateTo.value ? new Date(dateTo.value) : null;
            const selectedStatus = statusFilter.value;

            console.log('Filtering with:', {
                searchTerm,
                fromDate: dateFrom.value,
                toDate: dateTo.value,
                status: selectedStatus
            });

            filteredRows = Array.from(tableRows).filter(row => {
                const patientData = row.getAttribute('data-patient') || '';
                const statusValue = row.getAttribute('data-status') || '';
                const dateValue = row.getAttribute('data-date') || '';
                
                console.log('Checking row:', { patientData, statusValue, dateValue });

                // Search filter
                const matchesSearch = !searchTerm || patientData.includes(searchTerm);

                // Status filter
                const matchesStatus = !selectedStatus || statusValue === selectedStatus;

                // Date filter - show only if date matches the range
                let matchesDate = true;
                if (dateValue && fromDate && toDate) {
                    const rowDate = new Date(dateValue);
                    const fromDateOnly = new Date(fromDate);
                    fromDateOnly.setHours(0, 0, 0, 0);
                    const toDateOnly = new Date(toDate);
                    toDateOnly.setHours(23, 59, 59, 999);
                    const rowDateOnly = new Date(rowDate);
                    rowDateOnly.setHours(0, 0, 0, 0);

                    if (rowDateOnly < fromDateOnly || rowDateOnly > toDateOnly) {
                        matchesDate = false;
                        console.log('Date not in range:', dateValue);
                    }
                }

                const shouldShow = matchesSearch && matchesStatus && matchesDate;
                console.log('Row result:', { 
                    matchesSearch, 
                    matchesStatus, 
                    matchesDate, 
                    shouldShow 
                });

                return shouldShow;
            });

            console.log('Filtered rows count:', filteredRows.length);

            // Update total count
            if (totalAppointments) {
                totalAppointments.textContent = `Total: ${filteredRows.length}`;
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

            document.getElementById('appointmentsTable').parentNode.insertBefore(paginationContainer, document.getElementById('appointmentsTable').nextSibling);
        };

        const filterAndPaginate = () => {
            currentPage = 1;
            filterTable();
            paginateResults();
        };

        const clearAllFilters = () => {
            searchInput.value = '';
            // Reset to today's date when clearing filters
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            dateFrom.value = todayStr;
            dateTo.value = todayStr;
            statusFilter.value = '';
            perPageSelect.value = '25';
            filterAndPaginate();
        };

        // Initialize with today's data
        filterAndPaginate();
        
        searchInput.addEventListener('input', filterAndPaginate);
        dateFrom.addEventListener('change', filterAndPaginate);
        dateTo.addEventListener('change', filterAndPaginate);
        statusFilter.addEventListener('change', filterAndPaginate);
        perPageSelect.addEventListener('change', filterAndPaginate);
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    } else {
        console.log('Required elements not found or no table rows');
    }
}

function loadAppointmentDetails(appointmentId) {
    Swal.fire({ 
        title: 'Loading...', 
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); }
    });

    const url = `/appointments/${appointmentId}/details`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success && data.appointment) {
            const apt = data.appointment;
            const modalContent = document.getElementById('appointmentDetailsContent');
            
            // Check if meeting exists and is available (only when paid)
            const hasMeeting = apt.zoom_meeting_id && apt.zoom_join_url;
            const canJoinMeeting = apt.status == 'confirmed' && apt.payment_status == 'paid' && hasMeeting;
            
            let meetingSection = '';
            if (hasMeeting) {
                meetingSection = `
                    <div class="zoom-meeting-section">
                        <h6 class="text-white mb-3"><i class="fas fa-video me-2"></i>Zoom Meeting Details</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Meeting ID:</strong> ${apt.zoom_meeting_id || 'N/A'}</p>
                                <p class="mb-1"><strong>Password:</strong> ${apt.zoom_meeting_password || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Created At:</strong> ${apt.meeting_created_at ? new Date(apt.meeting_created_at).toLocaleString() : 'Not created'}</p>
                            </div>
                        </div>
                        
                        ${canJoinMeeting ? `
                        <div class="mt-3">
                            <p class="mb-2"><strong>Join URL:</strong></p>
                            <div class="zoom-link">
                                <small>${apt.zoom_join_url}</small>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-light btn-sm" onclick="window.open('${apt.zoom_join_url}', '_blank', 'noopener,noreferrer')">
                                    <i class="fas fa-external-link-alt me-1"></i> Join Meeting
                                </button>
                                ${apt.zoom_start_url ? `
                                <button class="btn btn-outline-light btn-sm ms-2" onclick="window.open('${apt.zoom_start_url}', '_blank', 'noopener,noreferrer')">
                                    <i class="fas fa-play me-1"></i> Start Meeting
                                </button>
                                ` : ''}
                                <button class="btn btn-outline-light btn-sm ms-2" onclick="copyToClipboard('${apt.zoom_join_url}')">
                                    <i class="fas fa-copy me-1"></i> Copy Link
                                </button>
                            </div>
                        </div>
                        ` : `
                        <div class="alert alert-warning mt-2 mb-0">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                ${apt.payment_status == 'paid' ? 'Meeting is ready. Patient can join anytime.' : 'Meeting link will be available after payment is completed.'}
                            </small>
                        </div>
                        `}
                    </div>
                `;
            }
            
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Patient Information</h6>
                        <p><strong>Name:</strong> ${apt.patient_name}</p>
                        <p><strong>Email:</strong> ${apt.patient_email}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Appointment Details</h6>
                        <p><strong>Date:</strong> ${new Date(apt.appointment_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        <p><strong>Time:</strong> ${apt.start_time} - ${apt.end_time}</p>
                        <p><strong>Token:</strong> <span class="token-badge">#${apt.token_number}</span></p>
                    </div>
                </div>
                
                ${meetingSection}
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Fee & Status</h6>
                        <p><strong>Fee:</strong> Rs. ${parseFloat(apt.fee).toLocaleString()}</p>
                        <p><strong>Payment Status:</strong> 
                            <span class="badge ${apt.payment_status == 'paid' ? 'bg-success' : apt.payment_status == 'pending' ? 'bg-warning text-dark' : 'bg-danger'}">
                                ${apt.payment_status.charAt(0).toUpperCase() + apt.payment_status.slice(1)}
                            </span>
                            ${apt.payment_method ? `<br><small class="text-muted">via ${apt.payment_method.charAt(0).toUpperCase() + apt.payment_method.slice(1)}</small>` : ''}
                        </p>
                        <p><strong>Appointment Status:</strong> 
                            <span class="badge ${apt.status == 'pending' ? 'bg-warning text-dark' : apt.status == 'confirmed' ? 'bg-success' : apt.status == 'completed' ? 'bg-info' : 'bg-danger'}">
                                ${apt.status.charAt(0).toUpperCase() + apt.status.slice(1)}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Timestamps</h6>
                        <p><strong>Booked On:</strong> ${new Date(apt.created_at).toLocaleString()}</p>
                        <p><strong>Last Updated:</strong> ${new Date(apt.updated_at).toLocaleString()}</p>
                    </div>
                </div>
                ${apt.notes ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-muted">Notes</h6>
                        <div class="alert alert-light">${apt.notes}</div>
                    </div>
                </div>
                ` : ''}
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
            modal.show();
        } else {
            Swal.fire('Error', data.message || 'Failed to load appointment details', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'Could not load appointment details. Please try again.', 'error');
    });
}

function confirmAppointmentStatus(appointmentId, status, message) {
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: status == 'confirmed' ? '#28a745' : (status == 'cancelled' ? '#dc3545' : '#007bff'),
        cancelButtonColor: '#6c757d',
        confirmButtonText: status == 'confirmed' ? 'Yes, Confirm!' : (status == 'cancelled' ? 'Yes, Cancel!' : 'Yes, Complete!'),
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            updateAppointmentStatus(appointmentId, status);
        }
    });
}

function updateAppointmentStatus(appointmentId, status) {
    Swal.fire({ 
        title: 'Updating...', 
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); }
    });

    const url = `/appointments/${appointmentId}/update-status`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire('Error', 'Network error. Please try again.', 'error');
    });
}

// Utility function to copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Meeting link copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    }, function(err) {
        console.error('Could not copy text: ', err);
        Swal.fire('Error', 'Failed to copy link', 'error');
    });
}
</script>
@endsection