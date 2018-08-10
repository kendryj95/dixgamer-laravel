<?php

namespace App\Http\Middleware;

use Closure;

class AnalystMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (\Helper::validateAdminAnalyst(session()->get('usuario')->Level)) {
        return $next($request);
      }

      return redirect('home')->withErrors(['Acceso denegado']);
    }
}
