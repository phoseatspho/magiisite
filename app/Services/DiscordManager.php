<?php

namespace App\Services;

use App\Models\Discord\DiscordReward;
use App\Models\User\UserAlias;
use App\Models\User\UserDiscordLevel;
use Config;
use Settings;

class DiscordManager extends Service
{
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
     * Show the user their EXP and level info.
     *
     * @param mixed $user
     * @param mixed $message
     */
    public function showUserInfo($user, $message)
    {
        // we're only returning formatting here since I * refuse * to pass around the $discord variable (not worth the trouble)
        return [];
    }

    /**
     * Check and distribute rewards.
     *
     * @param mixed $id
     */
    public function checkRewards($id)
    {
        try {
            if (UserAlias::where('extra_data', $id)->exists()) {
                $user = UserAlias::where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = UserDiscordLevel::where('user_id', $user->id)->first();

            $rewards = DiscordReward::where('level', $level->level)->get();

            if ($rewards) {
                $assets = createAssetsArray();
                $count = 0;

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

                                $count += 1;
                            }
                        }
                    }
                }
            }

            // Logging data
            $logType = 'Discord Level Up';
            $data = [
                'data' => 'Received rewards for levelling up to level '.$level->level.'.',
            ];

            // Distribute user rewards
            if (!$assets = fillUserAssets($assets, null, $user, $logType, $data)) {
                throw new \Exception('Failed to distribute rewards to user.');
            }

            return $count;
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
            if (UserAlias::where('extra_data', $id)->exists()) {
                $user = UserAlias::where('extra_data', $id)->first()->user;
            } else {
                return;
            }
            $level = UserDiscordLevel::where('user_id', $user->id)->first();

            if (!$level) {
                $level = UserDiscordLevel::create([
                    'user_id'         => $user->id,
                    'level'           => 0,
                    'exp'             => 0,
                    'last_message_at' => $timestamp,
                ]);
                // set constant for max exp you can gain.
                // multiplier can increase this
                $exp = 20;
                $multiplier = Settings::get('discord_exp_multiplier') ?? 1;
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
