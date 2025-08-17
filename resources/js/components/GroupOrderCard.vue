<template lang="pug">
q-card.card-group-order
    //- 卡片圖片
    q-img(:src="props.store.image" height="25vh")
        template(v-if="status === 'closed'")
            .absolute-center.full-height.full-width.column.justify-center.align-center
                .text-h4.text-red-6.text-center 已關閉
        template(v-else-if="status === 'ordered'")
            .absolute-center.full-height.full-width.column.justify-center.align-center
                .text-h4.text-green.text-center 已下單
    //- 卡片內容
    q-card-section.text-center
        .text-h6.text-purple.text-center {{ props.store.name }}
    q-separator
    q-card-section
        q-list
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="calendar_month" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 開團日期
                    q-item-label(caption) {{ new Date(props.updated_at).toLocaleString() }}
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="person" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 發起人
                    q-item-label(caption)
                        | {{ props.user.name }}
                        q-badge.q-ml-sm(align="middle" color="indigo" text-color="white")
                            | {{ props.user.seat_number }}
</template>

<script setup lang="ts">
import type { GroupOrderWithStore } from '@/types';

const props = defineProps<GroupOrderWithStore>();
</script>
