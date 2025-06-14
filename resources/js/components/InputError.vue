<script setup lang="ts">

import { computed } from 'vue';

const props = defineProps({
    message: {
        type: [String, Array], // Can be a string or an array of strings
        default: null,
    },
});

// Compute the message(s) to display
const displayMessages = computed(() => {
    if (!props.message) {
        return []; // No messages
    }
    // If it's a string, wrap it in an array
    if (typeof props.message === 'string') {
        return [props.message];
    }
    // If it's already an array, return it as is
    return props.message;
});


</script>

<template>
    <div v-if="displayMessages.length" class="text-sm text-red-600 dark:text-red-400 mt-1">
        <p v-for="(msg, index) in displayMessages" :key="index">
            {{ msg }}
        </p>
    </div>
</template>

