<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    // GET /api/geo - HOME API endpoint
    public function getGeoLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'nullable|ip'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid IP address format',
                'errors' => $validator->errors()
            ], 422);
        }

        $ip = $request->input('ip');
        $url = $ip ? "https://ipinfo.io/{$ip}/geo" : "https://ipinfo.io/geo";

        try {
            // Disable SSL verification for development (common issue on Windows/XAMPP)
            $response = Http::withOptions(['verify' => false])
                ->timeout(10)
                ->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Failed to fetch geolocation data',
                    'status' => $response->status()
                ], $response->status());
            }

            $geoData = $response->json();

            // If this is a search for a specific IP, save to history
            if ($ip) {
                $request->user()->searchHistories()->create([
                    'search_term' => $ip,
                    'geo_data' => $geoData
                ]);
            }

            return response()->json($geoData);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'External API error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/history
    public function index(Request $request)
    {
        // Return the user's history, newest first
        return $request->user()->searchHistories()->latest()->get();
    }

    // POST /api/history
    public function store(Request $request)
    {
        $request->validate([
            'search_term' => 'required', // The IP address
            'geo_data' => 'required'     // The full JSON result from IPInfo
        ]);

        // Save to database
        $history = $request->user()->searchHistories()->create([
            'search_term' => $request->search_term,
            'geo_data' => $request->geo_data
        ]);

        return response()->json($history, 201);
    }

    // POST /api/history/delete (Optional: For deleting multiple items)
    public function destroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        // Only delete if the ID belongs to this user
        $request->user()->searchHistories()
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
