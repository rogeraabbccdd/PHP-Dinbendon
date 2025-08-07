<template lang="pug">
q-page.q-py-lg
    .container
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
        //- 內容
        q-card
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
            q-card-section
                q-img(:src="page.props.groupOrder.store.image" height="25vh")
            //- 菜單
            q-card-section
                q-table(
                    :rows="page.props.groupOrder.menu_snapshot"
                    :columns="tableColumns"
                    row-key="id"
                    flat
                    table-header-class="text-rose"
                    :rows-per-page-options="[20, 50, 100, 0]"
                )
                    template(#body-cell-quantity="props")
                        q-td(:props="props")
                            q-input(
                                :disable="page.props.groupOrder.status !== 'open'"
                                :model-value="orderInputs[props.row.id].quantity"
                                outlined
                                dense
                                color="purple" type="number" min="0" @update:model-value="onQuantityChange(props.row.id, $event)"
                            )
                    template(#body-cell-comment="props")
                        q-td(:props="props")
                            q-input(
                                v-model="orderInputs[props.row.id].comment"
                                :disable="page.props.groupOrder.status !== 'open'"
                                outlined
                                dense
                                color="purple"
                                placeholder="備註"
                                maxlength="50"
                            )
            //- 動作按鈕
            q-card-section.q-gutter-md
                .text-center
                    q-btn(
                        label="送出訂單"
                        color="green"
                        icon="upload"
                        :disable="page.props.groupOrder.status !== 'open' || totalPrice === 0"
                        @click="placeOrder"
                    )
                .text-center
                    | 總金額: ${{ totalPrice }}
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { GroupOrdersOrderPageProps } from '@/types';
import { openLink } from '@/utils/url';
import { router, usePage } from '@inertiajs/vue3';
import type { QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<GroupOrdersOrderPageProps>();
const $q = useQuasar();

const tableColumns: QTableColumn[] = [
    {
        name: 'name',
        label: '品項',
        field: 'name',
        sortable: true,
        align: 'left',
    },
    {
        name: 'price',
        label: '價格',
        field: 'price',
        sortable: true,
        align: 'left',
    },
    {
        name: 'quantity',
        label: '數量',
        field: (row) => orderInputs.value[row.id].quantity,
        sortable: true,
        align: 'left',
    },
    {
        name: 'comment',
        label: '備註',
        field: (row) => orderInputs.value[row.id].comment,
        sortable: true,
        align: 'left',
    },
];

interface OrderInput {
    [number: number]: {
        quantity: number;
        comment: string;
    };
}
const orderInputs = ref<OrderInput>({});

// 先初始化所有 menu_item 為 0
page.props.groupOrder.menu_snapshot.forEach((item) => {
    orderInputs.value[item.id] = { quantity: 0, comment: '' };
});

// 如果有 myOrder，將 order_items 帶入
if (page.props.myOrder) {
    page.props.myOrder.order_items.forEach((item) => {
        orderInputs.value[item.menu_item_id] = {
            quantity: item.quantity,
            comment: item.comment || '',
        };
    });
}

const totalPrice = computed(() => {
    return page.props.groupOrder.menu_snapshot.reduce((total, item) => {
        const quantity = orderInputs.value[item.id]?.quantity || 0;
        return total + item.price * quantity;
    }, 0);
});

const onQuantityChange = (id: number, value: string | number | null) => {
    const v = parseInt(value as string);
    if (value === null || value === '' || isNaN(v) || v < 0) {
        orderInputs.value[id].quantity = 0;
    } else {
        orderInputs.value[id].quantity = v;
    }
};

const placeOrder = () => {
    const payload = Object.entries(orderInputs.value)
        .filter(([, data]) => data.quantity > 0)
        .map(([id, data]) => ({
            menu_item_id: id,
            name: page.props.groupOrder.menu_snapshot.find((item) => item.id === parseInt(id))?.name,
            price: page.props.groupOrder.menu_snapshot.find((item) => item.id === parseInt(id))?.price,
            quantity: data.quantity,
            comment: data.comment,
        }));
    router.post(
        '/group-orders/{id}/order',
        {
            group_order_id: page.props.groupOrder.id,
            items: payload,
            total_price: totalPrice.value,
        },
        {
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: `團購訂單送出成功！請把 $${totalPrice.value} 交給值日生`,
                });
            },
            onError: () => {},
        },
    );
};
</script>
