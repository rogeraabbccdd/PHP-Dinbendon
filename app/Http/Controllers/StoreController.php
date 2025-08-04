<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\MenuItem;
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
        $stores = Store::all();

        return Inertia::render('stores/Index', [
            'stores' => $stores,
        ]);
    }

    /**
     * 顯示單一店家資訊.
     */
    public function show(Request $request, int $id): Response
    {
        $store = Store::findOrFail($id);
        $menuItems = $store->menuItems()->where('is_available', true)->get();
        return Inertia::render('stores/Show', [
            'store' => $store,
            'menuItems' => $menuItems,
        ]);
    }
}
