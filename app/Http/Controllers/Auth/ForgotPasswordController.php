<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent.',
        ], 201);

        // return $response == Password::RESET_LINK_SENT
        //             ? $this->sendResetLinkResponse($request, $response)
        //             : $this->sendResetLinkFailedResponse($request, $response);
    }
    public function showLinkRequestForm(Request $request, $token = null)
    {
        $email = null;
        $user = null;

        if ($request->token != null) {
            $reset = DB::table("password_resets")->where('email', $request->email)->first();

            if (Hash::check($request->token, $reset->token)) {
                $user = User::where('email', '=', $request->email)->first();
                return view('home', ['token' => $request->token, 'email' => $email, 'user' => $user]);
            } else {
                $error = true;
            }

            // if (!$this->hasher->check($request->token, $reset->token)) //throw error, token invalid

        }
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }
}
