<template lang="pug">
q-page.q-my-lg
    .container
        //- 歇業警告
        template(v-if="!page.props.store.is_open")
            q-banner.text-white.bg-red.q-mb-lg(rounded)
                | 店家已歇業
                template(#avatar)
                    q-icon(name="warning")
        //- 內容
        q-card
            //- 店家名稱
            q-card-section
                .text-h4.text-center
                    | {{ page.props.store.name }}
                .text-subtitle1.text-center
                    | 地址: {{ page.props.store.address }}
                    q-btn(
                        icon="location_on"
                        flat round
                        color="green"
                        @click="openLink(page.props.store.google_map)"
                    )
                .text-subtitle1.text-center
                    | 電話: {{ page.props.store.phone }}
            //- 店家圖片
            q-card-section
                q-img(:src="page.props.store.image" height="25vh")
            //- 菜單
            q-card-section
                q-table(
                    :rows="page.props.menuItems" :columns="tableColumns"
                    row-key="id"
                    flat
                    table-header-class="text-rose"
                    :rows-per-page-options="[20, 50, 100, 0]"
                )
            //- 日期
            q-card-section
                .text-caption.text-grey.text-center
                    | 最後更新日期: {{ new Date(page.props.store.updated_at).toLocaleString() }}
                    br
                    | 店家建立日期: {{ new Date(page.props.store.created_at).toLocaleString() }}
            //- 動作按鈕
            q-card-section
                .text-center.q-gutter-md
                    q-btn(
                        label="開團訂購"
                        color="rose"
                        icon="group_add"
                        :disable="!page.props.store.is_open"
                    )
                    q-btn(
                        label="編輯資訊"
                        color="purple"
                        icon="edit"
                    )
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { StoreShowPageProps } from '@/types';
import { openLink } from '@/utils/url';
import { usePage } from '@inertiajs/vue3';
import type { QTableColumn } from 'quasar';

const page = usePage<StoreShowPageProps>();

defineOptions({ layout: MainLayout });

const tableColumns: QTableColumn[] = [
    {
        name: 'name',
        label: '品項',
        field: 'name',
        sortable: true,
        align: 'left',
    },
    {
        name: 'price',
        label: '價格',
        field: 'price',
        sortable: true,
        align: 'left',
    },
];
</script>
