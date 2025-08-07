<template lang="pug">
q-page.q-py-lg
    .container.q-gutter-y-lg
        //- 團購已關閉
        template(v-if="page.props.groupOrder.status === 'closed'")
            q-banner.text-white.bg-red.q-mb-lg(rounded)
                | 團購已關閉
                template(#avatar)
                    q-icon(name="warning")
        //- 團購已下單
        template(v-else-if="page.props.groupOrder.status === 'ordered'")
            q-banner.text-white.bg-green.q-mb-lg(rounded)
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
                div
                    | 營業時間: {{ page.props.groupOrder.store.business_hours }}
                    br
                    | 外送條件: {{ page.props.groupOrder.store.delivery_conditions }}
                    br
                    | 地址: {{ page.props.groupOrder.store.address }}
                    br
                    | 電話: {{ page.props.groupOrder.store.phone }}
                    br
                    | 發起人: {{ page.props.groupOrder.user.name }}
                    q-badge.q-ml-sm(align="middle" color="indigo" text-color="white")
                        | {{ page.props.groupOrder.user.seat_number }}
                    br
                    | 開團日期: {{ new Date(page.props.groupOrder.created_at).toLocaleString() }}
                div
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
            //- q-card-section
            //-     q-img(:src="page.props.groupOrder.store.image" height="25vh")
            //- 訂單資訊
            q-card-section
                //- 分頁
                q-tabs.text-primary(v-model="tab" align="center")
                    q-tab(name="user" label="使用者訂單")
                    q-tab(name="all" label="所有訂單")
                q-tab-panels(v-model="tab")
                    //- 使用者訂單卡片
                    q-tab-panel(name="user")
                        .row
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
                q-btn.q-mt-sm(color="green" icon="download" label="下載 csv")
            //- 我的訂單
            q-card-section
                template(v-if="page.props.myOrder")
        //- 我的訂單
        q-card
            q-card-section.text-center
                h4.q-my-md.text-purple
                    | 我的訂單
            //- 有訂單
            template(v-if="page.props.myOrder")
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
                    q-btn.q-mt-sm(
                        color="rose"
                        label="修改訂單"
                        icon="shopping_cart"
                        @click="router.visit(route('groupOrders.order', page.props.groupOrder.id))"
                    )
            //- 無訂單
            template(v-else)
                q-card-section.text-center
                    | 尚未下單
                    q-btn.q-mt-sm(
                        color="rose"
                        label="前往下單"
                        icon="shopping_cart"
                        @click="router.visit(route('groupOrders.order', page.props.groupOrder.id))"
                    )
</template>

<script setup lang="ts">
import OrderCard from '@/components/OrderCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { GroupOrderShowPageProps, OrderItem } from '@/types';
import { openLink } from '@/utils/url';
import { router, usePage } from '@inertiajs/vue3';
import _ from 'lodash';
import type { QTableColumn } from 'quasar';
import { computed, ref } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<GroupOrderShowPageProps>();

const tab = ref<'user' | 'all'>('user');

const groupedMenuItems = computed(() => {
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

    return _.map(groupedById, (orderItems: OrderItem[], menuItemId: number) => {
        const menuItem = menuItemsById[menuItemId];
        const total = _.sumBy(orderItems, 'quantity');
        const subtotal = menuItem.price * total;
        const orders = orderItems.map((item) => {
            const user = orderIdToUser.get(item.order_id);
            return {
                comment: item.comment,
                name: user?.name,
                seat_number: user?.seat_number,
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
    });
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
        return sum + parseFloat(order.total_price);
    }, 0);
});

const myTotalPrice = computed(() => {
    return page.props.myOrder
        ? page.props.myOrder.order_items.reduce((sum, item) => {
              return sum + item.price * item.quantity;
          }, 0)
        : 0;
});
</script>
