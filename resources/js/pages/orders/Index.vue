<template lang="pug">
Head(title="訂單記錄")
q-page.q-py-lg
    .container
        q-card.full-width
            q-card-section
                full-calendar(
                    :options="calendarOptions"
                )
                    template(#eventContent='props')
                        q-list
                            q-expansion-item
                                template(#header)
                                    q-item-section
                                        q-item-label
                                            | {{ props.event.extendedProps.groupOrder.store.name }}
                                            q-badge.q-ml-sm(align="middle" color="orange" text-color="black")
                                                | ${{ props.event.extendedProps.totalPrice }}
                                q-list
                                    q-item(
                                        v-for="item in props.event.extendedProps.orderItems"
                                        :key="item.id"
                                    )
                                        q-item-section
                                            q-item-label
                                                | {{ item.name }}
                                                q-badge.q-ml-sm(align="middle" color="orange" text-color="black")
                                                    | ${{ item.price }}
                                            q-item-label(v-if="item.comment" caption lines="1")
                                                | {{ item.comment }}
                                        q-item-section(side)
                                            | x{{ item.quantity }}
                                    q-separator(spaced)
                                    q-item
                                        q-item-section
                                            q-item-label
                                                | 總計
                                        q-item-section(side)
                                            q-item-label
                                                | ${{ props.event.extendedProps.totalPrice }}
                                    q-item.q-gutter-x-md
                                        q-btn(
                                            color="purple" label="團購資訊" icon="group"
                                            @click="router.visit(route('groupOrders.show', props.event.extendedProps.groupOrder.id))"
                                        )
                                        q-btn(
                                            color="purple" label="店家資訊" icon="restaurant"
                                            @click="router.visit(route('stores.show', props.event.extendedProps.groupOrder.store.id))"
                                        )
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { OrderPageProps } from '@/types';
import zhTWLocale from '@fullcalendar/core/locales/zh-tw';
import listPlugin from '@fullcalendar/list';
import FullCalendar from '@fullcalendar/vue3';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: MainLayout });

const page = usePage<OrderPageProps>();

const calendarOptions = computed(() => {
    return {
        initialView: 'listMonth',
        plugins: [listPlugin],
        locales: [zhTWLocale],
        locale: 'zh-tw',
        events: page.props.orders.map((order) => {
            return {
                date: order.created_at,
                groupOrder: order.group_order,
                orderItems: order.order_items,
                totalPrice: order.total_price,
            };
        }),
    };
});
</script>

<style scoped lang="sass">
@use 'sass:color'

:deep(.fc-list-event-graphic)
    display: none !important
:deep(.fc-list-event-time)
    vertical-align: middle !important
:deep(.fc-button-primary)
    background: $purple
    border: none !important
:deep(.fc-button-primary:disabled)
    background: color.scale($purple, $lightness: 10%) !important
:deep(.fc-button-primary:hover)
    background: color.scale($purple, $lightness: 5%) !important
:deep(.fc-button-primary:active)
    background: color.scale($purple, $lightness: 5%) !important
:deep(.fc-button-primary:focus)
    box-shadow: none !important
</style>
