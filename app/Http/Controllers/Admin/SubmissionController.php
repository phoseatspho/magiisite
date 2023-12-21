<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Element\Element;
use App\Models\Item\Item;
use App\Models\Loot\LootTable;
use App\Models\Prompt\PromptCategory;
use App\Models\Raffle\Raffle;
use App\Models\Submission\Submission;
use App\Services\SubmissionManager;
use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Models\Item\ItemCategory;
use App\Models\Currency\Currency;
use App\Models\Pet\Pet;
use App\Models\Skill\Skill;
use App\Models\Claymore\Gear;
use App\Models\Claymore\Weapon;
use App\Models\Recipe\Recipe;

class SubmissionController extends Controller {
    /**
     * Shows the submission index page.
     *
     * @param string $status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmissionIndex(Request $request, $status = null) {
        $submissions = Submission::with('prompt')->where('status', $status ? ucfirst($status) : 'Pending')->whereNotNull('prompt_id');
        $data = $request->only(['prompt_category_id', 'sort']);
        if (isset($data['prompt_category_id']) && $data['prompt_category_id'] != 'none') {
            $submissions->whereHas('prompt', function ($query) use ($data) {
                $query->where('prompt_category_id', $data['prompt_category_id']);
            });
        }
        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'newest':
                    $submissions->sortNewest();
                    break;
                case 'oldest':
                    $submissions->sortOldest();
                    break;
            }
        } else {
            $submissions->sortOldest();
        }

        return view('admin.submissions.index', [
            'submissions' => $submissions->paginate(30)->appends($request->query()),
            'categories'  => ['none' => 'Any Category'] + PromptCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'isClaims'    => false,
        ]);
    }

    /**
     * Shows the submission detail page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmission($id) {
        $submission = Submission::whereNotNull('prompt_id')->where('id', $id)->first();
        $inventory = isset($submission->data['user']) ? parseAssetData($submission->data['user']) : null;
        if (!$submission) {
            abort(404);
        }

        return view('admin.submissions.submission', [
            'submission'       => $submission,
            'awardsrow' => Award::all()->keyBy('id'),
            'inventory'        => $inventory,
            'rewardsData'      => isset($submission->data['rewards']) ? parseAssetData($submission->data['rewards']) : null,
            'itemsrow'         => Item::all()->keyBy('id'),
            'page'             => 'submission',
            'expanded_rewards' => Config::get('lorekeeper.extensions.character_reward_expansion.expanded'),
            'characters'       => Character::visible(Auth::check() ? Auth::user() : null)->myo(0)->orderBy('slug', 'DESC')->get()->pluck('fullName', 'slug')->toArray(),
        ] + ($submission->status == 'Pending' ? [
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'items'               => Item::orderBy('name')->pluck('name', 'id'),
            'awards' => Award::orderBy('name')->released()->where('is_user_owned',1)->pluck('name', 'id'),
            'characterAwards' => Award::orderBy('name')->released()->where('is_character_owned',1)->pluck('name', 'id'),
            'pets' => Pet::orderBy('name')->pluck('name', 'id'),
            'gears' => Gear::orderBy('name')->pluck('name', 'id'),
            'weapons' => Weapon::orderBy('name')->pluck('name', 'id'),
            'skills' => Skill::pluck('name', 'id')->toArray(),
            'recipes'=> Recipe::orderBy('name')->pluck('name', 'id'),
            'currencies'          => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables'              => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles'             => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'count'               => Submission::where('prompt_id', $submission->prompt_id)->where('status', 'Approved')->where('user_id', $submission->user_id)->count(),
            'elements'            => Element::orderBy('name')->pluck('name', 'id'),
        ] : []));
    }

    /**
     * Shows the claim index page.
     *
     * @param string $status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClaimIndex(Request $request, $status = null) {
        $submissions = Submission::where('status', $status ? ucfirst($status) : 'Pending')->whereNull('prompt_id');
        $data = $request->only(['sort']);
        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'newest':
                    $submissions->sortNewest();
                    break;
                case 'oldest':
                    $submissions->sortOldest();
                    break;
            }
        } else {
            $submissions->sortOldest();
        }

        return view('admin.submissions.index', [
            'submissions' => $submissions->paginate(30),
            'isClaims'    => true,
        ]);
    }

    /**
     * Shows the claim detail page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClaim($id) {
        $submission = Submission::whereNull('prompt_id')->where('id', $id)->first();
        $inventory = isset($submission->data['user']) ? parseAssetData($submission->data['user']) : null;
        if (!$submission) {
            abort(404);
        }

        return view('admin.submissions.submission', [
            'submission'       => $submission,
            'awardsrow' => Award::all()->keyBy('id'),
            'inventory'        => $inventory,
            'itemsrow'         => Item::all()->keyBy('id'),
            'expanded_rewards' => Config::get('lorekeeper.extensions.character_reward_expansion.expanded'),
            'characters'       => Character::visible(Auth::check() ? Auth::user() : null)->myo(0)->orderBy('slug', 'DESC')->get()->pluck('fullName', 'slug')->toArray(),
        ] + ($submission->status == 'Pending' ? [
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'items'               => Item::orderBy('name')->pluck('name', 'id'),
            'awards' => Award::orderBy('name')->released()->where('is_user_owned',1)->pluck('name', 'id'),
            'characterAwards' => Award::orderBy('name')->released()->where('is_character_owned',1)->pluck('name', 'id'),
            'pets' => Pet::orderBy('name')->pluck('name', 'id'),
            'gears' => Gear::orderBy('name')->pluck('name', 'id'),
            'weapons' => Weapon::orderBy('name')->pluck('name', 'id'),
            'skills' => Skill::pluck('name', 'id')->toArray(),
            'recipes'=> Recipe::orderBy('name')->pluck('name', 'id'),
            'currencies'          => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables'              => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles'             => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'count'               => Submission::where('prompt_id', $id)->where('status', 'Approved')->where('user_id', $submission->user_id)->count(),
            'rewardsData'         => isset($submission->data['rewards']) ? parseAssetData($submission->data['rewards']) : null,
        ] : []));
    }

    /**
     * Creates a new submission.
     *
     * @param App\Services\SubmissionManager $service
     * @param int                            $id
     * @param string                         $action
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSubmission(Request $request, SubmissionManager $service, $id, $action) {
        $data = $request->only(['slug',  'character_rewardable_quantity', 'character_rewardable_id',  'character_rewardable_type', 'character_currency_id', 'rewardable_type', 'rewardable_id', 'quantity', 'staff_comments', 'character_is_focus', 'bonus_exp', 'bonus_points', 'bonus_user_exp', 'bonus_user_points',
        'skill_id', 'skill_quantity']);
        if ($action == 'reject' && $service->rejectSubmission($request->only(['staff_comments']) + ['id' => $id], Auth::user())) {
            flash('Submission rejected successfully.')->success();
        } elseif ($action == 'approve' && $service->approveSubmission($data + ['id' => $id], Auth::user())) {
            flash('Submission approved successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
