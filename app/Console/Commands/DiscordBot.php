<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use App\Services\DiscordManager;
use Carbon\Carbon;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Console\Command;

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
     */
    public function __construct()
    {
        parent::__construct();
        $this->token = env('DISCORD_BOT_TOKEN');
        $this->prefix = env('DISCORD_PREFIX') ?? '!';
        $this->error_channel_id = env('DISCORD_ERROR_CHANNEL') ?? null;
        // webhook related settings - if we should delete webhook messages and post them ourselves etc.
        $this->announcement_channel_id = env('DISCORD_ANNOUNCEMENT_CHANNEL') ?? null;
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
        // npm pm2 start 'php artisan discord-bot'

        // this is pre-emptive 'shutdown' stuff so it doesnt break
        if (php_sapi_name() !== 'cli') {
            exit;
        }
        if (!$this->token) {
            echo 'Please set the DISCORD_BOT_TOKEN environment variable.', PHP_EOL;
            exit;
        }
        if (!$this->error_channel_id) {
            echo 'Please set the DISCORD_ERROR_CHANNEL environment variable.', PHP_EOL;
            exit;
        }
        $discord = new Discord([
            'token' => $this->token,
        ]);

        $service = new DiscordManager();

        $discord->on('ready', function (Discord $discord) use ($service) {
            // startup message //////////////////
            echo 'Bot is ready!', PHP_EOL;
            // send message to specified channel
            $guild = $discord->guilds->first();
            $channel = $guild->channels->get('id', $this->error_channel_id);

            $channel->sendMessage('Bot is ready! Use '.$this->prefix.'ping to check delay.');
            if (!$this->announcement_channel_id) {
                $channel->sendMessage('No announcement channel is set! This means I will be unable to announce any new posts etc. Webhooks will function as normal.');
            }
            ////////////////////////////////////

            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($service) {
                $builder = MessageBuilder::new();

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

                // Check rank/level
                if ($message->content == $this->prefix.'level' || $message->content == $this->prefix.'rank') {
                    // Attempt to fetch level information
                    $response = $service->showUserInfo($message);
                    if (!$response) {
                        // Error if no corresponding on-site user
                        $message->reply('You don\'t seem to have a level! Have you linked your Discord account on site?');
                    }
                    // Otherwise return the generated rank card
                    $message->reply($builder->addFile(public_path('images/cards/'.$response)));
                    // Remove the card file since it is now uploaded to Discord
                    unlink(public_path('images/cards/'.$response));

                    return;
                }

                // finally check if we can give exp to this user
                try {
                    $action = $service->giveExp($message->author->id, $message->timestamp);
                    // if action is string, throw error
                    if (is_string($action)) {
                        throw new \Exception($action);
                    }
                    if (isset($action['action']) && $action['action'] == 'Level') {
                        // check for rewards
                        $data = $service->checkRewards($message->author->id);

                        if (isset($data['role']) && $data['role']) {
                            // give the user the role
                            $member = $message->guild->members->get('id', $message->author->id);
                            $member->addRole($data['role']);
                        }

                        switch (Settings::get('discord_level_notif')) {
                            case 0:
                                // Send no notification
                                break;
                            case 1:
                                // DM user
                                $message->author->sendMessage('You leveled up! You are now level '.$action['level'].'!'.($data['count'] ? ' You have received '.$data['count'].' rewards!' : ''));
                                break;
                            case 2:
                                // Reply directly to message
                                $message->reply('You leveled up! You are now level '.$action['level'].'!'.($data['count'] ? ' You have received '.$data['count'].' rewards!' : ''));
                                break;
                        }
                    }
                } catch (\Exception $e) {
                    // this sends the error to the specified channel
                    $guild = $discord->guilds->first();
                    $channel = $guild->channels->get('id', $this->error_channel_id);

                    $channel->sendMessage('Error: '.$e->getMessage());
                }
            });
        });
        // init loop
        $discord->run();
    }
}
