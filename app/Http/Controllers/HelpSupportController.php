<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HelpSupportController extends Controller
{
    public function sendEmail(Request $request)
    {
        // Validate the form fields
        $request->validate([
            'object' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        // Send the email using Laravel's built-in Mail facade
        Mail::raw($request->message, function ($message) use ($request) {
            $message->subject('Help and Support Request')
                ->to('hebaasker1772002@gmail.com')
                ->from($request->email, $request->object);
        });

        return response([
            'message' => 'Your message has been sent successfully'
        ]);
    }
}
