<template lang="pug">
q-page.q-py-lg
    q-form(@submit.prevent="onFormSubmit")
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
                        :rows="orderItems"
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
                                    :model-value="props.row.value.quantity"
                                    outlined
                                    dense
                                    hide-hint
                                    hide-bottom-space
                                    :bottom-slots="false"
                                    color="purple" type="number" min="0"
                                    :error="Boolean(form.errors.value[`items[${props.rowIndex}].quantity`])"
                                    @update:model-value="onQuantityChange(props.rowIndex, $event)"
                                )
                        template(#body-cell-comment="props")
                            q-td(:props="props")
                                q-input(
                                    v-model="props.row.value.comment"
                                    :disable="page.props.groupOrder.status !== 'open'"
                                    outlined
                                    dense
                                    color="purple"
                                    placeholder="備註 (最多 50 字)"
                                    hide-hint
                                    hide-bottom-space
                                    :bottom-slots="false"
                                    maxlength="50"
                                    :error="Boolean(form.errors.value[`items[${props.rowIndex}].comment`])"
                                )
                //- 動作按鈕
                q-card-section.q-gutter-md
                    .text-center
                        q-btn(
                            label="送出訂單"
                            color="green"
                            icon="upload"
                            type="submit"
                            :disable="page.props.groupOrder.status !== 'open' || totalPrice === 0"
                            :loading="loading"
                        )
                    .text-center
                        | 總金額: ${{ totalPrice }}
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { GroupOrdersOrderPageProps } from '@/types';
import { openLink } from '@/utils/url';
import { router, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import type { QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { useFieldArray, useForm } from 'vee-validate';
import { computed, ref } from 'vue';
import * as zod from 'zod';

defineOptions({ layout: MainLayout });

const page = usePage<GroupOrdersOrderPageProps>();
const $q = useQuasar();

const tableColumns: QTableColumn[] = [
    {
        name: 'name',
        label: '品項',
        field: (row) => row.value.name,
        sortable: true,
        align: 'left',
    },
    {
        name: 'price',
        label: '價格',
        field: (row) => row.value.price,
        sortable: true,
        align: 'left',
    },
    {
        name: 'quantity',
        label: '數量',
        field: (row) => row.value.quantity,
        sortable: true,
        align: 'left',
    },
    {
        name: 'comment',
        label: '備註',
        field: (row) => row.value.comment,
        sortable: true,
        align: 'left',
    },
    {
        name: 'total_ordered_count',
        label: '總訂購數',
        field: (row) => row.value.total_ordered_count,
        sortable: true,
        align: 'left',
    },
    {
        name: 'course_ordered_count',
        label: '班級訂購數',
        field: (row) => row.value.course_ordered_count,
        sortable: true,
        align: 'left',
    },
];

const form = useForm({
    validationSchema: toTypedSchema(
        zod.object({
            items: zod.array(
                zod.object({
                    menu_item_id: zod.number(),
                    quantity: zod.number().min(0),
                    comment: zod.string().max(50),
                    price: zod.number().min(0),
                    name: zod.string().max(255),
                    total_ordered_count: zod.number().optional(),
                    course_ordered_count: zod.number().optional(),
                }),
            ),
        }),
    ),
    initialValues: {
        items: page.props.groupOrder.menu_snapshot.map((item) => ({
            menu_item_id: item.id,
            quantity: 0,
            comment: '',
            price: item.price,
            name: item.name,
            total_ordered_count: item.total_ordered_count,
            course_ordered_count: item.course_ordered_count,
        })),
    },
});

const { fields: orderItems } = useFieldArray('items');

if (page.props.myOrder) {
    page.props.myOrder.order_items.forEach((myOrderItem) => {
        const i = form.values.items!.findIndex((item) => item.menu_item_id === myOrderItem.menu_item_id);
        if (i !== -1) {
            form.setFieldValue(`items[${i}].quantity`, myOrderItem.quantity as never);
            form.setFieldValue(`items[${i}].comment`, myOrderItem.comment as never);
        }
    });
}

const totalPrice = computed(() => {
    return form.values.items!.reduce((total, item) => {
        return total + item.quantity * item.price;
    }, 0);
});

const onQuantityChange = (i: number, value: string | number | null) => {
    const v = parseInt(value as string);
    if (value === null || value === '' || isNaN(v) || v < 0) {
        form.setFieldValue(`items[${i}].quantity`, 0 as never);
    } else {
        form.setFieldValue(`items[${i}].quantity`, v as never);
    }
};

const loading = ref(false);

const onFormSubmit = form.handleSubmit((values) => {
    loading.value = true;
    router.post(
        route('groupOrders.order.create', page.props.groupOrder.id),
        {
            group_order_id: page.props.groupOrder.id,
            items: values.items.filter((item) => item.quantity > 0),
            total_price: totalPrice.value,
        },
        {
            onSuccess: () => {
                loading.value = false;
                $q.notify({
                    type: 'positive',
                    message: `團購訂單送出成功！請把 $${totalPrice.value} 交給值日生`,
                });
            },
            onError: (errors) => {
                console.log(errors);
                loading.value = false;
                $q.notify({
                    type: 'negative',
                    message: '團購訂單送出失敗，請稍後再試',
                });
            },
        },
    );
});
</script>
