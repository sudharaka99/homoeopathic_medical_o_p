<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

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
                    'd.district', // Add district to select
                    'u.email',
                    'u.created_at'
                )
                ->where('u.role', 'doctor')
                ->where('d.is_admin_confirmed', 1)
                ->orderBy('u.created_at', $request->sort == '0' ? 'asc' : 'desc');

            // Apply filters
            if ($request->has('name') && $request->name != '') {
                $query->where('u.name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('specialization') && $request->specialization != '') {
                $query->where('d.specialization', $request->specialization);
            }

            // Add district filter
            if ($request->has('district') && $request->district != '') {
                $query->where('d.district', $request->district);
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

            // Get unique districts for filter dropdown
            $districts = DB::table('tbl_doctor')
                ->join('users', 'tbl_doctor.doctor_id', '=', 'users.id')
                ->where('users.role', 'doctor')
                ->whereNotNull('tbl_doctor.district')
                ->where('tbl_doctor.district', '!=', '')
                ->select('tbl_doctor.district')
                ->distinct()
                ->orderBy('tbl_doctor.district')
                ->pluck('district');

            return view('front.doctors', compact('doctors', 'specializations', 'districts'));

        } catch (\Exception $e) {
            \Log::error('Error fetching doctors: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
    

//     public function createAppointment(Request $request, $id)
//     {
//         try {

//             $doctors = DB::table('tbl_doctor as d')
//                 ->join('users as u', 'd.doctor_id', '=', 'u.id')
//                 ->where('d.doctor_id', $id)
//                 ->select(
//                     'u.name as doctor_name',
//                     'd.appointment_fee', 
//                 )
//                 ->get();

//             // Get availability list
//             $avalabilityList = DB::table('tbl_availability as da')
//                 ->where('da.doctor_id', $id)
//                 ->where('da.date', '>=', date('Y-m-d'))
//                 ->where('status', 'available')
//                 ->where('number_of_tokens', '>', 0)
//                 ->orderBy('da.date', 'asc')
//                 ->orderBy('da.start_time_slot', 'asc')
//                 ->get();

//             return view('front.bookAppointmentShow', compact('avalabilityList','doctors'));

//         } catch (\Exception $e) {
//             Log::error('Error in createAppointment: ' . $e->getMessage());
//             return redirect()->back()->with('error', 'Failed to load appointment booking page.');
//         }
//     }


// public function getAvailabilityDetails($id)
// {
//     try {
//         $availability = DB::table('tbl_availability')
//             ->where('id', $id)
//             ->first();

//         if (!$availability) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Availability not found'
//             ], 404);
//         }

//         $doctor = DB::table('tbl_doctor as d')
//             ->join('users as u', 'd.doctor_id', '=', 'u.id')
//             ->where('d.doctor_id', $availability->doctor_id)
//             ->select('u.name as doctor_name', 'd.appointment_fee')
//             ->first();

//         return response()->json([
//             'success' => true,
//             'availability' => [
//                 'id' => $availability->id,
//                 'doctor_id' => $availability->doctor_id,
//                 'date' => $availability->date,
//                 'start_time_slot' => $availability->start_time_slot,
//                 'end_time_slot' => $availability->end_time_slot,
//                 'formatted_date' => \Carbon\Carbon::parse($availability->date)->format('M d, Y'),
//                 'formatted_time' => \Carbon\Carbon::parse($availability->start_time_slot)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($availability->end_time_slot)->format('h:i A')
//             ],
//             'doctor' => [
//                 'doctor_name' => $doctor->doctor_name ?? 'Doctor',
//                 'appointment_fee' => $doctor->appointment_fee ?? '0'
//             ]
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Error fetching availability details: ' . $e->getMessage());
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to load availability details'
//         ], 500);
//     }
// }

    public function createAppointment(Request $request, $id)
    {
        try {
            $doctors = DB::table('tbl_doctor as d')
                ->join('users as u', 'd.doctor_id', '=', 'u.id')
                ->where('d.doctor_id', $id)
                ->select(
                    'u.name as doctor_name',
                    'd.appointment_fee', 
                )
                ->get();

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

    public function getAvailabilityDetails($id)
    {
        try {
            $availability = DB::table('tbl_availability')
                ->where('id', $id)
                ->first();

            if (!$availability) {
                return response()->json([
                    'success' => false,
                    'message' => 'Availability not found'
                ], 404);
            }

            $doctor = DB::table('tbl_doctor as d')
                ->join('users as u', 'd.doctor_id', '=', 'u.id')
                ->where('d.doctor_id', $availability->doctor_id)
                ->select('u.name as doctor_name', 'd.appointment_fee')
                ->first();

            return response()->json([
                'success' => true,
                'availability' => [
                    'id' => $availability->id,
                    'doctor_id' => $availability->doctor_id,
                    'date' => $availability->date,
                    'start_time_slot' => $availability->start_time_slot,
                    'end_time_slot' => $availability->end_time_slot,
                    'formatted_date' => \Carbon\Carbon::parse($availability->date)->format('M d, Y'),
                    'formatted_time' => \Carbon\Carbon::parse($availability->start_time_slot)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($availability->end_time_slot)->format('h:i A')
                ],
                'doctor' => [
                    'doctor_name' => $doctor->doctor_name ?? 'Doctor',
                    'appointment_fee' => $doctor->appointment_fee ?? '0'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching availability details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load availability details'
            ], 500);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'availability_id' => 'required|integer',
                'amount' => 'required|numeric',
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));

            $availabilityId = $request->availability_id;
            $amount = $request->amount * 100; // Convert to cents

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'metadata' => [
                    'availability_id' => $availabilityId,
                    'patient_id' => Auth::id(),
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return response()->json([
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment intent creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent'
            ], 500);
        }
    }

    public function bookAppointment(Request $request)
    {
        try {
            $request->validate([
                'availability_id' => 'required|integer',
                'doctor_id' => 'required|integer',
                'appointment_fee' => 'required|numeric',
                'payment_method' => 'required|in:stripe,paypal',
                'patient_notes' => 'nullable|string|max:500',
                'stripe_payment_intent_id' => 'required_if:payment_method,stripe',
            ]);

            DB::beginTransaction();

            $availabilityId = $request->availability_id;
            $doctorId = $request->doctor_id;
            $appointmentFee = $request->appointment_fee;

            // Check availability
            $availability = DB::table('tbl_availability')
                ->where('id', $availabilityId)
                ->where('number_of_tokens', '>', 0)
                ->first();

            if (!$availability) {
                return redirect()->back()->with('error', 'Sorry, this time slot is no longer available.');
            }

            // Verify Stripe payment if applicable
            if ($request->payment_method === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));
                
                try {
                    $paymentIntent = PaymentIntent::retrieve($request->stripe_payment_intent_id);
                    
                    if ($paymentIntent->status !== 'succeeded') {
                        throw new \Exception('Payment not completed. Status: ' . $paymentIntent->status);
                    }
                    
                    $paymentIntentId = $request->stripe_payment_intent_id;
                } catch (\Exception $e) {
                    throw new \Exception('Payment verification failed: ' . $e->getMessage());
                }
            } else {
                $paymentIntentId = 'paypal_' . time();
            }

            // Create appointment
            $appointmentId = DB::table('tbl_appointment')->insertGetId([
                'patient_id' => Auth::id(),
                'doctor_id' => $doctorId,
                'availability_id' => $availabilityId,
                'appointment_date' => $availability->date,
                'appointment_time' => $availability->start_time_slot,
                'status' => 'confirmed',
                'payment_method' => $request->payment_method,
                'payment_intent_id' => $paymentIntentId,
                'amount_paid' => $appointmentFee,
                'patient_notes' => $request->patient_notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update availability
            DB::table('tbl_availability')
                ->where('id', $availabilityId)
                ->decrement('number_of_tokens');

            $updatedTokens = DB::table('tbl_availability')
                ->where('id', $availabilityId)
                ->value('number_of_tokens');

            if ($updatedTokens <= 0) {
                DB::table('tbl_availability')
                    ->where('id', $availabilityId)
                    ->update(['status' => 'booked']);
            }

            DB::commit();

            return redirect()->route('patient.appointments')
                ->with('success', 'Appointment booked successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Appointment booking error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to book appointment: ' . $e->getMessage());
        }
    }




/////////////////////////////////////////////////////////////////////////////////////////////////////
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
            ->orderBy('created_at', 'desc')
            ->get();
            
        $prescriptions = DB::table('tbl_prescriptions')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $medicalReports = DB::table('tbl_medical_reports')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $insuranceDocuments = DB::table('tbl_insurance_documents')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('front.account.my-details', compact(
            'medicalInfo', 
            'bloodTestReports', 
            'prescriptions', 
            'medicalReports', 
            'insuranceDocuments'
        ));
    }

    public function store(Request $request)
    {
        try {
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

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please check the form for errors.');
            }

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
                array_merge($data, [
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())')
                ])
            );

            // Handle file uploads with tbl_ prefix
            $fileFields = [
                'blood_test_reports' => 'tbl_blood_test_reports',
                'prescriptions' => 'tbl_prescriptions',
                'medical_reports' => 'tbl_medical_reports',
                'insurance_documents' => 'tbl_insurance_documents',
            ];

            $uploadedFilesCount = 0;
            foreach ($fileFields as $inputName => $tableName) {
                if ($request->hasFile($inputName)) {
                    $files = $request->file($inputName);
                    foreach ($files as $file) {
                        if ($file->isValid()) {
                            $filename = time() . '_' . uniqid() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                            $path = $file->storeAs("medical-documents/{$tableName}/{$userId}", $filename, 'public');
                            
                            DB::table($tableName)->insert([
                                'user_id' => $userId,
                                'file_path' => $filename,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $uploadedFilesCount++;
                        }
                    }
                }
            }

            $message = 'Medical information saved successfully!';
            if ($uploadedFilesCount > 0) {
                $message .= " {$uploadedFilesCount} document(s) uploaded.";
            }

            return redirect()->route('patient.myDetails')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Medical info save error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save medical information. Please try again.');
        }
    }

    public function viewDocument($documentType, $id)
    {
        try {
            $user = Auth::user();
            $table = $this->getTableName($documentType);
            
            if (!$table) {
                abort(404, 'Invalid document type');
            }

            $record = DB::table($table)
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$record) {
                abort(404, 'Document not found or access denied');
            }

            $filePath = "medical-documents/{$table}/{$user->id}/{$record->file_path}";
            
            if (!Storage::disk('public')->exists($filePath)) {
                Log::warning("Document file not found: {$filePath}");
                abort(404, 'Document file not found on server');
            }

            $fullPath = Storage::disk('public')->path($filePath);
            
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($record->file_path) . '"',
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Type-Options' => 'nosniff',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Document view error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            abort(404, 'Unable to view document');
        }
    }

    public function deleteDocument(Request $request)
    {
        try {
            $user = Auth::user();
            $documentType = $request->input('document_type');
            $id = $request->input('id');

            // Validate inputs
            if (!$documentType || !$id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Missing document type or ID'
                ], 400);
            }

            $table = $this->getTableName($documentType);
            
            if (!$table) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Invalid document type'
                ], 400);
            }

            // Find the document
            $record = DB::table($table)
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$record) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Document not found or access denied'
                ], 404);
            }

            // Delete the physical file
            $filePath = "medical-documents/{$table}/{$user->id}/{$record->file_path}";
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info("Deleted file: {$filePath}");
            } else {
                Log::warning("File not found for deletion: {$filePath}");
            }

            // Delete the database record
            $deleted = DB::table($table)->where('id', $id)->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Document deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete document record'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Document delete error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while deleting the document'
            ], 500);
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

            $record = DB::table($table)
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$record) {
                abort(404, 'Document not found or access denied');
            }

            $filePath = "medical-documents/{$table}/{$user->id}/{$record->file_path}";
            
            if (!Storage::disk('public')->exists($filePath)) {
                Log::warning("Download file not found: {$filePath}");
                abort(404, 'Document file not found on server');
            }

            return Storage::disk('public')->download($filePath, basename($record->file_path));
            
        } catch (\Exception $e) {
            Log::error('Document download error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            abort(404, 'Unable to download document');
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

    // public function storeAppointment(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $request->validate([
    //             'availability_id' => 'required|integer',
    //         ]);

    //         $availability = DB::table('tbl_availability')->where('id', $request->availability_id)->first();
    //         if (!$availability) {
    //             return response()->json(['success' => false, 'message' => 'Invalid slot selected.']);
    //         }

    //         // check if tokens are available
    //         if ($availability->number_of_tokens <= 0) {
    //             return response()->json(['success' => false, 'message' => 'No tokens available.']);
    //         }

    //         // check if user already booked
    //         $alreadyBooked = DB::table('tbl_doctor_appointment')
    //             ->where('user_id', Auth::id())
    //             ->where('availability_id', $availability->id)
    //             ->exists();

    //         if ($alreadyBooked) {
    //             return response()->json(['success' => false, 'message' => 'You have already booked this slot.']);
    //         }

    //         // FIX: Get doctor using the user ID from availability table
    //         // In your availability table, doctor_id is actually the user ID
    //         $doctor = DB::table('tbl_doctor')->where('doctor_id', $availability->doctor_id)->first();
            
    //         if (!$doctor) {
    //             \Log::error('Doctor not found for availability', [
    //                 'availability_id' => $availability->id,
    //                 'doctor_id_in_availability' => $availability->doctor_id,
    //                 'all_doctors' => DB::table('tbl_doctor')->select('id', 'doctor_id', 'doctor_name')->get()
    //             ]);
    //             return response()->json(['success' => false, 'message' => 'Doctor not found for this availability slot.']);
    //         }

    //         $fee = $doctor->appointment_fee ?? 0;

    //         // insert appointment
    //         DB::table('tbl_doctor_appointment')->insert([
    //             'doctor_id'        => $doctor->id, // Use doctor's auto-increment ID
    //             'user_id'          => Auth::id(),
    //             'availability_id'  => $availability->id,
    //             'appointment_date' => $availability->date,
    //             'start_time'       => $availability->start_time_slot,
    //             'end_time'         => $availability->end_time_slot,
    //             'fee'              => $fee,
    //             'status'           => 'pending',
    //             'created_at'       => now(),
    //             'updated_at'       => now(),
    //         ]);

    //         // reduce available tokens
    //         DB::table('tbl_availability')
    //             ->where('id', $availability->id)
    //             ->decrement('number_of_tokens', 1);

    //         DB::commit();

    //         return response()->json(['success' => true, 'message' => 'Appointment booked successfully with Dr. ' . $doctor->doctor_name . '!']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error booking appointment: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'message' => 'Failed to book appointment. Please try again.']);
    //     }
    // }

public function storeAppointment(Request $request)
{
    DB::beginTransaction();
    try {
        $request->validate([
            'availability_id' => 'required|integer',
        ]);

        $availability = DB::table('tbl_availability')->where('id', $request->availability_id)->first();
        if (!$availability) {
            return response()->json(['success' => false, 'message' => 'Invalid slot selected.']);
        }

        // check if tokens are available
        if ($availability->number_of_tokens <= 0) {
            return response()->json(['success' => false, 'message' => 'No tokens available.']);
        }

        // check if user already booked
        $alreadyBooked = DB::table('tbl_doctor_appointment')
            ->where('user_id', Auth::id())
            ->where('availability_id', $availability->id)
            ->exists();

        if ($alreadyBooked) {
            return response()->json(['success' => false, 'message' => 'You have already booked this slot.']);
        }

        // Get doctor using the user ID from availability table
        $doctor = DB::table('tbl_doctor')->where('doctor_id', $availability->doctor_id)->first();
        
        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found for this availability slot.']);
        }

        $fee = $doctor->appointment_fee ?? 0;

        // Calculate token number: Count existing appointments for this availability + 1
        $existingAppointmentsCount = DB::table('tbl_doctor_appointment')
            ->where('availability_id', $availability->id)
            ->count();

        $patientTokenNumber = $existingAppointmentsCount + 1;

        // insert appointment with token number
        $appointmentId = DB::table('tbl_doctor_appointment')->insertGetId([
            'doctor_id'        => $doctor->id,
            'user_id'          => Auth::id(),
            'availability_id'  => $availability->id,
            'appointment_date' => $availability->date,
            'start_time'       => $availability->start_time_slot,
            'end_time'         => $availability->end_time_slot,
            'fee'              => $fee,
            'token_number'     => $patientTokenNumber,
            'status'           => 'pending',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // reduce available tokens
        DB::table('tbl_availability')
            ->where('id', $availability->id)
            ->decrement('number_of_tokens', 1);

        DB::commit();

        return response()->json([
            'success' => true, 
            'message' => 'Appointment booked successfully!',
            'token_number' => $patientTokenNumber,
            'doctor_name' => $doctor->doctor_name
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error booking appointment: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to book appointment. Please try again.']);
    }
}
    // public function myAppointments()
    // {
    //     try {
    //         $appointments = DB::table('tbl_doctor_appointment as da')
    //             ->join('tbl_doctor as d', 'da.doctor_id', '=', 'd.id')
    //             ->join('users as u', 'd.doctor_id', '=', 'u.id')
    //             ->join('tbl_availability as av', 'da.availability_id', '=', 'av.id')
    //             ->where('da.user_id', Auth::id())
    //             ->select(
    //                 'da.id',
    //                 'da.appointment_date',
    //                 'da.start_time',
    //                 'da.end_time',
    //                 'da.fee',
    //                 'da.status',
    //                 'da.created_at',
    //                 'u.name as doctor_name',
    //                 'd.specialization',
    //                 'd.clinic_name',
    //                 'av.number_of_tokens'
    //             )
    //             ->orderBy('da.appointment_date', 'desc')
    //             ->orderBy('da.start_time', 'desc')
    //             ->get();

    //         return view('front.my-appointment', compact('appointments'));

    //     } catch (\Exception $e) {
    //         Log::error('Error fetching appointments: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Failed to load appointments.');
    //     }
    // }
    public function myAppointments()
{
    try {
        $appointments = DB::table('tbl_doctor_appointment as da')
            ->join('tbl_doctor as d', 'da.doctor_id', '=', 'd.id')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->join('tbl_availability as av', 'da.availability_id', '=', 'av.id')
            ->where('da.user_id', Auth::id())
            ->select(
                'da.id',
                'da.appointment_date',
                'da.start_time',
                'da.end_time',
                'da.fee',
                'da.status',
                'da.token_number', // Add token number
                'da.created_at',
                'u.name as doctor_name',
                'd.specialization',
                'd.clinic_name',
                'av.number_of_tokens as remaining_tokens'
            )
            ->orderBy('da.appointment_date', 'desc')
            ->orderBy('da.start_time', 'desc')
            ->get();

        return view('front.my-appointment', compact('appointments'));

    } catch (\Exception $e) {
        Log::error('Error fetching appointments: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to load appointments.');
    }
}
  

}











