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

    public function doctorDetailsShow($id)
    {
        $doctor = DB::table('tbl_doctor as d')
            ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_feedback as f', 'f.doctor_id', '=', 'd.id')
            ->select(
                'd.*',
                's.name as specialization_name',
                'u.email',
                'u.mobile',
                'u.image'
            )
            ->where('d.id', $id)
            ->first();

        if (!$doctor) {
            return redirect()->route('doctors.list')->with('error', 'Doctor not found.');
        }

        // Fetch feedbacks separately
        $feedbacks = DB::table('tbl_feedback as f')
            ->join('users as u', 'f.user_id', '=', 'u.id')
            ->select('f.*', 'u.name as user_name')
            ->where('f.doctor_id', $id)
            ->orderBy('f.created_at', 'desc')
            ->get();

        // Attach feedback list
        $doctor->feedback = $feedbacks;

        return view('front.doctorDetails', compact('doctor'));
    }

    /**
     * Store feedback
     */
    public function storeFeedback(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login to submit feedback.');
        }

        $validator = Validator::make($request->all(), [
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid feedback data.');
        }

        try {
            DB::table('tbl_feedback')->insert([
                'doctor_id' => $id,
                'user_id'   => Auth::id(),
                'rating'    => $request->rating,
                'feedback'  => $request->feedback,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Your feedback has been submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


}
