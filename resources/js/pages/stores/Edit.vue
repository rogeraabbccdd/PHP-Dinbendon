<template lang="pug">
q-page.q-py-lg
    q-form(@submit.prevent="onFormSubmit")
        .column.container.q-gutter-y-lg
            //- 基本資料
            q-card
                q-card-section.text-center
                    h4.q-my-md
                        | 基本資料
                q-card-section
                    q-list
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | 店家名稱
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="name" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.name)"
                                        :error-message="form.errors.value.name"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | 地址
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="address" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.address)"
                                        :error-message="form.errors.value.address"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | 電話
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="phone" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.phone)"
                                        :error-message="form.errors.value.phone"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | 營業時間
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="business_hours" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.business_hours)"
                                        :error-message="form.errors.value.business_hours"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | 外送條件
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="delivery_conditions" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.delivery_conditions)"
                                        :error-message="form.errors.value.delivery_conditions"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | Google 地圖
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="google_map" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.google_map)"
                                        :error-message="form.errors.value.google_map"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | Facebook
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="facebook" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.facebook)"
                                        :error-message="form.errors.value.facebook"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | Instagram
                                .col-12.col-md-10
                                    q-input.q-pb-none(
                                        v-model="instagram" outlined square
                                        color="purple"
                                        :error="Boolean(form.errors.value.instagram)"
                                        :error-message="form.errors.value.instagram"
                                    )
                        q-item.q-py-lg.q-py-md-md
                            .row.justify-center.items-center.full-width
                                .col-12.col-md-2.q-mb-md.q-mb-md-none
                                    | 已歇業
                                .col-12.col-md-10
                                    q-checkbox(
                                        v-model="is_closed"
                                        color="purple"
                                        :error="Boolean(form.errors.value.is_closed)"
                                        :error-message="form.errors.value.is_closed"
                                    )
            //- 圖片
            q-card
                q-card-section.text-center
                    h4.q-my-md
                        | 店家圖片
                q-card-section
                    q-img(
                        :src="selectedImage || page.props.store.image"
                        height="25vh"
                    )
                q-card-section.text-center.q-gutter-md
                    input.hidden(
                        ref="imageInput"
                        type="file" accept="image/png, image/jpeg"
                        @change="onImageInputChange"
                    )
                    q-btn(
                        label="變更圖片"
                        icon="file_upload"
                        color="green"
                        :loading="loading"
                        @click="onEditImageClick"
                    )
                    q-btn(
                        v-if="page.props.store.image"
                        :disable="!selectedImage"
                        label="復原圖片"
                        icon="undo"
                        color="red"
                        :loading="loading"
                        @click="onRestoreImageClick"
                    )
            //- 菜單
            q-card
                q-card-section.text-center
                    h4.q-my-md
                        | 菜單
                //- 菜單表格
                q-card-section
                    q-table(
                        :rows="items"
                        :rows-per-page-options="[0]"
                        :columns="tableColumns"
                        flat
                        table-header-class="text-rose"
                    )
                        template(#body-cell-name="props")
                            q-td(:props="props")
                                q-input(
                                    v-model="props.row.value.name"
                                    outlined
                                    dense
                                    color="purple"
                                    required
                                    :bottom-slots="false"
                                    hide-hint
                                    hide-bottom-space
                                    :error="Boolean(form.errors.value[`menuItems[${props.rowIndex}].name`])"
                                )
                        template(#body-cell-price="props")
                            q-td(:props="props")
                                q-input(
                                    v-model.number="props.row.value.price"
                                    outlined
                                    dense
                                    color="purple"
                                    required
                                    type="number"
                                    :bottom-slots="false"
                                    hide-hint
                                    hide-bottom-space
                                    :error="Boolean(form.errors.value[`menuItems[${props.rowIndex}].price`])"
                                )
                        template(#body-cell-is_available="props")
                            q-td(:props="props")
                                q-checkbox(
                                    v-model="props.row.value.is_available"
                                    color="purple"
                                )
                        template(#body-cell-actions="props")
                            q-td(:props="props")
                                template(v-if="props.row.value.id < 0")
                                    q-btn(
                                        icon="delete",
                                        color="red",
                                        round
                                        flat
                                        @click="removeMenuItem(props.row.value.id)"
                                    )
                                template(v-else)
                                    .text-red 無法刪除
                //- 菜單操作
                q-card-section.text-center.q-gutter-md
                    //- q-btn(
                    //-     label="匯入 csv"
                    //-     icon="input"
                    //-     color="blue"
                    //-     @click="importMenuCSV"
                    //- )
                    //- q-btn(
                    //-     label="匯出 csv"
                    //-     icon="output"
                    //-     color="blue"
                    //-     @click="exportMenuCSV"
                    //- )
                    //- br
                    q-btn(
                        label="新增品項"
                        icon="add"
                        color="green"
                        :loading="loading"
                        @click="addMenuItem"
                    )
                    q-btn(
                        label="調漲 5 元"
                        icon="attach_money"
                        color="red"
                        :loading="loading"
                        @click="increaseMenuPrice(5)"
                    )
                    q-btn(
                        label="調漲 10 元"
                        icon="attach_money"
                        color="red"
                        :loading="loading"
                        @click="increaseMenuPrice(10)"
                    )
            //- 送出
            q-card
                q-card-section.text-center
                    h4.q-my-md
                        | 操作
                q-card-section.text-center
                    q-btn(
                        label="送出"
                        type="submit"
                        color="green"
                        icon="upload"
                        :loading="loading"
                    )
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import type { StoreShowPageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import type { QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { useField, useFieldArray, useForm } from 'vee-validate';
import { ref, useTemplateRef } from 'vue';
import * as zod from 'zod';

const page = usePage<StoreShowPageProps>();
const $q = useQuasar();

defineOptions({ layout: MainLayout });

const imageInput = useTemplateRef<HTMLInputElement>('imageInput');
const selectedImage = ref('');
let selectedImageFile: File | null = null;

const onEditImageClick = () => {
    imageInput.value?.click();
};

const onRestoreImageClick = () => {
    selectedImage.value = '';
    selectedImageFile = null;
};

const onImageInputChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target || !target.files || target.files.length === 0) return;

    selectedImage.value = URL.createObjectURL(target.files[0]);
    selectedImageFile = target.files[0];
};

const form = useForm({
    validationSchema: toTypedSchema(
        zod.object({
            name: zod.string().min(1, { message: '必填欄位' }),
            address: zod.string().min(1, { message: '必填欄位' }),
            google_map: zod.url().min(1, { message: '必填欄位' }),
            facebook: zod.url().min(1, { message: '必填欄位' }),
            instagram: zod.url().min(1, { message: '必填欄位' }),
            business_hours: zod.string().min(1, { message: '必填欄位' }),
            phone: zod.string().min(1, { message: '必填欄位' }),
            delivery_conditions: zod.string().min(1, { message: '必填欄位' }),
            is_closed: zod.boolean(),
            menuItems: zod.array(
                zod.object({
                    name: zod.string().min(1, { message: '必填欄位' }),
                    price: zod.number().min(1, { message: '必填欄位' }),
                    is_available: zod.boolean(),
                    id: zod.number(),
                }),
            ),
        }),
    ),
    initialValues: {
        name: '',
        address: '',
        google_map: 'https://maps.google.com/',
        facebook: 'https://facebook.com/',
        instagram: 'https://instagram.com/',
        business_hours: '',
        phone: '',
        delivery_conditions: '',
        is_closed: false,
        menuItems: [],
    },
});
const { value: name } = useField<string>('name');
const { value: address } = useField<string>('address');
const { value: google_map } = useField<string>('google_map');
const { value: facebook } = useField<string>('facebook');
const { value: instagram } = useField<string>('instagram');
const { value: business_hours } = useField<string>('business_hours');
const { value: phone } = useField<string>('phone');
const { value: delivery_conditions } = useField<string>('delivery_conditions');
const { value: is_closed } = useField<boolean>('is_closed');
const { fields: items, push: itemsPush, remove: itemsRemove } = useFieldArray('menuItems');

if (page.props.store) {
    form.setValues({
        name: page.props.store.name,
        address: page.props.store.address,
        google_map: page.props.store.google_map,
        facebook: page.props.store.facebook,
        instagram: page.props.store.instagram,
        business_hours: page.props.store.business_hours,
        phone: page.props.store.phone,
        delivery_conditions: page.props.store.delivery_conditions,
        is_closed: page.props.store.is_closed,
        menuItems: page.props.menuItems.map((item) => ({
            name: item.name,
            price: item.price,
            is_available: item.is_available,
            id: item.id,
        })),
    });
}

// 新增商品的 ID 使用負數，避免與資料庫中的 ID 衝突
let newItemId = -1;

const addMenuItem = () => {
    itemsPush({
        name: '',
        price: 0,
        is_available: true,
        id: newItemId--,
    });
};

// const importMenuCSV = () => {};

// const exportMenuCSV = () => {};

const increaseMenuPrice = (amount: number) => {
    form.values.menuItems!.forEach((item, i) => {
        form.setFieldValue(`menuItems[${i}].price`, (item.price + amount) as never);
    });
};

const removeMenuItem = (id: number) => {
    const idx = form.values.menuItems!.findIndex((item): boolean => item.id === id);
    if (idx !== -1) {
        itemsRemove(idx);
    }
};

const tableColumns: QTableColumn[] = [
    {
        name: 'name',
        label: '品項',
        field: (row) => row.value.name,
        sortable: true,
        align: 'left',
    },
    {
        name: 'price',
        label: '價格',
        field: (row) => row.value.price,
        sortable: true,
        align: 'left',
    },
    {
        name: 'is_available',
        label: '供應中',
        field: (row) => row.value.is_available,
        sortable: true,
        align: 'center',
    },
    {
        name: 'actions',
        label: '操作',
        field: 'actions',
        sortable: false,
        align: 'center',
    },
];

const loading = ref(false);

const onFormSubmit = form.handleSubmit(async (values) => {
    loading.value = true;
    await new Promise((resolve) => {
        router.post(
            route('stores.edit.submit', page.props.store.id),
            { ...values, image: selectedImageFile },
            {
                onSuccess: () => {
                    resolve(undefined);
                    $q.notify({
                        type: 'positive',
                        message: '更新成功！',
                    });
                },
                onError: (errors) => {
                    form.setErrors(errors);
                    resolve(undefined);
                },
            },
        );
    });
    loading.value = false;
});
</script>
