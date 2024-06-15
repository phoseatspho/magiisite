<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Carousel\Carousel;
use App\Services\CarouselManager;
use Auth;
use Illuminate\Http\Request;

class CarouselController extends Controller {
    /**
     * Shows the files index.
     *
     * @param string $folder
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex($folder = null) {
        $carousels = Carousel::all();

        return view('admin.carousel.index', [
            'carousels' => $carousels,
        ]);
    }

    /**
     * Uploads a site image file.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadCarousel(Request $request, CarouselManager $service) {
        $request->validate(Carousel::$createRules);
        $data = $request->only('image', 'alt_text', 'link');

        if ($service->createCarousel($data, Auth::user())) {
            flash('New carousel section uploaded successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Creates or edits an item.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCarousel(Request $request, CarouselManager $service, $id) {
        if ($id && $service->deleteCarousel(Carousel::find($id), Auth::user())) {
            flash('Carousel deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/carousel');
    }

    public function getDeleteCarousel($id) {
        $carousel = Carousel::find($id);

        return view('admin.carousel._delete_carousel', [
            'carousel' => $carousel,
        ]);
    }
}
