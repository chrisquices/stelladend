<template>
    <div class="relative inline-block xl:w-auto" ref="dropdownRef">
        <div @click="toggleDropdown" ref="triggerRef" class="cursor-pointer">
            <slot name="trigger"></slot>
        </div>

        <transition
            enter-active-class="animate__animated animate__fadeIn ultra-fast"
            leave-active-class="animate__animated animate__fadeOut ultra-fast"
        >
            <div
                v-if="isOpen"
                class="absolute mt-2 rounded-lg shadow-lg !z-[120] text-sm"
                tabindex="-1"
                :class="{
                  'right-0': direction === 'left',
                  'left-0': direction === 'right',
                }"
            >
                <Card
                    class="!p-0 !bg-card border border-border"
                    tabindex="-1"
                    :style="{
                        width: width,
                        minWidth: triggerWidth + 'px',
                        maxWidth: width !== 'auto' ? width : triggerWidth + 'px',
                    }"
                    @click="handleOptionClick"
                >
                    <slot name="content"></slot>
                </Card>
            </div>
        </transition>
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';

export default {
    name: "Dropdown",
    components: { Card },

    props: {
        direction: {
            type: String,
            default: "right",
        },
        closeTriggerOnClick: {
            type: Boolean,
            default: true,
        },
        closeContentOnClick: {
            type: Boolean,
            default: true,
        },
        width: {
            type: String,
            default: "auto",
        },
    },

    data() {
        return {
            isOpen: false,
            triggerWidth: 0,
        };
    },

    methods: {
        toggleDropdown() {
            if (this.isOpen && this.closeTriggerOnClick) {
                this.closeDropdown();
            } else {
                this.isOpen = true;
                this.updateTriggerWidth();
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        handleClickOutside(event) {
            const path = event.composedPath?.() || [];
            if (!path.includes(this.$refs.dropdownRef)) {
                this.closeDropdown();
            }
        },

        handleOptionClick() {
            if (this.closeContentOnClick) {
                this.closeDropdown();
            }
        },

        updateTriggerWidth() {
            if (this.$refs.triggerRef) {
                this.triggerWidth = this.$refs.triggerRef.offsetWidth;
            }
        },
    },

    mounted() {
        this.updateTriggerWidth();
        window.addEventListener("resize", this.updateTriggerWidth);
        document.addEventListener("mousedown", this.handleClickOutside);
    },

    beforeUnmount() {
        window.removeEventListener("resize", this.updateTriggerWidth);
        document.removeEventListener("mousedown", this.handleClickOutside);
    },

    updated() {
        this.updateTriggerWidth();
    },
};
</script>

<style scoped>
.ultra-fast {
    --animate-duration: 0.2s;
}
</style>
