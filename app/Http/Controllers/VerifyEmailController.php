<?php

namespace App\Http\Controllers;

use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Laravel\Fortify\Http\Requests\VerifyEmailRequest;
use App\Models\User;

use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    public function verify(VerifyEmailRequest $request)
    {
        Log::info("verify" . var_export($request, true));
        $user = User::where('id', $request->id)->first();

        if ($user()->hasVerifiedEmail()) {
            return app(VerifyEmailResponse::class);
        }

        if ($user()->markEmailAsVerified()) {
            event(new Verified($user()));
        }

        return app(VerifyEmailResponse::class);

    }
}
