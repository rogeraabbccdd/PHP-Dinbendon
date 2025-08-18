<template lang="pug">
q-layout(view='hHh lpR fff')
    //- Header
    q-header(elevated)
        .container
            //- 導覽列
            q-toolbar.text-white
                q-toolbar-title.text-weight-bold
                    q-icon(name="fastfood")
                    | &nbsp;DinBenDon
                //- 導覽列按鈕 (登入)
                template(v-if="isLoggedIn")
                    //- PC
                    template(v-if="$q.screen.gt.sm")
                        q-tabs(
                            :model-value="routeTab"
                            inline-label
                        )
                            q-tab(
                                name="stores" icon="restaurant" stretch flat label="店家"
                                @click="router.get(route('stores'))"
                            )
                            q-tab(
                                name="groupOrders" icon="group" stretch flat label="團購"
                                @click="router.get(route('groupOrders'))"
                            )
                            q-btn-dropdown(stretch flat icon="person" :label="page.props.auth?.user?.name")
                                q-list
                                    q-item(v-close-popup clickable @click="router.get(route('orders'))")
                                        q-item-section 訂單記錄
                                    q-item(v-close-popup clickable @click="logout")
                                        q-item-section 登出
                    //- 手機
                    template(v-else)
                        q-btn(icon="menu" stretch flat @click="drawer = true")
                //- 導覽列按鈕 (未登入)
                template(v-else)
                    q-btn(icon="person" stretch flat label="未登入")
    //- 手機側邊攔
    template(v-if="!$q.screen.gt.sm")
        q-drawer(
            v-model="drawer"
            dark
        )
            q-scroll-area.fit
                q-list(padding)
                    template(v-if="isLoggedIn")
                        q-item-label(header)
                            | {{ page.props.auth?.user?.name }}
                        q-item(clickable ripple @click="drawer = false; router.get(route('stores'))")
                            q-item-section(avatar)
                                q-icon(name="restaurant")
                            q-item-section
                                |店家
                        q-item(clickable ripple @click="drawer = false; router.get(route('groupOrders'))")
                            q-item-section(avatar)
                                q-icon(name="group")
                            q-item-section
                                | 團購
                        q-item(clickable ripple @click="drawer = false; router.get(route('orders'))")
                            q-item-section(avatar)
                                q-icon(name="list")
                            q-item-section
                                | 訂單記錄
                        q-item(clickable ripple @click="drawer = false; logout()")
                            q-item-section(avatar)
                                q-icon(name="logout")
                            q-item-section
                                | 登出
                    template(v-else)
                        q-item-label(header)
                            | 未登入
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
        br
        github-button.q-mx-sm(
            href="https://github.com/rogeraabbccdd/PHP-Dinbendon"
            data-color-scheme="no-preference: light; light: light; dark: light;"
            data-icon="octicon-star" data-show-count="true"
            aria-label="Star buttons/github-buttons on GitHub"
        )
            | Star
        github-button.q-mx-sm(
            href="https://github.com/rogeraabbccdd/PHP-Dinbendon/fork"
            data-color-scheme="no-preference: light; light: light; dark: light;"
            data-icon="octicon-repo-forked" data-show-count="true"
            aria-label="Fork buttons/github-buttons on GitHub"
        )
            | Fork
</template>

<script setup lang="ts">
import type { AuthPageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';
import GithubButton from 'vue-github-button';

const page = usePage<AuthPageProps>();
const $q = useQuasar();

const year = new Date().getFullYear();

const isLoggedIn = computed(() => {
    return page.props.auth.user !== null;
});

const drawer = ref(false);

const logout = () => {
    $q.dialog({
        title: '你確定?',
        message: '你確定要登出嗎?',
        cancel: true,
        persistent: true,
    }).onOk(() => {
        router.post(route('logout'));
        drawer.value = false;
    });
};

const routeTab = computed(() => {
    const currentRoute = route().current();
    console.log('Current Route:', currentRoute);
    return currentRoute.includes('stores') ? 'stores' : currentRoute.includes('groupOrders') ? 'groupOrders' : '';
});
</script>

<style lang="sass" scoped>
.q-header
    background-color: #00bcd4
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
