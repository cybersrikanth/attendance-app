<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles = null)
    {
        if (!$roles) {
            $roles = array_slice(User::ROLES, 1, 2);
        } else {
            $roles = explode("|", $roles);
        }
        if (!in_array($request->user()->role, $roles)) {
            throw new AuthorizationException();
        }
        return $next($request);
    }
}
