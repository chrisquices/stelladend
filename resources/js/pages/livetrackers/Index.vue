<template>
    <Head title="Live Tracker" />

    <AuthLayout>
        <div class="grid grid-cols-4 gap-6 sm:gap-8">
            <!-- Live Tracker -->
            <div class="col-span-full space-y-4 text-text">
                <div class="flex items-center justify-between">
                    <p class="text-xl font-medium text-text">Live Tracker</p>
                </div>

                <Card>
                    <div class="grid grid-cols-2 gap-6 sm:gap-8">
                        <SecurityLineChart v-for="trackedSecurity in trackedSecurities" :chartData="trackedSecurity" />
                    </div>
                </Card>
            </div>
        </div>
    </AuthLayout>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import AccountsChart from '@/pages/dashboard/partials/AccountsChart.vue';
import SecurityLineChart from '@/pages/livetrackers/partials/SecurityLineChart.vue';
import { Head } from '@inertiajs/vue3';

export default {
    components: {
        SecurityLineChart,
        AccountsChart,
        Card,
        AuthLayout,
        Head,
    },

    props: {
        trackedSecurities: Object,
    },

    data() {
        return {};
    },

    computed: {},

    methods: {},
};
</script>
