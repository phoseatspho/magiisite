<?php

namespace App\Models\Border;

use Config;
use App\Models\Model;
use App\Models\Border\BorderCategory;
use Auth;
use App\Models\User\User;

class Border extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'is_default', 'border_category_id','is_active','border_style','admin_only'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'borders';

    protected $appends = ['image_url'];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:borders|between:3,100',
        'image' => 'required|mimes:jpeg,jpg,gif,png',
        'border_style' => 'required',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'image' => 'nullable|mimes:jpeg,jpg,gif,png',
        'border_style' => 'required',
    ];

    /**********************************************************************************************
        SCOPES
    **********************************************************************************************/

    /**
     * Scope a query to sort borders in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort borders in category order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        if(BorderCategory::all()->count()) return $query->orderBy(BorderCategory::select('sort')->whereColumn('borders.border_category_id', 'border_categories.id'), 'DESC');
        return $query;
    }

    /**
     * Scope a query to sort borders by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort borders oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to show only active borders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $withHidden = 0)
    {
        if ($withHidden) {
            return $query;
        }
        return $query->where('is_active', 1);
    }




    /**********************************************************************************************
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the category the border belongs to.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Border\BorderCategory', 'border_category_id');
    }


    /**********************************************************************************************
        ACCESSORS
    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-item">'.$this->name.'</a>';
    }

    
    /**
     * get the name if default or not
     *
     * @return string
     */
    public function getSettingsNameAttribute()
    {
        if($this->admin_only) return $this->name . ' (Staff)';
        if($this->is_default) return $this->name . ' (Default)';
        else return $this->name ;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/borders';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }


    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/borders?name='.$this->name);
    }

        /**
     * Gets the URL of the individual item's page, by ID.
     *
     * @return string
     */
    public function getIdUrlAttribute()
    {
        return url('world/borders/'.$this->id);
    }

    /**
     * Gets the border's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'borders';
    }

    /**
     * get the preview for the border
     *
     */
    public function preview($id = null)
    {
        //we will preview the border on various site pages for purposes of fun :}
        //and so people can "test" their look without having to unlock one
        //if we pass $id, return the avatar of that user
       
        if($id){
            $user = User::find($id)->avatarUrl;

        //else, check if logged in
        //if logged in, return the user avatar to preview
        }elseif(Auth::check()) {
            $user = Auth::user()->avatarUrl;
            
            //finally if not either of these, return default avatar
        } else {
            $user = url('images/avatars/default.jpg');
        }
        //basically just an ugly ass string of html for copypasting use
        //would you want to keep posting this everywhere? yeah i thought so. me neither
        //there's probably a less hellish way to do this but it beats having to paste this over everywhere... EVERY SINGLE TIME.
        //especially with the checks
        //first check the frame style

        //under style
        if ($this->border_style) {
            return '<div style="width:125px; height:125px;border-radius:50%;">
                    <!-- avatar -->
                    <img class="avatar" src="' .
                $user .
                '" style="position: absolute; border-radius:50%; width:125px; height:125px;">
                    <!-- frame -->
                    <img src="' .
                $this->imageUrl .
                '" style="position: absolute;width:125px; height:125px;"  alt="avatar frame"></div>';

        //then over style
        } else {
            return '<div style="width:125px; height:125px;border-radius:50%;">
                    <!-- frame -->
                    <img src="' .
                $this->imageUrl .
                '" style="position: absolute;width:125px; height:125px;"  alt="avatar frame">
                    <!-- avatar -->
                    <img class="avatar" src="' .
                $user .
                '" style="position: absolute; border-radius:50%; width:125px; height:125px;"></div>';
        }
    }

}