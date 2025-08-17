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
        $store = Store::with(['menuItems' => function ($query) {
            $query->where('is_available', true);
        }])->findOrFail($request->input('store_id'));

        if ($store->is_closed) {
            return redirect()->route('stores.show', $store->id);
        }

        $groupOrder = GroupOrder::create([
            'store_id' => $store->id,
            'course_id' => $request->user()->course_id,
            'user_id' => $request->user()->id,
            'status' => 'open',
            'menu_snapshot' => $store->menuItems->toArray(),
        ]);

        return redirect()->intended(route('groupOrders.show', $groupOrder->id));
    }

    /**
     * 更新團購狀態.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $updated = GroupOrder::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->update(['status' => $request->input('status')]);

        return redirect()->intended(route('groupOrders.show', $id));
    }

    /**
     * 顯示單筆團購.
     */
    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $groupOrder = GroupOrder::with([
            'store', 'user', 'orders.user', 'orders.orderItems'
        ])->findOrFail($id);

        if ($groupOrder->course_id !== $request->user()->course_id) {
            return redirect()->route('groupOrders');
        }

        $orders = $groupOrder->orders
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
        $userId = $request->user()->id;
        $groupOrder = GroupOrder::with([
            'store',
            'user',
            'orders' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->with('orderItems');
            }
        ])->findOrFail($id);

        // 若 course_id 不同則導向 stores 頁面
        if ($groupOrder->course_id !== $request->user()->course_id) {
            return redirect()->route('groupOrders');
        }

        $myOrder = $groupOrder->orders->first();

        $courseId = $request->user()->course_id;
        $menuItemIds = collect($groupOrder->menu_snapshot)->pluck('id');

        $menuItemsWithCounts = MenuItem::whereIn('id', $menuItemIds)
            ->withSum([
                'orderItems as total_ordered_count' => function ($query) {
                    $query->whereHas('order.groupOrder', fn ($q) => $q->where('status', 'ordered'));
                }
            ], 'quantity')
            ->when($courseId, function ($query) use ($courseId) {
                $query->withSum([
                    'orderItems as course_ordered_count' => function ($query) use ($courseId) {
                        $query->whereHas('order', function ($q) use ($courseId) {
                            $q->whereHas('groupOrder', fn ($subQ) => $subQ->where('status', 'ordered'))
                                ->whereHas('user', fn ($subQ) => $subQ->where('course_id', $courseId));
                        });
                    }
                ], 'quantity');
            })
            ->get()
            ->keyBy('id');

        $menuSnapshotWithCounts = collect($groupOrder->menu_snapshot)->map(function ($item) use ($menuItemsWithCounts, $courseId) {
            $itemWithCount = $menuItemsWithCounts->get($item['id']);
            if ($itemWithCount) {
                $item['total_ordered_count'] = (int) $itemWithCount->total_ordered_count;
                $item['course_ordered_count'] = $courseId ? (int) $itemWithCount->course_ordered_count : 0;
            } else {
                $item['total_ordered_count'] = 0;
                $item['course_ordered_count'] = 0;
            }
            return $item;
        });

        $groupOrder->menu_snapshot = $menuSnapshotWithCounts;

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
            // Bug Fix: 這裡應該使用 $groupOrder->id
            return redirect()->intended(route('groupOrders.show', $groupOrder->id));
        }

        $order = \DB::transaction(function () use ($validated, $request) {
            $order = Order::updateOrCreate(
                [
                    'group_order_id' => $validated['group_order_id'],
                    'user_id'        => $request->user()->id,
                ],
                [
                    'total_price' => $validated['total_price'],
                ]
            );

            $order->orderItems()->delete();
            $order->orderItems()->createMany($validated['items']);

            return $order;
        });

        return redirect()->intended(route('groupOrders.show', $order->group_order_id));
    }

    /**
     * 顯示所有團購.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $groupOrders = GroupOrder::where('course_id', $user->course_id)
            ->with([
                'store' => function ($query) use ($user) {
                    $query->withCount([
                        'groupOrders as course_ordered_group_orders_count' => function ($subQuery) use ($user) {
                            $subQuery->where('course_id', $user->course_id)
                                ->where('status', 'ordered');
                        }
                    ]);
                },
                'user'
            ])
            ->orderByDesc('created_at')
            ->get();

        $groupOrders->makeHidden('menu_snapshot');

        return Inertia::render('groupOrders/Index', [
            'groupOrders' => $groupOrders,
        ]);
    }
}
