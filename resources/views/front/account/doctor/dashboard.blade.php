@extends('front.layouts.app')

@section('main')

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('front.account.doctor.slidebar')
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                @include('front.message')

                <!-- Welcome Message -->
                <div class="card border-0 shadow mb-4" style="background-color: #f7f9fc;">
                    <div class="card-body text-center">
                        <h3 class="fs-4 mb-1" style="color: #1b365e;">Welcome, Dr.{{ Auth::user()->name}}!</h3>
                        <p class="text-muted">We're glad to have you here. Check your appointments, review student progress, and explore resources to help your students thrive.</p>
                    </div>
                </div>

                <!-- Placeholder for additional content -->
                {{-- <div class="row">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow mb-4">
                            <div class="card-body">
                                <h5 class="card-title text-center mb-4">Appointments Summary</h5>
                    
                                <!-- Pending Appointments -->
                                <div class="mb-3 p-3 border rounded" style="background-color: #fff3cd; border-color: #ffeeba;">
                                    <h6 class="card-subtitle text-warning d-flex justify-content-between">
                                        <span>Pending Appointments</span>
                                        <span class="badge bg-warning text-dark">{{ count($pendingAppointments) }}</span>
                                    </h6>
                                    <p class="card-text mt-2">
                                        @if(count($pendingAppointments) > 0)
                                            {{ count($pendingAppointments) }} appointments are currently pending.
                                        @else
                                            No pending appointments.
                                        @endif
                                    </p>
                                </div>
                    
                                <!-- Confirmed Appointments -->
                                <div class="mb-3 p-3 border rounded" style="background-color: #d4edda; border-color: #c3e6cb;">
                                    <h6 class="card-subtitle text-success d-flex justify-content-between">
                                        <span>Confirmed Appointments</span>
                                        <span class="badge bg-success text-dark">{{ count($confirmedAppointments) }}</span>
                                    </h6>
                                    <p class="card-text mt-2">
                                        @if(count($confirmedAppointments) > 0)
                                            {{ count($confirmedAppointments) }} appointments have been confirmed.
                                        @else
                                            No confirmed appointments.
                                        @endif
                                    </p>
                                </div>
                    
                                <!-- Completed Appointments -->
                                <div class="mb-3 p-3 border rounded" style="background-color: #cce5ff; border-color: #b8daff;">
                                    <h6 class="card-subtitle text-primary d-flex justify-content-between">
                                        <span>Completed Appointments</span>
                                        <span class="badge bg-success">{{ count($completedAppointments) }}</span>
                                    </h6>
                                    <p class="card-text mt-2">
                                        @if(count($completedAppointments) > 0)
                                            {{ count($completedAppointments) }} appointments have been completed.
                                        @else
                                            No completed appointments.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                  
                    <div class="col-lg-6">
                        <!-- Completed Sessions Count -->
                        <div class="card border-0 shadow mb-4">
                            <div class="card-body">
                                <h5 class="card-title text-center mb-4">Sessions Summary</h5>
                                    <div class="mb-3 p-3 border rounded" style="background-color: #fff3cd; border-color: #ffeeba;">
                                        <h6 class="card-subtitle text-warning d-flex justify-content-between">
                                            <span>Completed Sessions</span>
                                            <span class="badge bg-warning text-dark">{{ $completedSessionsCount }}</span>
                                        </h6>
                                        <p class="card-text mt-2">
                                            @if($completedSessionsCount > 0)
                                                You have {{ $completedSessionsCount }} completed sessions.
                                            @else
                                                No completed sessions.
                                            @endif
                                        </p>
                                    </div>   
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</section>

@endsection

@section('customJS')
<script>
// Add any custom JavaScript code here if needed
</script>
@endsection
