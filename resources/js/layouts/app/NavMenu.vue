<template>
    <ul class="flex">
        <!-- Dashboard -->
        <li class="text-text">
            <Link
                :href="route('dashboard.index')"
                class="group flex items-center gap-2 rounded-full px-3 py-2 text-sm transition-all duration-300 hover:bg-primary-foreground/90 sm:px-4 sm:py-1"
                @mouseenter="hoveredItem = 'dashboard'"
                @mouseleave="hoveredItem = null"
                :class="{ 'bg-primary-foreground text-text-inverted ': currentActive === 'dashboard' }"
            >
                <i class="lni lni-dashboard-square-1 !text-xl"></i>
                <p
                    :class="[
                        'hidden whitespace-nowrap transition-all duration-300 ease-in-out tracking-wide',
                        currentActive === 'dashboard' ? 'max-w-[150px] opacity-100 sm:block' : 'max-w-0 overflow-hidden opacity-0 sm:block',
                    ]"
                >
                    Dashboard
                </p>
            </Link>
        </li>

        <!-- Portfolios -->
        <li class="text-text">
            <Link
                :href="route('portfolios.index')"
                class="group flex items-center gap-2 rounded-full px-3 py-2 text-sm transition-all duration-300 hover:bg-primary-foreground/90 sm:px-4 sm:py-1"
                @mouseenter="hoveredItem = 'portfolios'"
                @mouseleave="hoveredItem = null"
                :class="{ 'bg-primary-foreground text-text-inverted ': currentActive === 'portfolios' }"
            >
                <i class="lni lni-pie-chart-2 !text-xl"></i>
                <p
                    :class="[
                        'hidden whitespace-nowrap transition-all duration-300 ease-in-out tracking-wide',
                        currentActive === 'portfolios' ? 'max-w-[150px] opacity-100 sm:block' : 'max-w-0 overflow-hidden opacity-0 sm:block',
                    ]"
                >
                    Portfolios
                </p>
            </Link>
        </li>

        <!-- Watchlists -->
        <li class="text-text">
            <Link
                :href="route('watchlists.index')"
                class="group flex items-center gap-2 rounded-full px-3 py-2 text-sm transition-all duration-300 hover:bg-primary-foreground/90 sm:px-4 sm:py-1"
                @mouseenter="hoveredItem = 'watchlists'"
                @mouseleave="hoveredItem = null"
                :class="{ 'bg-primary-foreground text-text-inverted ': currentActive === 'watchlists' }"
            >
                <i class="lni lni-bookmark-1 !text-xl"></i>
                <p
                    :class="[
                        'hidden whitespace-nowrap transition-all duration-300 ease-in-out tracking-wide',
                        currentActive === 'watchlists' ? 'max-w-[150px] opacity-100 sm:block' : 'max-w-0 overflow-hidden opacity-0 sm:block',
                    ]"
                >
                    Watchlists
                </p>
            </Link>
        </li>

        <!-- Calendar -->
        <li class="text-text">
            <Link
                :href="route('calendar.index')"
                class="group flex items-center gap-2 rounded-full px-3 py-2 text-sm transition-all duration-300 hover:bg-primary-foreground/90 sm:px-4 sm:py-1"
                @mouseenter="hoveredItem = 'calendar'"
                @mouseleave="hoveredItem = null"
                :class="{ 'bg-primary-foreground text-text-inverted ': currentActive === 'calendar' }"
            >
                <i class="lni lni-calendar !text-xl"></i>
                <p
                    :class="[
                        'hidden whitespace-nowrap transition-all duration-300 ease-in-out tracking-wide',
                        currentActive === 'calendar' ? 'max-w-[150px] opacity-100 sm:block' : 'max-w-0 overflow-hidden opacity-0 sm:block',
                    ]"
                >
                    Calendar
                </p>
            </Link>
        </li>

        <!-- Accounts -->
        <li class="text-text">
            <Link
                :href="route('accounts.index')"
                class="group flex items-center gap-2 rounded-full px-3 py-2 text-sm transition-all duration-300 hover:bg-primary-foreground/90 sm:px-4 sm:py-1"
                @mouseenter="hoveredItem = 'accounts'"
                @mouseleave="hoveredItem = null"
                :class="{ 'bg-primary-foreground text-text-inverted ': currentActive === 'accounts' }"
            >
                <i class="lni lni-wallet-4 !text-xl"></i>
                <p
                    :class="[
                        'hidden whitespace-nowrap transition-all duration-300 ease-in-out tracking-wide',
                        currentActive === 'accounts' ? 'max-w-[150px] opacity-100 sm:block' : 'max-w-0 overflow-hidden opacity-0 sm:block',
                    ]"
                >
                    Accounts
                </p>
            </Link>
        </li>
    </ul>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: 'NavMenu',

    components: {
        Link,
    },

    data() {
        return {
            hoveredItem: null,
        };
    },

    computed: {
        routeActivePage() {
            const component = this.$page.component;

            if (component.startsWith('dashboard')) return 'dashboard';
            if (component.startsWith('portfolios')) return 'portfolios';
            if (component.startsWith('watchlists')) return 'watchlists';
            if (component.startsWith('calendar')) return 'calendar';
            if (component.startsWith('accounts')) return 'accounts';
            if (component.startsWith('settings')) return 'settings';

            return '';
        },

        currentActive() {
            return this.hoveredItem || this.routeActivePage;
        },
    },
};
</script>
