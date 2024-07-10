<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailingController extends Controller
{
    //
    public function sendMail($recipientEmail, $details)
    {

        Mail::to($recipientEmail)->send(new VerificationMail($details));

        return 'Email sent successfully!';
    }
}
