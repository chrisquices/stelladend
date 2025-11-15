<template>
<!--    <GuestLayout>-->
<!--        <nav class="top-0 z-50 h-16 w-full rounded-b-2xl bg-sidebar">-->
<!--            <div class="flex h-full w-full items-center justify-between px-4">-->
<!--                <div class="w-32 justify-start sm:w-48">-->
<!--                    <NavLogo />-->
<!--                </div>-->
<!--                <div class="w-auto justify-end space-x-4 sm:w-48 md:w-64">-->
<!--                    <Link :href="route('login')">-->
<!--                        <Button size="large" color="primary">Login</Button>-->
<!--                    </Link>-->
<!--                    <Link :href="route('register')">-->
<!--                        <Button size="large" color="primary">Sign Up</Button>-->
<!--                    </Link>-->
<!--                </div>-->
<!--            </div>-->
<!--        </nav>-->

<!--        <section class="py-8 lg:py-20" id="home">-->
<!--            <div class="mb-14 flex items-center justify-center gap-4 md:mb-16 lg:mb-30">-->
<!--                <img :src="logoUrl" alt="login via google" class="mr-4 h-12 md:h-18 lg:h-20 lg:w-20" />-->
<!--                <p class="text-center font-mono text-[2rem] font-semibold tracking-widest md:text-[3rem] lg:text-[4rem]">Stelladend</p>-->
<!--            </div>-->

<!--            <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">-->
<!--                <div class="max-lg:order-2">-->
<!--                    <h1 class="text-4xl font-bold tracking-tight lg:text-6xl lg:leading-none">-->
<!--                        Effortless, powerful, and built for long-term investors.-->
<!--                    </h1>-->
<!--                    <p class="mt-8 text-sm sm:text-lg">-->
<!--                        Track your portfolio, visualize dividend income, and stay on top of payouts — all from one beautiful, intuitive dashboard. No-->
<!--                        spreadsheets. Just results.-->
<!--                    </p>-->
<!--                    <div class="mt-8 inline-flex w-full gap-3 sm:mt-16 sm:w-auto">-->
<!--                        <Link :href="route('register')" class="w-full sm:w-auto">-->
<!--                            <Button size="large" color="primary" class="w-full sm:w-auto"> Sign Up</Button>-->
<!--                        </Link>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div-->
<!--                    class="h-fit rounded-2xl bg-accent/75 bg-linear-to-br p-4 max-lg:order-1"-->
<!--                    data-card3d-->
<!--                    data-card3d-perspective="1000"-->
<!--                    data-card3d-full-page-listening-->
<!--                >-->
<!--                    <img alt="hero" class="rounded-lg" :src="dashboardUrl" />-->
<!--                </div>-->
<!--            </div>-->
<!--        </section>-->

<!--        <section class="relative py-8 lg:py-20" id="features">-->
<!--            <div class="absolute start-[10%]">-->
<!--                <div-->
<!--                    class="pointer-events-none aspect-square w-60 rounded-full bg-gradient-to-r from-primary/10 to-secondary/10 blur-3xl lg:w-[600px]"-->
<!--                ></div>-->
<!--            </div>-->

<!--            <div class="flex flex-col items-center">-->
<!--                <h2 class="text-3xl font-semibold sm:text-4xl">Everything in One Place</h2>-->
<!--                <p class="mt-4 text-sm sm:text-lg">Manage, track, and optimize your income like never before.</p>-->
<!--            </div>-->

<!--            <div class="mt-8 space-y-32 lg:mt-20">-->
<!--                &lt;!&ndash; Portfolios &ndash;&gt;-->
<!--                <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">-->
<!--                    <div class="rounded-box overflow-hidden">-->
<!--                        <div class="h-fit rounded-2xl bg-linear-to-br from-accent/75 via-card to-accent/75 p-4 max-lg:order-1">-->
<!--                            <img alt="portfolios" class="rounded-lg" :src="portfoliosUrl" />-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="space-y-4 lg:mt-8">-->
<!--                        <div class="mb-6">-->
<!--                            <span class="rounded-2xl bg-accent px-3 py-2 font-bold tracking-widest text-primary">Portfolios</span>-->
<!--                        </div>-->
<!--                        <h3 class="mt-3 text-xl font-medium sm:text-2xl">Portfolios Made Simple</h3>-->
<!--                        <p class="mt-1 text-sm">-->
<!--                            Gain complete visibility and control over your investments. Our intuitive dashboard tracks every portfolio—from growth-->
<!--                            stocks to dividend ETFs—with live performance data, income projections, and visual insights.-->
<!--                        </p>-->
<!--                        <ul class="mt-4 list-inside list-disc space-y-2 text-sm">-->
<!--                            <li>Live portfolio market value and income tracking</li>-->
<!--                            <li>Monthly and annual dividend income projections</li>-->
<!--                            <li>Full visibility into upcoming dividend dates and payment history</li>-->
<!--                            <li>Clear allocation breakdown across all holdings</li>-->
<!--                            <li>Easy manual holding updates or import from your brokerage</li>-->
<!--                        </ul>-->
<!--                    </div>-->
<!--                </div>-->

