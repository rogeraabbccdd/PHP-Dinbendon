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

class OrderController extends Controller
{
    /**
     * 顯示使用者的訂單列表.
     */
    public function index(Request $request): Response
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->whereHas('groupOrder', fn ($query) => $query->where('status', 'ordered'))
            ->with([
                'groupOrder' => fn ($query) => $query
                    ->select('id', 'course_id', 'store_id', 'user_id', 'status', 'created_at', 'updated_at'),
                'groupOrder.store' => fn ($query) => $query->select('id', 'name'),
                'orderItems',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * 建立團購內訂單.
     */
    public function create(Request $request): RedirectResponse
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
 * 顯示團購內訂單表單.
 */
public function showForm(Request $request, int $id): Response|RedirectResponse
{
    $userId = $request->user()->id;

    $groupOrder = GroupOrder::with([
        'store',
        'user',
        'orders' => function ($query) use ($userId) {
            $query->where('user_id', $userId)->with('orderItems');
        }
    ])->findOrFail($id);

    //===========
    // 新增：僅允許「自己班級」或「公開團購（999999）」
    //===========
    if (
        $groupOrder->course_id !== $request->user()->course_id &&
        $groupOrder->course_id !== 999999
    ) {
        return redirect()->route('groupOrders');
    }
    //===========

    $myOrder = $groupOrder->orders->first();
    $groupOrder->makeHidden(['orders']);

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
     * 取消訂單
     */
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::with('groupOrder')->findOrFail($id);

        if ($order->user_id !== $request->user()->id || $order->groupOrder->status !== 'open') {
            return redirect()->route('groupOrders.show', $order->group_order_id);
        }

        $order->orderItems()->delete();
        $order->delete();

        return redirect()->route('groupOrders.show', $order->group_order_id);
    }
}
