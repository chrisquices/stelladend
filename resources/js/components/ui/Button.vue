<template>
    <button
        ref="button"
        class="!justify-center inline-flex items-center gap-2 whitespace-nowrap rounded font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 cursor-pointer tracking-wide"
        :class="[
            $attrs.class,
            colorClasses[color],
            textColorClasses[color],
            sizeClasses[size],
            hoverClasses[onhover],
        ]"
        type="button"
        v-bind="$attrs"
    >
        <slot/>
    </button>
</template>

<script>
import Select from '@/components/ui/Select.vue';

export default {
    name: 'Button',
    components: { Select },

    props: {
        size: {
            type: String,
            default: 'normal',
            validator: value => ['small', 'normal', 'large'].includes(value),
        },

        color: {
            type: String,
            default: 'primary',
            validator: value =>
                ['primary', 'primaryForeground', 'secondary', 'accent', 'transparent', 'destructive', 'input', 'inputForeground', 'premium'].includes(value),
        },

        onhover: {
            type: String,
            default: null,
            validator: value =>
                ['primary', 'primaryForeground', 'secondary', 'accent', 'transparent', 'destructive', 'input', 'inputForeground', 'premium'].includes(value),
        },
    },

    computed: {
        sizeClasses() {
            return {
                small: 'text-sm h-8 px-2.5 py-1.5',
                normal: 'text-sm h-10 px-4 py-2',
                large: 'text-base h-12 px-3.5 py-3',
            };
        },

        colorClasses() {
            return {
                primary: 'bg-primary hover:bg-primary/80',
                primaryForeground: 'bg-primary-foreground',
                secondary: 'bg-secondary',
                accent: 'bg-accent',
                transparent: 'bg-transparent',
                destructive: 'bg-destructive',
                input: 'bg-input',
                inputForeground: 'bg-input-foreground',
                premium: 'bg-yellow-500',
            };
        },

        textColorClasses() {
            return {
                primary: 'text-text-inverted',
                primaryForeground: 'text-text-inverted',
                secondary: 'text-text',
                accent: 'text-text-inverted',
                transparent: 'text-text-inverted',
                destructive: 'text-text-inverted',
                input: 'text-text',
                inputForeground: 'text-text',
                premium: 'text-slate-900',
            };
        },

        hoverClasses() {
            return {
                primary: 'hover:bg-primary',
                primaryForeground: 'hover:bg-primary-foreground text-text-inverted',
                secondary: 'hover:bg-primary',
                accent: 'hover:bg-accent',
                transparent: 'hover:bg-transparent',
                destructive: 'hover:bg-destructive hover:!text-text-inverted',
                input: 'hover:bg-input',
                inputForeground: 'hover:!bg-input-foreground',
                premium: 'bg-yellow-500',
            };
        },
    },
};
</script>
