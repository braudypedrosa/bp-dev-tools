<template>
  <Transition name="slide-fade">
    <div
      v-if="toast.show"
      :class="[
        'fixed top-8 right-8 px-6 py-4 rounded-lg shadow-2xl z-50',
        'flex items-center gap-3 font-semibold',
        toast.type === 'error'
          ? 'bg-gradient-to-r from-red-500 to-red-600 text-white'
          : 'bg-gradient-to-r from-green-500 to-green-600 text-white'
      ]"
    >
      <span
        :class="[
          'dashicons text-xl',
          toast.type === 'error' ? 'dashicons-warning' : 'dashicons-yes-alt'
        ]"
      ></span>
      <span>{{ toast.message }}</span>
    </div>
  </Transition>
</template>

<script setup>
import { useToast } from '@store/tools'

const { state: toast } = useToast()
</script>
