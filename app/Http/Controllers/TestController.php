<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function testMail(Request $request){



        $getUser = User::find(7);


        $url = url('/user/verify-email/' . $getUser->uuid);
        $mailData = [
            'email_subject' => 'Thank you for registering with Smart Office.',
            'url' => $url,
        ];

        Mail::to($getUser->email)->send(new WelcomeMail($mailData));

        return view('Mails.welcome');
    }
}
