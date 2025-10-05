<template>
    <Head title="Watchlists" />

    <AuthLayout>
        <Watchlists :watchlists="watchlists" :selectedWatchlist="null" />
    </AuthLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import Watchlists from '@/pages/watchlists/partials/Watchlists.vue';

export default {
    components: {
        Watchlists,
        Button,
        Card,
        AuthLayout,
        Head,
    },

    props: {
        watchlists: Object,
    },

    methods: {

    },
};
</script>
