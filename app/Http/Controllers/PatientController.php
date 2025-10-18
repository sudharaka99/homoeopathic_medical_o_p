<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class PatientController extends Controller
{
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

            // Paginate with 12 items per page and preserve query string
            $doctors = $query->paginate(10)->withQueryString();

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

    public function createAppointment(Request $request , $id)
    {
        return view('front.bookAppointmentShow');
    }

}




