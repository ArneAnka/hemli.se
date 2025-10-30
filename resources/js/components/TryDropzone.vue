<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { Button } from '@/components/ui/button'
import { Copy, FileUp, RefreshCcw, X, Sparkles, MousePointerClick, ChevronDown } from 'lucide-vue-next'

const MAX_BYTES = 5 * 1024 * 1024

const file = ref<File | null>(null)
const dragging = ref(false)
const step = ref<'idle' | 'encrypt' | 'upload' | 'done'>('idle')
const progress = ref(0)
const error = ref<string | null>(null)
const shareUrl = ref<string | null>(null)
const copied = ref(false)
const tooBig = computed(() => file.value && file.value.size > MAX_BYTES)

// --- Scroll reveal state/refs ---
const cardRef = ref<HTMLElement | null>(null)
const dzRef = ref<HTMLElement | null>(null)
const actionsRef = ref<HTMLElement | null>(null)
const cardVisible = ref(false)
const dzVisible = ref(false)
const actionsVisible = ref(false)
let io: IntersectionObserver | null = null

onMounted(() => {
  // Respektera prefers-reduced-motion
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        const el = e.target as HTMLElement
        const isVisible = e.isIntersecting
        if (el === cardRef.value) cardVisible.value = cardVisible.value || isVisible
        if (el === dzRef.value) dzVisible.value = dzVisible.value || isVisible
        if (el === actionsRef.value) actionsVisible.value = actionsVisible.value || isVisible
        // När den väl visats en gång, låt den vara synlig (ingen reverse animation)
        if (prefersReduced && isVisible) io?.unobserve(el)
      })
    },
    { rootMargin: '0px 0px -10% 0px', threshold: 0.15 }
  )

  if (cardRef.value) io.observe(cardRef.value)
  if (dzRef.value) io.observe(dzRef.value)
  if (actionsRef.value) io.observe(actionsRef.value)
})

onBeforeUnmount(() => {
  io?.disconnect()
  io = null
})

// --- helpers: base64url ---
function b64u(buf: ArrayBuffer): string {
  const b64 = btoa(String.fromCharCode(...new Uint8Array(buf)))
  return b64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '')
}
function b64uFromBytes(bytes: Uint8Array): string {
  return b64u(bytes.buffer)
}

// --- AES-GCM ---
async function encryptFile(plain: ArrayBuffer) {
  const key = await crypto.subtle.generateKey({ name: 'AES-GCM', length: 256 }, true, ['encrypt'])
  const iv = crypto.getRandomValues(new Uint8Array(12))
  const cipher = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, plain)
  const rawKey = await crypto.subtle.exportKey('raw', key)
  return {
    cipher: new Uint8Array(cipher),
    ivB64u: b64uFromBytes(iv),
    keyB64u: b64u(rawKey),
  }
}

function onPick(e: Event) {
  const input = e.target as HTMLInputElement
  if (input.files && input.files[0]) {
    file.value = input.files[0]
    error.value = null
    shareUrl.value = null
    step.value = 'idle'
    progress.value = 0
  }
}
function onDrop(e: DragEvent) {
  e.preventDefault()
  dragging.value = false
  if (e.dataTransfer?.files?.[0]) {
    file.value = e.dataTransfer.files[0]
    error.value = null
    shareUrl.value = null
    step.value = 'idle'
    progress.value = 0
  }
}
function onDrag(e: DragEvent) {
  e.preventDefault()
  dragging.value = e.type === 'dragover'
}

async function upload() {
  if (!file.value) return
  if (tooBig.value) {
    error.value = 'Filen är större än 5 MB.'
    return
  }
  error.value = null
  shareUrl.value = null
  copied.value = false

  try {
    // 1) Läs fil
    step.value = 'encrypt'
    progress.value = 10
    const plain = await file.value.arrayBuffer()

    // 2) Kryptera
    const { cipher, ivB64u, keyB64u } = await encryptFile(plain)
    progress.value = 35

    // 3) Ladda upp krypterad blob
    step.value = 'upload'
    const form = new FormData()
    const encBlob = new Blob([cipher], { type: 'application/octet-stream' })
    form.append('file', encBlob, file.value.name + '.enc')

    const res = await axios.post('/try/upload', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (e) => {
        if (e.total) {
          const base = 35
          const span = 60
          progress.value = Math.min(95, base + Math.round((e.loaded / e.total) * span))
        }
      },
    })

    const finalView = `${res.data.view_url}?iv=${ivB64u}#key=${keyB64u}`

    progress.value = 100
    step.value = 'done'
    shareUrl.value = finalView
  } catch (e: any) {
    console.error(e)
    error.value = e?.response?.data?.message || 'Det gick inte att ladda upp just nu.'
    step.value = 'idle'
    progress.value = 0
  }
}

