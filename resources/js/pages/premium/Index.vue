<template>
    <Head title="Watchlists" />

    <AuthLayout>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            <!-- Account Settings -->
            <div class="col-span-full w-full space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="lni lni-star-fat !text-xl"></i>
                        <p class="text-xl font-medium text-text">Upgrade your account</p>
                    </div>
                </div>

                <Card class="flex items-center justify-between gap-8">
                    <p class="text-center text-text text-sm">TBA</p>
                </Card>

                <Card v-if="false" class="flex items-center justify-between gap-8">
                    <p class="text-center text-text text-sm">We're working to add a payment processor. Thank you for your patience.</p>
                </Card>

                <Card v-if="false" class="flex items-center justify-between gap-8">
                    <div class="w-64 flex-shrink-0 space-y-8 self-start">
                        <!-- Current Plan -->
                        <div class="flex items-center justify-between">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-text-muted">Current Plan</p>
                                <p class="font-mono text-lg font-semibold tracking-widest uppercase"
                                   :class="{
                                        '!text-yellow-500' : currentPlan.plan === 'Premium',
                                        '!text-yellow-500' : currentPlan.plan === 'Lifetime',
                                   }"
                                >
                                    {{ currentPlan.plan }}
                                </p>
                                <p v-if="currentPlan.expiresIn" class="font-mono text-sm font-semibold tracking-widest">{{ currentPlan.expiresIn }}</p>
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div class="space-y-4">
                            <p v-if="currentPlan.plan === 'Free'" class="text-sm font-medium text-text-muted">What you get</p>
                            <p v-if="currentPlan.plan !== 'Free'" class="text-sm font-medium text-text-muted">You now have access to</p>
                            <ul class="list-disc space-y-4 pl-5 text-sm text-text">
                                <li>Unlimited portfolios</li>
                                <li>Unlimited watchlists</li>
                                <li>Compare Portfolios</li>
                                <li>Export Watchlists</li>
                                <li>Dual Currency</li>
                                <li>Early access to new features</li>
                            </ul>
                        </div>

                        <!-- Upgrade -->
                        <div v-if="currentPlan.plan !== 'Lifetime'" class="space-y-4">
                            <p v-if="currentPlan.plan === 'Premium' || currentPlan.plan === 'Lifetime'" class="text-sm text-yellow-500">
                                You’re already a premium user.
                            </p>
                            <p v-if="currentPlan.plan === 'Premium' || currentPlan.plan === 'Lifetime'" class="text-sm text-yellow-500">
                                You can extend your current subscription by purchasing another Yearly Plan, or upgrade to a Lifetime Plan for permanent access.
                            </p>
                        </div>
                    </div>

                    <Card class="bg-card-foreground">
                        <!-- Current Plan -->
                        <div class="space-y-4">
                            <p>
                                Pay with Bitcoin (BTC)

                                <span v-if="pendingPayment" class="ml-4 rounded-full bg-background font-mono tracking-widest">{{ timeLeft }}</span>
                            </p>
                            <p class="text-sm">
                                Bitcoin is a decentralized digital currency that allows you to send payments without banks or intermediaries. It’s fast, global, and secure — which is why it’s our only accepted payment method.
                            </p>
                        </div>

                        <div v-if="currentPlan.plan !== 'Lifetime'" class="flex items-center gap-8">
                            <div class="flex-grow space-y-8">
                                <div class="space-y-4">
                                    <p v-if="pendingPayment" class="text-sm">
                                        BTC Price locked in: <b>{{ pendingPayment.btcPriceFormatted }}</b>
                                    </p>

                                    <p v-if="pendingPayment && pendingPayment.plan === 'yearly'" class="text-sm">
                                        <b class="text-accent">For the Yearly Plan:</b>
                                        send the equivalent to <b class="font-mono tracking-widest">$5</b> or
                                        <b class="font-mono tracking-widest">{{ pendingPayment.yearlyPlanPrice }} BTC</b>.
                                    </p>

                                    <p v-if="pendingPayment && pendingPayment.plan === 'lifetime'" class="text-sm">
                                        <b class="text-accent">For the Lifetime Plan:</b>
                                        send the equivalent to <b class="font-mono tracking-widest">10</b> or
                                        <b class="font-mono tracking-widest">{{ pendingPayment.lifetimePlanPrice }} BTC</b>.
                                    </p>

                                    <p v-if="!pendingPayment" class="text-sm">
                                        You’ll have 60 minutes to complete the transaction, during which the BTC rate will be locked in.
                                    </p>

                                    <p v-if="!pendingPayment" class="text-sm">
                                        Once you’ve chosen your plan, simply click the corresponding button to continue.
                                    </p>
                                </div>

                                <!-- Generate Address - Plans -->
                                <div class="space-y-4 rounded-lg border border-accent p-8 text-center">
                                    <p v-if="pendingPayment" class="font-mono tracking-widest">{{ pendingPayment.btcAddress.address }}</p>

                                    <div v-if="!pendingPayment" class="flex w-full items-center justify-center gap-4">

                                        <!-- Yearly Plan ($49) -->
                                        <Button size="normal" color="primary" class="font-mono tracking-widest" @click="generateAddress('yearly')">
                                            Yearly Plan ($49)
                                        </Button>

                                        <!-- Lifetime Plan ($149) -->
                                        <Button size="normal" color="primary" class="font-mono tracking-widest" @click="generateAddress('lifetime')">
                                            Lifetime Plan ($149)
                                        </Button>
                                    </div>
                                    <p v-if="!pendingPayment" class="text-xs text-text-muted italic">Lifetime Plan only available for the first 100 users.</p>
                                </div>
                            </div>

                            <div v-if="pendingPayment" class="w-full flex-shrink-0 lg:w-44">
                                <img :src="pendingPayment.qrCodeUrl" alt="qr code" class="rounded-lg" />
                            </div>
                        </div>

                        <p class="text-center text-sm text-text-muted">
                            Note: Transactions are final and non-refundable. Access is granted immediately after blockchain confirmation.
                        </p>

                        <Table size="comfortable" class="col-span-full -mx-4 hidden sm:block">
                            <template #head>
                                <TableHeadRow>

                                    <!-- Paid At -->
                                    <TableHeader>Paid On</TableHeader>

                                    <!-- Plan -->
                                    <TableHeader>Plan</TableHeader>

                                    <!-- Address -->
                                    <TableHeader>Address</TableHeader>

                                    <!-- Amount (USD) -->
                                    <TableHeader class="text-right">Amount (USD)</TableHeader>

                                    <!-- Amount (BTC) -->
                                    <TableHeader class="text-right">Amount (BTC)</TableHeader>

                                    <!-- Status -->
                                    <TableHeader class="text-right">Status</TableHeader>
                                </TableHeadRow>
                            </template>

                            <template #body>
                                <TableRow v-for="(payment, index) in payments" :key="`table-yearly-plan-payments-row-${index}`">

                                    <!-- Paid At -->
                                    <TableCell>{{ payment.confirmedAt ?? '-' }}</TableCell>

                                    <!-- Plan -->
                                    <TableCell class="capitalize">{{ payment.plan ?? '-' }}</TableCell>

                                    <!-- Address -->
                                    <TableCell>{{ payment.btcAddress.address }}</TableCell>

                                    <!-- Amount (USD) -->
                                    <TableCell class="text-right font-mono tracking-widest">{{ payment.amountUsdFormatted ?? '-' }}</TableCell>

                                    <!-- Amount (BTC) -->
                                    <TableCell class="text-right font-mono tracking-widest">{{ payment.amountBtc ?? '-' }}</TableCell>

                                    <!-- Status -->
                                    <TableCell class="text-right">
                                        <div v-if="!payment.confirmedAt" class="flex items-center justify-end gap-2 !text-text-muted !font-bold">
                                            <i class="lni lni-clock-eight"></i>
                                            Processing
                                        </div>
                                        <div v-if="payment.confirmedAt" class="flex items-center justify-end gap-2 !text-restorative !font-bold">
                                            <i class="lni lni-check-circle-1"></i>
                                            Paid
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </template>
                        </Table>

                        <div v-if="!payments.length" class="flex h-full flex-col items-center justify-center space-y-4 py-12">
                            <p class="flex items-center justify-center text-sm text-text-muted">No payments found</p>
                        </div>
                    </Card>
                </Card>
            </div>
        </div>
    </AuthLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import Watchlists from '@/pages/watchlists/partials/Watchlists.vue';
