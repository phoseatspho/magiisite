<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Settings;
use Carbon\Carbon;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

use App\Services\DiscordManager;

class DiscordBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the discord bot.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->token = env('DISCORD_BOT_TOKEN');
        $this->prefix = env('DISCORD_PREFIX') ? env('DISCORD_PREFIX') : '';
        $this->channel_id = env('DISCORD_ERROR_CHANNEL') ? env('DISCORD_ERROR_CHANNEL') : null;
        // set constant for max exp you can gain.
        // multiplier can increase this
        $this->exp = 20;
        $this->multiplier = Settings::get('discord_exp_multiplier') ? Settings::get('discord_exp_multiplier') : 1;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Hi, if you're reading this you're likely either trying to understand the following mess,
        // or you're trying to add features etc.
        // if you think your feature idea is a good one, please let me know!
        // Otherwise, good luck! You may or may not need it
        // I've commented somewhat extensively, but there is an expectation you know what you're doing.

        // to start the bot run the following:
        // nohup php artisan discord-bot --run=true --daemon > app/storage/logs/laravel.log &

        // this is pre-emptive 'shutdown' stuff so it doesnt break
        if (php_sapi_name() !== 'cli' || $this->argument('command') !== $this->signature) {
            exit;
        }
        if(!$this->token) {
            echo 'Please set the DISCORD_BOT_TOKEN environment variable.', PHP_EOL;
            exit;
        }
        if(!$this->channel_id) {
            echo 'Please set the DISCORD_ERROR_CHANNEL environment variable.', PHP_EOL;
            exit;
        }
        $discord = new Discord([
            'token' => $this->token,
        ]);

        $service = new DiscordManager();
        
        $discord->on('ready', function (Discord $discord) {
            // startup message //////////////////
            echo "Bot is ready!", PHP_EOL;
            // send message to specified channel
            $guild = $discord->guilds->first();
            $channel = $guild->channels->get('id', $this->channel_id);

            $channel->sendMessage('Bot is ready! Use '.$this->prefix.'ping to check delay.');
            ////////////////////////////////////
        
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
                
                // don't reply to ourselves
                if ($message->author->bot) {
                    return;
                }

                if ($message->content == $this->prefix.'ping') {
                    // compare timestamps by milliseconds
                    $now = Carbon::now();
                    $message->reply('Pong! Delay: '.$now->diffInMilliseconds($message->timestamp).'ms');
                    return;
                }

                // finally check if we can give exp to this user
                try {
                    $action = $service->giveExp($message->author->id, $message->timestamp);
                    // if action is string, throw error
                    if(is_string($action)) {
                        throw new \Exception($action);
                    }
                    if(isset($action['action']) && $action['action'] == 'Level') {
                        if(Settings::get('discord_level_notif')) $message->reply('You leveled up! You are now level '.$action['level'].'!');
                        // dm user otherwise
                        else $message->author->sendMessage('You leveled up! You are now level '.$action['level'].'!');
                        // check for rewards
                    }
                
                } catch (\Exception $e) {
                    // this sends the error to the specified channel
                    $guild = $discord->guilds->first();
                    $channel = $guild->channels->get('id', $this->channel_id);
        
                    $channel->sendMessage('Error: '.$e->getMessage());
                }
            });
        });
        // init loop
        $discord->run();
    }
}
