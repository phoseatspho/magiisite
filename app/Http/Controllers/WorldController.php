<?php

namespace App\Http\Controllers;

use App\Models\Character\CharacterCategory;
use App\Models\Currency\Currency;
use App\Models\Element\Element;
use App\Models\Feature\Feature;
use App\Models\Feature\FeatureCategory;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Award\AwardCategory;
use App\Models\Award\Award;
use App\Models\Pet\PetCategory;
use App\Models\Pet\Pet;
use App\Models\Skill\SkillCategory;
use App\Models\Skill\Skill;
use App\Models\Rarity;
use App\Models\Shop\Shop;
use App\Models\Shop\ShopStock;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\User\User;
use Config;
use Illuminate\Http\Request;

use App\Models\User\UserAward;

use App\Models\Collection\Collection;
use App\Models\Collection\CollectionCategory;
use App\Models\Level\Level;
use App\Models\Level\CharacterLevel;
use App\Models\Level\UserLevel;
use App\Models\Stat\Stat;

use App\Models\Claymore\WeaponCategory;
use App\Models\Claymore\Weapon;
use App\Models\Claymore\GearCategory;
use App\Models\Claymore\Gear;
use App\Models\Character\CharacterClass;
use Illuminate\Support\Facades\Auth;

use App\Models\Volume\Volume;
use App\Models\Volume\Book;

use App\Models\Recipe\Recipe;
use App\Models\Character\CharacterTransformation as Transformation;

use App\Models\Border\BorderCategory;
use App\Models\Border\Border;

