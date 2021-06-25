<?php

namespace App\Models\Prompt;

use Config;
use App\Models\Model;

class PromptReward extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prompt_id', 'skill_id', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prompt_skills';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'skill_id' => 'required',
        'quantity' => 'required|integer|min:1',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'skill_id' => 'required',
        'quantity' => 'required|integer|min:1',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the reward attached to the prompt reward.
     */
    public function skill() 
    {
        $this->belongsTo('App\Models\Skill');
    }
}
