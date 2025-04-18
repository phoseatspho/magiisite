<?php

namespace App\Services;
use App\Models\Criteria\Criterion;

use Carbon\Carbon;

use Config;
use Image;


use Illuminate\Support\Arr;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\UserAward;
use App\Facades\Notifications;
use App\Facades\Settings;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Element\Element;
use App\Models\Item\Item;
use App\Models\Award\Award;
use App\Models\Loot\LootTable;
use App\Models\Prompt\Prompt;
use App\Models\Raffle\Raffle;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Pet\Pet;
use App\Models\Skill\Skill;
use App\Models\Claymore\Gear;
use App\Models\Claymore\Weapon;

use App\Services\Stat\ExperienceManager;
use App\Services\Stat\StatManager;
use App\Services\SkillManager;
use App\Models\Recipe\Recipe;
use Illuminate\Support\Facades\DB;

class SubmissionManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Submission Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of submission data.
    |
    */

    /**
     * Creates a new submission.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     * @param bool                  $isClaim
     * @param mixed                 $isDraft
     *
     * @return mixed
     */
    public function createSubmission($data, $user, $isClaim = false, $isDraft = false) {
        DB::beginTransaction();

        try {
            // 1. check that the prompt can be submitted at this time
            // 2. check that the characters selected exist (are visible too)
            // 3. check that the currencies selected can be attached to characters
            // 4. If there is a parent, check the user has completed the prompt
            if(!$isClaim && !Settings::get('is_prompts_open')) 
             throw new \Exception("The prompt queue is closed for submissions.");
            else if($isClaim && !Settings::get('is_claims_open')) throw new \Exception("The claim queue is closed for submissions.");
            if(!$isClaim && !isset($data['prompt_id'])) throw new \Exception("Please select a prompt.");
            if(!$isClaim) {
                $prompt = Prompt::active()->where('id', $data['prompt_id'])->with('rewards')->first();
                if(!$prompt) throw new \Exception("Invalid prompt selected.");
                if($prompt->parent_id)
                {
                    $submission = Submission::where('user_id', $user->id)->where('prompt_id', $prompt->parent_id)->where('status', 'Approved')->count();    
                    if($submission < $prompt->parent_quantity) throw new \Exception('Please complete the prerequisite.');
                }
            }
            
            if(!$isClaim)
            {
                //level req
                if($prompt->level_req)
                {
                    if(!$user->level || $user->level->current_level < $prompt->level_req) throw new \Exception('You are not high enough level to enter this prompt');
                }
            

            } else {
                $prompt = null;
            }

            $withCriteriaSelected = isset($data['criterion']) ? array_filter($data['criterion'], function($obj){
                return isset($obj['id']);
            }) : [];
            if(count($withCriteriaSelected) > 0) $data['criterion'] = $withCriteriaSelected;
            else $data['criterion'] = null;

            // Create the submission itself.    
            $submission = Submission::create([
                'user_id'   => $user->id,
                'url'       => $data['url'] ?? null,
                'status'    => $isDraft ? 'Draft' : 'Pending',
                'comments'  => $data['comments'],
                'data'      => null,
            ] + ($isClaim ? [] : [
                'prompt_id' => $prompt->id,
            ]));

            // Set items that have been attached.
            $assets = $this->createUserAttachments($submission, $data, $user);
            $userAssets = $assets['userAssets'];
            $promptRewards = $assets['promptRewards'];

            $submission->update([
                'data' => json_encode([
                    'user'    => Arr::only(getDataReadyAssets($userAssets), ['user_items', 'currencies']),
                    'rewards' => getDataReadyAssets($promptRewards),
                    'criterion' => isset($data['criterion']) ? $data['criterion'] : null,
                ]), // list of rewards and addons
            ]);

            // Set characters that have been attached.
            $this->createCharacterAttachments($submission, $data);

            // send webhook alert to staff
            $response = (new DiscordManager)->handleWebhook(
                'A new ' . ($isClaim ? 'claim' : 'submission for ' . $prompt->name) . ' has been created by [' . $user->name . ']('. $user->url .'). (#'.$submission->id.')',
                ($isClaim ? 'Claim' : 'Submission') . ($isClaim ? '' : ' for ' . $prompt->name),
                $user,
                $submission->adminUrl,
                null,
                true
            );

            if (is_array($response)) {
                flash($response['error'])->error();
                throw new \Exception('Failed to create webhook.');
            }

            return $this->commitReturn($submission);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Edits an existing submission.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     * @param bool                  $isClaim
     * @param mixed                 $submission
     * @param mixed                 $isSubmit
     *
     * @return mixed
     */
    public function editSubmission($submission, $data, $user, $isClaim = false, $isSubmit = false) {
        DB::beginTransaction();

        try {
            // 1. check that the prompt can be submitted at this time
            // 2. check that the characters selected exist (are visible too)
            // 3. check that the currencies selected can be attached to characters
            if (!$isClaim && !Settings::get('is_prompts_open')) {
                throw new \Exception('The prompt queue is closed for submissions.');
            } elseif ($isClaim && !Settings::get('is_claims_open')) {
                throw new \Exception('The claim queue is closed for submissions.');
            }
            if (!$isClaim && !isset($data['prompt_id'])) {
                throw new \Exception('Please select a prompt.');
            }
            if (!$isClaim) {
                $prompt = Prompt::active()->where('id', $data['prompt_id'])->with('rewards')->first();
                if (!$prompt) {
                    throw new \Exception('Invalid prompt selected.');
                }
            } else {
                $prompt = null;
            }

            // First, return all items and currency applied.
            // Also, as this is an edit, delete all attached characters to be re-applied later.
            $this->removeAttachments($submission);
            SubmissionCharacter::where('submission_id', $submission->id)->delete();

            if ($isSubmit) {
                $submission->update(['status' => 'Pending']);
            }

            // Then, re-attach everything fresh.
            $assets = $this->createUserAttachments($submission, $data, $user);
            $userAssets = $assets['userAssets'];
            $promptRewards = $assets['promptRewards'];
            $this->createCharacterAttachments($submission, $data);

            // Modify submission
            $submission->update([
                'url'           => $data['url'] ?? null,
                'updated_at'    => Carbon::now(),
                'comments'      => $data['comments'],
                'data'          => json_encode([
                    'user'          => Arr::only(getDataReadyAssets($userAssets), ['user_items', 'currencies']),
                    'rewards'       => getDataReadyAssets($promptRewards),
                    'criterion'     => isset($data['criterion']) ? $data['criterion'] : null,
                ]), // list of rewards and addons
            ] + ($isClaim ? [] : ['prompt_id' => $prompt->id]));

            return $this->commitReturn($submission);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
    * Cancels a submission.
    *
    * @param mixed $data the submission data
    * @param mixed $user the user performing the cancellation
    */
    public function cancelSubmission($data, $user) {
        DB::beginTransaction();

        try {
            // 1. check that the submission exists
            // 2. check that the submission is pending
            if (!isset($data['submission'])) {
                $submission = Submission::where('status', 'Pending')->where('id', $data['id'])->first();
            } elseif ($data['submission']->status == 'Pending') {
                $submission = $data['submission'];
            } else {
                $submission = null;
            }
            if (!$submission) {
                throw new \Exception('Invalid submission.');
            }

            // Set staff comments
            if (isset($data['staff_comments']) && $data['staff_comments']) {
                $data['parsed_staff_comments'] = parse($data['staff_comments']);
            } else {
                $data['parsed_staff_comments'] = null;
            }

            $assets = $submission->data;
            $userAssets = $assets['user'];
            // Remove prompt-only rewards
            $promptRewards = $this->removePromptAttachments($submission);

            if ($user->id != $submission->user_id) {
                // The only things we need to set are:
                // 1. staff comment
                // 2. staff ID
                // 3. status
                $submission->update([
                    'staff_comments'        => $data['staff_comments'],
                    'parsed_staff_comments' => $data['parsed_staff_comments'],
                    'updated_at'            => Carbon::now(),
                    'staff_id'              => $user->id,
                    'status'                => 'Draft',
                    'data'                  => json_encode([
                        'user'      => $userAssets,
                        'rewards'   => getDataReadyAssets($promptRewards),
                    ]), // list of rewards and addons
                ]);

                Notifications::create($submission->prompt_id ? 'SUBMISSION_CANCELLED' : 'CLAIM_CANCELLED', $submission->user, [
                    'staff_url'     => $user->url,
                    'staff_name'    => $user->name,
                    'submission_id' => $submission->id,
                    'data'          => json_encode(getDataReadyAssets($assets)),
                    'is_focus'      => isset($data['character_is_focus']) && $data['character_is_focus'][$c->id] ? $data['character_is_focus'][$c->id] : 0,
                ]);
            } else {
                // This is when a user cancels their own submission back into draft form
                $submission->update([
                    'status'     => 'Draft',
                    'updated_at' => Carbon::now(),
                    'data'       => json_encode([
                        'user'      => $userAssets,
                        'rewards'   => getDataReadyAssets($promptRewards),
                    ]), // list of rewards and addons
                ]);
            }

            return $this->commitReturn($submission);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
            return $this->rollbackReturn(false);
    }


    /**
     * Rejects a submission.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return mixed
     */
    public function rejectSubmission($data, $user) {
        DB::beginTransaction();

        try {
            // 1. check that the submission exists
            // 2. check that the submission is pending
            if (!isset($data['submission'])) {
                $submission = Submission::where('status', 'Pending')->where('id', $data['id'])->first();
            } elseif ($data['submission']->status == 'Pending') {
                $submission = $data['submission'];
            } else {
                $submission = null;
            }
            if (!$submission) {
                throw new \Exception('Invalid submission.');
            }

            // Return all items and currency applied.
            $this->removeAttachments($submission);

            if (isset($data['staff_comments']) && $data['staff_comments']) {
                $data['parsed_staff_comments'] = parse($data['staff_comments']);
            } else {
                $data['parsed_staff_comments'] = null;
            }

            // The only things we need to set are:
            // 1. staff comment
            // 2. staff ID
            // 3. status
            $submission->update([
                'staff_comments'        => $data['staff_comments'],
                'parsed_staff_comments' => $data['parsed_staff_comments'],
                'staff_id'              => $user->id,
                'status'                => 'Rejected',
            ]);

            Notifications::create($submission->prompt_id ? 'SUBMISSION_REJECTED' : 'CLAIM_REJECTED', $submission->user, [
                'staff_url'     => $user->url,
                'staff_name'    => $user->name,
                'submission_id' => $submission->id,
            ]);

            if (!$this->logAdminAction($user, 'Submission Rejected', 'Rejected submission <a href="'.$submission->viewurl.'">#'.$submission->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($submission);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Approves a submission.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return mixed
     */
    public function approveSubmission($data, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the submission exists
            // 2. check that the submission is pending
            $submission = Submission::where('status', 'Pending')->where('id', $data['id'])->first();
            if(!$submission) throw new \Exception("Invalid submission.");

            // Remove any added items, hold counts, and add logs
            $addonData = $submission->data['user'];
            $inventoryManager = new InventoryManager;
            if(isset($addonData['user_items'])) {
                $stacks = $addonData['user_items'];
                foreach($addonData['user_items'] as $userItemId => $quantity) {
                    $userItemRow = UserItem::find($userItemId);
                    if(!$userItemRow) throw new \Exception("Cannot return an invalid item. (".$userItemId.")");
                    if($userItemRow->submission_count < $quantity) throw new \Exception("Cannot return more items than was held. (".$userItemId.")");
                    $userItemRow->submission_count -= $quantity;
                    $userItemRow->save();
                }

                // Workaround for user not being unset after inventory shuffling, preventing proper staff ID assignment
                $staff = $user;

                foreach($stacks as $stackId=>$quantity) {
                    $stack = UserItem::find($stackId);
                    $user = User::find($submission->user_id);
                    if(!$inventoryManager->debitStack($user, $submission->prompt_id ? 'Prompt Approved' : 'Claim Approved', ['data' => 'Item used in submission (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)'], $stack, $quantity)) throw new \Exception("Failed to create log for item stack.");
                }

                // Set user back to the processing staff member, now that addons have been properly processed.
                $user = $staff;
            }

            // Log currency removal, etc.
            $currencyManager = new CurrencyManager;
            if(isset($addonData['currencies']) && $addonData['currencies'])
            {
                foreach($addonData['currencies'] as $currencyId=>$quantity) {
                    $currency = Currency::find($currencyId);
                    if(!$currencyManager->createLog($submission->user_id, 'User', null, null,
                    $submission->prompt_id ? 'Prompt Approved' : 'Claim Approved', 'Used in ' . ($submission->prompt_id ? 'prompt' : 'claim') . ' (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)', $currencyId, $quantity))
                        throw new \Exception("Failed to create currency log.");
                }
            }

            // The character identification comes in both the slug field and as character IDs
            // that key the reward ID/quantity arrays.
            // We'll need to match characters to the rewards for them.
            // First, check if the characters are accessible to begin with.
            if(isset($data['slug'])) {
                $characters = Character::myo(0)->visible()->whereIn('slug', $data['slug'])->get();
                if(count($characters) != count($data['slug'])) throw new \Exception("One or more of the selected characters do not exist.");
            }
            else $characters = [];

            // Get the updated set of rewards
            $rewards = $this->processRewards($data, false, true);


            // Logging data
            $promptLogType = $submission->prompt_id ? 'Prompt Rewards' : 'Claim Rewards';
            $promptData = [
                'data' => 'Received rewards for '.($submission->prompt_id ? 'submission' : 'claim').' (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)'
            ];

            // Distribute user rewards
            if(!$rewards = fillUserAssets($rewards, $user, $submission->user, $promptLogType, $promptData)) throw new \Exception("Failed to distribute rewards to user.");

            // Distribute currency from criteria
            $service = new CurrencyManager;
            
            if(isset($data['criterion'])) {
                foreach($data['criterion'] as $key => $criterionData) {
                    $criterion = Criterion::where('id', $criterionData['id'])->first();
                    if(!$service->creditCurrency($user, $submission->user, $promptLogType, $promptData['data'], $criterion->currency, $criterion->calculateReward($criterionData))) throw new \Exception("Failed to distribute criterion rewards to user.");
                }
            }
        
            
            // Retrieve all reward IDs for characters
            $currencyIds = []; $itemIds = []; $tableIds = []; $awardIds = [];  $elementIds = [];
            if(isset($data['character_currency_id'])) {
                foreach($data['character_currency_id'] as $c)
                {
                    foreach($c as $currencyId) $currencyIds[] = $currencyId;
                } // Non-expanded character rewards
            }
            elseif(isset($data['character_rewardable_id']))
            {
                $data['character_rewardable_id'] = array_map(array($this, 'innerNull'),$data['character_rewardable_id']);
                foreach($data['character_rewardable_id'] as $ckey => $c)
                {
                    foreach($c as $key => $id) {

                        switch($data['character_rewardable_type'][$ckey][$key])
                        {
                            case 'Currency':    $currencyIds[]  = $id; break;
                            case 'Item':        $itemIds[]      = $id; break;
                            case 'LootTable':   $tableIds[]     = $id; break;
                            case 'Award':       $awardIds[]     = $id; break;
                            case 'Element':     $elementIds[]   = $id; break;
                        }
                    }
                } // Expanded character rewards
            }
            array_unique($currencyIds);            array_unique($itemIds);            array_unique($tableIds);          array_unique($awardIds);     array_unique($elementIds);
            $currencies = Currency::whereIn('id', $currencyIds)->where('is_character_owned', 1)->get()->keyBy('id');
            $items = Item::whereIn('id', $itemIds)->get()->keyBy('id');
            $tables = LootTable::whereIn('id', $tableIds)->get()->keyBy('id');
            $awards = Award::whereIn('id', $awardIds)->get()->keyBy('id');
            $elements = Element::whereIn('id', $elementIds)->get()->keyBy('id');

            // We're going to remove all characters from the submission and reattach them with the updated data
            $submission->characters()->delete();

            // do the user stats stuff first so that we can use variables later
            // stats & exp ---- currently prompt only
            if($submission->prompt_id && $submission->prompt->expreward) {
                // initialise
                $levelLog = new ExperienceManager;
                $statLog = new StatManager;
                // data
                $levelData = 'Received rewards for '.($submission->prompt_id ? 'submission' : 'claim').' (<a href="'.$submission->viewUrl.'">#'.$submission->id.'</a>)';
                // to be encoded
                $user_exp = null;
                $user_points = null;
                $character_exp = null;
                $character_points = null;
                // user
                $level = $submission->user->level;
                $levelUser = $submission->user;
                if(!$level) throw new \Exception('This user does not have a level log.');

                // exp
                if($submission->prompt->expreward->user_exp || isset($data['bonus_user_exp']))
                {
                    // get predefined user exp amount
                    $quantity = $submission->prompt->expreward->user_exp;
                        if(isset($data['bonus_user_exp']))
                        {
                            // add bonus
                            $quantity += $data['bonus_user_exp'];
                        }
                        else $data['bonus_user_exp'] = 0;
                        $user_exp += $data['bonus_user_exp'];
                    if(!$levelLog->creditExp(null, $levelUser, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant user exp');
                }
                //points
                if($submission->prompt->expreward->user_points || isset($data['bonus_user_points']))
                {
                    $quantity = $submission->prompt->expreward->user_points;
                        if(isset($data['bonus_user_points']))
                        {
                            $quantity += $data['bonus_user_points'];
                        }
                        else $data['bonus_user_points'] = 0;
                        $user_points +=  $data['bonus_user_points'];
                    if(!$statLog->creditStat(null, $levelUser, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant user points');
                }
            }

            // Distribute character rewards
            foreach($characters as $key => $c)
            {
                // Users might not pass in clean arrays (may contain redundant data) so we need to clean that up
                $assets = $this->processRewards($data + ['character_id' => $c->id, 'currencies' => $currencies, 'items' => $items, 'tables' => $tables, 'awards' => $awards, 'elements'  => $elements], true);

                if(!$assets = fillCharacterAssets($assets, $user, $c, $promptLogType, $promptData, $submission->user)) throw new \Exception("Failed to distribute rewards to character.");

                SubmissionCharacter::create([
                    'character_id'  => $c->id,
                    'submission_id' => $submission->id,
                    'data'          => json_encode(getDataReadyAssets($assets)),
                    'is_focus'      => isset($data['character_is_focus']) && $data['character_is_focus'][$c->id] ? $data['character_is_focus'][$c->id] : 0,
                ]);

                // here we do da skills
                $skillManager = new SkillManager;
                $skills = [];
                if(isset($data['character_is_focus']) && $data['character_is_focus'][$c->id] && $submission->prompt_id) {
                    if(isset($data['skill_id'])) {
                        foreach($data['skill_id'] as $key => $skill_id) {
                            // find skill
                            $skill = Skill::find($skill_id);
                            if (!$skill) continue;
                            $quantity = $data['skill_quantity'][$key];
                            // add info to $skills
                            $skills[] = [
                                'skill' => $skill->id,
                                'quantity' => $quantity
                            ];
                            if(!$skillManager->creditSkill($user, $c, $skill, $quantity, 'Prompt Reward')) throw new \Exception("Failed to credit skill.");
                        }
                    }
                    // if there's exp rewards
                    if($submission->prompt->expreward) {
                        $level = $c->level;
                        if(!$level) throw new \Exception('One or more characters do not have a level log.');
                        // exp
                        if($submission->prompt->expreward->chara_exp || isset($data['bonus_exp']))
                        {
                            $quantity = $submission->prompt->expreward->chara_exp;
                            if(isset($data['bonus_exp']))
                            {
                                $quantity += $data['bonus_exp'];
                            }
                            else $data['bonus_exp'] = 0;
                            $character_exp += $data['bonus_exp'];
                            if(!$levelLog->creditExp(null, $c, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant character exp');
                        }
                        // points
                        if($submission->prompt->expreward->chara_points || isset($data['bonus_points']))
                        {
                            $quantity = $submission->prompt->expreward->chara_points;
                            if(isset($data['bonus_points']))
                            {
                                $quantity += $data['bonus_points'];
                            }
                            else $data['bonus_points'] = 0;
                            $character_points += $data['bonus_points'];
                            if(!$statLog->creditStat(null, $c, $promptLogType, $levelData, $quantity)) throw new \Exception('Could not grant character points');
                        }
                    }
                }
            }

            if($submission->prompt_id && $submission->prompt->expreward) {
                $json[] = [
                    'User_Bonus' => [
                        'exp' => $user_exp,
                        'points' => $user_points
                    ],
                    'Character_Bonus' => [
                        'exp' => $character_exp,
                        'points' => $character_points
                    ],
                ];

                $bonus = json_encode($json);
            }

            // Increment user submission count if it's a prompt
            if($submission->prompt_id) {
                $user->settings->submission_count++;
                $user->settings->save();
            }

			if(isset($data['staff_comments']) && $data['staff_comments']) $data['parsed_staff_comments'] = parse($data['staff_comments']);
			else $data['parsed_staff_comments'] = null;

            // Finally, set:
			// 1. staff comments
            // 2. staff ID
            // 3. status
            // 4. final rewards
            $submission->update([
			    'staff_comments' => $data['staff_comments'],
				'parsed_staff_comments' => $data['parsed_staff_comments'],
                'staff_id' => $user->id,
                'status' => 'Approved',
                'data' => json_encode([
                    'user' => $addonData,
                    'rewards' => getDataReadyAssets($rewards),
                    'skills' => $skills ?? null,
                    'criterion' => isset($data['criterion']) ? $data['criterion'] : null,
                    ]), // list of rewards
                
            ]);

            Notifications::create($submission->prompt_id ? 'SUBMISSION_APPROVED' : 'CLAIM_APPROVED', $submission->user, [
                'staff_url' => $user->url,
                'staff_name' => $user->name,
                'submission_id' => $submission->id,
            ]);

            return $this->commitReturn($submission);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a submission.
     *
     * @param mixed $data the data of the submission to be deleted
     * @param mixed $user the user performing the deletion
     */
    public function deleteSubmission($data, $user) {
        DB::beginTransaction();
        try {
            // 1. check that the submission exists
            // 2. check that the submission is a draft
            if (!isset($data['submission'])) {
                $submission = Submission::where('status', 'Draft')->where('id', $data['id'])->first();
            } elseif ($data['submission']->status == 'Pending') {
                $submission = $data['submission'];
            } else {
                $submission = null;
            }
            if (!$submission) {
                throw new \Exception('Invalid submission.');
            }
            if ($user->id != $submission->user_id) {
                throw new \Exception('This is not your submission.');
            }

            // Remove characters and attachments.
            SubmissionCharacter::where('submission_id', $submission->id)->delete();
            $this->removeAttachments($submission);
            $submission->delete();

            return $this->commitReturn($submission);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**************************************************************************************************************
     *
     * PRIVATE FUNCTIONS
     *
     **************************************************************************************************************/

    /**
     * Helper function to remove all empty/zero/falsey values.
     *
     * @param array $value
     *
     * @return array
     */
    private function innerNull($value) {
        return array_values(array_filter($value));
    }

    /**
     * Processes reward data into a format that can be used for distribution.
     *
     * @param array $data
     * @param bool  $isCharacter
     * @param bool  $isStaff
     * @param bool  $isClaim
     *
     * @return array
     */
    private function processRewards($data, $isCharacter, $isStaff = false, $isClaim = false) {
        if ($isCharacter) {
            $assets = createAssetsArray(true);

            if (isset($data['character_currency_id'][$data['character_id']]) && isset($data['character_quantity'][$data['character_id']])) {
                foreach ($data['character_currency_id'][$data['character_id']] as $key => $currency) {
                    if ($data['character_quantity'][$data['character_id']][$key]) {
                        addAsset($assets, $data['currencies'][$currency], $data['character_quantity'][$data['character_id']][$key]);
                    }
                }
            } elseif (isset($data['character_rewardable_type'][$data['character_id']]) && isset($data['character_rewardable_id'][$data['character_id']]) && isset($data['character_rewardable_quantity'][$data['character_id']])) {
                $data['character_rewardable_id'] = array_map([$this, 'innerNull'], $data['character_rewardable_id']);

                foreach ($data['character_rewardable_id'][$data['character_id']] as $key => $reward) {
                    switch ($data['character_rewardable_type'][$data['character_id']][$key]) {
                        case 'Currency': if ($data['character_rewardable_quantity'][$data['character_id']][$key]) {
                            addAsset($assets, $data['currencies'][$reward], $data['character_rewardable_quantity'][$data['character_id']][$key]);
                        } break;
                        case 'Item': if ($data['character_rewardable_quantity'][$data['character_id']][$key]) {
                            addAsset($assets, $data['items'][$reward], $data['character_rewardable_quantity'][$data['character_id']][$key]);
                        } break;   
                        case 'Award': if($data['character_rewardable_quantity'][$data['character_id']][$key]) {
                            addAsset($assets, $data['awards'][$reward], $data['character_rewardable_quantity'][$data['character_id']][$key]); 
                        } break;
                        case 'LootTable': if ($data['character_rewardable_quantity'][$data['character_id']][$key]) {
                            addAsset($assets, $data['tables'][$reward], $data['character_rewardable_quantity'][$data['character_id']][$key]);
                        } break;
                        case 'Element': // we don't check for quanity here
                            addAsset($assets, $data['elements'][$reward], 1);
                            break;
                    }
                }
            }

            return $assets;
        } else {
            $assets = createAssetsArray(false);
            // Process the additional rewards
            if (isset($data['rewardable_type']) && $data['rewardable_type']) {
                foreach ($data['rewardable_type'] as $key => $type) {
                    $reward = null;
                    switch ($type) {
                        case 'Item':
                            $reward = Item::find($data['rewardable_id'][$key]);
                            break;
                        case 'Currency':
                            $reward = Currency::find($data['rewardable_id'][$key]);
                            if (!$reward->is_user_owned) {
                                throw new \Exception('Invalid currency selected.');
                            }
                            break;
                            case 'Award':
                                $reward = Award::find($data['rewardable_id'][$key]);
                                break;
                            case 'Pet':
                                if (!$isStaff) break;
                                $reward = Pet::find($data['rewardable_id'][$key]);
                                break;
                                case 'Gear':
                                    if (!$isStaff) break;
                                    $reward = Gear::find($data['rewardable_id'][$key]);
                                    break;
                                case 'Weapon':
                                    if (!$isStaff) break;
                                    $reward = Weapon::find($data['rewardable_id'][$key]);
                                    break;
                                case 'Recipe':
                                    if (!$isStaff) break;
                                    $reward = Recipe::find($data['rewardable_id'][$key]);
                                    if(!$reward->needs_unlocking) throw new \Exception("Invalid recipe selected.");
                                    break;
                        case 'LootTable':
                            if (!$isStaff) {
                                break;
                            }
                            $reward = LootTable::find($data['rewardable_id'][$key]);
                            break;
                        case 'Raffle':
                            if (!$isStaff && !$isClaim) {
                                break;
                            }
                            $reward = Raffle::find($data['rewardable_id'][$key]);
                            break;
                    }
                    if (!$reward) {
                        continue;
                    }
                    addAsset($assets, $reward, $data['quantity'][$key]);
                }
            }

            return $assets;
        }
    }

    /**************************************************************************************************************
     *
     * ATTACHMENT FUNCTIONS
     *
     **************************************************************************************************************/

    /**
     * Creates user attachments for a submission.
     *
     * @param mixed $submission the submission object
     * @param mixed $data       the data for creating the attachments
     * @param mixed $user       the user object
     */
    private function createUserAttachments($submission, $data, $user) {
        $userAssets = createAssetsArray();

        // Attach items. Technically, the user doesn't lose ownership of the item - we're just adding an additional holding field.
        // We're also not going to add logs as this might add unnecessary fluff to the logs and the items still belong to the user.
        if (isset($data['stack_id'])) {
            foreach ($data['stack_id'] as $stackId) {
                $stack = UserItem::with('item')->find($stackId);
                if (!$stack || $stack->user_id != $user->id) {
                    throw new \Exception('Invalid item selected.');
                }
                if (!isset($data['stack_quantity'][$stackId])) {
                    throw new \Exception('Invalid quantity selected.');
                }
                $stack->submission_count += $data['stack_quantity'][$stackId];
                $stack->save();

                addAsset($userAssets, $stack, $data['stack_quantity'][$stackId]);
            }
        }

        // Attach currencies.
        if (isset($data['currency_id'])) {
            foreach ($data['currency_id'] as $holderKey=>$currencyIds) {
                $holder = explode('-', $holderKey);
                $holderType = $holder[0];
                $holderId = $holder[1];

                $holder = User::find($holderId);

                $currencyManager = new CurrencyManager;
                foreach ($currencyIds as $key=>$currencyId) {
                    $currency = Currency::find($currencyId);
                    if (!$currency) {
                        throw new \Exception('Invalid currency selected.');
                    }
                    if ($data['currency_quantity'][$holderKey][$key] < 0) {
                        throw new \Exception('Cannot attach a negative amount of currency.');
                    }
                    if (!$currencyManager->debitCurrency($holder, null, null, null, $currency, $data['currency_quantity'][$holderKey][$key])) {
                        throw new \Exception('Invalid currency/quantity selected.');
                    }

                    addAsset($userAssets, $currency, $data['currency_quantity'][$holderKey][$key]);
                }
            }
        }

        // Get a list of rewards, then create the submission itself
        $promptRewards = createAssetsArray();
        if ($submission->status == 'Pending' && isset($submission->prompt_id) && $submission->prompt_id) {
            foreach ($submission->prompt->rewards as $reward) {
                addAsset($promptRewards, $reward->reward, $reward->quantity);
            }
        }
        $promptRewards = mergeAssetsArrays($promptRewards, $this->processRewards($data, false));

        return [
            'userAssets'    => $userAssets,
            'promptRewards' => $promptRewards,
        ];
    }

    /**
     * Removes the attachments associated with a prompt from a submission.
     *
     * @param mixed $submission the submission object
     */
    private function removePromptAttachments($submission) {
        $assets = $submission->data;
        // Get a list of rewards, then create the submission itself
        $promptRewards = createAssetsArray();
        $promptRewards = mergeAssetsArrays($promptRewards, parseAssetData($assets['rewards']));
        if (isset($submission->prompt_id) && $submission->prompt_id) {
            foreach ($submission->prompt->rewards as $reward) {
                removeAsset($promptRewards, $reward->reward, $reward->quantity);
            }
        }

        return $promptRewards;
    }

    /**
     * Creates character attachments for a submission.
     *
     * @param mixed $submission the submission object
     * @param mixed $data       the data for creating character attachments
     */
    private function createCharacterAttachments($submission, $data) {
        // The character identification comes in both the slug field and as character IDs
        // that key the reward ID/quantity arrays.
        // We'll need to match characters to the rewards for them.
        // First, check if the characters are accessible to begin with.
        if (isset($data['slug'])) {
            $characters = Character::myo(0)->visible()->whereIn('slug', $data['slug'])->get();
            if (count($characters) != count($data['slug'])) {
                throw new \Exception('One or more of the selected characters do not exist.');
            }
        } else {
            $characters = [];
        }

        // Retrieve all reward IDs for characters
        $currencyIds = [];
        $itemIds = [];
        $tableIds = [];
        if (isset($data['character_currency_id'])) {
            foreach ($data['character_currency_id'] as $c) {
                foreach ($c as $currencyId) {
                    $currencyIds[] = $currencyId;
                }
            } // Non-expanded character rewards
        } elseif (isset($data['character_rewardable_id'])) {
            $data['character_rewardable_id'] = array_map([$this, 'innerNull'], $data['character_rewardable_id']);
            foreach ($data['character_rewardable_id'] as $ckey => $c) {
                foreach ($c as $key => $id) {
                    switch ($data['character_rewardable_type'][$ckey][$key]) {
                        case 'Currency': $currencyIds[] = $id;
                            break;
                        case 'Item': $itemIds[] = $id;
                            break;
                        case 'LootTable': $tableIds[] = $id;
                            break;
                    }
                }
            } // Expanded character rewards
        }
        array_unique($currencyIds);
        array_unique($itemIds);
        array_unique($tableIds);
        $currencies = Currency::whereIn('id', $currencyIds)->where('is_character_owned', 1)->get()->keyBy('id');
        $items = Item::whereIn('id', $itemIds)->get()->keyBy('id');
        $tables = LootTable::whereIn('id', $tableIds)->get()->keyBy('id');

        // Attach characters
        foreach ($characters as $c) {
            // Users might not pass in clean arrays (may contain redundant data) so we need to clean that up
            $assets = $this->processRewards($data + ['character_id' => $c->id, 'currencies' => $currencies, 'items' => $items, 'tables' => $tables], true);

            // Now we have a clean set of assets (redundant data is gone, duplicate entries are merged)
            // so we can attach the character to the submission
            SubmissionCharacter::create([
                'character_id'  => $c->id,
                'submission_id' => $submission->id,
                'data'          => json_encode(getDataReadyAssets($assets)),
            ]);
            if(isset($data['character_is_focus']) && $data['character_is_focus'][$c->id] && $submission->prompt_id) {
                if($prompt->level_req)
                {
                    if(!$c->level || $c->level->current_level < $prompt->level_req) throw new \Exception('One or more characters are not high enough level to enter this prompt');
                }
                foreach($submission->prompt->skills as $skill) {
                    if($skill->skill->parent) {
                        $charaSkill = $c->skills()->where('skill_id', $skill->skill->id)->first();
                        if(!$charaSkill || $charaSkill->level < $skill->parent_level) throw new \Exception("Skill level too low on one or more characters.");
                    }
                    if($skill->skill->prerequisite) {
                        $charaSkill = $c->skills()->where('skill_id', $skill->skill->id)->first();
                        if(!$charaSkill) throw new \Exception("Skill not unlocked on one or more characters.");
                    }
                }
            }
        }

        return true;
    }

    /**
     * Removes attachments from a submission.
     *
     * @param mixed $submission the submission object
     */
    private function removeAttachments($submission) {
        // This occurs when a draft is edited or rejected.

        // Return all added items
        $addonData = $submission->data['user'];
        if (isset($addonData['user_items'])) {
            foreach ($addonData['user_items'] as $userItemId => $quantity) {
                $userItemRow = UserItem::find($userItemId);
                if (!$userItemRow) {
                    throw new \Exception('Cannot return an invalid item. ('.$userItemId.')');
                }
                if ($userItemRow->submission_count < $quantity) {
                    throw new \Exception('Cannot return more items than was held. ('.$userItemId.')');
                }
                $userItemRow->submission_count -= $quantity;
                $userItemRow->save();
            }
        }

        // And currencies
        $currencyManager = new CurrencyManager;
        if (isset($addonData['currencies']) && $addonData['currencies']) {
            foreach ($addonData['currencies'] as $currencyId=>$quantity) {
                $currency = Currency::find($currencyId);
                if (!$currency) {
                    throw new \Exception('Cannot return an invalid currency. ('.$currencyId.')');
                }
                if (!$currencyManager->creditCurrency(null, $submission->user, null, null, $currency, $quantity)) {
                    throw new \Exception('Could not return currency to user. ('.$currencyId.')');
                }
            }
        }
    }
}    
