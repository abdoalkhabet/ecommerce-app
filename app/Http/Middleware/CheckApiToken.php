<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!empty(trim($request->input('api_token')))) {

            $is_exists = User::where('id', Auth::guard('api')->id())->exists();
            if ($is_exists) {
                return $next($request);
            }
        }
        return response()->json('Invalid Token', 401);
    }
}