<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Controller;
use Request;
use Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class Language
{
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::get('applocale') !== null) {
            App::setLocale(Session::get('applocale'));
        } else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
