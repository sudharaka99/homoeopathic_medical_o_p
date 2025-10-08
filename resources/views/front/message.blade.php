
@if(session('success'))
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