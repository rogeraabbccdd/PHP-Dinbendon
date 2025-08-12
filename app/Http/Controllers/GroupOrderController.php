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
        $store = Store::findOrFail($groupOrder->store_id);
        if ($store->is_closed) {
            return redirect()->route('stores.show', $store->id);
        }

        $groupOrder = new GroupOrder();
        $groupOrder->store_id = $request->input('store_id');
        $groupOrder->course_id = $request->user()->course_id;
        $groupOrder->user_id = $request->user()->id;
        $groupOrder->status = 'open';

        $menuItems = MenuItem::where('store_id', $store->id)->where('is_available', 1)->get();
        $groupOrder->menu_snapshot = $menuItems->toArray();

        $groupOrder->save();

        return redirect()->intended(route('groupOrders.show', $groupOrder->id));
    }

    /**
     * 更新團購狀態.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $groupOrder = GroupOrder::findOrFail($id);

        if ($groupOrder->user_id !== $request->user()->id) {
            return redirect()->intended(route('groupOrders.show', $groupOrder->id));
        }

        $groupOrder->status = $request->input('status');
        $groupOrder->save();

        return redirect()->intended(route('groupOrders.show', $groupOrder->id));
    }

    /**
     * 顯示單筆團購.
     */
    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $groupOrder = GroupOrder::findOrFail($id);
        $groupOrder->load('store', 'user');

        // 若 course_id 不同則導向 stores 頁面
        if ($groupOrder->course_id !== $request->user()->course_id) {
            return redirect()->route('groupOrders');
        }

        $orders = Order::where('group_order_id', $groupOrder->id)
            ->with(['user', 'orderItems'])
            ->get()
            ->sortBy(function ($order) {
                return $order->user->seat_number;
            })
            ->values();

        $myOrder = $orders->firstWhere('user_id', $request->user()->id);

        return Inertia::render('groupOrders/Show', [
            'groupOrder' => $groupOrder,
            'orders' => $orders,
            'myOrder' => $myOrder,
        ]);
    }

    /**
     * 顯示團購內訂單表單.
     */
    public function showOrderForm(Request $request, int $id): Response|RedirectResponse
    {
        $groupOrder = GroupOrder::findOrFail($id);
        $groupOrder->load('store', 'user');

        // 若 course_id 不同則導向 stores 頁面
        if ($groupOrder->course_id !== $request->user()->course_id) {
            return redirect()->route('groupOrders');
        }

        // 查詢目前使用者的訂單
        $myOrder = Order::where('group_order_id', $groupOrder->id)
            ->where('user_id', $request->user()->id)
            ->with('orderItems')
            ->first();

        return Inertia::render('groupOrders/Order', [
            'groupOrder' => $groupOrder,
            'myOrder' => $myOrder,
        ]);
    }

    /**
     * 建立團購內訂單.
     */
    public function createOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group_order_id' => 'required|exists:group_orders,id',
            'total_price' => 'required|integer|min:1',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.comment' => 'nullable|string|max:50',
        ]);

        $groupOrder = GroupOrder::with('store')->findOrFail($validated['group_order_id']);
        if ($groupOrder->store->is_closed) {
            return redirect()->intended(route('groupOrders.show', $order->group_order_id));
        }

        $order = null;
        \DB::transaction(function () use ($validated, $request, &$order) {
            $order = Order::where('group_order_id', $validated['group_order_id'])
                ->where('user_id', $request->user()->id)
                ->first();

            if ($order) {
                // 已下訂，先刪除舊的 orderItems
                $order->orderItems()->delete();
                $order->total_price = $validated['total_price'];
                $order->save();
            } else {
                // 尚未下訂，建立新訂單
                $order = new Order();
                $order->group_order_id = $validated['group_order_id'];
                $order->user_id = $request->user()->id;
                $order->total_price = $validated['total_price'];
                $order->save();
            }

            $orderItems = [];
            foreach ($validated['items'] as $item) {
                $orderItems[] = new OrderItem([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'comment' => $item['comment'] ?? '',
                ]);
            }
            $order->orderItems()->saveMany($orderItems);
        });

        return redirect()->intended(route('groupOrders.show', $order->group_order_id));
    }

    /**
     * 顯示所有團購.
     */
    public function index(Request $request): Response
    {
        $groupOrders = GroupOrder::where('course_id', $request->user()->course_id)
            ->orderByDesc('created_at')
            ->get();
        $groupOrders->load(['store' => function ($query) use ($request) {
            $query->withCount(['groupOrders as course_ordered_group_orders_count' => function ($subQuery) use ($request) {
                $subQuery->where('course_id', $request->user()->course_id)
                    ->where('status', 'ordered');
            }]);
        }, 'user']);
        $groupOrders->makeHidden('menu_snapshot');


        return Inertia::render('groupOrders/Index', [
            'groupOrders' => $groupOrders,
        ]);
    }
}
