@extends('front.layouts.app')

@section('main')

<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.doctor.slidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')

                <!-- Profile Update Section -->
                <div class="card border-0 shadow mb-4">
                    <form action="{{route('account.updateProfile')}}" method="POST" id="userForm" name="userForm">
                        @csrf
                        @method('PUT')
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">My Profile</h3>
                            <div class="mb-4">
                                <label for="name" class="mb-2">Name*</label>
                                <input type="text" name="name" id="name" placeholder="Enter Name" class="form-control" value="{{$user->name}}">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="mb-2">Email*</label>
                                <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="{{$user->email}}">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="designation" class="mb-2">Designation</label>
                                <input type="text" name="designation" id="designation" placeholder="Designation" class="form-control" value="{{$user->designation}}">
                            </div>
                            <div class="mb-4">
                                <label for="mobile" class="mb-2">Mobile</label>
                                <input type="text" name="mobile" id="mobile" placeholder="Mobile" class="form-control" value="{{$user->mobile}}">
                            </div>                        
                        </div>
                        <div class="card-footer p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Section -->
                <div class="card border-0 shadow mb-4">
                    <form action="{{route('account.updatePassword')}}" method="POST" id="changePasswordForm" name="changePasswordForm">
                        @csrf
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">Change Password</h3>
                            <div class="mb-4">
                                <label for="old_password" class="mb-2">Old Password*</label>
                                <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="new_password" class="mb-2">New Password*</label>
                                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="mb-2">Confirm Password*</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="form-control">
                                <p></p>
                            </div>                        
                        </div>
                        <div class="card-footer p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

               


</section>

@endsection

@section('customJS')
<script type="text/javascript">
$("#userForm").submit(function(e){
    e.preventDefault();

    $.ajax({
        url: '{{route("account.updateProfile")}}',
        type: 'PUT',
        dataType: 'json',
        data: $("#userForm").serializeArray(),
        success: function(response) {
            if(response.status == true) {
                window.location.href = "{{route('account.profile')}}";
            } else {
                handleErrors(response.errors);
            }
        },
        error: function(response) {
            console.log("Error during request:", response);
        }
    });
});

$("#changePasswordForm").submit(function(e){
    e.preventDefault();

    $.ajax({
        url: '{{route("account.updatePassword")}}',
        type: 'POST',
        dataType: 'json',
        data: $("#changePasswordForm").serializeArray(),
        success: function(response) {
            if(response.status == true) {
                window.location.href = "{{route('account.profile')}}";
            } else {
                handleErrors(response.errors);
            }
        },
        error: function(response) {
            console.log("Error during request:", response);
        }
    });
});



function handleErrors(errors) {
    for (let key in errors) {
        if (errors.hasOwnProperty(key)) {
            let inputField = $("#" + key);
            inputField.addClass('is-invalid')
                .siblings('p')
                .addClass('invalid-feedback')
                .html(errors[key]);
        }
    }
}
</script>
@endsection
