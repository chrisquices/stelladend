<!doctype html>
<html lang="en" class="group/html">
<head>
    <meta charset="utf-8" />
    <title>SaaS Landing Page</title>
    <meta content="Saas Landing page" name="description" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="./images/favicon.ico" />

    <script src="https://cdn.jsdelivr.net/npm/card3d@2.6.5/dist/card3d.min.js"></script>

    @vite('resources/css/index.css')
</head>

<body>
<div
    id="layout-topbar"
    class="bg-base-100 lg:bg-base-100/90 border-base-300 sticky top-0 z-10 border-b data-[at-top=true]:border-transparent lg:backdrop-blur-sm">
    <div class="container">
        <nav class="flex items-center justify-between py-2">
            <div class="flex items-center gap-2">
                <label
                    for="menu-drawer"
                    id="menu-drawer-trigger"
                    aria-label="open sidebar"
                    class="btn btn-square btn-ghost btn-sm lg:hidden">
                    <span class="iconify lucide--menu size-5"></span>
                </label>

                <!-- Navbar Brand logo -->
                <a href="#" class="text-xl font-semibold">Stelladend</a>
            </div>

            <div class="max-lg:hidden">
            </div>

            <div class="space-x-2">
                <a class="btn btn-ghost btn-sm" onclick="register_modal.showModal()">Register</a>
                <a class="btn btn-primary btn-sm" onclick="login_modal.showModal()">Login</a>
            </div>
        </nav>
    </div>
</div>

<section class="py-8 lg:py-20" id="home">
    <div class="container">
        <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">
            <div class="max-lg:order-2">
                <h1 class="text-4xl font-bold tracking-tight lg:text-6xl lg:leading-none">
                    Effortless, powerful, and built for long-term investors.
                </h1>
                <p class="text-base-content/80 mt-8 sm:text-lg">
                    Track your portfolio, visualize dividend income, and stay on top of payouts — all from one beautiful, intuitive dashboard. No spreadsheets. Just results.
                </p>
                <div class="mt-8 inline-flex gap-3 sm:mt-16">
                    <a
                        href="https://daisyui.com/store/checkout?product=2d843d3e-6c74-4cc9-8c7f-f2cbd633a21e&aff=K0XmL"
                        target="_blank">
                        <button class="btn btn-primary">
                            Sign up
                        </button>
                    </a>
                </div>
            </div>

            <div
                class="from-primary/40 to-secondary/40 h-fit rounded-2xl bg-linear-to-r p-3 max-lg:order-1"
                data-card3d
                data-card3d-perspective="1000"
                data-card3d-full-page-listening>
                <img alt="SaaS" class="rounded-lg" src="./images/landing/hero.webp" />
            </div>
        </div>

        {{--        <h2 class="text-base-content/60 mt-12 text-center text-2xl font-medium lg:mt-32">--}}
        {{--            Our Partners--}}
        {{--        </h2>--}}

        {{--        <div--}}
        {{--            class="mt-12 grid grid-cols-2 justify-center gap-8 **:cursor-pointer **:grayscale **:transition-all **:duration-500 **:hover:grayscale-0 sm:grid-cols-3 md:grid-cols-5">--}}
        {{--            <img class="mx-auto h-8" src="./images/logo/google.svg" alt="google logo" />--}}
        {{--            <img--}}
        {{--                class="mx-auto h-6"--}}
        {{--                src="./images/logo/microsoft.svg"--}}
        {{--                alt="microsoft logo" />--}}
        {{--            <img--}}
        {{--                class="mx-auto hidden h-6 md:inline"--}}
        {{--                src="./images/logo/netflix.svg"--}}
        {{--                alt="netflix logo" />--}}
        {{--            <img--}}
        {{--                class="mx-auto hidden h-8 md:inline"--}}
        {{--                src="./images/logo/spotify.svg"--}}
        {{--                alt="spotify logo" />--}}
        {{--            <img--}}
        {{--                class="mx-auto hidden h-8 sm:inline"--}}
        {{--                src="./images/logo/paypal.svg"--}}
        {{--                alt="paypal logo" />--}}
        {{--        </div>--}}
    </div>
</section>

