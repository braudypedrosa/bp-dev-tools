/**
 * BP Dev Tools - Admin Vue Application Entry Point
 * 
 * This is the main entry point for the Vue application.
 * It creates and mounts the Vue app to the WordPress admin page.
 * 
 * @package BP_Dev_Tools
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from '../vue/App.vue'
import '../css/app.css'

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
  // Find the mount point
  const mountEl = document.getElementById('bp-dev-tools-app')
  
  if (mountEl) {
    // Create Pinia store
    const pinia = createPinia()
    
    // Create Vue app
    const app = createApp(App)
    
    // Use Pinia
    app.use(pinia)
    
    // Mount the app
    app.mount(mountEl)
    
    console.log('✅ BP Dev Tools Vue app mounted successfully')
  } else {
    console.error('❌ BP Dev Tools: Mount element #bp-dev-tools-app not found')
  }
})
