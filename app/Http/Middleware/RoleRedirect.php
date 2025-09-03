<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user) {
            // Eğer doktor değilse ve doctor-panel'e erişmeye çalışıyorsa dashboard'a yönlendir
            if (!$user->isDoctor() && $request->routeIs('doctor-panel')) {
                return redirect()->route('dashboard');
            }
            
            // Eğer doktorsa ve dashboard'a erişmeye çalışıyorsa doctor-panel'e yönlendir
            if ($user->isDoctor() && $request->routeIs('dashboard')) {
                return redirect()->route('doctor-panel');
            }
        }
        
        return $next($request);
    }
}
