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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'code' => ['string', function ($attribute, $value, $fail) {
                        if(!$value) $fail('Please enter a code.');
                        $codesuccess = PrizeCode::where('code', $value)->first();
                        if(!$codesuccess) $fail('Invalid code entered.');
                        // Check it's not expired 
                        if(!$codesuccess->active) throw new \Exception("This code is not active"); 
                        // or user already redeemed it 
                        if($codesuccess->redeemers()->where('user_id', $user->id)) throw new \Exception('You have already redeemed this code.');
                        //or if it's limited, make sure the claim wouldn't be exceeded
                        if ($codesuccess->use_limit > 0) {
                        if($codesuccess->use_limit >= $codesuccess->redeemers()->count()) throw new \Exception("This code has reached the maximum number of users");
                        }
                }
            ]
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User\User
     */
    protected function postRedeemPrize(array $data)
    { 
        $service = new PrizeCodeManager;
        $user = $service->reedeemPrize(Arr::only($data, ['code']));  
    }
    
}