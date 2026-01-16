<template>
  <div
    :class="[
      'border-2 rounded-xl p-6 transition-all duration-300',
      'hover:-translate-y-1 hover:shadow-xl',
      tool.enabled
        ? 'bg-gradient-to-br from-blue-50 to-blue-100 border-blue-300'
        : 'bg-gray-50 border-gray-200 hover:border-gray-300'
    ]"
  >
    <div class="flex flex-col gap-4">
      <!-- Header with Toggle -->
      <div class="flex justify-between items-start gap-4">
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-gray-900 mb-1">
            {{ tool.title }}
          </h3>
          <p class="text-sm text-gray-600 leading-relaxed">
            {{ tool.description }}
          </p>
        </div>
        
        <!-- Toggle Switch -->
        <ToolToggle
          :model-value="tool.enabled"
          @update:model-value="handleToggle"
          :disabled="loading"
        />
      </div>

      <!-- Configure Link (when enabled) -->
      <div
        v-if="tool.enabled"
        class="pt-4 border-t border-blue-200/30"
      >
        <a
          href="#"
          @click.prevent="$emit('navigate', tool.id)"
          class="inline-flex items-center text-sm font-semibold text-accent hover:text-accent/80 transition-colors group"
        >
          Configure Tool
          <span class="dashicons dashicons-arrow-right-alt ml-1 text-base group-hover:translate-x-1 transition-transform"></span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ToolToggle from './ToolToggle.vue'

const props = defineProps({
  tool: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['toggle', 'navigate'])

const loading = ref(false)

const handleToggle = async (enabled) => {
  console.log('🎯 ToolCard handleToggle:', { toolId: props.tool.id, enabled, type: typeof enabled })
  loading.value = true
  emit('toggle', props.tool.id, enabled)
  console.log('📤 ToolCard emitted toggle event')
  loading.value = false
}
</script>
