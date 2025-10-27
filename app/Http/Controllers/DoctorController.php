<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    public function index()
    {
        return view('front.account.doctor.dashboard');
    }

  public function profile()
{
    $id = Auth::id();

    $user = DB::table('users')
        ->leftJoin('tbl_doctor as d', 'users.id', '=', 'd.doctor_id')
        ->select('users.*', 'd.specialization', 'd.years_experience', 'd.clinic_name', 'd.license_number', 'd.district', 'd.qualification', 'd.license_image', 'd.appointment_fee', 'd.is_admin_confirmed')
        ->where('users.id', $id)
        ->first(); // Use first() instead of get() since we're getting a single user

    // Get specializations for dropdown
    $specializations = DB::table('tbl_specializations')->get(); // Adjust table name if different

    return view('front.account.doctor.profile', compact('user', 'specializations'));
}


public function updateDoctorProfile(Request $request)
{
    $user = Auth::user();
    
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' .$user->id. ',id',
        'mobile' => 'required|string|max:15',
        'designation' => 'nullable|string|max:255',
        'license_number' => 'required|string|max:50',
        'qualification' => 'required|string',
        'specialization' => 'required|integer',
        'district' => 'required|string|max:255',
        'years_experience' => 'nullable|integer',
        'clinic_name' => 'nullable|string|max:255',
        'appointment_fee' => 'nullable|numeric',
        'license_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
    ]);

    if ($validator->passes()) {
        // Update user table
        DB::table('users')->where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'designation' => $request->designation,
            'updated_at' => now()
        ]);

        // Check if doctor record exists
        $doctorExists = DB::table('tbl_doctor')->where('doctor_id', $user->id)->exists();
        
        // Prepare doctor data - only fields that exist in tbl_doctor
        $doctorData = [
            'license_number' => $request->license_number,
            'qualification' => $request->qualification,
            'specialization' => $request->specialization,
            'district' => $request->district,
            'years_experience' => $request->years_experience,
            'clinic_name' => $request->clinic_name,
            'appointment_fee' => $request->appointment_fee,
            'is_admin_confirmed' => 0, // Reset to pending when updated
        ];

        // Handle license image upload
        if ($request->hasFile('license_image')) {
            $imagePath = $request->file('license_image')->store('doctor_licenses', 'public');
            $doctorData['license_image'] = $imagePath;
        }

        if ($doctorExists) {
            // Update existing doctor record
            DB::table('tbl_doctor')->where('doctor_id', $user->id)->update($doctorData);
        } else {
            // Create new doctor record
            $doctorData['doctor_id'] = $user->id;
            $doctorData['doctor_name'] = $request->name; // Use doctor_name column
            DB::table('tbl_doctor')->insert($doctorData);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}

    // public function doctorDetailsShow($id)
    // {
    //     $doctor = DB::table('tbl_doctor as d')
    //         ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
    //         ->join('users as u', 'd.doctor_id', '=', 'u.id')
    //         ->leftJoin('tbl_feedback as f', 'f.doctor_id', '=', 'd.id')
    //         ->select(
    //             'd.*',
    //             's.name as specialization_name',
    //             'u.email',
    //             'u.mobile',
    //             'u.image'
    //         )
    //         ->where('d.id', $id)
    //         ->first();

    //     if (!$doctor) {
    //         return redirect()->route('doctors.list')->with('error', 'Doctor not found.');
    //     }

    //     // Fetch feedbacks separately
    //     $feedbacks = DB::table('tbl_feedback as f')
    //         ->join('users as u', 'f.user_id', '=', 'u.id')
    //         ->select('f.*', 'u.name as user_name')
    //         ->where('f.doctor_id', $id)
    //         ->orderBy('f.created_at', 'desc')
    //         ->get();

    //     // Attach feedback list
    //     $doctor->feedback = $feedbacks;

    //     return view('front.doctorDetails', compact('doctor'));
    // }

    public function doctorDetailsShow($id)
    {
        try {
            $doctor = DB::table('tbl_doctor as d')
                ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
                ->join('users as u', 'd.doctor_id', '=', 'u.id')
                ->select(
                    'd.*',
                    's.name as specialization_name',
                    'u.email',
                    'u.mobile',
                    'u.profile_photo_path as image', // Fixed column name
                    'u.name as doctor_name' // Added doctor name from users table
                )
                ->where('d.id', $id)
                ->first();

            if (!$doctor) {
                return redirect()->back()->with('error', 'Doctor not found.');
            }

            // Fetch feedbacks separately
            $feedbacks = DB::table('tbl_feedback as f')
                ->join('users as u', 'f.user_id', '=', 'u.id')
                ->select('f.*', 'u.name as user_name')
                ->where('f.doctor_id', $id)
                ->orderBy('f.created_at', 'desc')
                ->get();

            // Check if doctor is saved by current user
            $isSaved = false;
            $savedNote = '';

            if (Auth::check()) {
                $savedDoctor = DB::table('tbl_saved_doctors')
                    ->where('user_id', Auth::id())
                    ->where('doctor_id', $id)
                    ->first();

                if ($savedDoctor) {
                    $isSaved = true;
                    $savedNote = $savedDoctor->save_reason ?? '';
                }
            }

            // Attach feedback list and save status to doctor object
            $doctor->feedback = $feedbacks;
            $doctor->is_saved = $isSaved;
            $doctor->saved_note = $savedNote;

            return view('front.doctorDetails', compact('doctor'));

        } catch (\Exception $e) {
            \Log::error('Error fetching doctor details: ' . $e->getMessage());
            return redirect()->route('doctors.list')->with('error', 'Something went wrong. Please try again.');
        }
    }


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


    // Show add availability page
    public function addAvailability()
    {
        try {
            // Check if doctor record exists
            $doctorExists = DB::table('tbl_doctor')
                ->where('doctor_id', Auth::id())
                ->exists();

            if (!$doctorExists) {
                return redirect()->route('account.profile')
                    ->with('error', 'Please complete your doctor profile before adding availability.');
            }

            // Get doctor details
            $doctor = DB::table('tbl_doctor as d')
                ->join('users as u', 'd.doctor_id', '=', 'u.id')
                ->select('d.*', 'u.name as doctor_name', 'u.email', 'u.mobile')
                ->where('d.doctor_id', Auth::id())
                ->first();

            // Get existing availabilities for current and future dates
            $availabilities = DB::table('tbl_availability')
                ->where('doctor_id', Auth::id())
                ->where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date', 'asc')
                ->orderBy('start_time_slot', 'asc')
                ->get();

            return view('front.account.doctor.addAvalability', compact('doctor', 'availabilities'));

        } catch (\Exception $e) {
            Log::error('Error in addAvailability: ' . $e->getMessage());
            return redirect()->route('account.profile')->with('error', 'Something went wrong. Please try again.');
        }
    }

    // Store availability data
    public function storeAvailability(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'start_time_slot' => 'required|date_format:H:i',
                'end_time_slot' => 'required|date_format:H:i|after:start_time_slot',
                'number_of_tokens' => 'required|integer|min:1|max:10',
                'notes' => 'nullable|string|max:500',
            ]);

            // Convert times to proper format
            $startTime = $request->start_time_slot;
            $endTime = $request->end_time_slot;

            // Check if end time is after start time
            if (strtotime($endTime) <= strtotime($startTime)) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'End time must be after start time.')
                    ->withInput();
            }

            // Check for overlapping time slots
            $overlappingSlot = DB::table('tbl_availability')
                ->where('doctor_id', Auth::id())
                ->where('date', $request->date)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time_slot', '<', $endTime)
                          ->where('end_time_slot', '>', $startTime);
                    });
                })
                ->first();

            if ($overlappingSlot) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'This time slot overlaps with an existing availability. Please choose a different time.')
                    ->withInput();
            }

            // Insert availability
            DB::table('tbl_availability')->insert([
                'doctor_id' => Auth::id(),
                'date' => $request->date,
                'start_time_slot' => $startTime,
                'end_time_slot' => $endTime,
                'number_of_tokens' => $request->number_of_tokens,
                'notes' => $request->notes,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('doctor.addAvailability')
                ->with('success', 'Availability slot added successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing availability: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to add availability slot. Please try again.')
                ->withInput();
        }
    }

    // Get availability slot data for editing (AJAX)
    public function getAvailability($id)
    {
        try {
            $availability = DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->first();

            if (!$availability) {
                return response()->json([
                    'success' => false,
                    'message' => 'Availability slot not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'slot' => $availability
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load slot data.'
            ], 500);
        }
    }

    // Show edit availability form
    public function editAvailability($id)
    {
        try {
            // Get the availability slot
            $availability = DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->first();

            if (!$availability) {
                return redirect()->route('doctor.addAvailability')
                    ->with('error', 'Availability slot not found.');
            }

            // Check if slot can be edited (only available slots)
            if ($availability->status !== 'available') {
                return redirect()->route('doctor.addAvailability')
                    ->with('error', 'Only available slots can be edited.');
            }

            // Get user details
            $user = DB::table('users')
                ->where('id', Auth::id())
                ->first();

            // Check if doctor profile exists
            $doctorExists = DB::table('tbl_doctor')
                ->where('doctor_id', Auth::id())
                ->exists();

            if ($doctorExists) {
                $doctor = DB::table('tbl_doctor as d')
                    ->join('users as u', 'd.doctor_id', '=', 'u.id')
                    ->select('d.*', 'u.name as doctor_name', 'u.email', 'u.mobile')
                    ->where('d.doctor_id', Auth::id())
                    ->first();
            } else {
                // Create a dummy doctor object with user data
                $doctor = (object)[
                    'doctor_name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'qualification' => 'Not specified',
                    'years_experience' => 0,
                    'clinic_name' => 'Your Clinic',
                    'license_number' => 'Pending'
                ];
            }

            return view('front.account.doctor.editAvailability', compact('availability', 'doctor'));

        } catch (\Exception $e) {
            Log::error('Error in editAvailability: ' . $e->getMessage());
            return redirect()->route('doctor.addAvailability')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    // Update availability slot
    public function updateAvailability(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'start_time_slot' => 'required|date_format:H:i',
                'end_time_slot' => 'required|date_format:H:i|after:start_time_slot',
                'number_of_tokens' => 'required|integer|min:1|max:10',
                'notes' => 'nullable|string|max:500',
            ]);

            // Check if availability slot exists and belongs to doctor
            $availability = DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->first();

            if (!$availability) {
                DB::rollback();
                return redirect()->route('doctor.addAvailability')
                    ->with('error', 'Availability slot not found.');
            }

            // Check if slot can be edited
            if ($availability->status !== 'available') {
                DB::rollback();
                return redirect()->route('doctor.addAvailability')
                    ->with('error', 'Only available slots can be edited.');
            }

            // Convert times to proper format
            $startTime = $request->start_time_slot;
            $endTime = $request->end_time_slot;

            // Check if end time is after start time
            if (strtotime($endTime) <= strtotime($startTime)) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'End time must be after start time.')
                    ->withInput();
            }

            // Check for overlapping time slots (excluding current slot)
            $overlappingSlot = DB::table('tbl_availability')
                ->where('doctor_id', Auth::id())
                ->where('date', $request->date)
                ->where('id', '!=', $id)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->where('start_time_slot', '<', $endTime)
                          ->where('end_time_slot', '>', $startTime);
                    });
                })
                ->first();

            if ($overlappingSlot) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'This time slot overlaps with another availability. Please choose a different time.')
                    ->withInput();
            }

            // Update the availability slot
            DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->update([
                    'date' => $request->date,
                    'start_time_slot' => $startTime,
                    'end_time_slot' => $endTime,
                    'number_of_tokens' => $request->number_of_tokens,
                    'notes' => $request->notes,
                    'updated_at' => now(),
                ]);

            DB::commit();
            return redirect()->back()->with('success', 'Availability slot updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating availability: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update availability slot. Please try again.')
                ->withInput();
        }
    }

    // Delete availability slot
    public function deleteAvailability($id)
    {
        DB::beginTransaction();
        try {
            $availability = DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->first();

            if (!$availability) {
                DB::rollback();
                return redirect()->back()->with('error', 'Availability slot not found.');
            }

            // Check if slot is already booked
            if ($availability->status === 'booked') {
                DB::rollback();
                return redirect()->back()->with('error', 'Cannot delete a booked slot. Please contact patients first.');
            }

            DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Availability slot deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting availability: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete availability slot.');
        }
    }
}



