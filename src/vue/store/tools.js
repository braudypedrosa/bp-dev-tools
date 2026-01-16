/**
 * Pinia Store for Tools Management
 * 
 * Manages the state of all dev tools including:
 * - Loading tools from WordPress
 * - Toggling tools on/off
 * - Tracking enabled tools
 * - Making AJAX requests to WordPress
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useToolsStore = defineStore('tools', () => {
  // State
  const tools = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Getters (computed)
  const enabledTools = computed(() => {
    return tools.value
      .filter(tool => tool.enabled)
      .map(tool => tool.id)
  })

  const getToolById = computed(() => {
    return (id) => tools.value.find(tool => tool.id === id)
  })

  // Actions
  const loadTools = async () => {
    // Get tools from WordPress localized data
    if (window.bpDevToolsData && window.bpDevToolsData.tools) {
      tools.value = window.bpDevToolsData.tools
    }
  }

  const toggleTool = async (toolId, enabled) => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post(
        window.bpDevToolsAdmin.ajaxUrl,
        new URLSearchParams({
          action: 'bp_dev_tools_toggle_tool',
          nonce: window.bpDevToolsAdmin.nonce,
          tool_id: toolId,
          enabled: enabled
        })
      )

      if (response.data.success) {
        // Update local state
        const tool = tools.value.find(t => t.id === toolId)
        if (tool) {
          tool.enabled = enabled
        }

        // Show success notification
        useToast().show(
          enabled 
            ? window.bpDevToolsAdmin.strings.toolEnabled 
            : window.bpDevToolsAdmin.strings.toolDisabled,
          'success'
        )

        return true
      } else {
        throw new Error(response.data.data?.message || 'Failed to toggle tool')
      }
    } catch (err) {
      error.value = err.message
      
      // Show error notification
      useToast().show(
        err.message || window.bpDevToolsAdmin.strings.error,
        'error'
      )

      return false
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    tools,
    loading,
    error,
    
    // Getters
    enabledTools,
    getToolById,
    
    // Actions
    loadTools,
    toggleTool,
  }
})

// Toast composable (simple implementation)
const toastState = ref({ show: false, message: '', type: 'success' })

export const useToast = () => {
  return {
    show: (message, type = 'success') => {
      toastState.value = { show: true, message, type }
      
      setTimeout(() => {
        toastState.value.show = false
      }, type === 'error' ? 3000 : 2500)
    },
    state: toastState,
  }
}
