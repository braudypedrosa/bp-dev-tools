<template>
  <div class="bulk-create-post-tool">
    <!-- Generator Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
      <!-- Post Type Selector -->
      <div>
        <label for="post-type" class="block text-sm font-semibold text-gray-700 mb-2">
          Post Type (defaults to Page)
        </label>
        <select
          id="post-type"
          v-model="selectedPostType"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option
            v-for="type in postTypes"
            :key="type.value"
            :value="type.value"
          >
            {{ type.label }}
          </option>
        </select>
        <p class="mt-1 text-xs text-gray-500">
          Choose a post type to create. If nothing is selected, we default to Page.
        </p>
      </div>

      <!-- Post Status Selector -->
      <div>
        <label for="post-status" class="block text-sm font-semibold text-gray-700 mb-2">
          Post Status (defaults to Draft)
        </label>
        <select
          id="post-status"
          v-model="selectedStatus"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option value="draft">Draft</option>
          <option value="publish">Publish</option>
        </select>
        <p class="mt-1 text-xs text-gray-500">
          Draft lets you review before publishing. Publish makes posts live immediately.
        </p>
      </div>

      <!-- Titles Input -->
      <div>
        <label for="titles" class="block text-sm font-semibold text-gray-700 mb-2">
          Titles to Create
        </label>
        <textarea
          id="titles"
          v-model="titlesInput"
          rows="10"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-mono text-sm"
          placeholder="About Us&#10;Contact&#10;Services"
        ></textarea>
        <p class="mt-1 text-xs text-gray-500">
          Enter one title per line. Each line becomes a new post with the selected status.
        </p>
        <p class="mt-1 text-xs text-gray-500">
          Limit: {{ maxTitles }} titles per request.
        </p>
      </div>

      <!-- Options -->
      <div class="space-y-3">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
          <input
            type="checkbox"
            v-model="skipEmptyLines"
            class="rounded border-gray-300 text-primary focus:ring-primary"
          />
          Skip empty lines
        </label>
        <p class="text-xs text-gray-500">
          Uncheck to see empty lines listed as failures.
        </p>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
          <input
            type="checkbox"
            v-model="allowDuplicates"
            class="rounded border-gray-300 text-primary focus:ring-primary"
          />
          Allow duplicate titles (adds -2, -3 to slug)
        </label>
      </div>

      <!-- Generate Button -->
      <div class="flex items-center justify-between">
        <button
          @click="generatePosts"
          :disabled="!titlesInput.trim() || generating"
          class="px-6 py-3 bg-gradient-to-r from-primary-start to-primary-end text-white font-semibold rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg disabled:hover:shadow-none"
        >
          <span v-if="generating" class="flex items-center gap-2">
            <ArrowPathIcon class="w-5 h-5 animate-spin" />
            Generating...
          </span>
          <span v-else class="flex items-center gap-2">
            <PlusCircleIcon class="w-5 h-5" />
            Generate
          </span>
        </button>

        <div v-if="titlesInput.trim()" class="text-sm text-gray-600">
          {{ titleCount }} titles to create
        </div>
      </div>
    </div>

    <!-- Results Modal -->
    <Modal v-model="showResults">
      <template #title>
        Generation Results
      </template>

      <div class="space-y-6">
        <!-- Summary -->
        <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ results.total }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-600">Created</p>
            <p class="text-2xl font-bold text-green-600">{{ results.created.length }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-600">Failed</p>
            <p class="text-2xl font-bold text-red-600">{{ results.failed.length }}</p>
          </div>
        </div>

        <!-- Created Posts -->
        <div v-if="results.created.length > 0">
          <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <CheckCircleIcon class="w-6 h-6 text-green-600" />
            Created ({{ results.created.length }})
          </h4>
          <div class="space-y-2 max-h-64 overflow-y-auto">
            <div
              v-for="(item, index) in results.created"
              :key="`created-${index}`"
              class="bg-green-50 border border-green-200 rounded-lg p-4"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ item.title }}</p>
                  <div class="flex items-center gap-3 mt-2 text-xs text-gray-600">
                    <span class="inline-flex items-center gap-1">
                      <DocumentTextIcon class="w-3 h-3" />
                      {{ item.type }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                      <FlagIcon class="w-3 h-3" />
                      {{ item.status }}
                    </span>
                  </div>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                  <a
                    :href="item.edit_url"
                    target="_blank"
                    class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 hover:text-white active:text-white transition-colors"
                  >
                    Edit
                  </a>
                  <a
                    :href="item.view_url"
                    target="_blank"
                    class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 hover:text-white active:text-white transition-colors"
                  >
                    View
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Failed Posts -->
        <div v-if="results.failed.length > 0">
          <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
            Failed ({{ results.failed.length }})
          </h4>
          <div class="space-y-2 max-h-64 overflow-y-auto">
            <div
              v-for="(item, index) in results.failed"
              :key="`failed-${index}`"
              class="bg-red-50 border border-red-200 rounded-lg p-4"
            >
              <p class="text-sm font-semibold text-gray-900">{{ item.title }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ item.message }}</p>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="results.created.length === 0 && results.failed.length === 0 && results.total > 0" class="text-center py-8">
          <InformationCircleIcon class="w-16 h-16 text-gray-300 mx-auto mb-2" />
          <p class="text-gray-600">No results to display</p>
        </div>
      </div>

      <template #footer>
        <button
          @click="copyCreatedList"
          :disabled="results.created.length === 0"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Copy IDs/URLs
        </button>
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import {
  ArrowPathIcon,
  CheckCircleIcon,
  DocumentTextIcon,
  ExclamationTriangleIcon,
  FlagIcon,
  InformationCircleIcon,
  PlusCircleIcon
} from '@heroicons/vue/24/outline'
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
const selectedPostType = ref('page')
const selectedStatus = ref('draft')
const titlesInput = ref('')
const skipEmptyLines = ref(true)
const allowDuplicates = ref(false)
const generating = ref(false)
const showResults = ref(false)
const results = ref({
  created: [],
  failed: [],
  total: 0
})
const maxTitles = 20

