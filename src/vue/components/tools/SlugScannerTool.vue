<template>
  <div class="slug-scanner-tool">
    <!-- Scanner Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
      <!-- Post Type Selector -->
      <div>
        <label for="post-type" class="block text-sm font-semibold text-gray-700 mb-2">
          Post Type (optional)
        </label>
        <select
          id="post-type"
          v-model="selectedPostType"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option value="">All Post Types</option>
          <option
            v-for="type in postTypes"
            :key="type.value"
            :value="type.value"
          >
            {{ type.label }}
          </option>
        </select>
        <p class="mt-1 text-xs text-gray-500">
          Select a post type to narrow your search, or leave as "All Post Types"
        </p>
      </div>

      <!-- URL Input -->
      <div>
        <label for="urls" class="block text-sm font-semibold text-gray-700 mb-2">
          URLs to Scan
        </label>
        <textarea
          id="urls"
          v-model="urlsInput"
          rows="10"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-mono text-sm"
          placeholder="https://example.com/my-post&#10;https://example.com/another-post&#10;https://example.com/page-name"
        ></textarea>
        <p class="mt-1 text-xs text-gray-500">
          Enter one URL per line. The domain will be stripped and only the slug will be checked.
        </p>
      </div>

      <!-- Scan Button -->
      <div class="flex items-center justify-between">
        <button
          @click="scanSlugs"
          :disabled="!urlsInput.trim() || scanning"
          class="px-6 py-3 bg-gradient-to-r from-primary-start to-primary-end text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg disabled:hover:shadow-none"
        >
          <span v-if="scanning" class="flex items-center gap-2">
            <span class="dashicons dashicons-update animate-spin"></span>
            Scanning...
          </span>
          <span v-else class="flex items-center gap-2">
            <span class="dashicons dashicons-search"></span>
            Scan Slugs
          </span>
        </button>

        <div v-if="urlsInput.trim()" class="text-sm text-gray-600">
          {{ urlsInput.trim().split('\n').length }} URLs to scan
        </div>
      </div>
    </div>

    <!-- Results Modal -->
    <Modal v-model="showResults">
      <template #title>
        Scan Results
      </template>

      <div class="space-y-6">
        <!-- Summary -->
        <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total Scanned</p>
            <p class="text-2xl font-bold text-gray-900">{{ results.total }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-600">Found</p>
            <p class="text-2xl font-bold text-green-600">{{ results.found.length }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-600">Not Found</p>
            <p class="text-2xl font-bold text-red-600">{{ results.not_found.length }}</p>
          </div>
        </div>

        <!-- Found Posts -->
        <div v-if="results.found.length > 0">
          <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <span class="dashicons dashicons-yes-alt text-green-600"></span>
            Found ({{ results.found.length }})
          </h4>
          <div class="space-y-2 max-h-64 overflow-y-auto">
            <div
              v-for="(item, index) in results.found"
              :key="`found-${index}`"
              class="bg-green-50 border border-green-200 rounded-lg p-4"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <p class="font-mono text-sm text-gray-700">{{ item.slug }}</p>
                  <p class="text-sm font-semibold text-gray-900 mt-1">{{ item.title }}</p>
                  <div class="flex items-center gap-3 mt-2 text-xs text-gray-600">
                    <span class="inline-flex items-center">
                      <span class="dashicons dashicons-admin-post text-xs mr-1"></span>
                      {{ item.type }}
                    </span>
                    <span class="inline-flex items-center">
                      <span class="dashicons dashicons-flag text-xs mr-1"></span>
                      {{ item.status }}
                    </span>
                  </div>
                </div>
                <div class="flex gap-2">
                  <a
                    :href="item.edit_url"
                    target="_blank"
                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors"
                  >
                    Edit
                  </a>
                  <a
                    :href="item.view_url"
                    target="_blank"
                    class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors"
                  >
                    View
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Not Found Posts -->
        <div v-if="results.not_found.length > 0">
          <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <span class="dashicons dashicons-warning text-red-600"></span>
            Not Found ({{ results.not_found.length }})
          </h4>
          <div class="space-y-2 max-h-64 overflow-y-auto">
            <div
              v-for="(item, index) in results.not_found"
              :key="`notfound-${index}`"
              class="bg-red-50 border border-red-200 rounded-lg p-4"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="font-mono text-sm text-gray-700">{{ item.slug }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ item.url }}</p>
                </div>
                <div class="flex gap-2">
                  <button
                    @click="createPost(item.slug, 'post')"
                    :disabled="creatingPost[item.slug]"
                    class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors disabled:opacity-50"
                  >
                    <span v-if="creatingPost[item.slug]">Creating...</span>
                    <span v-else>Create Post</span>
                  </button>
                  <button
                    @click="createPost(item.slug, 'page')"
                    :disabled="creatingPost[item.slug]"
                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors disabled:opacity-50"
                  >
                    <span v-if="creatingPost[item.slug]">Creating...</span>
                    <span v-else>Create Page</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="results.found.length === 0 && results.not_found.length === 0 && results.total > 0" class="text-center py-8">
          <span class="dashicons dashicons-info text-4xl text-gray-300 mb-2"></span>
          <p class="text-gray-600">No results to display</p>
        </div>
      </div>

      <template #footer>
        <button
          @click="showResults = false"
          class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
        >
          Close
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Modal from '../Modal.vue'
import { useToast } from '../../store/tools'

const props = defineProps({
  tool: {
    type: Object,
    required: true
  }
})

const { show: showToast } = useToast()

// State
const postTypes = ref([])
const selectedPostType = ref('')
const urlsInput = ref('')
const scanning = ref(false)
const showResults = ref(false)
const results = ref({
  found: [],
  not_found: [],
  total: 0
})
const creatingPost = ref({})

// Load post types on mount
onMounted(async () => {
  try {
    const response = await axios.post(
      window.bpDevToolsAdmin.ajaxUrl,
      new URLSearchParams({
        action: 'bp_dev_tools_get_post_types',
        nonce: window.bpDevToolsAdmin.nonce
      })
    )

    if (response.data.success) {
      postTypes.value = response.data.data.post_types
    }
  } catch (error) {
    console.error('Failed to load post types:', error)
  }
})

// Scan slugs
const scanSlugs = async () => {
  if (!urlsInput.value.trim()) {
    showToast('Please enter at least one URL', 'error')
    return
  }

  scanning.value = true

  try {
    const response = await axios.post(
      window.bpDevToolsAdmin.ajaxUrl,
      new URLSearchParams({
        action: 'bp_dev_tools_scan_slugs',
        nonce: window.bpDevToolsAdmin.nonce,
        urls: urlsInput.value,
        post_type: selectedPostType.value
      })
    )

    if (response.data.success) {
      results.value = response.data.data
      showResults.value = true
      showToast(`Scan complete! Found ${response.data.data.found.length} of ${response.data.data.total} slugs`, 'success')
    } else {
      showToast(response.data.data?.message || 'Scan failed', 'error')
    }
  } catch (error) {
    console.error('Scan error:', error)
    showToast('An error occurred during scanning', 'error')
  } finally {
    scanning.value = false
  }
}

// Create post
const createPost = async (slug, postType) => {
  creatingPost.value[slug] = true

  try {
    const response = await axios.post(
      window.bpDevToolsAdmin.ajaxUrl,
      new URLSearchParams({
        action: 'bp_dev_tools_create_post',
        nonce: window.bpDevToolsAdmin.nonce,
        slug: slug,
        post_type: postType
      })
    )

    if (response.data.success) {
      showToast('Post created successfully!', 'success')
      
      // Open edit page in new tab
      if (response.data.data.edit_url) {
        window.open(response.data.data.edit_url, '_blank')
      }

      // Remove from not_found and refresh results
      results.value.not_found = results.value.not_found.filter(item => item.slug !== slug)
    } else {
      showToast(response.data.data?.message || 'Failed to create post', 'error')
    }
  } catch (error) {
    console.error('Create post error:', error)
    showToast('An error occurred while creating the post', 'error')
  } finally {
    delete creatingPost.value[slug]
  }
}
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
