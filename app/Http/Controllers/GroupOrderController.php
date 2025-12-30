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

        $isOpen = $request->boolean('is_open');

        $groupOrder = GroupOrder::create([
            'store_id' => $store->id,
            'course_id' => $request->user()->course_id,
            'user_id' => $request->user()->id,
            'is_open' => $isOpen,
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
        $courseId = $request->user()->course_id;

        $groupOrder = GroupOrder::with([
            'store' => function ($query) use ($courseId) {
                $query->withAvg('comments as rating_avg', 'rating')
                    ->withCount('comments as rating_count')
                    ->withAvg([
                        'comments as course_rating_avg' => function ($query) use ($courseId) {
                            $query->whereHas('user', function ($q) use ($courseId) {
                                $q->where('course_id', $courseId);
                            });
                        }
                    ], 'rating')
                    ->withCount([
                        'comments as course_rating_count' => function ($query) use ($courseId) {
                            $query->whereHas('user', function ($q) use ($courseId) {
                                $q->where('course_id', $courseId);
                            });
                        }
                    ]);
            },
            'user',
            'orders.user',
            'orders.orderItems'
        ])->findOrFail($id);

        /*
 |---------------------------------------
 | 權限判斷：自己班級 or 公開團購
 |---------------------------------------
 */
        if (
            $groupOrder->course_id !== $request->user()->course_id
            && !$groupOrder->is_open
        ) {
            return redirect()->route('groupOrders');
        }

        /*
 |---------------------------------------
 | 公開團購不計算班級評價
 |---------------------------------------
 */
        if ($groupOrder->is_open) {
            $groupOrder->store->course_rating_avg = null;
            $groupOrder->store->course_rating_count = 0;
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
     * 顯示所有團購.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $groupOrders = GroupOrder::where(function ($query) use ($user) {
            $query->where('course_id', $user->course_id)
                ->orWhere('is_open', 1);
        })
            ->with([
                'store' => function ($query) use ($user) {
                    $query->withCount([
                        'groupOrders as course_ordered_group_orders_count' => function ($subQuery) use ($user) {
                            $subQuery->where(function ($q) use ($user) {
                                $q->where('course_id', $user->course_id)
                                    ->orWhere('is_open', 1);
                            })
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
