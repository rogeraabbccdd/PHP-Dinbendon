<template lang="pug">
q-page.q-py-lg
    .container
        //- 搜尋
        q-card
            q-card-section
                q-input(
                    v-model="search"
                    outlined bg-color="white" color="purple"
                    placeholder="搜尋店家"
                )
                    template(#prepend)
                        q-icon(name="search")
                    template(#append)
                        q-icon.cursor-pointer(v-if="search" name="close" @click="search = ''")
            q-card-section
                .row.align.items-center.q-gutter-y-md
                    .col-12.col-sm-6.col-lg-6
                        | 狀態
                    .col-12.col-sm-6.col-lg-6
                        .q-gutter-md-xs
                            q-checkbox(
                                v-model="status"
                                color="purple"
                                val="open"
                                label="開團中"
                            )
                            q-checkbox(
                                v-model="status"
                                color="purple"
                                val="closed"
                                label="已關閉"
                            )
                            q-checkbox(
                                v-model="status"
                                color="purple"
                                val="ordered"
                                label="已下單"
                            )
            q-card-section
                .row.align.items-center.q-gutter-y-md
                    .col-12.col-sm-6.col-lg-6
                        | 排序
                    .col-12.col-sm-6.col-lg-6
                        .q-gutter-md-xs
                            q-btn(
                                flat
                                label="開團日期"
                                :icon-right="getSortIcon('created_at')"
                                :text-color="sort.field === 'created_at' ? 'purple' : 'grey'"
                                @click="changeSort('created_at')"
                            )
                            q-btn(
                                flat
                                label="店家名稱"
                                :icon-right="getSortIcon('name')"
                                :text-color="sort.field === 'name' ? 'purple' : 'grey'"
                                @click="changeSort('name')"
                            )
                            q-btn(
                                flat
                                label="班級成團次數"
                                :icon-right="getSortIcon('course_ordered_group_orders_count')"
                                :text-color="sort.field === 'course_ordered_group_orders_count' ? 'purple' : 'grey'"
                                @click="changeSort('course_ordered_group_orders_count')"
                            )
        .row.q-col-gutter-lg.q-mt-sm
            .col-12.col-sm-6.col-md-6.col-lg-4(
                v-for="groupOrder in filteredGroupOrders"
                :key="groupOrder.id"
            )
                GroupOrderCard.cursor-pointer(v-bind="groupOrder" @click="router.get(route('groupOrders.show', groupOrder.id))")
</template>

<script setup lang="ts">
import GroupOrderCard from '@/components/GroupOrderCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { GroupOrderBase, GroupOrderPageProps, GroupOrderStatus } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { computed, onMounted, ref } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<GroupOrderPageProps>();
const $q = useQuasar();

const status = ref<GroupOrderStatus[]>(['open', 'closed', 'ordered']);
const search = ref('');

type SortField = 'created_at' | 'name' | 'course_ordered_group_orders_count';
type SortOrder = 1 | -1;

const sort = ref<{
    field: SortField;
    order: SortOrder;
}>({
    field: 'created_at',
    order: -1,
});

const filteredGroupOrders = computed(() => {
    return page.props.groupOrders
        .filter((groupOrder): GroupOrderBase[] => {
            return status.value.includes(groupOrder.status) && groupOrder.store.name.toLowerCase().includes(search.value.toLowerCase());
        })
        .sort((a, b) => {
            if (sort.value.field === 'created_at') {
                const aDate = new Date(a.created_at);
                const bDate = new Date(b.created_at);
                return (aDate.getTime() - bDate.getTime()) * sort.value.order;
            } else if (sort.value.field === 'name') {
                const aName = a.store.name.toLowerCase();
                const bName = b.store.name.toLowerCase();
                if (aName < bName) return -1 * sort.value.order;
                if (aName > bName) return 1 * sort.value.order;
                return 0;
            } else {
                return (a.store.course_ordered_group_orders_count - b.store.course_ordered_group_orders_count) * sort.value.order;
            }
        });
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

onMounted(() => {
    const openGroupOrdersCount = page.props.groupOrders.filter((groupOrder) => groupOrder.status === 'open').length;

    if (openGroupOrdersCount > 0) {
        $q.notify({
            message: `目前有 ${openGroupOrdersCount} 個團購進行中`,
            color: 'purple',
            position: 'top',
            icon: 'notifications',
        });
    }
});
</script>
