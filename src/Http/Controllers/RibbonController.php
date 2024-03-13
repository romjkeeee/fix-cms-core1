<?php

namespace AltSolution\Admin\Http\Controllers;

use AltSolution\Admin\Forms\MenuForm;
use AltSolution\Admin\Forms\MenuItemForm;
use AltSolution\Admin\Models\Menu;
use AltSolution\Admin\Models\MenuItem;
use Illuminate\Http\Request;

class RibbonController extends Controller
{
    public function getIndex()
    {
        $this->authorize('permission', 'menu.list');

        //$menus = Menu::query()
          //  ->paginate(config('admin.item_per_page', 20));


        $this->layout
            ->setActiveSection('ribbon')
            ->setTitle(123);
        return view('admin::ribbons.list', ['menus' => $menus]);
    }

    public function getEdit($id = null)
    {
        $this->authorize('permission', 'menu.edit');

        $menu = null;
        if ($id) {
            $menu = Menu::query()->find($id);
        }

        $form = cms_create_form(MenuForm::class, $menu);

        $this->layout
            ->setActiveSection('menu')
            ->setTitle(trans($menu ? 'admin::menu.edit' : 'admin::menu.add'))
            ->addBreadcrumb(trans('admin::menu.title'), route('admin::menu_list'));
        return view('admin::menu.edit', compact('form'));
    }

    public function postSave(Request $request)
    {
        $this->authorize('permission', 'menu.edit');

        $this->validate($request, [
            'name' => 'required|max:45',
            'description' => 'required|max:245',
        ]);

        $menu = Menu::query()->firstOrNew(['id' => $request->id]);
        $menu->fill($request->all());
        $menu->save();

        $this->layout->addNotify('success', trans('admin::menu.saved'));

        if ($request['button_apply']) {
            return redirect()->route('admin::menu_edit', $menu->id);
        }

        return redirect()->route('admin::menu_list');
    }

    public function action(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids');
        if (!$ids) {
            return;
        }

        if ($action == 'delete') {
            $this->authorize('permission', 'menu.delete');

            foreach ($ids as $id) {
                $menu = Menu::query()->find($id);
                if (!$menu) {
                    continue;
                }

                $items = MenuItem::query()->where('menu_id', $menu->id)->get();
                foreach ($items as $item) {
                    $item->delete();
                }

                $menu->delete();
            }
        }
    }

    public function getItems($id = null)
    {
        $this->authorize('permission', 'menuitem.list');

        if ($id) {
            $menu = Menu::query()->find($id);

            if ($menu) {
                $items = $menu->items()->where('parent_id', 0)->orderBy('sort')->get();
            } else {
                return redirect()->route('admin::menu_list');
            }
        } else {
            return redirect()->route('admin::menu_list');
        }

        $this->layout
            ->setActiveSection('menu')
            ->setTitle(trans('admin::menu.items', ['menu' => $menu->description]))
            ->addBreadcrumb(trans('admin::menu.title'), route('admin::menu_list'));
        return view('admin::menu.items', ['items' => $items, 'parentMenu' => $menu]);
    }

    public function getEdititem($id = 0, $item_id = 0)
    {
        $this->authorize('permission', 'menuitem.edit');

        if ($id) {
            $menu = Menu::query()->find($id);
            if (!$menu) {
                return redirect()->route('admin::menu_list');
            }
        } else {
            return redirect()->route('admin::menu_list');
        }

        if ($item_id) {
            $item = MenuItem::query()->find($item_id);
        } else {
            $item = null;
        }

        $parent_items = $menu->items()->parent($item_id)->get();
        $entities = cms_system()->getEntity()->getItems();

        $form = cms_create_form(MenuItemForm::class, $item, [
            'parent_items' => $parent_items,
        ]);

        $data = [
            'parentMenu' => $menu,
            'item' => $item,
            'entities' => $entities,
            //
            'form' => $form,
        ];

        $this->layout
            ->setActiveSection('menu')
            ->setTitle(trans($item ? 'admin::menu.item_edit' : 'admin::menu.item_add'))
            ->addBreadcrumb(trans('admin::menu.title'), route('admin::menu_list'))
            ->addBreadcrumb(trans('admin::menu.items', ['menu' => $menu->description]), route('admin::menu_items', ['id' => $menu->id]));
        return view('admin::menu.edititem', $data);
    }

    public function postSaveitem(Request $request)
    {
        $this->authorize('permission', 'menuitem.edit');

        $validate_data = [];
        $validate_data['menu_id'] = 'required|integer';

        $validate_data['name_' . config('app.locale')] = 'required';

        $this->validate($request, $validate_data);

        $item = MenuItem::query()->firstOrNew(['id' => $request->id]);
        $item->fill($request->all());
        $item->target = $request->target ? $request->target : 0;
        $item->save();

        $this->layout->addNotify('success', trans('admin::menu.item_saved'));

        if ($request['button_apply']) {
            return redirect()->route('admin::menu_edititem', ['id' => $request->menu_id, 'id2' => $item->id]);
        }

        return redirect()->route('admin::menu_items', $request->menu_id);
    }

    public function getDeleteitem($id = null)
    {
        $this->authorize('permission', 'menuitem.delete');

        if ($id) {
            $item = MenuItem::query()->find($id);
            $menuId = $item->menu_id;

            if ($item) {
                $item->delete();
            }
        }

        return redirect()->route('admin::menu_items', $menuId);
    }

    public function postMassitemdelete(Request $request)
    {
        $this->authorize('permission', 'menuitem.delete');

        if ($request->data) {
            foreach ($request->data as $item_id) {
                if ($item_id) {
                    $item = MenuItem::query()->find($item_id);

                    if ($item) {
                        $item->delete();
                    }
                }
            }
        }
    }

    public function postSortitems(Request $request)
    {
        $this->authorize('permission', 'menuitem.edit');

        foreach ($request->data as $parent_id => $items) {
            if ($items && count($items)) {
                foreach ($items as $key => $item_id) {
                    $item = MenuItem::query()->find($item_id);
                    if ($item) {
                        $item->parent_id = $parent_id;
                        $item->sort = $key;
                        $item->save();
                    }
                }
            }
        }
    }
}
