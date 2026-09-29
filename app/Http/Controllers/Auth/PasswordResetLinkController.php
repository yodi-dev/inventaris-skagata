<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        // Hanya kirimkan tautan reset kata sandi jika akun terdaftar dan berstatus aktif
        if ($user && $user->status === 'aktif') {
            Password::sendResetLink(
                $request->only('email')
            );
        }

        // Respon publik dibuat 100% konsisten untuk semua kondisi (aktif, suspend, pending, nonexistent)
        // demi mencegah risiko kebocoran data akun (Account Enumeration & Status Disclosure).
        return back()->with('status', __(Password::RESET_LINK_SENT));
    }
}
