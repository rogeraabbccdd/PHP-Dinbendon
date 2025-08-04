<template lang="pug">
q-layout(view='hHh lpR fff')
    //- Header
    q-header(bordered)
        .container
            //- 導覽列
            q-toolbar.text-white
                q-toolbar-title.text-weight-bold
                    q-icon(name="fastfood")
                    | &nbsp;DinBenDon
                //- 導覽列按鈕 (登入)
                template(v-if="isLoggedIn")
                    q-btn.self-stretch(icon="restaurant" flat label="店家")
                    q-btn.self-stretch(icon="group" flat label="開團")
                    q-btn-dropdown(stretch flat icon="person" :label="page.props.auth?.user?.name")
                        q-list
                            q-item(v-close-popup clickable @click="logout")
                                q-item-section 登出
                //- 導覽列按鈕 (未登入)
                template(v-else)
                    q-btn.self-stretch(icon="person" flat label="未登入")
    //- 內容
    q-page-container
        //- 滿版背景
        #bg
        //- 主要內容區域
        slot
        //- 回到頂部按鈕
        q-page-scroller(position="bottom-right" :scroll-offset="150" :offset="[18, 18]")
            q-btn(fab icon="keyboard_arrow_up" color="rose" text-color="white")
    //- Footer
    q-footer.bg-transparent.text-center.q-py-md
        span.text-yellow 請使用 Chrome 瀏覽本系統
        br
        | &copy;{{ year }} PHP資料庫網頁設計班
</template>

<script setup lang="ts">
import type { AuthPageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { computed } from 'vue';

const page = usePage<AuthPageProps>();
const $q = useQuasar();

const year = new Date().getFullYear();

const isLoggedIn = computed(() => {
    return page.props.auth.user !== null;
});

const logout = () => {
    $q.dialog({
        title: '你確定?',
        message: '你確定要登出嗎?',
        cancel: true,
        persistent: true,
    }).onOk(() => {
        router.post('/logout');
    });
};
</script>

<style lang="sass" scoped>
.q-header
    background-color: #00bcd4
    border-bottom: 1px solid #fff
.q-toolbar__title
    color: lemonchiffon
#bg
    background-attachment: fixed
    background-repeat: no-repeat
    background-position: center
    background-size: cover
    background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),url('$/images/background.webp')
    display: block
    height: 100vh
    width: 100vw
    position: fixed
    left: 0px
    right: 0px
    z-index: -1
    filter: blur(5px)
</style>
