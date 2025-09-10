@extends('layouts.app')

@section('title', 'Quick URL - Free URL Shortener')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Hero Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="fas fa-link text-warning me-3"></i>
                    Quick URL Shortener
                </h1>
                <p class="lead text-white-75">
                    Transform long URLs into short, shareable links in seconds. 
                    Track clicks and manage your links with our powerful dashboard.
                </p>
            </div>
            
            <!-- URL Shortener Form -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <form id="shortenForm">
                        <div class="mb-3">
                            <label for="url" class="form-label fw-semibold">
                                <i class="fas fa-globe me-2 text-primary"></i>
                                Enter your URL
                            </label>
                            <input type="url" class="form-control" id="url" name="url" 
                                   placeholder="https://example.com/very-long-url" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="prefix" class="form-label fw-semibold">
                                    <i class="fas fa-hashtag me-2 text-info"></i>
                                    Custom Prefix (Optional)
                                </label>
                                <input type="text" class="form-control" id="prefix" name="prefix" 
                                       placeholder="myprefix" maxlength="20" pattern="[a-zA-Z0-9_-]+">
                                <small class="text-muted">Only letters, numbers, hyphens, and underscores allowed</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="title" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-2 text-success"></i>
                                    Custom Title (Optional)
                                </label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="My awesome link">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="expires_at" class="form-label fw-semibold">
                                    <i class="fas fa-clock me-2 text-warning"></i>
                                    Expires At (Optional)
                                </label>
                                <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                            </div>
                        </div>
                        
                        <!-- Short Code Preview -->
                        <div id="shortCodePreview" class="alert alert-info mb-3" style="display: none;">
                            <i class="fas fa-eye me-2"></i>
                            <strong>Preview:</strong> Your short URL will look like: 
                            <code id="previewUrl"></code>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span class="loading spinner-border spinner-border-sm me-2"></span>
                                <i class="fas fa-magic me-2"></i>
                                Shorten URL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Result Card -->
            <div id="resultCard" class="card" style="display: none;">
                <div class="card-body p-4">
                    <h5 class="card-title text-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        URL Shortened Successfully!
                    </h5>
                    
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <small class="text-muted">Short URL:</small>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="shortUrl" readonly>
                                    <button class="btn btn-outline-primary copy-btn" type="button" 
                                            onclick="copyToClipboard($('#shortUrl').val(), this)">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">Original URL:</small>
                                <p class="mb-0 text-break" id="originalUrl"></p>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Title:</small>
                                    <p class="mb-0" id="urlTitle"></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Created:</small>
                                    <p class="mb-0" id="createdAt"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-end">
                            <div class="stats-card text-center">
                                <div class="stats-icon mx-auto mb-2">
                                    <i class="fas fa-mouse-pointer"></i>
                                </div>
                                <h4 class="mb-1" id="clickCount">0</h4>
                                <small>Clicks</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-info" onclick="showStats()">
                            <i class="fas fa-chart-bar me-1"></i>
                            View Stats
                        </button>
                        <button class="btn btn-sm btn-success" onclick="shareUrl()">
                            <i class="fas fa-share-alt me-1"></i>
                            Share
                        </button>
                        @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Features Section -->
            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="stats-icon mx-auto mb-3" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h5>Lightning Fast</h5>
                            <p class="text-muted">Shorten URLs instantly with our optimized infrastructure.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="stats-icon mx-auto mb-3" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5>Analytics</h5>
                            <p class="text-muted">Track clicks and analyze your link performance with detailed statistics.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="stats-icon mx-auto mb-3" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5>Secure</h5>
                            <p class="text-muted">Your links are safe with our advanced security measures and monitoring.</p>
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
