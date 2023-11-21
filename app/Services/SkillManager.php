<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Notifications;
use Carbon\Carbon;
use App\Models\Character\Character;
use App\Models\Skill\SkillCategory;
use App\Models\Skill\Skill;
use App\Models\Character\CharacterSkill;
use App\Models\Character\CharacterLog;

class SkillManager extends Service
{
/*
    |--------------------------------------------------------------------------
    | Skill Manager
    |--------------------------------------------------------------------------
    |
    | Handles modification of user-owned skills.
    |
    */

    /**
     * Grants skills to multiple characters
     *
     * @param \App\Models\User\User $user
     * @param  array  $characters
     * @param  array  $skills
     * @param  array  $quantities
     * @param  string $type
     * @return bool
     */
    public function grantSkills($data, $staff)
    {
        DB::beginTransaction();

        try {
            foreach($data['quantities'] as $q) {
                if($q == 0) throw new \Exception("All quantities must not be 0.");
            }

            // Process names
            $characters = Character::find($data['character_ids']);
            if(count($characters) != count($data['character_ids'])) throw new \Exception("An invalid character was selected.");

            $keyed_quantities = [];
            array_walk($data['skill_ids'], function($id, $key) use(&$keyed_quantities, $data) {
                if($id != null && !in_array($id, array_keys($keyed_quantities), TRUE)) {
                    $keyed_quantities[$id] = $data['quantities'][$key];
                }
            });

            // Process skils
            $skills = Skill::find($data['skill_ids']);
            if(!count($skills)) throw new \Exception("No valid skill found.");

            foreach($characters as $character) {
                foreach($skills as $key=>$skill) {
                    if($this->creditSkill($staff, $character, $skill, $data['quantities'][$key], 'Staff Grant', isset($data['notes']) ? $data['notes'] + ' - ' : null))
                    {
                        if($character->user) {
                            Notifications::create('SKILL_GRANT', $character->user, [
                                'skill_name' => $skill->name,
                                'skill_quantity' => $data['quantities'][$key],
                                'sender_url' => $staff->url,
                                'sender_name' => $staff->name,
                                'url' => $character->url . '/skill-logs',
                            ]);
                        }
                    }
                    else
                    {
                        throw new \Exception("Failed to credit skills to ".$character->fullname.".");
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Credits skill to a character.
     *
     * @param \App\Models\User\User $user
     * @param  \App\Models\Character\Character  $recipient
     * @param  string                                                 $type
     * @param  array                                                  $data
     * @param  \App\Models\Skill\Skill                                  $skill
     * @param  int                                                    $quantity
     * @return bool
     */
    public function creditSkill($sender, $recipient, $skill, $quantity, $type, $data = null)
    {
        DB::beginTransaction();

        try {
            // check that the character is the right species
            $species_match = false;
            foreach($skill->species as $species) {
                if($species->species_id == $recipient->image->species_id || ($species->is_subtype && $species->species_id == $recipient->image->subtype_id)) {
                    $species_match = true;
                    break;
                }
            }
            if(count($skill->species) && !$species_match) throw new \Exception("This skill is not available to this character's species and/or subtype.");

            $recipient_stack = CharacterSkill::where([
                ['character_id', '=', $recipient->id],
                ['skill_id', '=', $skill->id]
            ])->first();

            if (!$data) $data = '';

            if(!$recipient_stack) {
                $data = $data . 'Received ' . $quantity . ' points for ' . $skill->name . ' skill. Previous: 0, New: ' . $quantity . '.';

                $recipient_stack = CharacterSkill::create(['character_id' => $recipient->id, 'skill_id' => $skill->id, 'level' => $quantity]);
            }
            else {
                $data = $data . 'Received ' . $quantity . ' points for ' . $skill->name . ' skill. Previous: ' . $recipient_stack->level .', New: ' . ($recipient_stack->level + $quantity) . '.';

                $recipient_stack->level += $quantity;
                $recipient_stack->save();
            }

            if($type && !$this->createLog($recipient->id, $sender->id, $type, $data)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * createLog
     */
    public function createLog($recipientId, $senderId, $type, $data)
    {

        return DB::table('character_log')->insert(
            [
                'character_id' => $recipientId,
                'sender_id' => $senderId,
                'log' => 'Skill Awarded (' . $type . ')',
                'log_type' => 'Skill Awarded',
                'data' => $data, // this should be just a string
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}
