<?php
namespace App\Http\Controllers;

use App\Http\Requests\ContactUsRequest;
use Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Config;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{

    private $user;

    public function __construct()
    {
        // $this->middleware('auth');
        $this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Session::forget('user');
        Session::forget('phone_number');

        // dd(auth()->user());
        if (Auth::user()) {
            return view('pages.events');
        } else {
            return view('home');
        }
    }

    public function changeLocale($locale)
    {
        Session::put('applocale', $locale);

        return redirect()->back();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function contactUs()
    {
        return view('pages.contact_us');
    }

    public function sendContactEmail(ContactUsRequest $request){



    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacyPolicy()
    {
        return view('pages.privacy_policy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function termsConditions()
    {
        return view('pages.terms_conditions');
    }
}
