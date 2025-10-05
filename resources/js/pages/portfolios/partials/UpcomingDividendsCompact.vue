<template>
    <div class="flex flex-col gap-4 h-full">
        <div class="flex h-10 items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="lni lni-plant-pot-1 !text-xl"></i>
                <p class="text-xl font-medium text-text">Upcoming Dividends ({{ upcomingDividends.length }})</p>
            </div>

            <div class="flex items-center justify-between gap-4">
                <Link :href="route('portfolios.show', { portfolioUid: portfolio.uid, tab: 'upcoming-dividends' })">
                    <Button size="small" color="transparent" class="text-text-muted">
                        <i class="lni lni-plus"></i>
                        <span class="hidden md:block">View more</span>
                    </Button>
                </Link>
            </div>
        </div>

        <Card class="h-64 overflow-y-auto !pt-4">
            <Table v-if="localUpcomingDividends.length" size="normal" class="-mx-4">
                <template #head>
                    <TableHeadRow>
                        <!-- Days -->
                        <TableHeader class="w-16">
                            <div class="flex items-center gap-2" @click="sort('daysUntilPayment')">
                                <span>Days</span>
                                <SortIcon v-if="filters.sortBy === 'daysUntilPayment'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Security -->
                        <TableHeader>
                            <div class="flex items-center gap-2" @click="sort('security.ticker')">
                                <span>Security</span>
                                <SortIcon v-if="filters.sortBy === 'security.ticker'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Payment Date -->
                        <TableHeader>
                            <div class="flex items-center gap-2" @click="sort('paymentDate')">
                                <span>Payment Date</span>
                                <SortIcon v-if="filters.sortBy === 'paymentDate'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- $ Per Share -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('amount')">
                                <span>$ Per Share</span>
                                <SortIcon v-if="filters.sortBy === 'amount'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Expected -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('totalExpected')">
                                <span>Expected</span>
                                <SortIcon v-if="filters.sortBy === 'totalExpected'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>
                    </TableHeadRow>
                </template>

                <template #body>
                    <TableRow v-for="(upcomingDividend, index) in localUpcomingDividends" :key="`table-upcoming-dividends-row-${index}`">
                        <!-- Days -->
                        <TableCell
                            class="font-mono font-medium tracking-widest text-text-muted uppercase"
                            v-tooltip="
                                upcomingDividend.daysUntilPayment === 0
                                    ? 'Paid today'
                                    : upcomingDividend.daysUntilPayment === 1
                                      ? 'Payment due tomorrow'
                                      : `Payment in ${upcomingDividend.daysUntilPayment} days`
                            "
                        >
                            {{ upcomingDividend.daysUntilPaymentFormatted }}
                        </TableCell>

                        <!-- Security -->
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <div class="w-7 flex-shrink-0">
                                    <img
                                        v-if="upcomingDividend.security.logoUrl"
                                        :src="upcomingDividend.security.logoUrl"
                                        alt="company logo"
                                        class="h-6 w-6 rounded"
                                    />
                                    <div
                                        v-if="!upcomingDividend.security.logoUrl"
                                        class="flex h-6 w-6 items-center justify-center rounded text-white"
                                        :style="{ backgroundColor: hexToRgba(upcomingDividend.security.color, 0.5) }"
                                    >
                                        {{ upcomingDividend.security.ticker.charAt(0) }}
                                    </div>
                                </div>

                                <p>{{ upcomingDividend.security.ticker }}</p>
                            </div>
                        </TableCell>

                        <!-- Payment Date -->
                        <TableCell>
                            <div v-if="upcomingDividend.paymentDate">
                                <p class="font-mono font-medium tracking-widest">{{ upcomingDividend.paymentDate }}</p>
                            </div>
                            <div v-if="!upcomingDividend.paymentDate" class="text-text-muted">-</div>
                        </TableCell>

                        <!-- $ Per Share -->
                        <TableCell class="text-right">
                            <div v-if="upcomingDividend.amount">
                                <p class="font-mono font-medium tracking-widest">{{ upcomingDividend.amountFormatted }}</p>
                                <p v-if="upcomingDividend.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                    {{ upcomingDividend.secondaryCurrency.amountFormatted }}
                                </p>
                            </div>
                            <div v-if="!upcomingDividend.amount" class="text-text-muted">-</div>
                        </TableCell>

                        <!-- Expected -->
                        <TableCell class="text-right">
                            <div v-if="upcomingDividend.totalExpected">
                                <p class="font-mono font-medium tracking-widest text-text-inverted">{{ upcomingDividend.totalExpectedFormatted }}</p>
                                <p v-if="upcomingDividend.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                    {{ upcomingDividend.secondaryCurrency.totalExpectedFormatted }}
                                </p>
                            </div>
                            <div v-if="!upcomingDividend.totalExpected" class="text-text-muted">-</div>
                        </TableCell>
                    </TableRow>
                </template>
            </Table>

            <div v-if="!localUpcomingDividends.length" class="flex h-full flex-col items-center justify-center space-y-4 py-18">
                <p class="flex items-center justify-center text-sm text-text-muted">Once a dividend has a known ex-date, it’ll appear here</p>
            </div>
        </Card>
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import Table from '@/components/ui/Table.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import axios from 'axios';
import { hexToRgba } from '@/utils/colorHelper';
import { toast } from 'vue-sonner';
import Button from '@/components/ui/Button.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
        Button,
        SortIcon,
        Table,
        TableHeadRow,
        TableRow,
        TableHeader,
        TableCell,
        Card,
    },

    props: {
        portfolio: Object,
        upcomingDividends: Object,
    },

    computed: {},

    data() {
        return {
            localUpcomingDividends: this.upcomingDividends,
            filters: {
                sortBy: 'ticker',
                sortDirection: 'asc',
            },
        };
    },

    watch: {
        upcomingDividends() {
            this.localUpcomingDividends = this.upcomingDividends;
        },
    },

    methods: {
        hexToRgba,

        async sort(sortBy) {
            this.filters.sortBy = sortBy;
            this.filters.sortDirection = this.filters.sortDirection === 'asc' ? 'desc' : 'asc';

            axios
                .post(route('portfolios.sort-upcoming-dividends'), {
                    upcomingDividends: this.localUpcomingDividends,
                    ...(this.filters.sortBy && { sortBy: this.filters.sortBy }),
                    ...(this.filters.sortDirection && { sortDirection: this.filters.sortDirection }),
                })
                .then((response) => {
                    this.localUpcomingDividends = response.data;
                })
                .catch((error) => {
                    toast.error(error);
                });
        },
    },
};
</script>

