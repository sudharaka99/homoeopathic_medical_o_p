<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{

    // public function index()
    // {
    //         // Get counts for dashboard statistics
    //         $patientCount = DB::table('users')->where('role', 'patient')->count();
    //         $doctorCount = DB::table('users')->where('role', 'doctor')->count();
            
         
            
    //         return view('admin.dashboard', compact(
    //             'patientCount',
    //             'doctorCount',
                
    //         ));

       
    // }

public function dashboard()
{

    // Get counts for dashboard statistics
    $patientCount = DB::table('users')->where('role', 'patient')->count();
    $doctorCount = DB::table('users')->where('role', 'doctor')->count();
    
    // For doctor status, check tbl_doctor table
    $approvedDoctors = DB::table('tbl_doctor')
        ->where('is_admin_confirmed', 1)
        ->count();
        
    $pendingDoctors = DB::table('tbl_doctor')
        ->where('is_admin_confirmed', 0)
        ->count();
    
    // // Get appointment statistics - adjust table name as needed
    // $totalAppointments = DB::table('tbl_appointments')->count();
    // $todayAppointments = DB::table('tbl_appointments')
    //     ->whereDate('appointment_date', today())
    //     ->count();
    
    // // Get recent pending doctors for the sidebar
    // $recentPendingDoctors = DB::table('tbl_doctor as d')
    //     ->join('users as u', 'd.doctor_id', '=', 'u.id')
    //     ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
    //     ->select(
    //         'u.name',
    //         'd.specialization',
    //         'd.created_at',
    //         's.name as specialization_name'
    //     )
    //     ->where('d.is_admin_confirmed', 0)
    //     ->orderBy('d.created_at', 'desc')
    //     ->limit(5)
    //     ->get();
    
    // // Get recent appointments
    // $recentAppointments = DB::table('tbl_appointments as a')
    //     ->join('users as patient', 'a.patient_id', '=', 'patient.id')
    //     ->join('users as doctor', 'a.doctor_id', '=', 'doctor.id')
    //     ->select(
    //         'patient.name as patient_name',
    //         'doctor.name as doctor_name',
    //         'a.appointment_date',
    //         'a.status'
    //     )
    //     ->orderBy('a.appointment_date', 'desc')
    //     ->limit(5)
    //     ->get();
    
    // Get registration data for the chart (last 6 months)
    $months = [];
    $patientRegistrations = [];
    $doctorRegistrations = [];
    
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $months[] = $date->format('M Y');
        
        $patientRegistrations[] = DB::table('users')
            ->where('role', 'patient')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
            
        $doctorRegistrations[] = DB::table('users')
            ->where('role', 'doctor')
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
    }
    
    return view('admin.dashboard', compact(
        'patientCount',
        'doctorCount',
        'approvedDoctors',
        'pendingDoctors',
        // 'totalAppointments',
        // 'todayAppointments',
        // 'recentPendingDoctors',
        // 'recentAppointments',
        'months',
        'patientRegistrations',
        'doctorRegistrations'
    ));


}

public function pendingDoctors()
{
    try {
        $pendingDoctors = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_id',
                'd.doctor_name',
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
            ->where('d.is_admin_confirmed', 0)
            ->orderBy('u.created_at', 'desc')
            ->paginate(10);

        return view('admin.doctor-registration', compact('pendingDoctors'));

    } catch (\Exception $e) {
        \Log::error('Error fetching pending doctors: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong.');
    }
}