<!--                &lt;!&ndash; Watchlists &ndash;&gt;-->
<!--                <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">-->
<!--                    <div class="space-y-4 lg:mt-8">-->
<!--                        <div class="mb-6">-->
<!--                            <span class="rounded-2xl bg-accent px-3 py-2 font-bold tracking-widest text-primary">Watchlists</span>-->
<!--                        </div>-->
<!--                        <h3 class="mt-3 text-xl font-medium sm:text-2xl">Effortless Dividend Tracking</h3>-->
<!--                        <p class="mt-1 text-sm">-->
<!--                            Stay on top of the stocks and ETFs you care about. Our Watchlists let you monitor yield, payouts, prices, and-->
<!--                            performance—all in one clean, responsive table.-->
<!--                        </p>-->
<!--                        <ul class="mt-4 list-inside list-disc space-y-2 text-sm">-->
<!--                            <li>Track estimated ex-dividend and payment dates</li>-->
<!--                            <li>Compare dividend yields, payout amounts, and growth</li>-->
<!--                            <li>Monitor price changes and performance in real-time</li>-->
<!--                            <li>Instantly view top dividend-per-share leaders</li>-->
<!--                            <li>Custom list naming and export capabilities</li>-->
<!--                        </ul>-->
<!--                    </div>-->

<!--                    <div class="order-first lg:order-last">-->
<!--                        <div class="rounded-box overflow-hidden">-->
<!--                            <div class="h-fit rounded-2xl bg-linear-to-br from-accent/75 via-card to-accent/75 p-4 max-lg:order-1">-->
<!--                                <img alt="watchlists" class="rounded-lg" :src="watchlistsUrl" />-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

<!--                &lt;!&ndash; Calendar &ndash;&gt;-->
<!--                <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">-->
<!--                    <div class="rounded-box overflow-hidden">-->
<!--                        <div class="h-fit rounded-2xl bg-linear-to-br from-accent/75 via-card to-accent/75 p-4 max-lg:order-1">-->
<!--                            <img alt="calendar" class="rounded-lg" :src="calendarUrl" />-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="space-y-4 lg:mt-8">-->
<!--                        <div class="mb-6">-->
<!--                            <span class="rounded-2xl bg-accent px-3 py-2 font-bold tracking-widest text-primary">Calendar</span>-->
<!--                        </div>-->
<!--                        <h3 class="mt-3 text-xl font-medium sm:text-2xl">Stay Ahead with Dividend Calendar</h3>-->
<!--                        <p class="mt-1 text-sm">-->
<!--                            Plan your cash flow with precision. Our interactive calendar view helps you visualize upcoming ex-dividend and payment-->
<!--                            dates across all holdings.-->
<!--                        </p>-->
<!--                        <ul class="mt-4 list-inside list-disc space-y-2 text-sm">-->
<!--                            <li>Interactive calendar with ex-dividend and pay dates</li>-->
<!--                            <li>Filter stocks by portfolio and watchlist</li>-->
<!--                            <li>Daily, weekly, and monthly payout previews</li>-->
<!--                            <li>Instant breakdown of expected payments per holding</li>-->
<!--                            <li>Clear summaries of past and upcoming income</li>-->
<!--                        </ul>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </section>-->

<!--        <section class="py-8 lg:mt-24 lg:py-16" id="dual-currency">-->
<!--            <div class="text-center">-->
<!--                <h2 class="text-3xl font-semibold sm:text-4xl">Dual Currency Support</h2>-->
<!--                <p class="mt-4 text-sm sm:text-lg">-->
<!--                    Track your investments in multiple currencies. Stelladend converts and displays values based on your preferred currency settings.-->
<!--                </p>-->
<!--            </div>-->

