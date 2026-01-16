<template>
  <div class="w-72 bg-white border-r border-gray-200 py-6">
    <nav class="space-y-1">
      <!-- General Settings -->
      <a
        href="#"
        @click.prevent="$emit('navigate', 'general')"
        :class="[
          'flex items-center px-6 py-3 text-sm font-medium transition-all border-l-3',
          currentPage === 'general'
            ? 'bg-blue-50 text-blue-700 border-blue-700 font-semibold'
            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-transparent'
        ]"
      >
        <span class="dashicons dashicons-admin-settings mr-3 text-xl"></span>
        General Settings
      </a>

      <!-- Check Updates -->
      <a
        href="#"
        @click.prevent="$emit('navigate', 'check-updates')"
        :class="[
          'flex items-center px-6 py-3 text-sm font-medium transition-all border-l-3',
          currentPage === 'check-updates'
            ? 'bg-blue-50 text-blue-700 border-blue-700 font-semibold'
            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-transparent'
        ]"
      >
        <span class="dashicons dashicons-update mr-3 text-xl"></span>
        Check Updates
      </a>

      <!-- Enabled Tools Section -->
      <template v-if="enabledTools.length > 0">
        <div class="pt-6 pb-2 px-6">
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">
            Enabled Tools
          </h3>
        </div>

        <a
          v-for="toolId in enabledTools"
          :key="toolId"
          href="#"
          @click.prevent="$emit('navigate', toolId)"
          :class="[
            'flex items-center px-6 py-3 text-sm font-medium transition-all border-l-3',
            currentPage === toolId
              ? 'bg-blue-50 text-blue-700 border-blue-700 font-semibold'
              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-transparent'
          ]"
        >
          <span :class="['dashicons mr-3 text-xl', getToolIcon(toolId)]"></span>
          {{ getToolTitle(toolId) }}
        </a>
      </template>

      <!-- No Tools Message -->
      <div v-else class="px-6 pt-6">
        <p class="text-xs text-gray-400 italic leading-relaxed">
          No tools enabled yet. Enable tools in General Settings.
        </p>
      </div>
    </nav>
  </div>
</template>

<script setup>
const props = defineProps({
  currentPage: {
    type: String,
    required: true
  },
  enabledTools: {
    type: Array,
    default: () => []
  }
})

defineEmits(['navigate'])

// Get tool data from WordPress
const toolsData = window.bpDevToolsData?.tools || []

const getToolTitle = (toolId) => {
  const tool = toolsData.find(t => t.id === toolId)
  return tool?.title || toolId
}

const getToolIcon = (toolId) => {
  const tool = toolsData.find(t => t.id === toolId)
  return tool?.icon || 'dashicons-admin-generic'
}
</script>
