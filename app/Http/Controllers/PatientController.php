<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\User;

class PatientController extends Controller
{
    // public function dashboard()
    // {
    //     return view('front.account.dashboard');
    // }

     public function dashboard()
    {
        $user = Auth::user();

        // Load user’s appointments
        // $appointments = Appointment::with('doctor')
        //     ->where('patient_id', $user->id)
        //     ->orderBy('appointment_date', 'desc')
        //     ->get();

        // Recently consulted doctors (based on appointments)
        // $recentDoctors = Doctor::whereIn('id', $appointments->pluck('doctor_id')->unique())->get();

        return view('front.account.dashboard');
    }

    public function saveDoctor(Request $request)
    {
        try {
            // Ensure user is authenticated
            if (!auth()->check()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            DB::beginTransaction();

            $userId = auth()->id();
            $doctorId = $request->input('doctor_id');

            // Validate input
            $validator = Validator::make($request->all(), [
                'doctor_id' => 'required|integer|exists:tbl_doctor,id',
                'save_reason' => 'nullable|string|max:500',
                'save_category' => 'nullable|string|in:favorite,consult_later,reference',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
            }

            // Check if already saved
            $alreadySaved = DB::table('tbl_saved_doctors')
                ->where('user_id', $userId)
                ->where('doctor_id', $doctorId)
                ->exists();

            if ($alreadySaved) {
                return response()->json([
                    'status' => false,
                    'message' => 'This doctor is already in your saved list.'
                ], 200);
            }

            // Save the doctor with reason and category into tbl_saved_doctors
            $savedId = DB::table('tbl_saved_doctors')->insertGetId([
                'user_id' => $userId,
                'doctor_id' => $doctorId,
                'save_reason' => $request->input('save_reason'),
                'save_category' => $request->input('save_category') ?? 'favorite',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Doctor saved successfully.',
                'id' => $savedId,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');

        }
    }

    public function savedDoctors()
    {
        $userId = Auth::id(); // always current user

        $savedDoctors = DB::table('tbl_saved_doctors as sd')
            ->join('tbl_doctor as d', 'sd.doctor_id', '=', 'd.id')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->where('sd.user_id', $userId)
            ->select(
                'sd.id as saved_id',
                'd.id as doctor_id',
                'd.doctor_name',
                'u.profile_photo_path',
                'd.qualification',
                'd.years_experience',
                's.name as specialization',
                'd.clinic_name',
                'sd.save_reason',
                'sd.save_category',
                'sd.created_at'
            )
            ->orderBy('sd.created_at', 'desc')
            ->paginate(10);

        return view('front.account.doctor.savedDoctor', compact('savedDoctors'));
    }

   

    public function findDoctors(Request $request)
    {
        try {
            $query = DB::table('tbl_doctor as d')
                ->join('users as u', 'd.doctor_id', '=', 'u.id')
                ->leftJoin('tbl_specializations as s', 'd.specialization', '=', 's.id')
                ->select(
                    'u.id as user_id',
                    'd.id',
                    'd.doctor_id',
                    'u.name as doctor_name',
                    'u.profile_photo_path as profile_picture',
                    'd.qualification',
                    'd.years_experience',
                    'd.appointment_fee as fee',
                    'd.clinic_name',
                    'd.license_number',
                    's.name as specialization_name',
                    'u.email',
                    'u.created_at'
                )
                ->where('u.role', 'doctor')
                ->orderBy('u.created_at', 'desc');

            // Apply filters
            if ($request->has('name') && $request->name != '') {
                $query->where('u.name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('specialization') && $request->specialization != '') {
                $query->where('d.specialization', $request->specialization);
            }

            // Get the doctors first
            $doctors = $query->paginate(10)->withQueryString();

            // Check if each doctor is saved by current user
            if (Auth::check()) {
                $savedDoctorIds = DB::table('tbl_saved_doctors')
                    ->where('user_id', Auth::id())
                    ->pluck('doctor_id')
                    ->toArray();

                // Add is_saved flag to each doctor
                $doctors->getCollection()->transform(function ($doctor) use ($savedDoctorIds) {
                    $doctor->is_saved = in_array($doctor->id, $savedDoctorIds);
                    return $doctor;
                });
            } else {
                // For non-logged in users, set is_saved to false
                $doctors->getCollection()->transform(function ($doctor) {
                    $doctor->is_saved = false;
                    return $doctor;
                });
            }

            $specializations = DB::table('tbl_specializations')
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();

            return view('front.doctors', compact('doctors', 'specializations'));

        } catch (\Exception $e) {
            \Log::error('Error fetching doctors: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    

    public function createAppointment(Request $request, $id)
    {
        try {

            $doctors = DB::table('tbl_doctor as d')
                ->join('users as u', 'd.doctor_id', '=', 'u.id')
                ->where('d.doctor_id', $id)
                ->select(
                    'u.name as doctor_name',
                )
                ->get();

            // Get availability list
            $avalabilityList = DB::table('tbl_availability as da')
                ->where('da.doctor_id', $id)
                ->where('da.date', '>=', date('Y-m-d'))
                ->where('status', 'available')
                ->where('number_of_tokens', '>', 0)
                ->orderBy('da.date', 'asc')
                ->orderBy('da.start_time_slot', 'asc')
                ->get();

            return view('front.bookAppointmentShow', compact('avalabilityList','doctors'));

        } catch (\Exception $e) {
            Log::error('Error in createAppointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load appointment booking page.');
        }
    }

    public function hydration()
    {
        return view('front.account.health-tips.hydration');
    }

    public function exercise()
    {
        return view('front.account.health-tips.exercise');
    }

    public function sleep()
    {
        return view('front.account.health-tips.sleep');
    }

    public function diet()
    {
        return view('front.account.health-tips.diet');
    }



    public function myDetails(Request $request)
    {
        if ($request->isMethod('get')) {
            $request->session()->forget(['success', 'error']);
        }

        $medicalInfo = DB::table('tbl_medical_info')
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$medicalInfo) {
            $medicalInfo = (object) [
                'medical_history' => null,
                'current_medications' => null,
                'allergies' => null,
                'hemoglobin' => null,
                'rbc_count' => null,
                'wbc_count' => null,
                'platelet_count' => null,
                'blood_sugar' => null,
                'cholesterol' => null,
                'emergency_contact_name' => null,
                'emergency_contact_relation' => null,
                'emergency_contact_phone' => null,
                'created_at' => null,
                'updated_at' => null,
            ];
        }

        // Fetch files from respective tables with tbl_ prefix
        $bloodTestReports = DB::table('tbl_blood_test_reports')
            ->where('user_id', Auth::id())
            ->get();
        $prescriptions = DB::table('tbl_prescriptions')
            ->where('user_id', Auth::id())
            ->get();
        $medicalReports = DB::table('tbl_medical_reports')
            ->where('user_id', Auth::id())
            ->get();
        $insuranceDocuments = DB::table('tbl_insurance_documents')
            ->where('user_id', Auth::id())
            ->get();

        return view('front.account.my-details', compact('medicalInfo', 'bloodTestReports', 'prescriptions', 'medicalReports', 'insuranceDocuments'));
    }

    public function store(Request $request)
    {
        $rules = [
            'medical_history' => 'nullable|string|max:5000',
            'current_medications' => 'nullable|string|max:5000',
            'allergies' => 'nullable|string|max:5000',
            'hemoglobin' => 'nullable|numeric|min:0|max:50',
            'rbc_count' => 'nullable|numeric|min:0|max:50',
            'wbc_count' => 'nullable|numeric|min:0|max:100000',
            'platelet_count' => 'nullable|numeric|min:0|max:1000000',
            'blood_sugar' => 'nullable|numeric|min:0|max:1000',
            'cholesterol' => 'nullable|numeric|min:0|max:1000',
            'blood_test_reports.*' => 'nullable|file|mimes:pdf|max:5120',
            'prescriptions.*' => 'nullable|file|mimes:pdf|max:5120',
            'medical_reports.*' => 'nullable|file|mimes:pdf|max:5120',
            'insurance_documents.*' => 'nullable|file|mimes:pdf|max:5120',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|in:Spouse,Parent,Sibling,Child,Friend,Other',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ];

        $request->validate($rules);

        $userId = Auth::id();
        $data = $request->only([
            'medical_history',
            'current_medications',
            'allergies',
            'hemoglobin',
            'rbc_count',
            'wbc_count',
            'platelet_count',
            'blood_sugar',
            'cholesterol',
            'emergency_contact_name',
            'emergency_contact_relation',
            'emergency_contact_phone',
        ]);

        // Update or create medical info record
        DB::table('tbl_medical_info')->updateOrInsert(
            ['user_id' => $userId],
            array_merge($data, ['updated_at' => now()])
        );

        // Handle file uploads with tbl_ prefix
        $fileFields = [
            'blood_test_reports' => 'tbl_blood_test_reports',
            'prescriptions' => 'tbl_prescriptions',
            'medical_reports' => 'tbl_medical_reports',
            'insurance_documents' => 'tbl_insurance_documents',
        ];

        foreach ($fileFields as $inputName => $tableName) {
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                foreach ($files as $file) {
                    $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    $path = $file->storeAs("medical-documents/{$tableName}/{$userId}", $filename, 'public');
                    DB::table($tableName)->insert([
                        'user_id' => $userId,
                        'file_path' => $filename,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Medical information saved successfully!');
    }

    public function viewDocument($documentType, $id)
    {
        try {
            $user = Auth::user();
            $table = $this->getTableName($documentType);
            if (!$table) {
                abort(404, 'Invalid document type');
            }

            $record = DB::table($table)->where('id', $id)->where('user_id', $user->id)->first();
            if (!$record) {
                abort(404, 'Document not found');
            }

            $filePath = "medical-documents/{$table}/{$user->id}/{$record->file_path}";
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'Document not found');
            }

            $fullPath = Storage::disk('public')->path($filePath);
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $record->file_path . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        } catch (\Exception $e) {
            \Log::error('Document view error: ' . $e->getMessage());
            abort(404, 'Document not found');
        }
    }

    public function deleteDocument(Request $request)
    {
        try {
            $user = Auth::user();
            $documentType = $request->input('document_type');
            $id = $request->input('id');

            $table = $this->getTableName($documentType);
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid document type'], 400);
            }

            $record = DB::table($table)->where('id', $id)->where('user_id', $user->id)->first();
            if (!$record) {
                return response()->json(['success' => false, 'message' => 'Document not found'], 404);
            }

            $filePath = "medical-documents/{$table}/{$user->id}/{$record->file_path}";
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            DB::table($table)->where('id', $id)->delete();

            // Clear session flash messages
            $request->session()->forget(['success', 'error']);

            return response()->json(['success' => true, 'message' => 'Document deleted']);
        } catch (\Exception $e) {
            \Log::error('Document delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting document: ' . $e->getMessage()], 500);
        }
    }

    public function downloadDocument($documentType, $id)
    {
        try {
            $user = Auth::user();
            $table = $this->getTableName($documentType);
            if (!$table) {
                abort(404, 'Invalid document type');
            }

            $record = DB::table($table)->where('id', $id)->where('user_id', $user->id)->first();
            if (!$record) {
                abort(404, 'Document not found');
            }

            $filePath = "medical-documents/{$table}/{$user->id}/{$record->file_path}";
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'Document not found');
            }

            return Storage::disk('public')->download($filePath, $record->file_path);
        } catch (\Exception $e) {
            \Log::error('Document download error: ' . $e->getMessage());
            abort(404, 'Document not found');
        }
    }

    private function getTableName($documentType)
    {
        $map = [
            'blood-test-report' => 'tbl_blood_test_reports',
            'prescription' => 'tbl_prescriptions',
            'medical-report' => 'tbl_medical_reports',
            'insurance-document' => 'tbl_insurance_documents',
        ];
        return $map[$documentType] ?? null;
    }

}










