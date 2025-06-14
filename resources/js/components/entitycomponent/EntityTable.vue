<script setup lang="ts">
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { FlexRender } from '@tanstack/vue-table';
import { cn } from '@/lib/utils';

const props = defineProps({
    table: Object, // The table instance
    columns: Array, // The column definitions
});

</script>

<template>
    <div class="rounded-md border">
        <Table>
            <TableHeader>
                <TableRow v-for="headerGroup in props.table?.getHeaderGroups()" :key="headerGroup.id">
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
                <template v-if="props.table?.getRowModel().rows?.length">
                    <template v-for="row in props.table.getRowModel().rows" :key="row.id">
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
</template>
