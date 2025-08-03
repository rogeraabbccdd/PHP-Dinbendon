<template lang="pug">
q-card.card-store
    //- 卡片圖片
    q-img(:src="props.image" height="25vh")
        .absolute-center.full-height.full-width.column.justify-center.align-center(v-if="!props.is_open")
            .text-h4.text-center 已關閉
    //- 卡片內容
    q-card-section.card-store-title
        //- 店家名稱
        .text-h6.text-center {{ props.name }}
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