<!--            <div class="mt-12 p-8 lg:p-16">-->
<!--                <div class="grid grid-cols-3 justify-center gap-6 md:grid-cols-5 lg:col-span-2 xl:grid-cols-10">-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="AUD" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/AUD.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="BRL" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/BRL.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="CAD" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/CAD.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="CHF" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/CHF.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="CNY" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/CNY.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="EUR" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/EUR.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="GBP" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/GBP.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="HKD" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/HKD.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="INR" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/INR.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="JPY" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/JPY.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="KRW" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/KRW.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="MXN" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/MXN.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="NOK" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/NOK.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="NZD" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/NZD.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="PYG" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/PYG.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="RUB" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/RUB.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="SEK" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/SEK.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="SGD" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/SGD.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="TRY" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/TRY.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="ZAR" class="h-20 w-20 rounded-full object-cover" src="/storage/currencies/ZAR.svg" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </section>-->

<!--        <section class="py-8 lg:py-20" id="integrations">-->
<!--            <div class="text-center">-->
<!--                <h2 class="text-3xl font-semibold sm:text-4xl">Import from Your Brokerage</h2>-->
<!--                <p class="mt-4 text-sm sm:text-lg">Simply export your holdings as a CSV from your brokerage and upload it here.</p>-->
<!--            </div>-->

<!--            <div class="mt-12 flex items-center justify-center p-8 lg:p-16">-->
<!--                <div class="grid grid-cols-3 justify-center gap-6 md:grid-cols-5 lg:col-span-2">-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="schwab" class="w-20 rounded-full" :src="schwabLogoUrl" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="fidelity" class="w-20 rounded-full" :src="fidelityLogoUrl" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="robinhood" class="w-20 rounded-full" :src="robinhoodLogoUrl" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="vanguard" class="w-20 rounded-full" :src="vanguardLogoUrl" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="text-center">-->
<!--                        <div class="inline-flex items-center justify-center rounded-full bg-card p-2 shadow">-->
<!--                            <img alt="interactive broker" class="w-20 rounded-full" :src="interactiveBrokerLogoUrl" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </section>-->

<!--        <section v-if="false" class="space-y-8 py-8 lg:py-20" id="pricing">-->
<!--            <div class="text-center">-->
<!--                <h2 class="text-3xl font-semibold sm:text-4xl">Pricing</h2>-->
<!--            </div>-->

<!--            <div class="flex w-full items-center justify-center">-->
<!--                <div class="mt-8 grid w-full grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-2 xl:w-auto">-->
<!--                    &lt;!&ndash; Monthly Plan &ndash;&gt;-->
<!--                    <Card v-if="false">-->
<!--                        <div>-->
<!--                            <div class="flex items-center justify-between">-->
<!--                                <h3 class="text-xl font-semibold text-text">Monthly</h3>-->
<!--                                <span class="rounded-2xl px-3 py-2 text-sm font-bold tracking-widest text-primary">&nbsp;</span>-->
<!--                            </div>-->

<!--                            <p class="mt-4 flex items-baseline">-->
<!--                                <span class="font-mono text-5xl font-semibold tracking-widest text-text"> $4.99 </span>-->
<!--                                <span class="ml-1 text-xl font-medium">/month</span>-->
<!--                            </p>-->
<!--                            <p class="mt-6">Unlock the entire system</p>-->
<!--                        </div>-->

<!--                        <div class="xl:p-6">-->
<!--                            <ul class="list-inside list-disc space-y-3" role="list">-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Unlimited portfolios</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Unlimited watchlists</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Compare Portfolios</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Export Watchlists</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Dual Currency</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Early access to new features</span>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->

<!--                        <div class="flex w-full items-center justify-center">-->
<!--                            <Link :href="route('register')">-->
<!--                                <Button size="large" color="primary"> Sign Up</Button>-->
<!--                            </Link>-->
<!--                        </div>-->
<!--                    </Card>-->

<!--                    &lt;!&ndash; Yearly Plan &ndash;&gt;-->
<!--                    <Card v-if="false">-->
<!--                        <div>-->
<!--                            <div class="flex items-center justify-between">-->
<!--                                <h3 class="text-xl font-semibold text-text">Yearly</h3>-->
<!--                                <span class="rounded-2xl bg-accent px-3 py-2 text-sm font-bold tracking-widest text-primary">Recommended</span>-->
<!--                            </div>-->

