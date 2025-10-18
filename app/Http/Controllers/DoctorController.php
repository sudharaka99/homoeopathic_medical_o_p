<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        ->where('id', $id)
        ->first();

        return view('doctor.profile', compact('user'));
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

    // public function addAvailability(Request $request)
    // {
    //     return view('front.account.doctor.addAvalability');
    // }

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

            // Get existing availabilities
            $availabilities = DB::table('tbl_availability')
                ->where('doctor_id', Auth::id())
                ->where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date', 'asc')
                ->orderBy('start_time_slot', 'asc')
                ->get();

            return view('front.account.doctor.addAvalability', compact('doctor', 'availabilities'));

        } catch (\Exception $e) {
            \Log::error('Error in addAvailability: ' . $e->getMessage());
            return redirect()->route('account.profile')->with('error', 'Something went wrong. Please try again.');
        }
    }

    // Store availability data
    public function storeAvailability(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'start_time_slot' => 'required',
                'end_time_slot' => 'required|after:start_time_slot',
                'number_of_tokens' => 'required|integer|min:1|max:10',
                'notes' => 'nullable|string|max:500',
            ]);

            // Check for overlapping time slots
            $overlappingSlot = DB::table('tbl_availability')
                ->where('doctor_id', Auth::id())
                ->where('date', $request->date)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time_slot', [$request->start_time_slot, $request->end_time_slot])
                          ->orWhereBetween('end_time_slot', [$request->start_time_slot, $request->end_time_slot])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_time_slot', '<=', $request->start_time_slot)
                                ->where('end_time_slot', '>=', $request->end_time_slot);
                          });
                })
                ->first();

            if ($overlappingSlot) {
                return redirect()->back()
                    ->with('error', 'This time slot overlaps with an existing availability. Please choose a different time.')
                    ->withInput();
            }

            // Insert availability
            DB::table('tbl_availability')->insert([
                'doctor_id' => Auth::id(),
                'date' => $request->date,
                'start_time_slot' => $request->start_time_slot,
                'end_time_slot' => $request->end_time_slot,
                'number_of_tokens' => $request->number_of_tokens,
                'notes' => $request->notes,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('doctor.addAvailability')
                ->with('success', 'Availability slot added successfully!');

        } catch (\Exception $e) {
            \Log::error('Error storing availability: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to add availability slot. Please try again.')
                ->withInput();
        }
    }

    // Manage availability (view all slots)
    public function manageAvailability()
    {
        try {
            $availabilities = DB::table('tbl_availability')
                ->where('doctor_id', Auth::id())
                ->orderBy('date', 'desc')
                ->orderBy('start_time_slot', 'desc')
                ->paginate(10);

            return view('front.account.doctor.manageAvailability', compact('availabilities'));

        } catch (\Exception $e) {
            \Log::error('Error in manageAvailability: ' . $e->getMessage());
            return redirect()->route('doctor.addAvailability')
                ->with('error', 'Failed to load availability slots.');
        }
    }

    // Delete availability slot
    public function deleteAvailability($id)
    {
        try {
            $availability = DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->first();

            if (!$availability) {
                return redirect()->back()->with('error', 'Availability slot not found.');
            }

            // Check if slot is already booked
            if ($availability->status === 'booked') {
                return redirect()->back()->with('error', 'Cannot delete a booked slot. Please contact patients first.');
            }

            DB::table('tbl_availability')
                ->where('id', $id)
                ->where('doctor_id', Auth::id())
                ->delete();

            return redirect()->back()->with('success', 'Availability slot deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Error deleting availability: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete availability slot.');
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

        // Get doctor details
        $doctor = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->select('d.*', 'u.name as doctor_name', 'u.email', 'u.mobile')
            ->where('d.doctor_id', Auth::id())
            ->first();

        return view('front.account.doctor.editAvailability', compact('availability', 'doctor'));

    } catch (\Exception $e) {
        \Log::error('Error in editAvailability: ' . $e->getMessage());
        return redirect()->route('doctor.addAvailability')
            ->with('error', 'Something went wrong. Please try again.');
    }
}

// Update availability slot
public function updateAvailability(Request $request, $id)
{
    try {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time_slot' => 'required',
            'end_time_slot' => 'required|after:start_time_slot',
            'number_of_tokens' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if availability slot exists and belongs to doctor
        $availability = DB::table('tbl_availability')
            ->where('id', $id)
            ->where('doctor_id', Auth::id())
            ->first();

        if (!$availability) {
            return redirect()->route('doctor.addAvailability')
                ->with('error', 'Availability slot not found.');
        }

        // Check for overlapping time slots (excluding current slot)
        $overlappingSlot = DB::table('tbl_availability')
            ->where('doctor_id', Auth::id())
            ->where('date', $request->date)
            ->where('id', '!=', $id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time_slot', [$request->start_time_slot, $request->end_time_slot])
                      ->orWhereBetween('end_time_slot', [$request->start_time_slot, $request->end_time_slot])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_time_slot', '<=', $request->start_time_slot)
                            ->where('end_time_slot', '>=', $request->end_time_slot);
                      });
            })
            ->first();

        if ($overlappingSlot) {
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
                'start_time_slot' => $request->start_time_slot,
                'end_time_slot' => $request->end_time_slot,
                'number_of_tokens' => $request->number_of_tokens,
                'notes' => $request->notes,
                'updated_at' => now(),
            ]);

        return redirect()->route('doctor.addAvailability')
            ->with('success', 'Availability slot updated successfully!');

    } catch (\Exception $e) {
        \Log::error('Error updating availability: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to update availability slot. Please try again.')
            ->withInput();
    }
}
}



