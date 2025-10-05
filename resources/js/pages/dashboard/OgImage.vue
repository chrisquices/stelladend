<template>
    <Head title="Dashboard" />

    <AuthLayout>
        <div class="flex items-center justify-center h-screen !z-40">
            <div class="flex items-center gap-4">
                <img :src="logoUrl" alt="Logo" class="h-52 w-52" />

                <p class="text-[6rem] font-mono font-semibold text-center tracking-widest text-text">Stelladend</p>
            </div>
        </div>
    </AuthLayout>

</template>

<script>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import logoUrl from '@img/logo.svg'

export default {
    components: {
        AuthLayout,
        Head,
    },

    data() {
        return {
            logoUrl,
        };
    },
};
</script>
