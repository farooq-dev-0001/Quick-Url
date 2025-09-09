@extends('layouts.app')

@section('title', 'Login - Quick URL')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary mb-2">Welcome Back!</h2>
                        <p class="text-muted">Sign in to your account to manage your URLs</p>
                    </div>
                    
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-primary"></i>
                                Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="passwordToggle"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span class="loading spinner-border spinner-border-sm me-2"></span>
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Sign In
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? 
                            <a href="{{ route('register') }}" class="text-primary fw-semibold">Sign up here</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Features -->
            <div class="row mt-4">
                <div class="col-4 text-center">
                    <div class="text-white">
                        <i class="fas fa-tachometer-alt fa-2x mb-2"></i>
                        <h6>Dashboard</h6>
                        <small>Manage all your URLs</small>
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="text-white">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <h6>Analytics</h6>
                        <small>Track click statistics</small>
                    </div>
                </div>
                <div class="col-4 text-center">
                    <div class="text-white">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <h6>History</h6>
                        <small>View all your links</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        showLoading(submitBtn);
        
        $.ajax({
            url: '{{ route("login") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome back!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                let message = 'Login failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: message
                });
            },
            complete: function() {
                hideLoading(submitBtn);
            }
        });
    });
});

function togglePassword() {
    const passwordField = $('#password');
    const passwordToggle = $('#passwordToggle');
    
    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        passwordToggle.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        passwordToggle.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}
</script>
@endpush
