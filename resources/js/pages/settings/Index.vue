<template>
    <Head title="Settings" />

    <AuthLayout>
        <div class="mx-auto flex flex-col space-y-8 lg:max-w-xl">
            <!-- Account Settings -->
            <div class="w-full space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xl font-medium text-text">Account Settings</p>
                </div>

                <form @submit.prevent="updateAccountSettings">
                    <Card>
                        <!-- Email -->
                        <div class="flex flex-col gap-2">
                            <TextLabel>Email</TextLabel>
                            <TextInput
                                color="inputForeground"
                                v-model="accountSettingsForm.email"
                                size="normal"
                                placeholder="Enter your email"
                                type="email"
                            />
                        </div>

                        <div class="col-span-full flex items-center justify-end gap-2">
                            <Button size="normal" color="primaryForeground" type="submit">
                                <i class="lni lni-floppy-disk-1"></i>
                                Save
                            </Button>
                        </div>
                    </Card>
                </form>
            </div>

            <!-- Password -->
            <div class="w-full space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xl font-medium text-text">Update Password</p>
                </div>

                <form @submit.prevent="updatePassword">
                    <Card>
                        <!-- Current Password -->
                        <div class="flex flex-col gap-2">
                            <TextLabel>Current Password</TextLabel>
                            <TextInput
                                color="inputForeground"
                                v-model="passwordForm.currentPassword"
                                size="normal"
                                placeholder="••••••••••••"
                                type="password"
                            />
                            <InputError :error="passwordForm.errors.currentPassword" />
                        </div>

                        <!-- New Password -->
                        <div class="flex flex-col gap-2">
                            <TextLabel>New Password</TextLabel>
                            <TextInput
                                color="inputForeground"
                                v-model="passwordForm.password"
                                size="normal"
                                placeholder="••••••••••••"
                                type="password"
                            />
                            <InputError :error="passwordForm.errors.password" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="flex flex-col gap-2">
                            <TextLabel>Confirm Password</TextLabel>
                            <TextInput
                                color="inputForeground"
                                v-model="passwordForm.password_confirmation"
                                size="normal"
                                placeholder="••••••••••••"
                                type="password"
                            />
                            <InputError :error="passwordForm.errors.password_confirmation" />
                        </div>

                        <div class="col-span-full flex items-center justify-end gap-2">
                            <Button size="normal" color="primaryForeground" type="submit">Update Password</Button>
                        </div>
                    </Card>
                </form>
            </div>

            <!-- Secondary Currency -->
            <div class="w-full space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xl font-medium text-text">
                        Secondary Currency
