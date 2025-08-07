<template lang="pug">
q-page.q-py-lg
    .container
        .row.q-col-gutter-lg
            .col-12.col-sm-6.col-md-6.col-lg-4(
                v-for="groupOrder in page.props.groupOrders"
                :key="groupOrder.id"
            )
                GroupOrderCard.cursor-pointer(v-bind="groupOrder" @click="$inertia.get(`/group-orders/${groupOrder.id}`)")
</template>

<script setup lang="ts">
import GroupOrderCard from '@/components/GroupOrderCard.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { GroupOrderPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: MainLayout });

const page = usePage<GroupOrderPageProps>();
</script>
