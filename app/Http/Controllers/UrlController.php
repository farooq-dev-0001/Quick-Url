<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Url;

class UrlController extends Controller
{
    public function index()
    {
        return view('url.index');
    }

    public function shorten(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048',
            'title' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now'
        ]);

        // Generate unique short code
        do {
            $shortCode = Str::random(6);
        } while (Url::where('short_code', $shortCode)->exists());

        // Get page title if not provided
        $title = $request->title;
        if (!$title) {
            $title = $this->extractTitle($request->url);
        }

        $url = Url::create([
            'original_url' => $request->url,
            'short_code' => $shortCode,
            'title' => $title,
            'user_id' => Auth::id(),
            'expires_at' => $request->expires_at
        ]);

        return response()->json([
            'success' => true,
            'message' => 'URL shortened successfully!',
            'data' => [
                'short_url' => $url->getShortUrl(),
                'original_url' => $url->original_url,
                'title' => $url->title,
                'short_code' => $url->short_code,
                'clicks' => $url->clicks,
                'created_at' => $url->created_at->format('M d, Y H:i')
            ]
        ]);
    }

    public function redirect($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->first();

        if (!$url) {
            abort(404, 'Short URL not found');
        }

        if ($url->isExpired()) {
            abort(410, 'This short URL has expired');
        }

        $url->incrementClicks();

        return redirect($url->original_url);
    }

    public function stats($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->first();

        if (!$url) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'short_url' => $url->getShortUrl(),
                'original_url' => $url->original_url,
                'title' => $url->title,
                'clicks' => $url->clicks,
                'created_at' => $url->created_at->format('M d, Y H:i'),
                'expires_at' => $url->expires_at ? $url->expires_at->format('M d, Y H:i') : null,
                'is_expired' => $url->isExpired()
            ]
        ]);
    }

    private function extractTitle($url)
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Quick URL Shortener Bot'
                ]
            ]);

            $html = file_get_contents($url, false, $context);

            if ($html && preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
                return trim(strip_tags($matches[1]));
            }
        } catch (\Exception $e) {
            // If we can't get the title, use the domain
        }

        return parse_url($url, PHP_URL_HOST) ?: 'Shortened URL';
    }
}
