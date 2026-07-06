<?php

namespace App\Http\Middleware;

use App\Models\Application;
use App\Services\HmacSigner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $appKey = $request->header('X-App-Key');
        $timestamp = $request->header('X-Timestamp');
        $nonce = $request->header('X-Nonce');
        $signature = $request->header('X-Signature');

        if (!$appKey || !$timestamp || !$nonce || !$signature) {
            return $this->reject('Missing authentication headers.');
        }

        // 1. Timestamp freshness - reject anything outside the allowed clock skew.
        //    Prevents captured requests from being replayed indefinitely.
        $skewSeconds = config('survey.hmac_skew_seconds', 300);
        if (abs(time() - (int) $timestamp) > $skewSeconds) {
            return $this->reject('Request timestamp outside allowed window.');
        }

        // 2. Nonce replay protection - each (app_key, nonce) pair usable exactly once
        //    within the skew window. Cache TTL must exceed the skew window.
        $nonceCacheKey = "hmac_nonce:{$appKey}:{$nonce}";
        if (Cache::has($nonceCacheKey)) {
            return $this->reject('Nonce already used (possible replay).');
        }

        // 3. Look up the application - must exist and be active.
        $application = Application::query()
            ->where('api_key', $appKey)
            ->where('is_active', true)
            ->first();

        if (!$application) {
            return $this->reject('Unknown or inactive application key.');
        }

        // 4. Recompute signature over the raw request body and compare (timing-safe).
        $rawBody = $request->getContent();
        $valid = HmacSigner::verify(
            $timestamp,
            $nonce,
            $rawBody,
            $application->api_secret, // decrypted transparently via the model's 'encrypted' cast
            $signature
        );

        if (!$valid) {
            return $this->reject('Invalid signature.');
        }

        // Signature confirmed valid - now reserve the nonce so it can't be replayed.
        Cache::put($nonceCacheKey, true, $skewSeconds + 60);

        $application->forceFill(['last_used_at' => now()])->saveQuietly();

        // Make the authenticated application available to the controller.
        $request->attributes->set('application', $application);

        return $next($request);
    }

    private function reject(string $message): Response
    {
        return response()->json(['message' => $message], 401);
    }
}
