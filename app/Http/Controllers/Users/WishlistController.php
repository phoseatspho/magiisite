<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\Wishlist;
use App\Models\User\WishlistItem;
use App\Models\Item\Item;
use App\Services\WishlistManager;

use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Wishlist Controller
    |--------------------------------------------------------------------------
    |
    | Handles wishlist management for the user.
    |
    */

    /**
     * Shows the user's wishlists.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = Auth::user()->wishlists;

        return view('home.wishlists', [
            'wishlists' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows a wishlist's page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWishlist($id)
    {
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!$wishlist) abort(404);

        return view('home.wishlist', [
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Shows the create wishlist modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateWishlist()
    {
        return view('home._create_edit_wishlist', [
            'wishlist' => new Wishlist
        ]);
    }

    /**
     * Shows the edit wishlist modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditWishlist($id)
    {
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!$wishlist) abort(404);

        return view('home._create_edit_wishlist', [
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Creates or edits a wishlist.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\WishlistManager  $service
     * @param  int|null                      $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditWishlist(Request $request, WishlistManager $service, $id = null)
    {
        $data = $request->only(['name']);

        if($id && $service->updateWishlist($data, Wishlist::find($id), Auth::user())) {
            flash('Wishlist updated successfully.')->success();
        }
        else if (!$id && $bookmark = $service->createWishlist($data, Auth::user())) {
            flash('Wishlist created successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the delete wishlist modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteWishlist($id)
    {
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!$wishlist) abort(404);

        return view('home._delete_wishlist', [
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Deletes a wishlist.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\WishlistManager    $service
     * @param  int                             $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteWishlist(Request $request, WishlistManager $service, $id)
    {
        if($id && $service->deleteWishlist(Wishlist::find($id), Auth::user())) {
            flash('Wishlist deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('wishlists');
    }

}