import ThisYearsDividends from '@/pages/portfolios/partials/ThisYearsDividends.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import Table from '@/components/ui/Table.vue';
import TableRow from '@/components/ui/TableRow.vue';

export default {
    components: {
        TableRow,
        Table,
        TableHeader,
        TableCell,
        TableHeadRow,
        TextLabel,
        TextInput,
        ThisYearsDividends,
        Watchlists,
        Button,
        Card,
        AuthLayout,
        Head,
    },

    props: {
        currentPlan: Object,
        pendingPayment: Object,
        payments: Object,
    },

    data() {
        return {
            timeLeft: this.pendingPayment ? this.pendingPayment.timeLeft : '10:00',
            countdownInterval: null,
            reloadInterval: null,
        };
    },

    mounted() {
        if (this.pendingPayment) {
            this.startCountdown();
            this.startReloadInterval();
        }
    },

    beforeUnmount() {
        this.clearIntervals();
    },

    watch: {
        pendingPayment(newVal, oldVal) {
            if (newVal) {
                this.startCountdown();
                this.startReloadInterval();
            } else {
                this.clearIntervals();
            }
        },
    },

    methods: {
        generateAddress(plan) {
            router.post(
                route('premium.store-payment'),
                {
                    plan: plan,
                },
                {
                    onSuccess: () => {
                        toast.success('Address generated');
                    },
                    onError: () => {
                        toast.error('Failed to generate payment address');
                    },
                }
            );
        },

        startCountdown() {
            if (!this.pendingPayment?.timeLeft) return;

            const [minStr, secStr] = this.pendingPayment.timeLeft.split(':');
            let totalSeconds = parseInt(minStr, 10) * 60 + parseInt(secStr, 10);

            clearInterval(this.countdownInterval);
            this.countdownInterval = setInterval(() => {
                if (totalSeconds <= 0) {
                    this.timeLeft = '00:00';
                    clearInterval(this.countdownInterval);
                    window.location.reload();
                    return;
                }

                totalSeconds--;
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                this.timeLeft = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }, 1000);
        },

        startReloadInterval() {
            if (!this.pendingPayment) return;

            clearInterval(this.reloadInterval);
            this.reloadInterval = setInterval(() => {
                if (!this.pendingPayment) {
                    this.clearIntervals();
                    return;
                }

                router.reload({
                    preserveScroll: true,
                    preserveState: true,
                });
            }, 10000);
        },

        clearIntervals() {
            clearInterval(this.countdownInterval);
            clearInterval(this.reloadInterval);
        },
    },
};
</script>
