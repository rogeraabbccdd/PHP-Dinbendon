<template lang="pug">
q-page.q-py-lg
    .container
        .row.q-col-gutter-lg
            //- 搜尋欄位
            .col-12
                q-input(
                    v-model="search"
                    rounded outlined bg-color="white" color="rose"
                    placeholder="搜尋店家"
                )
                    template(#prepend)
                        q-icon(name="search")
                    template(#append)
                        q-icon.cursor-pointer(v-if="search" name="close" @click="search = ''")
            //- 店家卡片列表
            .col-12.col-sm-6.col-md-6.col-lg-4(
                v-for="store in filteredStores"
                :key="store.id"
            )
                StoreCard.cursor-pointer(v-bind="store" @click="router.get(route('stores.show', store.id))")
</template>

<script setup lang="ts">
import StoreCard from '@/components/StoreCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { StorePageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<StorePageProps>();

const search = ref('');

const filteredStores = computed(() => {
    return page.props.stores.filter((store) => store.name.toLowerCase().includes(search.value.toLowerCase()));
});
</script>
