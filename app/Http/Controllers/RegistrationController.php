<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_wa' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:registrations,email'],
            'nama_lembaga' => ['required', 'string', 'max:255'],
            'captcha' => 'required'
        ]);

       // 2️⃣ Verifikasi ke Google reCAPTCHA
    $response = Http::asForm()->post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'secret'   => env('RECAPTCHA_SECRET'),
            'response' => $request->captcha,
            'remoteip' => $request->ip(),
        ]
    );

    if (! $response->json('success')) {
        return response()->json([
            'errors' => [
                'captcha' => ['Captcha tidak valid.']
            ]
        ], 422);
    }

    // 3️⃣ Simpan data jika captcha valid
    $registration = Registration::create([
        'nama' => $validated['nama'],
        'no_wa' => $validated['no_wa'],
        'email' => $validated['email'],
        'nama_lembaga' => $validated['nama_lembaga'],
    ]);

    return response()->json([
        'message' => 'Pendaftaran berhasil!',
        'data' => $registration
    ]);
}
}
