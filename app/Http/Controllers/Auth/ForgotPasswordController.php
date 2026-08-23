<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Services\SystemMailSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:5,1')->only('sendResetLinkEmail');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $genericStatus = 'Jika email tersebut terdaftar, link reset password akan dikirim ke email tersebut.';
        $email = strtolower(trim((string) $request->input('email')));
        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (!$user) {
            return back()->with('status', $genericStatus);
        }

        if (!SystemMailSettings::isReady()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Pengaturan email reset password belum lengkap. Silakan hubungi admin.']);
        }

        try {
            $response = Password::broker()->sendResetLink(['email' => $email]);
        } catch (\Exception $exception) {
            Log::error('Password reset email failed', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email reset password belum dapat dikirim. Silakan hubungi admin.']);
        }

        return $response == Password::RESET_LINK_SENT
            ? back()->with('status', $genericStatus)
            : back()->withInput($request->only('email'))->withErrors(['email' => 'Email reset password belum dapat dikirim. Silakan hubungi admin.']);
    }
}
