<template>
    <Head :title="selectedPortfolio.name" />

    <AuthLayout>
        <Portfolios :portfolios="portfolios" :selectedPortfolio="selectedPortfolio" :upcomingDividends="upcomingDividends" />

        <!-- Overview -->
        <div v-if="selectedTab === 'overview'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            <!-- Total Portfolio Value -->
            <Card :hoverable="true" class="relative">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Total Portfolio Value</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="selectedPortfolio.metrics.currentTotalPortfolioValue">{{
                                selectedPortfolio.metrics.currentTotalPortfolioValueFormatted
                            }}</span>
                            <span v-if="!selectedPortfolio.metrics.currentTotalPortfolioValue">-</span>
                        </p>
                        <p v-if="selectedPortfolio.metrics.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="selectedPortfolio.metrics.secondaryCurrency.currentTotalPortfolioValue">{{
                                selectedPortfolio.metrics.secondaryCurrency.currentTotalPortfolioValueFormatted
                            }}</span>
                            <span v-if="!selectedPortfolio.metrics.secondaryCurrency.currentTotalPortfolioValue">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Market Value -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Market Value</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="selectedPortfolio.metrics.currentMarketValue">{{
                                selectedPortfolio.metrics.currentMarketValueFormatted
                            }}</span>
                            <span v-if="!selectedPortfolio.metrics.currentMarketValue">-</span>
                        </p>
                        <p v-if="selectedPortfolio.metrics.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="selectedPortfolio.metrics.secondaryCurrency.currentMarketValue">{{
                                selectedPortfolio.metrics.secondaryCurrency.currentMarketValueFormatted
                            }}</span>
                            <span v-if="!selectedPortfolio.metrics.secondaryCurrency.currentMarketValue">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Gain Loss -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p v-if="selectedPortfolio.metrics.currentGainLossInDollar === 0" class="flex items-center text-base text-text-muted">
                        Gain/Loss
                    </p>
                    <p v-if="selectedPortfolio.metrics.currentGainLossInDollar > 0" class="flex items-center text-base text-restorative">Gain</p>
                    <p v-if="selectedPortfolio.metrics.currentGainLossInDollar < 0" class="flex items-center text-base text-destructive">Loss</p>

                    <div class="space-y-1 text-center">
                        <p
                            class="font-mono text-xl font-medium tracking-wide"
                            :class="{
                                '!text-restorative': selectedPortfolio.metrics.currentGainLossInDollar > 0,
                                '!text-destructive': selectedPortfolio.metrics.currentGainLossInDollar < 0,
                            }"
                        >
                            <span v-if="selectedPortfolio.metrics.currentGainLossInDollar > 0">
                                +{{ selectedPortfolio.metrics.currentGainLossInDollarFormatted }} (+{{
                                    selectedPortfolio.metrics.currentGainLossInPercentageFormatted
                                }})
                            </span>
                            <span v-if="selectedPortfolio.metrics.currentGainLossInDollar < 0">
                                {{ selectedPortfolio.metrics.currentGainLossInDollarFormatted }} ({{
                                    selectedPortfolio.metrics.currentGainLossInPercentageFormatted
                                }})
                            </span>
                            <span v-if="!selectedPortfolio.metrics.currentGainLossInDollar">-</span>
                        </p>
                        <p
                            v-if="selectedPortfolio.metrics.secondaryCurrency"
                            class="font-mono text-base font-medium tracking-widest text-text-muted"
                            :class="{
                                '!text-restorative': selectedPortfolio.metrics.currentGainLossInDollar > 0,
                                '!text-destructive': selectedPortfolio.metrics.currentGainLossInDollar < 0,
                            }"
                        >
                            <span v-if="selectedPortfolio.metrics.secondaryCurrency.currentGainLossInDollar > 0"
                                >+{{ selectedPortfolio.metrics.secondaryCurrency.currentGainLossInDollarFormatted }}</span
                            >
                            <span v-if="selectedPortfolio.metrics.secondaryCurrency.currentGainLossInDollar < 0">{{
                                selectedPortfolio.metrics.secondaryCurrency.currentGainLossInDollarFormatted
                            }}</span>
                            <span v-if="!selectedPortfolio.metrics.secondaryCurrency.currentGainLossInDollar">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Cash Available -->
            <div class="relative">
                <Card :hoverable="true" class="relative">
                    <div class="relative flex h-full flex-col items-center justify-center gap-4">
                        <p class="flex items-center text-base text-text-muted">Cash Available</p>

                        <div class="space-y-1 text-center">
                            <p class="font-mono text-xl font-medium tracking-widest">
                                <span v-if="selectedPortfolio.metrics.currentCashAvailable">{{
                                    selectedPortfolio.metrics.currentCashAvailableFormatted
                                }}</span>
                                <span v-if="!selectedPortfolio.metrics.currentCashAvailable">-</span>
                            </p>
                            <p
                                v-if="selectedPortfolio.metrics.secondaryCurrency"
                                class="font-mono text-base font-medium tracking-widest text-text-muted"
                            >
                                <span v-if="selectedPortfolio.metrics.secondaryCurrency.currentCashAvailable">{{
                                    selectedPortfolio.metrics.secondaryCurrency.currentCashAvailableFormatted
                                }}</span>
                                <span v-if="!selectedPortfolio.metrics.secondaryCurrency.currentCashAvailable">-</span>
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Seeing negative cash? -->
                <div v-if="selectedPortfolio.metrics.currentCashAvailable < 0" class="absolute top-2.5 left-1/2 -translate-x-1/2">
                    <p
                        class="cursor-default text-sm font-normal whitespace-nowrap text-text-muted"
                        v-tooltip="'Negative cash balance is a result of missing or incorrect cash movements'"
                    >
                        Seeing negative cash?
                    </p>
                </div>
            </div>

            <!-- Holdings -->
            <Holdings :portfolio="selectedPortfolio" :secondaryCurrency="secondaryCurrency" class="col-span-full" />

            <!-- This Years Dividends -->
            <div id="this-years-dividends" class="col-span-full space-y-4 lg:col-span-2 ">
                <div class="flex flex-col gap-4 h-full">
                    <div class="flex h-10 items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="lni lni-plant-pot-1 !text-xl"></i>
                            <p class="text-xl font-medium text-text">{{ currentYear }} Dividends</p>
                        </div>
                    </div>

                    <Card class="h-64 sm:h-full">
                        <ThisYearsDividends :portfolio="selectedPortfolio" :chartData="thisYearsDividends" class="h-full" />
                    </Card>
                </div>
            </div>

            <!-- Upcoming Dividends -->
            <div id="upcoming-dividends" class="col-span-full space-y-4 lg:col-span-2">
                <UpcomingDividendsCompact :portfolio="selectedPortfolio" :upcomingDividends="upcomingDividends" />
            </div>

            <!-- Performance -->
            <div class="col-span-full space-y-4 lg:col-span-full">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="lni lni-trend-up-1 !text-xl"></i>
                        <p class="text-xl font-medium text-text">Performance</p>
                    </div>
                </div>

                <Card>
                    <Performance :performance="performance" class="col-span-full lg:col-span-2" />
                </Card>
            </div>
        </div>

        <!-- Upcoming Dividends -->
        <div v-if="selectedTab === 'upcoming-dividends'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            <UpcomingDividends :upcomingDividends="upcomingDividends" />
        </div>

        <!-- Dividends Received -->
        <div v-if="selectedTab === 'dividends-received'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            <DividendsReceived v-if="selectedPortfolio.mode === 'advanced'" :portfolio="selectedPortfolio" :dividendsReceived="dividendsReceived" />
        </div>

        <!-- Cash Movements -->
        <div v-if="selectedTab === 'cash-movements'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            <CashMovements v-if="selectedPortfolio.mode === 'advanced'" :portfolio="selectedPortfolio" :cashMovements="cashMovements" />
        </div>
    </AuthLayout>
