<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-xl font-medium text-text">Holdings ({{ localHoldings.length }})</p>
        </div>

    </div>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import DivThatLooksLikeTextInput from '@/components/ui/DivThatLooksLikeTextInput.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import InfoTooltip from '@/components/ui/InfoTooltip.vue';
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
import { hexToRgba } from '@/utils/colorHelper';
import { formatNumberToDollar, formatNumberWithSeparators, fx } from '@/utils/numberHelper';
import schwabLogoUrl from '@img/brokerages/schwab.png';
import robinhoodLogoUrl from '@img/brokerages/robinhood.png';
import vanguardLogoUrl from '@img/brokerages/vanguard.png';
import interactiveBrokersLogoUrl from '@img/brokerages/interactive_brokers.png';
import fidelityLogoUrl from '@img/brokerages/fidelity.png';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue-sonner';
import TextArea from '@/components/ui/TextArea.vue';
import InputError from '@/components/ui/InputError.vue';
import AlertDialog from '@/components/ui/AlertDialog.vue';

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
                    portfolioUid: this.portfolio.uid,
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
                    price: null,
                    feesAndCommissions: null,
                    tradeDate: null,
                    note: null,
                }),
            },

            // Edit Transaction
            editTransactionModal: {
                isOpen: false,
                form: useForm({
                    transactionId: null,
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
                    transactionId: null,
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
                    toast.error(error)
                });
        },

        expandHolding(holdingUid) {
            if (holdingUid === this.expandedHoldingUid) {
                this.collapseHolding();
            } else {
                this.expandedHoldingUid = holdingUid;

                // Scroll to selected holding
                setTimeout(() =>  {
                    this.$nextTick(() => {
                        const ref = this.$refs[`holding-row-${holdingUid}`];
                        const target = Array.isArray(ref) ? ref[0]?.$el || ref[0] : ref?.$el || ref;

                        if (target && typeof target.scrollIntoView === 'function') {
                            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                }, 300)
            }
        },

        collapseHolding() {
            this.expandedHoldingUid = null;

            // Scroll to mid page(near this years dividends)
            this.$nextTick(() => {
                const el = document.getElementById('this-years-dividends');
                el?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
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
                    const brokerage = this.importPortfolioModal.form.brokerage

                    if (brokerage === 'schwab') {
                        toast.success('Portfolio imported')
                    }

                    if (brokerage === 'fidelity') {
                        toast.info("Import failed, try again later")
                    }

                    if (brokerage === 'robinhood') {
                        toast.info("Import failed, try again later")
                    }

                    if (brokerage === 'vanguard') {
                        toast.info("Import failed, try again later")
                    }

                    if (brokerage === 'interactive_brokers') {
                        toast.info("Import failed, try again later")
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
                this.$nextTick(() => {
                    this.$refs.addTransactionSearch?.focus?.();
                    this.$refs.addTransactionSearch?.click?.();
                });
            }
        },

        storeTransaction() {
            const form = this.addTransactionModal.form;

            if (!form.selectedSecurity || !form.type || !form.shares || !form.price || !form.tradeDate) {
                toast.error('Please complete all required fields')
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
                this.editTransactionModal.form.transactionId = transaction.id;
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
                toast.error('Please complete all required fields')
                return;
            }

            const transactionId = this.editTransactionModal.form.transactionId;

            form.patch(route('portfolios.transactions.update', { transactionId: transactionId }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleEditTransactionModal();

                    this.resetEditTransactionForm();

                    toast.success('Transaction updated');
                },
                onError: (errors) => {
                    this.toggleEditTransactionModal()
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
                this.editTransactionNote.form.transactionId = transaction.id;
                this.editTransactionNote.form.note = transaction.note;
            }

            this.editTransactionNote.isOpen = !this.editTransactionNote.isOpen;
        },

        updateTransactionNote() {
            const transactionId = this.editTransactionNote.form.transactionId;

            this.editTransactionNote.form.patch(route('portfolios.transactions.update-note', { transactionId: transactionId }), {
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
            this.editTransactionNote.form.transactionId = null;
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
            const transactionId = this.deleteTransactionAlertDialog.selectedTransaction.id;

            router.delete(route('portfolios.transactions.delete', { transactionId: transactionId }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleDeleteTransactionAlertDialog()
                    this.resetDeleteTransactionAlertDialog();

                    toast.success('Transaction deleted');
                },
                onFinish: () => {
                    if (!this.localHoldings.find((localHolding) => localHolding.uid === this.expandedHoldingUid)) {
                        this.collapseHolding()
                    }
                },
                onError: (errors) => {
                    this.toggleDeleteTransactionAlertDialog()
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
            this.$nextTick(() => {
                const listItems = this.$refs.securityListItems;
                const item = listItems?.[this.addTransactionModal.highlightedIndex];
                item?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            });
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
                toast.error(error)
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
