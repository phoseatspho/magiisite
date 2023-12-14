<?php

namespace App\Console\Commands;

use App\Models\RankReward;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User\User;
use App\Facades\Notifications;

class RewardRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reward-ranks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute rank rewards';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //code original by newt in shop features
        //tysm!!! :>
        $rankrewards = RankReward::where('is_active', 1)->get();
        foreach ($rankrewards as $reward) {
            if ($reward->reward_time == 1) {
                $this->grantRewards($reward);
            } elseif ($reward->reward_time == 2) {
                // check if it's start of week
                $now = Carbon::now();
                $day = $now->dayOfWeek;
                if ($day == 1) {
                    $this->grantRewards($reward);
                }
            } elseif ($reward->reward_time == 3) {
                // check if it's start of month
                $now = Carbon::now();
                $day = $now->day;
                if ($day == 1) {
                    $this->grantRewards($reward);
                }
            }
        }
    }

    public function grantRewards($reward)
    {
        $users = User::where('rank_id', $reward->rank_id)->get();
        foreach ($users as $user) {

            
            // Distribute user rewards
            $rewards = fillUserAssets($reward->rewardItems, null, $user, 'Rank Reward', [
                'data' => 'Reward for being part of the ' . $reward->rank->displayName . ' rank'
            ]);

            //notify the user of the grant
            Notifications::create('RANK_REWARD', $user, [
                'user_name' => $user->name,
                'rank_name' => $user->rank->name,
            ]);
        }

    }
}
