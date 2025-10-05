<template>
    <div class="w-full space-y-4">
        <div class="flex items-center justify-between">
            <div class="text-base font-medium text-text flex items-center justify-between w-full gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-7">
                        <img v-if="chartData.logoUrl" :src="chartData.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                        <div
                            v-if="!chartData.logoUrl"
                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                        >
                        </div>
                    </div>
                    <p class="w-44 truncate text-text-muted">
                        {{ chartData.ticker }}
                        <span class="ml-2 w-10 truncate whitespace-nowrap text-text-muted">{{ chartData.name }}</span>
                    </p>
                </div>

                <div class="font-mono tracking-wide flex items-center">
                    <div>
                        {{ chartData.currentPriceFormatted }}
                    </div>

                    <div class="px-2 bg-green2-500/50 rounded-lg"
                         :class="{
                            'text-restorative' : chartData.changeInDollar > 0,
                            'text-destructive' : chartData.changeInDollar < 0,
                         }"
                    >
                        {{ chartData.changeInDollarFormatted }} ({{ chartData.changeInPercentageFormatted }})
                    </div>
                </div>
            </div>
        </div>

        <div class="relative h-32">
            <Line v-if="chartData.chartData.length" :data="processedChartData" :options="chartOptions" />
            <p v-else class="flex h-full items-center justify-center text-sm text-text-muted">Not enough data available</p>
        </div>
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import { CategoryScale, Chart as ChartJS, Filler, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler);

export default {
    components: {
        Line,
        Card,
    },

    props: {
        chartData: {
            type: Object,
            required: true,
        },
    },

    computed: {
        processedChartData() {
            const prices = this.chartData.chartData.map((point) => point.currentPrice);
            const change = this.chartData.changeInDollar || 0;

            const borderColor = change > 0 ? '#16a34a' : '#dc2626'; // Tailwind text-green-600 / red-600

            return {
                labels: this.chartData.chartData.map((point) => point.dateFormatted),
                datasets: [
                    {
                        label: this.chartData.ticker,
                        data: prices,
                        borderColor,
                        backgroundColor: 'rgba(2, 132, 197, 0.20)',
                        pointRadius: 0,
                        pointHoverRadius: 0,
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
                scales: {
                    x: {
                        ticks: {
                            color: '#94a3b8',
                        },
                        title: {
                            display: false,
                        },
                    },
                    y: {
                        ticks: {
                            color: '#94a3b8',
                            callback: (value) => `$${value}`,
                            maxTicksLimit: 3, // Limit y-axis steps
                        },
                        title: {
                            display: false,
                        },
                    },
                },
                plugins: {
                    datalabels: {
                        display: false,
                    },
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        enabled: false,
                        external: function (context) {
                            let tooltipEl = document.getElementById('chartjs-tooltip');

                            if (!tooltipEl) {
                                tooltipEl = document.createElement('div');
                                tooltipEl.id = 'chartjs-tooltip';
                                tooltipEl.innerHTML = '<div></div>';
                                document.body.appendChild(tooltipEl);
                            }

                            const tooltipModel = context.tooltip;
                            if (tooltipModel.opacity === 0) {
                                tooltipEl.style.opacity = 0;
                                return;
                            }

                            const dataIndex = tooltipModel.dataPoints?.[0]?.dataIndex;
                            const dataPoint = this.chartData.chartData?.[dataIndex];

                            if (!dataPoint) return;

                            const innerHtml = `
                                <div class="bg-background text-text p-3 rounded-lg shadow-lg border w-56 space-y-2">
                                  <div class="font-semibold text-lg mb-2">${this.chartData.ticker}</div>
                                  <div class="flex items-center justify-between tracking-wide">
                                    <div class="text-sm font-medium">Price:</div>
                                    <div class="text-sm font-medium">${dataPoint.currentPriceFormatted}</div>
                                  </div>
                                  <div class="flex items-center justify-between tracking-wide">
                                    <div class="text-sm font-medium">Date:</div>
                                    <div class="text-sm font-medium">${dataPoint.dateFormatted}</div>
                                  </div>
                                </div>
                              `;

                            tooltipEl.innerHTML = innerHtml;

                            const { offsetLeft: left, offsetTop: top } = context.chart.canvas;
                            tooltipEl.style.opacity = 1;
                            tooltipEl.style.position = 'absolute';
                            tooltipEl.style.left = `${left + tooltipModel.caretX}px`;
                            tooltipEl.style.top = `${top + tooltipModel.caretY}px`;
                            tooltipEl.style.pointerEvents = 'none';
                            tooltipEl.style.zIndex = '9999';
                        }.bind(this),
                    },
                },
            },
        };
    },
};
</script>
