<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use Illuminate\Support\Arr;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Discord\DiscordReward;

class DiscordService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Discord Reward Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of discord rewards.
    |
    */

    /**********************************************************************************************

        DISCORD REWARDS

    **********************************************************************************************/

    /**
     * Creates a new discord reward.
     *
     */
    public function createReward($data, $user)
    {
        DB::beginTransaction();

        try {

            $reward = DiscordReward::create(Arr::only($data, ['level']));

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $reward);

            return $this->commitReturn($reward);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a discord reward.
     *
     */
    public function updateReward($reward, $data, $user)
    {
        DB::beginTransaction();

        try {

            // More specific validation
            if(DiscordReward::where('level', $data['level'])->where('id', '!=', $reward->id)->exists()) throw new \Exception("The level already has rewards.");

            $reward->update(Arr::only($data, ['level']));

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $reward);

            return $this->commitReturn($reward);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating rewards.
     */
    private function populateRewards($data, $reward)
    {
        $assets = createAssetsArray(false);
        // Process the additional rewards
        if(isset($data['rewardable_type']) && $data['rewardable_type'])
        {
            foreach($data['rewardable_type'] as $key => $type)
            {
                $asset = null;
                switch($type)
                {
                    case 'Item':
                        $asset = Item::find($data['rewardable_id'][$key]);
                        break;
                    case 'Currency':
                        $asset = Currency::find($data['rewardable_id'][$key]);
                        if(!$asset->is_user_owned) throw new \Exception("Invalid currency selected.");
                        break;
                    case 'LootTable':
                        $asset = LootTable::find($data['rewardable_id'][$key]);
                        break;
                }
                if(!$asset) continue;
                addDiscordAsset($assets, $asset, $data['quantity'][$key]);
            }
        }        
        $reward->loot = getDiscordDataReadyAssets($assets);
        $reward->save();
    }

    /**
     * Deletes a discord reward.
     * 
     */
    public function deleteReward($reward)
    {
        DB::beginTransaction();

        try {

            $reward->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}