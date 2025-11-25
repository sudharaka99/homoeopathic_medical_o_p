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

    /////////////////////////////////////
 

    public function doctorsList(Request $request)
    {
        $query = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_id',
                'u.name as doctor_name',
                'u.email',
                'u.mobile',
                'u.image as profile_image',
                'd.license_number',
                'd.license_image',
                'd.qualification',
                'd.specialization',
                'd.appointment_fee',
                'd.years_experience',
                'd.clinic_name',
                'd.district',
                'd.is_admin_confirmed',
                'd.reject_reason',
                's.name as specialization_name'
            );

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('u.name', 'like', "%{$search}%")
                ->orWhere('u.email', 'like', "%{$search}%")
                ->orWhere('d.license_number', 'like', "%{$search}%")
                ->orWhere('d.clinic_name', 'like', "%{$search}%")
                ->orWhere('d.district', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'approved') {
                $query->where('d.is_admin_confirmed', 1);
            } elseif ($request->status == 'pending') {
                $query->where('d.is_admin_confirmed', 0);
            } elseif ($request->status == 'disabled') {
                $query->where('d.is_admin_confirmed', 2);
            }
        }

        $doctors = $query->orderBy('u.created_at', 'desc')->paginate(10);

        // Get counts for filters
        $totalDoctors = DB::table('tbl_doctor')->count();
        $approvedDoctors = DB::table('tbl_doctor')->where('is_admin_confirmed', 1)->count();
        $pendingDoctors = DB::table('tbl_doctor')->where('is_admin_confirmed', 0)->count();
        $disabledDoctors = DB::table('tbl_doctor')->where('is_admin_confirmed', 2)->count();

        return view('admin.manage-doctors', compact(
            'doctors', 
            'totalDoctors',
            'approvedDoctors',
            'pendingDoctors',
            'disabledDoctors'
        ));
    }

    public function editDoctor($id)
    {
        $doctor = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_id',
                'u.name as doctor_name',
                'u.email',
                'u.mobile',
                'u.image as profile_image',
                'd.license_number',
                'd.license_image',
                'd.qualification',
                'd.specialization',
                'd.appointment_fee',
                'd.years_experience',
                'd.clinic_name',
                'd.district',
                'd.is_admin_confirmed',
                'd.reject_reason',
                's.name as specialization_name'
            )
            ->where('d.id', $id)
            ->first();

        if (!$doctor) {
            return redirect()->route('admin.doctorslist')->with('error', 'Doctor not found.');
        }

        $specializations = DB::table('tbl_specializations')->where('is_active', 1)->get();

        return view('admin.edit-doctor', compact('doctor', 'specializations'));
    }

    public function updateDoctor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'doctor_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:15',
            'license_number' => 'required|string|max:50',
            'qualification' => 'required|string',
            'specialization' => 'required|integer|exists:tbl_specializations,id',
            'appointment_fee' => 'required|numeric|min:0',
            'years_experience' => 'required|integer|min:0',
            'clinic_name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'is_admin_confirmed' => 'required|in:0,1,2',
            'reject_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form for errors.');
        }

        DB::beginTransaction();

        // Get doctor record
        $doctor = DB::table('tbl_doctor')->where('id', $id)->first();
        if (!$doctor) {
            return redirect()->route('admin.doctorslist')->with('error', 'Doctor not found.');
        }

        // Update user table
        DB::table('users')
            ->where('id', $doctor->doctor_id)
            ->update([
                'name' => $request->doctor_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
            ]);

        // Update doctor table
        $doctorData = [
            'doctor_name' => $request->doctor_name,
            'license_number' => $request->license_number,
            'qualification' => $request->qualification,
            'specialization' => $request->specialization,
            'appointment_fee' => $request->appointment_fee,
            'years_experience' => $request->years_experience,
            'clinic_name' => $request->clinic_name,
            'district' => $request->district,
            'is_admin_confirmed' => $request->is_admin_confirmed,
            'reject_reason' => $request->reject_reason,
        ];

        DB::table('tbl_doctor')
            ->where('id', $id)
            ->update($doctorData);

        DB::commit();

        return redirect()->route('admin.doctorslist')
            ->with('success', 'Doctor information updated successfully!');
    }

    public function deleteDoctor($id)
    {
        DB::beginTransaction();

        $doctor = DB::table('tbl_doctor')->where('id', $id)->first();
        
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found.'
            ], 404);
        }

        // Check if doctor has appointments
        $hasAppointments = DB::table('tbl_doctor_appointment')
            ->where('doctor_id', $id)
            ->exists();

        if ($hasAppointments) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete doctor with existing appointments. Please reassign or cancel appointments first.'
            ], 400);
        }

        // Delete doctor record
        DB::table('tbl_doctor')->where('id', $id)->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully!'
        ]);
    }

    public function toggleDoctorStatus($id)
    {
        DB::beginTransaction();

        $doctor = DB::table('tbl_doctor')->where('id', $id)->first();
        
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found.'
            ], 404);
        }

        // Toggle between approved (1) and disabled (2) - skip pending (0)
        if ($doctor->is_admin_confirmed == 1) {
            $newStatus = 2; // Approved -> Disabled
        } else {
            $newStatus = 1; // Disabled/Pending -> Approved
        }

        DB::table('tbl_doctor')
            ->where('id', $id)
            ->update([
                'is_admin_confirmed' => $newStatus,
                'reject_reason' => $newStatus == 2 ? 'Admin disabled account' : null,
            ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Doctor status updated successfully!',
            'new_status' => $newStatus,
            'status_text' => $newStatus == 1 ? 'Approved' : ($newStatus == 2 ? 'Disabled' : 'Pending')
        ]);
    }
//////////////////////////////////////////////////////////

    public function allAppointment()
    {
        $appointments = DB::table('tbl_doctor_appointment')
            ->join('users', 'tbl_doctor_appointment.user_id', '=', 'users.id')
            ->join('tbl_doctor', 'tbl_doctor_appointment.doctor_id', '=', 'tbl_doctor.id')
            ->join('tbl_specializations', 'tbl_doctor.specialization', '=', 'tbl_specializations.id')
            ->select(
                'tbl_doctor_appointment.*',
                'users.name as patient_name',
                'users.email as patient_email',
                'tbl_doctor.doctor_name',
                'tbl_specializations.name as specialization_name'
            )
            ->latest('tbl_doctor_appointment.created_at')
            ->paginate(10);

        return view('admin.all-appointment', compact('appointments'));
    }
///////////////////////////////////////////////////

    public function managePatientList(Request $request)
    {
        $query = DB::table('users')
            ->where('role', 'patient')
            ->latest();

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(10);

        return view('admin.manage-patient', compact('patients'));
    }

    public function managePatientListEdit($id)
    {
        $patient = DB::table('users')->where('id', $id)->where('role', 'patient')->first();
        
        if (!$patient) {
            return redirect()->route('admin.patients')->with('error', 'Patient not found.');
        }

        return view('admin.manage-patient-edit', compact('patient'));
    }

    public function managePatientListUpdate(Request $request, $id)
    {
        $patient = DB::table('users')->where('id', $id)->where('role', 'patient')->first();
        
        if (!$patient) {
            return redirect()->route('admin.patients')->with('error', 'Patient not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'mobile' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255'
        ]);

        DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'designation' => $request->designation,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.patients')->with('success', 'Patient updated successfully.');
    }

    public function managePatientListDestroy($id)
    {
        $patient = DB::table('users')->where('id', $id)->where('role', 'patient')->first();
        
        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found.'
            ], 404);
        }

        DB::table('users')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Patient deleted successfully.'
        ]);
    }

}
