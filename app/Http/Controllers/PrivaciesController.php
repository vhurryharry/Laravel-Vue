<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrivacyValue;
use App\RelationshipPrivacyValue;

class PrivaciesController extends Controller
{

    public function __construct()
    {
        $this->middleware('verified');
    }

    public function getPrivacies()
    {
        $privacyValues = PrivacyValue::all();

        return response()->json([
            'success' => true,
            'message' => 'Privacy values fetched.',
            'privacies' => $privacyValues,
        ], 200);
    }

    public function getRelationshipPrivacies()
    {
        $relationshipPrivacyValues = RelationshipPrivacyValue::all();

        return response()->json([
            'success' => true,
            'message' => 'Relationship privacy values fetched.',
            'privacies' => $relationshipPrivacyValues,
        ], 200);
    }

    public function updateSearchabilityPrivacy(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $privacyValue = RelationshipPrivacyValue::where('key', $request->privacy)->first();
        $profile->searchabilityPrivacy()->associate($privacyValue);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Privacy value updated.',
        ], 200);
    }

    public function updateMyFriendsPrivacy(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $privacyValue = RelationshipPrivacyValue::where('key', $request->privacy)->first();
        $profile->myFriendsPrivacy()->associate($privacyValue);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Privacy value updated.',
        ], 200);
    }

    public function updateMyEventsPrivacy(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $privacyValue = RelationshipPrivacyValue::where('key', $request->privacy)->first();
        $profile->myEventsPrivacy()->associate($privacyValue);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Privacy value updated.',
        ], 200);
    }

    public function updateMyCommunitiesPrivacy(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $privacyValue = RelationshipPrivacyValue::where('key', $request->privacy)->first();
        $profile->myCommunitiesPrivacy()->associate($privacyValue);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Privacy value updated.',
        ], 200);
    }
}
