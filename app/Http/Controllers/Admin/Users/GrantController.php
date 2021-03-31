<?php

namespace App\Http\Controllers\Admin\Users;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\User\User;
use App\Models\Item\Item;
use App\Models\Pet\Pet;
use App\Models\Currency\Currency;
use App\Models\Claymore\Gear;
use App\Models\Claymore\Weapon;

use App\Services\CurrencyManager;
use App\Services\InventoryManager;
use App\Services\Stats\ExperienceManager;
use App\Services\PetManager;
use App\Services\Claymore\GearManager;
use App\Services\Claymore\WeaponManager;

use App\Http\Controllers\Controller;

class GrantController extends Controller
{
    /**
     * Show the currency grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCurrency()
    {
        return view('admin.grants.user_currency', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->pluck('name', 'id')
        ]);
    }

    /**
     * Grants or removes currency from multiple users.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\CurrencyManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUserCurrency(Request $request, CurrencyManager $service)
    {
        $data = $request->only(['names', 'currency_id', 'quantity', 'data']);
        if($service->grantUserCurrencies($data, Auth::user())) {
            flash('Currency granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Show the item grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItems()
    {
        return view('admin.grants.items', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /**
     * Grants or removes items from multiple users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postItems(Request $request, InventoryManager $service)
    {
        $data = $request->only(['names', 'item_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantItems($data, Auth::user())) {
            flash('Items granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Grants or removes exp (show)
     */
    public function getExp()
    {
        return view('admin.grants.exp', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'users' => User::orderBy('id')->pluck('name', 'id'),
        ]);
    }
    
    /**
     * Grants or removes exp
     */
    public function postExp(Request $request, ExperienceManager $service)
    {
        $data = $request->only(['names', 'quantity', 'data']);
        if($service->grantExp($data, Auth::user())) {
            flash('EXP granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the pet grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPets()
    {
        return view('admin.grants.pets', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'pets' => Pet::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /** 
     * Grants or removes pets from multiple users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InvenntoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPets(Request $request, PetManager $service)
    {
        $data = $request->only(['names', 'pet_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantPets($data, Auth::user())) {
            flash('Pets granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the pet grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGear()
    {
        return view('admin.grants.gear', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'gears' => Gear::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /** 
     * Grants or removes gear from multiple users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InvenntoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGear(Request $request, GearManager $service)
    {
        $data = $request->only(['names', 'gear_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantGears($data, Auth::user())) {
            flash('Gear granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the pet grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWeapons()
    {
        return view('admin.grants.weapons', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'weapons' => Weapon::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /** 
     * Grants or removes gear from multiple users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InvenntoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postWeapons(Request $request, WeaponManager $service)
    {
        $data = $request->only(['names', 'weapon_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantWeapons($data, Auth::user())) {
            flash('Weapons granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
