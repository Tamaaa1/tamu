<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    // Handle request masuk - cek autentikasi dan otorisasi
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user bukan admin dan mencoba mengakses manajemen user, tolak akses
        if ($user->role !== 'admin' && $this->isUserManagementRoute($request)) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['access' => 'Akses manajemen user hanya untuk administrator.']);
        }

        return $next($request);
    }

    // Cek apakah request untuk route manajemen user
    private function isUserManagementRoute(Request $request): bool
    {
        $route = $request->route();
        if (!$route) {
            return false;
        }

        $routeName = $route->getName();
        return strpos($routeName, 'admin.users.') === 0;
    }
}
