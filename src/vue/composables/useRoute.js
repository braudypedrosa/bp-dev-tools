/**
 * useRoute Composable
 * 
 * A simple composable to handle URL parameters for routing within WordPress admin.
 * This provides a Vue Router-like interface without the full routing library.
 */

import { ref, computed } from 'vue'

export function useRoute() {
  const urlParams = new URLSearchParams(window.location.search)
  
  // Get current tab from URL
  const tab = computed(() => {
    return urlParams.get('tab') || 'general'
  })

  return {
    tab,
    params: urlParams
  }
}
