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

        // Featured doctors query
        $featuredDoctorsQuery = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_name',
                's.name as specialization_name',
                'd.clinic_name',
                'd.district', // Make sure this is selected
                'd.years_experience',
                'u.image'
            )
            ->where('d.is_admin_confirmed', 1);

        // Apply search filters
        if ($keyword) {
            $featuredDoctorsQuery->where(function($query) use ($keyword) {
                $query->where('d.doctor_name', 'like', "%$keyword%")
                    ->orWhere('s.name', 'like', "%$keyword%")
                    ->orWhere('d.clinic_name', 'like', "%$keyword%");
            });
        }
        
        if ($specialization) {
            $featuredDoctorsQuery->where('d.specialization', $specialization);
        }
        
        // FIXED: Search in district column
        if ($district) {
            $featuredDoctorsQuery->where('d.district', 'like', "%$district%");
        }

        $featuredDoctors = $featuredDoctorsQuery->take(6)->get();

        // Latest doctors query (apply same fixes)
        $latestDoctorsQuery = DB::table('tbl_doctor as d')
            ->join('users as u', 'd.doctor_id', '=', 'u.id')
            ->join('tbl_specializations as s', 'd.specialization', '=', 's.id')
            ->select(
                'd.id',
                'd.doctor_name',
                's.name as specialization_name',
                'd.clinic_name',
                'd.district', // Make sure this is selected
                'd.years_experience',
                'u.image'
            )
            ->where('d.is_admin_confirmed', 1);

        // Apply same filters
        if ($keyword) {
            $latestDoctorsQuery->where(function($query) use ($keyword) {
                $query->where('d.doctor_name', 'like', "%$keyword%")
                    ->orWhere('s.name', 'like', "%$keyword%")
                    ->orWhere('d.clinic_name', 'like', "%$keyword%");
            });
        }
        
        if ($specialization) {
            $latestDoctorsQuery->where('d.specialization', $specialization);
        }
        
        // FIXED: Search in district column
        if ($district) {
            $latestDoctorsQuery->where('d.district', 'like', "%$district%");
        }

        $latestDoctors = $latestDoctorsQuery->orderBy('d.id', 'desc')->take(6)->get();

        return view('front.home', compact('specializations', 'featuredDoctors', 'latestDoctors'));
    }


}


