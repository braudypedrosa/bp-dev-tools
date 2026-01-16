<template>
  <div v-if="currentTool">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">
        {{ currentTool.title }}
      </h2>
      <p class="text-gray-600">{{ currentTool.description }}</p>
    </div>

    <!-- Tool Content -->
    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-200">
      <!-- Tool-specific content will be rendered here -->
      <component 
        v-if="toolComponent" 
        :is="toolComponent"
        :tool="currentTool"
      />
      
      <!-- Default placeholder if no specific component -->
      <div v-else class="text-center py-16">
        <span class="dashicons dashicons-admin-generic text-6xl text-gray-300 mb-4"></span>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">
          {{ currentTool.title }}
        </h3>
        <p class="text-gray-500 mb-6">
          This tool's interface will be displayed here.
        </p>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-2xl mx-auto">
          <p class="text-sm text-blue-900">
            <strong>Developer Note:</strong> Implement the tool's functionality by creating a Vue component
            for this tool and registering it in the ToolPage component.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tool not found -->
  <div v-else class="text-center py-16">
    <span class="dashicons dashicons-warning text-6xl text-red-300 mb-4"></span>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">Tool Not Found</h3>
    <p class="text-gray-500">The requested tool could not be found or is not enabled.</p>
  </div>
</template>

<script setup>
import { computed, markRaw } from 'vue'
import { useRoute } from '@/composables/useRoute'

// Import tool-specific components
import SlugScannerTool from '@components/tools/SlugScannerTool.vue'

const props = defineProps({
  tools: {
    type: Array,
    default: () => []
  }
})

// Get current tool from URL/route
const route = useRoute()
const currentToolId = computed(() => route.tab.value)

// Find current tool data
const currentTool = computed(() => {
  return props.tools.find(tool => tool.id === currentToolId.value)
})

// Map tool IDs to their Vue components
const toolComponents = {
  'slug-scanner': markRaw(SlugScannerTool),
  // Add more tool components here as they're created
}

// Get the component for current tool
const toolComponent = computed(() => {
  return toolComponents[currentToolId.value] || null
})
</script>
