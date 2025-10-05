<template>
    <Modal v-if="localShowTutorial" v-model:isOpen="localShowTutorial" size="2extra-large">
        <form @submit.prevent="updateAccount">
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <p class="truncate font-medium whitespace-nowrap text-text">Welcome to Stelladend</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleManageAccountModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Cancel | Save Account -->
                <div class="flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleManageAccountModal" tabindex="-1"
                        >Cancel
                    </Button>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import { Line } from 'vue-chartjs';
import Button from '@/components/ui/Button.vue';
import Modal from '@/components/ui/Modal.vue';

export default {
    components: {
        Modal,
        Button,
        Card,
        Line,
    },

    props: {
        showTutorial: Boolean
    },

    computed: {
    },

    data() {
        return {
            localShowTutorial: Boolean,
        };
    },
};
</script>
