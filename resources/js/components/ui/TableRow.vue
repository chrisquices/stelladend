<template>
    <tr
        :class="[
        rowSizeClass,
            'hover:bg-hover odd:bg-hover',
            striped ? '' : ''
        ]"
        >
        <slot></slot>
    </tr>
</template>

<script>
export default {
    name: "TableRow",

    props: {
        size: {
            type: String,
            default: "normal",
        },

        striped: {
            type: Boolean,
            default: false,
        }
    },

    computed: {
        rowSizeClass() {
            switch (this.size) {
                case "compact":
                    return "!h-6";
                case "comfortable":
                    return "!h-14";
                default:
                    return "!h-10";
            }
        },
    },
};
</script>
