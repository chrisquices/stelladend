<template>
    <input
        ref="input"
        :type="$attrs.type || 'text'"
        class="rounded px-3 py-1 focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none disabled:opacity-50 text-text"
        :class="[
      sizeClasses[size],
      colorClasses[color],
      isCheckbox ? checkboxColorClasses[color] : ''
    ]"
        :value="displayValue"
        @input="handleInput"
        v-bind="$attrs"
    >
</template>

<script>
export default {
    name: 'TextInput',

    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },

        size: {
            type: String,
            default: 'normal',
            validator: value => ['small', 'normal', 'large'].includes(value)
        },

        color: {
            type: String,
            default: 'input',
            validator: value => ['input', 'inputForeground'].includes(value)
        },

        isMoney: {
            type: Boolean,
            default: false
        },

        currency: {
            type: Object,
            default: () => ({
                id: 1,
                code: 'USD',
                symbol: '$',
                thousands_separator: ',',
                decimal_separator: '.',
                decimal_points: 2
            })
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
        },

        isCheckbox() {
            return this.$attrs.type === 'checkbox';
        },

        checkboxColorClasses() {
            return {
                input: 'focus:!ring-0 accent-primary',
                inputForeground: 'focus:!ring-0 accent-primary'
            };
        },

        displayValue() {
            return this.modelValue?.toString() ?? '';
        }
    },

    methods: {
        handleInput(event) {
            let raw = event.target.value;

            if (!this.isMoney || !this.currency) {
                this.$emit('update:modelValue', raw);
                return;
            }

            const dec = this.currency.decimal_separator;
            const points = this.currency.decimal_points;

            const escapedDec = dec.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');

            const allowedCharsRegex = new RegExp(`[^\\d${escapedDec}-]`, 'g');
            raw = raw.replace(allowedCharsRegex, '');

            const hasNegative = raw.startsWith('-');
            raw = raw.replace(/-/g, '');
            if (hasNegative) {
                raw = '-' + raw;
            }

            // Handle decimals
            const parts = raw.split(dec);
            let whole = parts[0];
            let decimal = parts[1] ?? '';

            if (points === 0) {
                raw = whole;
            } else {
                decimal = decimal.slice(0, points);
                raw = whole + (parts.length > 1 ? dec + decimal : '');
            }

            // Update visible input value
            if (event.target.value !== raw) {
                event.target.value = raw;
            }

            this.$emit('update:modelValue', raw);
        },

        focus() {
            this.$refs.input && this.$refs.input.focus();
        },

        click() {
            this.$refs.input && this.$refs.input.click();
        }
    }
};
</script>
