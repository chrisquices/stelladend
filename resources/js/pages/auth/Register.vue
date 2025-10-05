<template>
    <Head title="Register" />

    <GuestLayout>
        <div class="flex h-screen items-center justify-center">
            <Card class="my-auto w-full max-w-lg !space-y-10 !p-12 text-text">
                <form @submit.prevent="register" class="space-y-10">
                    <div class="flex items-center justify-center gap-4">
                        <img :src="logo" alt="Stelladend logo" class="mr-2 h-7 w-7" />
                        <p class="text-center font-mono text-2xl font-semibold tracking-widest">Stelladend</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <TextLabel>Email</TextLabel>
                        <TextInput color="inputForeground" v-model="registerForm.email" size="normal" placeholder="john@email.com" type="email" />
                        <InputError :error="registerForm.errors.email" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <TextLabel>Password</TextLabel>
                        <div class="relative">
                            <TextInput
                                color="inputForeground"
                                v-model="registerForm.password"
                                size="normal"
                                placeholder="••••••••••••"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full"
                            />
                            <div class="absolute top-1 right-2 flex items-center justify-center">
                                <i
                                    v-if="showPassword"
                                    class="lni lni-eye cursor-pointer !text-2xl !text-text-muted transition-all duration-300 hover:!text-text"
                                    @click="toggleShowPassword()"
                                ></i>
                                <i
                                    v-if="!showPassword"
                                    class="lni lni-eye cursor-pointer !text-2xl !text-text-muted transition-all duration-300 hover:!text-text"
                                    @click="toggleShowPassword()"
                                ></i>
                            </div>
                        </div>
                        <InputError :error="registerForm.errors.password" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <TextLabel>Confirm Password</TextLabel>
                        <div class="relative">
                            <TextInput
                                color="inputForeground"
                                v-model="registerForm.password_confirmation"
                                size="normal"
                                placeholder="••••••••••••"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full"
                            />
                            <div class="absolute top-1 right-2 flex items-center justify-center">
                                <i
                                    v-if="showPassword"
                                    class="lni lni-eye cursor-pointer !text-2xl !text-text-muted transition-all duration-300 hover:!text-text"
                                    @click="toggleShowPassword()"
                                ></i>
                                <i
                                    v-if="!showPassword"
                                    class="lni lni-eye cursor-pointer !text-2xl !text-text-muted transition-all duration-300 hover:!text-text"
                                    @click="toggleShowPassword()"
                                ></i>
                            </div>
                        </div>
                        <InputError :error="registerForm.errors.password_confirmation" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <Button size="normal" color="primary" type="submit">Register</Button>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-grow border border-black/20"></div>
                        <div class="flex-shrink-0 text-text-muted italic">or</div>
                        <div class="flex-grow border border-black/20"></div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <a :href="route('auth.google.redirect')">
                            <Button size="normal" color="primary" class="w-full">
                                <img :src="googleLogo" alt="login via google" class="w-5 h-5 mr-2">
                                Register via Google
                            </Button>
                        </a>
                    </div>

                    <div class="mt-6 text-center text-sm text-text-muted">
                        Already have an account?
                        <Link :href="route('login')" class="font-medium text-accent hover:underline">Sign in</Link>
                    </div>
                </form>
            </Card>
        </div>
    </GuestLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import InputError from '@/components/ui/InputError.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import logo from '@img/logo.svg';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import googleLogo from '@img/companies/google.svg';

export default {
    components: {
        Link,
        InputError,
        TextLabel,
        TextInput,
        GuestLayout,
        Card,
        Head,
        Button,
    },

    data() {
        return {
            logo,
            googleLogo: googleLogo,
            showPassword: false,
            registerForm: useForm({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            }),
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.$refs.name?.focus?.();
        });
    },

    methods: {
        toggleShowPassword() {
            this.showPassword = !this.showPassword;
        },

        register() {
            this.registerForm.post(route('register'), {
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },
    },
};
</script>
