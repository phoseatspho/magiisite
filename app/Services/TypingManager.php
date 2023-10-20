<?php

namespace App\Services;

use App\Models\Element\Typing;
use DB;
use Auth;

class TypingManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Typng Manager
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of elements on objects
    |
    */

    /**********************************************************************************************

        TYPINGS

    **********************************************************************************************/

    /**
     * Creates a new typing for an object.
     */
    public function createTyping($typing_model, $typing_id, $element_ids = null) {
        DB::beginTransaction();

        try {

            if (!$element_ids) {
                throw new \Exception('No elements provided.');
            }
            // check that there is not more than two element ids
            if (count($element_ids) > 2) {
                throw new \Exception('Too many elements provided.');
            }
            // check that a typing with this model and id doesn't already exist
            if(Typing::where('typing_model', $typing_model)->where('typing_id', $typing_id)->exists()) {
                throw new \Exception('A typing with this model and id already exists.');
            }

            // create the typing
            $typing = Typing::create([
                'typing_model' => $typing_model,
                'typing_id'    => $typing_id,
                'element_ids'  => json_encode($element_ids),
            ]);

            // log the action
            if (!$this->logAdminAction(Auth::user(), 'Created Typing', 'Created '. $typing->object->displayName . ' typing')) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($typing);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * edits an existing typing on a model
     */
    public function editTyping($typing, $element_ids = null) {
        DB::beginTransaction();

        try {

            if (!$element_ids) {
                throw new \Exception('No elements provided.');
            }
            // check that there is not more than two element ids
            if (count($element_ids) > 2) {
                throw new \Exception('Too many elements provided.');
            }
            // check that a typing with this model and id doesn't already exist
            if(Typing::where('typing_model', $typing->typing_model)->where('typing_id', $typing->typing_id)->where('id', '!=', $typing->id)->exists()) {
                throw new \Exception('A typing with this model and id already exists.');
            }

            // create the typing
            $typing->update([
                'element_ids'  => json_encode($element_ids),
            ]);

            // log the action
            if (!$this->logAdminAction(Auth::user(), 'Edited Typing', 'Edited '. $typing->object->displayName . ' typing')) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($typing);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * deletes a typing
     */
    public function deleteTyping($typing) {
        DB::beginTransaction();

        try {

            // delete the typing
            $typing->delete();

            // log the action
            if (!$this->logAdminAction(Auth::user(), 'Deleted Typing', 'Deleted '. $typing->object->displayName . ' typing')) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($typing);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
