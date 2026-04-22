<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user() instanceof MustVerifyEmail && ! $request->user()->hasVerifiedEmail()
                    ? view('auth.verify-email')
                    : redirect()->intended(route('dashboard', absolute: false));
    }
}
