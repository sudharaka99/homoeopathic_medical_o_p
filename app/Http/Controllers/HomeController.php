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
        $location = $request->input('location');

        // Specializations with doctor counts
        $specializations = DB::table('tbl_doctor as d')
            ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select('s.id', 's.name as specialization_name', DB::raw('COUNT(d.id) as doctor_count'))
            ->groupBy('s.id', 's.name')
            ->get();

        // Featured doctors query
        $featuredDoctorsQuery = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_name',
                's.name as specialization_name',
                'd.clinic_name',
                'd.years_experience',
                'u.image'
            )
            ->where('d.is_admin_confirmed', 1);

        // Apply search filters
        if ($keyword) {
            $featuredDoctorsQuery->where('d.doctor_name', 'like', "%$keyword%");
        }
        if ($specialization) {
            $featuredDoctorsQuery->where('d.specialization', $specialization);
        }
        if ($location) {
            $featuredDoctorsQuery->where('d.clinic_name', 'like', "%$location%");
        }

        $featuredDoctors = $featuredDoctorsQuery->take(6)->get();

        // Latest doctors query (similar filtering)
        $latestDoctorsQuery = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_name',
                's.name as specialization_name',
                'd.clinic_name',
                'd.years_experience',
                'u.image'
            )
            ->where('d.is_admin_confirmed', 1);

        if ($keyword) {
            $latestDoctorsQuery->where('d.doctor_name', 'like', "%$keyword%");
        }
        if ($specialization) {
            $latestDoctorsQuery->where('d.specialization', $specialization);
        }
        if ($location) {
            $latestDoctorsQuery->where('d.clinic_name', 'like', "%$location%");
        }

        $latestDoctors = $latestDoctorsQuery->orderBy('d.id', 'desc')->take(6)->get();

        return view('front.home', compact('specializations', 'featuredDoctors', 'latestDoctors'));
    }



}


