<?php

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
|
| Routes for logged in users with a linked dA account.
|
*/

/**************************************************************************************************
    Users
**************************************************************************************************/

Route::group(['prefix' => 'notifications', 'namespace' => 'Users'], function () {
    Route::get('/', 'AccountController@getNotifications');
    Route::get('delete/{id}', 'AccountController@getDeleteNotification');
    Route::post('clear', 'AccountController@postClearNotifications');
    Route::post('clear/{type}', 'AccountController@postClearNotifications');
});

Route::group(['prefix' => 'account', 'namespace' => 'Users'], function () {
    Route::get('settings', 'AccountController@getSettings');
    Route::post('profile', 'AccountController@postProfile');
    Route::post('staff-profile', 'AccountController@postStaffProfile');
    Route::post('staff-links', 'AccountController@postStaffLinks');
    Route::post('password', 'AccountController@postPassword');
    Route::post('email', 'AccountController@postEmail');
    Route::post('location', 'AccountController@postLocation');
    Route::post('faction', 'AccountController@postFaction');
    Route::post('avatar', 'AccountController@postAvatar');
    Route::post('username', 'AccountController@postUsername');
    Route::get('aliases', 'AccountController@getAliases');
    Route::get('make-primary/{id}', 'AccountController@getMakePrimary');
    Route::post('make-primary/{id}', 'AccountController@postMakePrimary');
    Route::get('hide-alias/{id}', 'AccountController@getHideAlias');
    Route::post('hide-alias/{id}', 'AccountController@postHideAlias');
    Route::get('remove-alias/{id}', 'AccountController@getRemoveAlias');
    Route::post('remove-alias/{id}', 'AccountController@postRemoveAlias');
    Route::post('dob', 'AccountController@postBirthday');
    Route::post('border', 'AccountController@postBorder');

    Route::get('two-factor/confirm', 'AccountController@getConfirmTwoFactor');
    Route::post('two-factor/enable', 'AccountController@postEnableTwoFactor');
    Route::post('two-factor/confirm', 'AccountController@postConfirmTwoFactor');
    Route::post('two-factor/disable', 'AccountController@postDisableTwoFactor');

    Route::get('deactivate', 'AccountController@getDeactivate');
    Route::get('deactivate-confirm', 'AccountController@getDeactivateConfirmation');
    Route::post('deactivate', 'AccountController@postDeactivate');

    Route::get('bookmarks', 'BookmarkController@getBookmarks');
    Route::get('bookmarks/create', 'BookmarkController@getCreateBookmark');
    Route::get('bookmarks/edit/{id}', 'BookmarkController@getEditBookmark');
    Route::post('bookmarks/create', 'BookmarkController@postCreateEditBookmark');
    Route::post('bookmarks/edit/{id}', 'BookmarkController@postCreateEditBookmark');
    Route::get('bookmarks/delete/{id}', 'BookmarkController@getDeleteBookmark');
    Route::post('bookmarks/delete/{id}', 'BookmarkController@postDeleteBookmark');
});

Route::group(['prefix' => 'inventory', 'namespace' => 'Users'], function () {
    Route::get('/', 'InventoryController@getIndex');
    Route::post('edit', 'InventoryController@postEdit');
    Route::get('account-search', 'InventoryController@getAccountSearch');
    Route::get('full-inventory', 'InventoryController@getFullInventory');
    Route::get('consolidate-inventory', 'InventoryController@getConsolidateInventory');
    Route::post('consolidate', 'InventoryController@postConsolidateInventory');

    Route::get('selector', 'InventoryController@getSelector');
});

Route::group(['prefix' => __('awards.awardcase'), 'namespace' => 'Users'], function() {
    Route::get('/', 'AwardCaseController@getIndex');
    Route::post('edit', 'AwardCaseController@postEdit');
    Route::post('claim/{id}', 'AwardCaseController@postClaimAward');
    Route::get('selector', 'AwardCaseController@getSelector');
});

