<script setup lang="ts">
import { Input } from '@/components/ui/input';

const props = defineProps({
    filterValue: String,
    placeholder: String,
});

const emit = defineEmits(['filter']);
</script>

<template>
    <Input
        class="max-w-sm"
        :placeholder="props.placeholder"
        :model-value="props.filterValue"
        @update:model-value="(value) => emit('filter', value)"
    />
</template>
