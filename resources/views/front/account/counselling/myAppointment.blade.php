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
                        <li class="breadcrumb-item active">My Appointments</li>
                    </ol>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('CounselorShow') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Counselor</a>                
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- User Appointments Table -->
        <div class="row">
            <div class="col-lg-12">
                @include('front.message')

                <h5 class="mb-4">My Appointments</h5>

                @if($appointments->isNotEmpty())
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Counselor Name</th>
                                <th>Date</th>
                                <th>start_time_slot</th>
                                <th>end_time_slot</th>
                                <th>Status</th>
                                <th>token</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->counselorDetails->user->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->start_time_slot)->format('h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->end_time_slot)->format('h:i A') }}</td>

                                    <td>{{ ucfirst($appointment->status) }}</td>
                                    <td>{{ ucfirst($appointment->number_of_tokens) }}</td>

                                    <td>
                                        @if($appointment->status == 'pending')
                                            <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                            </form>
                                        @elseif($appointment->status =='confirmed')
                                        <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                        @else
                                            <p class="text-muted">done</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No appointments found.</p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
