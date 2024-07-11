<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailingController extends Controller
{
    //
    public function sendMail($recipientEmail, $details)
    {

        try {
            Mail::to($recipientEmail)->send(new VerificationMail($details));
            return [
                'issuccess' => true,
                'message' => 'Mail sent successfully.'
            ];
        }catch (Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            return [
                'issuccess' => false,
                'message' => 'Failed to send the mail: ' . $e->getMessage()
            ];
        }
    
    }
}
