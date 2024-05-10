<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function sendMessage($number)
    {
        return redirect()->away("https://wa.me/{$number}");
    }
}
