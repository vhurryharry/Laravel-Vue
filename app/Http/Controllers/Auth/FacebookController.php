<?php


namespace App\Http\Controllers\Auth;


use App\User;
use App\Http\Controllers\Controller;
use Socialite;
use Exception;
use Auth;
use Illuminate\Support\Str;


class FacebookController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        // try {
        $user = Socialite::driver('facebook')->user();

        $username = Str::slug($user->getName());
        $userRows = User::whereRaw("username REGEXP '^{$username}(-[0-9]*)?$'")->get();
        $countUser = count($userRows) + 1;

        $username = ($countUser > 1) ? "{$username}-{$countUser}" : $username;

        // dd($user);

        $create['name'] = $user->getName();
        $create['firstname'] = $user->getName();
        $create['lastname'] = $user->getName();
        $create['username'] = $username;
        $create['email'] = $user->getEmail();
        $create['facebook_id'] = $user->getId();

        $userModel = new User;
        $createdUser = $userModel->addNew($create);
        Auth::loginUsingId($createdUser->id);

        return redirect()->route('home');
        // } catch (Exception $e) {
        //     return redirect('auth/facebook');
        // }
    }
}
