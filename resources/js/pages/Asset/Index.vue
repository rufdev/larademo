<script setup lang="ts">
/* Import Components */
import { DropDownAction, EntityFilter, EntityPagination, EntityTable, ReusableAlertDialog } from '@/components/entitycomponent';
import { AutoForm, Config } from '@/components/ui/auto-form';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogDescription, DialogFooter, DialogHeader, DialogScrollContent, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

/* Import Utilities */
import { toTypedSchema } from '@vee-validate/zod';
import axios from 'axios';
import { ArrowUpDown, ChevronDown, Plus } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { h, ref } from 'vue';
import { toast } from 'vue-sonner';
import * as z from 'zod';

/* Import Table Utilities */
import { valueUpdater } from '@/components/ui/table/utils';
import { getLocalTimeZone, parseAbsolute } from "@internationalized/date";
import type { ColumnDef, ColumnFiltersState, ExpandedState, SortingState, VisibilityState } from '@tanstack/vue-table';
import {
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';


/* Import Types */
import { BreadcrumbItem } from '@/types';

const baseentityurl = '/assets';
const baseentityname = 'Asset';

/* Breadcrumbs */
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: baseentityname,
        href: baseentityurl,
    },
];

/* Define Props */
export interface Asset {
    id: number;
    category_id: string;
    location_id: string;
    manufacturer_id: string;
    assigned_to_user_id: string;
    asset_tag: string;
    name: string;
    serial_number: string;
    model_name: string;
    purchase_date: any; 
    purchase_price: number;
    status: string;
    notes: string;
}

const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
    locations: {
        type: Object,
        required: true,
    },
    manufacturers: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
});

const statusEnum = {
    Deployed: 'Deployed',
    InStorage: 'In Storage',
    Maintenance: 'Maintenance',
    Retired: 'Retired',
    Broken: 'Broken',
};


/* Define Table Columns */
const columns: ColumnDef<Asset>[] = [
    {
        id: 'select',
        header: ({ table }) =>
            h(Checkbox, {
                modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
                'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
                ariaLabel: 'Select all',
            }),
        cell: ({ row }) =>
            h(Checkbox, {
                modelValue: row.getIsSelected(),
                'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
                ariaLabel: 'Select row',
            }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }) => h('div', { class: 'break-words whitespace-normal' }, row.getValue('name')),
    },
    {
        accessorKey: 'category',
        header: 'Category',
        cell: ({ row }) => h('div', { class: 'break-words whitespace-normal' }, row.getValue('category')),
    },
    {
        accessorKey: 'location',
        header: 'Location',
        cell: ({ row }) => h('div', { class: 'break-words whitespace-normal' }, row.getValue('location')),
    },
    {
        accessorKey: 'manufacturer',
        header: 'Manufacturer',
        cell: ({ row }) => h('div', { class: 'break-words whitespace-normal' }, row.getValue('manufacturer')),
    },
    {
        accessorKey: 'assigned_to',
        header: 'Assigned To User',
        cell: ({ row }) => h('div', { class: 'break-words whitespace-normal' }, row.getValue('assigned_to')),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => h('div', { class: 'break-words whitespace-normal' }, row.getValue('status')),
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const rowitem = row.original;

            return h(
                'div',
                { class: 'relative' },
                h(DropDownAction, {
                    rowitem,
                    onEdit: handleEdit,
                    onDelete: openDeleteDialog,
                }),
            );
        },
    },
];

/* Define Table State */
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const expanded = ref<ExpandedState>({});
const pageSizesOptions = [5, 10, 20, 30];

/* Table Data and Pagination */
const data = ref(props.items.data);
const pagination = ref({
    pageIndex: props.items.meta.current_page - 1,
    pageSize: props.items.meta.per_page,
});

/* Initialize Table */
const table = useVueTable({
    data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    onExpandedChange: (updaterOrValue) => valueUpdater(updaterOrValue, expanded),
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get columnVisibility() {
            return columnVisibility.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
        get expanded() {
            return expanded.value;
        },
        get pagination() {
            return pagination.value;
        },
    },
    rowCount: props.items.meta.total,
    manualPagination: true,
    onPaginationChange: (updater) => {
        const newState = typeof updater === 'function' ? updater(pagination.value) : updater;
        pagination.value = newState;
        fetchPage(pagination);
    },
});

/* Fetch Page Data */
const fetchPage = (newpagination: any) => {
    router.get(
        `${baseentityurl}`,
        { page: newpagination.value.pageIndex + 1, per_page: newpagination.value.pageSize },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (pageData: any) => {
                data.value = pageData.props.items.data;
                pagination.value.pageIndex = pageData.props.items.meta.current_page - 1;
                pagination.value.pageSize = pageData.props.items.meta.per_page;
            },
        },
    );
};

/* Dialog State */
const showDialogForm = ref(false);
const mode = ref('create');
const itemID = ref<number | null>(null);

