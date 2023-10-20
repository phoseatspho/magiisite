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

            // create the typing
            $typing = Typing::create([
                'typing_model' => $typing_model,
                'typing_id'    => $typing_id,
                'element_ids'  => $element_ids,
            ]);

            // log the action
            // if (!$this->logAdminAction($user, 'Created Typing', 'Created '. $typing->object->displayName . ' typing')) {
            //     throw new \Exception('Failed to log admin action.');
            // }

            return $this->commitReturn($typing);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
