<template>
    <td
        class="whitespace-nowrap text-text px-4 first:rounded-l-4xl last:rounded-r-4xl"
        :class="[
            sizeClass,
        ]"
    >
        <slot></slot>
    </td>
</template>

<script>
export default {
    name: 'TableCell',

    props: {
        size: {
            type: String,
            default: null,
        },
        isStickyLeft: {
            type: Boolean,
            default: false,
        },
        isStickyRight: {
            type: Boolean,
            default: false,
        },
        isActions: {
            type: Boolean,
            default: false,
        },
    },

    inject: {
        tableSize: {
            from: 'tableSize',
            default: 'normal',
        },
    },

    computed: {
        sizeClass() {
            const resolvedSize = this.size || this.tableSize
            switch (resolvedSize) {
                case 'normal':
                    return 'py-2 px-4'
                case 'compact':
                    return 'py-1.5'
                case 'comfortable':
                    return 'py-4 px-4'
                default:
                    return 'py-2 px-4'
            }
        },
    },
}
</script>
