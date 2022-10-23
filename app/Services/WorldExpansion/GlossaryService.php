<?php namespace App\Services\WorldExpansion;

use App\Models\WorldExpansion\Glossary;
use App\Services\Service;

use DB;

class GlossaryService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Glossary Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of Glossary Terms
    |
    */


    /**
     * Creates a new term.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Event\Category
     */
    public function createTerm($data, $user)
    {

        DB::beginTransaction();

        try {

            $data['is_active'] = isset($data['is_active']) ? 1 : 0;
            if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
            $data['link_type'] = isset($data['attachment_type']) ? $data['attachment_type'][0] : null;
            $data['link_id'] = isset($data['attachment_id']) ? $data['attachment_id'][0] : null;

            $term = Glossary::create($data);

            return $this->commitReturn($term);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a term.
     *
     * @param  \App\Models\Category\Category  $category
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Category\Category
     */
    public function updateTerm($term, $data)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Glossary::where('name', $data['name'])->where('id', '!=', $term->id)->exists()) throw new \Exception("The name has already been taken.");

            $data['is_active'] = isset($data['is_active']) ? 1 : 0;
            $data['link_type'] = isset($data['attachment_type']) ? $data['attachment_type'][0] : null;
            $data['link_id'] = isset($data['attachment_id']) ? $data['attachment_id'][0] : null;
            if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

            
            $term->update($data);

            return $this->commitReturn($term);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }



    /**
     * Deletes a category.
     *
     * @param  \App\Models\Category\Category  $category
     * @return bool
     */
    public function deleteTerm($term)
    {
        DB::beginTransaction();

        try {
            $term->delete();
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
