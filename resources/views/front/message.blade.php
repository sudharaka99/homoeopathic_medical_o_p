
@if(session('success'))
    {{-- <div class="alert alert-success">{{ session('success') }}</div> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: 'Success!',
            text: {!! json_encode(session('success')) !!},
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif
@if(session('error'))
    {{-- <div class="alert alert-danger">{{ session('error') }}</div> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: 'Error!',
            text: {!! json_encode(session('error')) !!},
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif