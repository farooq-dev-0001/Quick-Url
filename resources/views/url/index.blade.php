@extends('layouts.app')

@section('title', 'Quick URL - Free URL Shortener')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Hero Section -->
            <div class="text-center mb-4">
                <h1 class="display-5 fw-bold mb-2" style="color: #4f46e5;">
                    <i class="fas fa-link text-warning me-2"></i>
                    Quick URL Shortener
                </h1>
                <p class="text-white-75" style="font-size: 1rem;">
                    Transform long URLs into short, shareable links in seconds. 
                    Track clicks and manage your links with our powerful dashboard.
                </p>
            </div>
            
            <!-- URL Shortener Form -->
            <div class="card mb-3">
                <div class="card-body p-3">
                    <form id="shortenForm">
                        <div class="mb-2">
                            <label for="url" class="form-label" style="font-size: 0.9rem; font-weight: 600;">
                                <i class="fas fa-globe me-1 text-primary"></i>
                                Enter your URL
                            </label>
                            <input type="url" class="form-control form-control-sm" id="url" name="url" 
                                   placeholder="https://example.com/very-long-url" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label for="prefix" class="form-label" style="font-size: 0.9rem; font-weight: 600;">
                                    <i class="fas fa-hashtag me-1 text-info"></i>
                                    Custom Prefix (Optional)
                                </label>
                                <input type="text" class="form-control form-control-sm" id="prefix" name="prefix" 
                                       placeholder="myprefix" maxlength="20" pattern="[a-zA-Z0-9_-]+">
                                <small class="text-muted" style="font-size: 0.75rem;">Only letters, numbers, hyphens, and underscores allowed</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="title" class="form-label" style="font-size: 0.9rem; font-weight: 600;">
                                    <i class="fas fa-tag me-1 text-success"></i>
                                    Custom Title (Optional)
                                </label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" 
                                       placeholder="My awesome link">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="expires_at" class="form-label" style="font-size: 0.9rem; font-weight: 600;">
                                    <i class="fas fa-clock me-1 text-warning"></i>
                                    Expires At (Optional)
                                </label>
                                <input type="datetime-local" class="form-control form-control-sm" id="expires_at" name="expires_at">
                            </div>
                        </div>
                        
                        <!-- Short Code Preview -->
                        <div id="shortCodePreview" class="alert alert-info mb-2 py-2" style="display: none; font-size: 0.85rem;">
                            <i class="fas fa-eye me-1"></i>
                            <strong>Preview:</strong> Your short URL will look like: 
                            <code id="previewUrl"></code>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <span class="loading spinner-border spinner-border-sm me-2"></span>
                                <i class="fas fa-magic me-1"></i>
                                Shorten URL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Result Card -->
            <div id="resultCard" class="card" style="display: none;">
                <div class="card-body p-3">
                    <h6 class="card-title text-success mb-2">
                        <i class="fas fa-check-circle me-1"></i>
                        URL Shortened Successfully!
                    </h6>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <small class="text-muted" style="font-size: 0.75rem;">Short URL:</small>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm" id="shortUrl" readonly>
                                    <button class="btn btn-outline-primary btn-sm copy-btn" type="button" 
                                            onclick="copyToClipboard($('#shortUrl').val(), this)">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted" style="font-size: 0.75rem;">Original URL:</small>
                                <p class="mb-0 text-break" id="originalUrl" style="font-size: 0.85rem;"></p>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted" style="font-size: 0.75rem;">Title:</small>
                                    <p class="mb-0" id="urlTitle" style="font-size: 0.85rem;"></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted" style="font-size: 0.75rem;">Created:</small>
                                    <p class="mb-0" id="createdAt" style="font-size: 0.85rem;"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-end">
                            <div class="stats-card text-center">
                                <div class="stats-icon mx-auto mb-1" style="width: 35px; height: 35px;">
                                    <i class="fas fa-mouse-pointer" style="font-size: 0.9rem;"></i>
                                </div>
                                <h5 class="mb-0" id="clickCount">0</h5>
                                <small style="font-size: 0.75rem;">Clicks</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2 d-flex gap-1">
                        <button class="btn btn-sm btn-info" onclick="showStats()" style="font-size: 0.7rem; padding: 3px 6px;">
                            <i class="fas fa-chart-bar me-1"></i>
                            View Stats
                        </button>
                        <button class="btn btn-sm btn-success" onclick="shareUrl()" style="font-size: 0.7rem; padding: 3px 6px;">
                            <i class="fas fa-share-alt me-1"></i>
                            Share
                        </button>
                        @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary" style="font-size: 0.7rem; padding: 3px 6px;">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Features Section -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body p-3">
                            <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #10b981, #059669); width: 40px; height: 40px;">
                                <i class="fas fa-bolt" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 style="font-size: 1rem;">Lightning Fast</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">Shorten URLs instantly with our optimized infrastructure.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body p-3">
                            <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #3b82f6, #2563eb); width: 40px; height: 40px;">
                                <i class="fas fa-chart-line" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 style="font-size: 1rem;">Analytics</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">Track clicks and analyze your link performance with detailed statistics.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body p-3">
                            <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 40px; height: 40px;">
                                <i class="fas fa-shield-alt" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 style="font-size: 1rem;">Secure</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">Your links are safe with our advanced security measures and monitoring.</p>
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
    let currentShortCode = null;
    
    // Prefix validation
    $('#prefix').on('input', function() {
        const prefix = $(this).val();
        const regex = /^[a-zA-Z0-9_-]*$/;
        
        if (prefix && !regex.test(prefix)) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Only letters, numbers, hyphens, and underscores are allowed</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
        
        // Show preview of short code
        if (prefix && regex.test(prefix)) {
            updateShortCodePreview(prefix);
        } else {
            $('#shortCodePreview').hide();
        }
    });
    
    // Form submission
    $('#shortenForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        showLoading(submitBtn);
        
        $.ajax({
            url: '{{ route("url.shorten") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    currentShortCode = response.data.short_code;
                    displayResult(response.data);
                    
                    // Check if this is an existing URL or a new one
                    const isExisting = response.message.includes('already exists');
                    
                    Swal.fire({
                        icon: isExisting ? 'info' : 'success',
                        title: isExisting ? 'URL Found!' : 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join('<br>');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: message
                });
            },
            complete: function() {
                hideLoading(submitBtn);
            }
        });
    });
    
    function displayResult(data) {
        $('#shortUrl').val(data.short_url);
        $('#originalUrl').text(data.original_url);
        $('#urlTitle').text(data.title || 'No title');
        $('#createdAt').text(data.created_at);
        $('#clickCount').text(data.clicks);
        $('#resultCard').fadeIn();
        
        // Scroll to result
        $('html, body').animate({
            scrollTop: $('#resultCard').offset().top - 100
        }, 500);
    }
    
    // Show stats function
    window.showStats = function() {
        if (!currentShortCode) return;
        
        $.ajax({
            url: `/stats/${currentShortCode}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    Swal.fire({
                        title: 'Link Statistics',
                        html: `
                            <div class="text-start">
                                <p><strong>Title:</strong> ${data.title}</p>
                                <p><strong>Short URL:</strong> <a href="${data.short_url}" target="_blank">${data.short_url}</a></p>
                                <p><strong>Original URL:</strong> <br><small class="text-break">${data.original_url}</small></p>
                                <p><strong>Total Clicks:</strong> ${data.clicks}</p>
                                <p><strong>Created:</strong> ${data.created_at}</p>
                                ${data.expires_at ? `<p><strong>Expires:</strong> ${data.expires_at}</p>` : ''}
                                ${data.is_expired ? '<p class="text-danger"><strong>Status:</strong> Expired</p>' : '<p class="text-success"><strong>Status:</strong> Active</p>'}
                            </div>
                        `,
                        width: 600
                    });
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load statistics', 'error');
            }
        });
    };
    
    // Share function
    window.shareUrl = function() {
        const shortUrl = $('#shortUrl').val();
        const title = $('#urlTitle').text();
        
        if (navigator.share) {
            navigator.share({
                title: title,
                url: shortUrl
            });
        } else {
            // Fallback: copy to clipboard
            copyToClipboard(shortUrl, $('.copy-btn')[0]);
            Swal.fire({
                icon: 'info',
                title: 'URL Copied!',
                text: 'The short URL has been copied to your clipboard.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    };
});

function updateShortCodePreview(prefix) {
    const baseUrl = window.location.origin;
    const previewUrl = `${baseUrl}/${prefix}-XXXXXX`;
    $('#previewUrl').text(previewUrl);
    $('#shortCodePreview').show();
}
</script>
@endpush
