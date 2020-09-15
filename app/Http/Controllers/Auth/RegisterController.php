<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CodeVerificationRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\SendWelcomeUserEmail;
use App\Profile;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Config;

class RegisterController extends Controller
{
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  RegisterRequest  $request
     * @return json
     */
    protected function register(RegisterRequest $request)
    {
        Session::forget('user');
        Session::forget('phone_number');

        $username = Str::slug($request->firstname . "-" . $request->lastname);
        $userRows = User::whereRaw("username REGEXP '^{$username}(-[0-9]*)?$'")->get();
        $countUser = count($userRows) + 1;
        $username = ($countUser > 1) ? "{$username}-{$countUser}" : $username;

        $user = new User([
            'name' => $request->firstname . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user = $this->createUsername($user, 0);

        Session::put('user', $user);

        
        return response()->json([
            'success' => true,
            'message' => 'Register success.',
            'user' => $user,
        ], 201);
    }

    protected function sendVerificationCode(CodeVerificationRequest $request)
    {
        $sid = Config::get('twilio.account_id');
        $token = Config::get('twilio.auth_token');

        $client = new Client($sid, $token);

        $userExists = User::where([
            ['phone_number', '=', $request->phone_number],
            ['banned', '=', 0],
        ])->exists();

        $digits = 5;
        $verificationCode = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        if ($userExists) {
            return response()->json([
                'success' => false,
                'message' => 'Profile already exists.',
            ], 400);
        } else if (auth()->user()) {
            $user = auth()->user();
            $user->verification_code = $verificationCode;
            $user->save();

            Session::put('phone_number', $request->phone_number);
        } else {
            $user = Session::get('user');
            $user->verification_code = $verificationCode;

            Session::put('user', $user);

            Session::put('phone_number', $request->phone_number);
        }

        $client->messages->create(
            $request->phone_number,
            array(
                'from' => Config::get('twilio.phone_number'),
                'body' => __('components/settings.mail_phone.phone_title') . $verificationCode,
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent.',
            'data' => $user,
        ], 201);
    }

    protected function verifyCode(Request $request)
    {
        if (auth()->user()) {
            $user = auth()->user();
        } else {
            $user = Session::get('user');
        }

        if ((string) $user->verification_code === (string) $request->verification_code) {
            $user->verified = true;
            $user->verification_code = null;
            $user->phone_number = Session::get('phone_number');
            $user->save();

            if (!auth()->user()) {

                Auth::login($user);

                Mail::to($user->email)->queue(new SendWelcomeUserEmail($user));

                Session::forget('user');
            }
            Session::forget('phone_number');

            return response()->json([
                'success' => true,
                'message' => 'Code verified.',
                'user' => $user
            ], 201);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Wrong code.',
            ], 404);
        }
    }

    protected function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'This activation token is invalid.',
            ], 404);
        }

        $user->active = true;
        $user->activation_token = '';
        $user->save();

        return $user;
    }

    protected function createUsername(User $user, $id = 0)
    {
        $username = str_slug($user->name);
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allUsernames = $this->getRelatedUsernames($username, $id);
        // If we haven't used it before then we are all good.
        if (!$allUsernames->contains('username', $username)) {
            $user->username = $username;
            // $user->save();

            return $user;
            // return $username;
        }
        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 1000; $i++) {
            $newUsername = $username . '-' . $i;
            if (!$allUsernames->contains('username', $newUsername)) {
                // return $newUsername;
                $user->username = $newUsername;
                // $user->save();

                return $user;
            }
        }
        throw new \Exception('Can not create a unique username');
    }


    protected function getRelatedUsernames($username, $id = 0)
    {
        return User::select('username')->where('username', 'like', $username . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
