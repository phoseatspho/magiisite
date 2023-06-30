<?php

namespace App\Models\Species;

use Config;
use App\Models\Model;

class SpeciesLimit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'species_id', 'type', 'type_id', 'is_subtype'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'species_limits';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the type of limit
     */
    public function type()
    {
        switch($this->type) {
            case 'stat':
                return $this->belongsTo('App\Models\Stat\Stat', 'type_id');
            case 'skill':
                return $this->belongsTo('App\Models\Skill\Skill', 'type_id');
        }
    }

}
