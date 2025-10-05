<template>
    <div
        :class="[
            'bg-card rounded-xl space-y-6 sm:space-y-8 p-6 sm:p-8',
            hoverable && 'transition-all duration-200 hover:shadow-md hover:-translate-y-0.5'
        ]"
    >
        <slot />
    </div>
</template>

<script>
export default {
    name: "Card",

    props: {
        scrollable: {
            type: Boolean,
            default: false,
        },

        hoverable: {
            type: Boolean,
            default: false,
        },
    },
};
</script>
