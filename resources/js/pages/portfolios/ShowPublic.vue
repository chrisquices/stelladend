<template>
    <Head :title="selectedPortfolio.name" />

    <GuestLayout>
        <div class="mx-auto flex max-w-xl flex-col space-y-8 my-6 sm:my-8">
            <div class="flex items-center justify-center gap-4">
                <p class="text-center font-mono text-2xl font-semibold tracking-widest">Stelladend</p>
            </div>

            <div class="flex flex-col gap-6 sm:gap-8">
                <div class="grid grid-cols-2 gap-6 sm:gap-8 md:grid-cols-2">
                    <!-- Monthly Income -->
                    <Card :hoverable="true">
                        <div class="relative flex h-full flex-col items-center justify-center gap-2 sm:gap-4">
                            <p class="flex items-center text-sm text-text-muted sm:text-base">Monthly Income</p>

                            <div class="space-y-1 text-center">
                                <p class="font-mono text-base font-medium tracking-widest sm:text-xl">
                                    <span v-if="selectedPortfolio.metrics.currentMonthlyIncome">{{
                                        selectedPortfolio.metrics.currentMonthlyIncomeFormatted
                                    }}</span>
                                    <span v-if="!selectedPortfolio.metrics.currentMonthlyIncome">-</span>
                                </p>
                            </div>
                        </div>
                    </Card>

                    <!-- Annual Income -->
                    <Card :hoverable="true">
                        <div class="relative flex h-full flex-col items-center justify-center gap-2 sm:gap-4">
                            <p class="flex items-center text-sm text-text-muted sm:text-base">Annual Income</p>

                            <div class="space-y-1 text-center">
                                <p class="font-mono text-base font-medium tracking-widest sm:text-xl">
                                    <span v-if="selectedPortfolio.metrics.currentAnnualIncome">{{
                                        selectedPortfolio.metrics.currentAnnualIncomeFormatted
                                    }}</span>
                                    <span v-if="!selectedPortfolio.metrics.currentAnnualIncome">-</span>
                                </p>
                            </div>
                        </div>
                    </Card>

                    <!-- Dividend Yield -->
                    <Card :hoverable="true">
                        <div class="relative flex h-full flex-col items-center justify-center gap-2 sm:gap-4">
                            <p class="flex items-center text-sm text-text-muted sm:text-base">Dividend Yield</p>

                            <div class="space-y-1 text-center">
                                <p class="font-mono text-base font-medium tracking-widest sm:text-xl">
                                    <span v-if="selectedPortfolio.metrics.currentAverageDividendYield">{{
                                        selectedPortfolio.metrics.currentAverageDividendYieldFormatted
                                    }}</span>
                                    <span v-if="!selectedPortfolio.metrics.currentAverageDividendYield">-</span>
                                </p>
                            </div>
                        </div>
                    </Card>

                    <!-- N° of Holdings -->
                    <Card :hoverable="true">
                        <div class="relative flex h-full flex-col items-center justify-center gap-2 sm:gap-4">
                            <p class="flex items-center text-sm text-text-muted sm:text-base">N° of Holdings</p>

                            <div class="space-y-1 text-center">
                                <p class="font-mono text-base font-medium tracking-widest sm:text-xl">
                                    {{ selectedPortfolio.holdings.length }}
                                </p>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- This Years Dividends -->
                <div id="this-years-dividends" class="col-span-full space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-medium text-text sm:text-xl">{{ currentYear }} Dividends</p>
                    </div>

                    <Card>
                        <ThisYearsDividends
                            :chartData="thisYearsDividends"
                            :hideYAxisLabels="true"
                            :rotateXAxisLabels="true"
                            class="col-span-full lg:col-span-2"
                        />
                    </Card>
                </div>

                <!-- Holdings -->
                <div class="col-span-full space-y-4">
                    <div class="flex items-center sm:justify-between">
                        <p class="text-lg font-medium text-text sm:text-xl">Holdings</p>
                    </div>

                    <Card>
                        <ul v-if="selectedPortfolio.holdings.length" class="space-y-6 text-sm sm:space-y-8 sm:max-h-96 overflow-y-scroll">
                            <li
                                v-for="(holding, index) in selectedPortfolio.holdings"
                                :key="`list-holding-row-${index}`"
                                class="grid grid-cols-3 gap-4"
                            >
                                <!-- Security -->
                                <div class="col-span-full">
                                    <div class="flex w-full items-center gap-4">
                                        <div class="w-7 flex-shrink-0">
                                            <img
                                                v-if="holding.logoUrl"
                                                :src="holding.logoUrl"
                                                :alt="`${holding.ticker} logo`"
                                                class="h-7 w-7 rounded"
                                            />
                                            <div
                                                v-if="!holding.logoUrl"
                                                class="flex h-7 w-7 items-center justify-center rounded text-white"
                                                :style="{ backgroundColor: hexToRgba(holding.color, 0.5) }"
                                            >
                                                {{ holding.ticker.charAt(0) }}
                                            </div>
                                        </div>

                                        <p class="truncate tracking-wide whitespace-nowrap text-text-muted">
                                            <span class="text-text">{{ holding.ticker }}</span>
                                            <span class="ml-2 truncate whitespace-nowrap text-text-muted">{{ holding.name }}</span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <div v-if="!selectedPortfolio.holdings.length" class="h-full flex flex-col items-center justify-center space-y-4 py-18">
                            <p class="flex items-center justify-center text-sm text-text-muted">
                                Nothing here
                            </p>
                        </div>
                    </Card>
                </div>

                <div v-if="!isLoggedIn" class="hidden sm:block">
                    <Button size="normal" color="accent" class="col-span-full w-full !text-text">
                        <img :src="logo" alt="login via google" class="mr-2 h-7 w-7 opacity-60 rounded-full bg-card p-px" />
                        Sign up!
                    </Button>
                </div>
            </div>
        </div>
    </GuestLayout>

    <div v-if="!isLoggedIn" class="fixed bottom-0 left-0 right-0 z-50 bg-card p-4 rounded-t-2xl lg:hidden">
        <Button size="normal" color="accent" class="bg-gradient-to-br from-background-corner via-background to-background-corner w-full rounded-full !text-text">
            <img :src="logo" alt="login via google" class="mr-2 h-7 w-7 opacity-60 rounded-full bg-card p-px" />
            Sign up!
        </Button>
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import Allocation from '@/pages/portfolios/partials/Allocation.vue';
import ThisYearsDividends from '@/pages/portfolios/partials/ThisYearsDividends.vue';
import logo from '@img/logo.svg';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';

export default {
    components: {
        Button,
        GuestLayout,
        ThisYearsDividends,
        Allocation,
        Card,
        Head,
    },

    props: {
        selectedPortfolio: Object,
        thisYearsDividends: Object,
        isLoggedIn: Boolean,
    },

    data() {
        return {
            logo: logo,
        };
    },

    computed: {
        currentYear() {
            return new Date().getFullYear();
        },
    },

    methods: {},
};
</script>
