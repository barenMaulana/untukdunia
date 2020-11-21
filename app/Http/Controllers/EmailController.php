<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $dataEmail = ['baren'];
        Mail::send('email.index', $dataEmail, function ($mail) {
            $mail->to("barenmaulana@gmail.com", "Baren")->subject("Pesan dari pengguna");
        });
    }
}
