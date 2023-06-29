<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //function my profile
        $pageTitle = 'Profile';

        return view ('profile',['pageTitle'=> $pageTitle]);
    }
}
