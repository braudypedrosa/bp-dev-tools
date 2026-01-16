<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-2">General Settings</h2>
      <p class="text-gray-600">Enable or disable development tools</p>
    </div>

    <!-- Tools Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ToolCard
        v-for="tool in tools"
        :key="tool.id"
        :tool="tool"
        @toggle="$emit('tool-toggled', $event, arguments[1])"
        @navigate="$emit('navigate', $event)"
      />
    </div>

    <!-- No Tools Message -->
    <div
      v-if="tools.length === 0"
      class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300"
    >
      <span class="dashicons dashicons-admin-tools text-6xl text-gray-300 mb-4"></span>
      <h3 class="text-xl font-semibold text-gray-700 mb-2">No Tools Available</h3>
      <p class="text-gray-500">Add tools to get started</p>
    </div>
  </div>
</template>

<script setup>
import ToolCard from '@components/ToolCard.vue'

defineProps({
  tools: {
    type: Array,
    default: () => []
  }
})

defineEmits(['tool-toggled', 'navigate'])
</script>
