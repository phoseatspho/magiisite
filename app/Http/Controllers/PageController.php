<?php

namespace App\Http\Controllers;

use App\Models\SitePage;
use Illuminate\Support\Facades\DB;
use App\Models\News;

class PageController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Page Controller
    |--------------------------------------------------------------------------
    |
    | Displays site pages, editable from the admin panel.
    |
    */

    /**
     * Shows the page with the given key.
     *
     * @param string $key
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPage($key) {
        $page = SitePage::where('key', $key)->where('is_visible', 1)->first();
        if (!$page) {
            abort(404);
        }

        return view('pages.page', ['page' => $page,
        'newses'   => News::visible()->orderBy('updated_at', 'DESC')->take(2)->get(),]);
    }

    /**
     * Shows the credits page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreditsPage() {
        return view('pages.credits', [
            'credits'    => SitePage::where('key', 'credits')->first(),
            'extensions' => DB::table('site_extensions')->get(),
        ]);
    }
}
