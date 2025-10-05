<template>
    <div class="flex flex-col space-y-4 sm:space-y-4">
        <div class="flex w-full flex-col items-start gap-6 text-text-inverted lg:flex-row sm:gap-8">
            <Card v-if="false" class="grid w-full flex-shrink-0 !bg-card-foreground xl:block lg:w-88 lg:grid-cols-1">
                <!-- Change -->
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm tracking-wide text-text-muted">
                        Change <span class="uppercase">{{ selectedRange }}</span>
                    </p>
                    <div class="flex flex-col justify-between text-sm">
                        <div v-if="performance.metrics.changeInPercentage">
                            <p class="font-mono font-medium tracking-widest"
                               :class="{
                                '!text-restorative': performance.metrics.changeInPercentage > 0,
                                '!text-destructive': performance.metrics.changeInPercentage < 0,
                            }"
                            >
                                <span v-if="performance.metrics.changeInPercentage > 0">+{{ performance.metrics.changeInPercentageFormatted }}</span>
                                <span v-if="performance.metrics.changeInPercentage < 0">{{ performance.metrics.changeInPercentageFormatted }}</span>
                            </p>
                        </div>
                        <div v-if="!performance.metrics.changeInPercentage" class="text-text-muted">-</div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-4">
                        <!-- Starting Value -->
                        <div class="flex flex-col justify-between text-sm">
                            <div>
                                <p class="font-mono font-medium tracking-widest">
                                    {{ performance.metrics.startingValueFormatted }}
                                </p>
                                <p v-if="performance.metrics.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                    {{ performance.metrics.secondaryCurrency.startingValueFormatted }}
                                </p>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div>
                            <i
                                class="lni lni-arrow-right !font-bold"
                                :class="{
                                '!text-restorative': performance.metrics.changeInDollar > 0,
                                '!text-destructive': performance.metrics.changeInDollar < 0,
                            }"
                            ></i>
                        </div>

                        <!-- Ending Value -->
                        <div class="flex flex-col justify-between text-sm">
                            <div v-if="performance.metrics.endingValue">
                                <p class="text-right font-mono font-medium tracking-widest">
                                    {{ performance.metrics.endingValueFormatted }}
                                </p>
                                <p v-if="performance.metrics.secondaryCurrency" class="text-right font-mono font-medium tracking-widest text-text-muted">
                                    {{ performance.metrics.secondaryCurrency.endingValueFormatted }}
                                </p>
                            </div>
                            <div v-if="!performance.metrics.endingValue" class="text-right text-text-muted">-</div>
                        </div>
                    </div>

                    <div v-if="performance.metrics.changeInDollar" class="text-sm">
                        <p
                            v-if="performance.metrics.changeInDollar"
                            class="text-right font-mono font-medium tracking-widest text-text-muted"
                            :class="{
                                '!text-restorative': performance.metrics.changeInPercentage > 0,
                                '!text-destructive': performance.metrics.changeInPercentage < 0,
                            }"
                        >
                            <span v-if="performance.metrics.changeInPercentage > 0">+{{ performance.metrics.changeInDollarFormatted }}</span>
                            <span v-if="performance.metrics.changeInPercentage < 0">{{ performance.metrics.changeInDollarFormatted }}</span>
                        </p>
                        <p v-if="performance.metrics.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted text-right">
                            {{ performance.metrics.secondaryCurrency.changeInDollarFormatted }}
                        </p>
                    </div>
                </div>

                <!-- Best Day -->
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm tracking-wide text-text-muted">Best Day</p>
                    <div class="flex flex-col justify-between text-sm">
                        <div v-if="performance.metrics.bestDay">
                            <p class="text-right font-mono font-medium tracking-widest">
                                {{ performance.metrics.bestDay }}
                            </p>
                            <p
                                v-if="performance.metrics.bestDayGainLossInPercentage"
                                class="text-right font-mono font-medium tracking-widest text-text-muted"
                                :class="{
                                    '!text-restorative': performance.metrics.bestDayGainLossInPercentage > 0,
                                    '!text-destructive': performance.metrics.bestDayGainLossInPercentage < 0,
                                }"
                            >
                                <span v-if="performance.metrics.bestDayGainLossInPercentage > 0">+{{ performance.metrics.bestDayGainLossInPercentageFormatted }}</span>
                                <span v-if="performance.metrics.bestDayGainLossInPercentage < 0">{{ performance.metrics.bestDayGainLossInPercentageFormatted }}</span>
                            </p>
                        </div>
                        <div v-if="!performance.metrics.bestDay" class="text-right text-text-muted">-</div>
                    </div>
                </div>

                <!-- Worst Day -->
                <div class="flex items-start justify-between gap-4">
                    <p class="text-sm tracking-wide text-text-muted">Worst Day</p>
                    <div class="flex flex-col justify-between text-sm">
                        <div v-if="performance.metrics.worstDay">
                            <p class="text-right font-mono font-medium tracking-widest">
                                {{ performance.metrics.worstDay }}
                            </p>
                            <p
                                v-if="performance.metrics.worstDayGainLossInPercentage"
                                class="text-right font-mono font-medium tracking-widest text-text-muted"
                                :class="{
                                    '!text-restorative': performance.metrics.worstDayGainLossInPercentage > 0,
                                    '!text-destructive': performance.metrics.worstDayGainLossInPercentage < 0,
                                }"
                            >
                                <span v-if="performance.metrics.worstDayGainLossInPercentage > 0">+{{ performance.metrics.worstDayGainLossInPercentageFormatted }}</span>
                                <span v-if="performance.metrics.worstDayGainLossInPercentage < 0">{{ performance.metrics.worstDayGainLossInPercentageFormatted }}</span>
                            </p>
                        </div>
                        <div v-if="!performance.metrics.worstDay" class="text-right text-text-muted">-</div>
                    </div>
                </div>
            </Card>

            <!-- Chart -->
            <div
                class="flex h-76 w-full flex-grow flex-col justify-between gap-8"
                :class="{
                    'h-84': performance.metrics.secondaryCurrency,
                }"
            >
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:justify-end">
                    <Button
                        v-for="range in ['1m', '3m', '6m', '1y', '2y', '5y', 'ytd', 'max']"
                        :key="range"
                        size="small"
                        :color="selectedRange === range ? 'primary' : 'transparent'"
                        @click="setRange(range)"
                    >
                        {{ range.toUpperCase() }}
                    </Button>
                </div>

                <v-chart v-if="performance.chartData.length" class="h-full w-full" :option="chartOption" autoresize />
                <div v-else class="flex h-full flex-col items-center justify-center space-y-4">
                    <p class="flex items-center justify-center text-center text-sm text-text-muted">No performance data available yet.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import { useCssVar } from '@/utils/colorHelper';