<!--                            <p class="mt-4 flex items-baseline">-->
<!--                                <span class="font-mono text-5xl font-semibold tracking-widest text-text"> $49 </span>-->
<!--                                <span class="ml-1 text-xl font-medium">/year</span>-->
<!--                            </p>-->
<!--                            <p class="mt-6">Unlock the entire system</p>-->
<!--                        </div>-->

<!--                        <div class="xl:p-6">-->
<!--                            <ul class="list-inside list-disc space-y-3" role="list">-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Unlimited portfolios</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Unlimited watchlists</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Compare Portfolios</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Export Watchlists</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Dual Currency</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Early access to new features</span>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->

<!--                        <div class="flex w-full items-center justify-center">-->
<!--                            <Link :href="route('register')">-->
<!--                                <Button size="large" color="primary"> Sign Up</Button>-->
<!--                            </Link>-->
<!--                        </div>-->
<!--                    </Card>-->

<!--                    &lt;!&ndash; Lifetime Plan &ndash;&gt;-->
<!--                    <Card v-if="false">-->
<!--                        <div>-->
<!--                            <div class="flex items-center justify-between">-->
<!--                                <h3 class="text-xl font-semibold text-text">Lifetime</h3>-->
<!--                                <span-->
<!--                                    class="rounded-2xl bg-linear-to-br from-primary via-primary to-accent px-3 py-2 text-sm font-bold tracking-widest text-accent"-->
<!--                                    >Limited to first 100</span-->
<!--                                >-->
<!--                            </div>-->

<!--                            <p class="mt-4 flex items-baseline">-->
<!--                                <span class="font-mono text-5xl font-semibold tracking-widest text-text"> $149 </span>-->
<!--                                <span class="ml-1 text-xl font-medium"></span>-->
<!--                            </p>-->
<!--                            <p class="mt-6">Forever access</p>-->
<!--                        </div>-->

<!--                        <div class="xl:p-6">-->
<!--                            <ul class="list-inside list-disc space-y-3" role="list">-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Unlimited portfolios</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Unlimited watchlists</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Compare Portfolios</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Export Watchlists</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Dual Currency</span>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <span class="ms-3">Early access to new features</span>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->

<!--                        <div class="flex w-full items-center justify-center">-->
<!--                            <Link :href="route('register')">-->
<!--                                <Button size="large" color="primary"> Sign Up</Button>-->
<!--                            </Link>-->
<!--                        </div>-->
<!--                    </Card>-->
<!--                </div>-->
<!--            </div>-->
<!--        </section>-->
<!--    </GuestLayout>-->
</template>

<script>
// import GuestLayout from '@/layouts/GuestLayout.vue';
//
// import logoUrl from '@img/logo.svg';
//
// import calendarUrl from '@img/index/calendar.webp';
// import dashboardUrl from '@img/index/dashboard.webp';
// import portfoliosUrl from '@img/index/portfolios.webp';
// import watchlistsUrl from '@img/index/watchlists.webp';
//
// import Button from '@/components/ui/Button.vue';
// import Card from '@/components/ui/Card.vue';
// import NavLogo from '@/layouts/app/NavLogo.vue';
// import NavMenu from '@/layouts/app/NavMenu.vue';
// import NavUser from '@/layouts/app/NavUser.vue';
// import fidelityLogoUrl from '@img/brokerages/fidelity.png';
// import interactiveBrokerLogoUrl from '@img/brokerages/interactive_brokers.png';
// import robinhoodLogoUrl from '@img/brokerages/robinhood.png';
// import schwabLogoUrl from '@img/brokerages/schwab.png';
// import vanguardLogoUrl from '@img/brokerages/vanguard.png';
// import { Link } from '@inertiajs/vue3';
//
// export default {
//     components: {
//         Link,
//         Card,
//         Button,
//         NavLogo,
//         NavMenu,
//         NavUser,
//         GuestLayout,
//     },
//
//     props: {},
//
//     data() {
//         return {
//             logoUrl,
//
//             // Features
//             dashboardUrl,
//             portfoliosUrl,
//             watchlistsUrl,
//             calendarUrl,
//
//             // Brokerages
//             fidelityLogoUrl,
//             interactiveBrokerLogoUrl,
//             robinhoodLogoUrl,
//             schwabLogoUrl,
//             vanguardLogoUrl,
//         };
//     },
//
//     mounted() {},
//
//     watch: {},
//
//     methods: {},
// };
</script>
