<template>
    <Head title="Accounts" />

    <AuthLayout>
        <div class="grid grid-cols-1 gap-6 sm:gap-8 lg:grid-cols-4">
            <!-- Account Settings -->
            <div class="col-span-2 w-full space-y-4">
                <form @submit.prevent="uploadStockAnalysisDividendData">
                    <Card>
                        <p class="text-sm text-text">Upload StockAnalysis Dividend Data (CSV Only)</p>

                        <div class="flex items-center justify-between gap-6 sm:gap-8">
                            <!-- Names -->
                            <Select color="inputForeground" v-model="uploadStockAnalysisDividendDataForm.securityUid">
                                <option v-for="security in securities" :value="security.uid">
                                    {{ security.ticker }}
                                </option>
                            </Select>

                            <TextInput
                                ref="fileInput"
                                type="file"
                                color="inputForeground"
                                size="normal"
                            />
                        </div>

                        <!-- Cancel - Save -->
                        <div class="col-span-full flex items-center justify-end gap-2">
                            <Button size="normal" color="primaryForeground" type="submit">Upload</Button>
                        </div>
                    </Card>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import Select from '@/components/ui/Select.vue';

export default {
    components: {
        Select,
        TextLabel,
        TextInput,
        Button,
        Card,
        AuthLayout,
        Head,
    },

    props: {
        securities: Object,
    },

    data() {
        return {
            uploadStockAnalysisDividendDataForm: useForm({
                securityUid: null,
            }),
        };
    },

    methods: {
        uploadStockAnalysisDividendData() {
            const file = this.$refs.fileInput?.$el?.files?.[0];

            if (!file) {
                toast.error('Please select a file.');
                return;
            }

            const formData = new FormData();
            formData.append('securityUid', this.uploadStockAnalysisDividendDataForm.securityUid);
            formData.append('file', file);

            router.post(route('admin.upload-stock-analysis-dividend-data'), formData, {
                preserveScroll: true,
                forceFormData: true,
                onSuccess: () => toast.success('File uploaded'),
                onError: (errors) => {
                    Object.values(errors).forEach((message) => toast.error(message));
                },
            });
        }
    },
};
</script>
