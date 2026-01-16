<template>
  <div class="check-updates-page p-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Check for Updates</h2>
      <p class="text-gray-600">
        Manage plugin updates from GitHub releases. Updates are checked automatically every 12 hours.
      </p>
    </div>

    <!-- Current Version Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
          <span class="dashicons dashicons-admin-plugins text-2xl"></span>
          Current Version
        </h3>
        <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-semibold">
          v{{ currentVersion }}
        </span>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        <div class="bg-gray-50 rounded-lg p-4">
          <p class="text-sm text-gray-600 mb-1">Plugin Name</p>
          <p class="font-semibold text-gray-900">BP Dev Tools</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
          <p class="text-sm text-gray-600 mb-1">Repository</p>
          <a 
            :href="repositoryUrl" 
            target="_blank" 
            class="font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1"
          >
            GitHub
            <span class="dashicons dashicons-external text-sm"></span>
          </a>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
          <p class="text-sm text-gray-600 mb-1">Update Method</p>
          <p class="font-semibold text-gray-900">GitHub Releases</p>
        </div>
      </div>
    </div>

    <!-- Update Status Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
          <span class="dashicons dashicons-update text-2xl"></span>
          Update Status
        </h3>
        <button
          @click="checkForUpdates"
          :disabled="checking"
          class="px-4 py-2 bg-gradient-to-r from-primary-start to-primary-end text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg disabled:hover:shadow-none flex items-center gap-2"
        >
          <span v-if="checking" class="dashicons dashicons-update animate-spin"></span>
          <span v-else class="dashicons dashicons-download"></span>
          {{ checking ? 'Checking...' : 'Check Now' }}
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="checking" class="text-center py-8">
        <span class="dashicons dashicons-update animate-spin text-4xl text-gray-400"></span>
        <p class="text-gray-600 mt-4">Checking for updates...</p>
      </div>

      <!-- Up to Date -->
      <div v-else-if="!updateAvailable" class="text-center py-8">
        <span class="dashicons dashicons-yes-alt text-6xl text-green-600 mb-4"></span>
        <h4 class="text-xl font-semibold text-gray-900 mb-2">You're up to date!</h4>
        <p class="text-gray-600">
          You're running the latest version of BP Dev Tools.
        </p>
        <p v-if="lastChecked" class="text-sm text-gray-500 mt-4">
          Last checked: {{ lastChecked }}
        </p>
      </div>

      <!-- Update Available -->
      <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-start gap-4">
          <span class="dashicons dashicons-warning text-3xl text-yellow-600 flex-shrink-0"></span>
          <div class="flex-1">
            <h4 class="text-lg font-semibold text-gray-900 mb-2">
              Update Available: v{{ latestVersion }}
            </h4>
            <p class="text-gray-700 mb-4">
              A new version is available. Go to the WordPress Plugins page to update.
            </p>
            <div class="flex gap-3">
              <a
                :href="adminUrl + 'plugins.php'"
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center gap-2"
              >
                <span class="dashicons dashicons-update"></span>
                Go to Plugins Page
              </a>
              <a
                v-if="releaseUrl"
                :href="releaseUrl"
                target="_blank"
                class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center gap-2"
              >
                <span class="dashicons dashicons-external"></span>
                View Release Notes
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Settings Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <span class="dashicons dashicons-admin-settings text-2xl"></span>
        Update Settings
      </h3>
      
      <div class="space-y-4">
        <div class="flex items-start gap-3">
          <span class="dashicons dashicons-info text-blue-600 mt-1"></span>
          <div>
            <p class="font-semibold text-gray-900 mb-1">Automatic Update Checks</p>
            <p class="text-sm text-gray-600">
              WordPress automatically checks for updates every 12 hours. You can also manually check for updates using the button above.
            </p>
          </div>
        </div>
        
        <div class="flex items-start gap-3">
          <span class="dashicons dashicons-shield text-green-600 mt-1"></span>
          <div>
            <p class="font-semibold text-gray-900 mb-1">GitHub Releases</p>
            <p class="text-sm text-gray-600">
              Updates are delivered via GitHub releases. When a new release is published with a version tag (e.g., v1.1.0), it will be automatically detected.
            </p>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <span class="dashicons dashicons-backup text-purple-600 mt-1"></span>
          <div>
            <p class="font-semibold text-gray-900 mb-1">Safe Updates</p>
            <p class="text-sm text-gray-600">
              Always backup your site before updating. Updates will replace plugin files but preserve your settings and data.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// State
const currentVersion = ref(window.bpDevToolsAdmin?.version || '1.0.0')
const latestVersion = ref(null)
const updateAvailable = ref(false)
const checking = ref(false)
const lastChecked = ref(null)
const releaseUrl = ref(null)
const repositoryUrl = ref('https://github.com/braudypedrosa/bp-dev-tools')
const adminUrl = ref(window.bpDevToolsAdmin?.adminUrl || '/wp-admin/')

// Check for updates
const checkForUpdates = async () => {
  checking.value = true
  
  try {
    // Trigger WordPress to check for updates
    const response = await fetch(
      window.bpDevToolsAdmin.ajaxUrl,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'bp_dev_tools_check_updates',
          nonce: window.bpDevToolsAdmin.nonce
        })
      }
    )

    const data = await response.json()
    
    if (data.success) {
      updateAvailable.value = data.data.update_available || false
      latestVersion.value = data.data.latest_version || currentVersion.value
      releaseUrl.value = data.data.release_url || null
      lastChecked.value = new Date().toLocaleString()
    }
  } catch (error) {
    console.error('Failed to check for updates:', error)
  } finally {
    checking.value = false
  }
}

// Load on mount
onMounted(() => {
  // Optionally check on page load
  // checkForUpdates()
})
</script>

<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
