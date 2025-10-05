<template>
    <div class="col-span-full space-y-4">
        <div class="h-10 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="lni lni-plant-dollar !text-xl"></i>
                <p class="text-xl font-medium text-text">Dividends Received</p>
            </div>
        </div>

        <Card>
            <div class="flex flex-col gap-6 sm:gap-8 xl:flex-row">
                <!-- Stats -->
                <Card
                    class="grid-cols-2 xl:!grid-cols-1 grid w-full flex-shrink-0 xl:grid-cols-3 xl:block xl:w-80 xl:grid-cols-1 !bg-card-foreground"
                >
                    <!-- Total Payments -->
                    <div class="flex flex-col gap-4">
                        <p class="text-sm tracking-wide text-text-muted">Total Payments</p>
                        <div class="flex justify-between">
                            <p class="font-mono text-sm font-medium tracking-widest">
                                <span v-if="dividendsReceived.stats.totalPayments">{{ dividendsReceived.stats.totalPaymentsFormatted }}</span>
                                <span v-if="!dividendsReceived.stats.totalPayments">-</span>
                            </p>
                        </div>
                    </div>

                    <!-- Total Cash Received -->
                    <div class="flex flex-col gap-4">
                        <p class="text-sm tracking-wide text-text-muted">Total Cash Received</p>
                        <div class="flex flex-col sm:flex-row sm:justify-between">
                            <p class="font-mono text-sm font-medium tracking-widest">
                                <span v-if="dividendsReceived.stats.totalReceived">{{ dividendsReceived.stats.totalReceivedFormatted }}</span>
                                <span v-if="!dividendsReceived.stats.totalReceived">-</span>
                            </p>
                            <p v-if="dividendsReceived.stats.secondaryCurrency" class="font-mono text-sm font-medium tracking-widest text-text-muted">
                                <span v-if="dividendsReceived.stats.secondaryCurrency.totalReceived">{{
                                    dividendsReceived.stats.secondaryCurrency.totalReceivedFormatted
                                }}</span>
                                <span v-if="!dividendsReceived.stats.secondaryCurrency.totalReceived">-</span>
                            </p>
                        </div>
                    </div>

                    <!-- Total Cash Received By Security -->
                    <div class="col-span-full flex flex-col gap-6 sm:gap-8">
                        <p class="text-sm tracking-wide text-text-muted">Total Cash Received By Security</p>
                        <div class="grid-col-1 grid gap-6 sm:gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:block xl:grid-cols-4 xl:space-y-8">
                            <div
                                v-if="dividendsReceived.totals.length"
                                v-for="total in dividendsReceived.totals"
                                class="flex w-full items-center justify-between rounded-lg xl:bg-transparent xl:p-0"
                            >
                                <div class="flex items-center gap-4">
                                    <img :src="total.logoUrl" :alt="`${total.ticker} logo`" class="h-7 w-7 rounded" />
                                    <p class="flex items-center text-sm font-bold">{{ total.ticker }}</p>
                                </div>

                                <div v-if="total.total">
                                    <p class="text-right font-mono text-sm font-medium tracking-widest">
                                        <span v-if="total.total">{{ total.totalFormatted }}</span>
                                        <span v-if="!total.total">-</span>
                                    </p>
                                    <p v-if="total.secondaryCurrency" class="font-mono text-sm font-medium tracking-widest text-text-muted">
                                        <span v-if="total.secondaryCurrency.total">{{ total.secondaryCurrency.totalFormatted }}</span>
                                        <span v-if="!total.secondaryCurrency.total">-</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-if="!dividendsReceived.totals.length" class="flex w-full items-center justify-between">
                            <div>
                                <p class="text-right font-mono text-sm font-medium tracking-widest">-</p>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Table -->
                <div class="flex min-w-0 flex-grow flex-col space-y-8">

                    <!-- Filters -->
                    <div class="w-full flex flex-col md:flex-row gap-4 whitespace-nowrap justify-end">

                        <!-- Types with Data -->
                        <Dropdown :closeContentOnClick="false" class="w-full md:w-auto">
                            <template #trigger>
                                <Button size="normal" color="primaryForeground" class="font-mono w-full sm:w-44">
                                    {{
                                        excludedTickers.length === 0
                                            ? 'All Securities'
                                            : excludedTickers.length === dividendsReceived.securitiesWithData.length
                                                ? 'None Selected'
                                                : `${dividendsReceived.securitiesWithData.length - excludedTickers.length} Selected`
                                    }}
                                    <i class="lni lni-chevron-down"></i>
                                </Button>
                            </template>

                            <template #content>
                                <ul class="max-h-80 w-full overflow-y-auto">
                                    <li
                                        v-for="ticker in dividendsReceived.securitiesWithData"
                                        :key="`ticker-filter-${ticker}`"
                                        class="px-4 py-2 hover:bg-hover"
                                    >
                                        <label class="flex cursor-pointer items-center gap-2">
                                            <TextInput
                                                size="normal"
                                                color="inputForeground"
                                                type="checkbox"
                                                :checked="!excludedTickers.includes(ticker)"
                                                @change="toggleTicker(ticker)"
                                            />
                                            {{ ticker }}
                                        </label>
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>

                        <div class="flex flex-row gap-4 w-full md:w-auto">
                            <!-- Year -->
                            <Dropdown>
                                <template #trigger>
                                    <Button size="normal" color="primaryForeground" class="font-mono">
                                        {{ selectedYear }}
                                        <i class="lni lni-chevron-down"></i>
                                    </Button>
                                </template>

                                <template #content>
                                    <ul class="max-h-80 w-full overflow-y-auto">
                                        <li
                                            v-for="yearWithData in dividendsReceived.yearsWithData"
                                            :key="`year-filter-${yearWithData}`"
                                            class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover"
                                            @click="selectedYear = yearWithData"
                                        >
                                            {{ yearWithData }}
                                        </li>
                                    </ul>
                                </template>
                            </Dropdown>

                            <!-- Quarters -->
                            <Dropdown :closeContentOnClick="false" class="flex-grow">
                                <template #trigger>
                                    <Button size="normal" color="primaryForeground" class="font-mono w-full sm:w-40">
                                        {{ selectedQuarters.length === 4 ? 'All Quarters' : selectedQuarters.sort((a, b) => a - b).map((q) => 'Q' + q).join(', ') }}
                                        <i class="lni lni-chevron-down"></i>
                                    </Button>
                                </template>

                                <template #content>
                                    <ul class="max-h-80 w-full overflow-y-auto">
                                        <li v-for="q in [1, 2, 3, 4]" :key="`quarter-filter-${q}`" class="px-4 py- hover:bg-hover">
                                            <label class="flex cursor-pointer items-center gap-2">
                                                <TextInput size="normal" color="inputForeground" type="checkbox" :checked="selectedQuarters.includes(q)" @change="toggleQuarter(q)" />
                                                Q{{ q }}
                                            </label>
                                        </li>
                                    </ul>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Dividends Received -->
                    <Table v-if="filteredDividends.length" size="comfortable" class="-mx-4 flex-grow">
                        <template #head>
                            <TableHeadRow>
                                <!-- Payment Date -->
                                <TableHeader>
                                    <div class="flex items-center gap-2" @click="sort('date')">
                                        <span>Payment Date</span>
                                        <SortIcon v-if="filters.sortBy === 'date'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Security -->
                                <TableHeader>
                                    <div class="flex items-center gap-2" @click="sort('security.ticker')">
                                        <span>Security</span>
                                        <SortIcon v-if="filters.sortBy === 'security.ticker'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Yield -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('dividendYield')">
                                        <span>Yield</span>
                                        <SortIcon v-if="filters.sortBy === 'dividendYield'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Shares Owned -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('shares')">
                                        <span>Shares Owned</span>
                                        <SortIcon v-if="filters.sortBy === 'shares'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- $ Per Share -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('dividendAmount')">
                                        <span>$ Per Share</span>
                                        <SortIcon v-if="filters.sortBy === 'dividendAmount'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Received -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('total')">
                                        <span>Received</span>
                                        <SortIcon v-if="filters.sortBy === 'total'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!--                                        &lt;!&ndash; DRIP Enabled &ndash;&gt;-->
                                <!--                                        <TableHeader></TableHeader>-->
                            </TableHeadRow>
                        </template>

                        <template #body>
                            <TableRow v-for="(dividendReceived, index) in filteredDividends" :key="`table-cash-flow-row-${index}`">
                                <!-- Payment Date -->
                                <TableCell class="font-mono font-medium tracking-widest">{{ dividendReceived.dividend.paymentDate }}</TableCell>

                                <!-- Security -->
                                <TableCell class="font-bold">
                                    <div class="flex items-center gap-4">
                                        <img
                                            :src="dividendReceived.security.logoUrl"
                                            :alt="`${dividendReceived.security.ticker} logo`"
                                            class="h-7 w-7 rounded"
                                        />
                                        <p class="flex items-center">
                                            <span>{{ dividendReceived.security.ticker }}</span>
                                            <span class="ml-2 hidden truncate whitespace-nowrap text-text-muted xl:block">{{
                                                dividendReceived.security.name
                                            }}</span>
                                        </p>
                                    </div>
                                </TableCell>

                                <!-- Dividend Yield -->
                                <TableCell class="text-end">
                                    <div v-if="dividendReceived.dividendYield">
                                        <p class="font-mono font-medium tracking-widest">{{ dividendReceived.dividendYieldFormatted }}</p>
                                    </div>
                                    <div v-if="!dividendReceived.dividendYield" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Shares -->
                                <TableCell class="text-end">
                                    <div v-if="dividendReceived.shares">
                                        <p class="font-mono font-medium tracking-widest">{{ dividendReceived.sharesFormatted }}</p>
                                    </div>
                                    <div v-if="!dividendReceived.shares" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- $ Per Share -->
                                <TableCell class="text-end">
                                    <div v-if="dividendReceived.dividendAmount">
                                        <p class="font-mono font-medium tracking-widest">{{ dividendReceived.dividendAmountFormatted }}</p>
                                        <p v-if="dividendReceived.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                            {{ dividendReceived.secondaryCurrency.dividendAmountFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!dividendReceived.dividendAmount" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Received -->
                                <TableCell class="text-end">
                                    <div v-if="dividendReceived.total">
                                        <p class="font-mono font-medium tracking-widest text-text-inverted"
                                           :class="{
                                              '!text-restorative' : dividendReceived.total,
                                           }"
                                        >
                                            {{ dividendReceived.totalFormatted }}
                                        </p>
                                        <p v-if="dividendReceived.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                            {{ dividendReceived.secondaryCurrency.totalFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!dividendReceived.total" class="text-text-muted">-</div>
                                </TableCell>

                                <!--                                        &lt;!&ndash; DRIP Enabled &ndash;&gt;-->
                                <!--                                        <TableCell class="text-end">-->
                                <!--                                            <div-->
                                <!--                                                v-tooltip="'DRIP enabled'"-->
                                <!--                                                class="flex h-8 !w-8 items-center justify-center rounded-full hover:!bg-accent/50"-->
                                <!--                                            >-->
                                <!--                                                <p><i class="lni lni-water-drop-1 !fill-accent !text-xl !text-text-accent-foreground"></i></p>-->
                                <!--                                            </div>-->
                                <!--                                        </TableCell>-->
                            </TableRow>
                        </template>
                    </Table>

                    <!-- No dividends received -->
                    <div v-if="!filteredDividends.length" class="h-full flex flex-col items-center justify-center space-y-4">
                        <p class="flex items-center justify-center text-sm text-text-muted">
                            Once dividend payments are paid and posted, they’ll be listed here.
                        </p>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import SegmentedControls from '@/components/ui/SegmentedControls.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import TextInput from '@/components/ui/TextInput.vue';