<section class="relative py-8 lg:py-20" id="features">
    <div class="absolute start-[10%]">
        <div
            class="from-primary/10 to-secondary/10 pointer-events-none aspect-square w-60 rounded-full bg-gradient-to-r blur-3xl lg:w-[600px]"></div>
    </div>

    <div class="container">
        <div class="flex flex-col items-center">
            <h2 class="text-3xl font-semibold sm:text-4xl">Everything in One Place</h2>
            <p class="text-base-content/80 mt-4 sm:text-lg">
                Manage, track, and optimize your income like never before.
            </p>
        </div>

        <div class="mt-8 space-y-8 lg:mt-20 xl:space-y-20">
            <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">
                <div class="rounded-box overflow-hidden">
                    <div
                        class="from-primary/40 to-secondary/40 h-fit rounded-2xl bg-linear-to-r p-8 max-lg:order-1"
                    >
                        <img alt="SaaS" class="rounded-lg" src="./images/landing/portfolios.webp" />
                    </div>
                </div>

                <div class="lg:mt-8">
                    <div class="badge badge-soft badge-primary">Portfolios</div>
                    <h3 class="mt-3 text-xl font-medium sm:text-2xl">
                        Portfolios Made Simple
                    </h3>
                    <p class="text-base-content/80 mt-1">
                        Gain complete visibility and control over your investments. Our intuitive dashboard tracks every portfolio—from growth stocks to dividend ETFs—with live
                        performance data, income projections, and visual insights.
                    </p>
                    <ul class="mt-4 list-inside list-disc">
                        <li>Live portfolio market value and income tracking</li>
                        <li>Monthly and annual dividend income projections</li>
                        <li>Full visibility into upcoming dividend dates and payment history</li>
                        <li>Clear allocation breakdown across all holdings</li>
                        <li>Easy manual holding updates or import from your brokerage</li>
                    </ul>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">
                <div class="lg:mt-8">
                    <div class="badge badge-soft badge-secondary">Watchlists</div>
                    <h3 class="mt-3 text-xl font-medium sm:text-2xl">Effortless Dividend Tracking</h3>
                    <p class="text-base-content/80 mt-1">
                        Stay on top of the stocks and ETFs you care about. Our Watchlists let you monitor yield, payouts, prices, and performance—all in one clean, responsive
                        table.
                    </p>

                    <ul class="mt-4 list-inside list-disc">
                        <li>Track estimated ex-dividend and payment dates</li>
                        <li>Compare dividend yields, payout amounts, and growth</li>
                        <li>Monitor price changes and performance in real-time</li>
                        <li>Instantly view top dividend-per-share leaders</li>
                        <li>Custom list naming and export capabilities</li>
                    </ul>
                </div>

                <div class="order-first lg:order-last">
                    <div class="rounded-box overflow-hidden">
                        <div
                            class="from-primary/40 to-secondary/40 h-fit rounded-2xl bg-linear-to-r p-8 max-lg:order-1"
                        >
                            <img alt="SaaS" class="rounded-lg" src="./images/landing/watchlists.webp" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">
                <div class="rounded-box overflow-hidden">
                    <div
                        class="from-primary/40 to-secondary/40 h-fit rounded-2xl bg-linear-to-r p-8 max-lg:order-1"
                    >
                        <img alt="SaaS" class="rounded-lg" src="./images/landing/calendar.webp" />
                    </div>
                </div>

                <div class="lg:mt-8">
                    <div class="badge badge-soft badge-secondary">Calendar</div>
                    <h3 class="mt-3 text-xl font-medium sm:text-2xl">Stay Ahead with Dividend Calendar</h3>
                    <p class="text-base-content/80 mt-1">
                        Plan your cash flow with precision. Our interactive calendar view helps you visualize upcoming ex-dividend and payment dates across all holdings.
                    </p>

                    <ul class="mt-4 list-inside list-disc">
                        <li>Interactive calendar with ex-dividend and pay dates</li>
                        <li>Filter stocks by portfolio and watchlist</li>
                        <li>Daily, weekly, and monthly payout previews</li>
                        <li>Instant breakdown of expected payments per holding</li>
                        <li>Clear summaries of past and upcoming income</li>
                    </ul>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-2 lg:gap-16">
                <div>
                    <div class="badge badge-soft badge-secondary">Accounts</div>
                    <h3 class="mt-3 text-xl font-medium sm:text-2xl">Track All Your Accounts in One Place</h3>
                    <p class="text-base-content/80 mt-1">
                        Stay on top of your finances with unified account tracking. Whether it’s savings, loans, or hidden stashes — get a clear overview and detailed transaction
                        history in one simple view.
                    </p>

                    <ul class="mt-4 list-inside list-disc">
                        <li>Monitor balances across multiple account types</li>
                        <li>Log income, expenses, and transfers with ease</li>
                        <li>Sort and filter movements by date, category, or amount</li>
                    </ul>
                </div>

                <div class="order-first lg:order-last">
                    <div class="rounded-box overflow-hidden">
                        <div
                            class="from-primary/40 to-secondary/40 h-fit rounded-2xl bg-linear-to-r p-8 max-lg:order-1"
                        >
                            <img alt="SaaS" class="rounded-lg" src="./images/landing/watchlists.webp" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-8 lg:py-20" id="dual-currency">
    <div class="container">
        <div class="text-center">
            <h2 class="text-3xl font-semibold sm:text-4xl">Dual Currency Support</h2>
            <p class="text-base-content/80 mt-4 sm:text-lg">
                Upload your portfolio in seconds. Our platform supports CSV files from leading brokerages, no manual entry required.
            </p>
        </div>

        <div class="mt-12 p-8 lg:p-16">
            <div class="grid grid-cols-2 justify-center gap-6 md:grid-cols-5 lg:col-span-2">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="fidelity" class="size-12" src="./images/logo/fidelity.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="robinhood" class="size-12" src="./images/logo/robinhood.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="td ameritrade" class="size-12" src="./images/logo/td.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="schwab" class="size-12" src="./images/logo/schwab.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="etrade" class="size-12" src="./images/logo/etrade.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="webull" class="size-12" src="./images/logo/webull.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="fidelity" class="size-12" src="./images/logo/fidelity.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="fidelity" class="size-12" src="./images/logo/fidelity.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="fidelity" class="size-12" src="./images/logo/fidelity.svg" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                        <img alt="fidelity" class="size-12" src="./images/logo/fidelity.svg" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-8 lg:py-20" id="integrations">
    <div class="container">
        <div class="text-center">
            <h2 class="text-3xl font-semibold sm:text-4xl">Import from Your Brokerage</h2>
            <p class="text-base-content/80 mt-4 sm:text-lg">
                Upload your portfolio in seconds. Our platform supports CSV files from leading brokerages, no manual entry required.
            </p>
        </div>

        <div class="bg-base-200 rounded-box mt-12 p-8 lg:p-16">
            <div class="grid items-center gap-12 lg:grid-cols-5">
                <div class="lg:col-span-3">
                    <h3 class="text-2xl font-medium sm:text-3xl">
                        Fast, Accurate Portfolio Imports
                    </h3>
                    <p class="text-base-content/80 mt-4 sm:text-lg">
                        Simply export your holdings as a CSV from your brokerage and upload it here. We'll handle the rest—parsing your positions, dividends, and allocations into a
                        clear, interactive dashboard.
                    </p>

                    <div class="mt-8 flex justify-center lg:justify-start">
                        <button class="btn btn-primary">Sign up</button>
                    </div>
                </div>

                <div class="grid grid-cols-2 justify-center gap-6 md:grid-cols-3 lg:col-span-2">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                            <img alt="fidelity" class="size-12" src="./images/logo/fidelity.svg" />
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                            <img alt="robinhood" class="size-12" src="./images/logo/robinhood.svg" />
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                            <img alt="td ameritrade" class="size-12" src="./images/logo/td.svg" />
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                            <img alt="schwab" class="size-12" src="./images/logo/schwab.svg" />
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                            <img alt="etrade" class="size-12" src="./images/logo/etrade.svg" />
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white p-6 shadow">
                            <img alt="webull" class="size-12" src="./images/logo/webull.svg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-8 lg:py-20" id="pricing">
    <div class="group container">
        <div class="text-center">
            <h2 class="text-3xl font-semibold sm:text-4xl">Pricing</h2>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="card border-base-300 border p-3">
                <div class="bg-primary/10 rounded-box p-6">
                    <div class="flex justify-between">
                        <h3 class="text-primary text-xl font-semibold">Premium</h3>
                    </div>

                    <p class="mt-4 flex items-baseline">
                                <span
                                    class="text-primary text-5xl font-semibold tracking-tight group-has-[[value=yearly]:checked]:hidden">
                                    $49
                                </span>
                        <span class="ml-1 text-xl font-medium">/year</span>
                    </p>
                    <p class="mt-6">
                        Unlock the entire system
                    </p>
                </div>

                <div class="p-6">
                    <ul class="list-inside list-disc space-y-3" role="list">
                        <li>
                            <span class="ms-3">Unlimited portfolios</span>
                        </li>
                        <li>
                            <span class="ms-3">Unlimited watchlists</span>
                        </li>
                        <li>
                            <span class="ms-3">Compare Portfolios</span>
                        </li>
                        <li>
                            <span class="ms-3">Export Watchlists</span>
                        </li>
                        <li>
                            <span class="ms-3">Dual Currency</span>
                        </li>
                        <li>
                            <span class="ms-3">Early access to new features</span>
                        </li>
                    </ul>
                </div>
                <button class="btn btn-primary btn-block mt-auto">
                    Sign up
                </button>
            </div>

            <div class="card border-base-300 border p-3">
                <div class="bg-primary/10 rounded-box p-6">
                    <div class="flex justify-between">
                        <h3 class="text-primary text-xl font-semibold">Lifetime</h3>
                        <div class="badge badge-primary badge-sm">Recommended</div>
                    </div>

                    <p class="mt-4 flex items-baseline">
                        <span
                            class="text-primary text-5xl font-semibold tracking-tight group-has-[[value=yearly]:checked]:hidden">
                            $149
                        </span>
                        <span class="ml-1 text-xl font-medium"></span>
                    </p>
                    <p class="mt-6">
                        Forever access
                    </p>
                </div>

                <div class="p-6">
                    <ul class="list-inside list-disc space-y-3" role="list">
                        <li>
                            <span class="ms-3">Unlimited portfolios</span>
                        </li>
                        <li>
                            <span class="ms-3">Unlimited watchlists</span>
                        </li>
                        <li>
                            <span class="ms-3">Compare Portfolios</span>
                        </li>
                        <li>
                            <span class="ms-3">Export Watchlists</span>
                        </li>
                        <li>
                            <span class="ms-3">Dual Currency</span>
                        </li>
                        <li>
                            <span class="ms-3">Early access to new features</span>
                        </li>
                    </ul>
                </div>
                <button class="btn btn-primary btn-block mt-auto">
                    Sign up
                </button>
            </div>
        </div>
    </div>
</section>

<script src="./js/app.js"></script>
</body>
</html>
