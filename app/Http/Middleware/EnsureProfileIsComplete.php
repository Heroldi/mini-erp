<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $request->routeIs('profile.complete') ||
            $request->routeIs('profile.complete.store') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        if (! $user->role || $user->role->nome !== 'cliente') {
            return $next($request);
        }

        $profileIsComplete =
            filled($user->name) &&
            filled($user->cpf) &&
            filled($user->telefone) &&
            filled($user->data_nascimento);

        if (! $profileIsComplete) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}