<?php

namespace App\Services;

use App\Models\UserActionLog;
use Illuminate\Support\Facades\Auth;

class ActionLogger
{
    public static function log($action, $details = null)
    {
        UserActionLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => is_array($details) ? json_encode($details) : $details,
        ]);
    }
}
