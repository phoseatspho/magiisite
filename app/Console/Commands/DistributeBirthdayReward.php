<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use DB;
use Carbon\Carbon;
use App\Models\User\User;
 use App\Models\Item\Item;
use App\Services\InventoryManager;

class DistributeBirthdayReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distribute-birthday-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute birthday rewards.';

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
        $this->info("\n".'*******************************');
        $this->info('* BIRTHDAY REWARDS RUNNING *');
        $this->info('*******************************');

        // Get users with a birthday for the given month
        //it grants on the first of the month every month, it prevents players from having their exact day given out if they don't want to 
        //may add a setting later where players can opt out of rewards if they don't want even the month to be public
        $birthdayUsers = User::whereRaw('MONTH(birthday) = MONTH(NOW())')->get();
        // For each user
        forEach($birthdayUsers as $user) {
            //We're avoiding making whole new pages, and a model and whatnot, but the cost comes at having to edit this file
            //make sure you edit everything correctly or things will fail
            //if you have any questions, be sure to ask me or make a ticket
            //this is what will be granted to EVERY user, there is no tweaking it, so if you want to put some randomness in here, just make a box with a loot table

            try {
                //this is what the "log type" will be in the logs
                //it's usually something like "staff grant", "shop purchase" 
                //you can change this if you want
                $logType = 'Birthday Reward';
                //this is what appears after the log type, it will also show up as the source if it's an item, so you can take it out if you really want, just set it to null, don't outright remove it or it will break
                //usually it is "recieved item from X", "purchased from X by for (X currency)"
                //you can change this as well, we're setting it to a birthday message as default because it's cute 
                $data = 'Happy Birthday, '. $user->displayName .'!';

                //here's where we will put the rewards
                //we're just going to use an item because it's simplest and can contain pretty much whatever you as the mod/admin want
                //you can still just grant multiple items though, copy paste the grant code
                //if you copy paste more of the same type make sure that there's no overlap in the $item variables
                //you'd have to set a new variable like $item2 and change $item to that variable get it to grant a new item or it will just grant the same thing

                //set 'Put your item name here' to the item you want to grant, set 1 to the quantity you want to grant
                //make sure to change these or at best nothing will happen or at worst something might break
                    $item = Item::where('name', 'Put your item name here')->first();
                  (new InventoryManager)->creditItem(null, $user, $logType, [
                   'data' => $data,
                   'notes' => null, ],
                  $item, 1);            
                
                
            } catch(\Exception $e) {
                $this->error('error:'. $e->getMessage());
            }
    }
    $this->info('Rewards have been distributed');
}
}