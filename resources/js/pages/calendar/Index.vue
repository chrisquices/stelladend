<template>
    <Head title="Calendar" />

    <AuthLayout>
        <div class="space-y-4 lg:flex lg:h-full lg:flex-col">
            <div class="relative z-20 flex items-center justify-between lg:flex-none">
                <Dropdown width="100" :closeContentOnClick="false">
                    <template #trigger>
                        <Button size="normal" color="primaryForeground">
                            <i class="lni lni-funnel-2 !font-bold"></i>
                        </Button>
                    </template>

                    <template #content>
                        <div class="space-y-4 p-8">
                            <div class="space-y-2">
                                <p class="text-sm font-semibold whitespace-nowrap text-text">Calendar Sources</p>
                                <p class="text-xs whitespace-nowrap text-text-muted">Toggle securities from your portfolios and watchlists</p>
                            </div>

                            <div class="space-y-4">
                                <div class="space-y-4">
                                    <p class="text-xs text-text-muted">Portfolios</p>
                                    <div v-for="portfolio in portfolios" :key="`portfolio-option-${portfolio.uid}`">
                                        <Switch
                                            size="small"
                                            :model-value="portfolio.isIncludedInCalendar"
                                            :label="portfolio.name"
                                            @update:modelValue="() => togglePortfolioInclusion(portfolio.uid)"
                                        />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <p class="text-xs text-text-muted">Watchlists</p>
                                    <div v-for="watchlist in watchlists" :key="`watchlist-option-${watchlist.uid}`">
                                        <Switch
                                            size="small"
                                            :model-value="watchlist.isIncludedInCalendar"
                                            :label="watchlist.name"
                                            @update:modelValue="() => toggleWatchlistInclusion(watchlist.uid)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Dropdown>

                <!-- Period -->
                <div class="flex items-center">
                    <div class="flex items-center gap-1">
                        <Button size="normal" color="primaryForeground" @click="goToPreviousPeriod" :disabled="!canGoToPreviousPeriod">
                            <i class="lni lni-chevron-left"></i>
                        </Button>
                        <Dropdown>
                            <template #trigger>
                                <Button size="normal" color="primaryForeground" class="w-36">{{ selectedPeriod.label }}</Button>
                            </template>

                            <template #content>
                                <ul class="max-h-80 w-full overflow-y-auto">
                                    <li
                                        v-for="period in periods"
                                        class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover"
                                        @click="updateSelectedPeriod(period)"
                                    >
                                        {{ period.label }}
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>
                        <Button size="normal" color="primaryForeground" @click="goToNextPeriod" :disabled="!canGoToNextPeriod">
                            <i class="lni lni-chevron-right"></i>
                        </Button>
                    </div>
                </div>
            </div>

            <div class="lg:flex lg:flex-auto lg:flex-col">
                <div class="grid grid-cols-7 gap-px text-center text-xs leading-6 font-semibold lg:flex-none">
                    <div class="rounded-tl-lg bg-card py-2">S<span class="sr-only sm:not-sr-only">at</span></div>
                    <div class="bg-card py-2">M<span class="sr-only sm:not-sr-only">on</span></div>
                    <div class="bg-card py-2">T<span class="sr-only sm:not-sr-only">ue</span></div>
                    <div class="bg-card py-2">W<span class="sr-only sm:not-sr-only">ed</span></div>
                    <div class="bg-card py-2">T<span class="sr-only sm:not-sr-only">hu</span></div>
                    <div class="bg-card py-2">F<span class="sr-only sm:not-sr-only">ri</span></div>
                    <div class="rounded-tr-lg bg-card py-2">S<span class="sr-only sm:not-sr-only">un</span></div>
                </div>
                <div class="flex text-sm leading-6 lg:flex-auto">
                    <div class="hidden w-full lg:grid lg:grid-cols-7 lg:grid-rows-6 lg:gap-px">
                        <Card
                            v-for="calendarDay in calendarDays"
                            class="relative h-40 cursor-pointer !rounded-none !p-6 hover:bg-hover"
                            @click="selectCalendarDay(calendarDay)"
                        >
                            <!-- Darker BG for days not from current period -->
                            <div v-if="!calendarDay.isCurrentMonth" class="absolute inset-0 z-0 h-full bg-card-foreground/50"></div>

                            <!-- Day -->
                            <time
                                :datetime="calendarDay.date"
                                :class="{
                                    'flex h-6 w-6 items-center justify-center rounded-full bg-accent text-text-inverted': calendarDay.isToday,
                                }"
                            >
                                {{ calendarDay.day }}
                            </time>

                            <!-- Events -->
                            <ol class="mt-2 space-y-2">
                                <template v-for="(event, index) in calendarDay.events.slice(0, 3)" :key="`event-row-desktop-${index}`">
                                    <li v-if="index < 2">
                                        <div class="flex w-full items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-5 flex-shrink-0">
                                                    <img v-if="event.logoUrl" :src="event.logoUrl" alt="company logo" class="h-5 w-5 rounded" />
                                                    <div
                                                        v-else
                                                        class="flex h-5 w-5 items-center justify-center rounded text-white"
                                                        :style="{ backgroundColor: hexToRgba(event.color, 0.5) }"
                                                    ></div>
                                                </div>
                                                <p class="whitespace-nowrap">{{ event.ticker }}</p>
                                            </div>

                                            <p class="font-mono font-bold tracking-widest text-text-muted">
                                                {{ event.type === 'ex' ? 'Ex-D' : 'Pay' }}
                                            </p>
                                        </div>
                                    </li>

                                    <li v-else-if="index === 2">
                                        <p class="text-sm text-text-muted">
                                            + {{ calendarDay.events.length - 2 }} more event{{ calendarDay.events.length - 2 > 1 ? 's' : '' }}
                                        </p>
                                    </li>
                                </template>
                            </ol>
                        </Card>
                    </div>

                    <!-- Mobile Calendar-->
                    <div class="isolate grid w-full grid-cols-7 grid-rows-6 gap-px lg:hidden">
                        <Button
                            v-for="calendarDay in calendarDays"
                            color="primary"
                            class="relative flex h-14 flex-col items-start justify-start rounded-none !px-2 !py-1 text-text hover:bg-hover focus:z-10"
                            :class="{
                                '!bg-black/50': !calendarDay.isCurrentMonth,
                            }"
                            @click="selectCalendarDayMobile(calendarDay)"
                        >
                            <!-- Darker BG for days not from current period -->
                            <div v-if="!calendarDay.isCurrentMonth" class="absolute inset-0 z-0 h-full bg-black/50"></div>

                            <!-- Day -->
                            <time :datetime="calendarDay.date" class="text-left">{{ calendarDay.day }}</time>

                            <!-- Number of events -->
                            <div class="-mx-0.5 mt-auto flex flex-wrap-reverse">
                                <div v-for="event in calendarDay.events" class="mx-0.5 mb-1 h-1.5 w-1.5 rounded-full bg-gray-400"></div>
                            </div>
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Mobile Event List -->
            <Card v-if="calendarModal.selectedCalendarDay" class="lg:hidden">
                <p class="font-medium text-text">{{ calendarModal.selectedCalendarDay.dateFormatted }} Events</p>

                <ol class="space-y-6">
                    <li
                        v-for="(event, index) in calendarModal.selectedCalendarDay.events"
                        :key="`event-row-mobile-${index}`"
                        class="text-sm"
                    >
                        <div class="flex flex-col gap-4">
                            <div class="flex w-full items-center justify-between gap-4">
                                <!-- Security -->
                                <div class="flex items-center gap-4">
                                    <div class="w-7 flex-shrink-0">
                                        <img v-if="event.logoUrl" :src="event.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                        <div
                                            v-if="!event.logoUrl"
                                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                                            :style="{ backgroundColor: hexToRgba(event.color, 0.5) }"
                                        >
                                            {{ event.ticker.charAt(0) }}
                                        </div>
                                    </div>
                                    <p class="truncate whitespace-nowrap">{{ event.ticker }}</p>
                                </div>

                                <!-- Type -->
                                <p class="font-mono text-sm font-bold tracking-widest text-text-muted">
                                    {{ event.type === 'ex' ? 'Ex-Dividend' : 'Payment' }}
                                </p>
                            </div>

                            <div class="space-y-4">
                                <!-- Dividend Amount -->
                                <div class="flex items-center justify-between gap-4">
                                    <p class="text-text-muted">Dividend Amount</p>

                                    <div class="text-right text-sm">
                                        <div v-if="event.amount">
                                            <p class="font-mono font-medium tracking-widest">{{ event.amountFormatted }}</p>
                                            <p v-if="event.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                                {{ event.secondaryCurrency.amountFormatted }}
                                            </p>
                                        </div>
                                        <div v-if="!event.amount" class="text-text-muted">-</div>
                                    </div>
                                </div>

                                <!-- Shares Owned -->
                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <p class="text-text-muted">Shares Owned</p>

                                    <!-- Dividend Amount -->
                                    <p v-if="event.currentSharesOwned" class="font-mono font-medium tracking-widest">
                                        {{ event.currentSharesOwnedFormatted }}
                                    </p>
                                    <p v-if="!event.currentSharesOwned" class="font-mono font-medium tracking-widest">-</p>
                                </div>

                                <!-- Expected -->
                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <p class="text-text-muted">Expected</p>

                                    <!-- Expected -->
                                    <p v-if="event.totalExpected" class="font-mono font-medium tracking-widest">{{ event.totalExpectedFormatted }}</p>
                                    <p v-if="!event.totalExpected" class="font-mono font-medium tracking-widest">-</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ol>
            </Card>
        </div>

        <!-- Calendar Modal -->
        <Modal v-model:isOpen="calendarModal.isOpen" size="6extra-large">
            <div v-if="calendarModal.selectedCalendarDay" class="grid grid-cols-1 gap-6 sm:gap-8">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">{{ calendarModal.selectedCalendarDay.dateFormatted }} Events</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleCalendarModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Table of Account Movements-->
                <Table v-if="calendarModal.selectedCalendarDay.events.length" size="comfortable" class="-mx-4">
                    <template #head>
                        <TableHeadRow>
                            <!-- Event -->
                            <TableHeader>Event</TableHeader>

                            <!-- Security -->
                            <TableHeader>Security</TableHeader>

                            <!-- Dividend Amount -->
                            <TableHeader class="text-right">Dividend Amount</TableHeader>

                            <!-- Current Shares Owned -->
                            <TableHeader class="text-right">Shares Owned</TableHeader>

                            <!-- Expected -->
                            <TableHeader class="text-right">Expected</TableHeader>
                        </TableHeadRow>
                    </template>

                    <template #body>
                        <TableRow v-for="event in calendarModal.selectedCalendarDay.events">
                            <!-- Type -->
                            <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                <div class="flex items-center gap-2">
                                    <i v-if="event.type === 'ex'" class="lni lni-clock-eight !text-text-muted !text-base"></i>
                                    <i v-if="event.type === 'pay'" class="lni lni-check-circle-2 !text-restorative !text-base"></i>
                                    {{ event.type === 'ex' ? 'Ex-Dividend' : 'Payment' }}
                                </div>
                            </TableCell>

                            <!-- Security -->
                            <TableCell class="font-bold">
                                <div class="flex items-center gap-4">
                                    <div class="w-7 flex-shrink-0">
                                        <img v-if="event.logoUrl" :src="event.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                        <div
                                            v-if="!event.logoUrl"
                                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                                            :style="{ backgroundColor: hexToRgba(event.color, 0.5) }"
                                        >
                                            {{ event.ticker.charAt(0) }}
                                        </div>
                                    </div>
                                    <p>
                                        {{ event.ticker }}
                                        <span class="ml-2 w-10 truncate whitespace-nowrap text-text-muted">{{ event.name }}</span>
                                    </p>
                                </div>
                            </TableCell>

                            <!-- Dividend Amount -->
                            <TableCell class="text-right">
                                <div v-if="event.amount">
                                    <p class="font-mono font-medium tracking-widest">{{ event.amountFormatted }}</p>
                                    <p v-if="event.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ event.secondaryCurrency.amountFormatted }}
                                    </p>
                                </div>
                                <div v-if="!event.amount" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Current Shares Owned -->
                            <TableCell class="text-right">
                                <div v-if="event.currentSharesOwned">
                                    <p class="font-mono font-medium tracking-widest">{{ event.currentSharesOwnedFormatted }}</p>
                                    <p v-if="event.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ event.secondaryCurrency.currentSharesOwnedFormatted }}
                                    </p>
                                </div>
                                <div v-if="!event.currentSharesOwned" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Expected -->
                            <TableCell class="text-right">
                                <div v-if="event.totalExpected">
                                    <p class="font-mono font-medium tracking-widest">{{ event.totalExpectedFormatted }}</p>
                                    <p v-if="event.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ event.secondaryCurrency.totalExpectedFormatted }}
                                    </p>
                                </div>
                                <div v-if="!event.totalExpected" class="text-text-muted">-</div>
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>

                <p
                    v-if="!calendarModal.selectedCalendarDay.events.length"
                    class="flex h-full items-center justify-center pt-32 pb-26 text-sm text-text-muted"
                >
                    No events found
                </p>

                <!-- Close -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" @click="toggleCalendarModal">Close</Button>
                </div>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import InputError from '@/components/ui/InputError.vue';
