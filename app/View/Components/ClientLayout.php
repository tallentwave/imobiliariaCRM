<?php

namespace App\View\Components;

use App\Models\Setting;
use Illuminate\View\Component;
use Illuminate\View\View;

class ClientLayout extends Component
{
    public $settings;

    public $title;

    public function __construct(?string $title = null)
    {
        $this->title = $title;
        $this->settings = Setting::current();
    }

    public function render(): View
    {
        return view('layouts.client');
    }
}
