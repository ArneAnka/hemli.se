<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'

// Props från controller
const page = usePage()
const props = defineProps<{
  token: string
  name: string
  size: number
  expires_at: string
  used_at?: string | null
  expired?: boolean
}>()

const status = ref<'idle' | 'fetching' | 'decrypt' | 'done' | 'error'>('idle')
const error = ref<string | null>(null)
const downloadUrl = ref<string | null>(null)

// base64url -> ArrayBuffer
function b64uToBytes(b64u: string): Uint8Array {
  const b64 = b64u.replace(/-/g, '+').replace(/_/g, '/')
  const pad = '='.repeat((4 - (b64.length % 4)) % 4)
  const s = atob(b64 + pad)
  const bytes = new Uint8Array(s.length)
  for (let i = 0; i < s.length; i++) bytes[i] = s.charCodeAt(i)
  return bytes
}

async function run() {
  try {
    status.value = 'fetching'

    // Hämta iv från query & key från fragment
    const url = new URL(window.location.href)
    const ivB64u = url.searchParams.get('iv')
    const hash = url.hash.replace(/^#/, '')
    const keyB64u = new URLSearchParams(hash).get('key') || hash.replace(/^key=/, '') // stöd för #key=.. eller bara #..

    if (!ivB64u || !keyB64u) {
      throw new Error('Nyckeln eller IV saknas.')
    }

    const iv = b64uToBytes(ivB64u)
    const rawKey = b64uToBytes(keyB64u)

    // 1) Hämta KRYPTTEXT-bytar
    const resp = await fetch(`/try/blob/${props.token}`, { cache: 'no-store' })
    if (!resp.ok) throw new Error('Kunde inte hämta data.')
    const cipher = new Uint8Array(await resp.arrayBuffer())

    status.value = 'decrypt'

    // 2) Dekryptera i webbläsaren
    const key = await crypto.subtle.importKey('raw', rawKey, { name: 'AES-GCM' }, false, ['decrypt'])
    const plainBuf = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, key, cipher)

    // 3) Skapa nedladdningslänk
    const blob = new Blob([plainBuf])
    downloadUrl.value = URL.createObjectURL(blob)
    status.value = 'done'

    // Auto-öppna nedladdning
    setTimeout(() => {
      const a = document.createElement('a')
      a.href = downloadUrl.value!
      a.download = props.name.replace(/\.enc$/i, '')
      document.body.appendChild(a)
      a.click()
      a.remove()
    }, 300)
  } catch (e: any) {
    error.value = e?.message || 'Något gick fel.'
    status.value = 'error'
  }
}

onMounted(() => {
  if (props.expired) {
    status.value = 'error'
    error.value = 'Länken har gått ut.'
    return
  }
  if (props.used_at) {
    status.value = 'error'
    error.value = 'Länken har redan använts.'
    return
  }
  run()
})
</script>

<template>

  <Head :title="`Hämtar: ${props.name}`" />

  <div class="min-h-screen bg-white text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
    <div class="mx-auto max-w-md px-6 py-16 text-center">
      <h1 class="text-2xl font-bold">Dekryptering</h1>
      <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
        Filen dekrypteras i din webbläsare. Vi kan inte se innehållet.
      </p>

      <!-- OBSERVERA -->
      <p class="mt-3 text-xs font-medium text-amber-700 dark:text-amber-400">
        OBSERVERA: Länken fungerar bara en gång. Efter nedladdning raderas filen automatiskt.
      </p>

      <div class="mt-8 rounded-2xl border border-neutral-200/70 p-6 dark:border-neutral-800">
        <div v-if="status === 'fetching'" class="animate-pulse">Hämtar data…</div>
        <div v-else-if="status === 'decrypt'" class="animate-pulse">Dekrypterar…</div>
        <div v-else-if="status === 'done'">
          <p class="text-sm">Klar! Om nedladdningen inte startade:</p>
          <div class="mt-3"><Button asChild><a :href="downloadUrl!" :download="props.name.replace(/\.enc$/, '')">Ladda
                ned</a></Button></div>
        </div>
        <div v-else-if="status === 'error'" class="text-red-600 text-sm">
          {{ error || 'Länken är förbrukad eller ogiltig.' }}
        </div>
      </div>

      <p class="mt-6 text-xs text-neutral-500 dark:text-neutral-400">
        Länken kan vara tidsbegränsad. Nyckeln stannar alltid i URL-fragmentet (skickas inte till servern).
      </p>
    </div>
  </div>
</template>
