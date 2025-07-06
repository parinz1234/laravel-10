<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheTestController extends Controller
{
    public function cacheTest()
    {
        // Log the start of the cache test
        Log::info('Cache test endpoint accessed', [
            'timestamp' => now()->toDateTimeString(),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        Log::info("MANDATORY_ERROR", [
            'message' => 'This is a mandatory error',
            'timestamp' => now()->toDateTimeString()
        ]);

        // This is the key we will use to store our data in the cache.
        $cacheKey = 'expensive-db-query-results';

        // Check if cache exists before attempting to retrieve
        if (Cache::has($cacheKey)) {
            Log::debug('Cache hit for key: ' . $cacheKey);
        } else {
            Log::warning('Cache miss for key: ' . $cacheKey . ' - Will execute expensive operation');
        }

        // Cache::remember() is a powerful function.
        // 1. It checks if the data for 'cacheKey' exists in the cache.
        // 2. If YES, it returns the cached data immediately.
        // 3. If NO, it executes the function passed as the third argument.
        // 4. It then stores the result of that function in the cache for 60 seconds.
        // 5. Finally, it returns the result.
        $results = Cache::remember($cacheKey, 60, function () {
            Log::info('Executing expensive database query simulation');

            // Simulate a slow database query that takes 3 seconds.
            // This part will only run if the cache is empty.
            sleep(3);

            Log::info('Expensive database query simulation completed');

            // This is the data we want to cache.
            return [
                'message' => 'This data came from the "slow query".',
                'retrieved_at' => now()->toDateTimeString()
            ];
        });

        // Log successful completion
        Log::info('Cache test completed successfully', [
            'cache_key' => $cacheKey,
            'data_timestamp' => $results['retrieved_at'],
            'response_time' => microtime(true) - LARAVEL_START
        ]);

        return response()->json([
            'success' => true,
            'data' => $results,
            'cache_info' => [
                'key' => $cacheKey,
                'ttl_seconds' => 60,
                'is_cached' => Cache::has($cacheKey)
            ]
        ]);
    }
}
