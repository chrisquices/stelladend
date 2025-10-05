<template>
    <div class="flex items-center">
        <Link :href="route('dashboard.index')" class="transition-all duration-300 cursor-pointer rounded-full p-2">
            <img :src="logoUrl" alt="Logo" class="h-6 w-6 sm:h-8 sm:w-8" />
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import logoUrl from '@img/logo.svg'

export default {
    name: 'NavLogo',

    components: {
        Link,
    },

    data() {
        return {
            logoUrl,
        };
    },
};
</script>
