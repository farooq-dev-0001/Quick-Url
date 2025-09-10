<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Url;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        // Show all URLs instead of just user's URLs
        $urls = Url::with('user')->latest()->paginate(10);

        $stats = [
            'total_urls' => Url::count(),
            'total_clicks' => Url::sum('clicks'),
            'urls_today' => Url::whereDate('created_at', today())->count(),
            'top_urls' => Url::with('user')->orderBy('clicks', 'desc')->limit(10)->get()
        ];

        return view('admin.dashboard', compact('urls', 'stats'));
    }

    public function getUrls(Request $request)
    {
        // Show all URLs instead of just user's URLs
        $query = Url::with('user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('original_url', 'like', '%' . $request->search . '%')
                    ->orWhere('short_code', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->sort_by) {
            $direction = $request->sort_direction === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort_by, $direction);
        } else {
            $query->latest();
        }

        $urls = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $urls->items(),
            'pagination' => [
                'current_page' => $urls->currentPage(),
                'last_page' => $urls->lastPage(),
                'per_page' => $urls->perPage(),
                'total' => $urls->total()
            ]
        ]);
    }

    public function deleteUrl($id)
    {
        // Allow admin to delete any URL
        $url = Url::findOrFail($id);

        $url->delete();

        return response()->json([
            'success' => true,
            'message' => 'URL deleted successfully'
        ]);
    }

    public function updateUrl(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now'
        ]);

        // Allow admin to update any URL
        $url = Url::findOrFail($id);

        $url->update($request->only(['title', 'expires_at']));

        return response()->json([
            'success' => true,
            'message' => 'URL updated successfully',
            'data' => $url
        ]);
    }

    public function getStats()
    {
        // Show global stats instead of user-specific
        $stats = [
            'total_urls' => Url::count(),
            'total_clicks' => Url::sum('clicks'),
            'urls_today' => Url::whereDate('created_at', today())->count(),
            'urls_this_month' => Url::whereMonth('created_at', now()->month)->count(),
            'top_urls' => Url::with('user')->select(['title', 'short_code', 'clicks', 'original_url', 'user_id'])
                ->orderBy('clicks', 'desc')
                ->limit(5)
                ->get(),
            'recent_urls' => Url::with('user')->select(['title', 'short_code', 'clicks', 'created_at', 'user_id'])
                ->latest()
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getDatatableUrls(Request $request)
    {
        $query = Url::with('user');

        // Handle DataTables search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('original_url', 'like', '%' . $searchValue . '%')
                    ->orWhere('short_code', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('user', function ($userQuery) use ($searchValue) {
                        $userQuery->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('email', 'like', '%' . $searchValue . '%');
                    });
            });
        }

        // Handle DataTables ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            $columns = ['title', 'short_code', 'original_url', 'user.name', 'clicks', 'created_at', 'expires_at'];

            if (isset($columns[$orderColumn])) {
                if ($columns[$orderColumn] === 'user.name') {
                    $query->join('users', 'urls.user_id', '=', 'users.id')
                        ->orderBy('users.name', $orderDirection)
                        ->select('urls.*');
                } else {
                    $query->orderBy($columns[$orderColumn], $orderDirection);
                }
            }
        } else {
            $query->latest();
        }

        // Get total count before pagination
        $totalCount = Url::count();
        $filteredCount = $query->count();

        // Handle DataTables pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $urls = $query->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $urls
        ]);
    }

    public function editUrl($id)
    {
        $url = Url::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $url
        ]);
    }

    public function getTopUrls()
    {
        $topUrls = Url::with('user')
            ->orderBy('clicks', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topUrls
        ]);
    }

    public function exportUrls(Request $request)
    {
        $format = $request->get('format', 'csv');
        $urls = Url::with('user')->get();

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($urls);
            case 'excel':
                return $this->exportToExcel($urls);
            case 'json':
                return $this->exportToJson($urls);
            default:
                return $this->exportToCsv($urls);
        }
    }

    private function exportToCsv($urls)
    {
        $filename = 'urls_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($urls) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Title', 'Short Code', 'Original URL', 'User', 'Clicks', 'Created At', 'Expires At']);

            foreach ($urls as $url) {
                fputcsv($file, [
                    $url->title ?: 'Untitled',
                    $url->short_code,
                    $url->original_url,
                    $url->user ? $url->user->name : 'Guest',
                    $url->clicks,
                    $url->created_at,
                    $url->expires_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToJson($urls)
    {
        $filename = 'urls_export_' . date('Y-m-d') . '.json';
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $data = $urls->map(function ($url) {
            return [
                'title' => $url->title ?: 'Untitled',
                'short_code' => $url->short_code,
                'original_url' => $url->original_url,
                'user' => $url->user ? $url->user->name : 'Guest',
                'clicks' => $url->clicks,
                'created_at' => $url->created_at,
                'expires_at' => $url->expires_at
            ];
        });

        return response()->json($data, 200, $headers);
    }

    private function exportToExcel($urls)
    {
        // For now, return CSV as Excel functionality would require additional packages
        return $this->exportToCsv($urls);
    }
}
