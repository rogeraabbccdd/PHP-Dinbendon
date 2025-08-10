<template lang="pug">
q-page.q-py-lg
    .container
        //- 搜尋
        q-card
            q-card-section
                q-input(
                    v-model="search"
                    outlined bg-color="white" color="rose"
                    placeholder="搜尋店家"
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
                                label="名稱"
                                :icon-right="getSortIcon('name')"
                                :text-color="sort.field === 'name' ? 'rose' : 'grey'"
                                @click="changeSort('name')"
                            )
                            q-btn(
                                flat
                                label="更新時間"
                                :icon-right="getSortIcon('updated_at')"
                                :text-color="sort.field === 'updated_at' ? 'rose' : 'grey'"
                                @click="changeSort('updated_at')"
                            )
                            q-btn(
                                flat
                                label="總成團次數"
                                :icon-right="getSortIcon('ordered_group_orders_count')"
                                :text-color="sort.field === 'ordered_group_orders_count' ? 'rose' : 'grey'"
                                @click="changeSort('ordered_group_orders_count')"
                            )
                            q-btn(
                                flat
                                label="班級成團次數"
                                :icon-right="getSortIcon('course_ordered_group_orders_count')"
                                :text-color="sort.field === 'course_ordered_group_orders_count' ? 'rose' : 'grey'"
                                @click="changeSort('course_ordered_group_orders_count')"
                            )
        //- 店家卡片列表
        .row.q-col-gutter-lg.q-mt-sm
            .col-12.col-sm-6.col-md-6.col-lg-4(
                v-for="store in filteredStores"
                :key="store.id"
            )
                StoreCard.cursor-pointer(v-bind="store" @click="router.get(route('stores.show', store.id))")
</template>

<script setup lang="ts">
import StoreCard from '@/components/StoreCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { Store, StorePageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<StorePageProps>();

type SortField = 'created_at' | 'name' | 'updated_at' | 'ordered_group_orders_count' | 'course_ordered_group_orders_count';
type SortOrder = 1 | -1;

const search = ref('');
const sort = ref<{
    field: SortField;
    order: SortOrder;
}>({
    field: 'name',
    order: 1,
});

const filteredStores = computed(() => {
    return page.props.stores
        .filter((store) => store.name.toLowerCase().includes(search.value.toLowerCase()))
        .sort((a: Store, b: Store) => {
            const aValue = a[sort.value.field];
            const bValue = b[sort.value.field];
            if (aValue < bValue) return -1 * sort.value.order;
            if (aValue > bValue) return 1 * sort.value.order;
            return 0;
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
</script>
