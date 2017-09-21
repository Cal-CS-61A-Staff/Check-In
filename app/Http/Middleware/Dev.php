<?php namespace App\Http\Middleware;

use App, Closure;

class Dev {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
	    if (!App::environment('local'))
        {
            return response('Unauthorized.', 401);
        }
		return $next($request);
	}

}
