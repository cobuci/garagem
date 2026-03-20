<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            app()->setLocale(session()->get('locale'));
        } elseif (auth()->check()) {
            app()->setLocale(auth()->user()->locale);
        }

        return $next($request);
    }
}
