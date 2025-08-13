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
}
