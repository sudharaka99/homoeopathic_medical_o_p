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

Route::get('/forgot-password', [AccountController::class, 'forgotPassword'])->name('account.forgotPassword');
Route::post('/process-forgot-password', [AccountController::class, 'processForgotPassword'])->name('account.processForgotPassword');
Route::get('/reset-password/{token}', [AccountController::class, 'resetPassword'])->name('account.resetPassword');
Route::post('/process-reset-password',[AccountController::class,'processResetPassword'])->name('account.processResetPassword');
Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');



//admin
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/doctors-list', [AdminController::class, 'doctorsList'])->name('admin.doctorslist');
Route::get('/admin/pending-doctors', [AdminController::class, 'pendingDoctors'])->name('admin.pendingDoctors');
Route::post('/admin/approve-doctor', [AdminController::class, 'approveDoctor'])->name('admin.approveDoctor');
Route::post('/admin/reject-doctor', [AdminController::class, 'rejectDoctor'])->name('admin.rejectDoctor');
Route::get('/admin/doctor-details', [AdminController::class, 'doctorDetails'])->name('admin.doctorDetails');

// Admin Contact Routes

// Admin Contact Routes
Route::get('/admin/user-feedback', [AdminController::class, 'userFeedback'])->name('admin.user-feedback');
Route::put('/admin/contact/{id}/status', [AdminController::class, 'updateMessageStatus'])->name('admin.contact.update-status');
Route::post('/admin/contact/reply', [AdminController::class, 'replyToMessage'])->name('admin.contact.reply');
Route::delete('/admin/contact/{id}', [AdminController::class, 'deleteMessage'])->name('admin.contact.destroy');

//manage doctors
Route::get('/admin/doctors-list', [AdminController::class, 'doctorsList'])->name('admin.doctorslist');
Route::get('/admin/doctor/{id}/edit', [AdminController::class, 'editDoctor'])->name('admin.doctor.edit');
Route::put('/admin/doctor/{id}', [AdminController::class, 'updateDoctor'])->name('admin.doctor.update');
Route::delete('/admin/doctor/{id}', [AdminController::class, 'deleteDoctor'])->name('admin.doctor.delete');
Route::post('/admin/doctor/{id}/toggle-status', [AdminController::class, 'toggleDoctorStatus'])->name('admin.doctor.toggle-status');

//////appointment
Route::get('/admin/appointments', [AdminController::class, 'allAppointment'])->name('admin.appointments');

////// manage patient
Route::get('/admin/patients', [AdminController::class, 'managePatientList'])->name('admin.patients');
Route::get('/admin/patients/{id}/edit', [AdminController::class, 'managePatientListEdit'])->name('admin.patients.edit');
Route::put('/admin/patients/{id}', [AdminController::class, 'managePatientListUpdate'])->name('admin.patients.update');
Route::delete('/admin/patients/{id}', [AdminController::class, 'managePatientListDestroy'])->name('admin.patients.destroy');

//doctor
Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
Route::get('/doctor/profile', [DoctorController::class, 'profile'])->name('doctor.profile');
Route::put('/doctor/update-profile', [DoctorController::class, 'updateDoctorProfile'])->name('doctor.updateProfile');
// Route::get('/doctor/add-availability', [DoctorController::class, 'addAvailability'])->name('doctor.addAvailability');
///// patiend documents and medical details
Route::get('/patients', [DoctorController::class, 'myPatients'])->name('doctor.patients');
Route::get('/patient/{patientId}/medical-details', [DoctorController::class, 'patientMedicalDetails'])->name('doctor.patient.medical-details');
Route::get('/patient/{patientId}/document/{documentType}/{id}/view', [DoctorController::class, 'viewPatientDocument'])->name('doctor.patient.document.view');
Route::get('/patient/{patientId}/document/{documentType}/{id}/download', [DoctorController::class, 'downloadPatientDocument'])->name('doctor.patient.document.download');




Route::get('/doctor/availability', [DoctorController::class, 'addAvailability'])->name('doctor.addAvailability');
Route::post('/doctor/availability-store', [DoctorController::class, 'storeAvailability'])->name('doctor.availability.store');
Route::get('/doctor/get-availability/{id}', [DoctorController::class, 'getAvailability'])->name('doctor.availability.get');
Route::put('/doctor/availability/update/{id}', [DoctorController::class, 'updateAvailability'])->name('doctor.availability.update');
Route::delete('/doctor/availability/delete/{id}', [DoctorController::class, 'deleteAvailability'])->name('doctor.availability.delete');

//

