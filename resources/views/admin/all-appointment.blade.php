@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">All Appointments</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('admin.slidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')

                <div class="card border-0 shadow mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="mb-0">All Appointments</h2>
                                <p class="text-muted mb-0">
                                    Total Appointments: {{ $appointments->total() }}
                                </p>
                            </div>
                        </div>

                        @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date & Time</th>
                                        <th>Fee</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Zoom</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                    <tr>
                                        <td class="fw-semibold">{{ $appointment->id }}</td>
                                        
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $appointment->patient_name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $appointment->patient_email ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div>
                                                <div class="fw-semibold">Dr. {{ $appointment->doctor_name }}</div>
                                                <small class="text-muted">{{ $appointment->specialization_name ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="small">
                                                <div class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                                </div>
                                                <div class="text-muted">
                                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="fw-bold text-success">RS.{{ number_format($appointment->fee, 2) }}</td>
                                        
                                        <td>
                                            <span class="badge 
                                                @if($appointment->status == 'confirmed') bg-primary
                                                @elseif($appointment->status == 'pending') bg-warning text-dark
                                                @elseif($appointment->status == 'completed') bg-success
                                                @elseif($appointment->status == 'cancelled') bg-danger
                                                @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <span class="badge 
                                                @if($appointment->payment_status == 'paid') bg-success
                                                @elseif($appointment->payment_status == 'pending') bg-warning text-dark
                                                @elseif($appointment->payment_status == 'failed') bg-danger
                                                @endif">
                                                {{ ucfirst($appointment->payment_status) }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            @if($appointment->zoom_join_url)
                                                <span class="badge bg-success">Available</span>
                                            @else
                                                <span class="badge bg-secondary">No Meeting</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }} of {{ $appointments->total() }} results
                            </div>
                            <nav>
                                {{ $appointments->links() }}
                            </nav>
                        </div>
                        
                        @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Appointments Found</h4>
                            <p class="text-muted">There are no appointments in the system yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJS')
<style>
.table > :not(caption) > * > * {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection