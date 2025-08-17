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
        $courseId = $request->user()->course_id;

        $stores = Store::withCount([
            'groupOrders as ordered_group_orders_count' => function ($query) {
                $query->where('status', 'ordered');
            },
            'groupOrders as course_ordered_group_orders_count' => function ($query) use ($courseId) {
                $query->where('course_id', $courseId)
                    ->where('status', 'ordered');
            }
        ])
            ->withAvg('comments as rating_avg', 'rating')
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
            ])
            ->get();

        return Inertia::render('stores/Index', [
            'stores' => $stores,
        ]);
    }

    /**
     * 顯示單一店家資訊.
     */
    public function show(Request $request, int $id): Response
    {
        $courseId = $request->user()->course_id;

        $store = Store::withCount([
            'groupOrders as ordered_group_orders_count' => function ($query) {
                $query->where('status', 'ordered');
            },
            'groupOrders as course_ordered_group_orders_count' => function ($query) use ($request) {
                $query->where('course_id', $request->user()->course_id)
                    ->where('status', 'ordered');
            }
        ])
            ->withAvg('comments as rating_avg', 'rating')
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
            ])
            ->findOrFail($id);

        $courseId = $request->user()->course_id;

        $menuItemsQuery = $store->menuItems()->where('is_available', true);

        $menuItemsQuery->withSum([
            'orderItems as total_ordered_count' => function ($query) {
                $query->whereHas('order.groupOrder', function ($q) {
                    $q->where('status', 'ordered');
                });
            }
        ], 'quantity');

        $menuItemsQuery->withSum([
            'orderItems as course_ordered_count' => function ($query) use ($courseId) {
                $query->whereHas('order', function ($q) use ($courseId) {
                    $q->whereHas('groupOrder', function ($subQ) {
                        $subQ->where('status', 'ordered');
                    })->whereHas('user', function ($subQ) use ($courseId) {
                        $subQ->where('course_id', $courseId);
                    });
                });
            }
        ], 'quantity');

        $menuItems = $menuItemsQuery->get();

        $menuItems->each(function ($item) use ($courseId) {
            $item->total_ordered_count = (int) $item->total_ordered_count;
            $item->course_ordered_count = $courseId ? (int) $item->course_ordered_count : 0;
        });

        $groupOrders = $store->groupOrders()
            ->with('user')
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->get();
        $groupOrders->makeHidden('menu_snapshot');
        $groupOrders->store = $store;

        $myComment = $store->comments()->where('user_id', $request->user()->id)->first();

        $comments = $store->comments()->get();

        return Inertia::render('stores/Show', [
            'store' => $store,
            'menuItems' => $menuItems,
            'groupOrders' => $groupOrders,
            'myComment' => $myComment,
            'comments' => $comments,
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
