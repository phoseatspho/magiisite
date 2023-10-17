<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Character\Character;
use Settings;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Creates a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        if (Settings::get('featured_character')) {
            $character = Character::find(Settings::get('featured_character'));
        } else {
            $character = null;
        }
        View::share('featured', $character);
    }
}
