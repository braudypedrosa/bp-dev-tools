<template>
  <div class="bp-dev-tools-app">
    <!-- Header -->
    <AppHeader :enabled-count="enabledToolsCount" />

    <!-- Main Container -->
    <div class="flex min-h-screen">
      <!-- Sidebar -->
      <Sidebar 
        :current-page="currentPage"
        :enabled-tools="enabledTools"
        @navigate="handleNavigation"
      />

      <!-- Content Area -->
      <div class="flex-1 p-8 bg-gray-50">
        <component 
          :is="currentView" 
          :tools="tools"
          @tool-toggled="handleToolToggle"
        />
      </div>
    </div>

    <!-- Toast Notifications -->
    <ToastContainer />
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useToolsStore } from '@store/tools'
import AppHeader from '@components/AppHeader.vue'
import Sidebar from '@components/Sidebar.vue'
import ToastContainer from '@components/ToastContainer.vue'
import GeneralSettings from '@views/GeneralSettings.vue'
import CheckUpdates from '@views/CheckUpdates.vue'
import ToolPage from '@views/ToolPage.vue'

const store = useToolsStore()
const currentPage = ref('general')

// Get URL parameter for current page
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  currentPage.value = urlParams.get('tab') || 'general'
  
  // Load tools from WordPress
  store.loadTools()
})

// Computed properties
const tools = computed(() => store.tools)
const enabledTools = computed(() => store.enabledTools)
const enabledToolsCount = computed(() => enabledTools.value.length)

// Current view component
const currentView = computed(() => {
  if (currentPage.value === 'general') {
    return GeneralSettings
  }
  if (currentPage.value === 'check-updates') {
    return CheckUpdates
  }
  return ToolPage
})

// Handle navigation
const handleNavigation = (page) => {
  currentPage.value = page
  
  // Update URL without page reload
  const url = new URL(window.location)
  url.searchParams.set('tab', page)
  window.history.pushState({}, '', url)
}

// Handle tool toggle
const handleToolToggle = async (toolId, enabled) => {
  console.log('⚡ App.vue handleToolToggle:', { toolId, enabled, type: typeof enabled })
  await store.toggleTool(toolId, enabled)
  
  // Reload page after short delay to update sidebar
  if (enabled || (!enabled && currentPage.value === toolId)) {
    setTimeout(() => {
      window.location.reload()
    }, 1200)
  }
}
</script>

<style scoped>
.bp-dev-tools-app {
  @apply min-h-screen;
}
</style>