<!--<div v-for="(upcomingDividend, index) in localUpcomingDividends" class="border-l-4 border rounded-lg border-l-accent border-border h-18 py-2 px-4 space-y-2">-->

<!--<div class="flex items-center justify-between gap-4">-->
<!--    <div class="space-y-2">-->
<!--        <p class="font-mono font-medium tracking-widest text-text-muted uppercase text-xs" v-tooltip="upcomingDividend.daysUntilPayment === 0 ? 'Paid today' : upcomingDividend.daysUntilPayment === 1 ? 'Payment due tomorrow' : `Payment in ${upcomingDividend.daysUntilPayment} days`">-->
<!--            {{ upcomingDividend.daysUntilPaymentFormatted }}-->
<!--        </p>-->

<!--        <div class="flex items-center gap-4">-->
<!--            <div class="flex-shrink-0 w-7">-->
<!--                <img v-if="upcomingDividend.security.logoUrl" :src="upcomingDividend.security.logoUrl" alt="company logo" class="h-7 w-7 rounded" />-->
<!--                <div-->
<!--                    v-if="!upcomingDividend.security.logoUrl"-->
<!--                    class="flex h-7 w-7 items-center justify-center rounded text-white"-->
<!--                    :style="{ backgroundColor: hexToRgba(upcomingDividend.security.color, 0.5) }"-->
<!--                >-->
<!--                    {{ upcomingDividend.security.ticker.charAt(0) }}-->
<!--                </div>-->
<!--            </div>-->

<!--            <p>{{ upcomingDividend.security.ticker }}</p>-->
<!--        </div>-->
<!--    </div>-->

<!--    <div>-->
<!--        <p class="font-mono font-medium tracking-widest text-sm flex items-center justify-between gap-8">-->
<!--            <span class="text-text-muted text-xs">Ex-Dividend</span>-->
<!--            <span>{{ upcomingDividend.exDividendDate }}</span>-->
<!--        </p>-->
<!--        <p class="font-mono font-medium tracking-widest text-sm flex items-center justify-between gap-8">-->
<!--            <span class="text-text-muted text-xs">Payment</span>-->
<!--            <span>{{ upcomingDividend.exDividendDate }}</span>-->
<!--        </p>-->
<!--    </div>-->
<!--</div>-->
<!--</div>-->
