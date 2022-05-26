<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use Illuminate\Support\Arr;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Discord\DiscordReward;

use App\Models\User\UserAlias;
use App\Models\User\UserDiscordLevel;

class DiscordManager extends Service
{
    /**
     * Show the user their EXP and level info.
     */
    public function showUserInfo($user, $message)
    {
        // we're only returning formatting here since I * refuse * to pass around the $discord variable (not worth the trouble)
        return [];
    }

    /**
     * Check and distribute rewards.
     */
    public function checkRewards($id, $message)
    {
        try {

            if(UserAlias::where('extra_data', $id)->exists()) {
                $user = UserAlias::where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = UserDiscordLevel::where('user_id', $user->id)->first();

            $rewards = DiscordReward::where('level', $level->level)->get();

            if($rewards) {

                $assets = createAssetsArray();

                foreach($rewards as $reward) {
                    $raws = json_decode($reward->loot, true);
                    // 
                    foreach($raws as $raw) {
                        dd($raw);
                        $model = getAssetModelString($typeId);

                        if($model)
                        {
                            $assets[$typeId][] = [
                                'asset' => $model::find($result),
                                'quantity' => $raw[$typeId][$result]['quantity'],
                            ];
                        }
                    }
                }


            }
                
            // Logging data
            $logType = 'Discord Level Up';
            $data = [
                'data' => 'Received rewards for levelling up to level '.$level->level.'.'
            ];

            // Distribute user rewards
            if(!$assets = fillUserAssets($assets, null, $user, $logType, $data)) throw new \Exception("Failed to distribute rewards to user.");

                return count($rewards);
            }
            else return true;
                        
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Add EXP to a user.
     * 
     * @param int id
     * @param Carbon $timestamp
     */
    public function giveExp($id, $timestamp)
    {
        try {

            if(UserAlias::where('extra_data', $id)->exists()) {
                $user = UserAlias::where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = UserDiscordLevel::where('user_id', $user->id)->first();

            if(!$level) {
                $level = UserDiscordLevel::create([
                    'user_id'         => $user->id,
                    'level'           => 0,
                    'exp'             => 0,
                    'last_message_at' => $timestamp,
                ]);
                // since they've never had a message before, we can just add exp straight away
                $level->exp += mt_rand($this->exp / 2, $this->exp) * $this->multiplier;
                $level->save();
                // formula: 5 * (lvl ^ 2) + (50 * lvl) + 100 - xp
                // lvl is current level
                // xp is how much XP already have towards the next level.
            }
            else {
                // check if it's been a minute since the last message
                if(!$level->last_message_at || 1 <= $timestamp->diffInMinutes($level->last_message_at)) {
                    $level->exp += mt_rand($this->exp / 2, $this->exp) * $this->multiplier;
                    $level->last_message_at = $timestamp;
                    $level->save();
                }
            }

            $requiredExp = 5 * (pow($level->level, 2)) + (50 * $level->level) + 100 - $level->exp;
            if($requiredExp <= 0) {
                $level->level++;
                $level->save();

                return [
                    'action' => 'Level',
                    'level'  => $level->level,
                    'user'   => $user,
                ];
            }
            // if nothing happened just continue as normal
            return true;
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}