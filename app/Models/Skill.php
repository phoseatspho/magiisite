<?php

namespace App\Models;

use Config;
use App\Models\Model;

class Rarity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parent_id', 'prerequisite_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'skills';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:rarities|between:3,100',
        'description' => 'nullable',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'description' => 'nullable',
    ];

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
   
}
