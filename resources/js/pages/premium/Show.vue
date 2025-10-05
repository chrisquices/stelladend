<template>
    <Head :title="selectedWatchlist.name" />

    <AuthLayout>
        <Watchlists :watchlists="watchlists" :selectedWatchlist="selectedWatchlist" />

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            <!-- Dividend Yield Range -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Dividend Yield Range</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="metrics.dividendYieldRange">{{ metrics.dividendYieldRange }}</span>
                            <span v-if="!metrics.dividendYieldRange">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Average Dividend Yield -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Average Dividend Yield</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="metrics.averageDividendYield">{{ metrics.averageDividendYieldFormatted }}</span>
                            <span v-if="!metrics.averageDividendYield">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Top Dividend Per Share -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Top Dividend Per Share</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="metrics.topDividendPerShare">{{ metrics.topDividendPerShareFormatted }}</span>
                            <span v-if="!metrics.topDividendPerShare">-</span>
                        </p>
                        <p v-if="metrics.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="metrics.secondaryCurrency.topDividendPerShare">{{
                                    metrics.secondaryCurrency.topDividendPerShareFormatted
                                }}</span>
                            <span v-if="!metrics.secondaryCurrency.topDividendPerShare">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Average Change -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">
                        Average Change
                    </p>

                    <div class="space-y-1 text-center">
                        <p
                            class="font-mono text-xl font-medium tracking-wide"
                            :class="{
                                '!text-restorative': metrics.averageChangeInDollar > 0,
                                '!text-destructive': metrics.averageChangeInDollar < 0,
                            }"
                        >
                            <span v-if="metrics.averageChangeInDollar">
                                {{ metrics.averageChangeInDollarFormatted }}
                                ({{ metrics.averageChangeInPercentageFormatted }})
                            </span>
                            <span v-if="!metrics.averageChangeInDollar">-</span>
                        </p>
                        <p
                            v-if="metrics.secondaryCurrency"
                            class="font-mono text-base font-medium tracking-widest text-text-muted"
                            :class="{
                                '!text-restorative': metrics.averageChangeInDollar > 0,
                                '!text-destructive': metrics.averageChangeInDollar < 0,
                            }"
                        >
                            <span v-if="metrics.secondaryCurrency.averageChangeInDollar">
                                {{ metrics.secondaryCurrency.averageChangeInDollarFormatted }}
                            </span>
                            <span v-if="!metrics.secondaryCurrency.averageChangeInDollar">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Watching -->
            <div class="col-span-full">
                <div class="space-y-4">
                    <div class="flex flex-col items-center justify-between gap-6 sm:flex-row sm:gap-8">
                        <div class="flex w-full items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="lni lni-bookmark-1 !text-xl"></i>
                                <p class="text-xl font-medium text-text">Watching ({{ localWatchlistSecurities.length }})</p>
                                <p
                                    v-if="localIsMarketOpen"
                                    class="animate-pulse cursor-default text-restorative"
                                    v-tooltip="'Live market data – auto-refreshing while the market is open'"
                                >
                                    ●
                                </p>
                                <p
                                    v-if="!localIsMarketOpen"
                                    class="animate-pulse cursor-default text-text-muted"
                                    v-tooltip="'Market is closed – showing latest available data'"
                                >
                                    ●
                                </p>
                            </div>

                            <!-- Export -->
                            <div class="sm:hidden">
                                <Button v-if="localWatchlistSecurities.length" size="normal" color="primary" @click="exportWatchlist()">
                                    <i class="lni lni-download-1"></i>
                                    <span class="hidden md:block">Export</span>
                                </Button>
                            </div>
                        </div>

                        <div class="flex w-full items-center justify-between gap-4 sm:w-auto">
                            <!-- Export -->
                            <div class="hidden sm:!block">
                                <Button v-if="localWatchlistSecurities.length" size="normal" color="primary" @click="exportWatchlist()">
                                    <i class="lni lni-download-1"></i>
                                    <span class="hidden md:block">Export</span>
                                </Button>
                            </div>

                            <!-- Add to Watchlist -->
                            <Dropdown ref="addToWatchlistDropdown" direction="left" class="w-full">
                                <template #trigger>
                                    <TextInput
                                        color="input"
                                        ref="addToWatchlistSearch"
                                        v-model="search"
                                        placeholder="Add a security to the list..."
                                        class="w-full sm:w-80"
                                        background="input"
                                        @keydown.enter.prevent="handleEnter"
                                        @keydown.down.prevent="moveHighlightDown"
                                        @keydown.up.prevent="moveHighlightUp"
                                    />
                                </template>

                                <template #content>
                                    <ul v-if="securities.length > 0" class="max-h-80 w-full overflow-y-auto">
                                        <li
                                            ref="addToWatchlistSecurityList"
                                            v-for="(security, index) in securities"
                                            class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover"
                                            :class="{ '!bg-hover': index === highlightedIndex }"
                                            @click="storeWatchlistSecurity(security)"
                                        >
                                            <div class="flex w-full items-center gap-2">
                                                <div class="w-7 flex-shrink-0">
                                                    <img v-if="security.logoUrl" :src="security.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                                    <div
                                                        v-if="!security.logoUrl"
                                                        class="flex h-7 w-7 items-center justify-center rounded text-white"
                                                        :style="{ backgroundColor: hexToRgba(security.color, 0.5) }"
                                                    >
                                                        {{ security.ticker.charAt(0) }}
                                                    </div>
                                                </div>
                                                <p class="whitespace-nowrap">{{ security.ticker }}</p>
                                                <p class="truncate whitespace-nowrap text-text-muted">{{ security.name }}</p>
                                            </div>
                                        </li>
                                    </ul>

                                    <ul v-if="securities.length === 0" class="h-20 overflow-y-auto">
                                        <li class="flex h-full w-full items-center justify-center">
                                            <p v-if="!search">Type something</p>
                                            <p v-if="search">No tickers found</p>
                                        </li>
                                    </ul>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <Card>
                        <!-- Watchlist Securities -->
                        <Table v-if="localWatchlistSecurities.length" size="comfortable" class="-mx-4">
                            <template #head>
                                <TableHeadRow>
                                    <!-- Index -->
                                    <TableHeader class="w-1">N°</TableHeader>

                                    <!-- Security -->
                                    <TableHeader>
                                        <div class="flex items-center gap-2" @click="sort('ticker')">
                                            <span>Security</span>
                                            <SortIcon v-if="localFilters.sortBy === 'ticker'" :direction="localFilters.sortDirection" />
                                        </div>
                                    </TableHeader>

                                    <!-- Est. Next Ex-div -->
                                    <TableHeader>
                                        <div class="flex items-center justify-end gap-2" @click="sort('nextExDividendDate')">
                                            <span>Est. Next Ex-div</span>
                                            <SortIcon v-if="localFilters.sortBy === 'nextExDividendDate'" :direction="localFilters.sortDirection" />
                                        </div>
                                    </TableHeader>

                                    <!-- Est. Next Payment -->
                                    <TableHeader>
                                        <div class="flex items-center justify-end gap-2" @click="sort('nextPaymentDate')">
                                            <span>Est. Next Payment</span>
                                            <SortIcon v-if="localFilters.sortBy === 'nextPaymentDate'" :direction="localFilters.sortDirection" />
                                        </div>
                                    </TableHeader>

                                    <!-- Frequency -->
                                    <TableHeader>
                                        <div class="flex items-center gap-2" @click="sort('dividendPayoutFrequency')">
                                            <span>Frequency</span>
                                            <SortIcon
                                                v-if="localFilters.sortBy === 'dividendPayoutFrequency'"
                                                :direction="localFilters.sortDirection"
                                            />
                                        </div>
                                    </TableHeader>

                                    <!-- Div. Yield -->
                                    <TableHeader>
                                        <div class="flex items-center justify-end gap-2" @click="sort('currentDividendYield')">
                                            <span>Div. Yield</span>
                                            <SortIcon v-if="localFilters.sortBy === 'currentDividendYield'" :direction="localFilters.sortDirection" />
                                        </div>
                                    </TableHeader>

                                    <!-- Div. Per Share -->
                                    <TableHeader>
                                        <div class="flex items-center justify-end gap-2" @click="sort('currentDividendPerShare')">
                                            <span>Div. Per Share</span>
                                            <SortIcon
                                                v-if="localFilters.sortBy === 'currentDividendPerShare'"
                                                :direction="localFilters.sortDirection"
                                            />
                                        </div>
                                    </TableHeader>

                                    <!-- Current Price -->
                                    <TableHeader>
                                        <div class="flex items-center justify-end gap-2" @click="sort('currentMarketPrice')">
                                            <span>Current Price</span>
                                            <SortIcon v-if="localFilters.sortBy === 'currentMarketPrice'" :direction="localFilters.sortDirection" />
                                        </div>
                                    </TableHeader>

                                    <!-- Change In Dollar & Percentage -->
                                    <TableHeader class="text-center">
                                        <div class="flex items-center justify-end gap-2" @click="sort('currentChangeInDollar')">
                                            <span>Change</span>
                                            <SortIcon
                                                v-if="localFilters.sortBy === 'currentChangeInDollar'"
                                                :direction="localFilters.sortDirection"
                                            />
                                        </div>
                                    </TableHeader>

                                    <!-- Actions -->
                                    <TableHeader class="w-1 text-right"></TableHeader>
                                </TableHeadRow>
                            </template>

                            <template #body>
                                <TableRow v-for="(watchlistSecurity, index) in localWatchlistSecurities" :key="`table-watchlist-row-${index}`" :striped="true">
                                    <!-- Index -->
                                    <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ index + 1 }}
                                    </TableCell>

                                    <!-- Security -->
                                    <TableCell class="font-bold">
                                        <div class="flex items-center gap-4">
                                            <div class="w-7 flex-shrink-0">
                                                <img
                                                    v-if="watchlistSecurity.logoUrl"
                                                    :src="watchlistSecurity.logoUrl"
                                                    alt="company logo"
                                                    class="h-7 w-7 rounded"
                                                />
                                                <div
                                                    v-if="!watchlistSecurity.logoUrl"
                                                    class="flex h-7 w-7 items-center justify-center rounded text-white"
                                                    :style="{ backgroundColor: hexToRgba(watchlistSecurity.color, 0.5) }"
                                                >
                                                    {{ watchlistSecurity.ticker.charAt(0) }}
                                                </div>
                                            </div>
                                            <p>
                                                {{ watchlistSecurity.ticker }}
                                            </p>
                                        </div>
                                    </TableCell>

                                    <!-- Next Ex-div -->
                                    <TableCell class="text-right">
                                        <div v-if="watchlistSecurity.nextExDividendDate">
                                            <p class="font-mono font-medium tracking-widest">{{ watchlistSecurity.nextExDividendDate }}</p>
                                            <p
                                                v-if="watchlistSecurity.nextExDividendDate"
                                                class="font-mono font-medium tracking-widest text-text-muted"
                                            >
                                                {{ watchlistSecurity.daysUntilExDividendDate }} days left
                                            </p>
                                        </div>
                                        <div v-if="!watchlistSecurity.nextExDividendDate" class="text-text-muted">Not announced</div>
                                    </TableCell>

                                    <!-- Next Payment -->
                                    <TableCell class="text-right">
                                        <div v-if="watchlistSecurity.nextPaymentDate">
                                            <p class="font-mono font-medium tracking-widest">{{ watchlistSecurity.nextPaymentDate }}</p>
                                            <p v-if="watchlistSecurity.nextPaymentDate" class="font-mono font-medium tracking-widest text-text-muted">
                                                {{ watchlistSecurity.daysUntilPaymentDate }} days left
                                            </p>
                                        </div>
                                        <div v-if="!watchlistSecurity.nextPaymentDate" class="text-text-muted">Not announced</div>
                                    </TableCell>

                                    <!-- Frequency -->
                                    <TableCell>
                                        <div v-if="watchlistSecurity.dividendPayoutFrequency">
                                            <p class="font-mono font-medium tracking-widest">
                                                {{ watchlistSecurity.dividendPayoutFrequencyFormatted }}
                                            </p>
                                            <p
                                                v-if="watchlistSecurity.secondaryCurrency"
                                                class="font-mono font-medium tracking-widest text-text-muted"
                                            >
                                                {{ watchlistSecurity.secondaryCurrency.dividendPayoutFrequencyFormatted }}
                                            </p>
                                        </div>
                                        <div v-if="!watchlistSecurity.dividendPayoutFrequency" class="text-text-muted">-</div>
                                    </TableCell>

                                    <!-- Div. Yield -->
                                    <TableCell class="text-right">
                                        <div v-if="watchlistSecurity.currentDividendYield">
                                            <p class="font-mono font-medium tracking-widest">{{ watchlistSecurity.currentDividendYieldFormatted }}</p>
                                            <p
                                                v-if="watchlistSecurity.secondaryCurrency"
                                                class="font-mono font-medium tracking-widest text-text-muted"
                                            >
                                                {{ watchlistSecurity.secondaryCurrency.currentDividendYieldFormatted }}
                                            </p>
                                        </div>
                                        <div v-if="!watchlistSecurity.currentDividendYield" class="text-text-muted">-</div>
                                    </TableCell>

                                    <!-- Dividend Per Share -->
                                    <TableCell class="text-right">
                                        <div v-if="watchlistSecurity.currentDividendPerShare">
                                            <p class="font-mono font-medium tracking-widest">
                                                {{ watchlistSecurity.currentDividendPerShareFormatted }}
                                            </p>
                                            <p
                                                v-if="watchlistSecurity.secondaryCurrency"
                                                class="font-mono font-medium tracking-widest text-text-muted"
                                            >
                                                {{ watchlistSecurity.secondaryCurrency.currentDividendPerShareFormatted }}
                                            </p>
                                        </div>
                                        <div v-if="!watchlistSecurity.currentDividendPerShare" class="text-text-muted">-</div>
                                    </TableCell>

                                    <!-- Price -->
                                    <TableCell class="text-right">
                                        <div v-if="watchlistSecurity.currentMarketPrice">
                                            <p class="font-mono font-medium tracking-widest">{{ watchlistSecurity.currentMarketPriceFormatted }}</p>
                                            <p
                                                v-if="watchlistSecurity.secondaryCurrency"
                                                class="font-mono font-medium tracking-widest text-text-muted"
                                            >
                                                {{ watchlistSecurity.secondaryCurrency.currentMarketPriceFormatted }}
                                            </p>
                                        </div>
                                        <div v-if="!watchlistSecurity.currentMarketPrice" class="text-text-muted">-</div>
                                    </TableCell>

                                    <!-- Change In Dollar & Percentage -->
                                    <TableCell class="text-right">
                                        <div v-if="watchlistSecurity.currentChangeInDollar" class="font-mono">
                                            <div class="flex items-center justify-end gap-4">
                                                <div>
                                                    <p
                                                        v-if="watchlistSecurity.currentChangeInPercentage"
                                                        class="font-mono font-medium tracking-widest"
                                                    >
                                                        <span
                                                            :class="{
                                                                'text-restorative': watchlistSecurity.currentChangeInPercentage > 0,
                                                                'text-destructive': watchlistSecurity.currentChangeInPercentage < 0,
                                                            }"
                                                        >
                                                            <span v-if="watchlistSecurity.currentChangeInPercentage > 0">+</span
                                                            >{{ watchlistSecurity.currentChangeInPercentageFormatted }}
                                                        </span>
                                                    </p>
                                                    <p v-if="watchlistSecurity.secondaryCurrency">&nbsp;</p>
                                                </div>

                                                <div class="w-20">
                                                    <p
                                                        class="font-mono font-medium tracking-widest"
                                                        :class="{
                                                            'text-restorative': watchlistSecurity.currentChangeInDollar > 0,
                                                            'text-destructive': watchlistSecurity.currentChangeInDollar < 0,
                                                        }"
                                                    >
                                                        <span v-if="watchlistSecurity.currentChangeInDollar > 0">+</span
                                                        >{{ watchlistSecurity.currentChangeInDollarFormatted }}
                                                    </p>
                                                    <p
                                                        v-if="watchlistSecurity.secondaryCurrency"
                                                        class="font-mono font-medium tracking-widest text-text-muted"
                                                        :class="{
                                                            'text-restorative': watchlistSecurity.currentChangeInDollar > 0,
                                                            'text-destructive': watchlistSecurity.currentChangeInDollar < 0,
                                                        }"
                                                    >
                                                        <span v-if="watchlistSecurity.currentChangeInDollar > 0">+</span
                                                        >{{ watchlistSecurity.secondaryCurrency.currentChangeInDollarFormatted }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="!watchlistSecurity.currentChangeInDollar" class="text-text-muted">-</div>
                                    </TableCell>

                                    <!-- Actions -->
                                    <TableCell>
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                size="small"
                                                color="transparent"
                                                onhover="destructive"
                                                class="text-text-muted hover:text-text"
                                                @click="deleteWatchlistSecurity(watchlistSecurity)"
                                                :disabled="isProcessing"
                                            >
                                                <i class="lni lni-trash-1"></i>
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </template>
                        </Table>

                        <p v-if="!localWatchlistSecurities.length" class="flex h-full items-center justify-center py-8 text-sm text-text-muted">
                            No securities added
                        </p>
                    </Card>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import DivThatLooksLikeTextInput from '@/components/ui/DivThatLooksLikeTextInput.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import Modal from '@/components/ui/Modal.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextArea from '@/components/ui/TextArea.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Watchlists from '@/pages/watchlists/partials/Watchlists.vue';
import { hexToRgba } from '@/utils/colorHelper.js';
import { isMarketOpen } from '@/utils/dateHelper.js';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import throttle from 'lodash/throttle';
import { toast } from 'vue-sonner';

export default {
    components: {
        TextLabel,
        TextArea,
        TextInput,
        DivThatLooksLikeTextInput,
        Watchlists,
        SortIcon,
        Modal,
        TableHeadRow,
        TableCell,
        Table,
        TableRow,
        TableHeader,
        Card,
        AuthLayout,
        Head,
        Button,
        Dropdown,
    },

    props: {
        selectedWatchlist: Object,
        watchlists: Object,
        metrics: Object,
        filters: Object,
    },

    data() {
        return {
            localIsMarketOpen: this.isMarketOpen(),
            localWatchlistSecurities: [],
            localFilters: {
                sortBy: 'ticker',
                sortDirection: 'asc',
            },
            search: null,
            securities: [],
            highlightedIndex: 0,
            isProcessing: false,
            pollInterval: null,
        };
    },

    computed: {},

    mounted() {
        this.throttledReload = throttle(this.reload, 3000);
        this.localWatchlistSecurities = this.selectedWatchlist.watchlistSecurities;

        this.startMarketPollingCheck();
    },

    unmounted() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }

        if (this.marketCheckInterval) {
            clearInterval(this.marketCheckInterval);
            this.marketCheckInterval = null;
        }

        if (this.throttledReload?.cancel) {
            this.throttledReload.cancel();
        }
    },

    watch: {
        selectedWatchlist() {
            this.localWatchlistSecurities = this.selectedWatchlist.watchlistSecurities;
        },

        search: function () {
            this.getSecurities();
        },
    },

    methods: {
        hexToRgba,
        isMarketOpen,

        startMarketPollingCheck() {
            const checkMarketOpen = () => {
                const isOpen = this.isMarketOpen();

                if (isOpen && !this.pollInterval) {
                    // Start polling
                    this.pollInterval = setInterval(() => {
                        if (document.hasFocus()) {
                            this.throttledReload();
                        }
                    }, 5000);
                } else if (!isOpen && this.pollInterval) {
                    // Stop polling
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }
            };

            checkMarketOpen();

            // Re-check every 60 seconds in case market status changes
            this.marketCheckInterval = setInterval(checkMarketOpen, 60000);

            // Update market status
            this.localIsMarketOpen = this.isMarketOpen();
        },

        exportWatchlist() {
            const date = new Date();
            const nyDateString = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'America/New_York',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            }).format(date);

            const slugifiedName = this.selectedWatchlist.name
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9\-]/g, '')
                .replace(/--+/g, '-')
                .replace(/^-+|-+$/g, '');

            axios
                .post(
                    route('watchlists.export'),
                    {
                        watchlistUid: this.selectedWatchlist.uid,
                    },
                    {
                        responseType: 'blob',
                    },
                )
                .then((response) => {
                    const blob = new Blob([response.data], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');

                    link.href = url;
                    link.setAttribute('download', `${slugifiedName}-${nyDateString}.csv`);
                    document.body.appendChild(link);
                    link.click();

                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);

                    toast.success('Watchlist exported');
                })
                .catch((error) => {
                    toast.error('Failed to export watchlist.');
                });
        },

        startProcessing() {
            this.isProcessing = true;
        },

        finishProcessing() {
            this.isProcessing = false;
        },

        async sort(sortBy) {
            this.localFilters.sortBy = sortBy;
            this.localFilters.sortDirection = this.localFilters.sortDirection === 'asc' ? 'desc' : 'asc';

            await this.throttledReload();
        },

        async reload() {
            router.reload({
                preserveScroll: true,
                preserveState: true,
                data: {
                    sortBy: this.localFilters.sortBy,
                    sortDirection: this.localFilters.sortDirection,
                },
            });
        },

        storeWatchlistSecurity(security) {
            const watchlistUid = this.selectedWatchlist.uid;
            const securityUid = security.uid;

            this.startProcessing();

            router.post(
                route('watchlists.watchlist-securities.store', { watchlistUid: watchlistUid }),
                {
                    securityUid: securityUid,
                },
                {
                    preserveScroll: true,
                    onStart: () => {
                        this.startProcessing();
                    },
                    onFinish: () => {
                        this.finishProcessing();
                    },
                    onSuccess: () => {
                        this.search = '';

                        toast.success('Security added');
                    },
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                },
            );
        },

        deleteWatchlistSecurity(security) {
            const watchlistUid = this.selectedWatchlist.uid;
            const securityUid = security.uid;

            router.delete(route('watchlists.watchlist-securities.delete', { watchlistUid: watchlistUid, securityUid: securityUid }), {
                preserveScroll: true,
                onStart: () => {
                    this.startProcessing();
                },
                onFinish: () => {
                    this.finishProcessing();
                },
                onSuccess: () => {
                    toast.success('Security removed');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Search Dropdown ----------------------------------------------------------------------------------------------------------------------------------
        async getSecurities() {
            try {
                const response = await axios.get(route('watchlists.get-securities'), {
                    params: {
                        search: this.search || '',
                    },
                });

                const existingUids = this.localWatchlistSecurities.map((h) => h.uid);

                this.securities = response.data.filter((security) => {
                    return !existingUids.includes(security.uid);
                });
            } catch (error) {
                toast.error(error);
            }
        },

        // Dropdown ----------------------------------------------------------------------------------------------------------------------------------
        moveHighlightDown() {
            const max = this.securities.length - 1;
            if (this.highlightedIndex < max) {
                this.highlightedIndex++;
                this.scrollHighlightedItemIntoView();
            }
        },

        moveHighlightUp() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
                this.scrollHighlightedItemIntoView();
            }
        },

        scrollHighlightedItemIntoView() {
            this.$nextTick(() => {
                const listItems = this.$refs.addToWatchlistSecurityList;
                const item = listItems?.[this.highlightedIndex];
                item?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            });
        },

        handleEnter() {
            const firstItem = this.securities?.[this.highlightedIndex];

            this.storeWatchlistSecurity(firstItem);

            this.$nextTick(() => {
                this.$refs.addToWatchlistDropdown?.closeDropdown();
                this.$refs.addToWatchlistSearch?.click?.();
            });
        },
    },
};
</script>
