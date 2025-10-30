<script setup>
import { onMounted, ref } from 'vue'
import { Moon, Sun } from 'lucide-vue-next';

const isDark = ref(false)

function applyTheme(dark) {
  const root = document.documentElement
  if (dark) root.classList.add('dark')
  else root.classList.remove('dark')
  localStorage.setItem('theme', dark ? 'dark' : 'light')
  isDark.value = dark
}

function toggleTheme() {
  applyTheme(!isDark.value)
}

onMounted(() => {
  // 1) respektera sparad preferens
  const saved = localStorage.getItem('theme')
  if (saved === 'dark' || saved === 'light') {
    applyTheme(saved === 'dark')
    return
  }
  // 2) annars matcha systemläge
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  applyTheme(prefersDark)
})
</script>

<template>
  <button
    @click="toggleTheme"
    class="inline-flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm
           hover:bg-muted transition"
  >
    <span v-if="isDark"><Sun class="size-5 text-white"/></span>
    <span v-else><Moon class="size-5" /></span>
  </button>
</template>
