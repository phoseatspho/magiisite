<?php namespace App\Services;

use DB;
use Illuminate\Support\Arr;
use App\Services\Service;

use App\Models\PrizeCode;

class PrizeCodeService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Prize Service
    |--------------------------------------------------------------------------
    |
    | Handles creation and usage of site registration prize codes.
    |
    */

    
    /**
     * Creates a new prize.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\PrizeCode
     */
    public function createPrize($data, $user)
    {
        DB::beginTransaction();

        try { 
            if(!isset($data['rewardable_type'])) throw new \Exception('Please add at least one reward to the prize.');

            $data = $this->populateData($data);

            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Reward type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['reward_quantity'][$key] || $data['reward_quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
            }

            if(!isset($data['limit_check'])) $data['use_limit'] = null;

            $prize = PrizeCode::create(Arr::only($data, ['start_at', 'end_at', 'is_active', 'use_limit', 'limit_check',]));
            $prize->code = randomString(15);
            $prize->user_id = $user->id; 

            $prize->output = $this->populateRewards($data);
            $prize->save();

            return $this->commitReturn($prize);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates an prize.
     *
     * @param  \App\Models\PrizeCode  $prize
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\rizeCode
     */
    public function updatePrize($prize, $data, $user)
    {
        DB::beginTransaction();

        try { 

            // More specific validation
            if(PrizeCode::where('name', $data['name'])->where('id', '!=', $prize->id)->exists()) throw new \Exception("The name has already been taken.");  
            if(!isset($data['rewardable_type'])) throw new \Exception('Please add at least one reward to the prize.');

            $data = $this->populateData($data); 

            if(!isset($data['limit_check'])) $data['use_limit'] = null;

            $prize = Prize::update(Arr::only($data, ['start_at', 'end_at', 'is_active', 'use_limit', 'limit_check',]));
            $prize->output = $this->populateRewards($data);
            $prize->save(); 

            return $this->commitReturn($prize);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Creates the assets json from rewards
     *
     * @param  \App\Models\PrizeCode   $prize
     * @param  array                       $data 
     */
    private function populateRewards($data)
    {
        if(isset($data['rewardable_type'])) {
            // The data will be stored as an asset table, json_encode()d. 
            // First build the asset table, then prepare it for storage.
            $assets = createAssetsArray();
            foreach($data['rewardable_type'] as $key => $r) {
                switch ($r)
                {
                    case 'Item':
                        $type = 'App\Models\Item\Item';
                        break;
                    case 'Currency':
                        $type = 'App\Models\Currency\Currency';
                        break;
                    case 'LootTable':
                        $type = 'App\Models\Loot\LootTable';
                        break;
                    case 'Raffle':
                        $type = 'App\Models\Raffle\Raffle';
                        break;
                }
                $asset = $type::find($data['rewardable_id'][$key]);
                addAsset($assets, $asset, $data['reward_quantity'][$key]);
            }
            
            return getDataReadyAssets($assets);
        }
        return null;
    }

    /**
     * Processes user input for creating/updating an prize.
     *
     * @param  array                  $data 
     * @param  \App\Models\PrizeCode  $prize
     * @return array
     */
    private function populateData($data, $prize = null)
    {
        if(!isset($data['is_active'])) $data['is_active'] = 0;

        return $data;
    }

}