class WorldController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | World Controller
    |--------------------------------------------------------------------------
    |
    | Displays information about the world, as entered in the admin panel.
    | Pages displayed by this controller form the site's encyclopedia.
    |
    */

    /**
     * Shows the index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('world.index');
    }

    /**
     * Shows the currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCurrencies(Request $request) {
        $query = Currency::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('abbreviation', 'LIKE', '%'.$name.'%');
        }

        return view('world.currencies', [
            'currencies' => $query->orderBy('name')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the rarity page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRarities(Request $request) {
        $query = Rarity::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.rarities', [
            'rarities' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the species page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSpecieses(Request $request) {
        $query = Species::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.specieses', [
            'specieses' => $query->with(['subtypes' => function ($query) {
                $query->visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC');
            }])->visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the subtypes page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubtypes(Request $request) {
        $query = Subtype::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.subtypes', [
            'subtypes' => $query->with('species')->visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the item categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemCategories(Request $request) {
        $query = ItemCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.item_categories', [
            'categories' => $query->visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

        /**
     * Shows the award categories page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAwardCategories(Request $request)
    {
        $query = AwardCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.award_categories', [
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the trait categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatureCategories(Request $request) {
        $query = FeatureCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.feature_categories', [
            'categories' => $query->visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the traits page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatures(Request $request) {
        $query = Feature::visible(Auth::check() ? Auth::user() : null)->with('category')->with('rarity')->with('species');
        $data = $request->only(['rarity_id', 'feature_category_id', 'species_id', 'subtype_id', 'name', 'sort']);
        if (isset($data['rarity_id']) && $data['rarity_id'] != 'none') {
            $query->where('rarity_id', $data['rarity_id']);
        }
        if (isset($data['feature_category_id']) && $data['feature_category_id'] != 'none') {
            if ($data['feature_category_id'] == 'withoutOption') {
                $query->whereNull('feature_category_id');
            } else {
                $query->where('feature_category_id', $data['feature_category_id']);
            }
        }
        if (isset($data['species_id']) && $data['species_id'] != 'none') {
            if ($data['species_id'] == 'withoutOption') {
                $query->whereNull('species_id');
            } else {
                $query->where('species_id', $data['species_id']);
            }
        }
        if (isset($data['subtype_id']) && $data['subtype_id'] != 'none') {
            if ($data['subtype_id'] == 'withoutOption') {
                $query->whereNull('subtype_id');
            } else {
                $query->where('subtype_id', $data['subtype_id']);
            }
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'rarity':
                    $query->sortRarity();
                    break;
                case 'rarity-reverse':
                    $query->sortRarity(true);
                    break;
                case 'species':
                    $query->sortSpecies();
                    break;
                case 'subtypes':
                    $query->sortSubtype();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.features', [
            'features'   => $query->paginate(20)->appends($request->query()),
            'rarities'   => ['none' => 'Any Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses'  => ['none' => 'Any Species'] + ['withoutOption' => 'Without Species'] + Species::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes'   => ['none' => 'Any Subtype'] + ['withoutOption' => 'Without Subtype'] + Subtype::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'Any Category'] + ['withoutOption' => 'Without Category'] + FeatureCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows a species' visual trait list.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSpeciesFeatures($id) {
        $categories = FeatureCategory::orderBy('sort', 'DESC')->get();
        $rarities = Rarity::orderBy('sort', 'ASC')->get();
        $species = Species::visible(Auth::check() ? Auth::user() : null)->where('id', $id)->first();
        if (!$species) {
            abort(404);
        }
        if (!config('lorekeeper.extensions.species_trait_index.enable')) {
            abort(404);
        }

        $features = count($categories) ?
            $species->features()
                ->visible(Auth::check() ? Auth::user() : null)
                ->orderByRaw('FIELD(feature_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')
                ->orderByRaw('FIELD(rarity_id,'.implode(',', $rarities->pluck('id')->toArray()).')')
                ->orderBy('has_image', 'DESC')
                ->orderBy('name')
                ->get()
                ->filter(function ($feature) {
                    if ($feature->subtype) {
                        return $feature->subtype->is_visible;
                    }

                    return true;
                })
                ->groupBy(['feature_category_id', 'id']) :
            $species->features()
                ->visible(Auth::check() ? Auth::user() : null)
                ->orderByRaw('FIELD(rarity_id,'.implode(',', $rarities->pluck('id')->toArray()).')')
                ->orderBy('has_image', 'DESC')
                ->orderBy('name')
                ->get()
                ->filter(function ($feature) {
                    if ($feature->subtype) {
                        return $feature->subtype->is_visible;
                    }

                    return true;
                })
                ->groupBy(['feature_category_id', 'id']);

        return view('world.species_features', [
            'species'    => $species,
            'categories' => $categories->keyBy('id'),
            'rarities'   => $rarities->keyBy('id'),
            'features'   => $features,
        ]);
    }

    /**
     * Provides a single trait's description html for use in a modal.
     *
     * @param mixed $speciesId
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSpeciesFeatureDetail($speciesId, $id) {
        $feature = Feature::where('id', $id)->first();

        if (!$feature) {
            abort(404);
        }
        if (!config('lorekeeper.extensions.species_trait_index.trait_modals')) {
            abort(404);
        }

        return view('world._feature_entry', [
            'feature' => $feature,
        ]);
    }

    /**
     * Shows the items page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItems(Request $request) {
        $query = Item::with('category');

        if (!Auth::check() || !Auth::user()->isStaff) {
            $query->released();
        }

        $categoryVisibleCheck = ItemCategory::visible(Auth::check() ? Auth::user() : null)->pluck('id', 'name')->toArray();
        // query where category is visible, or, no category and released
        $query->where(function ($query) use ($categoryVisibleCheck) {
            $query->whereIn('item_category_id', $categoryVisibleCheck)->orWhereNull('item_category_id');
        });
        $data = $request->only(['item_category_id', 'name', 'sort', 'artist']);
        if (isset($data['item_category_id']) && $data['item_category_id'] != 'none') {
            if ($data['item_category_id'] == 'withoutOption') {
                $query->whereNull('item_category_id');
            } else {
                $query->where('item_category_id', $data['item_category_id']);
            }
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }
        if (isset($data['artist']) && $data['artist'] != 'none') {
            $query->where('artist_id', $data['artist']);
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.items', [
            'items'      => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + ['withoutOption' => 'Without Category'] + ItemCategory::visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'shops'      => Shop::orderBy('sort', 'DESC')->get(),
            'artists'    => ['none' => 'Any Artist'] + User::whereIn('id', Item::whereNotNull('artist_id')->pluck('artist_id')->toArray())->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows an individual item's page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItem($id) {
        $categories = ItemCategory::orderBy('sort', 'DESC')->get();

        if (!Auth::check() || !Auth::user()->isStaff) {
            $item = Item::where('id', $id)->released()->first();
        } else {
            $item = Item::where('id', $id)->first();
        }
        if (!$item) {
            abort(404);
        }
        if ($item->category && !$item->category->is_visible) {
            if (Auth::check() ? !Auth::user()->isStaff : true) {
                abort(404);
            }
        }

        return view('world.item_page', [
            'item'        => $item,
            'imageUrl'    => $item->imageUrl,
            'name'        => $item->displayName,
            'description' => $item->parsed_description,
            'categories' => $categories->keyBy('id'),
            'shops' => Shop::where(function($shops) {
                if(Auth::check() && Auth::user()->isStaff) return $shops;
                return $shops->where('is_staff', 0);
            })->whereIn('id', ShopStock::where('item_id', $item->id)->pluck('shop_id')->unique()->toArray())->orderBy('sort', 'DESC')->get()
        ]);
    }

     /**
     * Shows the awards page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAwards(Request $request)
    {
        $query = Award::with('category');
        $data = $request->only(['award_category_id', 'name', 'sort', 'ownership']);
        if(isset($data['award_category_id']) && $data['award_category_id'] != 'none')
            $query->where('award_category_id', $data['award_category_id']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        if(isset($data['ownership']))
        {
            switch($data['ownership']) {
                case 'all':
                    $query->where('is_character_owned',1)->where('is_user_owned',1);
                    break;
                case 'character':
                    $query->where('is_character_owned',1)->where('is_user_owned',0);
                    break;
                case 'user':
                    $query->where('is_character_owned',0)->where('is_user_owned',1);
                    break;
            }
        }

        if(isset($data['sort']))
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        }
        else $query->sortAlphabetical();

        if(!Auth::check() || !Auth::user()->isStaff) $query->released();

        return view('world.awards', [
            'awards' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + AwardCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'shops' => Shop::orderBy('sort', 'DESC')->get()
        ]);
    }


    /**
     * Shows an individual award's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAward($id)
    {
        $categories = AwardCategory::orderBy('sort', 'DESC')->get();
        $award = Award::where('id', $id);
        $released = $award->released()->count();
        if((!Auth::check() || !Auth::user()->isStaff)) $award = $award->released();
        $award = $award->first();
        if(!$award) abort(404);

        if(!$released) flash('This '.__('awards.award').' is not yet released.')->error();


        return view('world.award_page', [
            'award' => $award,
            'imageUrl' => $award->imageUrl,
            'name' => $award->displayName,
            'description' => $award->parsed_description,
            'categories' => $categories->keyBy('id'),
            'shops' => Shop::orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows the character categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterCategories(Request $request) {
        $query = CharacterCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('code', 'LIKE', '%'.$name.'%');
        }

        return view('world.character_categories', [
            'categories' => $query->visible(Auth::check() ? Auth::user() : null)->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the items page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCollections(Request $request)
    {
        $query = Collection::visible();
        $data = $request->only(['name', 'sort', 'collection_category_id']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        if(isset($data['collection_category_id']) && $data['collection_category_id'] != 'none') 
            $query->where('collection_category_id', $data['collection_category_id']);

        if(isset($data['sort']))
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        }
        else $query->sortNewest();

        return view('world.collections.collections', [
            'collections' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + CollectionCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }
    
    /**
     * Shows an individual collection;ss page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCollection($id)
    {
        $collection = Collection::visible()->where('id', $id)->first();
        $categories = CollectionCategory::orderBy('sort', 'DESC')->get();
        if(!$collection) abort(404);

        return view('world.collections._collection_page', [
            'collection' => $collection,
            'imageUrl' => $collection->imageUrl,
            'name' => $collection->displayName,
            'description' => $collection->parsed_description,
        ]);
    }

     /**
     * Shows the collection categories page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCollectionCategories(Request $request)
    {
        $query = CollectionCategory::query();
        $data = $request->only(['name']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('world.collection_categories', [  
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query())
        ]);
    }

    /**
     * 
     *  LEVELS
     * 
     */
    public function getLevels()
    {
        return view('world.level_index');
    }

    /**
     * Level types
     */
    public function getLevelTypes($type)
    {
        if($type == 'user')
        {
            $levels = Level::where('level_type', 'User')->get();
        }
        elseif($type == 'character')
        {
            $levels = Level::where('level_type', 'Character')->get();
        }
        else abort(404);

        return view('world.level_type_index', [
            'levels' => $levels->paginate(20),
            'type' => $type
        ]);
    }

  /**
     * ID view
     */
    public function getSingleLevel($type, $level)
    {
        if($type == 'user')
        {
            $levels = Level::where('level', $level)->where('level_type', 'User')->first();
        }
        elseif($type == 'character')
        {
            $levels = Level::where('level', $level)->where('level_type', 'Character')->first();
        }
        else abort(404);

        return view('world.level_single', [
            'level' => $levels,
            'type' => $type
        ]);
    }

    /**
     * STATS
     */
    public function getStats()
    {
        $stats = Stat::all();

        return view('world.stats', [
            'stats' => $stats,
        ]);
    }

    /**
     * Shows the skill categories page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSkillCategories(Request $request)
    {
        $query = SkillCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.skill_categories', [
            'categories' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the skills page.
     *      
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSkills(Request $request)
    {
        $query = Skill::with('category');
        $data = $request->only(['skill_category_id', 'name', 'sort']);
        if(isset($data['skill_category_id']) && $data['skill_category_id'] != 'none')
            $query->where('skill_category_id', $data['skill_category_id']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        return view('world.skills', [
            'skills' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + SkillCategory::pluck('name', 'id')->toArray(),
        ]);
    }
    
    /**
     * Shows an individual skill's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSkill($id)
    {
        $categories = SkillCategory::get();
        $skill = Skill::where('id', $id)->first();
        if(!$skill) abort(404);

        return view('world.skill_page', [
            'skill' => $skill,
            'imageUrl' => $skill->imageUrl,
            'name' => $skill->displayName,
            'description' => $skill->parsed_description,
            'categories' => $categories->keyBy('id'),
        ]);
    }

   /**
     * Shows the pet categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPetCategories(Request $request) {
        $query = PetCategory::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.pet_categories', [
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the pets page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPets(Request $request) {
        $query = Pet::with('category');
        $data = $request->only(['pet_category_id', 'name', 'sort']);
        if (isset($data['pet_category_id']) && $data['pet_category_id'] != 'none') {
            $query->where('pet_category_id', $data['pet_category_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortCategory();
        }

        return view('world.pets', [
            'pets'       => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + PetCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Gets a specific pet page.
     *
     * @param mixed $id
     */
    public function getPet($id) {
        $pet = Pet::with('category')->findOrFail($id);

        return view('world.pet_page', [
            'pet' => $pet,
        ]);
    } 

    /**
     * Shows the weapon categories page.
     *
     *    
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeaponCategories(Request $request)
    {
        $query = WeaponCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.weapon_categories', [  
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /** 
    * Shows the weapons page.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
   public function getWeapons(Request $request)
   {
       $query = Weapon::with('category');
       $data = $request->only(['weapon_category_id', 'name', 'sort']);
       if(isset($data['weapon_category_id']) && $data['weapon_category_id'] != 'none') 
           $query->where('weapon_category_id', $data['weapon_category_id']);
       if(isset($data['name'])) 
           $query->where('name', 'LIKE', '%'.$data['name'].'%');

       if(isset($data['sort'])) 
       {
           switch($data['sort']) {
               case 'alpha':
                   $query->sortAlphabetical();
                   break;
               case 'alpha-reverse':
                   $query->sortAlphabetical(true);
                   break;
               case 'category':
                   $query->sortCategory();
                   break;
               case 'newest':
                   $query->sortNewest();
                   break;
               case 'oldest':
                   $query->sortOldest();
                   break;
           }
       } 
       else $query->sortCategory();

       return view('world.weapons', [
           'weapons' => $query->paginate(20)->appends($request->query()),
           'categories' => ['none' => 'Any Category'] + WeaponCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
       ]);
    }    

    /**
     * Shows an individual weapon's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeapon($id)
    {
        $categories = WeaponCategory::orderBy('sort', 'DESC')->get();
        $weapon = Weapon::where('id', $id)->first();
        if(!$weapon) abort(404);

        return view('world.weapon_page', [
            'weapon' => $weapon,
            'imageUrl' => $weapon->imageUrl,
            'name' => $weapon->displayName,
            'description' => $weapon->parsed_description,
            'categories' => $categories->keyBy('id'),
        ]);
    }

    /**
     * Shows the gear categories page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGearCategories(Request $request)
    {
        $query = GearCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.gear_categories', [  
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /** 
    * Shows the gears page.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Contracts\Support\Renderable
    */
   public function getGears(Request $request)
   {
       $query = Gear::with('category');
       $data = $request->only(['gear_category_id', 'name', 'sort']);
       if(isset($data['gear_category_id']) && $data['gear_category_id'] != 'none') 
           $query->where('gear_category_id', $data['gear_category_id']);
       if(isset($data['name'])) 
           $query->where('name', 'LIKE', '%'.$data['name'].'%');

       if(isset($data['sort'])) 
       {
           switch($data['sort']) {
               case 'alpha':
                   $query->sortAlphabetical();
                   break;
               case 'alpha-reverse':
                   $query->sortAlphabetical(true);
                   break;
               case 'category':
                   $query->sortCategory();
                   break;
               case 'newest':
                   $query->sortNewest();
                   break;
               case 'oldest':
                   $query->sortOldest();
                   break;
           }
       } 
       else $query->sortCategory();

       return view('world.gears', [
           'gears' => $query->paginate(20)->appends($request->query()),
           'categories' => ['none' => 'Any Category'] + GearCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
       ]);
    }  
    
    /**
     * Shows an individual gear's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGear($id)
    {
        $categories = GearCategory::orderBy('sort', 'DESC')->get();
        $gear = Gear::where('id', $id)->first();
        if(!$gear) abort(404);

        return view('world.gear_page', [
            'gear' => $gear,
            'imageUrl' => $gear->imageUrl,
            'name' => $gear->displayName,
            'description' => $gear->parsed_description,
            'categories' => $categories->keyBy('id'),
        ]);
    }

    /**
     * Shows the character classes page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterClasses(Request $request)
    {
        $query = CharacterClass::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.character_class', [
            'classes' => $query->orderBy('name', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

     /**
     * Shows the items page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRecipes(Request $request)
    {
        $query = Recipe::query();
        $data = $request->only(['name', 'sort']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if(isset($data['sort']))
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
                case 'locked':
                    $query->sortNeedsUnlocking();
                    break;
            }
        }
        else $query->sortNewest();

        return view('world.recipes.recipes', [
            'recipes' => $query->paginate(20)->appends($request->query()),
        ]);
    

     /**
     * Shows an individual recipe;ss page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRecipe($id)
    {
        $recipe = Recipe::where('id', $id)->first();
        if(!$recipe) abort(404);

        return view('world.recipes._recipe_page', [
            'recipe' => $recipe,
            'imageUrl' => $recipe->imageUrl,
            'name' => $recipe->displayName,
            'description' => $recipe->parsed_description,
        ]);

    }


     /**
     * Shows the Transformations page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransformations(Request $request) {
        $query = Transformation::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return view('world.transformations', [
            'transformations' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }


  /**
     * Shows the elements page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getElements(Request $request) {
        $query = Element::query();
        $name = $request->get('name');
        if ($name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } else {
            $query->sortAlphabetical();
        }

        return view('world.elements', [
            'elements' => $query->orderBy('name', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows a single element's page.
     *
     * @param mixed $id
     */
    public function getElement($id) {
        $element = Element::where('id', $id)->first();
        if (!$element) {
            abort(404);
        }

        return view('world.element_page', [
            'element' => $element,
        ]);
    }

     /**
     * Shows the items page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getVolumes(Request $request)
    {
        $query = Volume::visible();
        $data = $request->only(['name', 'sort', 'book_id']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        if(isset($data['book_id']) && $data['book_id'] != 'none') 
            $query->where('book_id', $data['book_id']);

        if(isset($data['sort']))
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        }
        else $query->sortNewest();

        return view('world.volumes.volumes', [
            'volumes' => $query->paginate(20)->appends($request->query()),
            'books' => ['none' => 'Any Book'] + Book::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows an individual volume's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getVolume($id)
    {
        $volume = Volume::visible()->where('id', $id)->first();
        $books = Book::orderBy('name', 'DESC')->get();
        if(!$volume) abort(404);

        return view('world.volumes._volume_page', [
            'volume' => $volume,
            'imageUrl' => $volume->imageUrl,
            'name' => $volume->displayName,
            'description' => $volume->parsed_description,
        ]);
    }

     /**
     * Shows the library page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLibrary(Request $request)
    {
        $query = Book::visible();
        $data = $request->only(['name']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('world.volumes.library', [  
            'books' => $query->orderBy('name', 'DESC')->paginate(20)->appends($request->query())
        ]);
    }

    /**
     * Shows an individual book's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBook($id)
    {
        $book = Book::visible()->where('id', $id)->first();
        if(!$book) abort(404);

        return view('world.volumes._book_page', [
            'book' => $book,
            'imageUrl' => $book->imageUrl,
            'name' => $book->displayName,
            'description' => $book->parsed_description,
        ]);
    }

      /**
     * Shows the border categories page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBorderCategories(Request $request)
    {
        $query = BorderCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.border_categories', [
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the borders page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBorders(Request $request)
    {
        $query = Border::active();
        $data = $request->only(['border_category_id', 'name', 'sort','is_default']);
        if(isset($data['border_category_id']) && $data['border_category_id'] != 'none')
            $query->where('border_category_id', $data['border_category_id']);
        if(isset($data['is_default']) && $data['is_default'] != 'none') 
            $query->where('is_default', $data['is_default'])->where('admin_only', 0);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        if(isset($data['sort']))
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        }
        else $query->sortCategory();

        return view('world.borders', [
            'borders' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + BorderCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'is_default' => ['none' => 'Any Type', '0' => 'Unlockable', '1' => 'Default'],
        ]);
    }

    
        /**
     * Shows an individual border's page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBorder($id)
    {
        $border = Border::where('id', $id)->active()->first();
        if(!$border) abort(404);

        return view('world._border_page', [
            'border' => $border,
            'imageUrl' => $border->imageUrl,
            'name' => $border->displayName,
            'description' => $border->parsed_description,
        ]);
    }


}
