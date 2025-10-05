<template>
    <div>
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0">
            <div class="w-10 flex-shrink-0 hidden sm:block"></div>
            <div class="flex flex-grow items-center justify-center gap-4">
                <Link
                    v-if="selectedWatchlist"
                    v-for="watchlist in watchlists"
                    :href="route('watchlists.show', { watchlistUid: watchlist.uid })"
                    class="flex h-10 cursor-pointer items-center justify-center gap-2 rounded-full px-3 py-2 text-sm tracking-wide text-text-muted transition-all duration-300 hover:bg-primary/20 sm:px-4 sm:py-1"
                    :class="{
                        '!text-text-inverted bg-primary': selectedWatchlist.uid === watchlist.uid,
                    }"
                >
                    {{ watchlist.name }}
                </Link>
            </div>
            <div class="flex w-10 flex-shrink-0 items-end justify-center sm:justify-end">
                <Button size="normal" color="transparent" @click="toggleAddWatchlistModal">
                    <i class="lni lni-plus !text-2xl !text-text-muted"></i>
                </Button>

                <Button size="normal" color="transparent" @click="toggleEditWatchlistModal">
                    <i class="lni lni-brush-2 !text-2xl !text-text-muted"></i>
                </Button>
            </div>
        </div>

        <!-- Add Watchlist -->
        <Modal v-model:isOpen="addWatchlistModal.isOpen" size="large">
            <form @submit.prevent="storeWatchlist" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Add a new Watchlist</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleAddWatchlistModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Name -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Name</TextLabel>
                    <TextInput
                        color="inputForeground"
                        ref="addWatchlistModalFormName"
                        v-model="addWatchlistModal.form.name"
                        size="normal"
                        placeholder="My New Watchlist"
                        type="text"
                    />
                    <InputError :error="addWatchlistModal.form.errors.name" />
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleAddWatchlistModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Edit Watchlist -->
        <Modal v-if="selectedWatchlist" v-model:isOpen="editWatchlistModal.isOpen" size="large">
            <form @submit.prevent="updateWatchlist" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Edit {{ selectedWatchlist.name }}</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleEditWatchlistModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Name -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Name</TextLabel>
                    <TextInput
                        color="inputForeground"
                        ref="editWatchlistModalFormName"
                        v-model="editWatchlistModal.form.name"
                        size="normal"
                        placeholder="My Watchlist"
                        type="text"
                    />
                    <InputError :error="editWatchlistModal.form.errors.name" />
                </div>

                <!-- Actions -->
                <div class="col-span-full flex flex-col gap-2">
                    <div class="space-y-8">
                        <Card class="bg-card-foreground relative flex cursor-pointer items-centergap-6 sm:gap-8 !space-y-4" @click="toggleConfirmDeleteWatchlistAlertDialog">
                            <div class="w-14">
                                <i class="lni lni-trash-1 !text-4xl"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-bold">Delete Watchlist</p>
                                <p class="text-sm text-text-muted">This will delete all of your watchlist data.</p>
                                <p class="text-sm text-text-muted italic">This action is irreversible.</p>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleEditWatchlistModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Confirm Delete Watchlist Alert Dialog -->
        <AlertDialog v-if="selectedWatchlist" :show="confirmDeleteWatchlistAlertDialog.isOpen">
            <template #title>
                <p>Confirmation Required</p>
            </template>
            <template #content>
                <p>
                    Are you sure you want to delete <b>{{ selectedWatchlist.name }}</b
                >?
                </p>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleConfirmDeleteWatchlistAlertDialog">Cancel</Button>
                <Button size="normal" color="destructive" @click="deleteWatchlist(selectedWatchlist.id)">Delete {{ selectedWatchlist.name }}</Button>
            </template>
        </AlertDialog>

        <!-- Upgrade to Premium Alert Dialog -->
        <AlertDialog v-if="!isPremium" :show="upgradeToPremiumAlertDialog.isOpen">
            <template #title>
                <p>Upgrade to Premium</p>
            </template>
            <template #content>
                <div class="space-y-2">
                    <p>Free users are limited to 1 watchlist.</p>
                    <p>Upgrade to Premium for <b>unlimited watchlists</b>.</p>
                </div>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleUpgradeToPremiumAlertDialog">Close</Button>
                <Link :href="route('premium.index')">
                    <Button size="normal" color="premium">Get Premium</Button>
                </Link>
            </template>
        </AlertDialog>
    </div>