/* Form Schema and Configuration */
const schema = z.object({
    name: z
        .string({
            required_error: 'Name is required',
            invalid_type_error: 'Name must be a string',
        })
        .min(3, 'Name must be at least 3 characters long')
        .max(255, 'Name must not exceed 255 characters'),
    serial_number: z
        .string({
            required_error: 'Serial number is required',
            invalid_type_error: 'Serial number must be a string',
        })
        .max(255, 'Serial number must not exceed 255 characters'),
    model_name: z
        .string({
            invalid_type_error: 'Model name must be a string',
        })
        .max(255, 'Model name must not exceed 255 characters')
        .optional(),
    category_id: z.enum(
        props.categories.map((item: any) => item.name),
        {
            required_error: 'Category is required',
        },
    ),
    location_id: z
        .enum(
            props.locations.map((item: any) => item.name),
            {
                required_error: 'Location is required',
            },
        )
        .optional(),
    manufacturer_id: z
        .enum(
            props.manufacturers.map((item: any) => item.name),
            {
                required_error: 'Manufacturer is required',
            },
        )
        .optional(),
    assigned_to_user_id: z
        .enum(
            props.users.map((item: any) => item.name),
            {
                required_error: 'Users is required',
            },
        )
        .optional(),
    asset_tag: z
        .string({
            required_error: 'Asset tag is required',
            invalid_type_error: 'Asset tag must be a string',
        })
        .max(255, 'Asset tag must not exceed 255 characters')
        .optional(),
    purchase_date: z.coerce.date()
        .optional(),
    purchase_price: z
        .number({
            invalid_type_error: 'Purchase price must be a number',
        })
        .nonnegative('Purchase price must be a positive number')
        .optional(),
    status: z.enum(Object.values(statusEnum) as [string, ...string[]], {
        required_error: 'Status is required',
    }),
    notes: z
        .string({
            invalid_type_error: 'Notes must be a string',
        })
        .max(1000, 'Notes must not exceed 1000 characters')
        .optional(),
});

const fieldconfig : Config<Asset> = {
    name: {
        label: 'Name',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter asset name',
        },
        description: 'Name of the asset.',
    },
    serial_number: {
        label: 'Serial Number',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter serial number',
        },
        description: 'Serial number of the asset.',
    },
    model_name: {
        label: 'Model Name',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter model name',
        },
        description: 'Model name of the asset.',
    },
    category_id: {
        label: 'Select Category', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a category...', // Placeholder for the select
        },
    },
    location_id: {
        label: 'Select Location', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a location...', // Placeholder for the select
        },
    },
    manufacturer_id: {
        label: 'Select Manufacturer', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a manufacturer...', // Placeholder for the select
        },
    },
    assigned_to_user_id: {
        label: 'Select Assigned User', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a user...', // Placeholder for the select
        },
    },
    asset_tag: {
        label: 'Asset Tag',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter asset tag',
        },
        description: 'Unique identifier for the asset.',
    },
    purchase_date: {
        label: 'Purchase Date',
        inputProps: {
            type: 'date',
        },
        description: 'Date when the asset was purchased.',
    },
    purchase_price: {
        label: 'Purchase Price',
        inputProps: {
            type: 'number',
            placeholder: 'Enter purchase price',
        },
        description: 'Price of the asset at the time of purchase.',
    },
    status: {
        label: 'Status',
        component: 'select', // Dropdown for selecting status
        description: 'Current status of the asset.',
    },
    notes: {
        label: 'Notes',
        component: 'textarea', // Textarea for notes
        inputProps: {
            class: 'uppercase',
            placeholder: 'Enter additional notes',
        },
        description: 'Additional information about the asset.',
    },
};

const form = useForm({
    validationSchema: toTypedSchema(schema),
    initialValues: {
        category_id: '',
        location_id: '',
        manufacturer_id: '',
        assigned_to_user_id: '',
        asset_tag: '',
        name: '',
        serial_number: '',
        model_name: '',
        purchase_date: parseAbsolute(new Date().toISOString(), getLocalTimeZone()).toDate(),
        purchase_price: 0,
        status: '',
        notes: '',
    },
});

/* Form Handlers */
const resetForm = () => {
    form.resetForm();
    itemID.value = null;
};

const handleOpenDialogForm = () => {
    showDialogForm.value = true;
    mode.value = 'create';
};

const onSubmit = async (values: any) => {
    const mappedValues = {
        ...values,
        category_id: props.categories.find((category : any) => category.name === values.category_id)?.id || null,
        location_id: props.locations.find((location : any) => location.name === values.location_id)?.id || null,
        manufacturer_id: props.manufacturers.find((manufacturer : any) => manufacturer.name === values.manufacturer_id)?.id || null,
        assigned_to_user_id: props.users.find((user : any) => user.name === values.assigned_to_user_id)?.id || null,
        status: Object.keys(statusEnum).find((key) => statusEnum[key as keyof typeof statusEnum] === values.status) || null,
    };
    
    try {

        if (mode.value === 'create') {
            await axios.post(`${baseentityurl}`, mappedValues);
            toast.success(`${baseentityname} created successfully.`);
        } else if (mode.value === 'edit') {
            await axios.put(`${baseentityurl}/${itemID.value}`, mappedValues);
            toast.success(`${baseentityname} updated successfully.`);
        }

        resetForm();
        fetchPage(pagination);
        showDialogForm.value = false;
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            form.setErrors(error.response.data.errors);
            toast.error('Validation failed. Please check your input.');
        } else {
            toast.error('An unexpected error occurred.');
        }
    }
};

