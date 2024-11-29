<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if($user) {
            $token = $user->currentAccessToken();

            if($token && $token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
                $token->delete();
                return response()->json(['message' => 'Token has expired. Please log in again.'], 401);
            }
        }

        return $next($request);
    }
}
