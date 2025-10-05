<template>
    <div class="w-full h-10">
        <Line :data="sparklineData" :options="sparklineOptions" />
    </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
} from 'chart.js';

ChartJS.register(LineElement, PointElement, LinearScale, CategoryScale, Tooltip);

export default {
    components: {
        Line,
    },

    data() {
        return {
            sparklineData: {
                labels: Array.from({ length: 30 }, (_, i) => i + 1),
                datasets: [
                    {
                        data: [
                            100, 90, 95, 85, 110, 130, 125, 140, 120, 100,
                            105, 115, 100, 98, 140, 150, 155, 120, 130, 110,
                            95, 125, 140, 135, 100, 90, 105, 108, 115, 98,
                            130, 150, 145, 160, 140, 138, 125, 120, 100, 105
                        ],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        borderWidth: 2,
                    },
                ],
            },

            sparklineOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                },
                scales: {
                    x: { display: false },
                    y: { display: false },
                },
                elements: {
                    line: {
                        borderCapStyle: 'round',
                    },
                },
            },
        };
    },
};
</script>
