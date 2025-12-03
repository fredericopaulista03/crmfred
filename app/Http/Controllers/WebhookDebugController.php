<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WebhookDebugController extends Controller
{
    public function index()
    {
        $logs = WebhookLog::orderBy('created_at', 'desc')->paginate(20);
        return view('webhook-debug.index', compact('logs'));
    }

    public function show($id)
    {
        $log = WebhookLog::findOrFail($id);
        return view('webhook-debug.show', compact('log'));
    }

    public function clear()
    {
        WebhookLog::truncate();
        return back()->with('success', 'Todos os logs foram limpos!');
    }
}
