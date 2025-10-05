<template>
    <div class="col-span-full space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="lni lni-dollar !text-xl"></i>
                <p class="text-xl font-medium text-text">Cash Movements</p>
            </div>

            <div class="flex items-center justify-between gap-4">
                <Button size="normal" color="primary" @click="toggleManageCashMovementModal()">
                    <i class="lni lni-plus"></i>
                    <span class="hidden md:block">Add Cash Movement</span>
                </Button>
            </div>
        </div>

        <Card
            :class="{
                'max-h-[60rem]': !showAllCashMovements,
                'h-[55rem]': !filteredCashMovements.length,
            }"
        >
            <div class="flex h-full flex-col gap-6 sm:gap-8">
                <!-- Filters -->
                <div class="flex w-full flex-col justify-end gap-4 whitespace-nowrap md:flex-row">
                    <!-- Types with Data -->
                    <Dropdown :closeContentOnClick="false" class="w-full md:w-auto">
                        <template #trigger>
                            <Button size="normal" color="primaryForeground" class="w-full font-mono sm:w-44">
                                {{
                                    excludedTypes.length === 0
                                        ? 'All Types'
                                        : excludedTypes.length === cashMovements.typesWithData.length
                                          ? 'None Selected'
                                          : `${cashMovements.typesWithData.length - excludedTypes.length} Selected`
                                }}
                                <i class="lni lni-chevron-down"></i>
                            </Button>
                        </template>

                        <template #content>
                            <ul class="max-h-[30rem] w-full overflow-y-auto">
                                <li v-for="type in cashMovements.typesWithData" :key="`type-filter-${type}`" class="px-4 hover:bg-hover">
                                    <label class="flex cursor-pointer items-center gap-2 whitespace-nowrap">
                                        <TextInput
                                            size="normal"
                                            color="inputForeground"
                                            type="checkbox"
                                            :checked="!excludedTypes.includes(type)"
                                            @change="toggleType(type)"
                                        />
                                        {{ type }}
                                    </label>
                                </li>
                            </ul>
                        </template>
                    </Dropdown>

                    <div class="flex w-full flex-row gap-4 md:w-auto">
                        <!-- Year -->
                        <Dropdown>
                            <template #trigger>
                                <Button size="normal" color="primaryForeground" class="font-mono">
                                    {{ selectedYear }}
                                    <i class="lni lni-chevron-down"></i>
                                </Button>
                            </template>

                            <template #content>
                                <ul class="max-h-80 w-full overflow-y-auto">
                                    <li
                                        v-for="yearWithData in cashMovements.yearsWithData"
                                        :key="`year-filter-${yearWithData}`"
                                        class="cursor-pointer px-4 py-2 whitespace-nowrap hover:bg-hover"
                                        @click="selectedYear = yearWithData"
                                    >
                                        {{ yearWithData }}
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>

                        <!-- Quarters -->
                        <Dropdown :closeContentOnClick="false" class="flex-grow">
                            <template #trigger>
                                <Button size="normal" color="primaryForeground" class="w-full font-mono sm:w-40">
                                    {{
                                        selectedQuarters.length === 4
                                            ? 'All Quarters'
                                            : selectedQuarters
                                                  .sort((a, b) => a - b)
                                                  .map((q) => 'Q' + q)
                                                  .join(', ')
                                    }}
                                    <i class="lni lni-chevron-down"></i>
                                </Button>
                            </template>

                            <template #content>
                                <ul class="max-h-80 w-full overflow-y-auto">
                                    <li v-for="q in [1, 2, 3, 4]" :key="`quarter-filter-${q}`" class="px-4 hover:bg-hover">
                                        <label class="flex cursor-pointer items-center gap-2">
                                            <TextInput
                                                size="normal"
                                                color="inputForeground"
                                                type="checkbox"
                                                :checked="selectedQuarters.includes(q)"
                                                @change="toggleQuarter(q)"
                                            />
                                            Q{{ q }}
                                        </label>
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Table -->
                <Table v-if="filteredCashMovements.length" size="comfortable" class="-mx-4 flex-grow">
                    <template #head>
                        <TableHeadRow>
                            <!-- Date -->
                            <TableHeader class="w-44">
                                <div class="flex items-center gap-2" @click="sort('date')">
                                    <span>Date</span>
                                    <SortIcon v-if="filters.sortBy === 'date'" :direction="filters.sortDirection" />
                                </div>
                            </TableHeader>

                            <!-- Type -->
                            <TableHeader class="w-52">
                                <div class="flex items-center gap-2" @click="sort('type')">
                                    <span>Type</span>
                                    <SortIcon v-if="filters.sortBy === 'type'" :direction="filters.sortDirection" />
                                </div>
                            </TableHeader>

                            <!-- Description -->
                            <TableHeader>
                                <div class="flex items-center gap-2" @click="sort('description')">
                                    <span>Description</span>
                                    <SortIcon v-if="filters.sortBy === 'description'" :direction="filters.sortDirection" />
                                </div>
                            </TableHeader>

                            <!-- Amount -->
                            <TableHeader class="w-44">
                                <div class="flex items-center justify-end gap-2" @click="sort('amount')">
                                    <span>Amount</span>
                                    <SortIcon v-if="filters.sortBy === 'amount'" :direction="filters.sortDirection" />
                                </div>
                            </TableHeader>

                            <!-- Actions -->
                            <TableHeader class="w-1"></TableHeader>
                        </TableHeadRow>
                    </template>

                    <template #body>
                        <TableRow v-for="(cashMovement, index) in filteredCashMovements" :key="`table-cash-flow-row-${index}`">
                            <!-- Date -->
                            <TableCell class="font-mono font-medium tracking-widest">{{ cashMovement.date }}</TableCell>

                            <!-- Type -->
                            <TableCell>{{ cashMovement.typeFormatted }}</TableCell>

                            <!-- Description -->
                            <TableCell>{{ cashMovement.description }}</TableCell>

                            <!-- Amount -->
                            <TableCell class="text-end">
                                <div v-if="cashMovement.amount">
                                    <p class="font-mono font-medium tracking-widest"
                                       :class="{
                                          'text-restorative' : cashMovement.amount > 0,
                                          'text-destructive' : cashMovement.amount < 0,
                                       }"
                                    >{{ cashMovement.amountFormatted }}</p>
                                    <p v-if="cashMovement.secondaryCurrency" class="font-mono font-medium tracking-widest text-text-muted">
                                        {{ cashMovement.secondaryCurrency.amountFormatted }}
                                    </p>
                                </div>
                                <div v-if="!cashMovement.amount" class="text-text-muted">-</div>
                            </TableCell>

                            <!-- Actions -->
                            <TableCell class="text-end">
                                <div class="flex items-center justify-end gap-2">
                                    <Button
                                        size="small"
                                        :color="cashMovement.note ? 'primaryForeground' : 'transparent'"
                                        :onhover="cashMovement.note ? 'primaryForeground' : 'primaryForeground'"
                                        :class="cashMovement.note ? 'text-text' : 'text-text-muted hover:text-text'"
                                        v-tooltip="cashMovement.note"
                                        @click="toggleEditCashMovementNoteModal(cashMovement)"
                                    >
                                        <i class="lni lni-notebook-1"></i>
                                    </Button>

                                    <Button
                                        size="small"
                                        color="transparent"
                                        onhover="primaryForeground"
                                        class="text-text-muted hover:text-text"
                                        @click="toggleManageCashMovementModal(cashMovement)"
                                    >
                                        <i class="lni lni-pencil-1"></i>
                                    </Button>

                                    <Button
                                        size="small"
                                        color="transparent"
                                        onhover="destructive"
                                        class="!text-text-muted hover:!text-text"
                                        @click="toggleDeleteCashMovementAlertDialog(cashMovement)"
                                    >
                                        <i class="lni lni-trash-1"></i>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>

                <!-- Show All/Less Movements-->
                <div v-if="filteredCashMovements.length && filteredCashMovements.length < localCashMovements.length" class="flex justify-center">
                    <Button size="small" color="transparent" @click="showAllCashMovements = !showAllCashMovements">
                        {{ showAllCashMovements ? 'Show Less' : 'Show All Movements' }}
                    </Button>
                </div>

                <!-- No cash movements found -->
                <div v-if="!filteredCashMovements.length" class="flex h-full flex-col items-center justify-center space-y-4">
                    <p class="flex items-center justify-center text-sm text-text-muted">
                        Once transactions like buys, sells, deposits, or dividends are recorded, they’ll appear here.
                    </p>
                </div>
            </div>
        </Card>
    </div>

    <!-- Manage Cash Movement -->
    <Modal v-model:isOpen="manageCashMovementModal.isOpen" size="medium">
        <form @submit.prevent="updateOrCreateCashMovement">
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <p class="truncate font-medium whitespace-nowrap text-text">Add a cash movement</p>

                    <div
                        class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                        @click="toggleManageCashMovementModal()"
                    >
                        <i class="lni lni-xmark"></i>
                    </div>
                </div>

                <p v-if="manageCashMovementModal.form.isImported" class="mt-4 text-text-muted text-sm">
                    This record was imported from your brokerage. Editing it may impact your portfolio calculations or data accuracy.
                </p>

                <!-- Date -->
                <div class="flex flex-col gap-2">
                    <TextLabel>Date</TextLabel>
                    <VueDatePicker
                        v-model="manageCashMovementModal.form.date"
                        :enable-time-picker="false"
                        :clearable="false"
                        :auto-apply="true"
                        model-type="yyyy-MM-dd"
                    >
                        <template #trigger="{ toggle, attrs }">
                            <Button size="normal" color="inputForeground" @click="toggle" class="w-full !justify-start font-mono tracking-widest">
                                <i class="lni lni-calendar-days !text-lg"></i>
                                {{ ymdToReadableDate(manageCashMovementModal.form.date) }}
                            </Button>
                        </template>
                    </VueDatePicker>
                    <InputError :error="manageCashMovementModal.form.errors.date" />
                </div>

                <!-- Description -->
                <div class="flex flex-grow flex-col gap-2">
                    <TextLabel>Description</TextLabel>
                    <TextInput
                        ref="manageCashMovementFormDescription"
                        color="inputForeground"
                        v-model="manageCashMovementModal.form.description"
                        size="normal"
                        placeholder="Monthly Deposit"
                        type="text"
                    />
                    <InputError :error="manageCashMovementModal.form.errors.description" />
                </div>

                <!-- Amount -->
                <div class="flex flex-grow flex-col gap-2">
                    <TextLabel>Amount</TextLabel>
                    <TextInput color="inputForeground" v-model="manageCashMovementModal.form.amount" size="normal" placeholder="$25" type="text" />
                    <InputError :error="manageCashMovementModal.form.errors.amount" />
                </div>

                <!-- Note -->
                <div class="col-span-full flex flex-col gap-2">
                    <TextLabel>Note</TextLabel>
                    <TextArea color="inputForeground" v-model="manageCashMovementModal.form.note" rows="5" placeholder="Enter your note here">
                    </TextArea>
                </div>

                <!-- Type -->
                <div class="flex items-center gap-6 sm:gap-8">
                    <RadioInput
                        v-model="manageCashMovementModal.form.type"
                        id="type-option-1"
                        name="type"
                        value="deposit"
                        label="Deposit"
                        tabindex="-1"
                    />
                    <RadioInput
                        v-model="manageCashMovementModal.form.type"
                        id="type-option-2"
                        name="type"
                        value="withdrawal"
                        label="Withdrawal"
                        tabindex="-1"
                    />
                </div>

                <!-- Cancel | Save -->
                <div class="flex items-center justify-end gap-2">
                    <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleManageCashMovementModal()" tabindex="-1">
                        Cancel
                    </Button>
                    <Button size="normal" color="primaryForeground" type="submit">
                        <i class="lni lni-floppy-disk-1"></i>
                        Save
                    </Button>
                </div>
            </div>
        </form>
    </Modal>

    <!-- Cash Movement Note -->
    <Modal v-model:isOpen="editCashMovementNoteModal.isOpen" size="large">
        <form @submit.prevent="updateCashMovementNote" class="grid grid-cols-1 gap-6 sm:gap-8">
            <div class="col-span-full flex items-center justify-between">
                <p class="font-medium text-text">Edit Note</p>

                <div
                    class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-lg hover:bg-hover"
                    @click="toggleEditCashMovementNoteModal"
                >
                    <i class="lni lni-xmark"></i>
                </div>
            </div>

            <!-- Note -->
            <div class="flex flex-col gap-2">
                <TextArea ref="editCashMovementFormNote" color="inputForeground" v-model="editCashMovementNoteModal.form.note" rows="5" placeholder="Enter your note here"> </TextArea>
            </div>

            <!-- Cancel - Save -->
            <div class="col-span-full flex items-center justify-end gap-2">
                <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleEditCashMovementNoteModal">Cancel</Button>
                <Button size="normal" color="primaryForeground" type="submit">
                    <i class="lni lni-floppy-disk-1"></i>
                    Save Note
                </Button>
            </div>
        </form>
    </Modal>

    <!-- Delete Cash Movement Alert Dialog -->
    <AlertDialog :show="deleteCashMovementAlertDialog.isOpen">
        <template #title>
            <p>Confirmation Required</p>
        </template>
        <template #content>
            <p>Are you sure you want to delete this cash movement?</p>
            <p v-if="deleteCashMovementAlertDialog.selectedCashMovement.isImported" class="mt-4 text-text-muted">
                This record was imported from your brokerage. Removing it may impact your portfolio calculations or data accuracy.
            </p>
        </template>
        <template #buttons>
            <Button size="normal" color="transparent" onhover="primaryForeground" @click="toggleDeleteCashMovementAlertDialog">Cancel</Button>
            <Button size="normal" color="destructive" @click="deleteCashMovement">Delete Cash Movement</Button>
        </template>
    </AlertDialog>
