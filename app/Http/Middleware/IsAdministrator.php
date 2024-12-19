<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdministrator
{
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user() && Auth::user()->role=='ADMINISTRATOR'){
            return $next($request);
        }
        
        if ($request->is('api/*')) {
            return response()->json(['message' => 'Tidak memiliki hak akses'], 401);
        } else {
            return redirect('/');
        }
    }
}