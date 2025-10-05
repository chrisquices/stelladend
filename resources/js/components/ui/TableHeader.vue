<template>
    <th
        class="text-text-muted/75 whitespace-nowrap cursor-pointer hover:text-text relative text-left px-4 !font-bold uppercase text-xs tracking-widest"
        :class="[
            // align === 'right' ? 'text-right' : align === 'center' ? 'text-center' : 'text-left',
            // isSticky ? 'sticky left-0 z-10 bg-card hover:bg-background after:content-[\'\'] after:absolute after:top-0 after:right-0 after:w-[1px] after:h-full after:bg-border !opacity-100' : '',
            // isOptions ? 'w-1 hover:text-text font-normal sticky left-0 right-0 z-50 bg-card after:content-[\'\'] after:absolute after:top-0 after:right-0 after:w-[1px] after:h-full after:bg-border !opacity-100 !p-1 hover:!bg-card' : '',
            // isSelected ? '!text-primary bg-hover' : '',
        ]"
    >
        <slot></slot>
    </th>
</template>

<script>
export default {
    name: 'TableHeader',

    props: {
        isSticky: {
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
            switch (this.tableSize) {
                case 'compact':
                    return 'h-10'
                case 'comfortable':
                    return 'h-14'
                default:
                    return 'h-12'
            }
        },
    },
}
</script>
