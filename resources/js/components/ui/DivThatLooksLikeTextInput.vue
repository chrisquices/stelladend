<template>
    <div
        ref="input"
        class="rounded px-3 py-1 focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none disabled:opacity-50 text-text flex items-center"
        :class="[
            sizeClasses[size],
            colorClasses[color],
        ]"
        v-bind="$attrs"
    >
        <slot />
    </div>
</template>

<script>
export default {
    name: 'DivThatLooksLikeTextInput',

    props: {
        size: {
            type: String,
            default: 'normal',
            validator: (value) => {
                return ['small', 'normal', 'large'].includes(value);
            },
        },

        color: {
            type: String,
            default: 'input',
            validator: value =>
                ['input', 'inputForeground'].includes(value),
        },
    },

    computed: {
        sizeClasses() {
            return {
                small: 'text-sm h-8 px-2.5 py-1.5',
                normal: 'text-sm h-10 px-3 py-2',
                large: 'text-base h-10 px-3.5 py-3',
            };
        },

        colorClasses() {
            return {
                input: 'bg-input',
                inputForeground: 'bg-input-foreground',
            };
        },
    },
};
</script>
