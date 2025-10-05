<template>
    <div class="h-full text-text-inverted">
        <v-chart :option="chartOption" autoresize class="h-full w-full" />
    </div>
</template>

<script>
import { useCssVar } from '@/utils/colorHelper';
import { formatNumberToDollar } from '@/utils/numberHelper.js';

export default {
    props: {
        chartData: {
            type: Array,
            required: true,
        },
    },

    data() {
        const tooltipBackgroundColor = useCssVar('--tooltip-background') || 'rgba(15,23,42,0.95)';
        const textColor = useCssVar('--text') || '#fff';
        const textMutedColor = useCssVar('--text-muted') || '#94a3b8';
        const accentColor = useCssVar('--accent') || 'rgba(2,132,199,1)'; // sky-600

        return {
            tooltipBackgroundColor,
            textColor,
            textMutedColor,
            accentColor,
        };
    },

    computed: {
        isMobile() {
            return window.innerWidth < 768;
        },

        normalizedChartData() {
            const reversed = [...this.chartData].reverse();

            return {
                labels: reversed.map((item) => item.name),
                data: reversed.map((item) => item.balance),
                formatted: reversed.map((item) => item.balanceFormatted),
                secondaryFormatted: reversed.map((item) => item.secondaryCurrency?.balanceFormatted || '-'),
                currencyCode: reversed.map((item) => item.secondaryCurrency?.currencyCode || ''),
            };
        },

        chartOption() {
            return {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' },
                    backgroundColor: this.tooltipBackgroundColor, // ← Using CSS var
                    borderRadius: 8,
                    borderWidth: 0,
                    confine: true,
                    textStyle: { color: this.textColor, fontSize: 12 },
                    formatter: (params) => {
                        const index = params[0].dataIndex;

                        return `
                            <div class="text-sm p-3 space-y-2 w-64">
                                <div class="font-semibold text-base mb-1 text-center">${this.normalizedChartData.labels[index]}</div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">USD</span>
                                    <span class="font-mono">${this.normalizedChartData.formatted[index]}</span>
                                </div>
                                ${ this.normalizedChartData.currencyCode[index]
                                        ? `<div class="flex justify-between">
                                                <span class="text-text-muted">${this.normalizedChartData.currencyCode[index]}</span>
                                                <span class="font-mono">${this.normalizedChartData.secondaryFormatted[index]}</span>
                                            </div>`
                                        : ''
                                }
                            </div>
                        `;
                    },
                },

                grid: {
                    left: 15,
                    right: 80,
                    top: 0,
                    bottom: 0,
                    containLabel: true,
                },

                xAxis: {
                    type: 'value',
                    axisLabel: {
                        color: this.textMutedColor,
                        ...(this.isMobile ? { rotate: 35 } : {}),
                    },
                    splitLine: {
                        lineStyle: {
                            color: 'rgba(100,116,139,0.2)',
                        },
                    },
                },

                yAxis: {
                    type: 'category',
                    data: this.normalizedChartData.labels,
                    axisLabel: {
                        color: this.textMutedColor,
                    },
                    axisLine: {
                        lineStyle: {
                            color: this.textMutedColor,
                        },
                    },
                },

                series: [
                    {
                        type: 'bar',
                        data: this.normalizedChartData.data,
                        barWidth: '50%',
                        itemStyle: {
                            color: this.accentColor,
                        },
                        label: {
                            show: true,
                            position: 'right',
                            formatter: ({ value }) => this.formatNumberToDollar(value),
                            color: this.textColor,
                            fontSize: 13,
                        },
                    },
                ],
            };
        },
    },

    methods: {
        formatNumberToDollar,
    }
};
</script>
