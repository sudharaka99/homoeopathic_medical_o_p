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
use App\Mail\ResetPasswordEmail;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;  // or use Imagick if you prefer
use Illuminate\Support\Facades\File;



class AccountController extends Controller
{
     public function registration() 
    {

        $specializations = DB::table('tbl_specializations')
            ->where('is_active', 1)
            ->get();

        return view('front.account.registration', compact('specializations'));
    }


    

    public function authenticate(Request $request)
    {
        // Step 1: Validate input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        try {
            // Step 2: Fetch user record
            $user = DB::table('users')->where('email', $request->email)->first();

            if (!$user) {
                Log::warning('Login failed: user not found', ['email' => $request->email]);
                return redirect()->route('account.login')
                    ->with('error', 'Either Email/Password is incorrect');
            }

            // Step 3: Check password manually
            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Login failed: incorrect password', ['email' => $request->email]);
                return redirect()->route('account.login')
                    ->with('error', 'Either Email/Password is incorrect');
            }

            // 🩺 Step 4: Extra check for doctor approval
            if ($user->role === 'doctor') {
                $doctor = DB::table('tbl_doctor')
                    ->where('doctor_id', $user->id)
                    ->first();

                if (!$doctor) {
                    Log::warning('Doctor record not found', ['user_id' => $user->id]);
                    return redirect()->route('account.login')
                        ->with('error', 'Doctor record not found. Please contact admin.');
                }

                if ($doctor->is_admin_confirmed != 1) {
                    Log::info('Doctor login blocked (not approved yet)', ['email' => $user->email]);
                    return redirect()->route('account.login')
                        ->with('error', 'Your account is pending admin approval. Please wait.');
                }
            }

            // Step 5: Log in user
            Auth::loginUsingId($user->id);
            Log::info('User authenticated successfully', ['email' => $user->email, 'role' => $user->role]);

            // Step 6: Redirect based on role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'doctor':
                    return redirect()->route('doctor.dashboard');
                default:
                    return redirect()->route('patient.dashboard');
            }

        } catch (\Exception $e) {
            Log::error('Authentication error: ' . $e->getMessage());

            return redirect()->route('account.login')
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    
    

public function profile()
{
    // Get the authenticated user's ID
    $id = Auth::id();

    // Fetch user data using Query Builder
    $user = DB::table('users')
        ->where('id', $id)
        ->first();

    // Return data to the view
    return view('front.account.profile', [
        'user' => $user,
    ]);
}



    public function updateProfile(Request $request) {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' .$id. ',id'
        ]);

        if ($validator->passes()) {  // Corrected the spacing issue
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();

            session()->flash('success', 'Profile updated successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $request) {

        $id = Auth::user()->id; 

        $validator = Validator::make($request->all(),[
            'image' => 'required|image'
        ]);

        if($validator->passes()){

            $image= $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName= $id.'-'.time().'.'.$ext;
            $image->move(public_path('/profile_pic'),$imageName);

        
            //create a small thumbnail

            $sourcePath= public_path('/profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);
            
            // crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
            $image->cover(150, 150);
            $image->toPng()->save(public_path('/profile_pic/thumb/'.$imageName));

            //Delete Old profile Pic
            File::delete(public_path('/profile_pic/thumb/'.Auth::user()->image));
            File::delete(public_path('/profile_pic/'.Auth::user()->image));

            User::where('id',$id)->update(['image'=> $imageName]);
            session()->flash('success','Profile PIcture Update successfully.');

            return response()->json([
                'status'=> true,
                'errors' => []
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'errors' => $validator->errors()
            ]);
        }
    }

        public function forgotPassword(){
        return view('front.account.forgot-password');
    }

    public function processForgotPassword(Request $request) {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.forgotPassword')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);

        \DB::table('password_resets')->where('email',$request->email)->delete();

        \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Send Email here
        $user = User::where('email',$request->email)->first();
        $mailData =  [
            'token' => $token,
            'user' => $user,
            'subject' => 'You have requested to change your password.'
        ];

        Mail::to($request->email)->send(new ResetPasswordEmail($mailData));

        return redirect()->route('account.forgotPassword')->with('success','Reset password email has been sent to your inbox.');
        
    }

    public function resetPassword($tokenString) {
        $token = \DB::table('password_resets')->where('token',$tokenString)->first();

        if ($token == null) {
            return redirect()->route('account.forgotPassword')->with('error','Invalid token.');
        }

        return view('front.account.reset-password',[
            'tokenString' => $tokenString
        ]);
    }

    public function processResetPassword(Request $request) {

        $token = \DB::table('password_resets')->where('token',$request->token)->first();

        if ($token == null) {
            return redirect()->route('account.forgotPassword')->with('error','Invalid token.');
        }
        
        $validator = Validator::make($request->all(),[
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.resetPassword',$request->token)->withErrors($validator);
        }

        User::where('email',$token->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('account.login')->with('success','You have successfully changed your password.');

    }

    public function updatePassword(Request $request)
{
    // Step 1: Validate input
    $validator = Validator::make($request->all(), [
        'old_password'     => 'required',
        'new_password'     => 'required|min:5',
        'confirm_password' => 'required|same:new_password',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ]);
    }

    try {
        $userId = Auth::id();

        // Step 2: Get current password hash from DB
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'errors' => ['general' => 'User not found.'],
            ]);
        }

        // Step 3: Verify old password
        if (!Hash::check($request->old_password, $user->password)) {
            session()->flash('error', 'Your old password is incorrect.');
            return response()->json([
                'status' => false,
                'errors' => ['old_password' => ['Your old password is incorrect.']],
            ]);
        }

        // Step 4: Begin transaction
        DB::beginTransaction();

        // Step 5: Update password
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'password'   => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

        // Step 6: Commit transaction
        DB::commit();

        // Step 7: Success response
        session()->flash('success', 'Password updated successfully.');
        return response()->json([
            'status' => true,
            'errors' => [],
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Password update failed: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'errors' => ['general' => 'An unexpected error occurred. Please try again.'],
        ]);
    }
}
}
