<template lang="pug">
q-page.q-py-lg
    .container
        q-card
            q-card-section
                .flex.items-center
                    q-btn-group
                        q-btn(label="<" color="rose" @click="calendar?.prev();")
                        q-btn(label="今天" color="rose" @click="calendar?.moveToToday();")
                        q-btn(label=">" color="rose" @click="calendar?.next();")
                    q-space
                    .text-h6 {{ currentMonth }}
            q-card-section
                q-calendar-month(
                    ref="calendar"
                    v-model="selectedDate"
                    locale="zh-HANT"
                    :day-min-height="150"
                    :day-height="0"
                    animated
                    no-active-date
                )
                    template(#day="{ scope: { timestamp } }")
                        template(v-for="order in orderMap[timestamp.date]" :key="order.id")
                            q-chip.q-calendar__ellipsis.q-my-sm(
                                clickable
                                color="purple"
                                text-color="white"
                                @click="router.visit(route('groupOrders.show', order.group_order?.id))"
                            )
                                | {{ order.group_order?.store.name }}
                                q-tooltip
                                    | ${{ order.total_price }}
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { Order, OrderPageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { QCalendarMonth } from '@quasar/quasar-ui-qcalendar';
import '@quasar/quasar-ui-qcalendar/index.css';
import { computed, ref, useTemplateRef } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<OrderPageProps>();

const calendar = useTemplateRef<QCalendarMonth>('calendar');

// 必須要綁定 v-model 才能換月份
const selectedDate = ref(new Date().toISOString().split('T')[0]);

const currentMonth = computed(() => {
    const [year, month] = selectedDate.value.split('-');
    return `${year}年${month}月`;
});

const orderMap = page.props.orders.reduce((map: Record<string, Order[]>, order: any) => {
    const date = new Date(order.created_at).toISOString().split('T')[0];
    if (!map[date]) {
        map[date] = [];
    }
    map[date].push(order);
    return map;
}, {});
</script>
