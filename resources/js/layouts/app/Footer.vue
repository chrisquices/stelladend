<template>
    <footer class="py-8 mb-24 text-center text-sm text-text-muted">
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <Link :href="route('about-us')" class="transition hover:text-white">About</Link>
                <Link :href="route('terms-and-conditions')" class="transition hover:text-white">Terms & Conditions</Link>
                <Link :href="route('privacy-policy')" class="transition hover:text-white">Privacy Policy</Link>
                <Link :href="route('contact-us')" class="transition hover:text-white">Contact Us</Link>
            </div>
            <p class="text-xs">&copy; 2025 Stelladend. All Rights Reserved.</p>
        </div>
    </footer>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    name: 'Footer',

    components: {
        Button,
        Link,
    },

    methods: {
    },
};
</script>
