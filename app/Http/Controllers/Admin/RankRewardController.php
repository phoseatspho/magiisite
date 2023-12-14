<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Rank\Rank;

use App\Models\RankReward;

use App\Services\RankRewardService;

use App\Http\Controllers\Controller;

class RankRewardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Rank Reward Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of rank rewards.
    |
    */

    /**
     * Shows the rank reward index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.rank_rewards.rank_rewards', [
            'rankrewards' => RankReward::paginate(30),
        ]);
    }

    /**
     * Shows the create rank reward page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRankReward()
    {
        return view('admin.rank_rewards.create_edit_rank_reward', [
            'rankreward' => new RankReward(),
            'ranks' => Rank::orderBy('sort', 'DESC')
                ->pluck('name', 'id')
                ->toArray(),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)
                ->orderBy('name')
                ->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)
                ->where('is_active', 1)
                ->orderBy('name')
                ->pluck('name', 'id'),
        ]);
    }

    /**
     * Shows the edit rank reward page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRankReward($id)
    {
        $rankreward = RankReward::find($id);
        if (!$rankreward) {
            abort(404);
        }
        return view('admin.rank_rewards.create_edit_rank_reward', [
            'rankreward' => $rankreward,
            'ranks' => Rank::orderBy('sort', 'DESC')
                ->pluck('name', 'id')
                ->toArray(),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)
                ->orderBy('name')
                ->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)
                ->where('is_active', 1)
                ->orderBy('name')
                ->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a rank reward.
     *
     * @param  \Illuminate\Http\Request               $request
     * @param  App\Services\CharacterCategoryService  $service
     * @param  int|null                               $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditRankReward(Request $request, RankRewardService $service, $id = null)
    {
        $request->validate(RankReward::$rules);
        $data = $request->only(['name','rank_id', 'data', 'is_active', 'reward_time', 'rewardable_type', 'rewardable_id', 'quantity']);
        if ($id && $service->updateRankReward(RankReward::find($id), $data, Auth::user())) {
            flash('Rank reward updated successfully.')->success();
        } elseif (!$id && ($rankreward = $service->createRankReward($data, Auth::user()))) {
            flash('Rank reward created successfully.')->success();
            return redirect()->to('admin/rank-rewards/edit/' . $rankreward->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }
        return redirect()->back();
    }

    /**
     * Gets the rank reward deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRankReward($id)
    {
        $rankreward = RankReward::find($id);
        return view('admin.rank_rewards._delete_rank_reward', [
            'rankreward' => $rankreward,
        ]);
    }

    /**
     * Deletes a rank reward.
     *
     * @param  \Illuminate\Http\Request               $request
     * @param  App\Services\CharacterCategoryService  $service
     * @param  int                                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRankReward(Request $request, RankRewardService $service, $id)
    {
        if ($id && $service->deleteRankReward(RankReward::find($id))) {
            flash('Rank reward deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }
        return redirect()->to('admin/rank-rewards');
    }
}
