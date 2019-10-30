<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{

    /**
     * Show all of the users for the application.
     *
     * @return Response
     */
    public function getSettings()
    {
        $settings = DB::table('settings')->get();
        return view('settings', ['settings' => $settings]);

    }

}
