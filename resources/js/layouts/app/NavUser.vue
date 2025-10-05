<template>
    <div class="flex items-center justify-end gap-4">
        <div class="flex items-center gap-2">
            <p class="text-text-muted tracking-widest font-medium text-xs hidden lg:block">
                Market is
                <span v-if="isMarketCurrentlyOpen" class="text-restorative">OPEN</span>
                <span v-if="!isMarketCurrentlyOpen">CLOSED</span>
            </p>
        </div>

        <!-- Options -->
        <Dropdown width="100" direction="left">
            <template #trigger>
                <div
                    class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full text-text transition-all duration-300"
                >
                    <i class="lni lni-user-4 lni-flip-x cursor-pointer !text-xl"></i>
                </div>
            </template>

            <template #content>
                <ul class="max-h-80 w-full overflow-y-auto">
                    <li class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover text-text">
                        <Link :href="route('settings.index')" class="flex items-center gap-2">
                            <i class="lni lni-gear-1"></i>
                            <p>Account Settings</p>
                        </Link>
                    </li>

<!--                    <li class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover text-text">-->
<!--                        <Link :href="route('premium.index')" class="flex items-center gap-2">-->
<!--                            <i class="lni lni-star-fat"></i>-->
<!--                            <p>Premium</p>-->
<!--                        </Link>-->
<!--                    </li>-->

                    <li class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover text-text">
                        <a href="#" @click.prevent="submitLogout" class="flex items-center gap-2">
                            <i class="lni lni-exit lni-flip-x"></i>
                            <p>Logout</p>
                        </a>

                        <form id="logout-form" action="/logout" method="POST" class="hidden">
                            <input type="hidden" name="_token" />
                        </form>
                    </li>
                </ul>
            </template>
        </Dropdown>
    </div>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import { Link, router } from '@inertiajs/vue3';
import TextInput from '@/components/ui/TextInput.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import { isMarketOpen } from '@/utils/dateHelper.js';

export default {
    name: 'NavUser',

    components: {
        TextInput,
        Button,
        Link,
        Dropdown
    },

    data() {
        return {
            isMarketCurrentlyOpen: false,
            isCheckingMarketRealtime: false,
            marketRealtimeInterval: null,
            pollInterval: null,
        };
    },

    mounted() {
        // Initial market status check
        this.checkMarketStatusOnce();

        // Poll every 30s to see if we should start or stop real-time monitoring
        this.pollInterval = setInterval(() => {
            this.startMarketRealtimeMonitoring();
        }, 30000);

        // Start it immediately once
        this.startMarketRealtimeMonitoring();
    },

    beforeUnmount() {
        if (this.marketRealtimeInterval) {
            clearInterval(this.marketRealtimeInterval);
        }
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
    },

    methods: {
        isMarketOpen,

        checkMarketStatusOnce() {
            this.isMarketCurrentlyOpen = this.isMarketOpen();
        },

        startMarketRealtimeMonitoring() {
            const now = new Date();
            const nyTime = new Date(now.toLocaleString('en-US', { timeZone: 'America/New_York' }));
            const hours = nyTime.getHours();
            const minutes = nyTime.getMinutes();

            const inMorningWindow = (hours === 9 && minutes >= 29 && minutes <= 31);
            const inClosingWindow = (hours === 15 && minutes >= 59) || (hours === 16 && minutes <= 1);
            const shouldUpdateRealtime = inMorningWindow || inClosingWindow;

            if (shouldUpdateRealtime && !this.isCheckingMarketRealtime) {
                // Start realtime checks
                this.marketRealtimeInterval = setInterval(() => {
                    this.checkMarketStatusOnce();
                }, 1000);

                this.isCheckingMarketRealtime = true;
            } else if (!shouldUpdateRealtime && this.isCheckingMarketRealtime) {
                // Stop realtime checks
                clearInterval(this.marketRealtimeInterval);
                this.marketRealtimeInterval = null;
                this.isCheckingMarketRealtime = false;

                // Final status update
                this.checkMarketStatusOnce();
            }
        },

        submitLogout() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                document.querySelector('#logout-form input[name="_token"]').value = token;
                document.getElementById('logout-form').submit();
            } else {
                console.error('CSRF token not found.');
            }
        },
    },
};
</script>