const titleCount = computed(() => {
  if (!titlesInput.value.trim()) {
    return 0
  }

  const rawLines = titlesInput.value.split('\n').map(line => line.trim())
  return rawLines.filter(line => line.length > 0).length
})

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

      if (!postTypes.value.find(type => type.value === selectedPostType.value)) {
        selectedPostType.value = postTypes.value[0]?.value || 'page'
      }
    }
  } catch (error) {
    console.error('Failed to load post types:', error)
  }
})

// Generate posts
const generatePosts = async () => {
  if (!titlesInput.value.trim()) {
    showToast('Please enter at least one title', 'error')
    return
  }

  if (titleCount.value > maxTitles) {
    showToast(`Please limit your list to ${maxTitles} titles`, 'error')
    return
  }

  generating.value = true

  try {
    const response = await axios.post(
      window.bpDevToolsAdmin.ajaxUrl,
      new URLSearchParams({
        action: 'bp_dev_tools_bulk_create_posts',
        nonce: window.bpDevToolsAdmin.nonce,
        titles: titlesInput.value,
        post_type: selectedPostType.value,
        post_status: selectedStatus.value,
        skip_empty: skipEmptyLines.value,
        allow_duplicates: allowDuplicates.value
      })
    )

    if (response.data.success) {
      results.value = response.data.data
      showResults.value = true
      showToast(`Generated ${response.data.data.created.length} of ${response.data.data.total} items`, 'success')
    } else {
      showToast(response.data.data?.message || 'Generation failed', 'error')
    }
  } catch (error) {
    console.error('Generation error:', error)
    showToast('An error occurred while generating posts', 'error')
  } finally {
    generating.value = false
  }
}

const copyCreatedList = async () => {
  if (!results.value.created.length) {
    return
  }

  const lines = results.value.created.map(item => {
    return `ID: ${item.post_id} | URL: ${item.view_url} | Title: ${item.title}`
  })

  const text = lines.join('\n')

  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)
      showToast('Copied IDs and URLs to clipboard', 'success')
      return
    }

    const textarea = document.createElement('textarea')
    textarea.value = text
    document.body.appendChild(textarea)
    textarea.select()
    document.execCommand('copy')
    document.body.removeChild(textarea)
    showToast('Copied IDs and URLs to clipboard', 'success')
  } catch (error) {
    console.error('Copy error:', error)
    showToast('Failed to copy results', 'error')
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
