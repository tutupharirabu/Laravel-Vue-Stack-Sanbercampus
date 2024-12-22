<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ForgotPassword;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class VerifyPasswordResetToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Email tidak ditemukan.',
            ], 404);
        }

        $forgotPassword = ForgotPassword::where('user_id', $user->user_id)
            ->where('token', $request->reset_token)
            ->first();

        if (!$forgotPassword) {
            return response()->json([
                'error' => 'Token reset password tidak valid.',
            ], 403);
        }

        if (Carbon::now() > $forgotPassword->valid_until) {
            return response()->json([
                'error' => 'Token reset password sudah kadaluarsa.',
            ], 403);
        }

        return $next($request);
    }
}
