<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::where('user_id', Auth::id())->get();

        return view('application.index',compact('applications'));
    }

    public function detail($id)
    {
        $application = \App\Models\Application::findOrFail($id);
        return view('application.detail',compact('application'));
    }
}
