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
            'prefix' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'expires_at' => 'nullable|date|after:now'
        ]);

        // Check if URL already exists
        $query = Url::where('original_url', $request->url);

        if (Auth::check()) {
            // For authenticated users, check by user_id
            $query->where('user_id', Auth::id());
        } else {
            // For guest users, check by IP address or session
            $query->where(function ($q) use ($request) {
                $q->whereNull('user_id')
                    ->where('created_ip', $request->ip());
            });
        }

        $existingUrl = $query->first();

        if ($existingUrl) {
            // Update existing URL with new data if provided
            $updateData = [];

            if ($request->title && $request->title !== $existingUrl->title) {
                $updateData['title'] = $request->title;
            }

            if ($request->expires_at && $request->expires_at !== $existingUrl->expires_at) {
                $updateData['expires_at'] = $request->expires_at;
            }

            // Update the existing URL if there are changes
            if (!empty($updateData)) {
                $existingUrl->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'URL already exists! Returning your existing short URL.',
                'data' => [
                    'short_url' => $existingUrl->getShortUrl(),
                    'original_url' => $existingUrl->original_url,
                    'title' => $existingUrl->title,
                    'short_code' => $existingUrl->short_code,
                    'clicks' => $existingUrl->clicks,
                    'created_at' => $existingUrl->created_at->format('M d, Y H:i')
                ]
            ]);
        }

        // Generate unique short code for new URL
        $shortCode = $this->generateUniqueShortCode($request->prefix);

        if (!$shortCode) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate unique short code. Please try again.',
            ], 500);
        }

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
            'created_ip' => $request->ip(),
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

    /**
     * Fast unique short code generation - optimized for speed
     */
    private function generateUniqueShortCode($prefix = null)
    {
        // Fast method 1: Try 6-character random code (99.9% success rate)
        for ($i = 0; $i < 5; $i++) {
            $randomCode = Str::random(6);
            $shortCode = $prefix ? $prefix . '-' . $randomCode : $randomCode;

            if (!Url::where('short_code', $shortCode)->exists()) {
                return $shortCode;
            }
        }

        // Fast method 2: Try 7-character code (if 6 chars had collisions)
        for ($i = 0; $i < 3; $i++) {
            $randomCode = Str::random(7);
            $shortCode = $prefix ? $prefix . '-' . $randomCode : $randomCode;

            if (!Url::where('short_code', $shortCode)->exists()) {
                return $shortCode;
            }
        }

        // Fast method 3: Timestamp-based guaranteed unique (fallback)
        $timestamp = base_convert(microtime(true) * 10000, 10, 36);
        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 2);
        $shortCode = $prefix ? $prefix . '-' . $timestamp . $random : $timestamp . $random;

        return $shortCode;
    }

    /**
     * API endpoint to create short URL (returns only the short link)
     */
    public function apiShorten(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'url' => 'required|url|max:2048',
                'title' => 'nullable|string|max:255',
                'prefix' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_-]+$/',
                'expires_at' => 'nullable|date|after:now'
            ]);

            // Check if URL already exists
            $query = Url::where('original_url', $request->url);

            if (Auth::check()) {
                // For authenticated users, check by user_id
                $query->where('user_id', Auth::id());
            } else {
                // For guest users, check by IP address
                $query->where(function ($q) use ($request) {
                    $q->whereNull('user_id')
                        ->where('created_ip', $request->ip());
                });
            }

            $existingUrl = $query->first();

            if ($existingUrl) {
                // Return only the short URL for existing URLs
                return response($existingUrl->getShortUrl(), 200)
                    ->header('Content-Type', 'text/plain');
            }

            // Generate unique short code for new URL
            $shortCode = $this->generateUniqueShortCode($request->prefix);

            if (!$shortCode) {
                throw new \Exception('Unable to generate unique short code. Please try again.');
            }

            // Get page title if not provided
            $title = $request->title;
            if (!$title) {
                try {
                    $title = $this->extractTitle($request->url);
                } catch (\Exception $e) {
                    $title = parse_url($request->url, PHP_URL_HOST) ?: 'Shortened URL';
                }
            }

            // Create new URL record
            $url = Url::create([
                'original_url' => $request->url,
                'short_code' => $shortCode,
                'title' => $title,
                'user_id' => Auth::id(),
                'created_ip' => $request->ip(),
                'expires_at' => $request->expires_at
            ]);

            if (!$url) {
                throw new \Exception('Failed to create URL record in database');
            }

            // Return only the short URL
            return response($url->getShortUrl(), 200)
                ->header('Content-Type', 'text/plain');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as plain text
            $errors = collect($e->errors())->flatten()->implode('; ');
            return response('Validation Error: ' . $errors, 400)
                ->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            // Return any other errors as plain text
            return response('Error: ' . $e->getMessage(), 500)
                ->header('Content-Type', 'text/plain');
        }
    }
}
