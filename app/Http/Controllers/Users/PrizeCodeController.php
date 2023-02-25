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
    public function getIndex()
    { 
        return view('home._prize_redeem');
    }

    
    /**
     * Creates or edits a prize.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PrizeCodeService  $service 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRedeemPrize(Request $request, PrizeCodeManager $service)
    {
        
        $data = $request->only([
            'code' 
        ]);
        if($service->reedeemPrize($data, Auth::user())) {
            flash('Code redeemed successfully')->success();
        } 
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
}