<?php

namespace App\Services;

use App\Models\Carousel\Carousel;
use Illuminate\Support\Facades\DB;
use Log;

class CarouselManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Carousel Manager
    |--------------------------------------------------------------------------
    |
    | Handles uploading and manipulation of carousel files.
    |
    */

    /**
     * Uploads a file.
     *
     * @param mixed $data
     * @param mixed $user
     *
     * @return bool
     */
    public function createCarousel($data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }

            $data['image'] = $image->getClientOriginalName();

            $carousel = Carousel::create($data);

            if (!$this->logAdminAction($user, 'Created carousel', 'Created '.$carousel->link)) {
                throw new \Exception('Failed to log admin action.');
            }

            Log::info($carousel->imagePath);
            if ($image) {
                $this->handleImage($image, $carousel->imagePath, $image->getClientOriginalName(), null);
            }

            return $this->commitReturn($carousel);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a file.
     *
     * @param mixed $carousel
     * @param mixed $user
     *
     * @return bool
     */
    public function deleteCarousel($carousel, $user) {
        DB::beginTransaction();

        try {
            $this->deleteImage($carousel->imagePath, $carousel->imageFileName);
            $carousel->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
