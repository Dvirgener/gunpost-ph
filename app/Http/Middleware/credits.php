<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class credits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->post_credits <= 0) {
            return redirect()->route('posts')->with('message', 'You do not have enough post credits to create a new post. Please contact Administrators for assistance.');
        }

        return $next($request);
    }
}
