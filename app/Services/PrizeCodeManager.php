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
    public function reedeemPrize($data)
    {
        DB::beginTransaction();

        try {

            $reward = PrizeCode::where('code')->first();

            // if successful we can credit rewards
            $logType = 'Redeem Reward';
            $reedeemData = [
                'data' => 'Received rewards from '. $reward->displayName .' code'
            ];

            //make log
            $user = UserPrizeLog::create([
                'user_id' => $user->id,
                'prize_id' => $reward->id, 
                'claimed_at' => Carbon::now()
            ]);

            if(!fillUserAssets($reward->rewardItems, null, $user, $logType, $reedeemData)) throw new \Exception("Failed to distribute rewards to user.");

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

   
}