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
        .text-caption.text-center
            | 開團日期: {{ new Date(props.updated_at).toLocaleString() }}
            br
            | 發起人: {{ props.user.name }}
            q-badge.q-ml-sm(align="middle" color="indigo" text-color="white")
                | {{ props.user.seat_number }}
</template>

<script setup lang="ts">
import type { GroupOrderWithStore } from '@/types';

const props = defineProps<GroupOrderWithStore>();
</script>
