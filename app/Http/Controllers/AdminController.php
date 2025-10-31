<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    // public function doctorsList()
    // {
    //     return view('admin.doctor-registration');
    // }

    // In your AdminController
public function pendingDoctors()
{
    try {
        $pendingDoctors = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_id',
                'u.name',
                'u.email',
                'u.mobile',
                'u.profile_photo_path',
                'd.license_number',
                'd.license_image',
                'd.qualification',
                'd.specialization',
                's.name as specialization_name',
                'd.appointment_fee',
                'd.years_experience',
                'd.clinic_name',
                'd.district',
                'd.is_admin_confirmed',
                'u.created_at'
            )
            ->where('d.is_admin_confirmed', 0) // Only pending approvals
            ->orderBy('u.created_at', 'desc')
            ->paginate(10);

        return view('admin.doctor-registration', compact('pendingDoctors'));

    } catch (\Exception $e) {
        \Log::error('Error fetching pending doctors: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong.');
    }
}

public function approveDoctor(Request $request)
{
    try {
        $doctorId = $request->doctor_id;
        
        // Update doctor status
        DB::table('tbl_doctor')
            ->where('id', $doctorId)
            ->update(['is_admin_confirmed' => 1]);

        // You can also send email notification to doctor here

        return response()->json([
            'success' => true,
            'message' => 'Doctor approved successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error approving doctor: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to approve doctor.'
        ], 500);
    }
}

public function rejectDoctor(Request $request)
{
    try {
        $doctorId = $request->doctor_id;
        $rejectReason = $request->reject_reason;

        // Here you can:
        // 1. Delete the doctor record
        // 2. Or mark as rejected with reason
        // 3. Send rejection email to doctor

        DB::table('tbl_doctor')
            ->where('id', $doctorId)
            ->update([
                'is_admin_confirmed' => 2, // 2 for rejected
                'reject_reason' => $rejectReason
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Doctor registration rejected.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error rejecting doctor: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to reject doctor.'
        ], 500);
    }
}

public function doctorDetails(Request $request)
{
    try {
        $doctorId = $request->doctor_id;
        
        $doctor = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select('*')
            ->where('d.id', $doctorId)
            ->first();

        return view('admin.doctors.details-modal', compact('doctor'));

    } catch (\Exception $e) {
        return '<div class="alert alert-danger">Error loading details.</div>';
    }
}

}
