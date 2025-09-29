<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index()
    {
        $logs = \App\Models\ActivityLog::latest()->paginate(20);
        return view('logs.index', compact('logs'));
    }
}
