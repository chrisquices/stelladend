<template>
    <div class="relative w-full">
        <select
            ref="select"
            v-bind="$attrs"
            class="rounded px-3 pr-9 py-1 focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none disabled:opacity-50 bg-input text-text w-full appearance-none"
            :class="[
            sizeClasses[size],
            colorClasses[color],
        ]"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <slot />
        </select>

        <!-- Arrow Icon -->
        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
            <i class="lni lni-chevron-down"></i>
        </div>
    </div>

</template>

<script>
export default {
    name: 'Select',

    props: {
        modelValue: [String, Number],

        size: {
            type: String,
            default: 'normal',
            validator: (value) => {
                return ['small', 'normal', 'large'].includes(value);
            }
        },

        color: {
            type: String,
            default: 'input',
            validator: value =>
                ['input', 'inputForeground'].includes(value)
        }
    },

    computed: {
        sizeClasses() {
            return {
                small: 'text-sm h-8 px-2.5 py-1.5',
                normal: 'text-sm h-10 px-3 py-2',
                large: 'text-base h-10 px-3.5 py-3'
            };
        },

        colorClasses() {
            return {
                input: 'bg-input',
                inputForeground: 'bg-input-foreground'
            };
        }
    },

    methods: {
        focus() {
            this.$refs.select && this.$refs.select.focus();
        },

        click() {
            this.$refs.select && this.$refs.select.click();
        }
    }
};
</script>

<style scoped>
select {
}
</style>
