<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class LoginController extends Controller
{

    use ThrottlesLogins;
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  LoginRequest  $request
     * @return json
     */
    public function login(LoginRequest $request)
    {

        // $credentials = $request(['email', 'password']);

        $user = User::where('email', '=', $request->email)->first();
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'banned' => 0], $request->remember_me)) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
            ], 201);
        } else if (Auth::attempt(['phone_number' => $request->email, 'password' => $request->password, 'banned' => 0], $request->remember_me)) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
            ], 201);
        } else if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if ($user->banned) {
                return response()->json([
                    'success' => false,
                    'message' => 'User banned.',
                    'user' => $user
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login error.',
            ], 400);
        }

    }

    public function logout()
    {
        auth()->logout();

        return redirect()->to('/');
    }

    // /**
    //  * Logout user (Revoke the token)
    //  *
    //  * @return [string] message
    //  */
    // public function logout(Request $request)
    // {
    //     $request->user()->token()->revoke();

    //     return response()->json([
    //         'message' => 'Successfully logged out',
    //     ]);
    // }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        // return response()->json($request->user());

        return response()->json(['user' => auth()->user()], 200);
    }
}
