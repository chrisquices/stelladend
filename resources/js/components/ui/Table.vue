<template>
    <div class="relative min-w-0 group/main-table">
        <!-- Table wrapper with overflow -->
        <div class="w-full overflow-hidden">

            <div
                v-if="isOverflowing"
                class="pointer-events-none absolute right-0 top-0 h-full w-8 bg-gradient-to-l from-accent/7 to-transparent lg:hidden"
            ></div>

            <div class="relative w-full overflow-x-auto">
                <table class="min-w-full text-sm">
                    <slot name="head" :size="size"></slot>
                    <slot name="body" :size="size"></slot>
                </table>
            </div>
        </div>

        <!-- Options button (gear) - Hidden by default, appears on table hover -->
        <div class="absolute -right-12 top-0 z-50 opacity-0 transition-opacity duration-200 group-hover/main-table:opacity-100">
            <slot name="options" :size="size"></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Table',

    props: {
        size: {
            type: String,
            default: 'normal', // normal, compact, comfortable
        },
        mutedOnHover: {
            type: Boolean,
            default: false,
        },
    },

    provide() {
        return {
            tableSize: this.size,
        };
    },

    data() {
        return {
            isOverflowing: false,
        };
    },

    mounted() {
        this.checkOverflow();
        window.addEventListener('resize', this.checkOverflow);
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.checkOverflow);
    },

    methods: {
        checkOverflow() {
            this.$nextTick(() => {
                const el = this.$el.querySelector('.overflow-x-auto');
                this.isOverflowing = el && el.scrollWidth > el.clientWidth;
            });
        },
    }
};
</script>
