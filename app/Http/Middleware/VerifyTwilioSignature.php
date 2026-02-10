<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class VerifyTwilioSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        $validator = new RequestValidator(config('whatsapp.auth_token'));

        $isValid = $validator->validate(
            $request->header('X-Twilio-Signature', ''),
            $request->fullUrl(),
            $request->post(),
        );

        if (! $isValid) {
            abort(403, 'Invalid Twilio signature.');
        }

        return $next($request);
    }
}
