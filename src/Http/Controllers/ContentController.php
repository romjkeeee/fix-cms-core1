<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Helpers\ImagesInterface;
use AltSolution\Admin\Helpers\SeoInterface;
use AltSolution\Admin\Models\Content;
use AltSolution\Admin\Modules\Content\ContentFormInterface;
use AltSolution\Admin\Modules\Content\ContentModelInterface;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function getIndex(Request $request)
    {
        $this->authorize('permission', 'content.list');

        $filter = [
            'q' => $request->input('q'),
            'sort' => $request->input('sort'),
        ];

        $qb = app(ContentModelInterface::class)->query();
        if (!empty($filter['q'])) {
            $qb->where(function ($query) use ($filter) {
                foreach (cms_locales() as $locale) {
                    $query->orWhere('title_' . $locale, 'LIKE', '%' . $filter['q'] . '%');
                    $query->orWhere('content_' . $locale, 'LIKE', '%' . $filter['q'] . '%');
                }
            });
        }
        // TODO: refactor
        if (empty($filter['sort'])) {
            $filter['sort'] = 'abc-asc';
        }
        list($sortBy, $sortDir) = explode('-', $filter['sort']);
        if ($sortBy == 'abc') {
            $locale = config('app.locale');
            $qb->orderBy('title_' . $locale, $sortDir);
        } else {
            $qb->orderBy('id', $sortDir);
        }

        $articles = $qb->paginate(config('admin.item_per_page', 20));
        $articles->addQuery('q', $filter['q']);
        $articles->addQuery('sort', $filter['sort']);

        $this->layout
            ->setActiveSection('content')
            ->setTitle(trans('admin::content.title'));
        return view('admin::content.list', compact('articles', 'filter'));
    }

    public function getEdit($id = null)
    {
        $this->authorize('permission', 'content.edit');

        $content = app(ContentModelInterface::class)->query()->findOrNew($id);
        if (!$id) {
            $content['active'] = true;
        }

        $form = cms_create_form(ContentFormInterface::class, $content);

        $this->layout
            ->setActiveSection('content')
            ->setTitle(trans($content ? 'admin::content.edit' : 'admin::content.add'))
            ->addBreadcrumb(trans('admin::content.title'), route('admin::content_list'));
        return view('admin::content.edit', compact('form', 'content'));
    }

    public function postSave(Request $request, Guard $guard)
    {
        $this->authorize('permission', 'content.edit');

        $itemId = $request['id'];
        $this->validate($request, [
            'title_' . config('app.locale') => 'required',
            'url' => 'required|unique:contents,url,' . $itemId,
        ]);

        /** @var Content $content */
        $content = app(ContentModelInterface::class)->query()->firstOrNew(['id' => $itemId]);
        $content->fill($request->all());
        if ($content instanceof ImagesInterface) {
            $content->imageAllSave($request);
        }
        if (!$content->exists) {
            $content['created'] = time();
        }
        $content['modified'] = time();
        $content['user_id'] = $guard->user()->id;
        $content->save();
        if ($content instanceof SeoInterface) {
            $content->seoSave($request['seo']);
        }

        $this->layout->addNotify('success', trans('admin::content.saved'));

        if ($request['button_apply']) {
            return redirect()->route('admin::content_edit', $content['id']);
        }

        return redirect()->route('admin::content_list');
    }

    public function action(Request $request)
    {
        $this->authorize('permission', 'content.edit');

        $action = $request->input('action');
        $itemIds = $request->input('ids');
        if (!$itemIds) {
            return;
        }
        foreach ($itemIds as $itemId) {
            $item = app(ContentModelInterface::class)->query()->findOrFail($itemId);
            switch ($action) {
                case 'activate':
                    $item->active = true;
                    $item->modified = time();
                    $item->save();
                    break;
                case 'deactivate':
                    $item->active = false;
                    $item->save();
                    break;
                case 'delete':
                    if ($item instanceof ImagesInterface) {
                        $item->imageAllDelete();
                    }
                    $item->delete();
                    if ($item instanceof SeoInterface) {
                        $item->seoDelete();
                    }
                    break;
            }
        }
    }
}
