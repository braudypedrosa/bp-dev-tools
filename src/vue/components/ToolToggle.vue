<template>
  <label class="relative inline-flex items-center cursor-pointer">
    <input
      type="checkbox"
      class="sr-only peer"
      :checked="modelValue"
      :disabled="disabled"
      @change="$emit('update:modelValue', $event.target.checked)"
    >
    <div
      :class="[
        'w-[52px] h-7 rounded-full transition-all duration-300',
        'after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px]',
        'after:bg-white after:rounded-full after:h-6 after:w-6',
        'after:transition-all after:duration-300 after:shadow-md',
        'peer-checked:after:translate-x-[24px]',
        'peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
        modelValue
          ? 'bg-gradient-to-r from-primary-start to-primary-end'
          : 'bg-gray-300'
      ]"
    ></div>
  </label>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])
</script>
