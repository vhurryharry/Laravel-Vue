<?php

namespace App\Http\Controllers;

use App\Community;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Event;
use App\Http\Requests\CreateReportRequest;
use App\Post;
use App\Profile;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }

    public function store(CreateReportRequest $request)
    {
        $profile = auth()->user()->profile()->first();

        if ($request->reportedResourceType === 'communities') {
            $resource = Community::where('id', $request->reportedResource['id'])->first();
        } else if ($request->reportedResourceType === 'posts') {
            $resource = Post::where('id', $request->reportedResource['id'])->first();
        } else if ($request->reportedResourceType === 'events') {
            $resource = Event::where('id', $request->reportedResource['id'])->first();
        } else if ($request->reportedResourceType === 'profiles') {
            $resource = Profile::where('id', $request->reportedResource['id'])->first();
        }
        
        // $event = Event::where('id', $request->resource_id)->first();

        $date = Carbon::parse($request->date)->timestamp;

        $report = $resource->report()->create([
            'sender_id' => $profile->id,
            'category' => $request->category,
            'description' => $request->description,
            'date' => $date,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Report created.',
            'report' => $report,
        ], 200);
    }
}
