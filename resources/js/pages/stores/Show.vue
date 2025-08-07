<template lang="pug">
q-page.q-py-lg
    .container
        //- 歇業警告
        template(v-if="page.props.store.is_closed")
            q-banner.text-white.bg-red.q-mb-lg(rounded)
                | 店家已歇業，無法進行訂購
                template(#avatar)
                    q-icon(name="warning")
        //- 內容
        q-card
            //- 店家名稱和資訊
            q-card-section.text-center
                h4.q-my-md.text-purple
                    | {{ page.props.store.name }}
                div
                    | 營業時間: {{ page.props.store.business_hours }}
                    br
                    | 外送條件: {{ page.props.store.delivery_conditions }}
                    br
                    | 地址: {{ page.props.store.address }}
                    br
                    | 電話: {{ page.props.store.phone }}
                div
                    q-btn(
                        v-if="page.props.store.facebook"
                        icon="fa-brands fa-facebook"
                        flat
                        round
                        color="blue"
                        size="md"
                        @click="openLink(page.props.store.facebook)"
                    )
                    q-btn(
                        v-if="page.props.store.instagram"
                        icon="fa-brands fa-instagram"
                        flat
                        round
                        color="purple"
                        size="md"
                        @click="openLink(page.props.store.instagram)"
                    )
                    q-btn(
                        v-if="page.props.store.google_map"
                        icon="location_on"
                        flat
                        round
                        color="green"
                        size="md"
                        @click="openLink(page.props.store.google_map)"
                    )
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
                        color="green"
                        icon="group_add"
                        :disable="page.props.store.is_closed"
                        :loading="loading"
                        @click="createGroupOrder"
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
import { router, usePage } from '@inertiajs/vue3';
import type { QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { ref } from 'vue';

const page = usePage<StoreShowPageProps>();
const $q = useQuasar();

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

const loading = ref(false);

const createGroupOrder = () => {
    loading.value = true;
    router.post(
        '/group-orders',
        {
            store_id: page.props.store.id,
        },
        {
            onSuccess: () => {
                loading.value = false;
                $q.notify({
                    type: 'positive',
                    message: '團購建立成功！',
                });
            },
            onError: () => {
                loading.value = false;
                $q.notify({
                    type: 'negative',
                    message: '團購建立失敗！',
                });
            },
        },
    );
};
</script>