</template>

<script>
import TestModal from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import InfoTooltip from '@/components/ui/InfoTooltip.vue';
import Modal from '@/components/ui/Modal.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Allocation from '@/pages/portfolios/partials/Allocation.vue';
import CashMovements from '@/pages/portfolios/partials/CashMovements.vue';
import DividendsReceived from '@/pages/portfolios/partials/DividendsReceived.vue';
import Holdings from '@/pages/portfolios/partials/Holdings.vue';
import Performance from '@/pages/portfolios/partials/Performance.vue';
import Portfolios from '@/pages/portfolios/partials/Portfolios.vue';
import ThisYearsDividends from '@/pages/portfolios/partials/ThisYearsDividends.vue';
import UpcomingDividends from '@/pages/portfolios/partials/UpcomingDividends.vue';
import UpcomingDividendsCompact from '@/pages/portfolios/partials/UpcomingDividendsCompact.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';

export default {
    components: {
        Performance,
        PortfolioPerformance: Performance,
        CashMovements,
        DividendsReceived,
        UpcomingDividends,
        UpcomingDividendsCompact,
        TextLabel,
        TestModal,
        Portfolios,
        SortIcon,
        Bar,
        ThisYearsDividends,
        Allocation,
        Holdings,
        TextInput,
        Modal,
        TableHeadRow,
        TableCell,
        Table,
        TableRow,
        TableHeader,
        InfoTooltip,
        Card,
        AuthLayout,
        Head,
        Link,
        Button,
        Dropdown,
    },

    props: {
        portfolios: Object,
        selectedPortfolio: Object,
        performance: Object,
        thisYearsDividends: Object,
        dividendsReceived: Object,
        canImportData: Boolean,
        upcomingDividends: Object,
        cashMovements: Object,
        secondaryCurrency: Object,
    },

    data() {
        return {
            isAlertDialogOpen: false,
            filters: {
                sortBy: 'payment_date',
                sortDirection: 'asc',
            },
        };
    },

    computed: {
        selectedTab() {
            const url = new URL(this.$page.url, window.location.origin);
            const tab = url.searchParams.get('tab');
            const validTabs = ['overview', 'upcoming-dividends', 'dividends-received', 'cash-movements'];

            return validTabs.includes(tab) ? tab : 'overview';
        },

        currentYear() {
            return new Date().getFullYear();
        },
    },

    mounted() {
        // this.interval = setInterval(() => {
        //     router.reload({
        //         preserveScroll: true,
        //         preserveState: true,
        //     });
        // }, 10000);
    },

    beforeUnmount() {
        // clearInterval(this.interval);
    },

    methods: {
        updateSelectedTab(tab) {
            this.selectedTab = tab;
        },
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
