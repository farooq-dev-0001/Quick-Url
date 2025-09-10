@extends('layouts.app')

@section('title', 'Dashboard - Quick URL')

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
<style>
    body {
        background: #f8fafc !important;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.15);
    }
    
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        border: none;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .stats-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .stats-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
        margin-bottom: 10px;
    }
    
    .stats-icon.total-urls { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .stats-icon.total-clicks { background: linear-gradient(135deg, #10b981, #059669); }
    .stats-icon.urls-today { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stats-icon.avg-clicks { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
    
    .main-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
        border: none;
        overflow: hidden;
    }
    
    .main-card .card-header {
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb !important;
        padding: 12px 15px;
    }
    
    .main-card .card-header h4 {
        margin: 0;
        font-weight: 600;
    }
    
    .main-card .card-body {
        padding: 0;
    }
    
    .top-urls-slider {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 15px;
    }
    
    .swiper {
        width: 100%;
        height: 120px;
    }
    
    .swiper-slide {
        background: linear-gradient(135deg, #8b5cf6, #a855f7);
        border-radius: 8px;
        padding: 12px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .swiper-slide::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        transform: translate(12px, -12px);
    }
    
    .swiper-slide::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 25px;
        height: 25px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        transform: translate(-8px, 8px);
    }
    
    .table-container {
        width: 100%;
        overflow-x: auto;
        padding: 15px;
    }
    
    .dataTables_wrapper {
        padding: 0;
    }
    
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 20px;
    }
    
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }
    
    .dataTables_wrapper .dataTables_info {
        padding-top: 15px;
        margin: 0;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
        margin: 0;
    }
    
    .page-item.active .page-link {
        background-color: #0ea5e9;
        border-color: #0ea5e9;
    }
    
    /* Table Responsive Improvements */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        border-top: none;
        padding: 10px 6px;
        font-weight: 600;
        background-color: #f8fafc;
        border-bottom: 2px solid #e5e7eb;
        white-space: nowrap;
        font-size: 0.85rem;
    }
    
    .table td {
        padding: 10px 6px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    /* Button Group Responsive */
    .btn-group-sm > .btn {
        padding: 6px 10px;
        font-size: 0.875rem;
        margin: 1px;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .action-buttons .btn {
        min-width: 28px;
        height: 28px;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    
    /* Header Button Improvements */
    .header-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .header-buttons .btn {
        margin: 2px;
        white-space: nowrap;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .main-card .card-header {
            padding: 12px 15px;
            flex-direction: column;
            gap: 10px;
        }
        
        .main-card .card-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }
        
        .header-buttons {
            width: 100%;
            justify-content: flex-start;
        }
        
        .table-container {
            padding: 15px;
        }
        
        .table th,
        .table td {
            padding: 8px 6px;
            font-size: 0.8rem;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 3px;
        }
        
        .action-buttons .btn {
            min-width: 28px;
            height: 28px;
            padding: 4px;
        }
        
        .stats-card {
            margin-bottom: 15px;
        }
        
        .dashboard-header {
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .dashboard-header .d-flex {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start !important;
        }
    }
    
    @media (max-width: 576px) {
        .container-fluid {
            padding: 0 15px;
        }
        
        .table th:nth-child(3),
        .table td:nth-child(3) {
            display: none; /* Hide Original URL column on small screens */
        }
        
        .table th:nth-child(4),
        .table td:nth-child(4) {
            display: none; /* Hide User column on small screens */
        }
    }
    
    /* Copy Button Styling */
    .copy-btn {
        transition: all 0.2s ease;
    }
    
    .copy-btn:hover {
        background-color: #0ea5e9 !important;
        color: white !important;
        border-color: #0ea5e9 !important;
    }
    
    /* Badge Styling */
    .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
    }
    
    /* Swiper Customization */
    .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.5) !important;
        opacity: 0.7 !important;
    }
    
    .swiper-pagination-bullet-active {
        background: white !important;
        opacity: 1 !important;
    }
    
    /* Compact margins */
    .container-fluid {
        padding: 0 15px;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 10px;
        }
    }
    
    /* Additional compact styles */
    .btn-sm {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    h1 {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 8px;
        margin: 0;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">
                    <i class="fas fa-tachometer-alt me-3"></i>
                    Dashboard
                </h1>
                <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Here's your URL management overview.</p>
            </div>
            <a href="{{ route('home') }}" class="btn btn-light btn-lg">
                <i class="fas fa-plus me-2"></i>
                Create New URL
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-3">
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="stats-card">
                <div class="stats-icon total-urls">
                    <i class="fas fa-link"></i>
                </div>
                <h5 class="fw-bold mb-1" id="totalUrls">{{ $stats['total_urls'] }}</h5>
                <p class="text-muted mb-0" style="font-size: 0.8rem;">Total URLs</p>
                <small class="text-success" style="font-size: 0.7rem;"><i class="fas fa-arrow-up me-1"></i>All Users</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="stats-card">
                <div class="stats-icon total-clicks">
                    <i class="fas fa-mouse-pointer"></i>
                </div>
                <h5 class="fw-bold mb-1" id="totalClicks">{{ $stats['total_clicks'] }}</h5>
                <p class="text-muted mb-0" style="font-size: 0.8rem;">Total Clicks</p>
                <small class="text-success" style="font-size: 0.7rem;"><i class="fas fa-arrow-up me-1"></i>All URLs</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="stats-card">
                <div class="stats-icon urls-today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h5 class="fw-bold mb-1" id="urlsToday">{{ $stats['urls_today'] }}</h5>
                <p class="text-muted mb-0" style="font-size: 0.8rem;">URLs Today</p>
                <small class="text-info" style="font-size: 0.7rem;"><i class="fas fa-clock me-1"></i>Last 24h</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="stats-card">
                <div class="stats-icon avg-clicks">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h5 class="fw-bold mb-1" id="avgClicks">{{ $stats['total_urls'] > 0 ? round($stats['total_clicks'] / $stats['total_urls'], 1) : 0 }}</h5>
                <p class="text-muted mb-0" style="font-size: 0.8rem;">Avg. Clicks</p>
                <small class="text-info" style="font-size: 0.7rem;"><i class="fas fa-calculator me-1"></i>Per URL</small>
            </div>
        </div>
    </div>

    <!-- Top URLs Slider -->
    <div class="top-urls-slider">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">
                <i class="fas fa-star me-2 text-warning"></i>
                Top 10 Performing URLs
            </h6>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshTopUrls()" style="font-size: 0.75rem; padding: 4px 8px;">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
        
        <div class="swiper topUrlsSwiper">
            <div class="swiper-wrapper" id="topUrlsSlider">
                @if($stats['top_urls']->count() > 0)
                    @foreach($stats['top_urls']->take(10) as $url)
                    <div class="swiper-slide">
                        <div class="d-flex align-items-center h-100">
                            <div class="me-2">
                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <strong style="font-size: 0.7rem;">#{{ $loop->iteration }}</strong>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0" style="font-size: 0.8rem;">{{ $url->title ?: 'Untitled' }}</h6>
                                <p class="mb-0 opacity-75" style="font-size: 0.7rem;">{{ $url->clicks }} clicks</p>
                                <small class="opacity-50" style="font-size: 0.65rem;">
                                    @if($url->user)
                                        by {{ $url->user->name }}
                                    @else
                                        Guest User
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="swiper-slide">
                        <div class="text-center">
                            <i class="fas fa-chart-line mb-3" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="mb-0 opacity-75">No URLs created yet</p>
                        </div>
                    </div>
                @endif
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
    
    <!-- URL Management Table - Full Width -->
    <div class="main-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0">
            <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;">
                <i class="fas fa-list me-2 text-primary"></i>
                All URLs Management
            </h6>
            <div class="header-buttons">
                <button class="btn btn-success btn-sm" onclick="exportUrls()" style="font-size: 0.75rem; padding: 4px 8px;">
                    <i class="fas fa-download me-1"></i>
                    Export
                </button>
                <button class="btn btn-primary btn-sm" onclick="refreshUrls()" style="font-size: 0.75rem; padding: 4px 8px;">
                    <i class="fas fa-sync-alt me-1"></i>
                    Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table table-hover" id="urlsTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Short URL</th>
                            <th>Original URL</th>
                            <th>User</th>
                            <th>Clicks</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit URL Modal -->
<div class="modal fade" id="editUrlModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit URL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUrlForm">
                <div class="modal-body">
                    <input type="hidden" id="editUrlId">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="editExpiresAt" class="form-label">Expires At</label>
                        <input type="datetime-local" class="form-control" id="editExpiresAt" name="expires_at">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="loading spinner-border spinner-border-sm me-2"></span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
let urlsTable;
let topUrlsSwiper;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Initialize Swiper
    initializeSwiper();
    
    // Set up real-time refresh
    setupRealTimeRefresh();
    
    // Edit URL form submission
    $('#editUrlForm').on('submit', function(e) {
        e.preventDefault();
        
        const urlId = $('#editUrlId').val();
        const submitBtn = $(this).find('button[type="submit"]');
        showLoading(submitBtn);
        
        $.ajax({
            url: `/admin/urls/${urlId}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editUrlModal').modal('hide');
                    urlsTable.ajax.reload();
                    refreshStats();
                    Swal.fire('Success!', response.message, 'success');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                hideLoading(submitBtn);
            }
        });
    });
});

function initializeDataTable() {
    urlsTable = $('#urlsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.urls.datatable") }}',
            type: 'GET'
        },
        columns: [
            {
                data: 'title',
                name: 'title',
                render: function(data, type, row) {
                    return `
                        <div>
                            <strong>${data || 'Untitled'}</strong>
                        </div>
                    `;
                }
            },
            {
                data: 'short_code',
                name: 'short_code',
                render: function(data, type, row) {
                    const shortUrl = `${window.location.origin}/${data}`;
                    return `
                        <div class="d-flex align-items-center">
                            <code class="me-2">${shortUrl}</code>
                            <button class="btn btn-sm btn-outline-primary copy-btn" 
                                    onclick="copyToClipboard('${shortUrl}', this)" title="Copy URL">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    `;
                }
            },
            {
                data: 'original_url',
                name: 'original_url',
                render: function(data, type, row) {
                    return `<small class="text-muted text-break">${data.substring(0, 80)}${data.length > 80 ? '...' : ''}</small>`;
                }
            },
            {
                data: 'user',
                name: 'user.name',
                render: function(data, type, row) {
                    if (data) {
                        return `
                            <div>
                                <strong>${data.name}</strong>
                                <br><small class="text-muted">${data.email}</small>
                            </div>
                        `;
                    } else {
                        return `
                            <span class="text-muted">
                                <i class="fas fa-user-slash me-1"></i>
                                Guest User
                            </span>
                        `;
                    }
                }
            },
            {
                data: 'clicks',
                name: 'clicks',
                render: function(data, type, row) {
                    return `<span class="badge bg-primary rounded-pill">${data}</span>`;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    return `<small>${formatDate(data)}</small>`;
                }
            },
            {
                data: 'expires_at',
                name: 'expires_at',
                render: function(data, type, row) {
                    if (data && new Date(data) < new Date()) {
                        return '<span class="badge bg-danger">Expired</span>';
                    } else if (data) {
                        return '<span class="badge bg-warning">Expires Soon</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn btn-outline-info btn-sm" onclick="viewStats('${row.short_code}')" title="View Stats">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="editUrl(${data})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteUrl(${data})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        responsive: true,
        scrollX: true,
        autoWidth: false,
        columnDefs: [
            { 
                targets: [2, 3], 
                className: 'd-none d-md-table-cell',
                responsivePriority: 1
            },
            { 
                targets: [7], 
                className: 'text-center',
                width: '120px'
            },
            { 
                targets: [4, 6], 
                className: 'text-center'
            }
        ],
        language: {
            search: "Search URLs:",
            lengthMenu: "Show _MENU_ URLs per page",
            info: "Showing _START_ to _END_ of _TOTAL_ URLs",
            infoEmpty: "No URLs found",
            infoFiltered: "(filtered from _MAX_ total URLs)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
}

function initializeSwiper() {
    topUrlsSwiper = new Swiper('.topUrlsSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
        },
    });
}

function setupRealTimeRefresh() {
    // Refresh stats every 30 seconds
    setInterval(function() {
        refreshStats();
    }, 30000);
    
    // Refresh DataTable every 60 seconds
    setInterval(function() {
        if (urlsTable) {
            urlsTable.ajax.reload(null, false);
        }
    }, 60000);
}

function refreshUrls() {
    if (urlsTable) {
        urlsTable.ajax.reload();
    }
    Swal.fire({
        icon: 'success',
        title: 'Refreshed!',
        text: 'URL table has been refreshed',
        timer: 1500,
        showConfirmButton: false
    });
}

function refreshTopUrls() {
    $.ajax({
        url: '{{ route("admin.top-urls") }}',
        success: function(response) {
            if (response.success) {
                updateTopUrlsSlider(response.data);
                Swal.fire({
                    icon: 'success',
                    title: 'Refreshed!',
                    text: 'Top URLs have been refreshed',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }
    });
}

function updateTopUrlsSlider(topUrls) {
    const slider = $('#topUrlsSlider');
    slider.empty();
    
    if (topUrls.length > 0) {
        topUrls.forEach((url, index) => {
            const slide = `
                <div class="swiper-slide">
                    <div class="d-flex align-items-center h-100">
                        <div class="me-2">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <strong style="font-size: 0.7rem;">#${index + 1}</strong>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0" style="font-size: 0.8rem;">${url.title || 'Untitled'}</h6>
                            <p class="mb-0 opacity-75" style="font-size: 0.7rem;">${url.clicks} clicks</p>
                            <small class="opacity-50" style="font-size: 0.65rem;">
                                ${url.user ? `by ${url.user.name}` : 'Guest User'}
                            </small>
                        </div>
                    </div>
                </div>
            `;
            slider.append(slide);
        });
    } else {
        slider.append(`
            <div class="swiper-slide">
                <div class="text-center">
                    <i class="fas fa-chart-line mb-3" style="font-size: 2rem; opacity: 0.5;"></i>
                    <p class="mb-0 opacity-75">No URLs created yet</p>
                </div>
            </div>
        `);
    }
    
    topUrlsSwiper.update();
}

function viewStats(shortCode) {
    $.ajax({
        url: `/stats/${shortCode}`,
        success: function(response) {
            if (response.success) {
                const data = response.data;
                Swal.fire({
                    title: 'URL Statistics',
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
        }
    });
}

function editUrl(urlId) {
    // Get URL data and populate modal
    $.ajax({
        url: `/admin/urls/${urlId}/edit`,
        success: function(response) {
            if (response.success) {
                const url = response.data;
                $('#editUrlId').val(url.id);
                $('#editTitle').val(url.title);
                $('#editExpiresAt').val(url.expires_at ? url.expires_at.slice(0, 16) : '');
                $('#editUrlModal').modal('show');
            }
        }
    });
}

function deleteUrl(urlId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/urls/${urlId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        urlsTable.ajax.reload();
                        refreshStats();
                        refreshTopUrls();
                        Swal.fire('Deleted!', response.message, 'success');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to delete URL', 'error');
                }
            });
        }
    });
}

function refreshStats() {
    $.ajax({
        url: '{{ route("admin.stats") }}',
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalUrls').text(stats.total_urls);
                $('#totalClicks').text(stats.total_clicks);
                $('#urlsToday').text(stats.urls_today);
                $('#avgClicks').text(stats.total_urls > 0 ? (stats.total_clicks / stats.total_urls).toFixed(1) : 0);
            }
        }
    });
}

function exportUrls() {
    Swal.fire({
        title: 'Export URLs',
        html: `
            <div class="mb-3">
                <label for="exportFormat" class="form-label">Choose export format:</label>
                <select class="form-select" id="exportFormat">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    <option value="json">JSON</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const format = document.getElementById('exportFormat').value;
            return format;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const format = result.value;
            window.location.href = `/admin/urls/export?format=${format}`;
            
            Swal.fire({
                icon: 'success',
                title: 'Export Started!',
                text: 'Your file will download shortly.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Auto-refresh when new URLs are created (WebSocket or EventSource can be added here)
function setupUrlCreatedListener() {
    // This can be implemented with WebSocket or EventSource for real-time updates
    // For now, we'll use periodic refresh
}
</script>
@endpush
