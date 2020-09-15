<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Auth\AuthenticationException;

class EnsureAccountIsVerified
{

        /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }


    // /**
    //  * Handle an incoming request.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Closure  $next
    //  * @param  string|null  $redirectToRoute
    //  * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    //  */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $user = $this->auth->user();
        if ($user->banned) {
            auth()->logout();

            return redirect('/')->with([
                'user' => $user
            ]);
        }

        if (!$user->verified) {
            return redirect('/user/' . $user->username . '/settings')->with([
                'verified' => 'Account not verified'
            ]);
        }
        return $next($request);

        // return redirect('/')->with([
        //     'verified' => false
        // ]);

        // if (
        //     !$request->user() || ($request->user() instanceof MustVerifyEmail &&
        //         !$request->user()->hasVerifiedEmail())
        // ) {

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Account not verified.',
        //     ], 201);

        //     // return $request->expectsJson()
        //     //     ? abort(403, 'Your email address is not verified.')
        //     //     : Redirect::route($redirectToRoute ?: 'verification.notice');
        // }

        // return $next($request);
    }
}
