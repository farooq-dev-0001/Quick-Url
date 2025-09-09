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
        $user = Auth::user();
        $urls = $user->urls()->latest()->paginate(10);

        $stats = [
            'total_urls' => $user->urls()->count(),
            'total_clicks' => $user->urls()->sum('clicks'),
            'urls_today' => $user->urls()->whereDate('created_at', today())->count(),
            'top_urls' => $user->urls()->orderBy('clicks', 'desc')->limit(5)->get()
        ];

        return view('admin.dashboard', compact('urls', 'stats'));
    }

    public function getUrls(Request $request)
    {
        $user = Auth::user();
        $query = $user->urls();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('original_url', 'like', '%' . $request->search . '%')
                    ->orWhere('short_code', 'like', '%' . $request->search . '%');
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
        $user = Auth::user();
        $url = $user->urls()->findOrFail($id);

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

        $user = Auth::user();
        $url = $user->urls()->findOrFail($id);

        $url->update($request->only(['title', 'expires_at']));

        return response()->json([
            'success' => true,
            'message' => 'URL updated successfully',
            'data' => $url
        ]);
    }

    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'total_urls' => $user->urls()->count(),
            'total_clicks' => $user->urls()->sum('clicks'),
            'urls_today' => $user->urls()->whereDate('created_at', today())->count(),
            'urls_this_month' => $user->urls()->whereMonth('created_at', now()->month)->count(),
            'top_urls' => $user->urls()->select(['title', 'short_code', 'clicks', 'original_url'])
                ->orderBy('clicks', 'desc')
                ->limit(5)
                ->get(),
            'recent_urls' => $user->urls()->select(['title', 'short_code', 'clicks', 'created_at'])
                ->latest()
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
