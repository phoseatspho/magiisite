<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;
use App\Models\PrizeCode;
use App\Services\PrizeCodeService;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Currency\Currency;
use App\Models\User\UserPrizeLog;

use App\Http\Controllers\Controller;

class PrizeCodeController extends Controller
{
    /**
     * Shows the prize key index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.prize_codes.prize_codes', [
            'prizes' => PrizeCode::orderBy('id', 'DESC')->paginate(20)
        ]);
    }

     /**
     * Shows the create prize page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePrize()
    {
        return view('admin.prize_codes.create_edit_prize_code', [
            'prize' => new PrizeCode,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'), 
        ]);
    }

    /**
     * Shows the edit prize page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPrize($id)
    {
        $prize = PrizeCode::find($id);
        if(!$prize) abort(404);
        return view('admin.prize_codes.create_edit_prize_code', [
            'prize' => $prize,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'), 
            'redeemers' =>  $prize->redeemers,
        ]);
    }

    /**
     * Creates or edits a prize.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PrizeCodeService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPrize(Request $request, PrizeCodeService $service, $id = null)
    {
        
        $data = $request->only([
            'name','code', 'description', 'image', 'remove_image', 'start_at', 'end_at', 'is_active',
            'rewardable_type', 'rewardable_id', 'reward_quantity', 'use_limit' 
        ]);
        if($id && $service->updatePrize(PrizeCode::find($id), $data, Auth::user())) {
            flash('Prize updated successfully.')->success();
        }
        else if (!$id && $prize = $service->createPrize($data, Auth::user())) {
            flash('Prize created successfully.')->success();
            return redirect()->to('admin/prizecodes/edit/'.$prize->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Generates a new prize key.
     *
     * @param  App\Services\PrizeCodeService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGenerateKey(PrizeCodeService $service)
    {
        if($service->generatePrize(Auth::user())) {
            flash('Generated prize successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Generates a new prize key.
     *
     * @param  App\Services\PrizeCodeService  $service
     * @param  int                             $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteKey(PrizeCodeService $service, $id)
    {
        $prize = PrizeCode::find($id);
        if($prize && $service->deletePrize($prize)) {
            flash('Deleted prize key successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