public function doctorDetails(Request $request)
{
    try {
        $doctorId = $request->doctor_id;
        
        $doctor = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_id',
                'd.doctor_name',
                'd.license_number',
                'd.license_image',
                'd.qualification',
                'd.specialization',
                'd.appointment_fee',
                'd.years_experience',
                'd.clinic_name',
                'd.district',
                'd.is_admin_confirmed',
                'u.name',
                'u.email',
                'u.mobile',
                'u.profile_photo_path',
                'u.created_at as user_created_at',
                's.name as specialization_name'
            )
            ->where('d.id', $doctorId)
            ->first();

        if (!$doctor) {
            return '<div class="alert alert-danger">Doctor not found.</div>';
        }

        // Return HTML directly
        $html = '
        <div class="row">
            <div class="col-md-4 text-center">
                '.($doctor->profile_photo_path ? 
                    '<img src="'.asset('storage/'.$doctor->profile_photo_path).'" alt="'.$doctor->name.'" class="img-fluid rounded-circle mb-3" width="150" height="150">' 
                    : 
                    '<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                        <i class="fa fa-user-md fa-3x"></i>
                    </div>'
                ).'
                <h4 class="mb-1">'.$doctor->name.'</h4>
                <p class="text-muted mb-2">'.($doctor->specialization_name ?? 'Not specified').'</p>
                <span class="badge bg-'.($doctor->is_admin_confirmed == 0 ? 'warning' : ($doctor->is_admin_confirmed == 1 ? 'success' : 'danger')).'">
                    '.($doctor->is_admin_confirmed == 0 ? 'Pending' : ($doctor->is_admin_confirmed == 1 ? 'Approved' : 'Rejected')).'
                </span>
            </div>
            
            <div class="col-md-8">
                <div class="row">
                    <div class="col-12 mb-3">
                        <h5 class="border-bottom pb-2">Contact Information</h5>
                        <p><strong>Email:</strong> '.$doctor->email.'</p>
                        <p><strong>Mobile:</strong> '.($doctor->mobile ?? 'N/A').'</p>
                        '.($doctor->clinic_name ? '<p><strong>Clinic:</strong> '.$doctor->clinic_name.'</p>' : '').'
                        '.($doctor->district ? '<p><strong>District:</strong> '.$doctor->district.'</p>' : '').'
                    </div>
                    
                    <div class="col-12 mb-3">
                        <h5 class="border-bottom pb-2">Professional Information</h5>
                        '.($doctor->license_number ? '<p><strong>License Number:</strong> '.$doctor->license_number.'</p>' : '').'
                        '.($doctor->qualification ? '<p><strong>Qualification:</strong> '.$doctor->qualification.'</p>' : '').'
                        '.($doctor->years_experience ? '<p><strong>Experience:</strong> '.$doctor->years_experience.' years</p>' : '').'
                        '.($doctor->appointment_fee ? '<p><strong>Appointment Fee:</strong> Rs. '.$doctor->appointment_fee.'</p>' : '').'
                    </div>';
        
        // Handle license image
        if ($doctor->license_image) {
            $imagePath = asset('storage/doctor_licenses/' . basename($doctor->license_image));
            $html .= '
                    <div class="col-12 mb-3">
                        <h5 class="border-bottom pb-2">License Document</h5>
                        <div class="text-center">
                            <img src="'.$imagePath.'" alt="License Image" class="img-fluid rounded border" style="max-height: 200px;">
                            <div class="mt-2">
                                <a href="'.$imagePath.'" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fa fa-external-link-alt me-1"></i>Open Full Size
                                </a>
                                <a href="'.$imagePath.'" download class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>';
        } else {
            $html .= '
                    <div class="col-12 mb-3">
                        <h5 class="border-bottom pb-2">License Document</h5>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            No license image uploaded.
                        </div>
                    </div>';
        }
        
        $html .= '
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Registration Details</h5>
                        <p><strong>Registration Date:</strong> '.\Carbon\Carbon::parse($doctor->user_created_at)->format('M d, Y').'</p>
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;

    } catch (\Exception $e) {
        \Log::error('Error loading doctor details: ' . $e->getMessage());
        return '<div class="alert alert-danger">Error loading details: '.$e->getMessage().'</div>';
    }
}

public function approveDoctor(Request $request)
{
    try {
        $doctorId = $request->doctor_id;
        
        DB::table('tbl_doctor')
            ->where('id', $doctorId)
            ->update(['is_admin_confirmed' => 1]);

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

        DB::table('tbl_doctor')
            ->where('id', $doctorId)
            ->update([
                'is_admin_confirmed' => 2,
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


    public function userFeedback(Request $request)
    {
        // Base query
        $query = DB::table('tbl_contacts_us')->orderBy('created_at', 'desc');
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $messages = $query->get();
        
        // Get counts for all statuses
        $totalMessages = DB::table('tbl_contacts_us')->count();
        $newCount = DB::table('tbl_contacts_us')->where('status', 'new')->count();
        $inProgressCount = DB::table('tbl_contacts_us')->where('status', 'in_progress')->count();
        $resolvedCount = DB::table('tbl_contacts_us')->where('status', 'resolved')->count();

        return view('admin.user_feedback', compact('messages', 'totalMessages', 'newCount', 'inProgressCount', 'resolvedCount'));
    }

    public function replyToMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:tbl_contacts_us,id',
            'reply_message' => 'required|min:3'
        ]);

        try {
            DB::table('tbl_contacts_us')
                ->where('id', $request->message_id)
                ->update([
                    'reply_message' => $request->reply_message,
                    'replied_by' => auth()->id(),
                    'replied_at' => now(),
                    'status' => 'resolved',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateMessageStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved'
        ]);

        DB::table('tbl_contacts_us')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Status updated successfully!');
    }

    public function deleteMessage($id)
    {
        DB::table('tbl_contacts_us')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Message deleted successfully!');
    }




}
