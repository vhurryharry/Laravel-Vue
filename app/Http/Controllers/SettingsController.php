<?php

namespace App\Http\Controllers;

use App\Education;
use App\Http\Requests\UpdateUserEmailRequest;
use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Language;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Gender;
use App\TimezoneValue;
use App\EventParticipants;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('verified')->except(['index']);
        // $this->middleware('client');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($username)
    {
        // $user = User::where('email', '=', Auth::user()->email)->first();
        $user = Auth::user();

        // $eventParticipants = EventParticipants::all();
        // $profile = $user->profile->load('myFriendsPrivacy')->load('myEventsPrivacy')->load('myCommunitiesPrivacy')->load('location.country')->load('languages')->load('educations')->load('timezone.timezoneValue');
        $profile = $user->profile->load('searchabilityPrivacy')->load('myFriendsPrivacy')->load('myEventsPrivacy')->load('myCommunitiesPrivacy')->load('location.country')->load('languages')->load('educations')->load('timezone.timezoneValue');
        $profile->gender = $profile->gender;
        //

        return view('pages.settings', compact('user', 'profile'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function updateEmail(UpdateUserEmailRequest $request)
    {
        $user = auth()->user();

        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email update success.',
        ], 201);
    }

    public function updateBio(Request $request)
    {
        $profile = auth()->user()->profile;
        $profile->bio = $request->bio;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Description updated.',
            'profile' => $profile
        ], 201);
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = auth()->user();

        $current_password = $user->password;

        if (Hash::check($request->current_password, $current_password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' =>
                true,
                'message' => 'Password update success.',
            ], 201);
        }
    }

    public function updateName(UpdateUserNameRequest $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $profile = $user->profile;
        $profile->name = $request->name;
        $profile->save();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Name update success.',
        ], 201);
    }

    public function activateAccount()
    {
        $profile = auth()->user()->profile;
        $profile->active = 1;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Account deactivated.',
            'profile' => $profile
        ], 201);
    }

    public function deactivateAccount()
    {
        $profile = auth()->user()->profile;
        $profile->active = 0;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Account deactivated.',
            'profile' => $profile
        ], 201);
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted.',
        ], 201);
        // return view('home');
    }

    public function updateOrCreateLanguages(Request $request)
    {
        $profile = auth()->user()->profile;
        $profile->languages()->detach();

        foreach ($request->languages as $key => $language) {
            $language = Language::find($language['id']);
            $profile->languages()->save($language);
        }
        $profile->touch();

        return response()->json([
            'success' => true,
            'message' => 'Languages updated.',
            'profile' => $profile->load('languages'),
        ], 201);
    }

    public function updateOrCreateEducations(Request $request)
    {
        $profile = auth()->user()->profile;
        $profile->educations()->detach();

        foreach ($request->educations as $key => $education) {
            $education = Education::find($education['id']);

            $profile->educations()->save($education);
        }
        $profile->touch();

        return response()->json([
            'success' => true,
            'message' => 'Educations updated.',
            'profile' => $profile->load('educations'),
        ], 201);
    }

    public function updateGender(Request $request)
    {
        $profile = auth()->user()->profile;

        $gender = Gender::where('key', $request->gender)->first();

        if (!$request->gender) {
            $profile->gender()->detach();

            return response()->json([
                'success' => true,
                'message' => 'Gender updated.',
                'profile' => $profile,
            ], 201);
        }

        if ($profile->gender()->first()) {
            $profile->gender()->detach();
        }

        $profile->gender()->save($gender);


        // $profile->gender = $request->gender;
        // $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Gender updated.',
            'profile' => $profile,
        ], 201);
    }

    public function updateTimezone(Request $request)
    {
        $user = auth()->user();
        $profile = auth()->user()->profile()->first();

        if (!$request->timezone['key']) {
            $profile->timezone()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Timezone updated.',
                'profile' => $profile,
            ], 201);
        }

        $timezoneValue = TimezoneValue::where('key', $request->timezone['key'])->first();

        if ($profile->timezone()->first()) {
            $profile->timezone()->delete();
        }

        $timezone = $profile->timezone()->create([
            'key' => $request->timezone['key'],
            'name' => $request->timezone['key'],
        ]);

        $timezone->timezoneValue()->associate($timezoneValue);

        // $timezoneValue->timezone()->save($timezone);
        $timezone->save();

        // $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Timezone updated.',
            'profile' => $timezone,
        ], 201);
    }

    public function updateImage(Request $request)
    {
        $profile = auth()->user()->profile;

        $file = $request->file('files');
        $image = Image::make(file_get_contents($file));

        $mime = $image->mime();
        if ($mime == 'image/jpeg') {
            $extension = '.jpg';
        } else if ($mime == 'image/png') {
            $extension = '.png';
        } else if ($mime == 'image/gif') {
            $extension = '.gif';
        } else {
            $extension = '';
        }

        $image_url = "profile-" . time() . $extension;
        $path = storage_path('/app/public/images/profiles/' . $image_url);
        $image = $image->save($path);

        $profile->update([
            'image_path' => "profiles/" . $image->filename . $extension,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image updated.',
            'profile' => $profile,
        ], 201);
    }

    public function updateLocationBirthdayEducationPrivacy(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        if ($profile->location()->first()) {
            $location = $profile->location()->first();
            $location->hidden = $request->location;
            $location->save();
            // $profile->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => __('components/settings.toast.select_location_first'),
            ], 400);
        }

        if ($profile->educations) {
            foreach ($profile->educations as $education) {
                $education->pivot->hidden = $request->education;
                $education->pivot->save();
            }
            // $profile->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => __('components/settings.toast.select_educations_first'),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Privacies updated.',
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
