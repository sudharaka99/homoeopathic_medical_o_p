<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
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




    public function contact()
    {
        // Check if user is logged in
        if (Auth::check()) {
            $user = Auth::user();
            
            // If you want to use Query Builder instead of Eloquent
            // $user = DB::table('users')->where('id', Auth::id())->first();
            
            return view('front.contact-us', compact('user'));
        }
        
        return view('front.contact-us', ['user' => null]);
    }

    /**
     * Handle contact form submission
     */
    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:3|max:1000',
        ]);

        try {
            // Insert data that matches your table structure exactly
            DB::table('tbl_contacts_us')->insert([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'] ?? null,
                'subject'    => $validated['subject'],
                'message'    => $validated['message'],
                'status'     => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Return JSON for AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! We will respond within 24 hours.'
                ], 200);
            }

            // Fallback: Redirect with flash
            return redirect()->back()->with('success', 'Thank you! We will respond within 24 hours.');

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            Log::error('Contact form data: ', $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Sorry, something went wrong. Please try again.');
        }
    }
    /**
     * Send notification email to admin (Optional)
     */
    // private function sendNotificationEmail($contactData)
    // {
    //     try {
    //         $toEmail = 'admin@homeocare.com'; // Change to your admin email
    //         $subject = 'New Contact Form Submission: ' . $contactData['subject'];
            
    //         $message = "
    //         <html>
    //         <head>
    //             <title>New Contact Form Submission</title>
    //         </head>
    //         <body>
    //             <h2>New Contact Form Submission</h2>
    //             <p><strong>Name:</strong> {$contactData['name']}</p>
    //             <p><strong>Email:</strong> {$contactData['email']}</p>
    //             <p><strong>Phone:</strong> {$contactData['phone'] ?? 'Not provided'}</p>
    //             <p><strong>Subject:</strong> {$contactData['subject']}</p>
    //             <p><strong>Message:</strong></p>
    //             <div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>
    //                 " . nl2br(e($contactData['message'])) . "
    //             </div>
    //             <br>
    //             <p><small>Submitted at: " . now()->format('Y-m-d H:i:s') . "</small></p>
    //         </body>
    //         </html>
    //         ";

    //         // For Laravel mail
    //         // Mail::send([], [], function ($message) use ($toEmail, $subject, $message) {
    //         //     $message->to($toEmail)
    //         //             ->subject($subject)
    //         //             ->setBody($message, 'text/html');
    //         // });

    //         // For basic PHP mail (if Laravel mail is not configured)
    //         $headers = "MIME-Version: 1.0" . "\r\n";
    //         $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    //         $headers .= "From: no-reply@homeocare.com" . "\r\n";

    //         mail($toEmail, $subject, $message, $headers);

    //     } catch (\Exception $e) {
    //         Log::error('Contact email notification error: ' . $e->getMessage());
    //     }
    // }

    /**
     * Admin: Get all contact messages
     */
    public function adminIndex()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $contacts = DB::table('tbl_contacts_us')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => DB::table('tbl_contacts_us')->count(),
            'new' => DB::table('tbl_contacts_us')->where('status', 'new')->count(),
            'in_progress' => DB::table('tbl_contacts_us')->where('status', 'in_progress')->count(),
            'resolved' => DB::table('tbl_contacts_us')->where('status', 'resolved')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'stats'));
    }

    /**
     * Admin: Show specific contact message
     */
    public function adminShow($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $contact = DB::table('tbl_contacts_us')->where('id', $id)->first();

        if (!$contact) {
            abort(404, 'Contact message not found.');
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Admin: Update contact status
     */
    public function adminUpdateStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:new,in_progress,resolved'
        ]);

        DB::table('tbl_contacts_us')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Admin: Send reply to contact
     */
    public function adminSendReply(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'reply_message' => 'required|string|min:10'
        ]);

        try {
            $contact = DB::table('tbl_contacts_us')->where('id', $id)->first();

            if (!$contact) {
                return redirect()->back()->with('error', 'Contact message not found.');
            }

            // Update contact with reply
            DB::table('tbl_contacts_us')
                ->where('id', $id)
                ->update([
                    'reply_message' => $request->reply_message,
                    'status' => 'resolved',
                    'replied_at' => now(),
                    'replied_by' => Auth::id(),
                    'updated_at' => now()
                ]);

            // Send email reply to user
            $this->sendReplyEmail($contact, $request->reply_message);

            return redirect()->route('admin.contacts.show', $id)
                ->with('success', 'Reply sent successfully.');

        } catch (\Exception $e) {
            Log::error('Admin reply error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }

    /**
     * Send reply email to user
     */
    private function sendReplyEmail($contact, $replyMessage)
    {
        try {
            $toEmail = $contact->email;
            $subject = 'Re: ' . $contact->subject;
            
            $message = "
            <html>
            <head>
                <title>Response to Your Inquiry</title>
            </head>
            <body>
                <h2>Response from HomeoCare</h2>
                <p>Dear <strong>{$contact->name}</strong>,</p>
                
                <p>Thank you for contacting us. Here is our response to your inquiry:</p>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #0d6efd;'>
                    " . nl2br(e($replyMessage)) . "
                </div>
                
                <div style='margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #dee2e6; border-radius: 5px;'>
                    <h4>Your Original Message:</h4>
                    <p><strong>Subject:</strong> {$contact->subject}</p>
                    <p><strong>Message:</strong><br>" . nl2br(e($contact->message)) . "</p>
                </div>
                
                <p>If you have any further questions, please don't hesitate to contact us again.</p>
                
                <p>Best regards,<br>
                <strong>HomeoCare Team</strong></p>
                
                <hr>
                <small style='color: #6c757d;'>
                    This is an automated response. Please do not reply to this email.<br>
                    &copy; " . date('Y') . " HomeoCare. All rights reserved.
                </small>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: no-reply@homeocare.com" . "\r\n";
            $headers .= "Reply-To: info@homeocare.com" . "\r\n";

            mail($toEmail, $subject, $message, $headers);

        } catch (\Exception $e) {
            Log::error('Reply email error: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Delete contact message
     */
    public function adminDestroy($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        DB::table('tbl_contacts_us')->where('id', $id)->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact message deleted successfully.');
    }
}