</template>

<script>
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import DivThatLooksLikeTextInput from '@/components/ui/DivThatLooksLikeTextInput.vue';
import InputError from '@/components/ui/InputError.vue';
import Modal from '@/components/ui/Modal.vue';
import Switch from '@/components/ui/Switch.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export default {
    components: {
        InputError,
        AlertDialog,
        Card,
        Switch,
        Button,
        DivThatLooksLikeTextInput,
        TextInput,
        Modal,
        TextLabel,
        Link,
    },

    props: {
        watchlists: Object,
        selectedWatchlist: {
            type: Object,
            required: false,
        },
    },

    data() {
        return {
            upgradeToPremiumAlertDialog: {
                isOpen: false,
            },

            addWatchlistModal: {
                isOpen: false,
                form: useForm({
                    name: null,
                }),
            },

            editWatchlistModal: {
                isOpen: false,
                form: useForm({
                    watchlistUid: this.selectedWatchlist?.uid,
                    name: this.selectedWatchlist?.name,
                }),
            },

            confirmDeleteWatchlistAlertDialog: {
                isOpen: false,
            },
        };
    },

    computed: {
        isPremium() {
            return usePage().props.auth?.user.isPremium;
        },
    },

    watch: {
        'editWatchlistModal.isOpen'(val) {
            if (val) {
                this.editWatchlistModal.form.watchlistUid = this.selectedWatchlist.uid;
                this.editWatchlistModal.form.name = this.selectedWatchlist.name;
            }
        },
    },

    mounted() {
        this.interval = setInterval(() => {
            router.reload({
                preserveScroll: true,
                preserveState: true,
            });
        }, 30000);
    },

    beforeUnmount() {
        clearInterval(this.interval);
    },

    methods: {
        // Add Watchlist Modal
        toggleAddWatchlistModal() {
            if (!this.isPremium) {
                this.toggleUpgradeToPremiumAlertDialog()
                return
            }

            this.addWatchlistModal.isOpen = !this.addWatchlistModal.isOpen;

            if (this.addWatchlistModal.isOpen) {
                this.$nextTick(() => {
                    this.$refs.addWatchlistModalFormName?.focus?.();
                });
            }
        },

        resetAddWatchlistForm() {
            this.addWatchlistModal.form.name = null;
        },

        storeWatchlist() {
            const form = this.addWatchlistModal.form;

            if (!form.name) {
                toast.error('Please complete all required fields');
                return;
            }

            form.post(route('watchlists.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleAddWatchlistModal();
                    this.resetAddWatchlistForm();

                    toast.success('Watchlist created');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Edit Watchlist Modal
        toggleEditWatchlistModal() {
            this.editWatchlistModal.isOpen = !this.editWatchlistModal.isOpen;

            if (this.editWatchlistModal.isOpen) {
                this.$nextTick(() => {
                    this.$refs.editWatchlistModalFormName?.focus?.();
                });
            }
        },

        resetEditWatchlistForm() {
            this.editWatchlistModal.form.name = null;
        },

        updateWatchlist() {
            const form = this.editWatchlistModal.form;

            if (!form.name) {
                toast.error('Please complete all required fields');
                return;
            }

            form.patch(route('watchlists.update', { watchlistUid: this.selectedWatchlist.uid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleEditWatchlistModal();
                    this.resetEditWatchlistForm();

                    toast.success('Watchlist updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        deleteWatchlist() {
            router.delete(route('watchlists.delete', { watchlistUid: this.selectedWatchlist.uid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleConfirmDeleteWatchlistAlertDialog();
                    this.toggleEditWatchlistModal();
                    this.resetEditWatchlistForm();

                    toast.success('Watchlist deleted');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        toggleConfirmDeleteWatchlistAlertDialog() {
            this.confirmDeleteWatchlistAlertDialog.isOpen = !this.confirmDeleteWatchlistAlertDialog.isOpen;
        },

        toggleUpgradeToPremiumAlertDialog() {
            this.upgradeToPremiumAlertDialog.isOpen = !this.upgradeToPremiumAlertDialog.isOpen;
        },
    },
};
</script>
