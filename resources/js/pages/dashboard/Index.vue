<template>
    <Head title="Dashboard" />

    <AuthLayout>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">

            <!-- Combined Assets Value -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Combined Assets Value</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="accountStats.combinedAssetsValue">{{ accountStats.combinedAssetsValueFormatted }}</span>
                            <span v-if="!accountStats.combinedAssetsValue">-</span>
                        </p>
                        <p v-if="accountStats.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="accountStats.secondaryCurrency.combinedAssetsValue">{{
                                    accountStats.secondaryCurrency.combinedAssetsValueFormatted
                                }}</span>
                            <span v-if="!accountStats.secondaryCurrency.combinedAssetsValue">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Combined Portfolio Value -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Combined Portfolio Value</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="accountStats.combinedPortfolioValue">{{ accountStats.combinedPortfolioValueFormatted }}</span>
                            <span v-if="!accountStats.combinedPortfolioValue">-</span>
                        </p>
                        <p v-if="accountStats.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="accountStats.secondaryCurrency.combinedPortfolioValue">{{
                                accountStats.secondaryCurrency.combinedPortfolioValueFormatted
                            }}</span>
                            <span v-if="!accountStats.secondaryCurrency.combinedPortfolioValue">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Combined Monthly Income -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Combined Monthly Income</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="accountStats.combinedMonthlyIncome">{{ accountStats.combinedMonthlyIncomeFormatted }}</span>
                            <span v-if="!accountStats.combinedMonthlyIncome">-</span>
                        </p>
                        <p v-if="accountStats.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="accountStats.secondaryCurrency.combinedMonthlyIncome">{{
                                accountStats.secondaryCurrency.combinedMonthlyIncomeFormatted
                            }}</span>
                            <span v-if="!accountStats.secondaryCurrency.combinedMonthlyIncome">-</span>
                        </p>
                    </div>
                </div>
            </Card>

            <!-- Combined Annual Income -->
            <Card :hoverable="true">
                <div class="relative flex h-full flex-col items-center justify-center gap-4">
                    <p class="flex items-center text-base text-text-muted">Combined Annual Income</p>

                    <div class="space-y-1 text-center">
                        <p class="font-mono text-xl font-medium tracking-widest">
                            <span v-if="accountStats.combinedAnnualIncome">{{ accountStats.combinedAnnualIncomeFormatted }}</span>
                            <span v-if="!accountStats.combinedAnnualIncome">-</span>
                        </p>
                        <p v-if="accountStats.secondaryCurrency" class="font-mono text-base font-medium tracking-widest text-text-muted">
                            <span v-if="accountStats.secondaryCurrency.combinedAnnualIncome">{{
                                accountStats.secondaryCurrency.combinedAnnualIncomeFormatted
                            }}</span>
                            <span v-if="!accountStats.secondaryCurrency.combinedAnnualIncome">-</span>
                        </p>
                    </div>
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-4 gap-6 sm:gap-8">
            <!-- Portfolios & Accounts -->
            <div class="col-span-full space-y-4 text-text">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="lni lni-pie-chart-2 !text-xl"></i>
                        <p class="text-xl font-medium text-text">Portfolios & Accounts</p>
                    </div>
                </div>

                <Card>
                    <!-- Portfolios & Accounts Graph -->
                    <div v-if="portfoliosAndAccounts.length" class="flex flex-col items-center gap-6 sm:gap-8 lg:flex-row">
                        <Card class="!space-y- w-full flex-shrink-0 bg-card-foreground lg:w-[28rem]">
                            <div
                                v-for="portfoliosAndAccount in portfoliosAndAccounts"
                                class="flex flex-row justify-between gap-2"
                                :class="{ '!flex-col': portfoliosAndAccount.secondaryCurrency }"
                            >
                                <p class="text-sm tracking-wide text-text-muted">{{ portfoliosAndAccount.name }}</p>
                                <div class="flex justify-between">
                                    <span class="font-mono text-base">{{ portfoliosAndAccount.balanceFormatted }}</span>
                                    <span v-if="portfoliosAndAccount.secondaryCurrency" class="font-mono text-base text-text-muted">{{
                                        portfoliosAndAccount.secondaryCurrency.balanceFormatted
                                    }}</span>
                                </div>
                            </div>
                        </Card>

                        <div
                            :style="{ height: `${portfoliosAndAccounts.length * (hasSecondaryCurrency ? 5 : 3.5)}rem` }"
                            class="w-full flex-grow lg:w-auto"
                        >
                            <AccountsChart :chartData="portfoliosAndAccounts" />
                        </div>
                    </div>

                    <!-- Portfolios -->
                    <Table v-if="portfolios.length" size="comfortable" class="-mx-4">
                        <template #head>
                            <TableHeadRow>
                                <!-- Portfolio -->
                                <TableHeader>
                                    <div class="flex items-center gap-2" @click="sort('name')">
                                        <span>Portfolio</span>
                                        <SortIcon v-if="filters.sortBy === 'name'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Total Value -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('currentTotalPortfolioValue')">
                                        <span>Total Value</span>
                                        <SortIcon v-if="filters.sortBy === 'currentTotalPortfolioValue'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Avg. Yield -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('currentAverageDividendYield')">
                                        <span>Avg. Yield</span>
                                        <SortIcon v-if="filters.sortBy === 'currentAverageDividendYield'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Monthly Income -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('currentMonthlyIncome')">
                                        <span>Monthly Income</span>
                                        <SortIcon v-if="filters.sortBy === 'currentMonthlyIncome'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Annual Income -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('currentAnnualIncome')">
                                        <span>Annual Income</span>
                                        <SortIcon v-if="filters.sortBy === 'currentAnnualIncome'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Cash -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('currentCashAvailable')">
                                        <span>Cash</span>
                                        <SortIcon v-if="filters.sortBy === 'currentCashAvailable'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>

                                <!-- Holdings -->
                                <TableHeader>
                                    <div class="flex items-center justify-end gap-2" @click="sort('holdingsCount')">
                                        <span>Holdings</span>
                                        <SortIcon v-if="filters.sortBy === 'holdingsCount'" :direction="filters.sortDirection" />
                                    </div>
                                </TableHeader>
                            </TableHeadRow>
                        </template>

                        <template #body>
                            <TableRow v-for="(portfolio, index) in portfolios" :key="`table-portfolios-row-${index}`">
                                <!-- Portfolio -->
                                <TableCell>{{ portfolio.name }}</TableCell>

                                <!-- Total Value -->
                                <TableCell class="text-right">
                                    <div v-if="portfolio.metrics.currentTotalPortfolioValue">
                                        <p class="font-mono font-medium tracking-widest">
                                            {{ portfolio.metrics.currentTotalPortfolioValueFormatted }}
                                        </p>
                                        <p v-if="portfolio.metrics.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                            {{ portfolio.metrics.secondaryCurrency.currentTotalPortfolioValueFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!portfolio.metrics.currentTotalPortfolioValue" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Avg. Yield -->
                                <TableCell class="text-right">
                                    <div v-if="portfolio.metrics.currentAverageDividendYield">
                                        <p class="font-mono font-medium tracking-widest">
                                            {{ portfolio.metrics.currentAverageDividendYieldFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!portfolio.metrics.currentAverageDividendYield" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Monthly Income -->
                                <TableCell class="text-right">
                                    <div v-if="portfolio.metrics.currentMonthlyIncome">
                                        <p class="font-mono font-medium tracking-widest">
                                            {{ portfolio.metrics.currentMonthlyIncomeFormatted }}
                                        </p>
                                        <p v-if="portfolio.metrics.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                            {{ portfolio.metrics.secondaryCurrency.currentMonthlyIncomeFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!portfolio.metrics.currentMonthlyIncome" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Annual Income -->
                                <TableCell class="text-right">
                                    <div v-if="portfolio.metrics.currentAnnualIncome">
                                        <p class="font-mono font-medium tracking-widest">
                                            {{ portfolio.metrics.currentAnnualIncomeFormatted }}
                                        </p>
                                        <p v-if="portfolio.metrics.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                            {{ portfolio.metrics.secondaryCurrency.currentAnnualIncomeFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!portfolio.metrics.currentAnnualIncome" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Cash -->
                                <TableCell class="text-right">
                                    <div v-if="portfolio.metrics.currentCashAvailable">
                                        <p class="font-mono font-medium tracking-widest">
                                            {{ portfolio.metrics.currentCashAvailableFormatted }}
                                        </p>
                                        <p v-if="portfolio.metrics.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                            {{ portfolio.metrics.secondaryCurrency.currentCashAvailableFormatted }}
                                        </p>
                                    </div>
                                    <div v-if="!portfolio.metrics.currentCashAvailable" class="text-text-muted">-</div>
                                </TableCell>

                                <!-- Holdings -->
                                <TableCell class="text-right">{{ portfolio.metrics.holdingsCountFormatted }}</TableCell>
                            </TableRow>
                        </template>
                    </Table>

                    <p v-if="!portfolios.length" class="flex h-full items-center justify-center py-8 text-sm text-text-muted">No portfolios added</p>
                </Card>
            </div>

            <!-- Pomodoro Timer -->
            <!--            <Card class="relative !space-y-0">-->
            <!--                &lt;!&ndash; Group &ndash;&gt;-->
            <!--                <div class="absolute top-3 left-4">-->
            <!--                    <div class="hover:bg-hover cursor-pointer text-text">-->
            <!--                        <p class="flex items-center tracking-widest text-text">-->
            <!--                            Work-->
            <!--                            <i class="lni lni-chevron-down text-xl"></i>-->
            <!--                        </p>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Settings &ndash;&gt;-->
            <!--                <div class="absolute top-3 right-4">-->
            <!--                    <div class="hover:bg-hover flex cursor-pointer items-center justify-center text-text hover:animate-spin">-->
            <!--                        <i class="lni lni-gear-1 !text-2xl"></i>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Reset &ndash;&gt;-->
            <!--                <div class="absolute bottom-3 left-4">-->
            <!--                    <div class="hover:bg-hover cursor-pointer text-text hover:animate-spin">-->
            <!--                        <i class="lni lni-refresh-circle-1-anticlockwise !text-2xl"></i>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Sessions Done &ndash;&gt;-->
            <!--                <div class="absolute right-4 bottom-4">-->
            <!--                    <div class="hover:bg-hover cursor-pointer text-text">-->
            <!--                        <p class="tracking-widest text-text">0/5</p>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <button-->
            <!--                    @click="toggleTimer"-->
            <!--                    class="relative mt-2 flex h-48 w-48 cursor-pointer items-center justify-center rounded-full transition-all duration-300 hover:bg-accent/10"-->
            <!--                >-->
            <!--                    <svg-->
            <!--                        class="absolute -top-0 left-0 h-full w-full rotate-[-90deg]"-->
            <!--                        viewBox="0 0 100 100"-->
            <!--                        style="filter: drop-shadow(0 0 4px #3b82f6aa)"-->
            <!--                    >-->
            <!--                        <defs>-->
            <!--                            <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">-->
            <!--                                <stop offset="0%" stop-color="#0ea5e9" />-->
            <!--                                <stop offset="100%" stop-color="#3b82f6" />-->
            <!--                            </linearGradient>-->
            <!--                        </defs>-->

            <!--                        &lt;!&ndash; Background Ring &ndash;&gt;-->
            <!--                        <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="4" fill="none" />-->

            <!--                        &lt;!&ndash; Progress Ring &ndash;&gt;-->
            <!--                        <circle-->
            <!--                            cx="50"-->
            <!--                            cy="50"-->
            <!--                            r="45"-->
            <!--                            stroke="url(#progressGradient)"-->
            <!--                            stroke-width="4"-->
            <!--                            fill="none"-->
            <!--                            stroke-linecap="round"-->
            <!--                            :stroke-dasharray="282.74"-->
            <!--                            :stroke-dashoffset="progressOffset"-->
            <!--                            class="transition-all duration-500 ease-linear"-->
            <!--                        />-->
            <!--                    </svg>-->

            <!--                    <div class="pt-6">-->
            <!--                        &lt;!&ndash; Timer stays perfectly centered &ndash;&gt;-->
            <!--                        <span class="font-mono text-3xl text-text">{{ formattedTime }}</span>-->

            <!--                        &lt;!&ndash; Play/Pause icon positioned independently &ndash;&gt;-->
            <!--                        <p class="hover:bg-hover !z-20 cursor-pointer rounded-full font-mono text-text-muted hover:!text-text">-->
            <!--                            <i :class="['lni', running ? 'lni-pause' : 'lni-play']" class="!text-xl !font-bold"></i>-->
            <!--                        </p>-->
            <!--                    </div>-->
            <!--                </button>-->
            <!--            </Card>-->

            <!-- Mini Watchlist -->
            <!--            <Card class="col-span-1 !space-y-4 text-text">-->
            <!--                &lt;!&ndash; JEPI &ndash;&gt;-->
            <!--                <div class="flex items-center justify-between gap-2">-->
            <!--                    <div class="flex w-18 flex-shrink-0 items-center gap-3">-->
            <!--                        <img src="https://financialmodelingprep.com/image-stock/JEPI.png" alt="jepi logo" class="h-4 w-4" />-->
            <!--                        <p class="text-sm font-medium tracking-wide text-text">JEPI</p>-->
            <!--                    </div>-->

            <!--                    <p class="w-14 flex-shrink-0 text-end text-sm tracking-wide text-text-muted">$56.53</p>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; SCHD &ndash;&gt;-->
            <!--                <div class="flex items-center justify-between gap-2">-->
            <!--                    <div class="flex w-18 flex-shrink-0 items-center gap-3">-->
            <!--                        <img src="https://financialmodelingprep.com/image-stock/SCHD.png" alt="jepi logo" class="h-4 w-4" />-->
            <!--                        <p class="text-sm font-medium tracking-wide text-text">SCHD</p>-->
            <!--                    </div>-->

            <!--                    <p class="w-14 flex-shrink-0 text-end text-sm tracking-wide text-text-muted">$27.53</p>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; AVGO &ndash;&gt;-->
            <!--                <div class="flex items-center justify-between gap-2">-->
            <!--                    <div class="flex w-18 flex-shrink-0 items-center gap-3">-->
            <!--                        <img src="https://financialmodelingprep.com/image-stock/AVGO.png" alt="jepi logo" class="h-4 w-4" />-->
            <!--                        <p class="text-sm font-medium tracking-wide text-text">AVGO</p>-->
            <!--                    </div>-->

            <!--                    <p class="w-14 flex-shrink-0 text-end text-sm tracking-wide text-text-muted">$168.53</p>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; NVDA &ndash;&gt;-->
            <!--                <div class="flex items-center justify-between gap-2">-->
            <!--                    <div class="flex w-18 flex-shrink-0 items-center gap-3">-->
            <!--                        <img src="https://financialmodelingprep.com/image-stock/NVDA.png" alt="jepi logo" class="h-4 w-4" />-->
            <!--                        <p class="text-sm font-medium tracking-wide text-text">NVDA</p>-->
            <!--                    </div>-->

            <!--                    <p class="w-14 flex-shrink-0 text-end text-sm tracking-wide text-text-muted">$153.53</p>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; GOOG &ndash;&gt;-->
            <!--                <div class="flex items-center justify-between gap-2">-->
            <!--                    <div class="flex w-18 flex-shrink-0 items-center gap-3">-->
            <!--                        <img src="https://financialmodelingprep.com/image-stock/GOOG.png" alt="jepi logo" class="h-4 w-4" />-->
            <!--                        <p class="text-sm font-medium tracking-wide text-text">GOOG</p>-->
            <!--                    </div>-->

            <!--                    <p class="w-14 flex-shrink-0 text-end text-sm tracking-wide text-text-muted">$192.53</p>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; RTX &ndash;&gt;-->
            <!--                <div class="flex items-center justify-between gap-2">-->
            <!--                    <div class="flex w-18 flex-shrink-0 items-center gap-3">-->
            <!--                        <img src="https://financialmodelingprep.com/image-stock/RTX.png" alt="jepi logo" class="h-4 w-4" />-->
            <!--                        <p class="text-sm font-medium tracking-wide text-text">RTX</p>-->
            <!--                    </div>-->

            <!--                    <p class="w-14 flex-shrink-0 text-end text-sm tracking-wide text-text-muted">$210.53</p>-->
            <!--                </div>-->
            <!--            </Card>-->

            <!-- Food Widget -->
            <!--            <Card class="self-start">-->
            <!--                &lt;!&ndash; Water Drops &ndash;&gt;-->
            <!--                <div class="flex w-full items-center justify-between gap-1">-->
            <!--                    <i class="lni lni-water-drop-1 !text-4xl !text-accent"></i>-->
            <!--                    <i class="lni lni-water-drop-1 !text-4xl !text-accent"></i>-->
            <!--                    <i class="lni lni-water-drop-1 !text-4xl !text-accent"></i>-->
            <!--                    <i class="lni lni-water-drop-1 !text-4xl !text-text-muted"></i>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Food Icons &ndash;&gt;-->

            <!--                <div class="flex w-full items-center justify-between gap-1">-->
            <!--                    <i class="lni lni-dumbbell-1 !text-4xl !text-accent"></i>-->
            <!--                    <i class="lni lni-apple !text-4xl !text-accent"></i>-->
            <!--                    <i class="lni lni-chicken-drumstick-1 !text-4xl !text-accent"></i>-->
            <!--                    <i class="lni lni-milk-bottle-2 !text-4xl !text-accent"></i>-->
            <!--                </div>-->
            <!--            </Card>-->

            <!-- News -->
            <!--            <Card class="col-span-3 self-start text-text">-->
            <!--                <div class="h-80 space-y-4 overflow-y-auto">-->
            <!--                    <p class="text-sm text-text line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-accent ">●</span>-->
            <!--                        <span>Tech stocks power Nasdaq 100 to a record high as markets cheer Israel-Iran ceasefire</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>Amazon and Microsoft announce mass layoffs amid AI investment boom</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>Micron projects high demand for AI-related memory chips, lifting revenue forecasts</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>EU warns U.S. clean-tech incentives could trigger global investment shift</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>Goldman Sachs urges diversification, saying U.S. tech is overexposed</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>Fed Chair warns tariffs may push inflation higher, justifying pause in rate hikes</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>J.P. Morgan forecasts Fed rate cut by December, citing economic resilience from tech</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>China’s lawmakers review tech-led growth strategy, challenging U.S. innovation lead</span>-->
            <!--                    </p>-->

            <!--                    <p class="text-sm text-text/75 line-clamp-1 space-x-2 tracking-wide">-->
            <!--                        <span class="text-text-muted/10">●</span>-->
            <!--                        <span>Cyberattacks on U.S. banks spike following Middle East tensions and strikes on Iran</span>-->
            <!--                    </p>-->
            <!--                </div>-->
            <!--            </Card>-->
        </div>
    </AuthLayout>

    <!-- Welcome -->
    <Modal v-model:isOpen="welcomeModal.isOpen" size="3extra-large">
        <div class="col-span-full mb-4 flex items-center justify-between sm:mb-0">
            <div></div>
            <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleWelcomeModal">
                <i class="lni lni-xmark"></i>
            </div>
        </div>

        <div class="relative flex flex-col gap-6 text-center sm:gap-8 sm:p-8">
            <!-- Header -->
            <div class="space-y-8">
                <h2 class="text-2xl font-bold text-text">Welcome to <span class="font-mono tracking-wide">Stelladend</span></h2>
                <div class="space-y-2">
                    <p class="mx-auto max-w-xl text-sm text-text-muted">You're getting early access to a smarter way to manage your investments.</p>
                    <p class="mx-auto max-w-xl text-sm text-text-muted">
                        Let's get started by checking out your <b>Portfolio</b> or <b>Watchlist</b>.
                    </p>
                </div>
            </div>

            <!-- Beta notice -->
            <div class="rounded-xl bg-card-foreground p-8 text-sm text-accent shadow-sm sm:mx-auto sm:max-w-lg">
                <div class="space-y-4">
                    <p class="mx-auto max-w-xl text-sm">
                        <span class="font-mono tracking-wide">Stelladend</span> is currently in <strong>Beta</strong>.
                    </p>
                    <p class="mx-auto max-w-xl text-sm">Any bugs you find and report will help us improve greatly.</p>
                    <p class="mx-auto max-w-xl text-sm">Thanks for shaping the future with us! 💛</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <Link :href="route('portfolios.index')" class="w-full sm:w-auto">
                    <Button size="large" color="primary" class="w-full sm:w-auto sm:min-w-[200px]">
                        <i class="lni lni-pie-chart-2 mr-2"></i> Open Portfolios
                    </Button>
                </Link>
                <Link :href="route('watchlists.index')" class="w-full sm:w-auto">
                    <Button size="large" color="primary" class="w-full sm:w-auto sm:min-w-[200px]">
                        <i class="lni lni-bookmark-1 mr-2"></i> Open Watchlists
                    </Button>
                </Link>
            </div>
        </div>
    </Modal>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import InputError from '@/components/ui/InputError.vue';
import Modal from '@/components/ui/Modal.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import AccountsChart from '@/pages/dashboard/partials/AccountsChart.vue';
import SecurityHistoricalChart from '@/pages/dashboard/partials/SecurityHistoricalChart.vue';
import Sparkline from '@/pages/dashboard/partials/Sparkline.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue-sonner';
import Tutorial from '@/pages/dashboard/partials/Tutorial.vue';

export default {
    components: {
        Link,
        Tutorial,
        Modal,
        TextLabel,
        TextInput,
        InputError,
        TableRow,
        SortIcon,
        Table,
        TableHeader,
        TableCell,
        TableHeadRow,
        AccountsChart,
        Sparkline,
        Button,
        SecurityHistoricalChart,
        AuthLayout,
        Head,
        Card,
    },

    props: {
        showTutorial: Boolean,
        portfolios: Object,
        portfoliosAndAccounts: Object,
        accountStats: Object,
        hasBeenWelcomed: Boolean,
        hasSecondaryCurrency: Boolean,
    },

    data() {
        return {
            welcomeModal: {
                isOpen: !this.hasBeenWelcomed,
            },

            filters: {
                sortBy: 'name',
                sortDirection: 'asc',
            },
        };
    },

    computed: {
        isPremium() {
            return usePage().props.auth?.user.isPremium;
        },
    },

    mounted() {
        if (this.welcomeModal.isOpen) {
            router.patch(
                route('preferences.update', {
                    key: 'has_been_welcomed',
                    value: true,
                }),
                {},
                {
                    preserveScroll: true,
                    preserveState: true,
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                },
            );
        }
    },

    methods: {
        async sort(sortBy) {
            this.filters.sortBy = sortBy;
            this.filters.sortDirection = this.filters.sortDirection === 'asc' ? 'desc' : 'asc';

            axios
                .post(route('portfolios.sort-holdings'), {
                    holdings: this.localHoldings,
                    ...(this.filters.sortBy && { sortBy: this.filters.sortBy }),
                    ...(this.filters.sortDirection && { sortDirection: this.filters.sortDirection }),
                })
                .then((response) => {
                    this.localHoldings = response.data;
                })
                .catch((error) => {
                    toast.error(error);
                });
        },

        toggleWelcomeModal() {
            this.welcomeModal.isOpen = !this.welcomeModal.isOpen;
        }
    },
};
</script>