Route::group(['prefix' => 'pets', 'namespace' => 'Users'], function () {
    Route::get('/', 'PetController@getIndex');
    Route::post('transfer/{id}', 'PetController@postTransfer');
    Route::post('delete/{id}', 'PetController@postDelete');
    Route::post('name/{id}', 'PetController@postName');
    Route::post('attach/{id}', 'PetController@postAttach');
    Route::post('detach/{id}', 'PetController@postDetach');
    Route::post('variant/{id}', 'PetController@postVariant');
    Route::post('evolution/{id}', 'PetController@postEvolution');

    Route::get('selector', 'PetController@getSelector');
    Route::post('collect/{id}', 'PetController@postClaimPetDrops');
    Route::post('collect-all', 'PetController@postClaimAllPetDrops');
    Route::post('image/{id}', 'PetController@postCustomImage');
    Route::post('description/{id}', 'PetController@postDescription');

    Route::get('view/{id}', 'PetController@getPetPage')->where('id', '[0-9]+');
    Route::post('view/{id}/edit', 'PetController@postEditPetProfile')->where('id', '[0-9]+');

    Route::post('bond/{id}', 'PetController@postBond');
});

Route::group(['prefix' => 'gears', 'namespace' => 'Users'], function() {
    Route::get('/', 'GearController@getIndex');
    Route::post('transfer/{id}', 'GearController@postTransfer');
    Route::post('delete/{id}', 'GearController@postDelete');
    Route::post('name/{id}', 'GearController@postName');
    Route::post('attach/{id}', 'GearController@postAttach');
    Route::post('detach/{id}', 'GearController@postDetach');
    Route::post('upgrade/{id}', 'GearController@postUpgrade');

    Route::get('selector', 'GearController@getSelector');
});

Route::group(['prefix' => 'weapons', 'namespace' => 'Users'], function() {
    Route::get('/', 'WeaponController@getIndex');
    Route::post('transfer/{id}', 'WeaponController@postTransfer');
    Route::post('delete/{id}', 'WeaponController@postDelete');
    Route::post('name/{id}', 'WeaponController@postName');
    Route::post('attach/{id}', 'WeaponController@postAttach');
    Route::post('detach/{id}', 'WeaponController@postDetach');
    Route::post('upgrade/{id}', 'WeaponController@postUpgrade');
    Route::post('image/{id}', 'WeaponController@postImage');

    Route::get('selector', 'WeaponController@getSelector');
});

