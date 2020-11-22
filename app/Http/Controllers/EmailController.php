<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $validate = $this->validate($request, [
            'email' => 'required|min:5',
            'name' => 'required|min:3',
            'message' => 'required|min:10'
        ]);

        $dataEmail = [
            'name' => $validate['name'],
            'email' => $validate['email'],
            'message_user' => $validate['message']
        ];

        Mail::send('email.index', $dataEmail, function ($message) {
            $message->to("wirabuanaiot@gmail.com", "untukdunia")->subject("Pesan dari pengguna");
        });

        Mail::send('email.user', $dataEmail, function ($mail) use ($validate) {
            $mail->to($validate['email'], $validate['name'])->subject("Untukdunia.com");
        });
    }
}
