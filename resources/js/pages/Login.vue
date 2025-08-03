<template lang="pug">
q-page.column.flex-center
    .container
        .row.justify-center
            .col-9.col-md-6.col-lg-4.col-xl-3
                q-form(@submit.prevent="onFormSubmit")
                    q-card.q-pa-lg
                        //- 卡片標題
                        q-card-section.text-h4.text-center.text-weight-light.q-my-sm
                            | 登入
                        //- 登入表單
                        q-card-section
                            q-input(
                                v-model="student_id"
                                auto-complete="username" placeholder="學號" color="rose"
                                :error="Boolean(form.errors.value.student_id)"
                                :error-message="form.errors.value.student_id"
                            )
                                template(#before)
                                    q-icon(name="face")
                            q-input(
                                v-model="password" placeholder="密碼" type="password" color="rose"
                                auto-complete="current-password"
                                :error="Boolean(form.errors.value.password)"
                                :error-message="form.errors.value.password"
                            )
                                template(#before)
                                    q-icon(name="lock")
                        //- 登入按鈕
                        q-card-section(align="center")
                            q-btn.q-px-lg.q-py-sm(type="submit" size="md" color="rose" label="登入" :loading="form.isSubmitting.value")
</template>

<script setup lang="ts">
import MainLayout from '@/layouts/MainLayout.vue';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useQuasar } from 'quasar';
import { useField, useForm } from 'vee-validate';
import * as zod from 'zod';

defineOptions({ layout: MainLayout });

const $q = useQuasar();

const form = useForm({
    validationSchema: toTypedSchema(
        zod.object({
            student_id: zod.string().min(1, { message: '必填欄位' }),
            password: zod.string().min(1, { message: '必填欄位' }),
        }),
    ),
    initialValues: {
        student_id: '',
        password: '',
    },
});
const { value: student_id } = useField<string>('student_id');
const { value: password } = useField<string>('password');

const onFormSubmit = form.handleSubmit(async (values) => {
    return new Promise((resolve) => {
        router.post('/login', values, {
            onSuccess: () => {
                resolve(undefined);
                $q.notify({
                    type: 'positive',
                    message: '登入成功！',
                });
            },
            onError: (errors) => {
                form.setErrors(errors);
                resolve(errors);
            },
        });
    });
});
</script>
