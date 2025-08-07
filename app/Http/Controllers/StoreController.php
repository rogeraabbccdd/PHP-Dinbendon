<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\MenuItem;
use App\Models\GroupOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    /**
     * 店家清單.
     */
    public function index(Request $request): Response
    {
        $stores = Store::withCount(['groupOrders as ordered_group_orders_count' => function ($query) {
            $query->where('status', 'ordered');
        }])->get();

        return Inertia::render('stores/Index', [
            'stores' => $stores,
        ]);
    }

    /**
     * 顯示單一店家資訊.
     */
    public function show(Request $request, int $id): Response
    {
        $store = Store::withCount(['groupOrders as ordered_group_orders_count' => function ($query) {
            $query->where('status', 'ordered');
        }])->findOrFail($id);
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
}
