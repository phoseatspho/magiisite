<?php

namespace App\Models\Element;

use App\Models\Model;

class Typing extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'typing_model', 'typing_id', 'element_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'typings';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * get the element of this type
     */
    public function element() {
        return $this->belongsTo('App\Models\Element\Element', 'element_id');
    }

    /**
     * get the object of this type
     */
    public function object() {
        return $this->morphTo('typing_model', 'typing_id');
    }
}
