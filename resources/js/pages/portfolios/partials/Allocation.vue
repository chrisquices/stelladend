<template>
    <div class="text-text-inverted h-44">
        <v-chart v-if="chartData.length" :option="chartOption" autoresize class="h-full w-full" />
        <div v-if="!chartData.length" class="h-full flex flex-col items-center justify-center space-y-4">
            <p class="flex items-center justify-center text-sm text-text-muted">
                Add holdings to view asset allocation across your portfolio.
            </p>
        </div>
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import { hslToHsla, useCssVar } from '@/utils/colorHelper';
import 'echarts/lib/chart/pie';

export default {
    components: {
        Card,
    },

    props: {
        chartData: {
            type: Array,
            required: true,
        },
    },

    data() {
        return {
            backgroundColor: useCssVar('--tooltip-background') || '#0f172a',
            textColor: useCssVar('--text') || '#cbd5e1',
        };
    },

    computed: {
        chartOption() {
            const processedData = this.chartData.map((item) => ({
                name: item.ticker,
                value: item.currentMarketValue,
                itemStyle: {
                    color: hslToHsla(item.color || 'hsl(0, 0%, 80%)', 1),
                },
            }));

            return {
                backgroundColor: 'transparent',
                tooltip: {
                    trigger: 'item',
                    backgroundColor: this.backgroundColor,
                    borderWidth: 0,
                    borderRadius: 8,
                    confine: true,
                    textStyle: {
                        color: this.textColor,
                        fontSize: 12,
                    },
                    formatter: (params) => {
                        const holding = this.chartData[params.dataIndex];

                        return `
              <div class="text-sm p-4 space-y-2 w-64">
                <div class="font-semibold text-base mb-2">${holding.ticker}</div>
                <div class="flex justify-between">
                  <span>Shares Owned</span>
                  <span class="font-mono">${holding.currentSharesOwnedFormatted}</span>
                </div>
                <div class="flex justify-between">
                  <span>Market Value</span>
                  <span class="font-mono">${holding.currentMarketValueFormatted}</span>
                </div>
              </div>
            `;
                    },
                },
                legend: {
                    show: false,
                    orient: 'vertical',
                    left: 'left',
                    top: 'center',
                    textStyle: {
                        color: this.textColor,
                    },
                    formatter: (name) => {
                        const holding = this.chartData.find((h) => h.ticker === name);
                        return `${name} (${holding.percentageOfPortfolioFormatted})`;
                    },
                },
                series: [
                    {
                        name: 'Allocation',
                        type: 'pie',
                        radius: ['30%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center',
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '14',
                                fontWeight: 'bold',
                            },
                        },
                        labelLine: {
                            show: false,
                        },
                        data: processedData,
                    },
                ],
            };
        },
    },
};
</script>
