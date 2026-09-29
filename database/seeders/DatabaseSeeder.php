<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Course;
use App\Models\GroupOrder;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 班級
        $course = Course::factory()->create([
            'name' => '前端網頁開發技術',
            'year' => 2025,
            'term' => 2,
        ]);
        $disabledCourse = Course::factory()->disabled()->create();

        // 學生，1 號為測試帳號 (學號、密碼皆為 1111)
        $users = User::factory()
            ->count(30)
            ->sequence(fn ($sequence) => ['seat_number' => $sequence->index + 1])
            ->create(['course_id' => $course->id]);
        $users->first()->forceFill(['student_id' => '1111', 'password' => Hash::make('1111')])->save();
        User::factory()
            ->count(10)
            ->sequence(fn ($sequence) => ['seat_number' => $sequence->index + 1])
            ->create(['course_id' => $disabledCourse->id]);

        // 店家與菜單
        $stores = Store::factory()->count(20)->create();
        $stores->each(function (Store $store) {
            MenuItem::factory()->count(rand(4, 8))->create(['store_id' => $store->id]);
        });
        $openStores = $stores->where('is_closed', false);

        // 過去的團購：已結單或已關閉
        for ($i = 0; $i < 15; $i++) {
            $factory = GroupOrder::factory();
            $factory = rand(1, 5) === 1 ? $factory->closed() : $factory->ordered();
            $createdAt = now()->subDays(rand(1, 60))->setTime(9, rand(0, 59));

            $groupOrder = $factory->create([
                'course_id' => $course->id,
                'store_id' => $stores->random()->id,
                'user_id' => $users->random()->id,
                'is_public' => rand(1, 4) === 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $this->createOrders($groupOrder, $users->random(rand(5, 20)));
        }

        // 進行中的團購，所有人都下單，方便測試品項統計與 csv 下載
        $groupOrder = GroupOrder::factory()->create([
            'course_id' => $course->id,
            'store_id' => $openStores->random()->id,
            'user_id' => $users->first()->id,
        ]);
        $this->createOrders($groupOrder, $users);

        // 進行中的公開團購
        $groupOrder = GroupOrder::factory()->public()->create([
            'course_id' => $course->id,
            'store_id' => $openStores->random()->id,
            'user_id' => $users->random()->id,
        ]);
        $this->createOrders($groupOrder, $users->random(8));

        // 店家評論，每位學生隨機評論 0 ~ 5 家店
        $users->each(function (User $user) use ($stores) {
            $stores->random(rand(0, 5))->each(function (Store $store) use ($user) {
                Comment::factory()->create([
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                ]);
            });
        });
    }

    /**
     * 為團購建立指定使用者的訂單，品項從團購的菜單快照中挑選
     *
     * @param  Collection<int, User>  $users
     */
    private function createOrders(GroupOrder $groupOrder, Collection $users): void
    {
        $menu = collect($groupOrder->menu_snapshot)->where('is_available', true);
        if ($menu->isEmpty()) {
            return;
        }

        $users->each(function (User $user) use ($groupOrder, $menu) {
            $order = Order::factory()->create([
                'group_order_id' => $groupOrder->id,
                'user_id' => $user->id,
                'created_at' => $groupOrder->created_at,
                'updated_at' => $groupOrder->created_at,
            ]);

            $items = $menu->random(min(rand(1, 2), $menu->count()));
            $totalPrice = 0;
            foreach ($items as $item) {
                $orderItem = OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'created_at' => $groupOrder->created_at,
                    'updated_at' => $groupOrder->created_at,
                ]);
                $totalPrice += $orderItem->price * $orderItem->quantity;
            }

            $order->update(['total_price' => $totalPrice]);
        });
    }
}
