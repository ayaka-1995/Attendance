<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', '承認待ち');

        $applications = Application::with('user')
            ->where('user_id', Auth::id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('application.index',compact('applications', 'status'));
    }

    public function detail($id)
    {
        $application = Application::with('user')->findOrFail($id);
        return view('application.detail',compact('application'));
    }
}
