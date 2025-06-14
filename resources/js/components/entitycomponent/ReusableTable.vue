<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { valueUpdater } from '@/components/ui/table/utils';
import { cn } from '@/lib/utils';
import type { ColumnDef } from '@tanstack/vue-table';
import {
    ColumnFiltersState,
    ExpandedState,
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    SortingState,
    useVueTable,
    VisibilityState,
} from '@tanstack/vue-table';
import axios from 'axios';
import { ChevronDown, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    columns: Array as () => ColumnDef<any>[],
    filterValue: String,
    placeholder: String,
    baseentityurl: String,
    baseentityname: String,
});

const data = ref([]);
const sorting = ref<SortingState>([]);
const globalFilter = ref('');
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const expanded = ref<ExpandedState>({});
const pageSizesOptions = [5, 10, 20, 30];
const loading = ref(true);

/* Table Data and Pagination */
const pagination = ref({
    paginationState: {
        pageIndex: 0,
        pageSize: 5,
    },
    current_page: 0,
    per_page: 5,
    last_page: 0,
    total: 0,
    path: '',
    from: 0,
    to: 0,
    links: [] as any[],
});

let table: any;

const fetchRows = async () => {
    try {
        const response = await axios.post(`${props.baseentityurl}/list`, {
            page: pagination.value.paginationState.pageIndex + 1,
            per_page: pagination.value.paginationState.pageSize,
            sort_field: sorting.value[0]?.id,
            sort_direction: sorting.value.length == 0 ? undefined : sorting.value[0]?.desc ? 'desc' : 'asc',
            searchtext: globalFilter.value,
        });

        const pageData = response.data;
        data.value = pageData.data;
        pagination.value = {
            ...pagination.value,
            ...pageData.meta,
            paginationState: {
                pageIndex: pageData.meta.current_page - 1,
                pageSize: pageData.meta.per_page,
            },
        };

        console.log('Pagination:', pagination.value);
    } catch (error) {
        console.error('Error fetching data:', error);
    }
};

/* Initialize Table */
const initializeTable = () => {
    table = useVueTable({
        data,
        columns: (props.columns as ColumnDef<unknown, any>[]) ?? [],
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getExpandedRowModel: getExpandedRowModel(),
        rowCount: pagination.value.total,
        manualPagination: true,
        manualSorting: true,
        manualFiltering: true,
        initialState: {
            pagination: {
                pageSize: 5,
            },
        },
        onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
        onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
        onExpandedChange: (updaterOrValue) => valueUpdater(updaterOrValue, expanded),
        state: {
            get globalFilter() {
                return globalFilter.value;
            },
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
                return pagination.value.paginationState;
            },
        },
        onPaginationChange: (updater) => {
            const newState = typeof updater === 'function' ? updater(pagination.value.paginationState) : updater;
            pagination.value.paginationState = newState;
            fetchRows();
        },
        onSortingChange: (updaterOrValue) => {
            valueUpdater(updaterOrValue, sorting); // Update sorting state
            fetchRows(); // Fetch sorted data
        },
        onGlobalFilterChange: (value) => {
            globalFilter.value = value; // Update the global filter state
            fetchRows(); // Fetch filtered data
            table.resetPagination();
        },
    });
};

watch(globalFilter, (newValue) => {
    //only send after 3 characters are typed
    //if searchtext is blank reset the table
    if (newValue === '') {
        table.setGlobalFilter('');
        return;
    }
    table.setGlobalFilter(newValue);
});

onMounted(async () => {
    loading.value = true; // Set loading to true
    await fetchRows(); // Fetch rows
    initializeTable(); // Initialize the table
    loading.value = false; // Set loading to false after initialization
});

defineExpose({
    fetchRows,
});
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div v-if="loading" class="flex h-full items-center justify-center">
            <div class="spinner-border inline-block h-8 w-8 animate-spin rounded-full border-4" role="status"></div>
        </div>

        <!-- Show table when not loading -->
        <div v-else>
            <div class="flex items-center gap-2 py-4">
                <Input class="max-w-sm" v-model="globalFilter" placeholder="Search..." />
                <div class="ml-auto flex items-center gap-2">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" class="ml-auto"> Columns <ChevronDown class="ml-2 h-4 w-4" /> </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuCheckboxItem
                                v-for="column in table.getAllColumns().filter((column: any) => column.getCanHide())"
                                :key="column.id"
                                class="capitalize"
                                :model-value="column.getIsVisible()"
                                @update:model-value="
                                    (value: any) => {
                                        column.toggleVisibility(!!value);
                                    }
                                "
                            >
                                {{ column.id }}
                            </DropdownMenuCheckboxItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <TableHead
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                :class="
                                    cn(
                                        { 'sticky bg-background/95': header.column.getIsPinned() },
                                        header.column.getIsPinned() === 'left' ? 'left-0' : 'right-0',
                                    )
                                "
                            >
                                <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="table.getRowModel().rows?.length">
                            <template v-for="row in table.getRowModel().rows" :key="row.id">
                                <TableRow>
                                    <TableCell
                                        v-for="cell in row.getVisibleCells()"
                                        :key="cell.id"
                                        :class="
                                            cn(
                                                { 'sticky bg-background/95': cell.column.getIsPinned() },
                                                cell.column.getIsPinned() === 'left' ? 'left-0' : 'right-0',
                                            )
                                        "
                                    >
                                        <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                    </TableCell>
                                </TableRow>
                            </template>
                        </template>
                        <TableRow v-else>
                            <TableCell :colspan="props.columns?.length" class="h-24 text-center"> No results. </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div class="flex items-center justify-end space-x-2 py-4">
                <div class="flex-1 text-sm text-muted-foreground">
                    {{ table.getFilteredSelectedRowModel().rows.length }} of {{ table.getFilteredRowModel().rows.length }} row(s) selected. Total
                    {{ pagination.total }} entities
                </div>
                <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium">Rows per page</p>
                    <Select :model-value="pagination.current_page.toString()" @update:model-value="(value) => table.setPageSize(Number(value))">
                        <SelectTrigger class="h-8 w-[70px]">
                            <SelectValue :placeholder="pagination.per_page.toString()" />
                        </SelectTrigger>
                        <SelectContent side="top">
                            <SelectItem v-for="size in pageSizesOptions" :key="size" :value="size.toString()">
                                {{ size }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-x-2">
                    <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.setPageIndex(0)">
                        <ChevronsLeft />
                    </Button>
                    <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                        <ChevronLeft />
                    </Button>
                    <span class="text-sm">Page {{ pagination.current_page }} of {{ table.getPageCount() }}</span>
                    <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                        <ChevronRight />
                    </Button>
                    <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.setPageIndex(table.getPageCount() - 1)">
                        <ChevronsRight />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