import { formatNumberToDollar } from '@/utils/numberHelper.js';
import { Link, router } from '@inertiajs/vue3';

export default {
    components: { Card, Link, Button },

    props: {
        performance: {
            type: Object,
            required: true,
        },
    },

    data() {
        const defaultRange = new URLSearchParams(window.location.search).get('ppr') || 'ytd';
        const tooltipBackgroundColor = useCssVar('--tooltip-background') || 'rgba(15,23,42,0.95)';
        const splitLineBackgroundColor = useCssVar('--background') || 'rgba(15,23,42,0.5)';
        const textColor = useCssVar('--text') || '#fff';
        const textMutedColor = useCssVar('--text-muted') || '#94a3b8';
        const accentColor = useCssVar('--accent') || 'rgba(2, 132, 197, 1)';

        return {
            selectedRange: defaultRange,
            splitLineBackgroundColor,
            tooltipBackgroundColor,
            textColor,
            textMutedColor,
            accentColor,
        };
    },

    computed: {
        labels() {
            return this.performance.chartData.map((entry) => entry.dateFormatted);
        },

        totalPortfolio() {
            return this.performance.chartData.map((entry) => ({
                value: [entry.date, Number(entry.totalPortfolioValue)],
            }));
        },

        marketValue() {
            return this.performance.chartData.map((entry) => ({
                value: [entry.date, Number(entry.marketValue)],
            }));
        },

        cashAvailable() {
            return this.performance.chartData.map((entry) => ({
                value: [entry.date, Number(entry.cashAvailable)],
            }));
        },

        chartOption() {
            const values = this.totalPortfolio.map(point => point.value[1]);
            const min = Math.min(...values);
            const max = Math.max(...values);

            const padding = (max - min) * 0.05;
            const calculatedMin = Math.floor((min - padding) / 10) * 10;
            const calculatedMax = Math.ceil((max + padding) / 10) * 10;

            return {
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: this.tooltipBackgroundColor,
                    borderRadius: 8,
                    borderWidth: 0,
                    confine: true,
                    textStyle: {
                        color: this.textColor,
                        fontSize: 12,
                    },
                    formatter: (params) => {
                        const i = params[0].dataIndex;
                        const item = this.performance.chartData[i];
                        let html = `<div class="text-sm space-y-4 p-2">`;
                        html += `<div class="font-semibold">${item.dateFormatted}</div>`;

                        const row = (label, value, secondary, color) => `
                  <div class="flex items-start justify-between space-x-12">
                     <div class="text-right">
                      <div class="font-medium flex items-center">
                          <span style="color: ${color}">${label}</span>
                      </div>
                    </div>

                    <div class="text-right">
                      <div class="font-medium font-mono tracking-widest">${value}</div>
                      ${secondary ? `<div class="text-xs text-text-muted font-mono tracking-widest">${secondary}</div>` : ''}
                    </div>
                  </div>`;

                        html += row(
                            'Total Portfolio',
                            item.totalPortfolioValueFormatted,
                            item.secondaryCurrency?.totalPortfolioValueFormatted,
                            '#0ea5e9',
                        );
                        html += row('Market Value', item.marketValueFormatted, item.secondaryCurrency?.marketValueFormatted, '#10b981');
                        html += row('Cash Available', item.cashAvailableFormatted, item.secondaryCurrency?.cashAvailableFormatted, '#facc15');
                        html += `</div>`;
                        return html;
                    },
                },
                grid: {
                    top: 24,
                    left: 7,
                    right: 7,
                    bottom: 20,
                    containLabel: true,
                },
                xAxis: {
                    type: 'time',
                    axisLine: { lineStyle: { color: this.textMutedColor } },
                    axisLabel: {
                        color: this.textMutedColor,
                    },
                    axisPointer: {
                        lineStyle: {
                            color: this.textMutedColor,
                            width: 1,
                        },
                    },
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        color: this.textMutedColor,
                        formatter: (value) => formatNumberToDollar(value, 0),
                    },
                    splitLine: {
                        show: true,
                        lineStyle: {
                            color: this.splitLineBackgroundColor,
                        },
                    },
                    min: calculatedMin,
                    max: calculatedMax,
                    interval: (calculatedMax - calculatedMin) / 5,
                },
                series: [
                    {
                        name: 'Total Portfolio Value',
                        type: 'line',
                        symbol: 'none',
                        lineStyle: { width: 2, color: '#0ea5e9' },
                        itemStyle: { color: '#0ea5e9' },
                        data: this.totalPortfolio,
                    },
                    {
                        name: 'Market Value',
                        type: 'line',
                        symbol: 'none',
                        lineStyle: { width: 2, color: '#10b981' },
                        itemStyle: { color: '#10b981' },
                        data: this.marketValue,
                    },
                    {
                        name: 'Cash Available',
                        type: 'line',
                        symbol: 'none',
                        lineStyle: { width: 2, color: '#facc15' },
                        itemStyle: { color: '#facc15' },
                        data: this.cashAvailable,
                    },
                ],
            };
        },
    },

    methods: {
        setRange(range) {
            this.selectedRange = range;

            router.reload({
                preserveState: true,
                preserveScroll: true,
                data: { ppr: range },
            });
        },
    },
};
</script>
