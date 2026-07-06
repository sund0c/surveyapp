<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * @param string $action e.g. 'user.created', 'auth.login_failed'
     * @param string $description human-readable summary
     * @param object|null $subject the model this action was performed on (e.g. a User or Application)
     * @param User|null $actorOverride use when the actor isn't the currently authenticated
     *                                 user (e.g. logging a failed login attempt before auth)
     */
    public static function log(string $action, string $description, ?object $subject = null, ?User $actorOverride = null): void
    {
        $actor = $actorOverride ?? Auth::user();

        AuditLog::create([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'actor_email' => $actor?->email,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->id,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 255),
        ]);
    }
}
