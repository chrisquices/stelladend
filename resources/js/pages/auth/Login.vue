<template>
    <Head title="Login" />

    <GuestLayout>
        <div class="flex h-screen items-center justify-center">
            <Card class="my-auto w-full max-w-lg !space-y-10 !p-12 text-text">
                <form @submit.prevent="login" class="space-y-10">
                    <div class="flex items-center justify-center gap-4">
                        <img :src="logo" alt="login via google" class="mr-2 h-7 w-7" />
                        <p class="text-center font-mono text-2xl font-semibold tracking-widest">Stelladend</p>
                    </div>

                    <p class="text-center font-mono text-sm text-restorative">
                        Demo Access Only
                        <br>
                        <br>
                        Built as a personal portfolio project.
                    </p>

                    <div class="flex flex-col gap-2">
                        <TextLabel>Email</TextLabel>
                        <TextInput
                            ref="email"
                            color="inputForeground"
                            v-model="loginForm.email"
                            size="normal"
                            placeholder="john@email.com"
                            type="email"
                            autofocus
                        />
                        <InputError :error="loginForm.errors.email" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <TextLabel>Password</TextLabel>
                        <TextInput color="inputForeground" v-model="loginForm.password" size="normal" placeholder="••••••••••••" type="password" />
<!--                        <p class="text-right text-sm">-->
<!--                            <a href="/forgot-password" class="text-text-muted hover:text-text">Forgot password?</a>-->
<!--                        </p>-->
                    </div>

                    <div class="flex flex-col gap-2">
                        <Button size="normal" color="primary" type="submit">Login</Button>
                    </div>

<!--                    <div class="flex items-center justify-between gap-4">-->
<!--                        <div class="flex-grow border border-black/20"></div>-->
<!--                        <div class="flex-shrink-0 text-text-muted italic">or</div>-->
<!--                        <div class="flex-grow border border-black/20"></div>-->
<!--                    </div>-->

<!--                    <div class="flex flex-col gap-2">-->
<!--                        <a :href="route('auth.google.redirect')">-->
<!--                            <Button size="normal" color="primary" class="w-full">-->
<!--                                <img :src="googleLogo" alt="login via google" class="w-5 h-5 mr-2">-->
<!--                                Login via Google-->
<!--                            </Button>-->
<!--                        </a>-->
<!--                    </div>-->

<!--                    <div class="mt-6 text-center text-sm text-text-muted">-->
<!--                        <Link :href="route('register')" class="font-medium text-accent hover:underline">Sign up</Link>-->
<!--                    </div>-->
                </form>
            </Card>

        </div>
    </GuestLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/ui/InputError.vue';
import logo from '@img/logo.svg';
import googleLogo from '@img/companies/google.svg';
import { toast } from 'vue-sonner';

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

    props: {},

    data() {
        return {
            logo: logo,
            googleLogo: googleLogo,

            loginForm: useForm({
                email: 'user@stelladend.com',
                password: 'password',
            }),
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.$refs.email?.focus?.();
        });
    },

    computed: {},

    methods: {
        login() {
            this.loginForm.post(route('login'), {
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
