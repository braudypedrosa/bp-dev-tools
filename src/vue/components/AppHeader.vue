<template>
  <div class="bg-gradient-to-r from-primary-start to-primary-end text-white px-10 py-8 shadow-lg">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl text-white font-bold mb-2 flex items-center gap-4">
          BP Development Tools
        </h1>
        <p class="text-white/90 text-lg">
          Manage your development tools and utilities
        </p>
      </div>
      <div>
        <span class="inline-flex items-center px-4 py-2 bg-white/25 backdrop-blur-md rounded-full text-white font-semibold text-sm">
          {{ enabledCount }} Tool{{ enabledCount !== 1 ? 's' : '' }} Enabled
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  enabledCount: {
    type: Number,
    default: 0
  }
})
</script>
