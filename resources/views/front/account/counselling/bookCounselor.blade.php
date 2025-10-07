@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <!-- Breadcrumb Navigation -->
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Counselor Sessions</li>
                    </ol>
                    <br>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('CounselorShow') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Counselor</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- My Appointments Button -->
        <div class="row mb-3">
            <div class="col text-center">
                <a href="{{ route('appointments.showMyAppointments') }}" class="btn btn-lg btn-primary">
                    <i class="fa fa-calendar-check" aria-hidden="true"></i> View My Appointments
                </a>
            </div>
        </div>

        <!-- Session Booking Table -->
        <div class="row">
            <div class="col-lg-12">
                @include('front.message')

                <h5 class="mb-4">Available Time Slots for {{ $counselor->user->name ?? 'Counselor' }}</h5>

                @if($availabilities->isNotEmpty())
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>start_time_slot</th>
                                <th>end_time_slot</th>
                                <th>Status</th>
                                <th>token</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($availabilities as $availability)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($availability->date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($availability->start_time_slot)->format('h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($availability->end_time_slot)->format('h:i A') }}</td>

                                    <td>{{ ucfirst($availability->status) }}</td>
                                    <td>{{ ucfirst($availability->number_of_tokens) }}</td>

                                    <td>
                                        @if($availability->status == 'available')
                                            <form action="{{ route('appointments.book') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="counselor_id" value="{{ $counselor->id }}">
                                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                                <input type="hidden" name="date" value="{{ $availability->date }}">
                                                <input type="hidden" name="start_time_slot" value="{{ $availability->start_time_slot }}">
                                                <input type="hidden" name="end_time_slot" value="{{ $availability->end_time_slot }}">
                                                <input type="hidden" name="number_of_tokens" value="{{ $availability->number_of_tokens }}">

                                                <button type="submit" class="btn btn-primary btn-sm">Book</button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary">Booked</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No available time slots at the moment. Please check back later.</p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
