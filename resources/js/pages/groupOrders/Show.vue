<template lang="pug">
Head(:title="page.props.groupOrder.store.name + '的團購'")
q-page.q-py-lg
    .column.container.q-gutter-y-lg
        //- 店家已歇業
        template(v-if="page.props.groupOrder.store.is_closed")
            q-banner.text-white.bg-red(rounded)
                | 店家已歇業，無法進行訂購
                template(#avatar)
                    q-icon(name="warning")
        //- 團購已關閉
        template(v-if="page.props.groupOrder.status === 'closed'")
            q-banner.text-white.bg-red(rounded)
                | 團購已關閉
                template(#avatar)
                    q-icon(name="warning")
        //- 團購已下單
        template(v-else-if="page.props.groupOrder.status === 'ordered'")
            q-banner.text-white.bg-green(rounded)
                | 團購已下單
                template(#avatar)
                    q-icon(name="check")
        //- 團購資訊、所有訂單
        q-card
            //- 店家名稱
            q-card-section.text-center
                h4.q-my-md.text-purple.cursor-pointer(
                    @click="router.visit(route('stores.show', page.props.groupOrder.store.id))"
                )
                    | {{ page.props.groupOrder.store.name }}
            q-card-section
                .row.q-col-gutter-md.full-width
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="access_time" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 營業時間
                                q-item-label(caption) {{ page.props.groupOrder.store.business_hours }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="delivery_dining" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 外送條件
                                q-item-label(caption) {{ page.props.groupOrder.store.delivery_conditions }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="place" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 地址
                                q-item-label(caption) {{ page.props.groupOrder.store.address }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="phone" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 電話
                                q-item-label(caption) {{ page.props.groupOrder.store.phone }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="person" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 發起人
                                q-item-label(caption)
                                    | {{ page.props.groupOrder.user.name }}
                                    q-badge.q-ml-sm(align="middle" color="indigo" text-color="white")
                                        | {{ page.props.groupOrder.user.seat_number }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="calendar_month" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 開團日期
                                q-item-label(caption) {{ new Date(page.props.groupOrder.created_at).toLocaleString() }}
            q-card-section.text-center
                q-btn(
                    v-if="page.props.groupOrder.store.facebook"
                    icon="fa-brands fa-facebook"
                    flat
                    round
                    color="blue"
                    size="md"
                    @click="openLink(page.props.groupOrder.store.facebook)"
                )
                q-btn(
                    v-if="page.props.groupOrder.store.instagram"
                    icon="fa-brands fa-instagram"
                    flat
                    round
                    color="purple"
                    size="md"
                    @click="openLink(page.props.groupOrder.store.instagram)"
                )
                q-btn(
                    v-if="page.props.groupOrder.store.google_map"
                    icon="location_on"
                    flat
                    round
                    color="green"
                    size="md"
                    @click="openLink(page.props.groupOrder.store.google_map)"
                )
            //- 店家圖片
            q-card-section
                q-img(:src="page.props.groupOrder.store.image" height="25vh")
            //- 訂單資訊
            q-card-section
                //- 分頁
                q-tabs.text-primary(v-model="tab" align="center")
                    q-tab(name="user" label="訂單")
                    q-tab(name="all" label="品項")
                q-tab-panels(v-model="tab")
                    //- 使用者訂單卡片
                    q-tab-panel(name="user")
                        .row
                            .col-12.text-center(v-if="page.props.orders.length === 0")
                                | 尚無訂單
                            .col-xs-12.col-sm-6.col-md-4.q-pa-xs(
                                v-for="order in page.props.orders" :key="order.id"
                            )
                                OrderCard(v-bind="order")
                    q-tab-panel(name="all")
                        q-table(
                            :rows="groupedMenuItems"
                            :columns="ordersColumns"
                            row-key="id"
                            flat
                            table-header-class="text-rose"
                            :rows-per-page-options="[0]"
                        )
                            template(#body-cell-orders="props")
                                q-td(:props="props")
                                    q-chip(
                                        v-for="(order, index) in props.row.orders" :key="index"
                                        :color="order.comment ? 'deep-orange' : 'green'"
                                        text-color="white"
                                    )
                                        q-avatar(color="indigo") {{ order.seat_number }}
                                        | {{ order.name }}
                                        q-separator.q-mx-sm(vertical inset color="white")
                                        | x{{ order.quantity }}
                                        template(v-if="order.comment")
                                            q-separator.q-mx-sm(vertical inset color="white")
                                            | {{ order.comment }}
            //- 總金額
            q-card-section.text-center
                | 總金額: {{ totalPrice }} 元
                br
                q-btn.q-my-sm(color="blue" icon="download" label="下載 csv" @click="downloadCsv")
                template(v-if="page.props.auth.user && page.props.groupOrder.user.id === page.props.auth.user.id")
                    br
                    q-btn.q-my-sm.q-mx-sm(
                        :disable="!canOrder"
                        :loading="loading"
                        color="green" icon="check" label="確認結單"
                        @click="setStatus('ordered')"
                    )
                    q-btn.q-my-sm.q-mx-sm(
                        :disable="page.props.groupOrder.status !== 'open'"
                        :loading="loading"
                        color="red" icon="close" label="關閉團購"
                        @click="setStatus('closed')"
                    )
        //- 我的訂單
        q-card
            //- 有訂單
            template(v-if="page.props.myOrder")
                q-card-section.text-center
                    h4.q-my-md
                        | 我的訂單
                q-card-section
                    q-table(
                        :rows="page.props.myOrder.order_items"
                        :columns="myOrdersColumns"
                        row-key="id"
                        flat
                        table-header-class="text-rose"
                        :rows-per-page-options="[0]"
                    )
                q-card-section.text-center
                    | 總金額: {{ myTotalPrice }} 元
                    br
                    q-btn.q-my-sm.q-mx-sm(
                        color="green"
                        label="修改訂單"
                        icon="edit"
                        :disable="!canOrder"
                        :loading="loading"
                        @click="router.visit(route('groupOrders.order', page.props.groupOrder.id))"
                    )
                    q-btn.q-my-sm.q-mx-sm(
                        color="red"
                        label="取消訂單"
                        icon="clear"
                        :disable="!canOrder"
                        :loading="loading"
                        @click="cancelOrder"
                    )
            //- 無訂單
            template(v-else)
                q-card-section.text-center
                    h4.q-my-md
                        | 尚未下單
                    q-btn.q-mt-sm(
                        color="green"
                        label="前往下單"
                        icon="shopping_cart"
                        :disable="!canOrder"
                        :loading="loading"
                        @click="router.visit(route('groupOrders.order', page.props.groupOrder.id))"
                    )
</template>

<script setup lang="ts">
import OrderCard from '@/components/OrderCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { GroupOrderShowPageProps } from '@/types';
import { openLink } from '@/utils/url';
import { Head, router, usePage } from '@inertiajs/vue3';
import _ from 'lodash';
import type { QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import * as xlsx from 'xlsx';

interface GroupedMenuItem {
    id: number;
    name: string;
    price: number;
    total: number;
    subtotal: number;
    orders: {
        comment?: string;
        name: string;
        seat_number: number;
        quantity: number;
    }[];
}

defineOptions({ layout: MainLayout });

const page = usePage<GroupOrderShowPageProps>();
const $q = useQuasar();

const canOrder = computed(() => {
    return page.props.groupOrder.status === 'open' && !page.props.groupOrder.store.is_closed;
});

const tab = ref<'user' | 'all'>('user');

const groupedMenuItems = computed((): GroupedMenuItem[] => {
    const menuItemsById = _.keyBy(page.props.groupOrder.menu_snapshot, 'id');
    const allOrderItems = _.flatMap(page.props.orders, 'order_items');
    const groupedById = _.groupBy(allOrderItems, 'menu_item_id');
    // 建立 orderId 對應 user 的 Map
    const orderIdToUser = new Map<number, { name: string; seat_number: number }>();
    page.props.orders.forEach((order) => {
        if (order.user) {
            orderIdToUser.set(order.id, {
                name: order.user.name,
                seat_number: order.user.seat_number,
            });
        }
    });

    return _.map(groupedById, (orderItems, menuItemId): GroupedMenuItem => {
        const menuItem = menuItemsById[menuItemId];
        const total = _.sumBy(orderItems, 'quantity');
        const subtotal = menuItem.price * total;
        const orders = orderItems.map((item) => {
            const user = orderIdToUser.get(item.order_id);
            return {
                comment: item.comment,
                name: user?.name || '',
                seat_number: user?.seat_number || 0,
                quantity: item.quantity,
            };
        });

        return {
            id: menuItem.id,
            name: menuItem.name,
            price: menuItem.price,
            total,
            subtotal,
            orders,
        };
    }) as unknown as GroupedMenuItem[];
});

const ordersColumns: QTableColumn[] = [
    { name: 'name', label: '品項', field: 'name', align: 'left', sortable: true },
    { name: 'price', label: '單價', field: 'price', align: 'right', sortable: true },
    { name: 'total', label: '數量', field: 'total', align: 'right', sortable: true },
    { name: 'subtotal', label: '金額', field: 'subtotal', align: 'right', sortable: true },
    { name: 'orders', label: '訂購者', field: 'orders', align: 'right', sortable: true },
];

const myOrdersColumns: QTableColumn[] = [
    { name: 'name', label: '品項', field: 'name', align: 'left', sortable: true },
    { name: 'price', label: '單價', field: 'price', align: 'right', sortable: true },
    { name: 'quantity', label: '數量', field: 'quantity', align: 'right', sortable: true },
    {
        name: 'subtotal',
        label: '金額',
        field: 'subtotal',
        align: 'right',
        sortable: true,
        format: (val, row) => (row.price * row.quantity).toString(),
    },
    { name: 'comment', label: '備註', field: 'comment', align: 'right', sortable: true },
];

const totalPrice = computed(() => {
    return page.props.orders.reduce((sum, order) => {
        return sum + order.total_price;
    }, 0);
});

const myTotalPrice = computed(() => {
    return page.props.myOrder
        ? page.props.myOrder.order_items.reduce((sum, item) => {
              return sum + item.price * item.quantity;
          }, 0)
        : 0;
});

const loading = ref(false);

const cancelOrder = () => {
    $q.dialog({
        title: '你確定?',
        message: '你確定要取消訂單嗎?',
        cancel: true,
        persistent: true,
    }).onOk(() => {
        loading.value = true;

        router.delete(route('orders.cancel', { id: page.props.myOrder!.id }), {
            onSuccess: () => {
                loading.value = false;
                $q.notify({
                    type: 'positive',
                    message: '訂單取消成功！',
                });
                router.reload();
            },
            onError: () => {
                loading.value = false;
                $q.notify({
                    type: 'negative',
                    message: '訂單取消失敗！',
                });
            },
        });
    });
};

const setStatus = (status: 'ordered' | 'closed') => {
    $q.dialog({
        title: '你確定?',
        message: `你確定要${status === 'ordered' ? '下單' : '關閉'}嗎?`,
        cancel: true,
        persistent: true,
    }).onOk(() => {
        loading.value = true;

        router.post(
            route('groupOrders.update', { id: page.props.groupOrder.id }),
            { status },
            {
                onSuccess: () => {
                    loading.value = false;
                    $q.notify({
                        type: 'positive',
                        message: '團購狀態更新成功！',
                    });
                },
                onError: () => {
                    loading.value = false;
                    $q.notify({
                        type: 'negative',
                        message: '團購狀態更新失敗！',
                    });
                },
            },
        );
    });
};

const downloadCsv = () => {
    const data = groupedMenuItems.value.map((item) => {
        return {
            品項: item.name,
            單價: item.price,
            數量: item.total,
            金額: item.subtotal,
            訂購者: item.orders
                .map((order) => `${order.seat_number} ${order.name} x${order.quantity}${order.comment ? ` (${order.comment})` : ''}`)
                .join(', '),
        };
    });
    const ws = xlsx.utils.json_to_sheet(data);

    const totalRow = [{ field: '總計', value: totalPrice.value }];
    xlsx.utils.sheet_add_json(ws, totalRow, { origin: -1, skipHeader: true });

    const wb = xlsx.utils.book_new();
    xlsx.utils.book_append_sheet(wb, ws, '訂單');

    const date = new Date().toISOString().slice(0, 10);
    xlsx.writeFile(wb, `${page.props.groupOrder.store.name}-團購訂單-${date}.xlsx`);
};

// 自動更新訂單
let timer = 0;
onMounted(() => {
    timer = setInterval(() => {
        router.reload({ only: ['orders', 'myOrder', 'groupOrder'] });
    }, 10000);
});

onUnmounted(() => {
    clearInterval(timer);
});
</script>
