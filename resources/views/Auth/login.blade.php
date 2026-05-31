<!-- filepath: c:\Users\Bishop\School\resources\views\auth\register.blade.php -->
@extends('layouts.navbar')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://accounts.google.com/gsi/client" async defer></script>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-yellow-600 mb-6">Welcome back</h2>
        
        <!-- Google Sign-In Button -->
        <div id="g_id_onload"
             data-client_id="494344827374-72gibtcdosc950qqptavgp12nj228osn.apps.googleusercontent.com"
             data-callback="handleCredentialResponse">
        </div>
        <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline"></div>

        <div class="mt-4 text-center text-gray-600">
             Don't have an account?
            <a href="{{ route('register') }}" class="text-yellow-600 hover:underline">Register</a>
        </div>
    </div>
</div>
@endsection

<script>
    function handleCredentialResponse(response) {
        // Send token to your backend
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("google.verify") }}';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = 'token';
        tokenInput.value = response.credential;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        form.appendChild(tokenInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
</script>


