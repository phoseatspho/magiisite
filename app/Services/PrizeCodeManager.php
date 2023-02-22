<?php namespace App\Services;

use App\Services\Service;

use DB;
use Notifications;
use Config;

use App\Models\User\User;
use App\Models\User\UserPrizeLog;

use App\Models\PrizeCode;
use App\Models\PrizeCodeReward;

class PrizeCodeManager extends Service
{

/**********************************************************************************************
    Code Redeem
 **********************************************************************************************/

    /**
     * Attempts to redeem the code
    *
    * @param  array                        $data 
    * @param  \App\Models\User\User        $user
    * @return bool
    */
    public function reedeemPrize($query, $user)
    {
        DB::beginTransaction();

        try {
            //search for matching code
            $prizes = PrizeCode::where('code', '==', '%'.$query.'%')->get(); 
            // Check it's not expired 
            if(!$prizes->active) throw new \Exception("This code is not active"); 
            // or user already redeemed it 
            if($prizes->redeemers()->where('user_id', $user->id)) throw new \Exception('You have already redeemed this code.');
            //or if it's limited, make sure the claim wouldn't be exceeded
            if ($prizes->use_limit >= 0) {
            if($prizes->use_limit >= $prizes->redeemers()->count()) throw new \Exception("This code has reached the maximum number of users");
            }

            //check if it's a valid code
            if(!$prizes) throw new \Exception("This is not a valid code"); 


            // if successful we can credit rewards
            $logType = 'Redeem Reward';
            $reedeemData = [
                'data' => 'Received rewards from '. $prizes->displayName .' prize'
            ];

            //make log
            $user = UserPrizeLog::create([
                'user_id' => $user->id,
                'prize_id' => $prizes->id, 
                'claimed_at' => Carbon::now()
            ]);

            if(!fillUserAssets($prizes->rewardItems, null, $user, $logType, $reedeemData)) throw new \Exception("Failed to distribute rewards to user.");

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

   
}