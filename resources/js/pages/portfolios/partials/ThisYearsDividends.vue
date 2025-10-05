    <template>
    <div class="text-text-inverted">
        <v-chart v-if="!isEmpty" class="h-full w-full" :option="chartOption" autoresize />
        <div v-if="isEmpty" class="h-full flex flex-col items-center justify-center space-y-4">
            <p class="flex items-center justify-center text-sm text-text-muted text-center">
                Once dividend payments are calculated, they will appear here.
            </p>
        </div>
    </div>
</template>

<script>
import Card from '@/components/ui/Card.vue';
import { hslToHsla, useCssVar } from '@/utils/colorHelper';

export default {
    components: {
        Card,
    },

    props: {
        portfolio: Object,

        chartData: {
            type: Array,
            required: true,
        },

        hideYAxisLabels: {
            type: Boolean,
            default: false,
        },

        rotateXAxisLabels: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        const tooltipBackgroundColor = useCssVar('--tooltip-background') || 'rgba(15,23,42,0.95)';
        const textColor = useCssVar('--text') || '#fff';
        const textMutedColor = useCssVar('--text-muted') || '#fff';
        const accentColor = useCssVar('--accent') || 'rgba(2, 132, 197, 1)'; // fallback sky-600

        return {
            months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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

        isEmpty() {
            return this.chartData.every(
                (month) => month.dividendsReceived.length === 0 && month.futureDividends.length === 0 && month.estimatedDividends.length === 0,
            );
        },

        received() {
            return this.chartData.map((m) => m.dividendsReceived.reduce((s, r) => s + parseFloat(r.total || 0), 0));
        },

        future() {
            return this.chartData.map((m) => m.futureDividends.reduce((s, r) => s + parseFloat(r.total || 0), 0));
        },

        estimated() {
            return this.chartData.map((m) => m.estimatedDividends.reduce((s, r) => s + parseFloat(r.total || 0), 0));
        },

        chartOption() {
            return {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' },
                    backgroundColor: this.tooltipBackgroundColor,
                    borderRadius: 8,
                    borderWidth: 0,
                    confine: true,
                    textStyle: { color: this.textColor, fontSize: 12 },
                    formatter: (params) => {
                        const dataIndex = params[0].dataIndex;
                        const monthData = this.chartData[dataIndex];

                        const renderSection = (title, dataArray) => {
                            if (!dataArray.length) return '';

                            return dataArray
                                .map((record) => {
                                    const label = title === 'Received' ? 'Received' : 'Total Expected';

                                    const icon =
                                        title === 'Future'
                                            ? '<i class="lni lni-check-circle-2 !text-restorative !text-base"></i>'
                                            : title === 'Received'
                                                ? '<i class="lni lni-clock-eight !text-text-muted !text-base"></i>'
                                                : '<i class="lni lni-question-mark-circle !text-text-muted !text-base"></i>';

                                    return `
                                      <div class="w-64 text-sm space-y-2">
                                        <div class="flex justify-between">
                                          <span class="text-text font-semibold flex items-center gap-1">
                                            ${record.ticker} ${icon}
                                          </span>
                                          <span class="font-mono tracking-widest font-medium text-text-muted">${record.dateFormatted ?? ''}</span>
                                        </div>
                                        <div class="flex justify-between">
                                          <span class="text-text font-mono tracking-wide"><span class="text-text-muted">${record.shares}</span> x ${record.dividendAmountFormatted}</span>
                                          <span class="font-mono tracking-wide">${record.totalFormatted}</span>
                                        </div>
                                        ${record.secondaryCurrency ? `<div class="flex justify-between"><span></span><span class="text-text-muted font-mono tracking-wide">${record.secondaryCurrency.totalFormatted}</span></div>` : ''}
                                      </div>
                                    `;
                                })
                                .join('');
                        };

                        return `
              <div class="text-sm p-4 space-y-8">
                <div class="font-semibold text-base mb-4 text-center">${monthData.month}</div>
                ${renderSection('Received', monthData.dividendsReceived)}
                ${renderSection('Future', monthData.futureDividends)}
                ${renderSection('Estimated', monthData.estimatedDividends)}
              </div>
            `;
                    },
                },
                grid: {
                    top: 17,
                    left: this.hideYAxisLabels ? -24 : 7,
                    right: this.hideYAxisLabels ? 2 : 0,
                    bottom: 0,
                    containLabel: true,
                },
                xAxis: {
                    type: 'category',
                    data: this.months,
                    axisLine: { lineStyle: { color: this.textMutedColor } },
                    axisLabel: {
                        color: this.textMutedColor,
                        interval: 0,
                        ...(this.rotateXAxisLabels || this.isMobile ? { rotate: 35 } : {})
                    },
                },
                yAxis: this.hideYAxisLabels
                    ? { show: false }
                    : {
                          type: 'value',
                          min: 0,
                          splitNumber: 4,
                          axisLabel: {
                              color: this.textMutedColor,
                              formatter: (value) => `$${value.toFixed(0)}`,
                          },
                          splitLine: { show: false },
                      },
                series: [
                    {
                        name: 'Received',
                        type: 'bar',
                        stack: 'dividends',
                        barWidth: '50%',
                        itemStyle: { color: hslToHsla(this.accentColor, 1) },
                        label: {
                            show: true,
                            position: 'top',
                            formatter: ({ value }) => (value ? `$${value.toFixed(0)}` : ''),
                            fontSize: 14,
                            color: this.textColor,
                        },
                        data: this.received,
                    },
                    {
                        name: 'Future',
                        type: 'bar',
                        stack: 'dividends',
                        itemStyle: { color: hslToHsla(this.accentColor, 0.5) },
                        label: {
                            show: true,
                            position: 'top',
                            formatter: ({ value }) => (value ? `$${value.toFixed(0)}` : ''),
                            fontSize: 14,
                            color: this.textColor,
                            opacity: 0.5,
                        },
                        data: this.future,
                    },
                    {
                        name: 'Estimated',
                        type: 'bar',
                        stack: 'dividends',
                        itemStyle: { color: hslToHsla(this.accentColor, 0.25) },
                        label: {
                            show: true,
                            position: 'top',
                            formatter: ({ value }) => (value ? `$${value.toFixed(0)}` : ''),
                            fontSize: 14,
                            color: this.textColor,
                            opacity: 0.25,
                        },
                        data: this.estimated,
                    },
                ],
            };
        },
    },
};
</script>