/* Edit Handler */
const handleEdit = async (id: number) => {
    try {
        mode.value = 'edit';
        itemID.value = id;
        const response = await axios.get(`${baseentityurl}/${id}`);
        const data = response.data;
         // Map the response fields to their corresponding IDs
        //  console.log(data.purchase_date);
         const mappedData = {
            ...data,
            category_id: data.category,
            location_id: data.location,
            manufacturer_id:  data.manufacturer,
            assigned_to_user_id: data.assigned_to,
            status: data.status,
            purchase_date: data.purchase_date ? parseAbsolute(new Date(data.purchase_date).toISOString(), getLocalTimeZone()) : null, 
        };
        // console.log(mappedData);
        
        form.setValues(mappedData);
        showDialogForm.value = true;
    } catch (error) {
        console.log(`Error fetching ${baseentityname} data:`, error);
        toast.error(`Failed to fetch ${baseentityname} data.`);
    }
};

/* Delete Dialog State */
const showDeleteDialog = ref(false);
const itemIDToDelete = ref<number | null>(null);

const openDeleteDialog = (id: number) => {
    itemIDToDelete.value = id;
    showDeleteDialog.value = true;
};

const handleDelete = async () => {
    try {
        if (!itemIDToDelete.value) return;

        await axios.delete(`${baseentityurl}/${itemIDToDelete.value}`);
        toast.success(`${baseentityname} deleted successfully.`);
        fetchPage(pagination);
        showDeleteDialog.value = false;
    } catch (error) {
        console.log(`Error deleting ${baseentityname}:`, error);
        toast.error(`Failed to delete ${baseentityname}. Please try again.`);
    }
};
</script>

<template>
    <Head :title="baseentityname" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Filter and Create Button -->
            <div class="flex items-center gap-2 py-4">
                <EntityFilter
                    :filterValue="table.getColumn('name')?.getFilterValue() as string"
                    placeholder="Filter categories..."
                    @filter="(value: any) => table.getColumn('name')?.setFilterValue(value)"
                />
                <div class="ml-auto flex items-center gap-2">
                    <Button class="bg-sky-300" @click="handleOpenDialogForm"> <Plus class="h-4"></Plus> Create {{ baseentityname }} </Button>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" class="ml-auto"> Columns <ChevronDown class="ml-2 h-4 w-4" /> </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuCheckboxItem
                                v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                                :key="column.id"
                                class="capitalize"
                                :model-value="column.getIsVisible()"
                                @update:model-value="(value: any) => column.toggleVisibility(!!value)"
                            >
                                {{ column.id }}
                            </DropdownMenuCheckboxItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Table -->
            <EntityTable :table="table" :columns="columns" />

            <!-- Pagination -->
            <EntityPagination
                :pagination="pagination"
                :pageSizeOptions="pageSizesOptions"
                :selectedRows="table.getFilteredSelectedRowModel().rows.length"
                :totalRows="table.getFilteredRowModel().rows.length"
                :totalEntities="table.getRowCount()"
                :table="table"
                @updatePageSize="(value: any) => table.setPageSize(value)"
                @goToFirstPage="() => table.setPageIndex(0)"
                @goToPreviousPage="() => table.previousPage()"
                @goToNextPage="() => table.nextPage()"
                @goToLastPage="() => table.setPageIndex(table.getPageCount() - 1)"
            />

            <!-- Dialog Form -->
            <Dialog v-model:open="showDialogForm">
                <DialogScrollContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>{{ mode === 'create' ? 'Create' : 'Update' }} {{ baseentityname }}</DialogTitle>
                    </DialogHeader>
                    <DialogDescription> Use this form to edit the {{ baseentityname }} details. </DialogDescription>
                    <AutoForm class="space-y-6" :form="form" :schema="schema" :field-config="fieldconfig" @submit="onSubmit">
                        <DialogFooter>
                            <Button type="submit" class="bg-green-300">
                                {{ mode === 'create' ? 'Create' : 'Update' }}
                            </Button>
                        </DialogFooter>
                    </AutoForm>
                </DialogScrollContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <ReusableAlertDialog
                :open="showDeleteDialog"
                :title="`Delete ${baseentityname}`"
                :description="`Are you sure you want to delete this ${baseentityname}? This action cannot be undone.`"
                @confirm="handleDelete"
                @cancel="showDeleteDialog = false"
            />
        </div>
    </AppLayout>
</template>
