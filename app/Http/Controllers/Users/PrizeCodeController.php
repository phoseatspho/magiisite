<?php

namespace App\Http\Controllers\Users;

use DB;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\PrizeCode;
use App\Models\User\UserPrizeLog;
use App\Services\PrizeCodeService;
use App\Services\PrizeCodeManager;
class PrizeCodeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Prize Code Controller
    |--------------------------------------------------------------------------
    |
    |  
    |
    */

    /**
     * Gets redeem page
     */
    public function getIndex(Request $request)
    { 
        return view('home._prize_redeem');
    }

    /**
     * redeems code
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postRedeemPrize(Request $request, PrizeCodeManager $service, $query)
    {
        $query = $query ?: $request->get('query');
        if(!$query) return redirect()->to('redeem-code');

        if($service->craftPrize($request->only(['query']), Auth::user())) {
            flash('Code redeemed successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}