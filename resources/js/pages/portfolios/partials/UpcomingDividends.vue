<template>
    <div class="col-span-full space-y-4">
        <div class="h-10 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="lni lni-plant-pot-1 !text-xl"></i>
                <p class="text-xl font-medium text-text">Upcoming Dividends ({{ upcomingDividends.length }})</p>
            </div>
        </div>

        <Card>
            <Table v-if="localUpcomingDividends.length" size="comfortable" class="-mx-4">
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

                        <!-- Ex-Dividend Date -->
                        <TableHeader>
                            <div class="flex items-center gap-2" @click="sort('exDividendDate')">
                                <span>Ex-Dividend Date</span>
                                <SortIcon v-if="filters.sortBy === 'exDividendDate'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Payment Date -->
                        <TableHeader>
                            <div class="flex items-center gap-2" @click="sort('paymentDate')">
                                <span>Payment Date</span>
                                <SortIcon v-if="filters.sortBy === 'paymentDate'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Yield -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('yield')">
                                <span>Yield</span>
                                <SortIcon v-if="filters.sortBy === 'yield'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Shares Owned -->
                        <TableHeader class="text-center">
                            <div class="flex items-center justify-end gap-2" @click="sort('currentSharesOwned')">
                                <span>Shares Owned</span>
                                <SortIcon v-if="filters.sortBy === 'currentSharesOwned'" :direction="filters.sortDirection" />
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
                        <TableCell class="font-mono font-medium tracking-widest text-text-muted uppercase"
                                   v-tooltip="upcomingDividend.daysUntilPayment === 0 ? 'Paid today' : upcomingDividend.daysUntilPayment === 1 ? 'Payment due tomorrow' : `Payment in ${upcomingDividend.daysUntilPayment} days`"
                        >
                            {{ upcomingDividend.daysUntilPaymentFormatted }}
                        </TableCell>

                        <!-- Security -->
                        <TableCell class="font-bold">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 w-7">
                                    <img v-if="upcomingDividend.security.logoUrl" :src="upcomingDividend.security.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                    <div
                                        v-if="!upcomingDividend.security.logoUrl"
                                        class="flex h-7 w-7 items-center justify-center rounded text-white"
                                        :style="{ backgroundColor: hexToRgba(upcomingDividend.security.color, 0.5) }"
                                    >
                                        {{ upcomingDividend.security.ticker.charAt(0) }}
                                    </div>
                                </div>

                                <p>
                                    {{ upcomingDividend.security.ticker }}
                                    <span class="hidden xl:inline ml-2 w-10 truncate whitespace-nowrap text-text-muted">
                                        {{ upcomingDividend.security.name }}
                                    </span>
                                </p>
                            </div>
                        </TableCell>

                        <!-- Ex-Dividend Date -->
                        <TableCell>
                            <div v-if="upcomingDividend.exDividendDate">
                                <p class="font-mono font-medium tracking-widest">{{ upcomingDividend.exDividendDate }}</p>
                            </div>
                            <div v-if="!upcomingDividend.exDividendDate" class="text-text-muted">-</div>
                        </TableCell>

                        <!-- Payment Date -->
                        <TableCell>
                            <div v-if="upcomingDividend.paymentDate">
                                <p class="font-mono font-medium tracking-widest">{{ upcomingDividend.paymentDate }}</p>
                            </div>
                            <div v-if="!upcomingDividend.paymentDate" class="text-text-muted">-</div>
                        </TableCell>

                        <!-- Dividend Yield -->
                        <TableCell class="text-end">
                            <div v-if="upcomingDividend.dividendYield" class="font-mono">
                                <p class="">{{ upcomingDividend.dividendYieldFormatted }}</p>
                            </div>
                            <div v-if="!upcomingDividend.dividendYield" class="text-text-muted">-</div>
                        </TableCell>

                        <!-- Shares Owned -->
                        <TableCell class="text-right">
                            <div v-if="upcomingDividend.currentSharesOwned">
                                <p class="font-mono font-medium tracking-widest">{{ upcomingDividend.currentSharesOwnedFormatted }}</p>
                            </div>
                            <div v-if="!upcomingDividend.currentSharesOwned" class="text-text-muted">-</div>
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

            <div v-if="!localUpcomingDividends.length" class="h-full flex flex-col items-center justify-center space-y-4 py-18">
                <p class="flex items-center justify-center text-sm text-text-muted">
                    Once a dividend has a known ex-date, it’ll appear here
                </p>
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

export default {
    components: {
        SortIcon,
        Table,
        TableHeadRow,
        TableRow,
        TableHeader,
        TableCell,
        Card,
    },

    props: {
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
        }
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
