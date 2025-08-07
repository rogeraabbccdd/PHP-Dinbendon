<template lang="pug">
q-card(flat bordered)
    q-card-section.bg-purple.text-white
        .text-h6
            | {{ user.name }}
            q-badge.q-ml-sm(align="middle" color="indigo" text-color="white")
                | {{ user.seat_number }}
    q-card-section
        q-expansion-item(label="訂單內容")
            q-list
                q-item(v-for="item in order_items" :key="item.id")
                    q-item-section
                        q-item-label
                            | {{ item.name }}
                            q-badge.q-ml-sm(align="middle")
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
                    | ${{ total_price }}
</template>

<script setup lang="ts">
import type { Order } from '@/types';

defineProps<Order>();
</script>
