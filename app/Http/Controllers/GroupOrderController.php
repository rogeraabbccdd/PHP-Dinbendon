<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Store;
use App\Models\MenuItem;
use App\Models\GroupOrder;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GroupOrderController extends Controller
{
    /**
     * 建立團購.
     */
    public function create(Request $request): RedirectResponse
    {
        $groupOrder = new GroupOrder();
        $groupOrder->store_id = $request->input('store_id');
        $groupOrder->course_id = $request->user()->course_id;
        $groupOrder->user_id = $request->user()->id;
        $groupOrder->status = 'open';

        $store = Store::findOrFail($groupOrder->store_id);
        $menuItems = MenuItem::where('store_id', $store->id)->where('is_available', 1)->get();
        $groupOrder->menu_snapshot = $menuItems->toArray();

        $groupOrder->save();

        return redirect()->intended(route('groupOrders.order', $groupOrder->id));
    }
}
