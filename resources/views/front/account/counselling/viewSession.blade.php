@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4 bg-light">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Counselor Sessions</li>
                    </ol>
                    <br>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{route('CounselorShow')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Back to Counselor</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                @include('front.message')

                <h5 class="mb-4">Counselor Sessions</h5>

                <!-- Sessions Table -->
                <div class="card border-0 shadow mb-4">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Session Date</th>
                                    <th>Session Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($sessions->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">No sessions available. Please add a new session.</td>
                                    </tr>
                                @else
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>{{ $session->title }}</td>
                                            <td>{{ $session->session_date->format('Y-m-d') }}</td>
                                            <td>
                                                {!! nl2br(Str::limit($session->session, 100)) !!}
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#fullSessionModal-{{ $session->id }}">Read more</a>
                                            </td>
                                            <td>
                                                <div class="action-dots">
                                                    <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#viewModal-{{ $session->id }}">
                                                                <i class="fa fa-eye" aria-hidden="true"></i> View Notes
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Full Session Details Modal -->
                                        <div class="modal fade" id="fullSessionModal-{{ $session->id }}" tabindex="-1" aria-labelledby="fullSessionModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Full Session Details: {{ $session->title }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>{!! nl2br($session->session) !!}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- View Notes Modal -->
                                        <div class="modal fade" id="viewModal-{{ $session->id }}" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Notes for {{ $session->title }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if ($session->notes->isEmpty())
                                                            <p>No notes uploaded for this session.</p>
                                                        @else
                                                            <ul class="list-group">
                                                                @foreach ($session->notes as $note)
                                                                    <li class="list-group-item">
                                                                        <a href="{{ asset('storage/' . $note->note_path) }}" target="_blank">{{ basename($note->note_path) }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        <!-- Pagination Links -->
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
