<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Quick URL - URL Shortener')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0ea5e9;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #0ea5e9;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            padding-top: 80px; /* Account for fixed navbar */
        }
        
        /* Navbar scroll effect */
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* User avatar hover effect */
        .user-avatar {
            transition: all 0.3s ease;
        }
        
        .dropdown:hover .user-avatar {
            transform: scale(1.1);
        }
        
        /* Notification badge for future use */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Active link indicator */
        .nav-link.active::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            border-radius: 50%;
        }
        
        /* Mobile navbar improvements */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .navbar-nav .nav-link {
                background: rgba(14, 165, 233, 0.03);
                margin: 0.1rem 0;
                border-radius: 10px;
            }
            
            .navbar-nav .nav-link:hover {
                background: rgba(14, 165, 233, 0.1);
            }
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
        }
        
        .navbar {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.95));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(14, 165, 233, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            text-decoration: none !important;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .navbar-brand i {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.3rem;
            margin-right: 0.5rem;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: #374151 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 0.2rem;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover {
            color: #0ea5e9 !important;
            background: rgba(14, 165, 233, 0.08);
            transform: translateY(-1px);
        }
        
        .navbar-nav .nav-link.active {
            color: #0ea5e9 !important;
            background: rgba(14, 165, 233, 0.1);
        }
        
        .navbar-nav .dropdown-toggle::after {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-nav .dropdown:hover .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            padding: 0.5rem;
            margin-top: 0.5rem;
            min-width: 200px;
        }
        
        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-weight: 500;
            color: #374151;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white !important;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 0.5rem;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid rgba(14, 165, 233, 0.2);
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler:hover {
            background: rgba(14, 165, 233, 0.1);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2814, 165, 233, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        @media (max-width: 768px) {
            .navbar-nav {
                padding-top: 1rem;
                padding-bottom: 0.5rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.2rem 0;
                padding: 0.6rem 1rem !important;
            }
            
            .dropdown-menu {
                background: rgba(248, 250, 252, 0.98);
                margin-top: 0.2rem;
            }
        }
        
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .badge {
            border-radius: 8px;
            padding: 6px 12px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .url-card {
            transition: all 0.3s ease;
        }
        
        .url-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .copy-btn {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .stats-card {
            background: white;
            color: #374151;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-rocket"></i>
                Quick URL
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>
                            Home
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    <i class="fas fa-user-circle text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt text-primary"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="fas fa-plus text-success"></i>
                                        Create URL
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="logout()">
                                        <i class="fas fa-sign-out-alt text-danger"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Login
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>
                                Register
                            </a>
                        </li> --}}
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="mt-4">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="text-center py-4 mt-5">
        <div class="container">
            <p class="text-white-50 mb-0">&copy; {{ date('Y') }} Quick URL. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Navbar scroll effect
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });
        
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if( target.length ) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });
        
        // Enhanced dropdown animations
        $('.dropdown').on('show.bs.dropdown', function() {
            $(this).find('.dropdown-menu').addClass('show').css({
                'animation': 'dropdownSlideIn 0.3s ease-out'
            });
        });
        
        $('.dropdown').on('hide.bs.dropdown', function() {
            $(this).find('.dropdown-menu').css({
                'animation': 'dropdownSlideOut 0.2s ease-in'
            });
        });
        
        // Add CSS animations
        $('<style>').prop('type', 'text/css').html(`
            @keyframes dropdownSlideIn {
                0% {
                    opacity: 0;
                    transform: translateY(-10px) scale(0.95);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            
            @keyframes dropdownSlideOut {
                0% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
                100% {
                    opacity: 0;
                    transform: translateY(-10px) scale(0.95);
                }
            }
            
            /* Pulse animation for brand icon */
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .navbar-brand:hover i {
                animation: pulse 0.6s ease-in-out;
            }
        `).appendTo('head');
        
        // Logout function
        function logout() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will be logged out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route("logout") }}')
                        .done(function(response) {
                            if (response.success) {
                                Swal.fire('Logged out!', response.message, 'success')
                                    .then(() => window.location.href = response.redirect);
                            }
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'Failed to logout', 'error');
                        });
                }
            });
        }
        
        // Copy to clipboard function
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = $(button).html();
                $(button).html('<i class="fas fa-check"></i> Copied!');
                setTimeout(() => {
                    $(button).html(originalText);
                }, 2000);
            });
        }
        
        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
        
        // Show loading state
        function showLoading(element) {
            $(element).find('.loading').addClass('show');
            $(element).prop('disabled', true);
        }
        
        // Hide loading state
        function hideLoading(element) {
            $(element).find('.loading').removeClass('show');
            $(element).prop('disabled', false);
        }
    </script>
    
    @stack('scripts')
</body>
</html>
