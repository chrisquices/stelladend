<template>
    <div style="height: 100px">
        <Line :data="processedChartData" :options="chartOptions" />
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import { CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

export default {
    components: {
        Card,
        Line,
    },

    props: {
        chartData: {
            type: Array,
            required: true,
        },
    },

    computed: {
        processedChartData() {
            const data = this.chartData.data;

            if (!Array.isArray(data) || data.length === 0) {
                return {
                    labels: [],
                    datasets: [],
                };
            }

            const labels = data.map((item) => new Date(item.t).toISOString().split('T')[0]);
            const prices = data.map((item) => item.c);

            return {
                labels,
                datasets: [
                    {
                        label: 'Price',
                        data: prices,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 2,
                    },
                ],
            };
        },
    },

    data() {
        return {
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                cutout: '30%',
            },
        };
    },

    methods: {},
};
</script>
