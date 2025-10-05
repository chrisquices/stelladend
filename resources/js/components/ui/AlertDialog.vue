<template>
    <transition name="fade">
        <teleport to="body">
            <div
                v-if="show"
                class="fixed inset-0 z-70 flex flex-col items-center justify-center space-y-12 p-4"
            >
                <!-- Background Layer -->
                <div class="absolute inset-0 h-screen pointer-events-none z-0 bg-gradient-to-br from-background-corner via-background/50 to-background-corner">

                    <!-- Stars -->
                    <div
                        v-for="(star, index) in stars"
                        :key="index"
                        class="absolute pointer-events-none"
                        :class="star.class"
                        :style="{
                            top: star.top,
                            left: star.left,
                            width: star.size + 'px',
                            height: star.size + 'px',
                            opacity: star.opacity,
                        }"
                    >
                        <div class="absolute inset-0 w-0.5 h-full bg-gradient-to-b from-transparent via-accent to-transparent mx-auto"></div>
                        <div class="absolute inset-0 h-0.5 w-full bg-gradient-to-r from-transparent via-accent to-transparent my-auto"></div>
                    </div>
                </div>

                <div
                    role="alertdialog"
                    class="z-10 space-y-8 p-8 transform transition-all max-w-md w-full h-auto max-h-full duration-300 bg-card text-text rounded-xl shadow-lg shadow-shadow"
                >
                    <div class="text-lg font-bold text-text mb-4">
                        <slot name="title"></slot>
                    </div>
                    <p class="text-sm text-text">
                        <slot name="content"></slot>
                    </p>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-4">
                        <slot name="buttons"></slot>
                    </div>
                </div>
            </div>
        </teleport>
    </transition>
</template>

<script>
export default {
    name: "AlertDialog",

    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },

    data() {
        return {
            stars: [
                { class: 'animate-rotateStar', top: '17%', left: '82%', size: 147, opacity: 0.16 },
                { class: 'animate-rotateStar', top: '71%', left: '67%', size: 174, opacity: 0.10 },
                { class: 'animate-rotateStarTiny', top: '73%', left: '29%', size: 131, opacity: 0.19 },
                { class: 'animate-rotateStar', top: '22%', left: '88%', size: 180, opacity: 0.09 },
                { class: 'animate-rotateStarTiny', top: '36%', left: '19%', size: 91, opacity: 0.18 },
                { class: 'animate-rotateStarTiny', top: '8%', left: '33%', size: 168, opacity: 0.33 },
                { class: 'animate-rotateStarReverse', top: '14%', left: '56%', size: 185, opacity: 0.15 },
                { class: 'animate-rotateStar', top: '57%', left: '87%', size: 164, opacity: 0.07 },
                { class: 'animate-rotateStarTiny', top: '90%', left: '12%', size: 108, opacity: 0.10 },
                { class: 'animate-rotateStarTiny', top: '22%', left: '94%', size: 139, opacity: 0.16 },
            ],
        };
    },
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
.modal-pop-enter-active,
.modal-pop-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.modal-pop-enter-from,
.modal-pop-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
@keyframes rotateStar { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
@keyframes rotateStarTiny { 0% { transform: rotate(0deg); } 100% { transform: rotate(90deg); } }
@keyframes rotateStarReverse { 0% { transform: rotate(360deg); } 100% { transform: rotate(0deg); } }
@keyframes driftSlow {
    0% { transform: translate(0px, 0px); }
    50% { transform: translate(8px, -6px); }
    100% { transform: translate(0px, 0px); }
}
@keyframes driftSmall {
    0% { transform: translate(0px, 0px); }
    50% { transform: translate(12px, -8px); }
    100% { transform: translate(0px, 0px); }
}
@keyframes driftSubtle {
    0% { transform: translate(-50%, -50%) scale(1); }
    50% { transform: translate(-50%, -50%) scale(1.01); }
    100% { transform: translate(-50%, -50%) scale(1); }
}
.animate-rotateStar { animation: rotateStar 30s linear infinite; }
.animate-rotateStarTiny { animation: rotateStarTiny 75s linear infinite; }
.animate-rotateStarReverse { animation: rotateStarReverse 60s linear infinite; }
.animate-driftSlow { animation: driftSlow 60s ease-in-out infinite; }
.animate-driftSmall { animation: driftSmall 80s ease-in-out infinite; }
.animate-driftSubtle { animation: driftSubtle 90s ease-in-out infinite; }
</style>
