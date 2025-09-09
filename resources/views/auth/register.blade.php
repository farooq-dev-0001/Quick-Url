@extends('layouts.app')

@section('title', 'Register - Quick URL')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary mb-2">Create Account</h2>
                        <p class="text-muted">Join us and start shortening URLs today!</p>
                    </div>
                    
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Full Name
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
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
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="passwordToggle"></i>
                                </button>
                            </div>
                            <small class="text-muted">Password must be at least 8 characters long</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-primary"></i>
                                Confirm Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="passwordConfirmToggle"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-primary">Terms of Service</a> and 
                                <a href="#" class="text-primary">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span class="loading spinner-border spinner-border-sm me-2"></span>
                                <i class="fas fa-user-plus me-2"></i>
                                Create Account
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? 
                            <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                        <div class="card-body text-white">
                            <h6 class="mb-3"><i class="fas fa-crown me-2 text-warning"></i>Member Benefits</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Unlimited URL shortening</li>
                                <li><i class="fas fa-check text-success me-2"></i>Detailed click analytics</li>
                                <li><i class="fas fa-check text-success me-2"></i>Custom expiration dates</li>
                                <li><i class="fas fa-check text-success me-2"></i>Link management dashboard</li>
                                <li><i class="fas fa-check text-success me-2"></i>Click history tracking</li>
                            </ul>
                        </div>
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
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check if passwords match
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        
        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'The passwords do not match. Please try again.'
            });
            return;
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        showLoading(submitBtn);
        
        $.ajax({
            url: '{{ route("register") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome to Quick URL!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                let message = 'Registration failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join('<br>');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    html: message
                });
            },
            complete: function() {
                hideLoading(submitBtn);
            }
        });
    });
    
    // Real-time password validation
    $('#password, #password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        
        if (confirmPassword && password !== confirmPassword) {
            $('#password_confirmation').addClass('is-invalid');
        } else {
            $('#password_confirmation').removeClass('is-invalid');
        }
    });
});

function togglePassword(fieldId) {
    const passwordField = $('#' + fieldId);
    const toggleIcon = fieldId === 'password' ? '#passwordToggle' : '#passwordConfirmToggle';
    
    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        $(toggleIcon).removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        $(toggleIcon).removeClass('fa-eye-slash').addClass('fa-eye');
    }
}
</script>
@endpush