export default {
    components: {
        TextInput,
        SegmentedControls,
        Button,
        SortIcon,
        Table,
        TableHeadRow,
        TableRow,
        TableHeader,
        TableCell,
        Card,
        Dropdown,
    },

    props: {
        portfolio: Object,
        dividendsReceived: Object,
    },

    data() {
        return {
            localDividendsReceived: this.dividendsReceived.data,
            filters: {
                sortBy: 'ticker',
                sortDirection: 'asc',
            },
            excludedTickers: [],
            selectedYear: this.dividendsReceived.yearsWithData?.[0] || new Date().getFullYear(),
            selectedQuarters: [1, 2, 3, 4],
        };
    },

    computed: {
        filteredDividends() {
            let results = this.localDividendsReceived;

            if (this.selectedYear) {
                results = results.filter((dividend) => new Date(dividend.date).getFullYear() === this.selectedYear);
            }

            if (this.selectedQuarters.length) {
                results = results.filter((dividend) => {
                    const month = parseInt(dividend.date.split('-')[1], 10); // 1 = Jan, 12 = Dec
                    const quarter = Math.floor((month - 1) / 3) + 1;
                    return this.selectedQuarters.includes(quarter);
                });
            }

            if (this.portfolio?.holdings?.length) {
                results = results.filter((dividend) => !this.excludedTickers.includes(dividend.security.ticker));
            }

            return results;
        },
    },

    watch: {
        dividendsReceived() {
            this.localDividendsReceived = this.dividendsReceived.data;
        },
    },

    methods: {
        toggleTicker(ticker) {
            const index = this.excludedTickers.indexOf(ticker);
            if (index !== -1) {
                this.excludedTickers.splice(index, 1);
            } else {
                this.excludedTickers.push(ticker);
            }
        },

        toggleQuarter(q) {
            const index = this.selectedQuarters.indexOf(q);
            if (index !== -1) {
                this.selectedQuarters.splice(index, 1);
            } else {
                this.selectedQuarters.push(q);
            }
        },

        async sort(sortBy) {
            this.filters.sortBy = sortBy;
            this.filters.sortDirection = this.filters.sortDirection === 'asc' ? 'desc' : 'asc';

            axios
                .post(route('portfolios.sort-dividends-received'), {
                    dividendsReceived: this.dividendsReceived.data,
                    ...(this.filters.sortBy && { sortBy: this.filters.sortBy }),
                    ...(this.filters.sortDirection && { sortDirection: this.filters.sortDirection }),
                })
                .then((response) => {
                    this.localDividendsReceived = response.data;
                })
                .catch((error) => {
                    toast.error(error);
                });
        },
    },
};
</script>
