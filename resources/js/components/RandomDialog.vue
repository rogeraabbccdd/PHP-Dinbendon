<!--
    CS:GO Style Raffle System
    https://codepen.io/re3ker/pen/VYBXBj
-->
<template lang="pug">
    q-dialog(v-model="model")
        q-card(style="width: 700px; max-width: 80vw;")
            q-card-section.text-center
                h4.q-my-md
                    | 隨機店家
            q-card-section
                .rollbox
                    .line
                    .roller-container(ref="loadout" :style="{ left: rollerLeft + 'px' }")
                        .roller-item(v-for="(store, index) in displayStores" :key="index")
                            .roller
                                q-img.full-width.full-height(:src="store.image")
                                    .absolute-bottom.text-subtitle1.text-center
                                        | {{ store.name }}
            q-card-section.text-center
                q-btn.q-my-sm.q-mx-sm(
                    :disabled="isRolling"
                    color="green"
                    label="開始隨機"
                    icon="shuffle"
                    @click="roll"
                )
                q-btn.q-my-sm.q-mx-sm(
                    color="purple"
                    label="前往訂餐"
                    icon="restaurant"
                    :disable="winner === null"
                    @click="router.visit(route('stores.show', winner?.id))"
                )
</template>

<script setup lang="ts">
import type { Store } from '@/types';
import { router } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const props = defineProps<{
    stores: Store[];
}>();

const model = defineModel({ type: Boolean });

const displayStores = ref<Store[]>([]);
const isRolling = ref(false);
const error = ref('');
const winner = ref<Store | null>(null);
const rollerLeft = ref(0);
const loadout = ref<HTMLElement | null>(null);

const roll = async () => {
    isRolling.value = true;
    winner.value = null;
    error.value = '';

    if (loadout.value) {
        loadout.value.style.transition = 'none';
    }

    rollerLeft.value = window.innerWidth * 0.5;

    const insert_times = props.stores.length < 10 ? 5 : 2;
    const duration_time = 5000;

    const shuffled = [];
    for (let i = 0; i < insert_times; i++) {
        shuffled.push(...shuffle([...props.stores]));
    }
    displayStores.value = shuffled;

    await nextTick();

    const scrollSize = shuffled.length * 192;
    const diff = Math.round(scrollSize / 2);
    const random_diff = randomEx(diff - 300, diff + 300);

    if (loadout.value) {
        loadout.value.style.transition = `left ${duration_time}ms cubic-bezier(0.25, 0.1, 0.25, 1)`;
        rollerLeft.value = -random_diff;
    }

    setTimeout(() => {
        isRolling.value = false;
        const center = window.innerWidth / 2;
        if (loadout.value) {
            const children = loadout.value.children;
            for (let i = 0; i < children.length; i++) {
                const child = children[i] as HTMLElement;
                const rect = child.getBoundingClientRect();
                if (rect.left < center && rect.right > center) {
                    winner.value = displayStores.value[i];
                    break;
                }
            }
        }
    }, duration_time);
};

const shuffle = (array: any[]) => {
    let counter = array.length;
    while (counter > 0) {
        const index = Math.floor(Math.random() * counter);
        counter--;
        [array[counter], array[index]] = [array[index], array[counter]];
    }
    return array;
};

const randomEx = (min: number, max: number) => {
    return Math.floor(Math.random() * (max - min + 1) + min);
};
</script>

<style lang="sass" scoped>
.rollbox
    height: 200px
    background: #000
    border: 1px solid #616161
    overflow: hidden
    position: relative
    padding: 0

.roller-container
    position: absolute
    top: 10px
    height: 180px
    display: flex

.roller-item
    width: 180px
    margin-right: 10px
    box-shadow: 0 0 5px 0 black
    height: 180px

.roller
    position: relative
    display: block
    height: 100%
    text-align: center
    color: white

.roller div
    display: block
    height: 50px
    position: absolute
    bottom: 0
    width: 100%
    left: 0

#roll
    margin-top: 30px

.line
    width: 2px
    height: 198px
    top: 1px
    left: 50%
    position: absolute
    background: #FFCE0A
    opacity: 0.6
    z-index: 2
</style>
