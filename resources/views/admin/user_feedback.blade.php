@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Contact Messages</li>
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
                                <h2 class="mb-0">Contact Messages</h2>
                                <p class="text-muted mb-0 small">
                                    Total: {{ $totalMessages }} | 
                                    New: {{ $newCount }} | 
                                    In Progress: {{ $inProgressCount }} | 
                                    Resolved: {{ $resolvedCount }}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fa fa-filter me-1"></i>
                                        @if(request('status') == 'new')
                                            New Messages
                                        @elseif(request('status') == 'in_progress')
                                            In Progress
                                        @elseif(request('status') == 'resolved')
                                            Resolved
                                        @else
                                            All Messages
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item {{ !request('status') ? 'active' : '' }}" 
                                               href="{{ route('admin.user-feedback') }}">
                                                <i class="fa fa-list me-2"></i>All Messages
                                                <span class="badge bg-secondary float-end">{{ $totalMessages }}</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item {{ request('status') == 'new' ? 'active' : '' }}" 
                                               href="{{ route('admin.user-feedback', ['status' => 'new']) }}">
                                                <i class="fa fa-clock me-2 text-warning"></i>New Messages
                                                <span class="badge bg-warning float-end">{{ $newCount }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item {{ request('status') == 'in_progress' ? 'active' : '' }}" 
                                               href="{{ route('admin.user-feedback', ['status' => 'in_progress']) }}">
                                                <i class="fa fa-spinner me-2 text-info"></i>In Progress
                                                <span class="badge bg-info float-end">{{ $inProgressCount }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item {{ request('status') == 'resolved' ? 'active' : '' }}" 
                                               href="{{ route('admin.user-feedback', ['status' => 'resolved']) }}">
                                                <i class="fa fa-check me-2 text-success"></i>Resolved
                                                <span class="badge bg-success float-end">{{ $resolvedCount }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                
                                <!-- Quick Action Buttons -->
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.user-feedback', ['status' => 'new']) }}" 
                                       class="btn btn-sm {{ request('status') == 'new' ? 'btn-warning' : 'btn-outline-warning' }}">
                                        <i class="fa fa-clock me-1"></i>New ({{ $newCount }})
                                    </a>
                                    <a href="{{ route('admin.user-feedback', ['status' => 'resolved']) }}" 
                                       class="btn btn-sm {{ request('status') == 'resolved' ? 'btn-success' : 'btn-outline-success' }}">
                                        <i class="fa fa-check me-1"></i>Resolved ({{ $resolvedCount }})
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Active Indicator -->
                        @if(request('status'))
                        <div class="alert alert-info py-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa fa-filter me-2"></i>
                                    Showing 
                                    @if(request('status') == 'new')
                                        <strong>New Messages</strong>
                                    @elseif(request('status') == 'in_progress')
                                        <strong>In Progress Messages</strong>
                                    @elseif(request('status') == 'resolved')
                                        <strong>Resolved Messages</strong>
                                    @endif
                                    <span class="badge bg-primary ms-2">{{ $messages->count() }} messages</span>
                                </div>
                                <a href="{{ route('admin.user-feedback') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-times me-1"></i>Clear Filter
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($messages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($messages as $message)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 50px; height: 50px;">
                                                    {{ substr($message->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $message->name }}</h6>
                                                    <p class="text-muted mb-1 small">
                                                        <i class="fa fa-envelope me-1"></i>{{ $message->email }}
                                                    </p>
                                                    @if($message->phone)
                                                    <p class="text-muted mb-0 small">
                                                        <i class="fa fa-phone me-1"></i>{{ $message->phone }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <strong>{{ $message->subject }}</strong>
                                        </td>
                                        
                                        <td>
                                            <p class="mb-0 text-truncate" style="max-width: 200px;">
                                                {{ Str::limit($message->message, 50) }}
                                            </p>
                                        </td>
                                        
                                        <td>
                                            <div class="small">
                                                <div>{{ \Carbon\Carbon::parse($message->created_at)->format('M j, Y') }}</div>
                                                <div class="text-muted">{{ \Carbon\Carbon::parse($message->created_at)->format('g:i A') }}</div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="badge 
                                                @if($message->status == 'new') bg-warning
                                                @elseif($message->status == 'in_progress') bg-info
                                                @elseif($message->status == 'resolved') bg-success
                                                @endif">
                                                @if($message->status == 'new')
                                                    <i class="fa fa-clock me-1"></i>
                                                @elseif($message->status == 'in_progress')
                                                    <i class="fa fa-spinner me-1"></i>
                                                @elseif($message->status == 'resolved')
                                                    <i class="fa fa-check me-1"></i>
                                                @endif
                                                {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item view-message" 
                                                           href="#" 
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#messageModal"
                                                           data-message-id="{{ $message->id }}"
                                                           data-message-name="{{ $message->name }}"
                                                           data-message-email="{{ $message->email }}"
                                                           data-message-phone="{{ $message->phone }}"
                                                           data-message-subject="{{ $message->subject }}"
                                                           data-message-content="{{ $message->message }}"
                                                           data-message-created="{{ $message->created_at }}"
                                                           data-message-reply="{{ $message->reply_message }}"
                                                           data-message-replied="{{ $message->replied_at }}">
                                                            <i class="fa fa-eye text-primary me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item reply-message" 
                                                           href="#" 
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#replyModal"
                                                           data-message-id="{{ $message->id }}"
                                                           data-customer-email="{{ $message->email }}"
                                                           data-customer-name="{{ $message->name }}"
                                                           data-subject="{{ $message->subject }}">
                                                            <i class="fa fa-reply text-success me-2"></i>Reply
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if($message->status != 'resolved')
                                                    <li>
                                                        <form action="{{ route('admin.contact.update-status', $message->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="resolved">
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="fa fa-check text-success me-2"></i>Mark Resolved
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                    @if($message->status != 'new')
                                                    <li>
                                                        <form action="{{ route('admin.contact.update-status', $message->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="new">
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="fa fa-clock text-warning me-2"></i>Mark as New
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this message?')">
                                                                <i class="fa fa-trash text-danger me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="fa fa-envelope-open text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">
                                @if(request('status') == 'new')
                                    No New Messages
                                @elseif(request('status') == 'in_progress')
                                    No In Progress Messages
                                @elseif(request('status') == 'resolved')
                                    No Resolved Messages
                                @else
                                    No Messages Found
                                @endif
                            </h4>
                            <p class="text-muted mb-4">
                                @if(request('status'))
                                    There are no {{ str_replace('_', ' ', request('status')) }} messages at the moment.
                                @else
                                    There are no contact messages to display.
                                @endif
                            </p>
                            @if(request('status'))
                            <a href="{{ route('admin.user-feedback') }}" class="btn btn-primary">
                                <i class="fa fa-list me-2"></i>View All Messages
                            </a>
                            @else
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                <i class="fa fa-dashboard me-2"></i>Go to Dashboard
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- View Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="messageModalLabel">
                    <i class="fa fa-envelope me-2"></i>Message Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0 text-primary">
                                    <i class="fa fa-user me-1"></i>Customer Message
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Name:</strong> <span id="detailName"></span></p>
                                <p class="mb-2"><strong>Email:</strong> <span id="detailEmail"></span></p>
                                <p class="mb-2"><strong>Phone:</strong> <span id="detailPhone"></span></p>
                                <p class="mb-2"><strong>Subject:</strong> <span id="detailSubject"></span></p>
                                <p class="mb-2"><strong>Sent:</strong> <span id="detailCreated"></span></p>
                                <div class="message-content mt-3 p-3 bg-light rounded border">
                                    <p class="mb-0" id="detailMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0 text-success">
                                    <i class="fa fa-user-shield me-1"></i>Admin Response
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="replySection">
                                    <p class="mb-2"><strong>Replied:</strong> <span id="detailReplied"></span></p>
                                    <div class="reply-content mt-3 p-3 bg-success bg-opacity-10 rounded border border-success">
                                        <p class="mb-0" id="detailReply"></p>
                                    </div>
                                </div>
                                <div id="noReplySection" class="text-center py-4" style="display: none;">
                                    <i class="fa fa-clock fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No response yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="replyModalLabel">
                    <i class="fa fa-reply me-2"></i>Reply to Customer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="replyForm" action="{{ route('admin.contact.reply') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">To:</label>
                                <input type="text" class="form-control" id="replyTo" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subject:</label>
                                <input type="text" class="form-control" id="replySubject" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="replyMessage" class="form-label fw-semibold">
                            Your Response <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="replyMessage" name="reply_message" rows="8" 
                                  placeholder="Type your response here..." required></textarea>
                        <div class="form-text">Your response will be saved in the message history.</div>
                    </div>
                    <input type="hidden" id="replyMessageId" name="message_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="sendReplyBtn">
                        <i class="fa fa-paper-plane me-2"></i>Send Response
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
$(document).ready(function() {
    // View Message Modal
    $('.view-message').on('click', function() {
        const name = $(this).data('message-name');
        const email = $(this).data('message-email');
        const phone = $(this).data('message-phone');
        const subject = $(this).data('message-subject');
        const messageContent = $(this).data('message-content');
        const created = $(this).data('message-created');
        const reply = $(this).data('message-reply');
        const replied = $(this).data('message-replied');
        
        // Populate modal with data
        $('#detailName').text(name);
        $('#detailEmail').text(email);
        $('#detailPhone').text(phone || 'N/A');
        $('#detailSubject').text(subject);
        $('#detailMessage').text(messageContent);
        $('#detailCreated').text(new Date(created).toLocaleString());
        
        if (reply) {
            $('#detailReply').text(reply);
            $('#detailReplied').text(replied ? new Date(replied).toLocaleString() : 'N/A');
            $('#replySection').show();
            $('#noReplySection').hide();
        } else {
            $('#replySection').hide();
            $('#noReplySection').show();
        }
    });

    // Reply Modal
    $('.reply-message').on('click', function() {
        const messageId = $(this).data('message-id');
        const customerEmail = $(this).data('customer-email');
        const customerName = $(this).data('customer-name');
        const subject = $(this).data('subject');
        
        $('#replyTo').val(`${customerName} <${customerEmail}>`);
        $('#replySubject').val(`Re: ${subject}`);
        $('#replyMessageId').val(messageId);
        $('#replyMessage').val(''); // Clear previous content
    });

    // Reply form submission
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#sendReplyBtn');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fa fa-spinner fa-spin me-2"></i>Sending...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        $('#replyModal').modal('hide');
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while sending the reply.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });

    // Delete confirmation
    $('form[action*="destroy"]').on('submit', function(e) {
        if (!confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>

<style>
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.table > :not(caption) > * > * {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.empty-state-icon {
    opacity: 0.6;
}

.message-content, .reply-content {
    font-size: 0.9rem;
    line-height: 1.5;
}

.bg-warning { background-color: #ffc107 !important; }
.bg-info { background-color: #0dcaf0 !important; }
.bg-success { background-color: #198754 !important; }

.dropdown-item.active {
    background-color: #0d6efd;
    color: white;
}

.quick-filter-btn {
    min-width: 120px;
}
</style>
@endsection