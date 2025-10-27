<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
public function index(Request $request)
{
    $keyword = $request->input('keyword');
    $specialization = $request->input('specialization');
    $district = $request->input('district');

    // Specializations with doctor counts
    $specializations = DB::table('tbl_doctor as d')
        ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
        ->select('s.id', 's.name as specialization_name', DB::raw('COUNT(d.id) as doctor_count'))
        ->where('d.is_admin_confirmed', 1)
        ->groupBy('s.id', 's.name')
        ->get();

    // Get available districts for suggestions
    $availableDistricts = DB::table('tbl_doctor')
        ->where('is_admin_confirmed', 1)
        ->whereNotNull('district')
        ->where('district', '!=', '')
        ->select('district')
        ->distinct()
        ->orderBy('district')
        ->pluck('district')
        ->toArray();

    // Featured doctors query
    $featuredDoctorsQuery = DB::table('tbl_doctor as d')
        ->join('users as u', 'd.doctor_id', '=', 'u.id')
        ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
        ->select(
            'd.id',
            'd.doctor_name',
            's.name as specialization_name',
            'd.clinic_name',
            'd.district',
            'd.years_experience',
            'u.image'
        )
        ->where('d.is_admin_confirmed', 1);

    // Apply search filters - CASE INSENSITIVE
    if ($keyword) {
        $featuredDoctorsQuery->where(function($query) use ($keyword) {
            $query->where(DB::raw('LOWER(d.doctor_name)'), 'like', '%' . strtolower($keyword) . '%')
                  ->orWhere(DB::raw('LOWER(s.name)'), 'like', '%' . strtolower($keyword) . '%')
                  ->orWhere(DB::raw('LOWER(d.clinic_name)'), 'like', '%' . strtolower($keyword) . '%');
        });
    }
    
    if ($specialization) {
        $featuredDoctorsQuery->where('d.specialization', $specialization);
    }
    
    // Case-insensitive district search
    if ($district) {
        $featuredDoctorsQuery->where(DB::raw('LOWER(d.district)'), 'like', '%' . strtolower($district) . '%');
    }

    $featuredDoctors = $featuredDoctorsQuery->take(6)->get();

    // Latest doctors query
    $latestDoctorsQuery = DB::table('tbl_doctor as d')
        ->join('users as u', 'd.doctor_id', '=', 'u.id')
        ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
        ->select(
            'd.id',
            'd.doctor_name',
            's.name as specialization_name',
            'd.clinic_name',
            'd.district',
            'd.years_experience',
            'u.image'
        )
        ->where('d.is_admin_confirmed', 1);

    // Apply same filters
    if ($keyword) {
        $latestDoctorsQuery->where(function($query) use ($keyword) {
            $query->where(DB::raw('LOWER(d.doctor_name)'), 'like', '%' . strtolower($keyword) . '%')
                  ->orWhere(DB::raw('LOWER(s.name)'), 'like', '%' . strtolower($keyword) . '%')
                  ->orWhere(DB::raw('LOWER(d.clinic_name)'), 'like', '%' . strtolower($keyword) . '%');
        });
    }
    
    if ($specialization) {
        $latestDoctorsQuery->where('d.specialization', $specialization);
    }
    
    if ($district) {
        $latestDoctorsQuery->where(DB::raw('LOWER(d.district)'), 'like', '%' . strtolower($district) . '%');
    }

    $latestDoctors = $latestDoctorsQuery->orderBy('d.id', 'desc')->take(6)->get();

    return view('front.home', compact('specializations', 'featuredDoctors', 'latestDoctors', 'availableDistricts'));
}


}


