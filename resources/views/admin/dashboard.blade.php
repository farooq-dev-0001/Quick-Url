@extends('layouts.app')

@section('title', 'Dashboard - Quick URL')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Page Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-white mb-1">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        Dashboard
                    </h1>
                    <p class="text-white-75 mb-0">Welcome back, {{ Auth::user()->name }}! Here's the global overview.</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-light">
                    <i class="fas fa-plus me-2"></i>
                    Create New URL
                </a>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" id="totalUrls">{{ $stats['total_urls'] }}</h4>
                        <small>Total URLs (All Users)</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" id="totalClicks">{{ $stats['total_clicks'] }}</h4>
                        <small>Total Clicks (All URLs)</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" id="urlsToday">{{ $stats['urls_today'] }}</h4>
                        <small>URLs Created Today</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" id="avgClicks">{{ $stats['total_urls'] > 0 ? round($stats['total_clicks'] / $stats['total_urls'], 1) : 0 }}</h4>
                        <small>Global Avg. Clicks</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- URL Management -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        All URLs
                    </h5>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control form-control-sm" id="searchInput" 
                                   placeholder="Search URLs...">
                            <button class="btn btn-outline-secondary btn-sm" onclick="searchUrls()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="refreshUrls()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Short URL</th>
                                    <th>User</th>
                                    <th>Clicks</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="urlsTableBody">
                                @foreach($urls as $url)
                                <tr data-url-id="{{ $url->id }}">
                                    <td>
                                        <div>
                                            <strong>{{ $url->title ?: 'Untitled' }}</strong>
                                            <br>
                                            <small class="text-muted text-break">{{ Str::limit($url->original_url, 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <code class="me-2">{{ $url->getShortUrl() }}</code>
                                            <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                    onclick="copyToClipboard('{{ $url->getShortUrl() }}', this)">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        @if($url->user)
                                            <div>
                                                <strong>{{ $url->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $url->user->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>
                                                Guest User
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $url->clicks }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $url->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" onclick="viewStats('{{ $url->short_code }}')">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                            <button class="btn btn-outline-warning" onclick="editUrl({{ $url->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteUrl({{ $url->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer bg-white" id="pagination">
                        {{ $urls->links() }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top URLs Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-star me-2 text-warning"></i>
                        Top Performing URLs
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['top_urls']->count() > 0)
                        @foreach($stats['top_urls'] as $url)
                        <div class="d-flex align-items-center mb-3">
                            <div class="stats-icon me-3" style="width: 40px; height: 40px; font-size: 14px;">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $url->title ?: 'Untitled' }}</div>
                                <small class="text-muted">{{ $url->clicks }} clicks</small>
                                @if($url->user)
                                    <br><small class="text-info">by {{ $url->user->name }}</small>
                                @else
                                    <br><small class="text-muted">by Guest User</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No URLs yet</p>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2 text-success"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Create New URL
                        </a>
                        <button class="btn btn-info" onclick="refreshStats()">
                            <i class="fas fa-chart-bar me-2"></i>
                            Refresh Stats
                        </button>
                        <button class="btn btn-success" onclick="exportUrls()">
                            <i class="fas fa-download me-2"></i>
                            Export URLs
                        </button>
                    </div>
                </div>
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
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchUrls();
        }
    });
    
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
                    refreshUrls();
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

function searchUrls() {
    const search = $('#searchInput').val();
    // Implementation for search functionality
    refreshUrls(search);
}

function refreshUrls(search = '') {
    $.ajax({
        url: '{{ route("admin.urls") }}',
        data: { search: search },
        success: function(response) {
            if (response.success) {
                updateUrlsTable(response.data);
            }
        }
    });
}

function updateUrlsTable(urls) {
    const tbody = $('#urlsTableBody');
    tbody.empty();
    
    urls.forEach(url => {
        const userDisplay = url.user 
            ? `<div><strong>${url.user.name}</strong><br><small class="text-muted">${url.user.email}</small></div>`
            : `<span class="text-muted"><i class="fas fa-user-slash me-1"></i>Guest User</span>`;
            
        const row = `
            <tr data-url-id="${url.id}">
                <td>
                    <div>
                        <strong>${url.title || 'Untitled'}</strong>
                        <br>
                        <small class="text-muted text-break">${url.original_url.substring(0, 50)}${url.original_url.length > 50 ? '...' : ''}</small>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <code class="me-2">${window.location.origin}/${url.short_code}</code>
                        <button class="btn btn-sm btn-outline-primary copy-btn" 
                                onclick="copyToClipboard('${window.location.origin}/${url.short_code}', this)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </td>
                <td>
                    ${userDisplay}
                </td>
                <td>
                    <span class="badge bg-primary">${url.clicks}</span>
                </td>
                <td>
                    <small>${formatDate(url.created_at)}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="viewStats('${url.short_code}')">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editUrl(${url.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteUrl(${url.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
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
    const row = $(`tr[data-url-id="${urlId}"]`);
    $('#editUrlId').val(urlId);
    $('#editUrlModal').modal('show');
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
                        $(`tr[data-url-id="${urlId}"]`).fadeOut(function() {
                            $(this).remove();
                        });
                        refreshStats();
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
        text: 'This feature will be available soon!',
        icon: 'info'
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
</script>
@endpush
