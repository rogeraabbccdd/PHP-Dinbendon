<template lang="pug">
q-card.card-store
    //- 卡片圖片
    q-img(:src="props.image" height="25vh")
        .absolute-center.full-height.full-width.column.justify-center.align-center(v-if="props.is_closed")
            .text-h4.text-center 已歇業
    //- 卡片內容
    q-card-section.card-store-title
        //- 店家名稱
        .text-h6.text-purple.text-center {{ props.name }}
    q-separator
    q-card-section
        .text-caption.text-center
            | 營業時間: {{ props.business_hours }}
            br
            | 外送條件: {{ props.delivery_conditions }}
            br
            | 更新日期: {{ new Date(props.updated_at).toLocaleString() }}
            br
            | 總成團次數: {{ props.ordered_group_orders_count }}
            br
            | 班級成團次數: {{ props.course_ordered_group_orders_count }}
</template>

<script setup lang="ts">
import type { Store } from '@/types';

const props = defineProps<Store>();
</script>

<style scoped lang="sass">
.card-store-title > .text-h6
    text-overflow: ellipsis
    overflow: hidden
</style>
