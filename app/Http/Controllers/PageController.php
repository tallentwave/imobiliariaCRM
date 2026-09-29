<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class PageController extends Controller
{
    public function about()
    {
        $settings = Setting::current();

        return view('site.about', compact('settings'));
    }

    public function contact()
    {
        $settings = Setting::current();

        return view('site.contact', compact('settings'));
    }
}
