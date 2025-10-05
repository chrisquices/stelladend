<template>
    <span class="flex items-center">
        <i v-if="direction === 'desc'" class="lni lni-chevron-up !font-bold"></i>
        <i v-if="direction === 'asc'" class="lni lni-chevron-down !font-bold"></i>
    </span>
</template>

<script>
export default {
    name: 'SortIcon',

    props: {
        direction: {
            type: String,
            required: true,
            validator: (value) => ['asc', 'desc'].includes(value),

        },
    },
};
</script>