function resetAll() {
  file.value = null
  progress.value = 0
  shareUrl.value = null
  error.value = null
  step.value = 'idle'
  copied.value = false
}

async function copyShare() {
  if (!shareUrl.value) return
  await navigator.clipboard.writeText(shareUrl.value)
  copied.value = true
  setTimeout(() => (copied.value = false), 1400)
}

// UI helper: statusprickarnas färg
function dotClass(which: 'encrypt' | 'upload' | 'done') {
  let cls = 'bg-neutral-300 dark:bg-neutral-700'
  if (which === 'encrypt') {
    if (step.value === 'encrypt') cls = 'bg-amber-500 animate-pulse'
    if (step.value === 'upload' || step.value === 'done') cls = 'bg-green-500'
  } else if (which === 'upload') {
    if (step.value === 'upload') cls = 'bg-amber-500 animate-pulse'
    if (step.value === 'done') cls = 'bg-green-500'
  } else if (which === 'done') {
    if (step.value === 'done') cls = 'bg-green-500'
  }
  return `inline-block h-2 w-2 rounded-full ${cls}`
}
</script>

<template>
  <!-- POP WRAPPER -->
  <div class="relative isolate">
    <!-- Gradient glow bakom kortet -->
    <div
      class="pointer-events-none absolute -inset-1 -z-10 rounded-3xl bg-gradient-to-r from-indigo-500/30 via-sky-500/30 to-emerald-500/30 blur-2xl opacity-60 dark:opacity-40"
      aria-hidden="true"
    ></div>

    <!-- Själva kortet -->
    <div
      ref="cardRef"
      class="relative z-10 rounded-2xl border border-neutral-200/70 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md hover:ring-1 hover:ring-indigo-500/20 dark:border-neutral-800 dark:bg-neutral-950
             motion-safe:will-change-[opacity,transform]
             "
      :class="cardVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-3 scale-[0.98]'"
    >
      <!-- Liten banderoll -->
<div
  class="absolute -top-3 left-4 z-20 inline-flex items-center gap-1
         rounded-full bg-white px-3 py-1 text-[11px] font-medium
         text-amber-800 shadow-sm ring-1 ring-amber-500/40
         dark:bg-neutral-950 dark:text-amber-200"
>
      <Sparkles class="h-3.5 w-3.5 text-amber-500" aria-hidden="true" /> Prova utan konto
