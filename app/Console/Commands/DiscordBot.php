<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use App\Models\User\UserDiscordLevel;
use App\Services\DiscordManager;
use Carbon\Carbon;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command as DiscordCommand;
use Discord\Parts\Interactions\Interaction;
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
        $this->token = config('lorekeeper.discord_bot.env.token');
        $this->prefix = '/';
        $this->error_channel_id = config('lorekeeper.discord_bot.env.error_channel');
        // webhook related settings - if we should delete webhook messages and post them ourselves etc.
        $this->announcement_channel_id = config('lorekeeper.discord_bot.env.announcement_channel');
        // 
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
            $guild = config('lorekeeper.discord_bot.env.guild_id') ? $discord->guilds->get('id', config('lorekeeper.discord_bot.env.guild_id')) : $discord->guilds->first();
            $channel = $guild->channels->get('id', $this->error_channel_id);

            $channel->sendMessage('Bot is ready! Use '.$this->prefix.'ping to check delay.');
            if (!$this->announcement_channel_id) {
                $channel->sendMessage('No announcement channel is set! This means I will be unable to announce any new posts etc. Webhooks will function as normal.');
            }
            ////////////////////////////////////

            // Register commands
            foreach (config('lorekeeper.discord_bot.commands') as $command) {
                $newCommand = new DiscordCommand($discord, $command);
                $discord->application->commands->save($newCommand);
            }

            // Listen for commands
            $discord->listenCommand('help', function (Interaction $interaction) use ($service) {
                $response = $service->showHelpMessage();
                if (!$response) {
                    // Error
                    $interaction->respondWithMessage(MessageBuilder::new()->setContent('Couldn\'t generate help message! Please try again later.'));

                    return;
                }
                $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($response));
            });

            $discord->listenCommand('ping', function (Interaction $interaction) {
                // Compare timestamps by milliseconds
                $now = Carbon::now();
                $interaction->respondWithMessage(
                    MessageBuilder::new()->setContent('Pong! Delay: '.$now->diffInMilliseconds($interaction->timestamp).'ms')
                );
            });

            $discord->listenCommand('rank', function (Interaction $interaction) use ($service) {
                $interaction->acknowledgeWithResponse(false);

                // Fetch level information
                $level = $service->getUserLevel($interaction);
                if (!$level) {
                    // Error if no corresponding on-site user
                    $interaction->updateOriginalResponse(MessageBuilder::new()->setContent('You don\'t seem to have a level! Have you linked your Discord account on site?'), true);

                    return;
                }

                // Generate and return rank card
                $response = $service->showUserInfo($level);
                $interaction->updateOriginalResponse(
                    MessageBuilder::new()->addFile(public_path('images/cards/'.$response))
                );
                // Remove the card file since it is now uploaded to Discord
                unlink(public_path('images/cards/'.$response));
            });

            $discord->listenCommand('leaderboard', function (Interaction $interaction) use ($discord, $service) {
                // See if the user has a level/rank
                $level = $service->getUserLevel($interaction);

                // Fetch top ten users
                $topTen = (new UserDiscordLevel)->topTen();
                $i = 1;
                $emojis = ['🥇', '🥈', '🥉'];
                $data = [];
                foreach ($topTen as $top) {
                    $data[] = [
                        'name'   => ($emojis[$i - 1] ?? '').' #'.$i.' '.$top->user->name,
                        'value'  => 'Level '.$top->level.' ('.$top->exp.' EXP)',
                        'inline' => false,
                    ];
                    $i++; // increment counter so that rank is correct (index 0 = rank 1)
                }

                // Assemble embed
                $level ? $footer = [
                    'text'    => 'Your position: #'.$level->relativeRank($level->user),
                    'iconUrl' => url('/images/avatars/'.$level->user->avatar),
                ]
                : $footer = null;
                $embed = $discord->factory(Embed::class, [
                    'color'        => hexdec(config('lorekeeper.discord_bot.rank_cards.exp_bar')),
                    'title'        => config('lorekeeper.settings.site_name').' ・ Leaderboard',
                    'type'         => 'rich',
                    'avatar_url'   => url('images/favicon.ico'),
                    'username'     => config('lorekeeper.settings.site_name'),
                    'fields'       => $data,
                    'footer'       => $footer,
                ]);

                $builder = MessageBuilder::new()->addEmbed($embed);

                $interaction->respondWithMessage($builder);
            });

            $discord->listenCommand('grant', function (Interaction $interaction) use ($service) {
                // Attempt to grant
                $response = $service->grant($interaction);
                if (!$response) {
                    // Error
                    $interaction->respondWithMessage(MessageBuilder::new()->setContent('Error granting level. Please check your input and try again.'));

                    return;
                }
                // response can still be error response
                $interaction->respondWithMessage(MessageBuilder::new()->setContent($response));
            });

            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($service) {
                // don't reply to ourselves
                if ($message->author->bot) {
                    return;
                }

                // finally check if we can give exp to this user
                try {
                    if (in_array($message->channel_id, config('lorekeeper.discord_bot.ignored_channels'))) {
                        return;
                    }

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
                                // check if we have a specific channel set
                                if (config('lorekeeper.discord_bot.level_up_channel_id')) {
                                    $channel = $message->guild->channels->get('id', config('lorekeeper.discord_bot.level_up_channel_id'));
                                    // mention user
                                    $channel->sendMessage('<@'.$message->author->id.'> You leveled up! You are now level '.$action['level'].'!'.($data['count'] ? ' You have received '.$data['count'].' rewards!' : ''));
                                } else {
                                    // Reply directly to message
                                    $message->reply('You leveled up! You are now level '.$action['level'].'!'.($data['count'] ? ' You have received '.$data['count'].' rewards!' : ''));
                                }
                                break;
                            default:
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
