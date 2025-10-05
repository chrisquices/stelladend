<template>
    <div class="relative flex min-h-screen flex-col">
        <!-- Background Layer -->
        <!--        <div-->
        <!--            class="fixed inset-0 z-0 flex items-center justify-center pointer-events-none"-->
        <!--        >-->
        <!--            <div-->
        <!--                class="w-96 h-96 bg-no-repeat bg-contain bg-center opacity-5"-->
        <!--                :style="{ backgroundImage: `url('${logoUrl}')` }"-->
        <!--            ></div>-->
        <!--        </div>-->

        <div class="fixed top-1/2 left-0 z-50 -translate-y-1/2 transform">
            <div class="relative">
                <div
                    @click="toggleFeedbackModal"
                    type="button"
                    class="group inline-flex items-center rounded-r-md bg-card px-4 py-2 text-text hover:bg-card/80"
                    aria-expanded="menuOpen"
                >
                    <span>
                        <i class="lni lni-chat-1-dots !text-2xl !font-bold"></i>
                    </span>
                </div>

                <!-- Dropdown -->
<!--                <transition name="fade">-->
<!--                    <div-->
<!--                        v-if="menuOpen"-->
<!--                        class="ring-opacity-5 absolute top-1/2 left-full ml-2 w-96 -translate-y-1/2 transform overflow-hidden rounded-lg bg-card shadow-lg ring-1 ring-black"-->
<!--                    >-->
<!--                        <div class="grid gap-6 px-5 py-6 sm:gap-8 sm:p-8">-->
<!--                            <div class="-m-3 flex items-center rounded-lg p-3 text-text transition hover:bg-gray-50 hover:bg-hover" @click="toggleFeedbackModal()">-->
<!--                                <i class="lni lni-xmark-octagon !text-2xl"></i>-->
<!--                                <div class="ml-4">-->
<!--                                    <p class="text-base font-medium">Give Feedback</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </transition>-->
            </div>
        </div>

        <!-- Foreground Content -->
        <div class="relative z-10 mx-auto w-full space-y-6 px-6 sm:space-y-8 sm:px-8 lg:max-w-[90rem] xl:max-w-[100rem]">
            <nav class="top-0 z-50 h-16 w-full rounded-b-2xl bg-sidebar">
                <div class="flex h-full w-full items-center justify-between px-4">
                    <div class="w-32 justify-start sm:w-48">
                        <NavLogo />
                    </div>
                    <NavMenu />
                    <div class="w-32 justify-end sm:w-48">
                        <NavUser />
                    </div>
                </div>
            </nav>

            <main class="relative mt-4 mb-24 w-full space-y-6 text-text sm:space-y-8">
                <slot />
            </main>
        </div>

        <Footer class="relative z-10" />
        <Toaster position="bottom-right" richColors />
    </div>

    <!-- Leave Feedback -->
    <Modal v-model:isOpen="feedbackModal.isOpen" size="2extra-large">
        <form @submit.prevent="storeFeedback" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
            <div class="col-span-full flex items-center justify-between">
                <p class="font-medium text-text">We’d love your feedback</p>

                <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleFeedbackModal">
                    <i class="lni lni-xmark"></i>
                </div>
            </div>

            <div class="col-span-full text-left sm:text-center space-y-2">
                <p>Your input helps us improve.</p>
                <p><span class="font-mono tracking-wide">Stelladend</span> is currently in beta — reporting bugs or sharing suggestions makes a big difference.</p>
            </div>

            <!-- Message -->
            <div class="col-span-full flex flex-col gap-2">
                <TextArea ref="feedbackFormMessage" v-model="feedbackModal.form.message" color="inputForeground" rows="5" placeholder="Tell us what you loved, what felt off, or any ideas you have…" />
            </div>

            <div class="col-span-full text-center">
                <p class="text-sm text-text-muted italic">Active, helpful users may receive free Premium access as a small token of appreciation.</p>
            </div>

            <!-- Cancel - Send Feedback -->
            <div class="col-span-full flex items-center justify-end gap-2">
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleFeedbackModal">Cancel</Button>
                <Button size="normal" color="primaryForeground" type="submit">
                    <i class="lni lni-send-4"></i>
                    Send Feedback
                </Button>
            </div>
        </form>
    </Modal>
</template>

<script>
import Footer from '@/layouts/app/Footer.vue';
import NavLogo from '@/layouts/app/NavLogo.vue';
import NavMenu from '@/layouts/app/NavMenu.vue';
import NavUser from '@/layouts/app/NavUser.vue';
import logoUrl from '@img/logo.svg';
import { Link, useForm } from '@inertiajs/vue3';
import { toast, Toaster } from 'vue-sonner';
import Card from '@/components/ui/Card.vue';
import Modal from '@/components/ui/Modal.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import Button from '@/components/ui/Button.vue';
import TextArea from '@/components/ui/TextArea.vue';

export default {
    name: 'AuthenticatedLayout',

    components: {
        TextArea,
        Button,
        TextLabel,
        TextInput,
        Modal,
        Card,
        Footer,
        NavLogo,
        NavMenu,
        NavUser,
        Link,
        Toaster,
    },
    data() {
        return {
            logoUrl,

            menuOpen: true,

            feedbackModal: {
                isOpen: false,
                form: useForm({
                    message: null,
                    source: null,
                })
            }
        };
    },

    methods: {
        toggleMenu() {
            this.menuOpen = !this.menuOpen;
        },

        // Feedback
        toggleFeedbackModal() {
            this.feedbackModal.isOpen = !this.feedbackModal.isOpen;

            if (this.feedbackModal.isOpen) {
                this.$nextTick(() => {
                    this.$refs.feedbackFormMessage?.focus?.();
                });
            }
        },

        resetFeedbackForm() {
            this.feedbackModal.form.message = null;
        },

        storeFeedback() {
            if (!this.feedbackModal.form.message) {
                toast.error('Enter a message');
                return;
            }

            this.feedbackModal.form.source = window.location.href;

            this.feedbackModal.form.post(route('feedback.store'), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.toggleFeedbackModal();
                    this.resetFeedbackForm();

                    toast.success('Feedback sent');
                },
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

<style>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
