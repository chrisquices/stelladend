<template>
    <div>
        <div class="flex flex-col items-center justify-center space-y-4 sm:flex-row sm:space-y-0">
            <!--            <div class="w-full sm:w-10">-->
            <!--                <p-->
            <!--                    v-if="selectedPortfolio.mode === 'simple'"-->
            <!--                    class="text-center font-mono text-sm tracking-widest whitespace-nowrap text-text-muted/50 sm:text-left"-->
            <!--                >-->
            <!--                    SIMPLE PORTFOLIO-->
            <!--                </p>-->
            <!--                <p-->
            <!--                    v-if="selectedPortfolio.mode === 'advanced'"-->
            <!--                    class="text-center font-mono text-sm tracking-widest whitespace-nowrap text-text-muted/50 sm:text-left"-->
            <!--                >-->
            <!--                    ADVANCED PORTFOLIO-->
            <!--                </p>-->
            <!--            </div>-->
            <div class="flex flex-grow flex-col items-center gap-4 sm:flex-row w-full">
                <div class="flex items-center justify-between w-full sm:w-auto">
                    <Dropdown width="100" class="md:w-auto">
                        <template #trigger>
                            <div class="flex items-center justify-center gap-2">
                                <div
                                    class="flex h-10 cursor-pointer items-center sm:justify-center gap-2 rounded-full bg-primary px-3 py-2 text-sm tracking-wide !text-text-inverted text-text-muted transition-all duration-300 hover:bg-primary/20 sm:px-4 sm:py-1 whitespace-nowrap w-44 sm:w-auto truncate"
                                >
                                    {{ selectedPortfolio.name }}
                                    <i v-if="portfolios.length > 1" class="lni lni-chevron-down !font-bold"></i>
                                </div>
                            </div>
                        </template>

                        <template #content>
                            <ul class="w-full">
                                <li v-for="portfolio in portfolios" :key="`type-filter-${portfolio.uid}`">
                                    <template v-if="portfolio.uid !== selectedPortfolio.uid">
                                        <Link :href="route('portfolios.show', { portfolioUid: portfolio.uid })">
                                            <div class="h-10 cursor-pointer px-4 py-2 text-left whitespace-nowrap hover:bg-hover">
                                                {{ portfolio.name }}
                                            </div>
                                        </Link>
                                    </template>
                                </li>
                            </ul>
                        </template>
                    </Dropdown>

                    <div class="flex sm:hidden flex-grow flex-shrink-0 items-end justify-end">
                        <Button size="normal" color="transparent" @click="toggleAddPortfolioModal" v-tooltip="'Add Portfolio'">
                            <i class="lni lni-plus !text-2xl !text-text-muted"></i>
                        </Button>

                        <Button size="normal" color="transparent" @click="toggleEditPortfolioModal" v-tooltip="'Edit Portfolio'">
                            <i class="lni lni-brush-2 !text-2xl !text-text-muted"></i>
                        </Button>

                        <Button
                            v-if="portfolios.length >= 2"
                            size="normal"
                            color="transparent"
                            @click="toggleComparePortfolioModal()"
                            v-tooltip="'Compare portfolios'"
                        >
                            <i class="lni lni-bar-chart-3 !text-2xl !text-text-muted"></i>
                        </Button>

                        <Button
                            v-if="selectedPortfolio.isPublic"
                            size="normal"
                            color="transparent"
                            @click="copyPublicPortfolioLink()"
                            v-tooltip="'Copy public URL'"
                        >
                            <i class="lni lni-link-1-angular-right !text-2xl !text-text-muted"></i>
                        </Button>
                    </div>
                </div>
                <div class="w-full flex items-start overflow-x-auto">
                    <div class="inline-flex items-center gap-4 px-2 py-1">
                        <Link
                            :href="route('portfolios.show', { portfolioUid: selectedPortfolio.uid, tab: 'overview' })"
                            class="flex-shrink-0 flex h-10 items-center justify-center gap-2 rounded-full px-3 py-2 text-sm tracking-wide text-text-muted transition-all duration-300 whitespace-nowrap hover:bg-primary/20 sm:px-4 sm:py-1"
                            :class="{ 'bg-primary !text-text-inverted': selectedTab === 'overview' }"
                        >
                            Overview
                        </Link>

                        <Link
                            :href="route('portfolios.show', { portfolioUid: selectedPortfolio.uid, tab: 'upcoming-dividends' })"
                            class="flex-shrink-0 flex h-10 items-center justify-center gap-2 rounded-full px-3 py-2 text-sm tracking-wide text-text-muted transition-all duration-300 whitespace-nowrap hover:bg-primary/20 sm:px-4 sm:py-1"
                            :class="{ 'bg-primary !text-text-inverted': selectedTab === 'upcoming-dividends' }"
                        >
                            Upcoming Dividends
                            <span v-if="upcomingDividends.length" class="!font-bold text-restorative">({{ upcomingDividends.length }})</span>
                        </Link>

                        <Link
                            :href="route('portfolios.show', { portfolioUid: selectedPortfolio.uid, tab: 'dividends-received' })"
                            class="flex-shrink-0 flex h-10 items-center justify-center gap-2 rounded-full px-3 py-2 text-sm tracking-wide text-text-muted transition-all duration-300 whitespace-nowrap hover:bg-primary/20 sm:px-4 sm:py-1"
                            :class="{ 'bg-primary !text-text-inverted': selectedTab === 'dividends-received' }"
                        >
                            Dividends Received
                        </Link>

