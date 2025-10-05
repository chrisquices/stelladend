<template>
    <Head title="Accounts" />

    <AuthLayout>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8">
            <!-- Accounts Cards -->
            <Card
                v-for="account in accounts"
                class="relative flex cursor-pointer flex-col !space-y-0 border-2 border-transparent transition-all duration-300 hover:bg-hover"
                @click="toggleManageAccountModal(account)"
            >
                <div class="flex-shrink-0 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-left tracking-widest text-text-muted">{{ account.name }}</p>
                        <p class="text-left tracking-widest text-text-muted">N° {{ account.reference }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="font-mono text-xl font-medium tracking-widest">{{ account.balanceFormatted }}</p>
                        <p v-if="account.secondaryCurrency" class="font-mono text-xl font-medium tracking-widest text-text-muted">
                            {{ account.secondaryCurrency.balanceFormatted }}
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Add new Account -->
            <Card
                class="relative flex cursor-pointer flex-col !space-y-0 border-2 border-transparent transition-all duration-300 hover:bg-hover"
                @click="storeAccount()"
            >
                <div class="flex-shrink-0 space-y-2 h-full">
                    <div class="flex items-center justify-center h-full">
                        <p class="flex w-full items-center justify-center gap-2 text-center tracking-widest text-text-muted">
                            <i class="lni lni-plus !text-4xl"></i>
                            Add a new account
                        </p>
                    </div>
                </div>
            </Card>
        </div>

        <!-- Manage Account Modal -->
        <Modal v-model:isOpen="manageAccountModal.isOpen" size="medium">
            <form @submit.prevent="updateAccount">
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <p class="truncate font-medium whitespace-nowrap text-text">{{ manageAccountModal.form.name }}</p>

                        <div
                            class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                            @click="toggleManageAccountModal()"
                        >
                            <i class="lni lni-xmark"></i>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="flex flex-col gap-2">
                        <TextLabel>Name</TextLabel>
                        <TextInput
                            color="inputForeground"
                            ref="manageAccountName"
                            v-model="manageAccountModal.form.name"
                            size="normal"
                            placeholder="My Savings Account"
                            type="text"
                        />
                        <InputError :error="manageAccountModal.form.errors.name" />
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Balance -->
                        <div class="flex flex-grow flex-col gap-2">
                            <TextLabel>Balance</TextLabel>
                            <TextInput
                                color="inputForeground"
                                v-model="manageAccountModal.form.balance"
                                size="normal"
                                placeholder="1000"
                                type="text"
                                :isMoney="true"
                            />
                            <InputError :error="manageAccountModal.form.errors.balance" />
                        </div>

                        <div v-if="currencies.length > 1" class="flex w-24 flex-col gap-2">
                            <TextLabel></TextLabel>
                            <div class="flex w-full justify-start rounded text-text">
                                <Button
                                    size="normal"
                                    :color="manageAccountModal.form.currencyId === 1 ? 'primaryForeground' : 'transparent'"
                                    class="w-full"
                                    @click="updateSelectedCurrency(currencies[0].id)"
                                    tabindex="-1"
                                >
                                    {{ currencies[0].symbol }}
                                </Button>
                                <Button
                                    v-if="currencies.length > 1"
                                    size="normal"
                                    :color="manageAccountModal.form.currencyId === currencies[1].id ? 'primaryForeground' : 'transparent'"
                                    class="w-full"
                                    @click="updateSelectedCurrency(currencies[1].id)"
                                    tabindex="-1"
                                >
                                    {{ currencies[1].symbol }}
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Reference -->
                    <div class="flex flex-col gap-2">
                        <TextLabel>Reference</TextLabel>
                        <TextInput
                            color="inputForeground"
                            v-model="manageAccountModal.form.reference"
                            size="normal"
                            placeholder="82946384"
                            type="text"
                        />
                        <InputError :error="manageAccountModal.form.errors.reference" />
                    </div>

                    <!-- Cancel | Save Account -->
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <Button size="normal" color="transparent" onhover="destructive" @click="toggleDeleteAccountAlertDialog" tabindex="-1">
                                <i class="lni lni-trash-1 !text-xl"></i>
                            </Button>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleManageAccountModal()" tabindex="-1"
                                >Cancel
                            </Button>
                            <Button size="normal" color="primaryForeground" type="submit">
                                <i class="lni lni-floppy-disk-1"></i>
                                Save Account
                            </Button>
                        </div>
                    </div>
                </div>
            </form>
        </Modal>

        <!-- Delete Account Alert Dialog -->
        <AlertDialog :show="deleteAccountAlertDialog.isOpen">
            <template #title>
                <p>Confirmation Required</p>
            </template>
            <template #content>
                <p>
                    Are you sure you want to delete <b>{{ manageAccountModal.form.name }}</b
                    >?
                </p>
                <br />
                <p class="italic">All of your movements will also be deleted.</p>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleDeleteAccountAlertDialog">Cancel</Button>
                <Button size="normal" color="destructive" @click="deleteAccount()">Delete {{ manageAccountModal.form.name }}</Button>
            </template>
        </AlertDialog>
    </AuthLayout>
</template>

<script>
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import DivThatLooksLikeTextInput from '@/components/ui/DivThatLooksLikeTextInput.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import InputError from '@/components/ui/InputError.vue';
import Modal from '@/components/ui/Modal.vue';
import RadioInput from '@/components/ui/RadioInput.vue';
import Select from '@/components/ui/Select.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { todayInYmd, ymdToReadableDate } from '@/utils/dateHelper.js';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export default {
    components: {
        DivThatLooksLikeTextInput,
        RadioInput,
        AlertDialog,
        InputError,
        Select,
        TextInput,
        Modal,
        TextLabel,
        Button,
        SortIcon,
        TableCell,
        Table,
        TableHeader,
        TableRow,
        TableHeadRow,
        Link,
        Card,
        AuthLayout,
        Head,
        Dropdown,
    },

    props: {
        accounts: Object,
        periods: Object,
        categories: Object,
        currencies: Object,
    },

    data() {
        return {
            // Accounts
            manageAccountModal: {
                isOpen: false,
                form: useForm({
                    currencyId: null,
                    uid: null,
                    name: null,
                    balance: null,
                    reference: null,
                }),
            },

            deleteAccountAlertDialog: {
                isOpen: false,
            },
        };
    },

    computed: {
    },

    watch: {
    },

    mounted() {
    },

    methods: {

        // Accounts
        toggleManageAccountModal(account = null) {
            this.resetManageAccountForm();

            if (account) {
                this.populateManageAccountForm(account);
            }

            this.manageAccountModal.isOpen = !this.manageAccountModal.isOpen;

            this.$nextTick(() => {
                this.$refs.manageAccountName?.focus?.();
            });
        },

        resetManageAccountForm() {
            this.manageAccountModal.form.currencyId = null;
            this.manageAccountModal.form.uid = null;
            this.manageAccountModal.form.name = null;
            this.manageAccountModal.form.balance = null;
            this.manageAccountModal.form.reference = null;
        },

        populateManageAccountForm(account) {
            this.manageAccountModal.form.currencyId = account.currencyId;
            this.manageAccountModal.form.uid = account.uid;
            this.manageAccountModal.form.name = account.name;
            this.manageAccountModal.form.balance = account.balanceToDisplayInClient;
            this.manageAccountModal.form.reference = account.reference;
        },

        toggleDeleteAccountAlertDialog() {
            this.deleteAccountAlertDialog.isOpen = !this.deleteAccountAlertDialog.isOpen;
        },

        storeAccount() {
            router.post(route('accounts.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Account added');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        updateAccount() {
            const form = this.manageAccountModal.form;

            form.patch(route('accounts.update', { accountUid: form.uid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleManageAccountModal();

                    toast.success('Account updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        deleteAccount() {
            router.delete(route('accounts.delete', { accountUid: this.manageAccountModal.form.uid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleDeleteAccountAlertDialog();
                    this.toggleManageAccountModal();

                    toast.success('Account deleted');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        updateSelectedCurrency(currencyId) {
            this.manageAccountModal.form.currencyId = currencyId;
        },
    },
};
</script>
