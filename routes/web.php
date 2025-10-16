<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;


use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [AccountController::class, 'registration'])->name('account.registration');
    Route::post('/process-register', [AccountController::class, 'processRegistration'])->name('account.processRegistration');
    Route::get('/login', [AccountController::class, 'login'])->name('account.login');
    Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
    Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');
    Route::post('/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic'); // For updating profile picture
    Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');


});

Route::get('/forgot-password', [AccountController::class, 'forgotPassword'])->name('account.forgotPassword');
Route::post('/process-forgot-password', [AccountController::class, 'processForgotPassword'])->name('account.processForgotPassword');
Route::get('/reset-password/{token}', [AccountController::class, 'resetPassword'])->name('account.resetPassword');
Route::post('/process-reset-password',[AccountController::class,'processResetPassword'])->name('account.processResetPassword');
Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');



//admin
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');


//doctor
Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
Route::get('/doctor/profile', [DoctorController::class, 'profile'])->name('doctor.profile');
Route::get('/doctor/{id}', [DoctorController::class, 'doctorDetailsShow'])->name('doctor.details');
Route::post('/doctor/{id}/feedback', [DoctorController::class, 'storeFeedback'])->name('doctor.feedback');




// patient
Route::post('/patient/save-doctor', [PatientController::class, 'saveDoctor'])->name('patient.saveDoctor');
Route::get('/account/saved-doctors/{id}', [PatientController::class, 'savedDoctors'])->name('account.savedDoctors');



// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
