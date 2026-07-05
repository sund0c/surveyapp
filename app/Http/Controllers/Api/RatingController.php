<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRatingRequest;
use App\Models\Application;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request)
    {
        /** @var Application $application */
        $application = $request->attributes->get('application');

        $rating = $application->ratings()->create([
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
            // Hash rather than store the raw IP - enough to spot abuse patterns
            // without retaining identifiable data on end users long-term.
            'origin_ip_hash' => hash('sha256', $request->ip()),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json([
            'message' => 'Rating recorded.',
            'id' => $rating->id,
        ], 201);
    }
}
