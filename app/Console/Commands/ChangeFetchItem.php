<?php

namespace App\Console\Commands;

use DB;
use Settings;
use Log;
use Illuminate\Console\Command;
use App\Models\Item\Item;

class ChangeFetchItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-fetch-item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes currently wanted fetch item.';

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
        $id = Item::all()->random()->id;
        $setting = Settings::get('fetch_item');
        while($id == $setting) {
            $id = Item::all()->random()->id;
        }

        DB::table('site_settings')->where('key', 'fetch_item')->update(['value' => $id]);
    }
}