Route::get('/appointments', [DoctorController::class, 'manageAppointments'])->name('doctor.appointments');
Route::post('/appointments/{id}/update-status', [DoctorController::class, 'updateStatus'])->name('doctor.appointments.updateStatus');
Route::get('/appointments/{id}/details', [DoctorController::class, 'getAppointmentDetails'])->name('doctor.appointments.details');

// Route::post('/doctor/{id}/feedback', [DoctorController::class, 'storeFeedback'])->name('doctor.feedback');

// Feedback Routes
Route::post('/doctor/{id}/feedback', [DoctorController::class, 'storeFeedback'])->name('doctor.feedback');
Route::get('/doctor/{id}/user-feedback', [DoctorController::class, 'getUserFeedback'])->name('doctor.getUserFeedback');
Route::get('/doctor/{id}', [DoctorController::class, 'doctorDetailsShow'])->name('doctor.details');


// patient
Route::post('/patient/save-doctor', [PatientController::class, 'saveDoctor'])->name('patient.saveDoctor');
Route::get('/account/saved-doctors/{id}', [PatientController::class, 'savedDoctors'])->name('account.savedDoctors');
Route::get('/patient/find-doctors', [PatientController::class, 'findDoctors'])->name('patient.findDoctors');



Route::get('/create-appointment/{id}', [PatientController::class, 'createAppointment'])->name('patient.bookAppointment');
Route::get('/patient/get-availability-details/{id}', [PatientController::class, 'getAvailabilityDetails'])->name('patient.get.availability.details');


Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');

Route::post('/book-appointment', [PatientController::class, 'bookAppointment'])->name('patient.book.appointment');
Route::get('/payment/success', [PatientController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [PatientController::class, 'paymentCancel'])->name('payment.cancel');

Route::get('/health-tips/hydration', [PatientController::class, 'hydration'])->name('health-tips.hydration');
Route::get('/health-tips/exercise', [PatientController::class, 'exercise'])->name('health-tips.exercise');
Route::get('/health-tips/sleep', [PatientController::class, 'sleep'])->name('health-tips.sleep');
Route::get('/health-tips/diet', [PatientController::class, 'diet'])->name('health-tips.diet');


Route::get('/patient/my-details', [PatientController::class, 'myDetails'])->name('patient.myDetails');
Route::post('/medical-information', [PatientController::class, 'store'])->name('account.medicalInfo.store');   
Route::get('/medical-documents/{documentType}/{filename}', [PatientController::class, 'viewDocument'])->name('account.medicalInfo.viewDocument');   
Route::get('/medical-documents/{documentType}/{filename}/download', [PatientController::class, 'downloadDocument'])->name('account.medicalInfo.downloadDocument');   
Route::delete('/medical-documents/delete', [PatientController::class, 'deleteDocument'])->name('account.medicalInfo.deleteDocument');


Route::post('/book-appointment-store', [PatientController::class, 'storeAppointment'])->name('front.bookAppointment');
// Patient Appointment Routes
Route::get('/my-appointments', [PatientController::class, 'myAppointments'])->name('patient.appointments');
Route::get('/appointment-details/{id}', [PatientController::class, 'getAppointmentDetails'])->name('patient.appointment.details');
Route::post('/create-payment-intent', [PatientController::class, 'createPaymentIntent'])->name('patient.create.payment.intent');
Route::post('/book-appointment', [PatientController::class, 'bookAppointment'])->name('patient.book.appointment');
Route::post('/appointments/process-payment', [PatientController::class, 'processPayment'])->name('patient.process.payment');
Route::post('/appointments/{id}/cancel', [PatientController::class, 'cancelAppointment'])->name('patient.cancel.appointment');


Route::post('/appointments/{id}/create-meeting', [PatientController::class, 'createZoomMeeting'])->name('patient.create.meeting');
Route::get('/meeting/{id}', [PatientController::class, 'joinMeeting'])->name('patient.join.meeting');


//contact page
// Contact Routes
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

// Admin Contact Management Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/contacts', [HomeController::class, 'adminIndex'])->name('admin.contacts.index');
    Route::get('/contacts/{id}', [HomeController::class, 'adminShow'])->name('admin.contacts.show');
    Route::post('/contacts/{id}/status', [HomeController::class, 'adminUpdateStatus'])->name('admin.contacts.update-status');
    Route::post('/contacts/{id}/reply', [HomeController::class, 'adminSendReply'])->name('admin.contacts.send-reply');
    Route::delete('/contacts/{id}', [HomeController::class, 'adminDestroy'])->name('admin.contacts.destroy');
});

});







// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });
