<template lang="pug">
q-card.card-store
    //- 卡片圖片
    q-img(:src="props.image" height="25vh")
        .absolute-center.full-height.full-width.column.justify-center.align-center(v-if="props.is_closed")
            .text-h4.text-red-6.text-center 已歇業
    //- 卡片內容
    q-card-section.card-store-title
        //- 店家名稱
        .text-h6.text-purple.text-center {{ props.name }}
    q-separator
    q-card-section
        q-list
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="access_time" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 營業時間
                    q-item-label(caption) {{ props.business_hours }}
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="delivery_dining" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 外送條件
                    q-item-label(caption) {{ props.delivery_conditions }}
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="calendar_month" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 更新日期
                    q-item-label(caption) {{ new Date(props.updated_at).toLocaleString() }}
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="group" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 總成團次數
                    q-item-label(caption) {{ props.ordered_group_orders_count }}
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="group" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 班級成團次數
                    q-item-label(caption) {{ props.course_ordered_group_orders_count }}
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="thumb_up" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 平均評價
                    q-item-label(caption) {{ props.rating_avg || 0 }} / {{ props.rating_count }} 則評價
                q-item-section(side top)
                    q-rating(
                        :model-value="props.rating_avg || 0"
                        readonly no-dimming max="5"
                        color="orange"
                        icon="star_border" icon-selected="star" icon-half="star_half"
                    )
            q-item
                q-item-section(avatar top)
                    q-avatar(icon="thumb_up" text-color="rose")
                q-item-section
                    q-item-label(lines="1") 班級平均評價
                    q-item-label(caption) {{ props.course_rating_avg || 0 }} / {{ props.course_rating_count }} 則評價
                q-item-section(side top)
                    q-rating(
                        :model-value="props.course_rating_avg || 0"
                        readonly no-dimming max="5"
                        color="orange"
                        icon="star_border" icon-selected="star" icon-half="star_half"
                    )
</template>

<script setup lang="ts">
import type { Store } from '@/types';

const props = defineProps<Store>();
</script>

<style scoped lang="sass">
.card-store-title > .text-h6,
.q-item__label.q-item__label--caption
    text-overflow: ellipsis
    overflow: hidden
    white-space: nowrap
</style>
