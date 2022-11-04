<?php

namespace App\Services;

use App\Models\Discord\DiscordReward;
use App\Models\User\UserAlias;
use App\Models\User\UserDiscordLevel;
use Config;
use Intervention\Image\Facades\Image;
use Settings;

class DiscordManager extends Service
{
    /**
     * Generates a message with all commands and their descriptions.
     * 
     */
    public function showHelpMessage()
    {
        $data = [];
        $data['title'] = 'Loaded Command List';
        $data['type'] = 'rich';
        $data['avatar_url'] = url('images/favicon.ico');
        $data['color'] = 6208428;

        foreach (config('lorekeeper.discord_bot.commands') as $command) {
            $data['fields'][] = [
                'name' => $command['name'],
                'value' => $command['description'],
                'inline' => false
            ];
        }

        return $data;
    }

    /**
     * Handles webhook messages.
     *
     * @param mixed      $content
     * @param mixed      $title
     * @param mixed      $embed_content
     * @param mixed|null $author
     * @param mixed|null $url
     * @param mixed|null $fields
     */
    public function handleWebhook($content, $title, $embed_content, $author = null, $url = null, $fields = null)
    {
        $webhook = env('DISCORD_WEBHOOK_URL');
        if ($webhook) {
            // format data
            if ($author) {
                $author_data = [
                    'name'     => $author->name,
                    'url'      => $author->url,
                    'icon_url' => url('/images/avatars/'.$author->avatar),
                ];
            }
            $description = $url ? 'View [Here]('.$url.')' : '_ _';
            $data = [];
            $data['username'] = Config::get('lorekeeper.settings.site_name', 'Lorekeeper');
            $data['avatar_url'] = url('images/favicon.ico');
            $data['content'] = $content;
            $data['embeds'] = [[
                'color'       => 6208428,
                'author'      => $author_data ?? null,
                'title'       => $title,
                'description' => $description,
            ]];
            if ($fields) {
                $data['embeds'][0]['fields'] = [$fields];
            }

            // send post to webhook, with $data as json payload
            $ch = curl_init($webhook);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // get response
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response != '') {
                return ['error' => $response];
            }

            return true;
        }
    }

    /**
     * Fetch user level.
     *
     * @param \Discord\Parts\Channel\Message|\Discord\Parts\Interactions\Interaction|\Discord\Parts\User\User|int $context
     * @param \Carbon\Carbon|null                                                                                 $timestamp
     *
     * @return \App\Models\User\UserDiscordLevel
     */
    public function getUserLevel($context, $timestamp = null)
    {
        try {
            if (is_object($context)) {
                switch (get_class($context)) {
                    case 'Discord\Parts\Interactions\Interaction':
                        $author = $context->user->id;
                        break;
                    case 'Discord\Parts\Channel\Message':
                        $author = $context->author->id;
                        break;
                    case 'Discord\Parts\User\User':
                        $author = $context->id;
                        break;
                }
            } else {
                // If a plain string is being passed in, it's liable
                // to just be a user ID, so there's no need to extract that
                // information.
                $author = $context;
            }

            // Provided message author, fetch user information
            if (UserAlias::where('site', 'discord')->where('extra_data', $author)->exists()) {
                $user = UserAlias::where('site', 'discord')->where('extra_data', $author)->first()->user;
            } else {
                return false;
            }

            // Fetch level information
            $level = UserDiscordLevel::where('user_id', $user->id)->first();
            if (!$level) {
                // Or create it, if necessary
                $this->giveExp($author, $timestamp ?? $context->timestamp);
                $level = UserDiscordLevel::where('user_id', $user->id)->first();
            }

            return $level;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Generate a rank card for a user on request.
     *
     * @param \App\Models\User\UserDiscordLevel $level
     *
     * @return string
     */
    public function showUserInfo($level)
    {
        $user = $level->user;

        // Fetch config values for convenience
        $config = [
            'accent'  => config('lorekeeper.discord_bot.rank_cards.accent_color'),
            'exp'     => config('lorekeeper.discord_bot.rank_cards.exp_bar'),
            'font'    => public_path(config('lorekeeper.discord_bot.rank_cards.font_file')),
            'text'    => config('lorekeeper.discord_bot.rank_cards.text_color'),
            'expText' => config('lorekeeper.discord_bot.rank_cards.exp_text') ?? config('lorekeeper.discord_bot.rank_cards.text_color'),
            'opacity' => config('lorekeeper.discord_bot.rank_cards.accent_opacity'),
        ];

        // Assemble avatar using circular mask
        $avatar = Image::canvas(150, 150, $config['accent'])
            ->insert(public_path('images/avatars/'.$user->avatar), 'center')
            ->mask(public_path('images/cards/assets/avatar_mask.png'));

        // Assemble EXP bar
        $requiredExp = 5 * (pow($level->level, 2)) + (50 * $level->level) + 100;
        $progress = Image::canvas(365, 20, $config['accent'])
            ->opacity($config['opacity'])
            ->rectangle(0, 0, ($level->exp / $requiredExp) * 365, 20, function ($draw) use ($config) {
                // Fill a portion of the bar relative to the user's
                // current discord EXP
                $draw->background($config['exp']);
            })
            ->mask(public_path('images/cards/assets/rank_card_mask.png'));

        $card = Image::make(public_path('images/cards/assets/generated-back.png'));

        $card
            ->insert($progress, 'bottom-right', 48, 45)
            ->insert($avatar, 'left', 20)
            ->text($user->name, 200, 85, function ($font) use ($config) {
                // Username
                $font->file($config['font']);
                $font->color($config['text']);
                $font->align('left');
                $font->size(40);
            })
            ->text($user->rank->name, 201, 105, function ($font) use ($user, $config) {
                // User rank
                $font->file($config['font']);
                $font->color($user->rank->color ?? $config['text']);
                $font->align('left');
                $font->size(22);
            })
            ->text('#'.$level->relativeRank($user), 540, 80, function ($font) use ($config) {
                // Relative discord rank
                $font->file($config['font']);
                $font->color($config['text']);
                $font->align('right');
                $font->size(40);
            })
            ->text('Level '.$level->level, 540, 105, function ($font) use ($config) {
                // Discord level
                $font->file($config['font']);
                $font->color($config['text']);
                $font->align('right');
                $font->size(22);
            })
            ->text($level->exp.'/'.$requiredExp.' EXP', 550, 170, function ($font) use ($config) {
                // Exp info
                $font->file($config['font']);
                $font->color($config['expText']);
                $font->align('right');
                $font->size(15);
            });

        // Set dir and filename
        $dir = public_path('images/cards');
        $filename = randomString(15).'.png';

        // Save the card itself and return the filename
        $card->save($dir.'/'.$filename);

        return $filename;
    }

    /**
     * Check and distribute rewards.
     *
     * @param mixed $id
     */
    public function checkRewards($id)
    {
        try {
            if (UserAlias::where('site', 'discord')->where('extra_data', $id)->exists()) {
                $user = UserAlias::where('site', 'discord')->where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = UserDiscordLevel::where('user_id', $user->id)->first();

            $rewards = DiscordReward::where('level', $level->level)->get();

            $data = [];
            // check if there's a role to be given to the user
            $role = $rewards->where('role_reward_id', '!=', null)->first();
            if ($role) {
                $data['role'] = $role->role_reward_id;
            }

            // on-site reward distribution
            if ($rewards) {
                $assets = createAssetsArray();
                $data['count'] = 0;

                foreach ($rewards as $reward) {
                    $raw_types = json_decode($reward->loot, true);
                    //
                    foreach ($raw_types as $type=>$raws) {
                        $model = getAssetModelString($type);
                        if ($model) {
                            foreach ($raws as $key=>$raw) {
                                $assets[$type][] = [
                                    'asset'    => $model::find($key),
                                    'quantity' => $raw['quantity'],
                                ];

                                $data['count'] += 1;
                            }
                        }
                    }
                }
            }

            // Logging data
            $logType = 'Discord Level Up';
            $logData = [
                'data' => 'Received rewards for levelling up to level '.$level->level.'.',
            ];

            // Distribute user rewards
            if (!$assets = fillUserAssets($assets, null, $user, $logType, $logData)) {
                throw new \Exception('Failed to distribute rewards to user.');
            }

            return $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Add EXP to a user.
     *
     * @param int id
     * @param Carbon $timestamp
     * @param mixed  $id
     */
    public function giveExp($id, $timestamp)
    {
        try {
            if (UserAlias::where('site', 'discord')->where('extra_data', $id)->exists()) {
                $user = UserAlias::where('site', 'discord')->where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = UserDiscordLevel::where('user_id', $user->id)->first();

            // set constant for max exp you can gain.
            // multiplier can increase this
            $exp = 20;
            $multiplier = Settings::get('discord_exp_multiplier') ?? 1;

            if (!$level) {
                $level = UserDiscordLevel::create([
                    'user_id'         => $user->id,
                    'level'           => 0,
                    'exp'             => 0,
                    'last_message_at' => $timestamp,
                ]);
                // since they've never had a message before, we can just add exp straight away
                $level->exp += mt_rand($exp / 2, $exp) * $multiplier;
                $level->save();
            // formula: 5 * (lvl ^ 2) + (50 * lvl) + 100 - xp
                // lvl is current level
                // xp is how much XP already have towards the next level.
            } else {
                // check if it's been a minute since the last message
                if (!$level->last_message_at || 1 <= $timestamp->diffInMinutes($level->last_message_at)) {
                    $level->exp += mt_rand($exp / 2, $exp) * $multiplier;
                    $level->last_message_at = $timestamp;
                    $level->save();
                }
            }

            $requiredExp = 5 * (pow($level->level, 2)) + (50 * $level->level) + 100 - $level->exp;
            if ($requiredExp <= 0) {
                $level->level++;
                $level->exp = 0;
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

    /**
     * Grant levels OR exp to a user.
     *
     * @param Interaction $interaction
     *
     * @return string
     */
    public function grant($interaction)
    {
        // check if the user has the permission to grant levels (on-site must have manage_discord power)
        if (UserAlias::where('extra_data', $interaction->user->id)->exists()) {
            $user = UserAlias::where('extra_data', $interaction->user->id)->first()->user;
        } else {
            return 'Could not verify user on site.';
        }

        if (!$user->hasPower('manage_discord')) {
            return 'You do not have the required permissions to grant levels.';
        }

        // Get command params from interaction data
        $options = $interaction->data->options->toArray();

        // check if the user exists on the site
        if (UserAlias::where('extra_data', $options['user'])->exists()) {
            $recipientInfo = UserAlias::where('extra_data', $options['user'])->first();
        } else {
            return 'Recipient does not have any discord level data. Check that they are correctly linked.';
        }

        // log the action
        if(!$this->logAdminAction($user, 'Discord Level Grant', 'Granted '.$options['amount'].' '.$options['type'].' to '.$recipientInfo->user->name)) {
            return 'Failed to log action, grant cancelled.';
        }
        $log = UserUpdateLog::create([
            'staff_id' => $user->id,
            'user_id' => $recipientInfo->user->id,
            'data' => json_encode(['amount' => $options['amount'], 'type' => $options['type']]),
            'type' => 'Discord Level Granted'
        ]);
        if(!$log)
        {
            return 'Failed to log action, grant cancelled.';
        }

        // check what type of grant it is
        if($options['type'] == 'level') {
            // increment the level by the amount specified
            // they wont receive any rewards for this type of level up
            $recipientInfo->user->discordLevel->level += $options['amount'];
        }
        else {
            // increment the exp by the amount specified
            $recipientInfo->user->discordLevel->exp += $options['amount'];
            // we dont have to worry about checking for a level up since it'll be done automatically next time they send a message
            // they will be notified of the level up when they send a message
        }

        $recipientInfo->user->discordLevel->save();

        return 'Successfully granted '.$options['amount'].' '.$options['type'].' to '.$recipientInfo->user->name.'.';
    }
}
