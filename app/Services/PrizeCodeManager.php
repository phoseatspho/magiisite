<?php namespace App\Services;

use App\Services\Service;

use DB;
use Notifications;
use Config;
use Auth;
use Carbon\Carbon;

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
            $user = Auth::user();
                        // check if the input matches any existing codes
                        $codesuccess = PrizeCode::where('code', 'LIKE BINARY', $data['code'])->first(); 
                        
                        if(!isset($codesuccess)) throw new \Exception('Invalid code entered.');
                        // Check it's not expired 
                        if(!$codesuccess->active()) throw new \Exception("This code is not active"); 
                        // or user already redeemed it 
                        if($codesuccess->redeemers()->where('user_id', $user->id)->exists()) throw new \Exception('You have already redeemed this code.');
                        //or if it's limited, make sure the claim wouldn't be exceeded
                        if ($codesuccess->use_limit > 0) {
                        if($codesuccess->use_limit <= $codesuccess->redeemers()->count()) throw new \Exception("This code has reached the maximum number of users.");
                        }

            // if successful we can credit rewards
            $logType = 'Redeem Reward'; 
            $redeemData = [
                'data' => 'Received rewards from '. $codesuccess->name .' code'
            ];
            //make log
            $logging = $user;
            $logging = UserPrizeLog::create([
                'user_id' => $user->id,
                'prize_id' => $codesuccess->id, 
                'claimed_at' => Carbon::now()
            ]); 
            //credit reward
            if(!$rewards = fillUserAssets($codesuccess->rewardItems, null, $user, $logType, $redeemData)) throw new \Exception("Failed to distribute rewards to user.");
            flash($this->getRewardsString($rewards));

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  array                  $rewards
     * @return string
     */
    private function getRewardsString($rewards)
    {
        $results = "You have received: ";
        $result_elements = [];
        foreach($rewards as $assetType)
        {
            if(isset($assetType))
            {
                foreach($assetType as $asset)
                {
                    array_push($result_elements, $asset['asset']->displayName.(class_basename($asset['asset']) == 'Raffle' ? ' (Raffle Ticket)' : '')." x".$asset['quantity']);
                }
            }
        }
        return $results.implode(', ', $result_elements);
    }
}