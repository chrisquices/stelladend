<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="lni lni-hands-holding !text-xl"></i>
                <p class="text-xl font-medium text-text">Holdings</p>
            </div>

            <div class="flex items-center justify-between gap-4">
                <Button v-if="portfolio.mode === 'advanced'" size="normal" color="primary" @click="toggleImportPortfolioModal">
                    <i class="lni lni-upload-1"></i>
                    <span class="hidden md:block">Import</span>
                </Button>

                <Button v-if="portfolio.mode === 'simple'" size="normal" color="primary" @click="toggleManageHoldingsModal">
                    <i class="lni lni-pencil-1"></i>
                    <span class="hidden md:block">Manage Holdings</span>
                </Button>

                <Button v-if="portfolio.mode === 'advanced'" size="normal" color="primary" @click="toggleAddTransactionModal">
                    <i class="lni lni-plus"></i>
                    <span class="hidden md:block">Add Transaction</span>
                </Button>
            </div>
        </div>

        <Card>
            <div class="grid grid-cols-1 gap-6 sm:gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- N° of Holdings -->
                <Card :hoverable="true" class="bg-card-foreground">
                    <div class="relative flex h-full items-start justify-between gap-4">
                        <p class="flex items-center text-base text-text-muted">N° of Holdings</p>

                        <div class="space-y-1 text-center">
                            <p class="font-mono text-base font-medium tracking-widest text-right">
                                <span v-if="portfolio.holdings.length">{{ portfolio.holdings.length }}</span>
                                <span v-if="!portfolio.holdings.length">-</span>
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Yield -->
                <Card :hoverable="true" class="bg-card-foreground">
                    <div class="relative flex h-full items-start justify-between gap-4">
                        <p class="flex items-center text-base text-text-muted">Dividend Yield</p>

                        <div class="space-y-1 text-center">
                            <p class="font-mono text-base font-medium tracking-widest text-right">
                                <span v-if="portfolio.metrics.currentAverageDividendYield">{{
                                    portfolio.metrics.currentAverageDividendYieldFormatted
                                }}</span>
                                <span v-if="!portfolio.metrics.currentAverageDividendYield">-</span>
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Monthly Income -->
                <Card :hoverable="true" class="bg-card-foreground">
                    <div class="relative flex h-full items-start justify-between gap-4">
                        <p class="flex items-center text-base text-text-muted">Monthly Income</p>

                        <div class="space-y-1 text-center">
                            <p class="font-mono text-base font-medium tracking-widest text-right">
                                <span v-if="portfolio.metrics.currentMonthlyIncome">{{ portfolio.metrics.currentMonthlyIncomeFormatted }}</span>
                                <span v-if="!portfolio.metrics.currentMonthlyIncome">-</span>
                            </p>
                            <p v-if="portfolio.metrics.secondaryCurrency" class="font-mono text-sm font-medium tracking-widest text-text-muted">
                                <span v-if="portfolio.metrics.secondaryCurrency.currentMonthlyIncome">{{
                                    portfolio.metrics.secondaryCurrency.currentMonthlyIncomeFormatted
                                }}</span>
                                <span v-if="!portfolio.metrics.secondaryCurrency.currentMonthlyIncome">-</span>
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Annual Income -->
                <Card :hoverable="true" class="bg-card-foreground">
                    <div class="relative flex h-full items-start justify-between gap-4">
                        <p class="flex items-center text-base text-text-muted">Annual Income</p>

                        <div class="space-y-1 text-center">
                            <p class="font-mono text-base font-medium tracking-widest text-right">
                                <span v-if="portfolio.metrics.currentAnnualIncome">{{ portfolio.metrics.currentAnnualIncomeFormatted }}</span>
                                <span v-if="!portfolio.metrics.currentAnnualIncome">-</span>
                            </p>
                            <p v-if="portfolio.metrics.secondaryCurrency" class="font-mono text-sm font-medium tracking-widest text-text-muted">
                                <span v-if="portfolio.metrics.secondaryCurrency.currentAnnualIncome">{{
                                    portfolio.metrics.secondaryCurrency.currentAnnualIncomeFormatted
                                }}</span>
                                <span v-if="!portfolio.metrics.secondaryCurrency.currentAnnualIncome">-</span>
                            </p>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Holding Table FOR MOBILE -->
            <ul v-if="false" class="space-y-6 md:hidden">
                <li v-for="(holding, index) in localHoldings" :key="`list-holding-row-${index}`" class="space-y-6">
                    <!-- Security -->
                    <div class="space-y-6">
                        <div class="border-b border-border"></div>

                        <div class="flex w-full items-center gap-4">
                            <div class="w-7 flex-shrink-0">
                                <img v-if="holding.logoUrl" :src="holding.logoUrl" :alt="`${holding.ticker} logo`" class="h-7 w-7 rounded" />
                                <div
                                    v-if="!holding.logoUrl"
                                    class="flex h-7 w-7 items-center justify-center rounded text-white"
                                    :style="{ backgroundColor: hexToRgba(holding.color, 0.5) }"
                                >
                                    {{ holding.ticker.charAt(0) }}
                                </div>
                            </div>

                            <p class="truncate tracking-wide whitespace-nowrap text-text-muted">
                                <span class="text-text">{{ holding.ticker }}</span>
                                <span class="ml-2 truncate whitespace-nowrap text-text-muted">{{ holding.name }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Current Price -->
                    <div class="space-y-4">
                        <p class="self-start text-sm text-text-muted">Current Price</p>
                        <div v-if="holding.currentMarketPrice" class="flex items-center justify-between text-sm">
                            <p class="text-right font-mono font-medium tracking-widest">{{ holding.currentMarketPriceFormatted }}</p>
                            <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                {{ holding.secondaryCurrency.currentMarketPriceFormatted }}
                            </p>
                        </div>
                        <div v-if="!holding.currentMarketPrice" class="text-text-muted">-</div>
                    </div>

                    <!-- Shares Owned -->
                    <div class="space-y-4">
                        <p class="self-start text-sm text-text-muted">Shares Owned</p>
                        <div v-if="holding.currentSharesOwned" class="flex items-center justify-between text-sm">
                            <p class="text-right font-mono font-medium tracking-widest">{{ holding.currentSharesOwnedFormatted }}</p>
                            <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                {{ holding.secondaryCurrency.currentSharesOwnedFormatted }}
                            </p>
                        </div>
                        <div v-if="!holding.currentSharesOwned" class="text-text-muted">-</div>
                    </div>

                    <!-- Market Value -->
                    <div class="space-y-4">
                        <p class="self-start text-sm text-text-muted">Market Value</p>
                        <div v-if="holding.currentMarketValue" class="flex items-center justify-between text-sm">
                            <p class="text-right font-mono font-medium tracking-widest">{{ holding.currentMarketValueFormatted }}</p>
                            <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                {{ holding.secondaryCurrency.currentMarketValueFormatted }}
                            </p>
                        </div>
                        <div v-if="!holding.currentMarketValue" class="text-text-muted">-</div>
                    </div>

                    <!-- Dividend Yield -->
                    <div class="space-y-4">
                        <p class="self-start text-sm text-text-muted">Dividend Yield</p>
                        <div v-if="holding.currentDividendYield" class="flex items-center justify-between text-sm">
                            <p class="text-right font-mono font-medium tracking-widest">{{ holding.currentDividendYieldFormatted }}</p>
                        </div>
                        <div v-if="!holding.currentDividendYield" class="text-text-muted">-</div>
                    </div>

                    <!-- Monthly Income -->
                    <div class="space-y-4">
                        <p class="self-start text-sm text-text-muted">Monthly Income</p>
                        <div v-if="holding.currentMonthlyIncome" class="flex items-center justify-between text-sm">
                            <p class="text-right font-mono font-medium tracking-widest">{{ holding.currentMonthlyIncomeFormatted }}</p>
                            <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                {{ holding.secondaryCurrency.currentMonthlyIncomeFormatted }}
                            </p>
                        </div>
                        <div v-if="!holding.currentMonthlyIncome" class="text-text-muted">-</div>
                    </div>

                    <!-- Annual Income -->
                    <div class="space-y-4">
                        <p class="self-start text-sm text-text-muted">Annual Income</p>
                        <div v-if="holding.currentAnnualIncome" class="flex items-center justify-between text-sm">
                            <p class="text-right font-mono font-medium tracking-widest">{{ holding.currentAnnualIncomeFormatted }}</p>
                            <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                {{ holding.secondaryCurrency.currentAnnualIncomeFormatted }}
                            </p>
                        </div>
                        <div v-if="!holding.currentAnnualIncome" class="text-text-muted">-</div>
                    </div>
                </li>
            </ul>

            <!-- Holding Table FOR DESKTOP -->
            <Table v-if="localHoldings.length" size="comfortable" class="-mx-4">
                <template #head>
                    <TableHeadRow>
                        <!-- Percentage of Portfolio -->
                        <TableHeader class="w-16">
                            <div class="flex items-center gap-2" @click="sort('percentageOfPortfolio')">
                                <span>%</span>
                                <SortIcon v-if="filters.sortBy === 'percentageOfPortfolio'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Security -->
                        <TableHeader>
                            <div class="flex items-center gap-2" @click="sort('ticker')">
                                <span>Security</span>
                                <SortIcon v-if="filters.sortBy === 'ticker'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Shares Owned -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('currentSharesOwned')">
                                <span>Shares Owned</span>
                                <SortIcon v-if="filters.sortBy === 'currentSharesOwned'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Current Price -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('currentMarketPrice')">
                                <span>Current Price</span>
                                <SortIcon v-if="filters.sortBy === 'currentMarketPrice'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Market Value -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('currentMarketValue')">
                                <span>Market Value</span>
                                <SortIcon v-if="filters.sortBy === 'currentMarketValue'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Gain/Loss -->
                        <TableHeader v-if="portfolio.mode === 'advanced'" class="text-center">
                            <div class="flex items-center justify-end gap-2" @click="sort('currentChangeInPercentage')">
                                <span>Gain Loss % $</span>
                                <SortIcon v-if="filters.sortBy === 'currentChangeInPercentage'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Yield -->
                        <TableHeader>
                            <div class="flex items-center justify-end gap-2" @click="sort('currentDividendYield')">
                                <span>Yield</span>
                                <SortIcon v-if="filters.sortBy === 'currentDividendYield'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Monthly Income -->
                        <TableHeader class="pr-0">
                            <div class="flex items-center justify-end gap-2" @click="sort('currentMonthlyIncome')">
                                <span>Monthly Inc.</span>
                                <SortIcon v-if="filters.sortBy === 'currentMonthlyIncome'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>

                        <!-- Annual Income -->
                        <TableHeader class="pr-0">
                            <div class="flex items-center justify-end gap-2" @click="sort('currentAnnualIncome')">
                                <span>Annual Inc.</span>
                                <SortIcon v-if="filters.sortBy === 'currentAnnualIncome'" :direction="filters.sortDirection" />
                            </div>
                        </TableHeader>
                    </TableHeadRow>
                </template>

                <template #body>
                    <template v-for="(holding, index) in localHoldings" :key="`table-holding-row-${index}`">
                        <!-- Holdings -->
                        <TableRow :ref="`holding-row-${holding.uid}`" @click="expandHolding(holding.uid)" class="cursor-pointer">
                            <!-- Percentage of Portfolio -->
                            <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                {{ holding.percentageOfPortfolioFormatted }}
                            </TableCell>

                            <!-- Security -->
                            <TableCell class="font-bold">
                                <div class="flex items-center gap-4">
                                    <div class="w-7 flex-shrink-0">
                                        <img v-if="holding.logoUrl" :src="holding.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                        <div
                                            v-if="!holding.logoUrl"
                                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                                            :style="{ backgroundColor: hexToRgba(holding.color, 0.5) }"
                                        >
                                            {{ holding.ticker.charAt(0) }}
                                        </div>
                                    </div>
                                    <p>
                                        {{ holding.ticker }}
                                        <span class="ml-2 w-10 truncate whitespace-nowrap text-text-muted">{{ holding.name }}</span>
                                    </p>
                                </div>
                            </TableCell>

                            <!-- Shares Owned -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentSharesOwned">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.currentSharesOwnedFormatted }}</p>
                                </div>
                                <div v-if="!holding.currentSharesOwned" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Current Price -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentMarketPrice">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.currentMarketPriceFormatted }}</p>
                                    <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ holding.secondaryCurrency.currentMarketPriceFormatted }}
                                    </p>
                                </div>
                                <div v-if="!holding.currentMarketPrice" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Market Value -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentMarketValue">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.currentMarketValueFormatted }}</p>
                                    <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ holding.secondaryCurrency.currentMarketValueFormatted }}
                                    </p>
                                </div>
                                <div v-if="!holding.currentMarketValue" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Gain/Loss -->
                            <TableCell v-if="portfolio.mode === 'advanced'" class="text-right">
                                <div v-if="holding.currentChangeInDollar" class="font-mono">
                                    <div class="flex items-center justify-end gap-4">
                                        <div>
                                            <p v-if="holding.currentChangeInPercentage" class="font-mono font-medium tracking-widest">
                                                <span
                                                    :class="{
                                                        'text-restorative': holding.currentChangeInPercentage > 0,
                                                        'text-destructive': holding.currentChangeInPercentage < 0,
                                                    }"
                                                >
                                                    <span v-if="holding.currentChangeInPercentage > 0">+</span
                                                    >{{ holding.currentChangeInPercentageFormatted }}
                                                </span>
                                            </p>
                                            <p v-if="holding.secondaryCurrency">&nbsp;</p>
                                        </div>

                                        <div class="w-20">
                                            <p
                                                class="font-mono font-medium tracking-widest"
                                                :class="{
                                                    'text-restorative': holding.currentChangeInDollar > 0,
                                                    'text-destructive': holding.currentChangeInDollar < 0,
                                                }"
                                            >
                                                <span v-if="holding.currentChangeInDollar > 0">+</span>{{ holding.currentChangeInDollarFormatted }}
                                            </p>
                                            <p
                                                v-if="holding.secondaryCurrency"
                                                class="font-mono font-medium tracking-widest text-text-muted"
                                                :class="{
                                                    'text-restorative': holding.currentChangeInDollar > 0,
                                                    'text-destructive': holding.currentChangeInDollar < 0,
                                                }"
                                            >
                                                <span v-if="holding.currentChangeInDollar > 0">+</span
                                                >{{ holding.secondaryCurrency.currentChangeInDollarFormatted }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!holding.currentChangeInDollar" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Yield -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentDividendYield">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.currentDividendYieldFormatted }}</p>
                                </div>
                                <div v-if="!holding.currentDividendYield" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Monthly Income -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentMonthlyIncome">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.currentMonthlyIncomeFormatted }}</p>
                                    <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ holding.secondaryCurrency.currentMonthlyIncomeFormatted }}
                                    </p>
                                </div>
                                <div v-if="!holding.currentMonthlyIncome" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Annual Income -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentAnnualIncome">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.currentAnnualIncomeFormatted }}</p>
                                    <p v-if="holding.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ holding.secondaryCurrency.currentAnnualIncomeFormatted }}
                                    </p>
                                </div>
                                <div v-if="!holding.currentAnnualIncome" class="text-text-muted">-</div>
                            </TableCell>
                        </TableRow>

                        <!-- Transactions -->
                        <Transition name="fade-slide">
                            <TableRow v-if="holding.uid === expandedHoldingUid" class="hover:!bg-transparent">
                                <TableCell colspan="100%">
                                    <Card class="bg-card-foreground">
                                        <div class="flex items-center justify-between">
                                            <p class="text-base font-medium text-text">Transactions ({{ holding.transactions.length }})</p>

                                            <!-- Transaction Actions -->
                                            <div class="flex items-center justify-between gap-4">
                                                <Button size="normal" color="primaryForeground" onhover="primaryForeground" @click="collapseHolding">
                                                    Close
                                                </Button>
                                            </div>
                                        </div>

                                        <Table size="comfortable" class="-mx-4 max-h-[52rem] overflow-y-auto">
                                            <template #head>
                                                <TableHeadRow>
                                                    <!-- Date -->
                                                    <TableHeader class="w-1">Date</TableHeader>

                                                    <!-- Type -->
                                                    <TableHeader>Type</TableHeader>

                                                    <!-- Shares -->
                                                    <TableHeader class="w-1 text-right">Shares</TableHeader>

                                                    <!-- Price -->
                                                    <TableHeader class="text-right">Price</TableHeader>

                                                    <!-- Fees and Commissions -->
                                                    <TableHeader class="text-right">Fees</TableHeader>

                                                    <!-- Total -->
                                                    <TableHeader class="text-right">Total</TableHeader>

                                                    <!-- Gain Loss in Dollar & Percentage -->
                                                    <TableHeader class="w-34 text-right">Gain/Loss</TableHeader>

                                                    <!-- Action -->
                                                    <TableHeader class="w-1 text-right"></TableHeader>
                                                </TableHeadRow>
                                            </template>

                                            <template #body>
                                                <template
                                                    v-for="(transaction, index) in holding.transactions"
                                                    :key="`table-holding-transaction-inner-row-${index}`"
                                                >
                                                    <!-- Has Entered -->
                                                    <TableRow v-if="transaction.hasEntered">
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                                            {{ transaction.tradeDate }}
                                                        </TableCell>
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted" colspan="100%">
                                                            <div class="flex items-center gap-6 sm:gap-8">
                                                                <p>
                                                                    You entered this position after buying
                                                                    <b>{{ transaction.sharesFormatted }}</b> shares
                                                                </p>
                                                            </div>
                                                        </TableCell>
                                                    </TableRow>

                                                    <!-- Has Exited -->
                                                    <TableRow v-if="transaction.hasExited">
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                                            {{ transaction.tradeDate }}
                                                        </TableCell>
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted" colspan="100%">
                                                            <div class="flex items-center gap-6 sm:gap-8">
                                                                <p>
                                                                    You exited this position after selling
                                                                    <b>{{ transaction.sharesFormatted }}</b> shares
                                                                </p>
                                                            </div>
                                                        </TableCell>
                                                    </TableRow>

                                                    <!-- Received Dividend -->
                                                    <TableRow v-if="transaction.type === 'buy_drip'">
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                                            {{ transaction.tradeDate }}
                                                        </TableCell>
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted" colspan="100%">
                                                            <div class="flex items-center gap-6 sm:gap-8">
                                                                <p>
                                                                    Your dividend was reinvested — you picked up
                                                                    <b>{{ transaction.sharesFormatted }}</b> new shares
                                                                </p>
                                                            </div>
                                                        </TableCell>
                                                    </TableRow>

                                                    <!-- Transaction Data -->
                                                    <TableRow>
                                                        <!-- Date -->
                                                        <TableCell class="font-mono font-medium tracking-widest text-text-muted">
                                                            {{ transaction.tradeDate }}
                                                        </TableCell>

                                                        <!-- Type -->
                                                        <TableCell class="uppercase">
                                                            {{ transaction.typeFormatted }}
                                                        </TableCell>

                                                        <!-- Shares -->
                                                        <TableCell class="text-right">
                                                            <div v-if="transaction.shares">
                                                                <p class="font-mono font-medium tracking-widest">
                                                                    {{ transaction.sharesFormatted }}
                                                                </p>
                                                            </div>
                                                            <div v-if="!transaction.shares" class="text-text-muted">-</div>
                                                        </TableCell>

                                                        <!-- Price -->
                                                        <TableCell class="text-right">
                                                            <div v-if="transaction.price">
                                                                <p class="font-mono font-medium tracking-widest">{{ transaction.priceFormatted }}</p>
                                                                <p
                                                                    v-if="transaction.secondaryCurrency"
                                                                    class="font-mono font-medium tracking-widest text-text-muted"
                                                                >
                                                                    {{ transaction.secondaryCurrency.priceFormatted }}
                                                                </p>
                                                            </div>
                                                            <div v-if="!transaction.price" class="text-text-muted">-</div>
                                                        </TableCell>

                                                        <!-- Fees and Commissions -->
                                                        <TableCell class="text-right">
                                                            <div v-if="transaction.feesAndCommissions">
                                                                <p class="font-mono font-medium tracking-widest">
                                                                    {{ transaction.feesAndCommissionsFormatted }}
                                                                </p>
                                                                <p
                                                                    v-if="transaction.secondaryCurrency"
                                                                    class="font-mono font-medium tracking-widest text-text-muted"
                                                                >
                                                                    {{ transaction.secondaryCurrency.feesAndCommissionsFormatted }}
                                                                </p>
                                                            </div>
                                                            <div v-if="!transaction.feesAndCommissions" class="text-text-muted">-</div>
                                                        </TableCell>

                                                        <!-- Total -->
                                                        <TableCell class="text-right">
                                                            <div v-if="transaction.total">
                                                                <p class="font-mono font-medium tracking-widest">{{ transaction.totalFormatted }}</p>
                                                                <p
                                                                    v-if="transaction.secondaryCurrency"
                                                                    class="font-mono font-medium tracking-widest text-text-muted"
                                                                >
                                                                    {{ transaction.secondaryCurrency.totalFormatted }}
                                                                </p>
                                                            </div>
                                                            <div v-if="!transaction.total" class="text-text-muted">-</div>
                                                        </TableCell>

                                                        <!-- Gain Loss in Dollar & Percentage -->
                                                        <TableCell class="text-right">
                                                            <div v-if="transaction.gainLossInDollar" class="font-mono">
                                                                <div class="flex items-center justify-end gap-4">
                                                                    <div>
                                                                        <p
                                                                            v-if="transaction.gainLossInPercentage"
                                                                            class="font-mono font-medium tracking-widest"
                                                                        >
                                                                            <span
                                                                                :class="{
                                                                                    'text-restorative': transaction.gainLossInPercentage > 0,
                                                                                    'text-destructive': transaction.gainLossInPercentage < 0,
                                                                                }"
                                                                            >
                                                                                <span v-if="transaction.gainLossInPercentage > 0">+</span
                                                                                >{{ transaction.gainLossInPercentageFormatted }}
                                                                            </span>
                                                                        </p>
                                                                        <p v-if="transaction.secondaryCurrency">&nbsp;</p>
                                                                    </div>

                                                                    <div class="w-32">
                                                                        <p
                                                                            class="font-mono font-medium tracking-widest"
                                                                            :class="{
                                                                                'text-restorative': transaction.gainLossInDollar > 0,
                                                                                'text-destructive': transaction.gainLossInDollar < 0,
                                                                            }"
                                                                        >
                                                                            <span v-if="transaction.gainLossInDollar > 0">+</span
                                                                            >{{ transaction.gainLossInDollarFormatted }}
                                                                        </p>
                                                                        <p
                                                                            v-if="transaction.secondaryCurrency"
                                                                            class="font-mono font-medium tracking-widest text-text-muted"
                                                                            :class="{
                                                                                'text-restorative': transaction.gainLossInDollar > 0,
                                                                                'text-destructive': transaction.gainLossInDollar < 0,
                                                                            }"
                                                                        >
                                                                            <span v-if="transaction.gainLossInDollar > 0">+</span
                                                                            >{{ transaction.secondaryCurrency.gainLossInDollarFormatted }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div v-if="!transaction.gainLossInDollar" class="text-text-muted">-</div>
                                                        </TableCell>

                                                        <!-- Actions -->
                                                        <TableCell>
                                                            <div class="flex items-center justify-end gap-2">
                                                                <Button
                                                                    size="small"
                                                                    :color="transaction.note ? 'primaryForeground' : 'transparent'"
                                                                    :onhover="transaction.note ? 'primaryForeground' : 'primaryForeground'"
                                                                    :class="transaction.note ? 'text-text' : 'text-text-muted hover:text-text'"
                                                                    v-tooltip="transaction.note"
                                                                    @click="toggleManageTransactionNoteModal(transaction)"
                                                                >
                                                                    <i class="lni lni-notebook-1"></i>
                                                                </Button>

                                                                <Button
                                                                    size="small"
                                                                    color="transparent"
                                                                    onhover="primaryForeground"
                                                                    class="text-text-muted hover:text-text"
                                                                    @click="toggleEditTransactionModal(transaction)"
                                                                >
                                                                    <i class="lni lni-pencil-1"></i>
                                                                </Button>

                                                                <Button
                                                                    size="small"
                                                                    color="transparent"
                                                                    onhover="destructive"
                                                                    class="text-text-muted hover:text-text"
                                                                    @click="toggleDeleteTransactionAlertDialog(transaction)"
                                                                >
                                                                    <i class="lni lni-trash-1"></i>
                                                                </Button>
                                                            </div>
                                                        </TableCell>
                                                    </TableRow>
                                                </template>
                                            </template>
                                        </Table>
                                    </Card>
                                </TableCell>
                            </TableRow>
                        </Transition>
                    </template>
                </template>
            </Table>

            <div v-if="!localHoldings.length" class="flex h-full flex-col items-center justify-center space-y-4 py-18">
                <div class="flex items-center justify-center space-x-2 text-sm text-text-muted">
                    <span>Start by adding securities to build your portfolio by clicking</span>
                    <span v-if="portfolio.mode === 'simple'">
                        <Button v-if="portfolio.mode === 'simple'" size="small" color="primary" @click="toggleManageHoldingsModal">
                            <i class="lni lni-pencil-1"></i>
                            Manage Holdings
                        </Button>
                    </span>
                    <span v-if="portfolio.mode === 'advanced'" class="space-x-2">
                        <Button size="small" color="primary" @click="toggleImportPortfolioModal">
                            <i class="lni lni-upload-1"></i>
                            Import
                        </Button>
                        <span>or</span>
                        <Button size="small" color="primary" @click="toggleAddTransactionModal">
                            <i class="lni lni-plus"></i>
                            Add Transaction
                        </Button>
                    </span>
                </div>
            </div>
        </Card>

        <!-- Import Portfolio -->
        <Modal v-model:isOpen="importPortfolioModal.isOpen" size="large">
            <div class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Import your Portfolio</p>

                    <div
                        class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                        @click="toggleImportPortfolioModal"
                    >
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-span-full flex flex-col gap-2">
                    <input type="file" ref="fileInput" style="display: none" @change="handleFileUpload" />

                    <div class="space-y-4">
                        <div
                            class="relative flex cursor-pointer items-centergap-6 sm:gap-8 rounded-lg p-4 hover:bg-hover"
                            @click="updateImportPortfolioFormBrokerage('schwab')"
                        >
                            <img :src="schwabLogoUrl" alt="schwab company logo" class="w-12 rounded" />
                            <p class="flex-grow text-sm font-bold">Import from Schwab</p>
                            <p class="text-sm text-text-muted">CSV Only</p>
                        </div>

                        <div
                            class="relative flex cursor-pointer items-centergap-6 sm:gap-8 rounded-lg p-4 hover:bg-hover"
                            @click="updateImportPortfolioFormBrokerage('fidelity')"
                        >
                            <img :src="fidelityLogoUrl" alt="schwab company logo" class="w-12 rounded" />
                            <p class="flex-grow text-sm font-bold">Import from Fidelity</p>
                            <p class="text-sm text-text-muted">CSV Only</p>
                        </div>

                        <div
                            class="relative flex cursor-pointer items-centergap-6 sm:gap-8 rounded-lg p-4 hover:bg-hover"
                            @click="updateImportPortfolioFormBrokerage('robinhood')"
                        >
                            <img :src="robinhoodLogoUrl" alt="schwab company logo" class="w-12 rounded" />
                            <p class="flex-grow text-sm font-bold">Import from Robinhood</p>
                            <p class="text-sm text-text-muted">CSV Only</p>
                        </div>

                        <div
                            class="relative flex cursor-pointer items-centergap-6 sm:gap-8 rounded-lg p-4 hover:bg-hover"
                            @click="updateImportPortfolioFormBrokerage('vanguard')"
                        >
                            <img :src="vanguardLogoUrl" alt="schwab company logo" class="w-12 rounded" />
                            <p class="flex-grow text-sm font-bold">Import from Vanguard</p>
                            <p class="text-sm text-text-muted">CSV Only</p>
                        </div>

                        <div
                            class="relative flex cursor-pointer items-centergap-6 sm:gap-8 rounded-lg p-4 hover:bg-hover"
                            @click="updateImportPortfolioFormBrokerage('interactive_brokers')"
                        >
                            <img :src="interactiveBrokersLogoUrl" alt="schwab company logo" class="w-12 rounded" />
                            <p class="flex-grow text-sm font-bold">Import from Interactive Brokers</p>
                            <p class="text-sm text-text-muted">CSV Only</p>
                        </div>

                        <div class="space-y-2 text-center">
                            <p class="text-sm text-text-muted">Importing a file will replace your current portfolio.</p>
                            <p class="text-sm text-text-muted">Don’t worry — we’ll rebuild everything from your new CSV.</p>
                            <p class="text-sm text-text-muted">
                                Just a heads up: any manual edits, like custom transactions or cash movements, will be cleared too.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleImportPortfolioModal">Cancel</Button>
                </div>
            </div>
        </Modal>

        <!-- Manage Holdings Modal (Simple Portfolio) -->
        <Modal v-model:isOpen="manageHoldingsModal.isOpen" size="6extra-large">
            <form @submit.prevent="updateHoldings" class="grid grid-cols-1 gap-6 sm:gap-8 lg:grid-cols-5">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Manage holdings</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleManageHoldingsModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Security -->
                <div class="flex flex-col gap-2 lg:col-span-2">
                    <TextLabel>Select a security</TextLabel>
                    <Dropdown ref="securitiesSimpleDropdown" class="w-full" :closeTriggerOnClick="false">
                        <template #trigger>
                            <TextInput
                                color="inputForeground"
                                v-show="!addTransactionModal.form.selectedSecurity"
                                v-model="addTransactionModal.search"
                                ref="manageHoldingsSearch"
                                placeholder="Enter a ticker..."
                                class="w-full"
                                @keydown.enter.prevent="handleEnter"
                                @keydown.down.prevent="moveHighlightDown"
                                @keydown.up.prevent="moveHighlightUp"
                            />
                            <DivThatLooksLikeTextInput
                                color="inputForeground"
                                v-if="addTransactionModal.form.selectedSecurity"
                                @click="clearSelectedSecurity"
                            >
                                <div class="flex w-full items-center gap-2">
                                    <div class="w-7 flex-shrink-0">
                                        <img
                                            v-if="addTransactionModal.form.selectedSecurity.logoUrl"
                                            :src="addTransactionModal.form.selectedSecurity.logoUrl"
                                            alt="company logo"
                                            class="h-7 w-7 rounded"
                                        />
                                        <div
                                            v-if="!addTransactionModal.form.selectedSecurity.logoUrl"
                                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                                            :style="{ backgroundColor: hexToRgba(addTransactionModal.form.selectedSecurity.color, 0.5) }"
                                        >
                                            {{ addTransactionModal.form.selectedSecurity.ticker.charAt(0) }}
                                        </div>
                                    </div>
                                    <p class="whitespace-nowrap">{{ addTransactionModal.form.selectedSecurity.ticker }}</p>
                                    <p class="truncate whitespace-nowrap text-text-muted">{{ addTransactionModal.form.selectedSecurity.name }}</p>
                                </div>
                            </DivThatLooksLikeTextInput>
                        </template>

                        <template #content>
                            <ul v-if="addTransactionModal.securities.length > 0" class="max-h-64 w-full space-y-2 overflow-y-auto" tabindex="-1">
                                <li
                                    v-for="(security, index) in addTransactionModal.securities"
                                    class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover"
                                    ref="securityListItems"
                                    :class="{ '!bg-hover': index === addTransactionModal.highlightedIndex }"
                                    @click="updateSelectedSecurity(security)"
                                >
                                    <div class="flex w-full items-center gap-2">
                                        <div class="w-7 flex-shrink-0">
                                            <img v-if="security.logoUrl" :src="security.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                            <div
                                                v-if="!security.logoUrl"
                                                class="flex h-7 w-7 items-center justify-center rounded text-white"
                                                :style="{ backgroundColor: hexToRgba(security.color, 0.5) }"
                                            >
                                                {{ security.ticker.charAt(0) }}
                                            </div>
                                        </div>
                                        <p class="whitespace-nowrap">{{ security.ticker }}</p>
                                        <p class="truncate whitespace-nowrap text-text-muted">{{ security.name }}</p>
                                    </div>
                                </li>
                            </ul>

                            <ul v-if="addTransactionModal.securities.length === 0" class="h-64 overflow-y-auto">
                                <li class="flex h-full w-full items-center justify-center">
                                    <p v-if="!addTransactionModal.search">Search for a security</p>
                                    <p v-if="addTransactionModal.search">No securities found</p>
                                </li>
                            </ul>
                        </template>
                    </Dropdown>
                    <p class="mt-2 text-sm text-text-muted">
                        Current Price:
                        <span v-if="addTransactionModal.form.selectedSecurity">
                            {{ addTransactionModal.form.selectedSecurity.currentPriceFormatted }}
                        </span>
                        <span v-if="!addTransactionModal.form.selectedSecurity">-</span>
                    </p>
                </div>

                <!-- Shares Owned -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Shares Owned</TextLabel>
                    <TextInput
                        color="inputForeground"
                        ref="manageHoldingsSharesOwned"
                        v-model="addTransactionModal.form.shares"
                        size="normal"
                        placeholder="1.000"
                        type="text"
                        class="font-mono font-medium tracking-widest"
                        @keydown.enter.prevent="triggerAddHolding"
                    />
                </div>

                <!-- Average Price -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Average Price</TextLabel>
                    <TextInput
                        color="inputForeground"
                        v-model="addTransactionModal.form.price"
                        size="normal"
                        placeholder="$10.99"
                        type="text"
                        class="font-mono font-medium tracking-widest"
                        @keydown.enter.prevent="triggerAddHolding"
                    />
                </div>

                <!-- Add Holding -->
                <div class="flex flex-col justify-end gap-2">
                    <div class="hidden h-10 sm:block">&nbsp;</div>
                    <Button ref="addHoldingButton" size="normal" color="primaryForeground" @click="addToHoldingsTable"
                        ><i class="lni lni-plus"></i> Add Holding
                    </Button>
                    <div class="hidden sm:block">&nbsp;</div>
                </div>

                <!-- Manage Holdings Table FOR MOBILE-->
                <ul class="space-y-6 md:hidden">
                    <li v-for="(holding, index) in manageHoldingsModal.form.holdings" :key="`list-manage-holding-row-${index}`" class="space-y-6">
                        <!-- Security -->
                        <div class="space-y-6">
                            <div class="border-b border-border"></div>

                            <div class="flex w-full items-center gap-4">
                                <div class="w-7 flex-shrink-0">
                                    <img
                                        v-if="holding.security.logoUrl"
                                        :src="holding.security.logoUrl"
                                        :alt="`${holding.security.ticker} logo`"
                                        class="h-7 w-7 rounded"
                                    />
                                    <div
                                        v-if="!holding.security.logoUrl"
                                        class="flex h-7 w-7 items-center justify-center rounded text-white"
                                        :style="{ backgroundColor: hexToRgba(holding.security.color, 0.5) }"
                                    >
                                        {{ holding.security.ticker.charAt(0) }}
                                    </div>
                                </div>

                                <p class="truncate tracking-wide whitespace-nowrap text-text-muted">
                                    <span class="text-text">{{ holding.security.ticker }}</span>
                                    <span class="ml-2 truncate whitespace-nowrap text-text-muted">{{ holding.security.name }}</span>
                                </p>

                                <div class="flex items-center gap-4">
                                    <Button
                                        size="small"
                                        color="transparent"
                                        onhover="destructive"
                                        class="!text-text-muted hover:!text-text"
                                        @click="removeFromHoldingsTable(holding)"
                                    >
                                        <i class="lni lni-trash-1"></i>
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <!-- Shares Owned -->
                            <div class="space-y-4">
                                <p class="self-start text-sm text-text-muted">Shares Owned</p>
                                <div v-if="holding.currentSharesOwned" class="font-mono">
                                    <TextInput
                                        color="inputForeground"
                                        v-model="holding.currentSharesOwned"
                                        size="normal"
                                        placeholder="1.000"
                                        type="text"
                                        class="w-full font-mono font-medium tracking-widest"
                                    />
                                </div>
                            </div>

                            <!-- Average Price -->
                            <div class="space-y-4">
                                <p class="self-start text-sm text-text-muted">Average Price</p>
                                <TextInput
                                    color="inputForeground"
                                    v-model="holding.averagePrice"
                                    size="normal"
                                    placeholder="1.000"
                                    type="text"
                                    class="w-full font-mono font-medium tracking-widest"
                                />
                            </div>

                            <!-- Cost Basis -->
                            <div class="space-y-4">
                                <p class="self-start text-sm text-text-muted">Cost Basis</p>
                                <p class="font-mono font-medium tracking-widest">{{ holding.costBasisFormatted }}</p>
                            </div>

                            <!-- Market Value -->
                            <div class="space-y-4">
                                <p class="self-start text-sm text-text-muted">Market Value</p>
                                <p class="font-mono font-bold tracking-widest text-accent">{{ holding.marketValueFormatted }}</p>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Manage Holdings Table FOR DESKTOP-->
                <Table size="comfortable" class="col-span-full -mx-4 hidden sm:block">
                    <template #head>
                        <TableHeadRow>
                            <!-- Security -->
                            <TableHeader>Security</TableHeader>

                            <!-- Shares Owned -->
                            <TableHeader class="text-right">Shares Owned</TableHeader>

                            <!-- Average Price -->
                            <TableHeader class="text-right">Average Price</TableHeader>

                            <!-- Cost Basis -->
                            <TableHeader class="text-right">Cost Basis</TableHeader>

                            <!-- Market Value -->
                            <TableHeader class="text-right !text-accent">Market Value</TableHeader>

                            <!-- Actions -->
                            <TableHeader class="w-1 text-right text-accent"></TableHeader>
                        </TableHeadRow>
                    </template>

                    <template #body>
                        <TableRow v-for="(holding, index) in manageHoldingsModal.form.holdings" :key="`table-add-holdings-row-${index}`">
                            <!-- Security -->
                            <TableCell class="font-bold">
                                <div class="flex items-center gap-4">
                                    <div class="w-7 flex-shrink-0">
                                        <img
                                            v-if="holding.security.logoUrl"
                                            :src="holding.security.logoUrl"
                                            alt="company logo"
                                            class="h-7 w-7 rounded"
                                        />
                                        <div
                                            v-if="!holding.security.logoUrl"
                                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                                            :style="{ backgroundColor: hexToRgba(holding.security.color, 0.5) }"
                                        >
                                            {{ holding.security.ticker.charAt(0) }}
                                        </div>
                                    </div>
                                    <p>
                                        {{ holding.security.ticker }}
                                        <span class="ml-2 w-10 truncate whitespace-nowrap text-text-muted">
                                            {{ holding.security.name }}
                                        </span>
                                    </p>
                                </div>
                            </TableCell>

                            <!-- Shares Owned -->
                            <TableCell class="text-right">
                                <div v-if="holding.currentSharesOwned" class="font-mono">
                                    <TextInput
                                        color="inputForeground"
                                        v-model="holding.currentSharesOwned"
                                        size="normal"
                                        placeholder="1.000"
                                        type="text"
                                        class="w-24 font-mono font-medium tracking-widest"
                                    />
                                </div>
                            </TableCell>

                            <!-- Average Price -->
                            <TableCell class="text-right">
                                <div v-if="holding.averagePrice" class="font-mono">
                                    <TextInput
                                        color="inputForeground"
                                        v-model="holding.averagePrice"
                                        size="normal"
                                        placeholder="1.000"
                                        type="text"
                                        class="w-24 font-mono font-medium tracking-widest"
                                    />
                                </div>
                            </TableCell>

                            <!-- Cost Basis -->
                            <TableCell class="text-right">
                                <div v-if="holding.costBasis" class="font-mono">
                                    <p class="font-mono font-medium tracking-widest">{{ holding.costBasisFormatted }}</p>
                                </div>
                            </TableCell>

                            <!-- Market Value -->
                            <TableCell class="text-right">
                                <div v-if="holding.marketValue" class="font-mono">
                                    <p class="font-mono font-bold tracking-widest text-accent">{{ holding.marketValueFormatted }}</p>
                                </div>
                            </TableCell>

                            <!-- Actions -->
                            <TableCell class="w-1 text-right">
                                <div class="flex items-center gap-4">
                                    <Button
                                        size="small"
                                        color="transparent"
                                        onhover="destructive"
                                        class="!text-text-muted hover:!text-text"
                                        @click="removeFromHoldingsTable(holding)"
                                    >
                                        <i class="lni lni-trash-1"></i>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="!manageHoldingsModal.form.holdings.length">
                            <TableCell class="text-right" colspan="100%">
                                <p class="flex h-full items-center justify-center py-6 text-sm text-text-muted">Add a holding to get started</p>
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleManageHoldingsModal"> Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save Holdings
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Add Transaction Modal (Advanced Portfolio) -->
        <Modal v-model:isOpen="addTransactionModal.isOpen" size="large">
            <form @submit.prevent="storeTransaction" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Add a transaction</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleAddTransactionModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Select a security</TextLabel>
                    <Dropdown ref="securitiesAdvancedDropdown" class="w-full">
                        <template #trigger>
                            <TextInput
                                color="inputForeground"
                                v-show="!addTransactionModal.form.selectedSecurity"
                                v-model="addTransactionModal.search"
                                ref="addTransactionSearch"
                                placeholder="Enter a ticker..."
                                class="w-full"
                                @keydown.enter.prevent="handleEnter"
                                @keydown.down.prevent="moveHighlightDown"
                                @keydown.up.prevent="moveHighlightUp"
                            />
                            <DivThatLooksLikeTextInput
                                color="inputForeground"
                                v-if="addTransactionModal.form.selectedSecurity"
                                @click="clearSelectedSecurity"
                            >
                                <div class="flex w-full items-center gap-2">
                                    <div class="w-7 flex-shrink-0">
                                        <img
                                            v-if="addTransactionModal.form.selectedSecurity.logoUrl"
                                            :src="addTransactionModal.form.selectedSecurity.logoUrl"
                                            alt="company logo"
                                            class="h-7 w-7 rounded"
                                        />
                                        <div
                                            v-if="!addTransactionModal.form.selectedSecurity.logoUrl"
                                            class="flex h-7 w-7 items-center justify-center rounded text-white"
                                            :style="{ backgroundColor: hexToRgba(addTransactionModal.form.selectedSecurity.color, 0.5) }"
                                        >
                                            {{ addTransactionModal.form.selectedSecurity.ticker.charAt(0) }}
                                        </div>
                                    </div>
                                    <p class="whitespace-nowrap">{{ addTransactionModal.form.selectedSecurity.ticker }}</p>
                                    <p class="truncate whitespace-nowrap text-text-muted">{{ addTransactionModal.form.selectedSecurity.name }}</p>
                                </div>
                            </DivThatLooksLikeTextInput>
                        </template>

                        <template #content>
                            <ul v-if="addTransactionModal.securities.length > 0" class="max-h-80 w-full overflow-y-auto">
                                <li
                                    v-for="security in addTransactionModal.securities"
                                    class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover"
                                    @click="updateSelectedSecurity(security)"
                                >
                                    <div class="flex w-full items-center gap-2">
                                        <div class="w-7 flex-shrink-0">
                                            <img v-if="security.logoUrl" :src="security.logoUrl" alt="company logo" class="h-7 w-7 rounded" />
                                            <div
                                                v-if="!security.logoUrl"
                                                class="flex h-7 w-7 items-center justify-center rounded text-white"
                                                :style="{ backgroundColor: hexToRgba(security.color, 0.5) }"
                                            >
                                                {{ security.ticker.charAt(0) }}
                                            </div>
                                        </div>
                                        <p class="whitespace-nowrap">{{ security.ticker }}</p>
                                        <p class="truncate whitespace-nowrap text-text-muted">{{ security.name }}</p>
                                    </div>
                                </li>
                            </ul>

                            <ul v-if="addTransactionModal.securities.length === 0" class="h-20 overflow-y-auto">
                                <li class="flex h-full w-full items-center justify-center">
                                    <p v-if="!addTransactionModal.search">Type something</p>
                                    <p v-if="addTransactionModal.search">No tickers found</p>
                                </li>
                            </ul>
                        </template>
                    </Dropdown>
                    <div class="flex items-center justify-between">
                        <p class="mt-2 text-sm text-text-muted">
                            Current Price:
                            <span v-if="addTransactionModal.form.selectedSecurity">
                                {{ addTransactionModal.form.selectedSecurity.currentPriceFormatted }}
                            </span>
                            <span v-if="!addTransactionModal.form.selectedSecurity">-</span>
                        </p>
                        <p class="mt-2 text-sm text-text-muted">Shares Owned: {{ addTransactionModal.currentSharesOwnedFormatted ?? '-' }}</p>
                    </div>
                </div>

                <!-- Type -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Type</TextLabel>
                    <div class="flex w-full justify-startgap-6 sm:gap-8 rounded text-text">
                        <Button
                            size="normal"
                            :color="addTransactionModal.form.type === 'buy' ? 'primaryForeground' : 'transparent'"
                            class="w-full"
                            @click="addTransactionModal.form.type = 'buy'"
                        >
                            Buy
                        </Button>
                        <Button
                            size="normal"
                            :color="addTransactionModal.form.type === 'sell' ? 'primaryForeground' : 'transparent'"
                            class="w-full"
                            @click="addTransactionModal.form.type = 'sell'"
                        >
                            Sell
                        </Button>
                    </div>
                </div>

                <!-- Shares -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Shares</TextLabel>
                    <TextInput color="inputForeground" v-model="addTransactionModal.form.shares" size="normal" placeholder="1.000" type="text" :isMoney="true" />
                </div>

                <!-- Price -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Price</TextLabel>
                    <TextInput color="inputForeground" v-model="addTransactionModal.form.price" size="normal" placeholder="$10.99" type="text" :isMoney="true" />
                </div>

                <!-- Fees & Commissions -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Fees & Commissions</TextLabel>
                    <TextInput
                        color="inputForeground"
                        v-model="addTransactionModal.form.feesAndCommissions"
                        size="normal"
                        placeholder="$0.10"
                        type="text"
                        :isMoney="true"
                    />
                </div>

                <!-- Trade Date -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Trade Date</TextLabel>
                    <VueDatePicker
                        v-model="addTransactionModal.form.tradeDate"
                        :enable-time-picker="false"
                        :clearable="false"
                        :auto-apply="true"
                        model-type="yyyy-MM-dd"
                    >
                        <template #trigger="{ toggle, attrs }">
                            <Button size="normal" color="inputForeground" @click="toggle" class="w-full !justify-start font-mono tracking-widest">
                                <i class="lni lni-calendar-days !text-lg"></i>
                                {{ ymdToReadableDate(addTransactionModal.form.tradeDate) }}
                            </Button>
                        </template>
                    </VueDatePicker>
                    <InputError :error="addTransactionModal.form.errors.tradeDate" />
                </div>

                <!-- Note -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Note</TextLabel>
                    <TextArea color="inputForeground" v-model="addTransactionModal.form.note" rows="5" placeholder="Enter your note here"> </TextArea>
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleAddTransactionModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Edit Transaction -->
        <Modal v-model:isOpen="editTransactionModal.isOpen" size="large">
            <form @submit.prevent="updateTransaction" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Update a transaction</p>

                    <div
                        class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                        @click="toggleEditTransactionModal"
                    >
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Type -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Type</TextLabel>
                    <div class="flex w-full justify-startgap-6 sm:gap-8 rounded text-text">
                        <Button
                            size="normal"
                            :color="editTransactionModal.form.type === 'buy' ? 'primaryForeground' : 'transparent'"
                            class="w-full"
                            @click="editTransactionModal.form.type = 'buy'"
                        >
                            Buy
                        </Button>
                        <Button
                            size="normal"
                            :color="editTransactionModal.form.type === 'sell' ? 'primaryForeground' : 'transparent'"
                            class="w-full"
                            @click="editTransactionModal.form.type = 'sell'"
                        >
                            Sell
                        </Button>
                    </div>
                    <InputError :error="editTransactionModal.form.errors.type" />
                </div>

                <!-- Shares -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Shares</TextLabel>
                    <TextInput color="inputForeground" v-model="editTransactionModal.form.shares" size="normal" placeholder="1.000" type="text" :isMoney="true" />
                    <InputError :error="editTransactionModal.form.errors.shares" />
                </div>

                <!-- Price -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Price</TextLabel>
                    <TextInput color="inputForeground" v-model="editTransactionModal.form.price" size="normal" placeholder="$10.99" type="text" :isMoney="true" />
                    <InputError :error="editTransactionModal.form.errors.price" />
                </div>

                <!-- Fees & Commissions -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Fees & Commissions</TextLabel>
                    <TextInput
                        color="inputForeground"
                        v-model="editTransactionModal.form.feesAndCommissions"
                        size="normal"
                        placeholder="$0.10"
                        type="text"
                        :isMoney="true"
                    />
                    <InputError :error="editTransactionModal.form.errors.feesAndCommissions" />
                </div>

                <!-- Trade Date -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Trade Date</TextLabel>
                    <VueDatePicker
                        v-model="editTransactionModal.form.tradeDate"
                        :enable-time-picker="false"
                        :clearable="false"
                        :auto-apply="true"
                        model-type="yyyy-MM-dd"
                    >
                        <template #trigger="{ toggle, attrs }">
                            <Button size="normal" color="inputForeground" @click="toggle" class="w-full !justify-start font-mono tracking-widest">
                                <i class="lni lni-calendar-days !text-lg"></i>
                                {{ ymdToReadableDate(editTransactionModal.form.tradeDate) }}
                            </Button>
                        </template>
                    </VueDatePicker>
                    <InputError :error="editTransactionModal.form.errors.tradeDate" />
                </div>

                <!-- Note -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Note</TextLabel>
                    <TextArea color="inputForeground" v-model="editTransactionModal.form.note" rows="5" placeholder="Enter your note here">
                    </TextArea>
                    <InputError :error="editTransactionModal.form.errors.note" />
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleEditTransactionModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Edit Transaction Note -->
        <Modal v-model:isOpen="editTransactionNote.isOpen" size="large">
            <form @submit.prevent="updateTransactionNote" class="grid grid-cols-1 gap-6 sm:gap-8">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Edit Note</p>

                    <div
                        class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                        @click="toggleManageTransactionNoteModal"
                    >
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Note -->
                <div class="flex flex-col gap-2">
                    <TextArea color="inputForeground" v-model="editTransactionNote.form.note" rows="5" placeholder="Enter your note here"> </TextArea>
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleManageTransactionNoteModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save Note
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Delete Transaction Alert Dialog -->
        <AlertDialog :show="deleteTransactionAlertDialog.isOpen">
            <template #title>
                <p>Confirmation Required</p>
            </template>
            <template #content>
                <p>Are you sure you want to delete this transaction?</p>
                <p v-if="portfolio.isImported" class="mt-4 text-text-muted">
                    This record was imported from your brokerage. Removing it may impact your portfolio calculations or data accuracy.
                </p>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleDeleteTransactionAlertDialog">Cancel</Button>
                <Button size="normal" color="destructive" @click="deleteTransaction">Delete Transaction</Button>
            </template>
        </AlertDialog>
    </div>
</template>

<script>
import AlertDialog from '@/components/ui/AlertDialog.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import DivThatLooksLikeTextInput from '@/components/ui/DivThatLooksLikeTextInput.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import InfoTooltip from '@/components/ui/InfoTooltip.vue';
import InputError from '@/components/ui/InputError.vue';
import Modal from '@/components/ui/Modal.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextArea from '@/components/ui/TextArea.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { hexToRgba } from '@/utils/colorHelper';
import { formatNumberToDollar, formatNumberWithSeparators, fx } from '@/utils/numberHelper';
import fidelityLogoUrl from '@img/brokerages/fidelity.png';
import interactiveBrokersLogoUrl from '@img/brokerages/interactive_brokers.png';
import robinhoodLogoUrl from '@img/brokerages/robinhood.png';
import schwabLogoUrl from '@img/brokerages/schwab.png';
import vanguardLogoUrl from '@img/brokerages/vanguard.png';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue-sonner';
import { ymdToReadableDate, todayInYmd } from '@/utils/dateHelper.js';

export default {
    components: {
        AlertDialog,
        InputError,
        TextArea,
        DivThatLooksLikeTextInput,
        SortIcon,
        Dropdown,
        TextLabel,
        TextInput,
        Modal,
        TableHeadRow,
        TableCell,
        Table,
        TableRow,
        TableHeader,
        InfoTooltip,
        Card,
        AuthLayout,
        Head,
        Link,
        Button,
    },

    props: {
        portfolio: Object,
        secondaryCurrency: Object,
    },

    data() {
        return {
            schwabLogoUrl,
            robinhoodLogoUrl,
            vanguardLogoUrl,
            interactiveBrokersLogoUrl,
            fidelityLogoUrl,

            // General
            localHoldings: this.portfolio.holdings,
            expandedHoldingUid: null,
            filters: {
                sortBy: 'ticker',
                sortDirection: 'asc',
            },

            // Import Portfolio
            importPortfolioModal: {
                isOpen: false,
                form: useForm({
                    portfolioUid: null,
                    brokerage: null,
                    file: null,
                }),
            },

            // Manage Holdings
            manageHoldingsModal: {
                isOpen: false,
                form: useForm({
                    portfolioUid: null,
                    holdings: [],
                }),
            },

            // Add Transaction
            addTransactionModal: {
                isOpen: false,
                search: '',
                highlightedIndex: 0,
                securities: [],
                currentSharesOwnedFormatted: null,
                form: useForm({
                    portfolioUid: null,
                    securityUid: null,
                    selectedSecurity: null,
                    type: 'buy',
                    shares: null,
                    price: '',
                    feesAndCommissions: null,
                    tradeDate: null,
                    note: null,
                }),
            },

            // Edit Transaction
            editTransactionModal: {
                isOpen: false,
                form: useForm({
                    transactionUid: null,
                    type: null,
                    shares: null,
                    price: null,
                    feesAndCommissions: null,
                    tradeDate: null,
                    note: null,
                }),
            },

            // Edit Transaction Note
            editTransactionNote: {
                isOpen: false,
                form: useForm({
                    transactionUid: null,
                    note: null,
                }),
            },

            // Delete Transaction Alert Dialog
            deleteTransactionAlertDialog: {
                isOpen: false,
                selectedTransaction: null,
            },
        };
    },

    watch: {
        portfolio() {
            this.localHoldings = this.portfolio.holdings;
        },

        'addTransactionModal.search': function () {
            this.getSecurities();

            this.addTransactionModal.highlightedIndex = 0;
        },

        search() {
            this.highlightedIndex = 0;
        },
    },

    methods: {
        hexToRgba,
        ymdToReadableDate,
        todayInYmd,

        // General -----------------------------------------------------------------------------------------------------------------------------------
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

        expandHolding(holdingUid) {
            if (holdingUid === this.expandedHoldingUid) {
                this.collapseHolding();
            } else {
                this.expandedHoldingUid = holdingUid;
            }
        },

        collapseHolding() {
            this.expandedHoldingUid = null;
        },

        // Simple Portfolio --------------------------------------------------------------------------------------------------------------------------
        toggleManageHoldingsModal() {
            this.manageHoldingsModal.isOpen = !this.manageHoldingsModal.isOpen;

            this.manageHoldingsModal.form.holdings = [];

            Object.entries(this.localHoldings).forEach(([key, value]) => {
                this.manageHoldingsModal.form.holdings.push({
                    security: {
                        uid: value.uid,
                        ticker: value.ticker,
                        name: value.name,
                        logoUrl: value.logoUrl,
                        color: value.color,
                    },
                    currentSharesOwned: value.currentSharesOwned.toString(),
                    currentSharesOwnedFormatted: value.currentSharesOwnedFormatted,
                    averagePrice: value.currentAveragePrice.toString(),
                    averagePriceFormatted: value.currentAveragePriceFormatted,
                    costBasis: fx(value.currentSharesOwned * value.currentAveragePrice),
                    costBasisFormatted: formatNumberToDollar(fx(value.currentSharesOwned * value.currentAveragePrice)),
                    marketValue: value.currentMarketValue,
                    marketValueFormatted: value.currentMarketValueFormatted,
                });
            });

            if (this.manageHoldingsModal.isOpen) {
                setTimeout(() => {
                    this.$nextTick(() => {
                        this.$refs.manageHoldingsSearch?.click?.();
                        this.$refs.manageHoldingsSearch?.focus?.();
                    });
                }, 100);
            }
        },

        triggerAddHolding() {
            this.$refs.addHoldingButton?.$el?.click();
        },

        addToHoldingsTable() {
            const form = this.addTransactionModal.form;

            if (!form.selectedSecurity || !form.shares || !form.price) {
                toast.error('All fields are required');
                return;
            }

            const shares = fx(form.shares);
            const price = fx(form.price);
            const marketValue = fx(shares * price);

            this.manageHoldingsModal.form.holdings.push({
                security: form.selectedSecurity,
                currentSharesOwned: shares,
                currentSharesOwnedFormatted: formatNumberWithSeparators(shares),
                averagePrice: price,
                averagePriceFormatted: formatNumberToDollar(price),
                costBasis: fx(shares * price),
                costBasisFormatted: formatNumberToDollar(fx(shares * price)),
                marketValue: marketValue,
                marketValueFormatted: formatNumberToDollar(marketValue),
            });

            // Reset the form
            form.reset();
            this.addTransactionModal.search = '';
            form.selectedSecurity = null;

            if (this.manageHoldingsModal.isOpen) {
                setTimeout(() => {
                    this.$nextTick(() => {
                        this.$refs.manageHoldingsSearch?.click?.();
                        this.$refs.manageHoldingsSearch?.focus?.();
                    });
                }, 100);
            }
        },

        removeFromHoldingsTable(holdingToRemove) {
            this.manageHoldingsModal.form.holdings = this.manageHoldingsModal.form.holdings.filter(
                (holding) => holding.security.uid !== holdingToRemove.security.uid,
            );
        },

        async updateHoldings() {
            this.manageHoldingsModal.form.portfolioUid = this.portfolio.uid

            this.manageHoldingsModal.form.post(route('portfolios.update-holdings'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleManageHoldingsModal();

                    this.localHoldings = this.portfolio.holdings;
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Advanced Portfolio ------------------------------------------------------------------------------------------------------------------------

        // Import Portfolio --------------------------------------------------------------------------------------------------------------------------
        toggleImportPortfolioModal() {
            this.importPortfolioModal.isOpen = !this.importPortfolioModal.isOpen;
        },

        updateImportPortfolioFormBrokerage(brokerage) {
            this.importPortfolioModal.form.brokerage = brokerage;

            this.triggerFileInput();
        },

        triggerFileInput() {
            this.$refs.fileInput.click();
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.importPortfolioModal.form.portfolioUid = this.portfolio.uid;
            this.importPortfolioModal.form.file = file;

            this.importPortfolioModal.form.post(route('portfolios.import-holdings'), {
                forceFormData: true,
                preserveScroll: true,
                onStart: () => {
                    this.toggleImportPortfolioModal();
                },
                onSuccess: () => {
                    const brokerage = this.importPortfolioModal.form.brokerage;

                    if (brokerage === 'schwab') {
                        toast.success('Portfolio imported');
                    }

                    if (brokerage === 'fidelity') {
                        toast.info('Import failed, try again later');
                    }

                    if (brokerage === 'robinhood') {
                        toast.info('Import failed, try again later');
                    }

                    if (brokerage === 'vanguard') {
                        toast.info('Import failed, try again later');
                    }

                    if (brokerage === 'interactive_brokers') {
                        toast.info('Import failed, try again later');
                    }
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Add Transactions --------------------------------------------------------------------------------------------------------------------------
        toggleAddTransactionModal() {
            this.addTransactionModal.isOpen = !this.addTransactionModal.isOpen;

            if (this.addTransactionModal.isOpen) {

                // Focus on Search Security input
                this.$nextTick(() => {
                    this.$refs.addTransactionSearch?.focus?.();
                    this.$refs.addTransactionSearch?.click?.();
                });

                // Set Trade Date to Today
                this.addTransactionModal.form.tradeDate = this.todayInYmd()
            }
        },

        storeTransaction() {
            const form = this.addTransactionModal.form;

            if (!form.selectedSecurity || !form.type || !form.shares || !form.price || !form.tradeDate) {
                toast.error('Please complete all required fields');
                return;
            }

            form.portfolioUid = this.portfolio.uid;
            form.securityUid = form.selectedSecurity.uid;

            form.post(route('portfolios.transactions.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleAddTransactionModal();

                    this.resetAddTransactionForm();

                    toast.success('Transaction added');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        resetAddTransactionForm() {
            this.addTransactionModal.form.search = null;
            this.addTransactionModal.form.selectedSecurity = null;
            this.addTransactionModal.form.securityId = null;
            this.addTransactionModal.form.type = 'buy';
            this.addTransactionModal.form.shares = null;
            this.addTransactionModal.form.price = null;
            this.addTransactionModal.form.feesAndCommissions = null;
            this.addTransactionModal.form.tradeDate = null;
            this.addTransactionModal.form.note = null;
        },

        // Edit Transactions -------------------------------------------------------------------------------------------------------------------------
        toggleEditTransactionModal(transaction) {
            this.editTransactionModal.isOpen = !this.editTransactionModal.isOpen;

            if (transaction) {
                this.editTransactionModal.form.transactionUid = transaction.uid;
                this.editTransactionModal.form.type = transaction.type;
                this.editTransactionModal.form.shares = transaction.shares;
                this.editTransactionModal.form.price = transaction.price;
                this.editTransactionModal.form.feesAndCommissions = transaction.feesAndCommissions;
                this.editTransactionModal.form.tradeDate = transaction.tradeDate;
                this.editTransactionModal.form.note = transaction.note;
            }
        },

        updateTransaction() {
            const form = this.editTransactionModal.form;

            if (!form.type || !form.shares || !form.price || !form.tradeDate) {
                toast.error('Please complete all required fields');
                return;
            }

            const transactionUid = this.editTransactionModal.form.transactionUid;

            form.patch(route('portfolios.transactions.update', { transactionUid: transactionUid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleEditTransactionModal();

                    this.resetEditTransactionForm();

                    toast.success('Transaction updated');
                },
                onError: (errors) => {
                    this.toggleEditTransactionModal();
                    this.resetEditTransactionForm();

                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        resetEditTransactionForm() {
            this.editTransactionModal.form.type = 'buy';
            this.editTransactionModal.form.shares = null;
            this.editTransactionModal.form.price = null;
            this.editTransactionModal.form.feesAndCommissions = null;
            this.editTransactionModal.form.tradeDate = null;
            this.editTransactionModal.form.note = null;
        },

        // Edit Transaction Note ---------------------------------------------------------------------------------------------------------------------
        toggleManageTransactionNoteModal(transaction = null) {
            if (transaction) {
                this.editTransactionNote.form.transactionUid = transaction.uid;
                this.editTransactionNote.form.note = transaction.note;
            }

            this.editTransactionNote.isOpen = !this.editTransactionNote.isOpen;
        },

        updateTransactionNote() {
            const transactionUid = this.editTransactionNote.form.transactionUid;

            this.editTransactionNote.form.patch(route('portfolios.transactions.update-note', { transactionUid: transactionUid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleManageTransactionNoteModal();

                    this.resetManageTransactionNoteForm();

                    toast.success('Note updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        resetManageTransactionNoteForm() {
            this.editTransactionNote.form.transactionUid = null;
            this.editTransactionNote.form.note = null;
        },

        // Delete Transaction ------------------------------------------------------------------------------------------------------------------------
        toggleDeleteTransactionAlertDialog(transaction = null) {
            this.deleteTransactionAlertDialog.isOpen = !this.deleteTransactionAlertDialog.isOpen;

            if (transaction) {
                this.deleteTransactionAlertDialog.selectedTransaction = transaction;
            }
        },

        deleteTransaction() {
            const transactionUid = this.deleteTransactionAlertDialog.selectedTransaction.uid;

            router.delete(route('portfolios.transactions.delete', { transactionUid: transactionUid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleDeleteTransactionAlertDialog();
                    this.resetDeleteTransactionAlertDialog();

                    toast.success('Transaction deleted');
                },
                onFinish: () => {
                    if (!this.localHoldings.find((localHolding) => localHolding.uid === this.expandedHoldingUid)) {
                        this.collapseHolding();
                    }
                },
                onError: (errors) => {
                    this.toggleDeleteTransactionAlertDialog();
                    this.resetDeleteTransactionAlertDialog();

                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        resetDeleteTransactionAlertDialog() {
            this.deleteTransactionAlertDialog.selectedTransaction = null;
        },

        // Dropdown ----------------------------------------------------------------------------------------------------------------------------------
        moveHighlightDown() {
            const max = this.addTransactionModal.securities.length - 1;
            if (this.addTransactionModal.highlightedIndex < max) {
                this.addTransactionModal.highlightedIndex++;
                this.scrollHighlightedItemIntoView();
            }
        },

        moveHighlightUp() {
            if (this.addTransactionModal.highlightedIndex > 0) {
                this.addTransactionModal.highlightedIndex--;
                this.scrollHighlightedItemIntoView();
            }
        },

        scrollHighlightedItemIntoView() {
            // this.$nextTick(() => {
            //     const listItems = this.$refs.securityListItems;
            //     const item = listItems?.[this.addTransactionModal.highlightedIndex];
            //     item?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            // });
        },

        handleEnter() {
            const firstItem = this.addTransactionModal.securities?.[this.addTransactionModal.highlightedIndex];

            this.updateSelectedSecurity(firstItem);

            this.$refs.securitiesSimpleDropdown?.closeDropdown();
            this.$refs.securitiesAdvancedDropdown?.closeDropdown();
        },

        async getSecurities() {
            try {
                const response = await axios.get(route('portfolios.get-securities'), {
                    params: {
                        search: this.addTransactionModal.search || '',
                    },
                });

                const existingUids = this.manageHoldingsModal.form.holdings.map((h) => h.security.uid);

                // Filter out securities that are already in holdings
                this.addTransactionModal.securities = response.data.filter((security) => {
                    return !existingUids.includes(security.uid);
                });
            } catch (error) {
                toast.error(error);
            }
        },

        updateSelectedSecurity(security) {
            this.addTransactionModal.search = '';

            this.addTransactionModal.form.selectedSecurity = security;

            const relevantHoldings = this.portfolio.holdings.find((holding) => holding.uid === security.uid);

            if (relevantHoldings && relevantHoldings.currentSharesOwned) {
                this.addTransactionModal.currentSharesOwnedFormatted = relevantHoldings.currentSharesOwnedFormatted;
            } else {
                this.addTransactionModal.currentSharesOwnedFormatted = 0;
            }

            if (this.manageHoldingsModal.isOpen) {
                this.$nextTick(() => {
                    this.$refs.manageHoldingsSharesOwned?.focus?.();
                });
            }
        },

        clearSelectedSecurity() {
            this.addTransactionModal.search = '';
            this.addTransactionModal.form.selectedSecurity = null;

            this.$nextTick(() => {
                this.$refs.addTransactionSearch?.focus?.();
            });
        },
    },
};
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.2s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

.fade-slide-enter-to,
.fade-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>
