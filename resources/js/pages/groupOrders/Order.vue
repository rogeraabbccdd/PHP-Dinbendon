<template lang="pug">
Head(:title="page.props.groupOrder.store.name + '的團購'")
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
            q-card.full-width
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
                                    q-item-label(caption) {{ page.props.groupOrder.store.business_hours || '-' }}
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="delivery_dining" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 外送條件
                                    q-item-label(caption) {{ page.props.groupOrder.store.delivery_conditions || '-' }}
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="place" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 地址
                                    q-item-label(caption) {{ page.props.groupOrder.store.address || '-' }}
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="phone" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 電話
                                    q-item-label(caption) {{ page.props.groupOrder.store.phone || '-' }}
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="thumb_up" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 平均評價
                                    q-item-label(caption) {{ page.props.groupOrder.store.rating_avg || 0 }} / {{ page.props.groupOrder.store.rating_count }} 則評價
                                q-item-section(side top)
                                    q-rating(
                                        :model-value="page.props.groupOrder.store.rating_avg || 0"
                                        readonly no-dimming max="5"
                                        color="orange"
                                        icon="star_border" icon-selected="star" icon-half="star_half"
                                    )
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="thumb_up" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 班級平均評價
                                    q-item-label(caption) {{ page.props.groupOrder.store.course_rating_avg || 0 }} / {{ page.props.groupOrder.store.course_rating_count }} 則評價
                                q-item-section(side top)
                                    q-rating(
                                        :model-value="page.props.groupOrder.store.course_rating_avg || 0"
                                        readonly no-dimming max="5"
                                        color="orange"
                                        icon="star_border" icon-selected="star" icon-half="star_half"
                                    )
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(:icon="page.props.groupOrder.is_public ? 'public' : 'lock'" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 性質
                                    q-item-label(caption) {{ page.props.groupOrder.is_public ? '公開團購' : '班級團購' }}
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="calendar_month" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 開團日期
                                    q-item-label(caption) {{ new Date(page.props.groupOrder.created_at).toLocaleString() }}
                        .col-12.col-md-6
                            q-item
                                q-item-section(avatar top)
                                    q-avatar(icon="home" text-color="rose")
                                q-item-section
                                    q-item-label(lines="1") 班級
                                    q-item-label(caption) {{ page.props.groupOrder.course.name }} {{ page.props.groupOrder.course.year }} 年第 {{ page.props.groupOrder.course.term }} 期
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
                        :grid="!$q.screen.gt.sm"
                        :bordered="!$q.screen.gt.sm"
                    )
                        template(#body-cell-quantity="props")
                            q-td(:props="props")
                                q-input(
                                    :key="props.row.key"
                                    :disable="page.props.groupOrder.status !== 'open'"
                                    :model-value="props.row.value.quantity"
                                    outlined
                                    dense
                                    hide-hint
                                    hide-bottom-space
                                    :bottom-slots="false" color="purple" type="number"
                                    min="0"
                                    :error="!!form.errors.value[`items[${props.row.key}].quantity`]"
                                    @update:model-value="onQuantityChange(props.row.key, $event)"
                                )
                        template(#body-cell-comment="props")
                            q-td(:props="props")
                                q-input(
                                    :key="props.row.key"
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
                                    :error="!!form.errors.value[`items[${props.row.key}].comment`]"
                                )
                        template(#item="props")
                            .q-table__grid-item.col-xs-12.col-sm-6.col-md-4.col-lg-3
                                .q-table__grid-item-card.q-table__card.q-table--flat.q-table--bordered
                                    .q-table__grid-item-row
                                        .q-table__grid-item-title 品項
                                        .q-table__grid-item-value {{ props.row.value.name }}
                                    .q-table__grid-item-row
                                        .q-table__grid-item-title 價格
                                        .q-table__grid-item-value {{ props.row.value.price }}
                                    .q-table__grid-item-row
                                        .q-table__grid-item-title 總訂購數
                                        .q-table__grid-item-value {{ props.row.value.total_ordered_count }}
                                    .q-table__grid-item-row
                                        .q-table__grid-item-title 班級訂購數
                                        .q-table__grid-item-value {{ props.row.value.course_ordered_count }}
                                    .q-table__grid-item-row
                                        .q-table__grid-item-title 數量
                                        .q-table__grid-item-value
                                            q-input(
                                                :key="props.row.key"
                                                :disable="page.props.groupOrder.status !== 'open'"
                                                :model-value="props.row.value.quantity"
                                                outlined
                                                dense
                                                hide-hint
                                                hide-bottom-space
                                                :bottom-slots="false"
                                                color="purple" type="number" min="0"
                                                :error="!!form.errors.value[`items[${props.row.key}].quantity`]"
                                                @update:model-value="onQuantityChange(props.row.key, $event)"
                                            )
                                    .q-table__grid-item-row
                                        .q-table__grid-item-title 備註
                                        .q-table__grid-item-value
                                            q-input(
                                                :key="props.row.key"
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
                                                :error="!!form.errors.value[`items[${props.row.key}].comment`]"
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
import { Head, router, usePage } from '@inertiajs/vue3';
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
        if (!form.values.items) return
        const i = form.values.items.findIndex((item) => {
            return item.menu_item_id * 1 === myOrderItem.menu_item_id * 1;
        });
        if (i !== -1) {
            form.setFieldValue(`items[${i}].quantity`, (myOrderItem.quantity as never) || 0);
            form.setFieldValue(`items[${i}].comment`, (myOrderItem.comment as never) || '');
        }
    });
}

const totalPrice = computed(() => {
    if (!form.values.items) return 0;
    return form.values.items.reduce((total, item) => {
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

<style lang="sass" scoped>
:deep(.q-table__grid-item-value)
    word-wrap: break-word
:deep(.q-table__grid-item-title)
    color: var(--rose)
    font-weight: bold
</style>
