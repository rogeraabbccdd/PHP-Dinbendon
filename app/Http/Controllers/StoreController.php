<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\MenuItem;
use App\Models\GroupOrder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    /**
     * 店家清單.
     */
    public function index(Request $request): Response
    {
        $stores = Store::withCount([
            'groupOrders as ordered_group_orders_count' => function ($query) {
                $query->where('status', 'ordered');
            },
            'groupOrders as course_ordered_group_orders_count' => function ($query) use ($request) {
                $query->where('course_id', $request->user()->course_id)
                    ->where('status', 'ordered');
            }
        ])->get();

        return Inertia::render('stores/Index', [
            'stores' => $stores,
        ]);
    }

    /**
     * 顯示單一店家資訊.
     */
    public function show(Request $request, int $id): Response
    {
        $store = Store::withCount([
            'groupOrders as ordered_group_orders_count' => function ($query) {
                $query->where('status', 'ordered');
            },
            'groupOrders as course_ordered_group_orders_count' => function ($query) use ($request) {
                $query->where('course_id', $request->user()->course_id)
                    ->where('status', 'ordered');
            }
        ])->findOrFail($id);
        $menuItems = $store->menuItems()->where('is_available', true)->get();

        $groupOrders = GroupOrder::where('store_id', $store->id)->where('status', 'open')
            ->orderByDesc('created_at')
            ->get();
        $groupOrders->load('user');
        $groupOrders->makeHidden('menu_snapshot');
        $groupOrders->store = $store;

        return Inertia::render('stores/Show', [
            'store' => $store,
            'menuItems' => $menuItems,
            'groupOrders' => $groupOrders,
        ]);
    }

    /**
     * 顯示單一店家資訊表單.
     */
    public function editForm(Request $request, int $id): Response
    {
        $store = Store::withCount(['groupOrders as ordered_group_orders_count' => function ($query) {
            $query->where('status', 'ordered');
        }])->findOrFail($id);
        $menuItems = $store->menuItems()->get();

        return Inertia::render('stores/Edit', [
            'store' => $store,
            'menuItems' => $menuItems,
        ]);
    }

    public function createForm(Request $request): Response
    {
        return Inertia::render('stores/Edit', [
            'store' => null,
            'menuItems' => null,
        ]);
    }

    /**
     * 更新單一店家資訊.
     */
    public function editFormSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'business_hours' => 'required|string|max:255',
            'delivery_conditions' => 'required|string|max:255',
            'google_map' => 'required|string|max:255',
            'facebook' => 'required|string|max:255',
            'instagram' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'is_closed' => 'required|boolean',
            'menuItems' => 'present|array',
            'menuItems.*.id' => 'nullable|integer',
            'menuItems.*.name' => 'required|string|max:255',
            'menuItems.*.price' => 'required|numeric|min:0',
            'menuItems.*.is_available' => 'required|boolean',
        ]);

        $store = null;
        if ($validated['store']) {
            $store = Store::findOrFail($validated['store']);
        }

        if ($request->hasFile('image')) {
            if ($store) {
                $oldImagePath = $store->getRawOriginal('image');
                if ($oldImagePath && File::exists(public_path($oldImagePath))) {
                    File::delete(public_path($oldImagePath));
                }
            }

            $file = $request->file('image');
            $filename = (string) Str::uuid() . '.' . $file->extension();
            $request->image->move(public_path('storage/store'), $filename);
            $validated['image'] = 'storage/store/' . $filename;
        } else {
            unset($validated['image']);
        }

        $store = Store::updateOrCreate(
            ['id' => $validated['store']],
            $validated
        );

        foreach ($validated['menuItems'] as $menuItemData) {
            MenuItem::updateOrCreate(
                [
                    'id' => $menuItemData['id'],
                    'store_id' => $store->id,
                ],
                $menuItemData
            );
        }

        return redirect()->route('stores.show', $store);
    }
}
