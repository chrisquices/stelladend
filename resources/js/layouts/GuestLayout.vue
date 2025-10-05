<template>
    <div class="relative flex min-h-screen flex-col">
<!--        <div class="pointer-events-none fixed inset-0 z-0 flex items-center justify-center">-->
<!--            <div class="h-96 w-96 bg-contain bg-center bg-no-repeat opacity-5" :style="{ backgroundImage: `url('${logoUrl}')` }"></div>-->
<!--        </div>-->

        <div class="relative z-10 mx-auto w-full space-y-6 px-6 sm:space-y-8 sm:px-8 lg:max-w-[90rem] xl:max-w-[100rem]">
            <!-- Main Content Area -->
            <main class="relative w-full space-y-6 sm:space-y-8 text-text">
                <slot />
            </main>
        </div>

        <Footer class="relative z-10" />
        <Toaster position="bottom-right" richColors />
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import NavLogo from '@/layouts/app/NavLogo.vue';
import NavMenu from '@/layouts/app/NavMenu.vue';
import NavUser from '@/layouts/app/NavUser.vue';
import logoUrl from '@img/logo.svg';
import Footer from '@/layouts/app/Footer.vue';

export default {
    name: 'GuestLayout',

    components: {
        Footer,
        NavLogo,
        NavMenu,
        NavUser,
        Link,
    },

    data() {
        return {
            logoUrl: logoUrl,
        };
    },
};
</script>