<!--                        <span class="ml-2 font-mono font-semibold tracking-widest text-yellow-500 uppercase">PREMIUM</span>-->
                    </p>
                </div>

                <Card>
                    <div class="flex flex-col space-y-2">
                        <TextLabel>Select a secondary currency</TextLabel>
                        <p class="mb-4 text-sm text-text-muted">
                            <b>Note:</b> Secondary currencies are shown next or below the main currency which is USD.
                        </p>

                        <div class="grid grid-cols-4 gap-6 md:grid-cols-7 lg:grid-cols-6">
                            <div class="w-full" @click="selectCurrency(null)">
                                <div class="flex h-full cursor-pointer items-center justify-center rounded-lg border border-border">
                                    <p class="text-sm text-text">None</p>
                                </div>
                            </div>

                            <div v-for="currency in currencies" class="relative w-full" @click="selectCurrency(currency.id)">
                                <div
                                    v-if="secondaryCurrency.id === currency.id"
                                    class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                >
                                    Selected
                                </div>
                                <img :src="currency.flagUrl" alt="country flag" class="h-full w-full cursor-pointer rounded-lg" />
                            </div>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Theme -->
            <div class="w-full space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xl font-medium text-text">Theme</p>
                </div>

                <Card>
                    <div class="flex flex-col space-y-2">
                        <div class="grid grid-cols-2 gap-6 sm:gap-8 md:grid-cols-3">
                            <!-- Onyx Dark -->
                            <div class="order-1 flex flex-col items-center justify-center space-y-4 text-sm sm:order-none">
                                <div class="onyx-dark relative w-full" @click="selectTheme('onyx-dark')">
                                    <div
                                        v-if="theme === 'onyx-dark'"
                                        class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                    >
                                        Selected
                                    </div>
                                    <div
                                        class="h-24 w-full cursor-pointer rounded-lg bg-gradient-to-br from-background-corner via-background to-background-corner"
                                    ></div>
                                </div>
                                <p>Onyx Dark</p>
                            </div>

                            <!-- Emerald Dark -->
                            <div class="order-3 flex flex-col items-center justify-center space-y-4 text-sm sm:order-none">
                                <div class="emerald-dark relative w-full" @click="selectTheme('emerald-dark')">
                                    <div
                                        v-if="theme === 'emerald-dark'"
                                        class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                    >
                                        Selected
                                    </div>
                                    <div
                                        class="h-24 w-full cursor-pointer rounded-lg bg-gradient-to-br from-background-corner via-background to-background-corner"
                                    ></div>
                                </div>
                                <p>Emerald Dark</p>
                            </div>

                            <!-- Sapphire Dark -->
                            <div class="order-5 flex flex-col items-center justify-center space-y-4 text-sm sm:order-none">
                                <div class="sapphire-dark relative w-full" @click="selectTheme('sapphire-dark')">
                                    <div
                                        v-if="theme === 'sapphire-dark'"
                                        class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                    >
                                        Selected
                                    </div>
                                    <div
                                        class="h-24 w-full cursor-pointer rounded-lg bg-gradient-to-br from-background-corner via-background to-background-corner"
                                    ></div>
                                </div>
                                <p>Sapphire Dark</p>
                            </div>

                            <!-- Onyx Light -->
                            <div class="order-2 flex flex-col items-center justify-center space-y-4 text-sm sm:order-none">
                                <div class="onyx-light relative w-full" @click="selectTheme('onyx-light')">
                                    <div
                                        v-if="theme === 'onyx-light'"
                                        class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                    >
                                        Selected
                                    </div>
                                    <div
                                        class="h-24 w-full cursor-pointer rounded-lg bg-gradient-to-br from-background-corner via-background to-background-corner"
                                    ></div>
                                </div>
                                <p>Onyx Light</p>
                            </div>

                            <!-- Emerald Light -->
                            <div class="order-4 flex flex-col items-center justify-center space-y-4 text-sm sm:order-none">
                                <div class="emerald-light relative w-full" @click="selectTheme('emerald-light')">
                                    <div
                                        v-if="theme === 'emerald-light'"
                                        class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                    >
                                        Selected
                                    </div>
                                    <div
                                        class="h-24 w-full cursor-pointer rounded-lg bg-gradient-to-br from-background-corner via-background to-background-corner"
                                    ></div>
                                </div>
                                <p>Emerald Light</p>
                            </div>

                            <!-- Sapphire Light -->
                            <div class="order-6 flex flex-col items-center justify-center space-y-4 text-sm sm:order-none">
                                <div class="sapphire-light relative w-full" @click="selectTheme('sapphire-light')">
                                    <div
                                        v-if="theme === 'sapphire-light'"
                                        class="absolute inset-0 flex h-full items-center justify-center rounded-lg border-2 border-accent bg-card-foreground text-xs transition-all duration-300"
                                    >
                                        Selected
                                    </div>
                                    <div
                                        class="h-24 w-full cursor-pointer rounded-lg bg-gradient-to-br from-background-corner via-background to-background-corner"
                                    ></div>
                                </div>
                                <p>Sapphire Light</p>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthLayout>

    <!-- Upgrade to Premium Alert Dialog -->
    <AlertDialog v-if="!isPremium" :show="upgradeToPremiumAlertDialog.isOpen">
        <template #title>
            <p>Upgrade to Premium</p>
        </template>
        <template #content>
            <div class="space-y-2">
                <p>Free users are limited to the primary currency (USD).</p>
                <p>Upgrade to Premium for <b>dual currency support</b>.</p>
            </div>
        </template>
        <template #buttons>
            <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleUpgradeToPremiumAlertDialog">Close</Button>
            <Link :href="route('premium.index')">
                <Button size="normal" color="premium">Get Premium</Button>
            </Link>
        </template>
    </AlertDialog>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import InputError from '@/components/ui/InputError.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AlertDialog from '@/components/ui/AlertDialog.vue';

export default {
    components: {
        Link,
        AlertDialog,
        InputError,
        TextLabel,
        TextInput,
        Card,
        AuthLayout,
        Head,
        Button,
    },

    props: {
        user: Object,
        currencies: Object,
        secondaryCurrency: Object,
        theme: String,
    },

    data() {
        return {
            upgradeToPremiumAlertDialog: {
                isOpen: false,
            },

            accountSettingsForm: useForm({
                email: this.user.email,
            }),

            passwordForm: useForm({
                currentPassword: null,
                password: null,
                password_confirmation: null,
            }),
        };
    },

    computed: {
        isPremium() {
            return usePage().props.auth?.user.isPremium;
        },
    },

    methods: {
        selectCurrency(currencyId) {
            if (!this.isPremium) {
                this.toggleUpgradeToPremiumAlertDialog()
                return
            }

            router.patch(
                route('preferences.update', {
                    key: 'secondary_currency_id',
                    value: currencyId,
                }),
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        toast.success('Secondary currency updated');
                    },
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                },
            );
        },

        selectTheme(theme) {
            router.patch(
                route('preferences.update', {
                    key: 'theme',
                    value: theme,
                }),
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        toast.success('Theme updated');

                        window.location.reload();
                    },
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                },
            );
        },

        updateAccountSettings() {
            this.accountSettingsForm.patch(route('settings.update-account-settings'), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Account settings updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        updatePassword() {
            this.passwordForm.patch(route('settings.update-password'), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Account settings updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Upgrade to Premium ------------------------------------------------------------------------------------------------------------------------
        toggleUpgradeToPremiumAlertDialog() {
            this.upgradeToPremiumAlertDialog.isOpen = !this.upgradeToPremiumAlertDialog.isOpen;
        },
    },
};
</script>
