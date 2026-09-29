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
        if ($user) {
            if ($user->status === 'suspend') {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Akun Anda sedang ditangguhkan. Silakan hubungi Toolman.']);
            }
            if ($user->status === 'menunggu_acc') {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Akun Anda masih menunggu persetujuan dari Toolman.']);
            }
            if ($user->status !== 'aktif') {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Status akun Anda tidak valid untuk pemulihan kata sandi.']);
            }
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
