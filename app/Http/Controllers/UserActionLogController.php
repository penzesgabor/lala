<?php

namespace App\Http\Controllers;

use App\Models\UserActionLog;
use Illuminate\Http\Request;

class UserActionLogController extends Controller
{
    public function index()
    {
        $logs = UserActionLog::with('user')->latest()->paginate(10);
        return view('logs.index', compact('logs'));
    }
}
