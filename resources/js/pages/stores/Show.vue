<template lang="pug">
Head(:title="page.props.store.name")
q-page.q-py-lg
    .column.container.q-gutter-y-lg
        //- 歇業警告
        template(v-if="page.props.store.is_closed")
            q-banner.text-white.bg-red(rounded)
                | 店家已歇業，無法進行訂購
                template(#avatar)
                    q-icon(name="warning")
        //- 內容
        q-card.full-width
            //- 店家名稱和資訊
            q-card-section.text-center
                h4.q-my-md.text-purple
                    | {{ page.props.store.name }}
            q-card-section
                .row.q-col-gutter-md.full-width
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="access_time" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 營業時間
                                q-item-label(caption) {{ page.props.store.business_hours || '-' }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="attach_money" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 價格區間
                                q-item-label(caption) {{ priceRange }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="delivery_dining" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 外送條件
                                q-item-label(caption) {{ page.props.store.delivery_conditions || '-' }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="alarm_on" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 準時送達機率
                                q-item-label(caption) {{ onTimeRate }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="place" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 地址
                                q-item-label(caption) {{ page.props.store.address || '-' }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="phone" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 電話
                                q-item-label(caption) {{ page.props.store.phone || '-' }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="calendar_month" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 建立日期
                                q-item-label(caption) {{ new Date(page.props.store.created_at).toLocaleString() }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="calendar_month" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 更新日期
                                q-item-label(caption) {{ new Date(page.props.store.updated_at).toLocaleString() }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="group" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 總成團次數
                                q-item-label(caption) {{ page.props.store.ordered_group_orders_count }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="group" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 班級成團次數
                                q-item-label(caption) {{ page.props.store.course_ordered_group_orders_count }}
                    .col-12.col-md-6
                        q-item
                            q-item-section(avatar top)
                                q-avatar(icon="thumb_up" text-color="rose")
                            q-item-section
                                q-item-label(lines="1") 平均評價
                                q-item-label(caption) {{ page.props.store.rating_avg || 0 }} / {{ page.props.store.rating_count }} 則評價
                            q-item-section(side top)
                                q-rating(
                                    :model-value="page.props.store.rating_avg || 0"
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
                                q-item-label(caption) {{ page.props.store.course_rating_avg || 0 }} / {{ page.props.store.course_rating_count }} 則評價
                            q-item-section(side top)
                                q-rating(
                                    :model-value="page.props.store.course_rating_avg || 0"
                                    readonly no-dimming max="5"
                                    color="orange"
                                    icon="star_border" icon-selected="star" icon-half="star_half"
                                )
            q-card-section.text-center
                q-btn(
                    v-if="page.props.store.facebook"
                    icon="fa-brands fa-facebook"
                    flat
                    round
                    color="blue"
                    size="md"
                    @click="openLink(page.props.store.facebook)"
                )
                q-btn(
                    v-if="page.props.store.instagram"
                    icon="fa-brands fa-instagram"
                    flat
                    round
                    color="purple"
                    size="md"
                    @click="openLink(page.props.store.instagram)"
                )
                q-btn(
                    v-if="page.props.store.google_map"
                    icon="location_on"
                    flat
                    round
                    color="green"
                    size="md"
                    @click="openLink(page.props.store.google_map)"
                )
            //- 店家圖片
            q-card-section
                q-img(:src="page.props.store.image" height="25vh")
            //- 菜單
            q-card-section
                q-table(
                    :rows="page.props.menuItems" :columns="tableColumns"
                    row-key="id"
                    flat
                    table-header-class="text-rose"
                    :rows-per-page-options="[20, 50, 100, 0]"
                    :grid="!$q.screen.gt.sm"
                    :bordered="!$q.screen.gt.sm"
                )
            //- 日期
            q-card-section
                .text-caption.text-grey.text-center
                    | 最後更新日期: {{ new Date(page.props.store.updated_at).toLocaleString() }}
                    br
                    | 店家建立日期: {{ new Date(page.props.store.created_at).toLocaleString() }}
            //- 動作按鈕
            q-card-section
                .text-center.q-gutter-md
                    q-btn(
                        label="開團訂購"
                        color="green"
                        icon="group_add"
                        :disable="page.props.store.is_closed"
                        :loading="loading"
                        @click="orderDialog = true"
                    )
                    q-btn(
                        label="編輯資訊"
                        color="purple"
                        icon="edit"
                        @click="router.get(route('stores.edit', page.props.store.id))"
                    )
            //- 團購對話框
            q-dialog(v-model="orderDialog")
                q-card
                    q-card-section
                        .text-h6.text-center 開團訂購
                    q-card-section
                        .row.justify-center
                            .col-12.col-md-6.text-center
                                q-btn.full-width(flat stack @click="createGroupOrder(false)")
                                    q-icon(name="lock" color="red" size="56px")
                                    .q-mt-md.text-center 班級團購
                            .col-12.col-md-6.text-center
                                q-btn.full-width(flat stack @click="createGroupOrder(true)")
                                    q-icon(name="public" color="green" size="56px")
                                    .q-mt-md.text-center 公開團購
        //- 進行中團購卡片
        .full-width
            .row.q-col-gutter-lg
                .col-12.col-sm-6.col-md-6.col-lg-4(
                    v-for="groupOrder in page.props.groupOrders"
                    :key="groupOrder.id"
                )
                    GroupOrderCard.cursor-pointer(v-bind="groupOrder" :store="page.props.store" @click="router.get(route('groupOrders.show', groupOrder.id))")
        //- 評價
        q-card.full-width
            q-card-section.text-center
                h4.q-my-md
                    | 我的評價
            q-card-section
                q-form(@submit.prevent="submitComment")
                    .column.q-gutter-md
                        q-input(
                            v-model="content"
                            type="textarea"
                            placeholder="評價內容..." outlined bg-color="white" color="purple"
                            :error="!!form.errors.value.content"
                            :error-message="form.errors.value.content"
                            hide-hint
                            hide-bottom-space
                        )
                        .text-center
                            q-rating(
                                v-model="rating"
                                :max="5"
                                color="orange" icon="star" size="2em"
                                :error="!!form.errors.value.rating"
                                :error-message="form.errors.value.rating"
                                no-reset
                            )
                        .text-center
                            q-btn(label="送出" type="submit" color="green" icon="comment" :loading="loading")
        q-card.full-width
            q-card-section.text-center
                h4.q-my-md
                    | 所有評價
            q-card-section
                q-input(
                    v-model="search"
                    outlined bg-color="white" color="purple"
                    placeholder="搜尋評價"
                )
                    template(#prepend)
                        q-icon(name="search")
                    template(#append)
                        q-icon.cursor-pointer(v-if="search" name="close" @click="search = ''")
            q-card-section
                .row.align.items-center.q-gutter-y-md
                    .col-12.col-sm-6.col-lg-6 排序
                    .col-12.col-sm-6.col-lg-6
                        .q-gutter-md-xs
                            q-btn(
                                flat
                                label="評價"
                                :icon-right="getSortIcon('rating')"
                                :text-color="sort.field === 'rating' ? 'purple' : 'grey'"
                                @click="changeSort('rating')"
                            )
                            q-btn(
                                flat
                                label="評價時間"
                                :icon-right="getSortIcon('created_at')"
                                :text-color="sort.field === 'created_at' ? 'purple' : 'grey'"
                                @click="changeSort('created_at')"
                            )
            q-separator
            q-card-section
                q-list()
                    q-item(v-for="comment in filteredComments" :key="comment.id")
                        q-item-section
                            q-item-label.text-rose
                                | {{ new Date(comment.created_at).toLocaleString() }}
                            q-item-label.text-body2
                                | {{ comment.content }}
                        q-item-section(side top)
                            q-rating(
                                :model-value="comment.rating"
                                readonly color="orange" icon="star"
                            )
                    q-item(v-if="filteredComments.length === 0")
                        q-item-section
                            q-item-label.text-center 暫無評價
</template>

<script setup lang="ts">
import GroupOrderCard from '@/components/GroupOrderCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { StoreShowPageProps } from '@/types';
import { openLink } from '@/utils/url';
import { Head, router, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import type { QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { useField, useForm } from 'vee-validate';
import { computed, ref } from 'vue';
import * as zod from 'zod';

const page = usePage<StoreShowPageProps>();
const $q = useQuasar();

defineOptions({ layout: MainLayout });

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
        name: 'total_ordered_count',
        label: '總訂購數',
        field: 'total_ordered_count',
        sortable: true,
        align: 'left',
    },
    {
        name: 'course_ordered_count',
        label: '班級訂購數',
        field: 'course_ordered_count',
        sortable: true,
        align: 'left',
    },
];

const loading = ref(false);

const onTimeRate = computed(() => {
    const total = page.props.store.on_time_total_count || 0
    const success = page.props.store.on_time_success_count || 0
    if (total === 0)    return '-'

    // 計算百分比並四捨五入
    const percentage = Math.round((success / total) * 100);

    return `${percentage}% (${success}/${total})`;
})

const priceRange = computed(() => {
    return Math.round(page.props.store.min_price) + ' ~ ' + Math.round(page.props.store.max_price)
})

const orderDialog = ref(false)

const createGroupOrder = (is_public: boolean) => {
    $q.dialog({
        title: '你確定?',
        message: '你確定要開團嗎?',
        cancel: true,
        persistent: true,
    }).onOk(() => {
        loading.value = true;
        router.post(
            route('groupOrders.create'),
            {
                store_id: page.props.store.id,
                is_public
            },
            {
                onSuccess: () => {
                    loading.value = false;
                    $q.notify({
                        type: 'positive',
                        message: '團購建立成功！',
                    });
                },
                onError: () => {
                    loading.value = false;
                    $q.notify({
                        type: 'negative',
                        message: '團購建立失敗！',
                    });
                },
            },
        );
    });
};

type SortField = 'rating' | 'created_at';
type SortOrder = 1 | -1;

const search = ref('');
const sort = ref<{
    field: SortField;
    order: SortOrder;
}>({
    field: 'rating',
    order: 1,
});

const getSortIcon = (field: SortField) => {
    if (sort.value.field === field) return sort.value.order > 0 ? 'arrow_drop_up' : 'arrow_drop_down';
    else return undefined;
};

const changeSort = (field: SortField) => {
    if (sort.value.field === field) sort.value.order *= -1;
    else {
        sort.value.field = field;
        sort.value.order = -1;
    }
};

const filteredComments = computed(() => {
    return page.props.comments
        .filter((comment) => {
            return comment.content.includes(search.value);
        })
        .sort((a, b) => {
            if (a[sort.value.field] < b[sort.value.field]) {
                return -sort.value.order;
            }
            if (a[sort.value.field] > b[sort.value.field]) {
                return sort.value.order;
            }
            return 0;
        });
});

const form = useForm({
    validationSchema: toTypedSchema(
        zod.object({
            content: zod.string().min(1, { message: '必填欄位' }).max(100, { message: '不得超過 200 字' }),
            rating: zod.number().min(1, { message: '必須在 1 到 5 之間' }).max(5, { message: '必須在 1 到 5 之間' }),
        }),
    ),
    initialValues: {
        content: '',
        rating: 5,
    },
});
const { value: content } = useField<string>('content');
const { value: rating } = useField<number>('rating');

if (page.props.myComment) {
    form.setFieldValue('content', page.props.myComment.content);
    form.setFieldValue('rating', page.props.myComment.rating);
}

const submitComment = form.handleSubmit(async (values) => {
    loading.value = true;
    await new Promise((resolve) => {
        router.post(
            route('comments.submit'),
            { ...values, store_id: page.props.store.id },
            {
                onSuccess: () => {
                    resolve(undefined);
                    $q.notify({
                        type: 'positive',
                        message: '評價成功！',
                    });
                    router.reload({ only: ['comments', 'myComment'] });
                },
                onError: (errors) => {
                    form.setErrors(errors);
                    resolve(undefined);
                },
            },
        );
    });
    loading.value = false;
});
</script>

<style lang="sass" scoped>
:deep(.q-table__grid-item-title)
    color: var(--rose)
    font-weight: bold
</style>