</div>
    

      <!-- Callout i toppen -->
      <div
        class="mx-auto mb-4 flex max-w-md items-center justify-center gap-2 rounded-lg border border-neutral-200/70 bg-neutral-50 px-3 py-2 text-xs text-neutral-700 dark:border-neutral-800 dark:bg-neutral-900/40 dark:text-neutral-200
               motion-safe:transition
               "
        :class="cardVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'"
      >
        <MousePointerClick class="h-4 w-4" aria-hidden="true" />
        Testa tjänsten direkt – krypterat och gratis upp till 5&nbsp;MB.
      </div>

      <h2 class="text-center text-lg font-semibold">Ladda upp en testfil</h2>
      <p class="mt-1 text-center text-sm text-neutral-600 dark:text-neutral-300">
        Filen krypteras i din webbläsare innan den laddas upp.
      </p>

      <!-- STEPS / ANIMATIONS -->
      <div class="mt-4 flex items-center justify-center gap-4 text-xs text-neutral-600 dark:text-neutral-300">
        <div class="flex items-center gap-2">
          <span :class="dotClass('encrypt')"></span>
          Krypterar
        </div>
        <div class="flex items-center gap-2">
          <span :class="dotClass('upload')"></span>
          Laddar upp
        </div>
        <div class="flex items-center gap-2">
          <span :class="dotClass('done')"></span>
          Klar
        </div>
      </div>

      <!-- Nedåtpil som pekar mot dropzonen -->
      <div class="mt-2 flex justify-center">
        <ChevronDown class="h-5 w-5 animate-bounce text-neutral-400 dark:text-neutral-500" aria-hidden="true" />
      </div>

      <!-- Dropzone -->
      <div
        ref="dzRef"
        class="mt-4 rounded-xl border border-dashed border-neutral-300 p-6 text-center transition-colors dark:border-neutral-700
               motion-safe:transition
               "
        :class="[
          dragging ? 'bg-neutral-50 dark:bg-neutral-900/40' : '',
          dzVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'
        ]"
        @dragover="onDrag" @dragleave="onDrag" @drop="onDrop"
      >
        <p class="text-sm">Dra hit en fil, eller välj från enhet</p>

        <!-- Filinput (syns inte, styrs med label) -->
        <input id="try-file" type="file" class="sr-only" @change="onPick" />

        <div
          ref="actionsRef"
          class="mt-3 flex flex-wrap items-center justify-center gap-3 motion-safe:transition"
          :class="actionsVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'"
        >
          <!-- Visa “Välj fil” bara när ingen fil är vald -->
          <Button v-if="!file" asChild>
            <label for="try-file" class="inline-flex cursor-pointer items-center gap-2">
              <FileUp class="h-4 w-4" aria-hidden="true" /> Välj fil
            </label>
          </Button>

          <!-- När filen är vald: visa byt/rensa + ladda upp -->
          <template v-else>
            <Button asChild variant="secondary">
              <label for="try-file" class="inline-flex cursor-pointer items-center gap-2">
                <RefreshCcw class="h-4 w-4" aria-hidden="true" /> Välj annan fil
              </label>
            </Button>
            <Button variant="ghost" @click="resetAll" class="inline-flex items-center gap-2">
              <X class="h-4 w-4" aria-hidden="true" /> Rensa
            </Button>
            <Button @click="upload" :disabled="tooBig" class="inline-flex items-center gap-2">
              <FileUp class="h-4 w-4" aria-hidden="true" /> Ladda upp
            </Button>
          </template>
        </div>

        <p v-if="file" class="mt-3 text-xs text-neutral-600 dark:text-neutral-300">
          Vald fil: <strong>{{ file.name }}</strong> ({{ (file.size/1024/1024).toFixed(2) }} MB)
        </p>
        <p v-if="tooBig" class="mt-2 text-xs text-red-600">Filen är större än 5 MB.</p>

        <!-- Progress bar -->
        <div v-if="step!=='idle'" class="mt-4">
          <div class="h-2 w-full overflow-hidden rounded bg-neutral-200 dark:bg-neutral-800">
            <div
              class="h-2 rounded bg-neutral-900 transition-[width] duration-300 dark:bg-neutral-100"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
          <p class="mt-2 text-xs text-neutral-600 dark:text-neutral-300">{{ progress }}%</p>
        </div>

        <!-- Result -->
        <div v-if="shareUrl" class="mt-5 space-y-2">
          <p class="text-sm">Klar! Dela länken här nedan:</p>
          <p class="break-all rounded-md border border-neutral-200/70 p-2 text-xs dark:border-neutral-800">
            {{ shareUrl }}
          </p>
          <!-- Behåll toast nära knappen — gör containern relative -->
          <div class="relative flex items-center justify-center gap-2">
            <Button @click="copyShare" class="inline-flex items-center gap-2">
              <Copy class="h-4 w-4" aria-hidden="true" /> Kopiera till urklipp
            </Button>
            <Button variant="secondary" @click="resetAll">Ladda upp en till</Button>

            <!-- Kopierat-toast nära knappen -->
            <transition name="fade-scale">
              <div
                v-if="copied"
                class="absolute -bottom-12 flex items-center gap-2 rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm font-medium shadow-lg dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100"
                role="status" aria-live="polite"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Kopierat!
              </div>
            </transition>
          </div>

          <!-- OBSERVERA -->
          <p class="text-center text-xs font-medium text-amber-700 dark:text-amber-400">
            OBSERVERA: Länken fungerar bara en gång. Efter nedladdning raderas filen automatiskt.
          </p>
        </div>

        <p v-if="error" class="mt-3 text-xs text-red-600">{{ error }}</p>
      </div>

      <p class="mt-4 text-center text-xs text-neutral-500 dark:text-neutral-400">
        Länken öppnar en sida som dekrypterar filen i webbläsaren och startar nedladdning.
      </p>
    </div>
  </div>
</template>

<style scoped>
/* Toast animation */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.18s ease-out;
}
.fade-scale-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(4px);
}
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(4px);
}
</style>
