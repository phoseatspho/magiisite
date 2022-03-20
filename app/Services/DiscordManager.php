<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use Illuminate\Support\Arr;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Discord\DiscordReward;

class DiscordManager extends Service
{
    /**
     * Show the user their EXP and level info.
     */
    public function showUserInfo($user, $message)
    {
        // we're only returning formatting here since I * refuse * to pass around the $discord variable (not worth the trouble)
        
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

            if(\App\Models\User\UserAlias::where('extra_data', $id)->exists()) {
                $user = \App\Models\User\UserAlias::where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = \App\Models\User\UserDiscordLevel::where('user_id', $user->id)->first();

            if(!$level) {
                $level = \App\Models\User\UserDiscordLevel::create([
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