<!--                        <Link-->
<!--                            :href="route('portfolios.show', { portfolioUid: selectedPortfolio.uid, tab: 'cash-movements' })"-->
<!--                            class="flex-shrink-0 flex h-10 items-center justify-center gap-2 rounded-full px-3 py-2 text-sm tracking-wide text-text-muted transition-all duration-300 whitespace-nowrap hover:bg-primary/20 sm:px-4 sm:py-1"-->
<!--                            :class="{ 'bg-primary !text-text-inverted': selectedTab === 'cash-movements' }"-->
<!--                        >-->
<!--                            Cash Movements-->
<!--                        </Link>-->
                    </div>
                </div>
            </div>
            <div class="hidden sm:flex w-10 flex-shrink-0 items-end justify-center sm:justify-end">
                <Button size="normal" color="transparent" @click="toggleAddPortfolioModal" v-tooltip="'Add Portfolio'">
                    <i class="lni lni-plus !text-2xl !text-text-muted"></i>
                </Button>

                <Button size="normal" color="transparent" @click="toggleEditPortfolioModal" v-tooltip="'Edit Portfolio'">
                    <i class="lni lni-brush-2 !text-2xl !text-text-muted"></i>
                </Button>

                <Button
                    v-if="portfolios.length >= 2"
                    size="normal"
                    color="transparent"
                    @click="toggleComparePortfolioModal()"
                    v-tooltip="'Compare portfolios'"
                >
                    <i class="lni lni-bar-chart-3 !text-2xl !text-text-muted"></i>
                </Button>

                <Button
                    v-if="selectedPortfolio.isPublic"
                    size="normal"
                    color="transparent"
                    @click="copyPublicPortfolioLink()"
                    v-tooltip="'Copy public URL'"
                >
                    <i class="lni lni-link-1-angular-right !text-2xl !text-text-muted"></i>
                </Button>
            </div>
        </div>

        <!-- Add Portfolio -->
        <Modal v-model:isOpen="addPortfolioForm.isOpen" size="2extra-large">
            <form @submit.prevent="storePortfolio" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Add a new Portfolio</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleAddPortfolioModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Name -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Name</TextLabel>
                    <TextInput
                        color="inputForeground"
                        ref="addPortfolioFormName"
                        v-model="addPortfolioForm.form.name"
                        size="normal"
                        placeholder="My New Portfolio"
                        type="text"
                    />
                </div>

                <!-- Select a Mode -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel class="mb-4 text-center text-text-muted">Select a mode</TextLabel>
                    <div class="space-y-8">
                        <Card
                            class="relative flex cursor-pointer flex-col items-start !space-y-4 bg-transparent"
                            @click="updateSelectedMode('simple')"
                            :class="{ '!bg-primary !text-text-inverted': addPortfolioForm.form.mode === 'simple' }"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <i class="lni lni-bolt-1 !text-2xl"></i>
                                </div>

                                <p class="font-bold">Simple Portfolio</p>

                                <div v-if="addPortfolioForm.form.mode === 'simple'" class="flex items-center">
                                    <i class="lni lni-check-circle-2 !text-xl !text-restorative"></i>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <p>Manually record your holdings by entering the stock, number of shares, and average share price.</p>
                                <p class="font-bold">
                                    Does NOT keep track of transactions, cash movements, received dividends and reinvested dividends.
                                </p>
                            </div>
                        </Card>

                        <Card
                            class="relative flex cursor-pointer flex-col items-start !space-y-4 bg-transparent"
                            @click="updateSelectedMode('advanced')"
                            :class="{ '!bg-primary !text-text-inverted': addPortfolioForm.form.mode === 'advanced' }"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <i class="lni lni-bar-chart-5 !text-2xl"></i>
                                </div>

                                <p class="font-bold">Advanced Portfolio</p>

                                <div v-if="addPortfolioForm.form.mode === 'advanced'" class="flex items-center">
                                    <i class="lni lni-check-circle-2 !text-xl !text-restorative"></i>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <p>This mode provides the most accurate performance, income tracking, and portfolio analysis.</p>
                                <p>— Import CSV files from your brokerage</p>
                                <p>— Manage Transactions</p>
                                <p>— Manage Cash Movements</p>
                                <p>— View Received Dividends</p>
                                <p>— Cash Deposits, Withdrawals and Reinvested Dividends</p>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleAddPortfolioModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Edit Portfolio -->
        <Modal v-model:isOpen="editPortfolioModal.isOpen" size="2extra-large">
            <form @submit.prevent="updatePortfolio" class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Edit {{ selectedPortfolio.name }}</p>

                    <div class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover" @click="toggleEditPortfolioModal">
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <!-- Name -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Name</TextLabel>
                    <TextInput color="inputForeground" v-model="editPortfolioModal.form.name" size="normal" placeholder="My Portfolio" type="text" />
                </div>

                <!-- Is Default -->
                <div class="col-span-full flex flex-col gap-2">
                    <div class="col-span-full flex justify-between gap-2">
                        <TextLabel>Make Default</TextLabel>
                        <Switch size="small" v-model="editPortfolioModal.form.isDefault" label="" />
                    </div>
                    <p class="text-sm text-text-muted">Your default portfolio is the one that loads first when you open the portfolios page.</p>
                </div>

                <!-- Is Public -->
                <div class="col-span-full flex flex-col gap-2">
                    <div class="col-span-full flex justify-between gap-2">
                        <TextLabel>Make Public</TextLabel>
                        <Switch size="small" v-model="editPortfolioModal.form.isPublic" label="" />
                    </div>
                    <p class="text-sm text-text-muted">
                        Public portfolios allow anyone to view key details such as monthly income, annual income, yield, holdings, and year-to-date
                        dividends.
                    </p>
                    <p class="text-sm text-text-muted">This is the link to your public portfolio.</p>
                    <div class="group relative mt-2 flex w-full cursor-pointer items-center hover:text-text" @click="copyPublicPortfolioLink">
                        <DivThatLooksLikeTextInput color="inputForeground" class="flex w-full items-center text-text-muted group-hover:text-text">
                            {{ selectedPortfolio.publicUrl }}
                        </DivThatLooksLikeTextInput>

                        <div class="absolute top-1/2 right-2 -translate-y-1/2 text-text-muted group-hover:text-text">
                            <i class="lni lni-copy-1 !text-xl text-base"></i>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <!-- Select a Mode -->
                <div class="col-span-full flex flex-col gap-2">
                    <div class="space-y-8">
                        <Card
                            v-if="selectedPortfolio.mode === 'advanced'"
                            class="relative flex cursor-pointer flex-col items-center gap-0 bg-card-foreground sm:flex-row sm:gap-8 sm:!space-y-0"
                            @click="toggleSwitchPortfolioModeAlertDialog()"
                        >
                            <div class="w-full flex-shrink-0 text-center sm:w-14">
                                <i class="lni lni-bolt-1 !text-4xl"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-bold">Switch to Simple Mode</p>
                                <div class="space-y-2 text-sm">
                                    <p>Manually record your holdings by entering the stock, number of shares, and average share price.</p>
                                    <p>Does NOT keep track of transactions, cash movements, received dividends and reinvested dividends.</p>
                                </div>
                                <p class="text-sm text-text-muted italic">This will wipe your entire portfolio.</p>
                            </div>
                        </Card>

                        <Card
                            v-if="selectedPortfolio.mode === 'simple'"
                            class="relative flex cursor-pointer flex-col items-center gap-0 bg-card-foreground sm:flex-row sm:gap-8 sm:!space-y-0"
                            @click="toggleSwitchPortfolioModeAlertDialog()"
                        >
                            <div class="w-full flex-shrink-0 text-center sm:w-14">
                                <i class="lni lni-bar-chart-5 !text-4xl"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-bold">Switch to Advanced Mode</p>
                                <p class="text-sm text-text-muted">
                                    This mode provides the most accurate performance, income tracking, and portfolio analysis.
                                </p>
                                <ul class="list-disc space-y-1 pl-4 text-sm">
                                    <li>Import CSV files from your brokerage</li>
                                    <li>Manage Transactions</li>
                                    <li>Manage Cash Movements</li>
                                    <li>View Received Dividends & Reinvested Dividends</li>
                                </ul>
                                <p class="text-sm text-text-muted italic">This will wipe your entire portfolio.</p>
                            </div>
                        </Card>
                    </div>
                </div>

                <div class="col-span-full flex flex-col gap-2">
                    <div class="space-y-8">
                        <Card
                            class="relative flex cursor-pointer flex-col items-center gap-0 bg-card-foreground sm:flex-row sm:gap-8 sm:!space-y-0"
                            @click="toggleConfirmWipePortfolioAlertDialog"
                        >
                            <div class="w-full flex-shrink-0 text-center sm:w-14">
                                <i class="lni lni-broom !text-4xl"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-bold">Wipe Portfolio</p>
                                <p class="text-sm text-text-muted">
                                    This will delete all of your holdings, transactions, cash movements and dividends received.
                                </p>
                                <p class="text-sm text-text-muted italic">This action is irreversible.</p>
                            </div>
                        </Card>

                        <Card
                            v-if="portfolios.length > 1"
                            class="relative flex cursor-pointer flex-col items-center gap-0 bg-card-foreground sm:flex-row sm:gap-8 sm:!space-y-0"
                            @click="toggleConfirmDeletePortfolioAlertDialog"
                        >
                            <div class="w-full flex-shrink-0 text-center sm:w-14">
                                <i class="lni lni-trash-1 !text-4xl"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-bold">Delete Portfolio</p>
                                <p class="text-sm text-text-muted">This will delete all of your portfolio data and is irreversible.</p>
                                <p class="text-sm text-text-muted italic">This action is irreversible.</p>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Cancel - Save -->
                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleEditPortfolioModal">Cancel</Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Compare Portfolio -->
        <Modal v-model:isOpen="comparePortfolioModal.isOpen" size="7extra-large">
            <div class="space-y-4">
                <div class="col-span-full flex items-center justify-between">
                    <p class="font-medium text-text">Compare Portfolios</p>

                    <div
                        class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                        @click="toggleComparePortfolioModal"
                    >
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <p v-if="comparePortfolioModal.portfolios.length === 2" class="text-sm text-text-muted">
                    Comparing <strong>{{ comparePortfolioModal.portfolios[0].name }}</strong> →
                    <strong>{{ comparePortfolioModal.portfolios[1].name }}</strong>
                </p>

                <Table v-if="comparePortfolioModal.portfolios.length === 2" size="comfortable" class="-mx-4">
                    <template #head>
                        <TableHeadRow class="!h-14">
                            <TableHeader class="w-[10%]">Metric</TableHeader>

                            <!-- Portfolio 1 -->
                            <TableHeader class="!mb-8 w-[20%] text-right">{{ comparePortfolioModal.portfolios[0].name }}</TableHeader>

                            <!-- Portfolio 2 -->
                            <TableHeader class="w-[20%] text-right">
                                <div class="flex w-full items-center justify-end">
                                    <div class="w-44">
                                        <Select size="small" color="inputForeground" v-model="comparePortfolioModal.portfolio2Uid">
                                            <option
                                                v-for="portfolio in portfolios.filter((p) => p?.uid && p.uid !== comparePortfolioModal.portfolio1Uid)"
                                                :key="portfolio.uid"
                                                :value="portfolio.uid"
                                            >
                                                {{ portfolio.name }}
                                            </option>
                                        </Select>
                                    </div>
                                </div>
                            </TableHeader>

                            <!-- Spacer -->
                            <TableHeader class="w-[5%]"></TableHeader>

                            <!-- Result -->
                            <TableHeader class="w-[45%] font-bold">Result</TableHeader>
                        </TableHeadRow>
                    </template>

                    <template #body>
                        <!-- Total Portfolio Value -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-pie-chart-2"></i>
                                    <p>Total Portfolio Value</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentTotalPortfolioValue">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentTotalPortfolioValueFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentTotalPortfolioValueFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentTotalPortfolioValue" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Total Portfolio Value is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentTotalPortfolioValue" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentTotalPortfolioValue"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentTotalPortfolioValue > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentTotalPortfolioValue < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentTotalPortfolioValueFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentTotalPortfolioValue > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentTotalPortfolioValue < 0" class="text-destructive">
                                        lower than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentTotalPortfolioValue === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Current Market Value -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-dollar"></i>
                                    <p>Current Market Value</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentMarketValue">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentMarketValueFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentMarketValueFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentMarketValue" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Current Market Value is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentMarketValue" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentMarketValue"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentMarketValue > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentMarketValue < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentMarketValueFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentMarketValue > 0" class="text-restorative"> higher than </span>
                                    <span v-if="comparePortfolioModal.difference.currentMarketValue < 0" class="text-destructive"> lower than </span>
                                    <span v-if="comparePortfolioModal.difference.currentMarketValue === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Gain/Loss ($) -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-arrow-2-both-direction-vertical"></i>
                                    <p>Gain/Loss ($)</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentGainLossInDollar">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentGainLossInDollarFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentGainLossInDollarFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentGainLossInDollar" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Gain/Loss ($) is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentGainLossInDollar" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentGainLossInDollar"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentGainLossInDollar > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentGainLossInDollar < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentGainLossInDollarFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentGainLossInDollar > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentGainLossInDollar < 0" class="text-destructive">
                                        lower than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentGainLossInDollar === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Gain/Loss (%) -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-arrow-2-both-direction-vertical"></i>
                                    <p>Gain/Loss (%)</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentGainLossInPercentage">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentGainLossInPercentageFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentGainLossInPercentageFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentGainLossInPercentage" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Gain/Loss (%) is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentGainLossInPercentage" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentGainLossInPercentage"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentGainLossInPercentage > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentGainLossInPercentage < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentGainLossInPercentageFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentGainLossInPercentage > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentGainLossInPercentage < 0" class="text-destructive">
                                        lower than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentGainLossInPercentage === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Cash Available -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-money-1-bills"></i>
                                    <p>Cash Available</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentCashAvailable">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentCashAvailableFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentCashAvailableFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentCashAvailable" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Cash Available is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentCashAvailable" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentCashAvailable"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentCashAvailable > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentCashAvailable < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentCashAvailableFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentCashAvailable > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentCashAvailable < 0" class="text-destructive">
                                        lower than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentCashAvailable === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Monthly Income -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-calendar"></i>
                                    <p>Monthly Income</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentMonthlyIncome">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentMonthlyIncomeFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentMonthlyIncomeFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentMonthlyIncome" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Monthly Income is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentMonthlyIncome" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentMonthlyIncome"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentMonthlyIncome > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentMonthlyIncome < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentMonthlyIncomeFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentMonthlyIncome > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentMonthlyIncome < 0" class="text-destructive">
                                        lower than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentMonthlyIncome === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Annual Income -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-calendar"></i>
                                    <p>Annual Income</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentAnnualIncome">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentAnnualIncomeFormatted }}</p>
                                    <p v-if="portfolio.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ portfolio.secondaryCurrency.currentAnnualIncomeFormatted }}
                                    </p>
                                </div>
                                <div v-if="!portfolio.currentAnnualIncome" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Annual Income is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentAnnualIncome" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentAnnualIncome"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentAnnualIncome > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentAnnualIncome < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentAnnualIncomeFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentAnnualIncome > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentAnnualIncome < 0" class="text-destructive"> lower than </span>
                                    <span v-if="comparePortfolioModal.difference.currentAnnualIncome === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Average Dividend Yield -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-plant-pot-1"></i>
                                    <p>Average Dividend Yield</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.currentAverageDividendYield">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.currentAverageDividendYieldFormatted }}</p>
                                </div>
                                <div v-if="!portfolio.currentAverageDividendYield" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Average Dividend Yield is
                                    <GainLossSymbol :value="comparePortfolioModal.difference.currentAverageDividendYield" />
                                    <span
                                        v-if="comparePortfolioModal.difference.currentAverageDividendYield"
                                        class="font-mono font-medium tracking-widest"
                                        :class="{
                                            'text-restorative': comparePortfolioModal.difference.currentAverageDividendYield > 0,
                                            'text-destructive': comparePortfolioModal.difference.currentAverageDividendYield < 0,
                                        }"
                                    >
                                        {{ comparePortfolioModal.difference.currentAverageDividendYieldFormatted }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentAverageDividendYield > 0" class="text-restorative">
                                        higher than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentAverageDividendYield < 0" class="text-destructive">
                                        lower than
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.currentAverageDividendYield === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>

                        <!-- Holdings Amount -->
                        <TableRow>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <i class="lni lni-hands-holding"></i>
                                    <p>Holdings Amount</p>
                                </div>
                            </TableCell>
                            <TableCell v-for="portfolio in comparePortfolioModal.portfolios" class="text-right">
                                <div v-if="portfolio.holdings.length">
                                    <p class="font-mono font-medium tracking-widest">{{ portfolio.holdings.length }}</p>
                                </div>
                                <div v-if="!portfolio.holdings.length" class="text-text-muted">-</div>
                            </TableCell>
                            <TableCell></TableCell>
                            <TableCell>
                                <p class="flex items-center gap-2">
                                    {{ comparePortfolioModal.portfolios[0].name }}'s Holdings Amount is
                                    <span class="font-mono font-medium tracking-widest">
                                        {{ comparePortfolioModal.difference.holdingsCount }}
                                    </span>
                                    <span v-if="comparePortfolioModal.difference.holdingsCount > 0" class="text-restorative"> more than </span>
                                    <span v-if="comparePortfolioModal.difference.holdingsCount < 0" class="text-destructive"> fewer than </span>
                                    <span v-if="comparePortfolioModal.difference.holdingsCount === 0"> identical as </span>
                                    {{ comparePortfolioModal.portfolios[1].name }}
                                </p>
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>

                <p
                    v-if="!comparePortfolioModal.portfolios.length"
                    class="flex h-full items-center justify-center pt-38 pb-34 text-sm text-text-muted"
                >
                    Loading...
                </p>

                <div class="col-span-full flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleComparePortfolioModal">Close</Button>
                </div>
            </div>
        </Modal>

        <!-- Switch Portfolio Mode Alert Dialog -->
        <AlertDialog :show="switchPortfolioModeAlertDialog.isOpen">
            <template #title>
                <p>Confirmation Required</p>
            </template>
            <template #content>
                <div class="space-y-2">
                    <p>
                        Are you sure you want to switch to <b>{{ selectedPortfolio.mode === 'simple' ? 'Advanced' : 'Simple' }} Mode</b>?
                    </p>
                    <p class="font-bold">This will wipe out all of your portfolio data.</p>
                </div>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleSwitchPortfolioModeAlertDialog">Cancel</Button>
                <Button size="normal" color="destructive" @click="switchPortfolioMode(selectedPortfolio.id)"
                    >Switch to {{ selectedPortfolio.mode === 'simple' ? 'Advanced' : 'Simple' }} Mode
                </Button>
            </template>
        </AlertDialog>

        <!-- Wipe Portfolio Alert Dialog -->
        <AlertDialog :show="wipePortfolioAlertDialog.isOpen">
            <template #title>
                <p>Confirmation Required</p>
            </template>
            <template #content>
                <p>
                    Are you sure you want to wipe <b>{{ selectedPortfolio.name }}</b
                    >?
                </p>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleConfirmWipePortfolioAlertDialog">Cancel</Button>
                <Button size="normal" color="destructive" @click="wipePortfolio(selectedPortfolio.id)">Wipe {{ selectedPortfolio.name }}</Button>
            </template>
        </AlertDialog>

        <!-- Delete Portfolio Alert Dialog -->
        <AlertDialog :show="deletePortfolioAlertDialog.isOpen">
            <template #title>
                <p>Confirmation Required</p>
            </template>
            <template #content>
                <p>
                    Are you sure you want to delete <b>{{ selectedPortfolio.name }}</b
                    >?
                </p>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleConfirmDeletePortfolioAlertDialog">Cancel</Button>
                <Button size="normal" color="destructive" @click="deletePortfolio(selectedPortfolio.id)">Delete {{ selectedPortfolio.name }}</Button>
            </template>
        </AlertDialog>

        <!-- Upgrade to Premium Alert Dialog -->
        <AlertDialog v-if="!isPremium" :show="upgradeToPremiumAlertDialog.isOpen">
            <template #title>
                <p>Upgrade to Premium</p>
            </template>
            <template #content>
                <div class="space-y-2">
                    <p>Free users are limited to 1 portfolio.</p>
                    <p>Upgrade to Premium for <b>unlimited portfolios</b>.</p>
                </div>
            </template>
            <template #buttons>
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleUpgradeToPremiumAlertDialog">Close</Button>
                <Link :href="route('premium.index')">
                    <Button size="normal" color="premium">Get Premium</Button>
                </Link>
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
import GainLossSymbol from '@/components/ui/GainLossSymbol.vue';
import Modal from '@/components/ui/Modal.vue';
import Select from '@/components/ui/Select.vue';
import Switch from '@/components/ui/Switch.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextInput from '@/components/ui/TextInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { debounce } from 'chart.js/helpers';
import { toast } from 'vue-sonner';

export default {
    components: {
        GainLossSymbol,
        Select,
        TableRow,
        TableHeader,
        TableHeadRow,
        Table,
        TableCell,
        AlertDialog,
        Card,
        Switch,
        Button,
        DivThatLooksLikeTextInput,
        TextInput,
        Modal,
        TextLabel,
        Link,
        Dropdown,
    },

    props: {
        portfolios: Object,
        selectedPortfolio: Object,
        upcomingDividends: Object,
    },

    data() {
        return {
            upgradeToPremiumAlertDialog: {
                isOpen: false,
            },

            // Add Portfolio
            addPortfolioForm: {
                isOpen: false,
                form: useForm({
                    name: null,
                    mode: 'simple',
                }),
            },

            // Edit Portfolio
            editPortfolioModal: {
                isOpen: false,
                form: useForm({
                    portfolioUid: this.selectedPortfolio.uid,
                    name: this.selectedPortfolio.name,
                    isDefault: this.selectedPortfolio.isDefault,
                    isPublic: this.selectedPortfolio.isPublic,
                }),
            },

            // Compare Portfolio
            comparePortfolioModal: {
                isOpen: false,
                portfolio1Uid: null,
                portfolio2Uid: null,
                portfolios: [],
                difference: null,
            },

            // Switch Portfolio Mode
            switchPortfolioModeAlertDialog: {
                isOpen: false,
            },

            // Wipe Portfolio
            wipePortfolioAlertDialog: {
                isOpen: false,
            },

            // Delete Portfolio
            deletePortfolioAlertDialog: {
                isOpen: false,
            },
        };
    },

    computed: {
        isPremium() {
            return usePage().props.auth?.user.isPremium;
        },

        selectedTab() {
            const url = new URL(this.$page.url, window.location.origin);
            const tab = url.searchParams.get('tab');
            const validTabs = ['overview', 'upcoming-dividends', 'dividends-received', 'cash-movements'];

            return validTabs.includes(tab) ? tab : 'overview';
        },
    },

    watch: {
        'editPortfolioModal.isOpen'(val) {
            if (val) {
                this.editPortfolioModal.form.name = this.selectedPortfolio.name;
            }
        },

        'comparePortfolioModal.portfolio2Uid'(newVal, oldVal) {
            // Prevent triggering if the modal is not open or value hasn't really changed
            if (newVal && newVal !== oldVal && this.comparePortfolioModal.isOpen) {
                this.debouncedGetPortfoliosToCompare();
            }
        },

        selectedPortfolio() {
            this.debouncedGetPortfoliosToCompare();
        },
    },

    created() {
        this.debouncedGetPortfoliosToCompare = debounce(this.getPortfoliosToCompare, 250);
    },

    methods: {
        // General
        copyPublicPortfolioLink() {
            const text = this.selectedPortfolio.publicUrl;
            navigator.clipboard
                .writeText(text)
                .then(() => {
                    toast.success('Link copied to clipboard!');
                })
                .catch(() => {
                    toast.error('Failed to copy link');
                });
        },

        // Add Portfolio
        toggleAddPortfolioModal() {
            if (!this.isPremium) {
                this.toggleUpgradeToPremiumAlertDialog();
                return;
            }

            this.addPortfolioForm.isOpen = !this.addPortfolioForm.isOpen;

            if (this.addPortfolioForm.isOpen) {
                this.$nextTick(() => {
                    this.$refs.addPortfolioFormName?.focus?.();
                });
            }
        },

        updateSelectedMode(mode) {
            this.addPortfolioForm.form.mode = mode;
        },

        resetAddPortfolioForm() {
            this.addPortfolioForm.form.name = null;
            this.addPortfolioForm.form.mode = null;
        },

        storePortfolio() {
            const form = this.addPortfolioForm.form;

            if (!form.name || !form.mode) {
                toast.error('Please complete all required fields');
                return;
            }

            form.post(route('portfolios.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleAddPortfolioModal();
                    this.resetAddPortfolioForm();

                    toast.success('Portfolio created');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Edit Portfolio
        toggleEditPortfolioModal() {
            this.editPortfolioModal.isOpen = !this.editPortfolioModal.isOpen;
        },

        resetEditPortfolioForm() {
            this.editPortfolioModal.form.name = null;
            this.editPortfolioModal.form.mode = null;
        },

        updatePortfolio() {
            const form = this.editPortfolioModal.form;

            if (!form.name) {
                toast.error('Please complete all required fields');
                return;
            }

            form.patch(route('portfolios.update', { portfolioUid: this.selectedPortfolio.uid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleEditPortfolioModal();
                    this.resetEditPortfolioForm();

                    toast.success('Portfolio updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        // Compare Portfolio
        toggleComparePortfolioModal() {
            if (!this.isPremium) {
                this.toggleUpgradeToPremiumAlertDialog();
                return;
            }

            this.comparePortfolioModal.isOpen = !this.comparePortfolioModal.isOpen;

            if (this.comparePortfolioModal.isOpen) {
                this.debouncedGetPortfoliosToCompare();
            }
        },

        getPortfoliosToCompare() {
            if (!this.comparePortfolioModal.isOpen) {
                return;
            }
            if (this.portfolios.length === 1) {
                return;
            }

            // If Portfolio 1 has no value, assign it
            if (!this.comparePortfolioModal.portfolio1Uid) {
                this.comparePortfolioModal.portfolio1Uid = this.selectedPortfolio.uid;
            }

            // If Portfolio 2 has no value, assign it
            if (!this.comparePortfolioModal.portfolio2Uid) {
                this.comparePortfolioModal.portfolio2Uid = this.portfolios.find((p) => p.uid !== this.comparePortfolioModal.portfolio1Uid)?.uid;
            }

            axios
                .get(
                    route('portfolios.get-portfolios-to-compare', {
                        portfolio1Uid: this.comparePortfolioModal.portfolio1Uid,
                        portfolio2Uid: this.comparePortfolioModal.portfolio2Uid,
                    }),
                )
                .then((response) => {
                    this.comparePortfolioModal.portfolios = response.data.portfolios;
                    this.comparePortfolioModal.difference = response.data.difference;
                })
                .catch((error) => {
                    if (error.response?.data?.errors) {
                        Object.values(error.response.data.errors).forEach((message) => {
                            toast.error(message);
                        });
                    } else {
                        toast.error('An unexpected error occurred.');
                    }
                });
        },

        // Switch Portfolio Mode
        switchPortfolioMode() {
            router.patch(
                route('portfolios.switch-mode', { portfolioUid: this.selectedPortfolio.uid }),
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.toggleSwitchPortfolioModeAlertDialog();
                        this.toggleEditPortfolioModal();
                        this.resetEditPortfolioForm();

                        toast.success('Portfolio mode switched');
                    },
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                },
            );
        },

        toggleSwitchPortfolioModeAlertDialog() {
            this.switchPortfolioModeAlertDialog.isOpen = !this.switchPortfolioModeAlertDialog.isOpen;
        },

        // Wipe Portfolio
        wipePortfolio() {
            router.patch(
                route('portfolios.wipe', { portfolioUid: this.selectedPortfolio.uid }),
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.toggleConfirmWipePortfolioAlertDialog();
                        this.toggleEditPortfolioModal();
                        this.resetEditPortfolioForm();

                        toast.success('Portfolio wiped');
                    },
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                },
            );
        },

        toggleConfirmWipePortfolioAlertDialog() {
            this.wipePortfolioAlertDialog.isOpen = !this.wipePortfolioAlertDialog.isOpen;
        },

        // Delete Portfolio
        deletePortfolio() {
            router.delete(route('portfolios.delete', { portfolioUid: this.selectedPortfolio.uid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleConfirmDeletePortfolioAlertDialog();
                    this.toggleEditPortfolioModal();
                    this.resetEditPortfolioForm();

                    toast.success('Portfolio deleted');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        toggleConfirmDeletePortfolioAlertDialog() {
            this.deletePortfolioAlertDialog.isOpen = !this.deletePortfolioAlertDialog.isOpen;
        },

        // Upgrade to Premium ------------------------------------------------------------------------------------------------------------------------
        toggleUpgradeToPremiumAlertDialog() {
            this.upgradeToPremiumAlertDialog.isOpen = !this.upgradeToPremiumAlertDialog.isOpen;
        },
    },
};
</script>
