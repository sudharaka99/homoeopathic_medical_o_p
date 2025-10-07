<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function index()
    {
        return view('doctor.dashboard');
    }

    public function profile()
    {
        $id = Auth::id();

        $user = DB::table('users')
        ->where('id', $id)
        ->first();

        return view('doctor.profile', compact('user'));
    }


}
