<script setup lang="ts">
import { dashboard, login, register } from '@/routes'
import { Head, Link } from '@inertiajs/vue3'
import ThemeToggle from '@/components/ThemeToggle.vue'
import ExplainerFlow from '@/components/ExplainerFlow.vue'
import TryDropzone from '@/components/TryDropzone.vue'

// shadcn-vue (se till att du har genererat dessa med CLI)
import { Button } from '@/components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'

withDefaults(
    defineProps<{
        canRegister: boolean
    }>(),
    {
        canRegister: true,
    },
)
</script>

<template>

    <Head title="Dela filer säkert" />

    <!-- Page -->
    <div class="min-h-screen bg-white text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
        <!-- Topbar -->
        <header class="w-full border-b border-neutral-200/60 dark:border-neutral-800/60">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-2">
                    <!-- Simple lock logo -->
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-200/70 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M7 10V8a5 5 0 1 1 10 0v2" stroke-width="1.5" />
                            <rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.5" />
                            <circle cx="12" cy="15" r="1.25" stroke-width="1.5" />
                        </svg>
                    </div>
                    <span class="text-base font-semibold">Hemli.se</span>
                </div>

                <div class="flex items-center gap-3">
                    <ThemeToggle />

                    <Link v-if="$page.props.auth.user" :href="dashboard()
                        " class="hidden rounded-md border border-neutral-200/70 px-4 py-1.5 text-sm hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900 md:inline-block">
                    Dashboard
                    </Link>

                    <template v-else>
                        <Link :href="login()"
                            class="hidden rounded-md px-4 py-1.5 text-sm hover:underline md:inline-block">
                        Logga in
                        </Link>

                        <Link v-if="canRegister" :href="register()"
                            class="hidden rounded-md border border-neutral-200/70 px-4 py-1.5 text-sm hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900 md:inline-block">
                        Skapa konto
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Main content with unified vertical rhythm -->
        <main class="mx-auto max-w-6xl px-6">
            <div class="space-y-12 md:space-y-16">
                <!-- Hero -->
                <section class="pt-12 md:pt-16">
                    <div class="mx-auto max-w-3xl text-center">
                        <p
                            class="mb-3 inline-block rounded-full border border-neutral-200/70 px-3 py-1 text-xs tracking-wide text-neutral-600 dark:border-neutral-800 dark:text-neutral-300">
                            End-to-end-kryptering • Zero-knowledge design
                        </p>

                        <h1 class="text-balance text-4xl font-extrabold leading-tight md:text-5xl">
                            Krypterad filuppladdning,
                            <span class="bg-gradient-to-r from-indigo-500 to-sky-500 bg-clip-text text-transparent">på
                                riktigt.</span>
                        </h1>

                        <p class="mx-auto mt-4 max-w-2xl text-pretty text-base text-neutral-600 dark:text-neutral-300">
                            Ladda upp, lagra och dela filer utan att någon annan kan läsa dem — inte ens vi.
                            Din nyckel. Dina regler.
                        </p>

                        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <Button v-if="canRegister" asChild>
                                <Link :href="register()">Kom igång gratis</Link>
                            </Button>

                            <Button asChild variant="secondary">
                                <Link :href="login()">Jag har redan konto</Link>
                            </Button>
                        </div>

                        <!-- Social proof / stats -->
                        <div
                            class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm text-neutral-500 dark:text-neutral-400">
                            <span>📦 Obegränsat antal uppladdningar*</span>
                            <span>🔐 AES-256 + klient-sidig kryptering</span>
                            <span>🕵️ Zero-knowledge</span>
                        </div>
                    </div>
                </section>

                <!-- Encryption flow -->
                <section>
                    <ExplainerFlow />
                </section>

                <!-- TryDropzone -->
                <section id="prova">
                    <TryDropzone />
                </section>

                <!-- Features -->
                <section>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <Card class="border-neutral-200/70 dark:border-neutral-800">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <span
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-neutral-200/70 dark:border-neutral-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
                                            <path d="M7 10V8a5 5 0 1 1 10 0v2" stroke-width="1.5" />
                                            <rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.5" />
                                        </svg>
                                    </span>
                                    End-to-end-kryptering
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-neutral-600 dark:text-neutral-300">
                                Kryptera innan filen lämnar din enhet. Nycklarna stannar hos dig — vilket betyder att
                                bara du
                                (och de du
                                delar med) kan läsa innehållet.
                            </CardContent>
                        </Card>

                        <Card class="border-neutral-200/70 dark:border-neutral-800">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <span
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-neutral-200/70 dark:border-neutral-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
                                            <path d="M3 6h18M3 12h18M3 18h18" stroke-width="1.5" />
                                        </svg>
                                    </span>
                                    Enkelt & snabbt
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-neutral-600 dark:text-neutral-300">
                                Dra-och-släpp, generera delningslänkar och hantera åtkomst med ett klick.
                            </CardContent>
                        </Card>

                        <Card class="border-neutral-200/70 dark:border-neutral-800">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <span
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-neutral-200/70 dark:border-neutral-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="1.5" />
                                            <path d="M7 10l5-5 5 5" stroke-width="1.5" />
                                        </svg>
                                    </span>
                                    Sekretess först
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-neutral-600 dark:text-neutral-300">
                                Zero-knowledge: vi kan inte läsa dina filer. Loggar minimeras och all metadata hålls
                                till ett
                                absolut
                                minimum.
                            </CardContent>
                        </Card>
                    </div>
                </section>

                <!-- How it works -->
                <section>
                    <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
                        <div class="rounded-2xl border border-neutral-200/70 p-6 dark:border-neutral-800">
                            <h2 class="text-xl font-semibold">Så funkar det</h2>
                            <ol class="mt-4 space-y-3 text-sm text-neutral-600 dark:text-neutral-300">
                                <li class="flex gap-3">
                                    <span
                                        class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full border border-neutral-200/70 text-xs dark:border-neutral-800">1</span>
                                    Skapa konto och logga in.
                                </li>
                                <li class="flex gap-3">
                                    <span
                                        class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full border border-neutral-200/70 text-xs dark:border-neutral-800">2</span>
                                    Välj filer — de krypteras lokalt med din nyckel.
                                </li>
                                <li class="flex gap-3">
                                    <span
                                        class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full border border-neutral-200/70 text-xs dark:border-neutral-800">3</span>
                                    Dela säkert med länk eller bjud in specifika mottagare.
                                </li>
                            </ol>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <Button v-if="canRegister" asChild>
                                    <Link :href="register()">Skapa konto</Link>
                                </Button>
                                <Button asChild variant="secondary">
                                    <Link :href="login()">Logga in</Link>
                                </Button>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border border-dashed border-neutral-200/70 p-8 text-center dark:border-neutral-800">
                            <div
                                class="mx-auto flex h-20 w-20 items-center justify-center rounded-xl border border-neutral-200/70 dark:border-neutral-800">
                                <!-- Simple upload icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path d="M12 16V4" stroke-width="1.5" />
                                    <path d="M8 8l4-4 4 4" stroke-width="1.5" />
                                    <rect x="3" y="16" width="18" height="4" rx="1.5" stroke-width="1.5" />
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-neutral-600 dark:text-neutral-300">
                                Dra-och-släpp uppladdning direkt i webben. Kryptering sker innan filerna lämnar din
                                enhet.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Bottom CTA -->
                <section>
                    <div
                        class="mb-5 rounded-2xl border border-neutral-200/70 bg-neutral-50 p-8 text-center dark:border-neutral-800 dark:bg-neutral-900/40">
                        <h3 class="text-lg font-semibold">Redo att skydda dina filer?</h3>
                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
                            Skapa ett konto gratis och börja ladda upp på några sekunder.
                        </p>
                        <div class="mt-5 flex justify-center gap-3">
                            <Button v-if="canRegister" asChild>
                                <Link :href="register()">Kom igång</Link>
                            </Button>
                            <Button asChild variant="secondary">
                                <Link :href="login()">Logga in</Link>
                            </Button>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="border-t border-neutral-200/60 py-8 text-center text-xs text-neutral-500 dark:border-neutral-800/60 dark:text-neutral-400">
            <div class="mx-auto max-w-6xl px-6">
                <p>* Begränsningar kan gälla beroende på plan. All kryptering sker klient-sidigt.</p>
                <p class="mt-1">&copy; {{ new Date().getFullYear() }} Hemli.se — Alla rättigheter förbehålls.</p>
            </div>
        </footer>
    </div>
</template>
