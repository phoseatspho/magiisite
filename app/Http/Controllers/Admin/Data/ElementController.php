<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Element\Element;
use App\Models\Element\ElementWeakness;
use App\Models\Element\ElementImmunity;
use App\Services\ElementService;
use Auth;
use Illuminate\Http\Request;

class ElementController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Element Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of element categories and elements.
    |
    */

    /**********************************************************************************************

        ELEMENTS

    **********************************************************************************************/

    /**
     * Shows the element index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        $query = Element::query();
        $data = $request->only(['name']);
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        return view('admin.elements.elements', [
            'elements' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create element page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateElement() {
        return view('admin.elements.create_edit_element', [
            'element'  => new Element,
            'elements' => Element::orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit element page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditElement($id) {
        $element = Element::find($id);
        if (!$element) {
            abort(404);
        }

        return view('admin.elements.create_edit_element', [
            'element'  => $element,
            'elements' => Element::where('id', '!=', $element->id)->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits an element.
     *
     * @param App\Services\ElementService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditElement(Request $request, ElementService $service, $id = null) {
        $id ? $request->validate(Element::$updateRules) : $request->validate(Element::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'weakness_id', 'weakness_multiplier', 'immunity_id', 'colour',
        ]);
        if ($id && $service->updateElement(Element::find($id), $data, Auth::user())) {
            flash('Element updated successfully.')->success();
        } elseif (!$id && $element = $service->createElement($data, Auth::user())) {
            flash('Element created successfully.')->success();

            return redirect()->to('admin/data/elements/edit/'.$element->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the element deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteElement($id) {
        $element = Element::find($id);

        return view('admin.elements._delete_element', [
            'element' => $element,
        ]);
    }

    /**
     * Creates or edits an element.
     *
     * @param App\Services\ElementService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteElement(Request $request, ElementService $service, $id) {
        if ($id && $service->deleteElement(Element::find($id), Auth::user())) {
            flash('Element deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/elements');
    }

    /**********************************************************************************************

        ELEMENT TAGS

    **********************************************************************************************/

    /**
     * Gets the tag addition page.
     *
     * @param App\Services\ElementService $service
     * @param int                      $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAddElementTag(ElementService $service, $id) {
        $element = Element::find($id);

        return view('admin.elements.add_tag', [
            'element' => $element,
            'tags' => array_diff($service->getElementTags(), $element->tags()->pluck('tag')->toArray()),
        ]);
    }

    /**
     * Adds a tag to an element.
     *
     * @param App\Services\ElementService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddElementTag(Request $request, ElementService $service, $id) {
        $element = Element::find($id);
        $tag = $request->get('tag');
        if ($tag = $service->addElementTag($element, $tag, Auth::user())) {
            flash('Tag added successfully.')->success();

            return redirect()->to($tag->adminUrl);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the tag editing page.
     *
     * @param int   $id
     * @param mixed $tag
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditElementTag(ElementService $service, $id, $tag) {
        $element = Element::find($id);
        $tag = $element->tags()->where('tag', $tag)->first();
        if (!$element || !$tag) {
            abort(404);
        }

        return view('admin.elements.edit_tag', [
            'element' => $element,
            'tag'  => $tag,
        ] + $tag->getEditData());
    }

    /**
     * Edits tag data for an element.
     *
     * @param App\Services\ElementService $service
     * @param int                      $id
     * @param string                   $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditElementTag(Request $request, ElementService $service, $id, $tag) {
        $element = Element::find($id);
        if ($service->editElementTag($element, $tag, $request->all(), Auth::user())) {
            flash('Tag edited successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the element tag deletion modal.
     *
     * @param int    $id
     * @param string $tag
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteElementTag($id, $tag) {
        $element = Element::find($id);
        $tag = $element->tags()->where('tag', $tag)->first();

        return view('admin.elements._delete_element_tag', [
            'element' => $element,
            'tag'  => $tag,
        ]);
    }

    /**
     * Deletes a tag from an element.
     *
     * @param App\Services\ElementService $service
     * @param int                      $id
     * @param string                   $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteElementTag(Request $request, ElementService $service, $id, $tag) {
        $element = Element::find($id);
        if ($service->deleteElementTag($element, $tag, Auth::user())) {
            flash('Tag deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/elements/edit/'.$element->id);
    }
}
