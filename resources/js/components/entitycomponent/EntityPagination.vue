<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';

const props = defineProps({
    pagination: Object, // Pagination state
    pageSizeOptions: Array<number>, // Page size options
    selectedRows: Number,
    totalRows: Number,
    totalEntities: Number,
    table: Object,
});

const emit = defineEmits(['updatePageSize', 'goToFirstPage', 'goToPreviousPage', 'goToNextPage', 'goToLastPage']);
</script>

<template>
    <div class="flex items-center justify-end space-x-2 py-4">
        <div class="flex-1 text-sm text-muted-foreground">
            {{ selectedRows }} of {{ totalRows }} row(s) selected. Total {{ totalEntities }} entities
        </div>
        <div class="flex items-center space-x-2">
            <p class="text-sm font-medium">Rows per page</p>
            <Select :model-value="props.pagination?.pageSize.toString()" @update:model-value="(value) => emit('updatePageSize', Number(value))">
                <SelectTrigger class="h-8 w-[70px]">
                    <SelectValue :placeholder="props.pagination?.pageSize.toString()" />
                </SelectTrigger>
                <SelectContent side="top">
                    <SelectItem v-for="size in pageSizeOptions" :key="size" :value="size.toString()">
                        {{ size }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
        <div class="space-x-2">
            <Button variant="outline" size="sm" :disabled="!props.table?.getCanPreviousPage()" @click="emit('goToFirstPage')">
                <ChevronsLeft />
            </Button>
            <Button variant="outline" size="sm" :disabled="!props.table?.getCanPreviousPage()" @click="emit('goToPreviousPage')">
                <ChevronLeft />
            </Button>
            <span class="text-sm">Page {{ props.pagination?.pageIndex + 1 }} of {{ props.table?.getPageCount() }}</span>
            <Button variant="outline" size="sm" :disabled="!props.table?.getCanNextPage()" @click="emit('goToNextPage')">
                <ChevronRight />
            </Button>
            <Button variant="outline" size="sm" :disabled="!props.table?.getCanNextPage()" @click="emit('goToLastPage')">
                <ChevronsRight />
            </Button>
        </div>
    </div>
</template>
