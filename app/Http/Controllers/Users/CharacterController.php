<?php

namespace App\Http\Controllers\Users;

use DB;
use Route;
use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Character\CharacterTransfer;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\User\UserCurrency;
use App\Models\Character\CharacterCurrency;
use App\Models\User\User;
use App\Services\CharacterManager;
use App\Services\CurrencyManager;
use App\Models\Character\CharacterClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Character Controller
    |--------------------------------------------------------------------------
    |
    | Handles displaying of the user's characters and transfers.
    |
    */

    /**
     * Shows the user's characters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        $characters = Auth::user()->characters()->with('image')->visible()->whereNull('trade_id')->get();

        return view('home.characters', [
            'characters' => $characters,
        ]);
    }

    /**
     * Shows the user's MYO slots.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyos() {
        $slots = Auth::user()->myoSlots()->with('image')->get();

        return view('home.myos', [
            'slots' => $slots,
        ]);
    }

    /**
     * Sorts the user's characters.
     *
     * @param App\Services\CharacterManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortCharacters(Request $request, CharacterManager $service) {
        if ($service->sortCharacters($request->only(['sort']), Auth::user())) {
            flash('Characters sorted successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Sorts the characters pets
     */
    public function postSortCharacterPets(CharacterManager $service, Request $request, $slug) {
        if ($service->sortCharacterPets($request->only(['sort']), Auth::user())) {
            flash('Pets sorted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the user's transfers.
     *
     * @param string $type
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransfers($type = 'incoming') {
        $transfers = CharacterTransfer::with('sender.rank')->with('recipient.rank')->with('character.image');
        $user = Auth::user();

        switch ($type) {
            case 'incoming':
                $transfers->where('recipient_id', $user->id)->active();
                break;
            case 'outgoing':
                $transfers->where('sender_id', $user->id)->active();
                break;
            case 'completed':
                $transfers->where(function ($query) use ($user) {
                    $query->where('recipient_id', $user->id)->orWhere('sender_id', $user->id);
                })->completed();
                break;
        }

        return view('home.character_transfers', [
            'transfers'      => $transfers->orderBy('id', 'DESC')->paginate(20),
            'transfersQueue' => Settings::get('open_transfers_queue'),
        ]);
    }

    /**
     * Transfers one of the user's own characters.
     *
     * @param App\Services\CharacterManager $service
     * @param int                           $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postHandleTransfer(Request $request, CharacterManager $service, $id) {
        if (!Auth::check()) {
            abort(404);
        }

        $action = $request->get('action');

        if ($action == 'Cancel' && $service->cancelTransfer(['transfer_id' => $id], Auth::user())) {
            flash('Transfer cancelled.')->success();
        } elseif ($service->processTransfer($request->only(['action']) + ['transfer_id' => $id], Auth::user())) {
            if (strtolower($action) == 'approve') {
                flash('Transfer '.strtolower($action).'d.')->success();
            } else {
                flash('Transfer '.strtolower($action).'ed.')->success();
            }
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /************************************************************************************
     * CLAYMORE
     ************************************************************************************/

    /**
     * Changes / assigns the character class
     * @param  \Illuminate\Http\Request       $request
     * @param  int                            $id
     * @param App\Services\CharacterManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getClassModal($id)
    {
        $this->character = Character::find($id);
        if(!$this->character) abort(404);
        return view('admin.claymores.classes._modal', [
            'classes' => ['none' => 'No Class'] + CharacterClass::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
            'character' => $this->character
        ]);
    }

    public function postClassModal($id, Request $request, CharacterManager $service)
    {
        $this->character = Character::find($id);
        if(!$this->character) abort(404);
        if($service->editClass($request->only(['class_id']), $this->character, Auth::user())) {
            flash('Character class edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