</template>

<script>
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import Dropdown from '@/components/ui/Dropdown.vue';
import SegmentedControls from '@/components/ui/SegmentedControls.vue';
import SortIcon from '@/components/ui/SortIcon.vue';
import Table from '@/components/ui/Table.vue';
import TableCell from '@/components/ui/TableCell.vue';
import TableHeader from '@/components/ui/TableHeader.vue';
import TableHeadRow from '@/components/ui/TableHeadRow.vue';
import TableRow from '@/components/ui/TableRow.vue';
import TextInput from '@/components/ui/TextInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { toast } from 'vue-sonner';
import Select from '@/components/ui/Select.vue';
import InputError from '@/components/ui/InputError.vue';
import RadioInput from '@/components/ui/RadioInput.vue';
import TextLabel from '@/components/ui/TextLabel.vue';
import Modal from '@/components/ui/Modal.vue';
import { ymdToReadableDate } from '@/utils/dateHelper.js';
import { todayInYmd } from '@/utils/dateHelper.js';
import TextArea from '@/components/ui/TextArea.vue';
import AlertDialog from '@/components/ui/AlertDialog.vue';

export default {
    components: {
        AlertDialog,
        TextArea,
        Modal,
        TextLabel,
        RadioInput,
        InputError,
        Select,
        TextInput,
        SegmentedControls,
        Button,
        SortIcon,
        Table,
        TableHeadRow,
        TableRow,
        TableHeader,
        TableCell,
        Card,
        Dropdown,
    },

    props: {
        portfolio: Object,
        cashMovements: Object,
    },

    data() {
        return {
            localCashMovements: this.cashMovements.data,
            showAllCashMovements: false,
            filters: {
                sortBy: 'ticker',
                sortDirection: 'asc',
            },
            excludedTypes: [],
            selectedYear: this.cashMovements.yearsWithData?.[0] || new Date().getFullYear(),
            selectedQuarters: [1, 2, 3, 4],

            // Manage Cash Movement
            manageCashMovementModal: {
                isOpen: false,
                form: useForm({
                    cashMovementUid: null,
                    date: null,
                    type: 'deposit',
                    description: null,
                    amount: null,
                    note: null,
                    isImported: false,
                }),
            },

            // Edit Cash Movement Note
            editCashMovementNoteModal: {
                isOpen: false,
                form: useForm({
                    cashMovementUid: null,
                    note: null,
                }),
            },

            // Delete Cash Movement Alert Dialog
            deleteCashMovementAlertDialog: {
                isOpen: false,
                selectedCashMovement: null,
            },
        };
    },

    computed: {
        filteredCashMovements() {
            let results = this.localCashMovements;

            if (this.selectedYear) {
                results = results.filter((cashMovement) => new Date(cashMovement.date).getFullYear() === this.selectedYear);
            }

            if (this.selectedQuarters.length) {
                results = results.filter((cashMovement) => {
                    const month = parseInt(cashMovement.date.split('-')[1], 10); // 1 = Jan, 12 = Dec
                    const quarter = Math.floor((month - 1) / 3) + 1;
                    return this.selectedQuarters.includes(quarter);
                });
            }

            results = results.filter((cashMovement) => !this.excludedTypes.includes(cashMovement.typeFormatted));

            return this.showAllCashMovements ? results : results.slice(0, 10);
        },
    },

    watch: {
        cashMovements() {
            this.localCashMovements = this.cashMovements.data;
        },
    },

    methods: {
        ymdToReadableDate,
        todayInYmd,

        toggleType(type) {
            const index = this.excludedTypes.indexOf(type);
            if (index !== -1) {
                this.excludedTypes.splice(index, 1);
            } else {
                this.excludedTypes.push(type);
            }
        },

        toggleQuarter(q) {
            const index = this.selectedQuarters.indexOf(q);
            if (index !== -1) {
                this.selectedQuarters.splice(index, 1);
            } else {
                this.selectedQuarters.push(q);
            }
        },

        async sort(sortBy) {
            this.filters.sortBy = sortBy;
            this.filters.sortDirection = this.filters.sortDirection === 'asc' ? 'desc' : 'asc';

            axios
                .post(route('portfolios.sort-cash-movements'), {
                    cashMovements: this.cashMovements.data,
                    ...(this.filters.sortBy && { sortBy: this.filters.sortBy }),
                    ...(this.filters.sortDirection && { sortDirection: this.filters.sortDirection }),
                })
                .then((response) => {
                    this.localCashMovements = response.data;
                })
                .catch((error) => {
                    toast.error(error);
                });
        },

        // Manage Cash Movements ---------------------------------------------------------------------------------------------------------------------
        resetManageCashMovementModal() {
            this.manageCashMovementModal.form.uid = null;
            this.manageCashMovementModal.form.date = this.todayInYmd();
            this.manageCashMovementModal.form.type = 'deposit';
            this.manageCashMovementModal.form.description = null;
            this.manageCashMovementModal.form.amount = null;
            this.manageCashMovementModal.form.note = null;
        },

        toggleManageCashMovementModal(cashMovement = null) {
            this.resetManageCashMovementModal();

            this.manageCashMovementModal.isOpen = !this.manageCashMovementModal.isOpen;

            // Populate form with existing data
            if (cashMovement) {
                this.manageCashMovementModal.form.cashMovementUid = cashMovement.uid;
                this.manageCashMovementModal.form.date = cashMovement.date;
                this.manageCashMovementModal.form.type = cashMovement.type;
                this.manageCashMovementModal.form.description = cashMovement.description;
                this.manageCashMovementModal.form.amount = cashMovement.amount;
                this.manageCashMovementModal.form.note = cashMovement.note;
                this.manageCashMovementModal.form.isImported = cashMovement.isImported;
            }

            if (this.manageCashMovementModal.isOpen) {
                // Autofocus on "Amount"
                this.$nextTick(() => {
                    this.$refs.manageCashMovementFormDescription?.focus?.();
                });
            }
        },

        updateOrCreateCashMovement() {
            this.manageCashMovementModal.form.portfolioUid = this.portfolio.uid;

            let action = null;
            if (!this.manageCashMovementModal.form.uid) action = 'store';
            if (this.manageCashMovementModal.form.uid) action = 'update';

            if (action === 'store') {
                this.manageCashMovementModal.form.post(route('portfolios.cash-movements.store', { portfolioUid: this.portfolio.uid }), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.toggleManageCashMovementModal();

                        toast.success('Cash Movement added');
                    },
                    onError: (errors) => {
                        Object.values(errors).forEach((message) => {
                            toast.error(message);
                        });
                    },
                });
            }

            if (action === 'update') {
                this.manageCashMovementModal.form.patch(
                    route('portfolios.cash-movements.update', {
                        portfolioUid: this.portfolio.uid,
                        cashMovementUid: this.manageCashMovementModal.form.uid,
                    }),
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.toggleManageCashMovementModal();

                            toast.success('Cash Movement updated');
                        },
                        onError: (errors) => {
                            Object.values(errors).forEach((message) => {
                                toast.error(message);
                            });
                        },
                    },
                );
            }
        },

        // Edit Cash Movement Note ---------------------------------------------------------------------------------------------------------------------
        toggleEditCashMovementNoteModal(cashMovement = null) {
            if (cashMovement) {
                this.editCashMovementNoteModal.form.cashMovementUid = cashMovement.uid;
                this.editCashMovementNoteModal.form.note = cashMovement.note;
            }

            this.editCashMovementNoteModal.isOpen = !this.editCashMovementNoteModal.isOpen;

            if (this.editCashMovementNoteModal.isOpen) {
                this.$nextTick(() => {
                    this.$refs.editCashMovementFormNote?.focus?.();
                });
            }
        },

        updateCashMovementNote() {
            this.editCashMovementNoteModal.form.patch(route('portfolios.cash-movements.update-note', {
                portfolioUid: this.portfolio.uid,
                cashMovementUid: this.editCashMovementNoteModal.form.cashMovementUid,
            }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleEditCashMovementNoteModal();

                    this.resetEditCashMovementForm();

                    toast.success('Note updated');
                },
                onError: (errors) => {
                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        resetEditCashMovementForm() {
            this.editCashMovementNoteModal.form.cashMovementUid = null;
            this.editCashMovementNoteModal.form.note = null;
        },

        // Delete Cash Movement ------------------------------------------------------------------------------------------------------------------------
        toggleDeleteCashMovementAlertDialog(cashMovement = null) {
            this.deleteCashMovementAlertDialog.isOpen = !this.deleteCashMovementAlertDialog.isOpen;

            if (cashMovement) {
                this.deleteCashMovementAlertDialog.selectedCashMovement = cashMovement;
            }
        },

        deleteCashMovement() {
            const cashMovementUid = this.deleteCashMovementAlertDialog.selectedCashMovement.uid;

            router.delete(route('portfolios.cash-movements.delete', { cashMovementUid: cashMovementUid }), {
                preserveScroll: true,
                onSuccess: () => {
                    this.toggleDeleteCashMovementAlertDialog();
                    this.resetDeleteCashMovementAlertDialog();

                    toast.success('Cash Movement deleted');
                },
                onError: (errors) => {
                    this.toggleDeleteCashMovementAlertDialog();
                    this.resetDeleteCashMovementAlertDialog();

                    Object.values(errors).forEach((message) => {
                        toast.error(message);
                    });
                },
            });
        },

        resetDeleteCashMovementAlertDialog() {
            this.deleteCashMovementAlertDialog.selectedCashMovement = null;
        },
    },
};
</script>