Route::group(['prefix' => 'wishlists', 'namespace' => 'Users'], function() {
    Route::get('/', 'WishlistController@getIndex');
    Route::get('create', 'WishlistController@getCreateWishlist');
    Route::get('{id}', 'WishlistController@getWishlist')->where('id', '[0-9]+');
    Route::get('default', 'WishlistController@getWishlist');
    Route::get('edit/{id}', 'WishlistController@getEditWishlist');
    Route::get('delete/{id}', 'WishlistController@getDeleteWishlist');
    Route::post('create', 'WishlistController@postCreateEditWishlist');
    Route::post('edit/{id}', 'WishlistController@postCreateEditWishlist');
    Route::post('delete/{id}', 'WishlistController@postDeleteWishlist');
    Route::post('add/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('item_id', '[0-9]+');
    Route::post('{id}/add/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('id', '[0-9]+')->where('item_id', '[0-9]+');
    Route::post('default/update/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('item_id', '[0-9]+');
    Route::post('{id}/update/{item_id}', 'WishlistController@postCreateEditWishlistItem')->where('id', '[0-9]+')->where('item_id', '[0-9]+');
    Route::post('move/{item_id}', 'WishlistController@postMoveWishlistItem')->where('item_id', '[0-9]+');
    Route::post('{id}/move/{item_id}', 'WishlistController@postMoveWishlistItem')->where('id', '[0-9]+')->where('item_id', '[0-9]+');
});

Route::group(['prefix' => 'characters', 'namespace' => 'Users'], function() {
    Route::get('/', 'CharacterController@getIndex');
    Route::post('sort', 'CharacterController@postSortCharacters');

    Route::post('{slug}/pets/sort', 'CharacterController@postSortCharacterPets');

    Route::get('transfers/{type}', 'CharacterController@getTransfers');
    Route::post('transfer/act/{id}', 'CharacterController@postHandleTransfer');

    Route::get('myos', 'CharacterController@getMyos');

    # CLASS
    Route::get('class/edit/{id}', 'CharacterController@getClassModal');
    Route::post('class/edit/{id}', 'CharacterController@postClassModal');
});

Route::group(['prefix' => 'bank', 'namespace' => 'Users'], function () {
    Route::get('/', 'BankController@getIndex');
    Route::post('transfer', 'BankController@postTransfer');
});

Route::group(['prefix' => 'level', 'namespace' => 'Users'], function() {
    Route::get('/', 'LevelController@getIndex');
    Route::post('up', 'LevelController@postLevel');
    Route::post('transfer', 'LevelController@postTransfer');
});

Route::group(['prefix' => 'trades', 'namespace' => 'Users'], function() {
    Route::get('{status}', 'TradeController@getIndex')->where('status', 'open|pending|completed|rejected|canceled');
    Route::get('create', 'TradeController@getCreateTrade');
    Route::get('{id}/edit', 'TradeController@getEditTrade')->where('id', '[0-9]+');
    Route::post('create', 'TradeController@postCreateTrade');
    Route::post('{id}/edit', 'TradeController@postEditTrade')->where('id', '[0-9]+');
    Route::get('{id}', 'TradeController@getTrade')->where('id', '[0-9]+');

    Route::get('{id}/confirm-offer', 'TradeController@getConfirmOffer');
    Route::post('{id}/confirm-offer', 'TradeController@postConfirmOffer');
    Route::get('{id}/confirm-trade', 'TradeController@getConfirmTrade');
    Route::post('{id}/confirm-trade', 'TradeController@postConfirmTrade');
    Route::get('{id}/cancel-trade', 'TradeController@getCancelTrade');
    Route::post('{id}/cancel-trade', 'TradeController@postCancelTrade');
});

Route::group(['prefix' => 'crafting', 'namespace' => 'Users'], function() {
    Route::get('/', 'CraftingController@getIndex');
    Route::get('craft/{id}', 'CraftingController@getCraftRecipe');
    Route::post('craft/{id}', 'CraftingController@postCraftRecipe');
});

/**************************************************************************************************
    Characters
**************************************************************************************************/
Route::group(['prefix' => 'character', 'namespace' => 'Characters'], function () {
    Route::get('{slug}/profile/edit', 'CharacterController@getEditCharacterProfile');
    Route::post('{slug}/profile/edit', 'CharacterController@postEditCharacterProfile');

    Route::post('{slug}/'.__('awards.awardcase').'/edit', 'CharacterController@postAwardEdit');
    Route::post('{slug}/inventory/edit', 'CharacterController@postInventoryEdit');

    Route::post('{slug}/bank/transfer', 'CharacterController@postCurrencyTransfer');
    Route::get('{slug}/transfer', 'CharacterController@getTransfer');
    Route::post('{slug}/transfer', 'CharacterController@postTransfer');
    Route::post('{slug}/transfer/{id}/cancel', 'CharacterController@postCancelTransfer');

    Route::post('{slug}/approval', 'CharacterController@postCharacterApproval');
    Route::get('{slug}/approval', 'CharacterController@getCharacterApproval');
    Route::get('{slug}/level-area', 'LevelController@getIndex');
    Route::get('{slug}/stats-area', 'LevelController@getStatsIndex');
    Route::post('{slug}/level-area/up', 'LevelController@postLevel');
    Route::post('{slug}/stats-area/{id}', 'LevelController@postStat');
    Route::post('{slug}/stats-area/admin/{id}', 'LevelController@postAdminStat');
    Route::post('{slug}/stats-area/edit/{id}', 'LevelController@postEditStat');
    Route::post('{slug}/stats-area/edit/base/{id}', 'LevelController@postEditBaseStat');
    Route::post('{slug}/approval/{id}', 'CharacterController@postCharacterApprovalSpecificImage');

    # EXP
    Route::post('{slug}/level-area/exp-grant', 'LevelController@postExpGrant');
    Route::post('{slug}/level-area/stat-grant', 'LevelController@postStatGrant');
});
Route::group(['prefix' => 'myo', 'namespace' => 'Characters'], function () {
    Route::get('{id}/profile/edit', 'MyoController@getEditCharacterProfile');
    Route::post('{id}/profile/edit', 'MyoController@postEditCharacterProfile');

    Route::get('{id}/transfer', 'MyoController@getTransfer');
    Route::post('{id}/transfer', 'MyoController@postTransfer');
    Route::post('{id}/transfer/{id2}/cancel', 'MyoController@postCancelTransfer');

    Route::post('{id}/approval', 'MyoController@postCharacterApproval');
    Route::get('{id}/approval', 'MyoController@getCharacterApproval');
    //this is useless but im not sure if we dont include it things will get weird or not
    Route::post('{slug}/approval/{id}', 'CharacterController@postCharacterApprovalSpecificImage');
});

Route::group(['prefix' => 'level', 'namespace' => 'Users'], function() {
    Route::get('/', 'LevelController@getIndex');

});



/**************************************************************************************************
    Submissions
**************************************************************************************************/

Route::group(['prefix' => 'gallery'], function () {
    Route::get('submissions/{type}', 'GalleryController@getUserSubmissions')->where('type', 'draft|pending|accepted|rejected');

    Route::post('favorite/{id}', 'GalleryController@postFavoriteSubmission');

    Route::get('submit/{id}', 'GalleryController@getNewGallerySubmission');
    Route::get('submit/character/{slug}', 'GalleryController@getCharacterInfo');
    Route::get('edit/{id}', 'GalleryController@getEditGallerySubmission');
    Route::get('queue/{id}', 'GalleryController@getSubmissionLog');
    Route::post('queue/totals/{id}', 'GalleryController@postSubmissionTotals');
    Route::post('submit', 'GalleryController@postCreateEditGallerySubmission');
    Route::post('edit/{id}', 'GalleryController@postCreateEditGallerySubmission');

    Route::post('collaborator/{id}', 'GalleryController@postEditCollaborator');

    Route::get('archive/{id}', 'GalleryController@getArchiveSubmission');
    Route::post('archive/{id}', 'GalleryController@postArchiveSubmission');
});

Route::group(['prefix' => 'submissions', 'namespace' => 'Users'], function () {
    Route::get('/', 'SubmissionController@getIndex');
    Route::get('new', 'SubmissionController@getNewSubmission');
    Route::get('new/character/{slug}', 'SubmissionController@getCharacterInfo');
    Route::get('new/prompt/{id}', 'SubmissionController@getPromptInfo');
    Route::post('new', 'SubmissionController@postNewSubmission');
    Route::post('new/{draft}', 'SubmissionController@postNewSubmission')->where('draft', 'draft');
    Route::get('draft/{id}', 'SubmissionController@getEditSubmission');
    Route::post('draft/{id}', 'SubmissionController@postEditSubmission');
    Route::post('draft/{id}/{submit}', 'SubmissionController@postEditSubmission')->where('submit', 'submit');
    Route::post('draft/{id}/delete', 'SubmissionController@postDeleteSubmission');
    Route::post('draft/{id}/cancel', 'SubmissionController@postCancelSubmission');
});

Route::group(['prefix' => 'claims', 'namespace' => 'Users'], function () {
    Route::get('/', 'SubmissionController@getClaimsIndex');
    Route::get('new', 'SubmissionController@getNewClaim');
    Route::post('new', 'SubmissionController@postNewClaim');
    Route::post('new/{draft}', 'SubmissionController@postNewClaim')->where('draft', 'draft');
    Route::get('draft/{id}', 'SubmissionController@getEditClaim');
    Route::post('draft/{id}', 'SubmissionController@postEditClaim');
    Route::post('draft/{id}/{submit}', 'SubmissionController@postEditClaim')->where('submit', 'submit');
    Route::post('draft/{id}/delete', 'SubmissionController@postDeleteClaim');
    Route::post('draft/{id}/cancel', 'SubmissionController@postCancelClaim');
});

Route::group(['prefix' => 'reports', 'namespace' => 'Users'], function () {
    Route::get('/', 'ReportController@getReportsIndex');
    Route::get('new', 'ReportController@getNewReport');
    Route::post('new', 'ReportController@postNewReport');
    Route::get('view/{id}', 'ReportController@getReport');
});

Route::group(['prefix' => 'designs', 'namespace' => 'Characters'], function () {
    Route::get('{type?}', 'DesignController@getDesignUpdateIndex')->where('type', 'draft|pending|approved|rejected');
    Route::get('{id}', 'DesignController@getDesignUpdate');

    Route::get('{id}/comments', 'DesignController@getComments');
    Route::post('{id}/comments', 'DesignController@postComments');

    Route::get('{id}/image', 'DesignController@getImage');
    Route::post('{id}/image', 'DesignController@postImage');

    Route::get('{id}/addons', 'DesignController@getAddons');
    Route::post('{id}/addons', 'DesignController@postAddons');

    Route::get('{id}/traits', 'DesignController@getFeatures');
    Route::post('{id}/traits', 'DesignController@postFeatures');
    Route::get('traits/subtype', 'DesignController@getFeaturesSubtype');

    Route::get('{id}/confirm', 'DesignController@getConfirm');
    Route::post('{id}/submit', 'DesignController@postSubmit');

    Route::get('{id}/delete', 'DesignController@getDelete');
    Route::post('{id}/delete', 'DesignController@postDelete');
});

/**************************************************************************************************
    Shops
**************************************************************************************************/

Route::group(['prefix' => 'shops'], function () {
    Route::post('buy', 'ShopController@postBuy');
    Route::get('history', 'ShopController@getPurchaseHistory');
});


/**************************************************************************************************
    Dailies
**************************************************************************************************/

Route::group(['prefix' => __('dailies.dailies')], function() {
    Route::post('{id}', 'DailyController@postRoll');
});


/**************************************************************************************************
    Activities
 **************************************************************************************************/

Route::group(['prefix' => 'activities'], function () {
    Route::get('/', 'ActivityController@getIndex');
    Route::get('{id}', 'ActivityController@getActivity')->where(['id' => '[0-9]+']);
    Route::post('{id}/act', 'ActivityController@postAct')->where(['id' => '[0-9]+']);
});

/**************************************************************************************************
    Scavenger Hunts
**************************************************************************************************/

Route::group(['prefix' => 'hunts'], function() {
    Route::get('{id}', 'HuntController@getHunt');
    Route::get('targets/{pageId}', 'HuntController@getTarget');
    Route::post('targets/claim', 'HuntController@postClaimTarget');
});

/**************************************************************************************************
    Comments
**************************************************************************************************/
Route::group(['prefix' => 'comments', 'namespace' => 'Comments'], function () {
    Route::post('make/{model}/{id}', 'CommentController@store');
    Route::delete('/{comment}', 'CommentController@destroy')->name('comments.destroy');
    Route::post('edit/{comment}', 'CommentController@update')->name('comments.update');
    Route::post('/{comment}', 'CommentController@reply')->name('comments.reply');
    Route::post('/{id}/feature', 'CommentController@feature')->name('comments.feature');
    Route::post('/{id}/like/{action}', 'CommentController@like')->name('comments.like');
    Route::get('/liked', 'CommentController@getLikedComments');
});

/**************************************************************************************************	
    Foraging
**************************************************************************************************/	
Route::group(['prefix' => 'foraging', 'namespace' => 'Users'], function() {
    Route::get('/', 'ForagingController@getIndex');
    Route::post('/forage/{id}', 'ForagingController@postForage');
    Route::post('/claim', 'ForagingController@postClaim');
    Route::post('edit/character', 'ForagingController@postEditCharacter');
});

/**************************************************************************************************	
   Collections
**************************************************************************************************/	
Route::group(['prefix' => 'collection', 'namespace' => 'Users'], function() {
    Route::get('/', 'CollectionController@getIndex');
    Route::get('complete/{id}', 'CollectionController@getCompleteCollection');
    Route::post('complete/{id}', 'CollectionController@postCompleteCollection');
});
    
    Route::group(['prefix' => 'redeem-code', 'namespace' => 'Users'], function() {
        Route::get('/', 'PrizeCodeController@getIndex');
        Route::post('/redeem', 'PrizeCodeController@postRedeemPrize');
});
/**************************************************************************************************
    Fetch Quests
**************************************************************************************************/
Route::group(['prefix' => 'fetch'], function() {
    Route::get('/', 'FetchQuestController@getIndex');
    Route::post('/new', 'FetchQuestController@postFetchQuest');
});

/**************************************************************************************************
    Criteria
**************************************************************************************************/
Route::group(['prefix' => 'criteria'], function() {
    Route::get('/{entity}/{id}', 'CriterionController@getCriterionSelector')->where('entity', 'prompt|gallery');
    Route::get('{entity}/{id}/{entity_id}/{form_id}', 'CriterionController@getCriterionForm')->where('entity', 'prompt|gallery');
    Route::get('/{id}', 'CriterionController@getCriterionFormLimited');
    Route::post('/rewards/{id}', 'CriterionController@postCriterionRewards');
    
    Route::get('guide/{id}', 'CriterionController@getCriterionGuide');
});