import Modal from '@/components/ui/Modal.vue';
import Switch from '@/components/ui/Switch.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextArea from '@/components/ui/TextArea.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { hexToRgba } from '@/utils/colorHelper.js';
import { Head, router } from '@inertiajs/vue3';

export default {
    components: {
        Switch,
        TableCell,
        TableHeader,
        TableRow,
        TableHeadRow,
        Table,
        InputError,
        TextInput,
        TextLabel,
        TextArea,
        Modal,
        Button,
        Card,
        AuthLayout,
        Head,
        Dropdown,
    },

    props: {
        periods: Object,
        calendarDays: Object,
        portfolios: Object,
        watchlists: Object,
    },

    data() {
        return {
            selectedPeriod: this.periods.find((p) => p.isCurrentPeriod) || this.periods[0],

            // Calendar Modal
            calendarModal: {
                isOpen: false,
                selectedCalendarDay: null,
            },
        };
    },

    computed: {
        selectedPeriodIndex() {
            if (!this.selectedPeriod || !this.selectedPeriod.period) {
                return -1; // or null if that's more appropriate for your case
            }

            return this.periods.findIndex((p) => p?.period === this.selectedPeriod.period);
        },

        canGoToPreviousPeriod() {
            return this.selectedPeriodIndex < this.periods.length - 1;
        },

        canGoToNextPeriod() {
            return this.selectedPeriodIndex > 0;
        },
    },

    methods: {
        hexToRgba,

        togglePortfolioInclusion(portfolioUid) {
            this.$inertia.patch(
                route('calendar.toggle-portfolio-inclusion'),
                {
                    portfolioUid: portfolioUid,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
                },
            );
        },

        toggleWatchlistInclusion(watchlistUid) {
            this.$inertia.patch(
                route('calendar.toggle-watchlist-inclusion'),
                {
                    watchlistUid: watchlistUid,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
                },
            );
        },

        goToPreviousPeriod() {
            if (this.canGoToPreviousPeriod) {
                const newPeriod = this.periods[this.selectedPeriodIndex - 1];
                this.updateSelectedPeriod(newPeriod);
            }
        },

        goToNextPeriod() {
            if (this.canGoToNextPeriod) {
                const newPeriod = this.periods[this.selectedPeriodIndex + 1];
                this.updateSelectedPeriod(newPeriod);
            }
        },

        updateSelectedPeriod(period) {
            this.selectedPeriod = period;

            router.reload({
                data: {
                    period: period.period,
                },
                preserveScroll: true,
                preserveState: true,
            });
        },

        toggleCalendarModal() {
            this.calendarModal.isOpen = !this.calendarModal.isOpen;
        },

        selectCalendarDay(calendarDay) {
            this.calendarModal.selectedCalendarDay = calendarDay;

            this.toggleCalendarModal();
        },

        selectCalendarDayMobile(calendarDay) {
            this.calendarModal.selectedCalendarDay = calendarDay;
        },
    },
};
</script>
