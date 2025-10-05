<template>
    <tr :class="rowSizeClass" v-bind="$attrs">
        <slot></slot>
    </tr>
</template>

<script>
export default {
    name: "TableHeadRow",

    props: {
        size: {
            type: String,
            default: "normal",
        },
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
