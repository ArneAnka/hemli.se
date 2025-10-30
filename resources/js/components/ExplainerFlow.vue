<script setup lang="ts">
import { Smartphone, Shield, Server, ShieldCheck, ArrowRight } from 'lucide-vue-next'
</script>

<template>
  <section
    class="rounded-3xl border border-neutral-200/70 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950 md:p-10"
    aria-label="Så funkar Hemli.se"
  >
    <div class="mb-6 md:mb-8 text-center">
      <h2 class="text-2xl font-bold md:text-3xl">Krypterad överföring</h2>
      <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
        Det här är standardläget på Hemli.se – enklast och säkrast. Allt sker i din webbläsare.
      </p>
    </div>

    <!-- Desktop: horisontellt flöde med jämna kolumner -->
    <div class="hidden md:grid md:grid-cols-[1fr_auto_1fr_auto_1fr_auto_1fr] md:items-center md:gap-6">
      <!-- 1 -->
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <Smartphone class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Din enhet</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
          Du väljer filen du vill skicka.
        </p>
      </div>

      <ArrowRight class="mx-auto h-6 w-6 opacity-70" aria-hidden="true" />

      <!-- 2 -->
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <Shield class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Kryptering i webbläsaren</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
          Filen krypteras lokalt. Nyckeln stannar hos dig.
        </p>
      </div>

      <ArrowRight class="mx-auto h-6 w-6 opacity-70" aria-hidden="true" />

      <!-- 3 -->
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <Server class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Hemli.se-servern</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
          Endast den <em>oläsliga</em> krypterade filen lagras tillfälligt.
        </p>
      </div>

      <ArrowRight class="mx-auto h-6 w-6 opacity-70" aria-hidden="true" />

      <!-- 4 -->
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <ShieldCheck class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Dekryptering i webbläsaren</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
          Mottagarens webbläsare dekrypterar med delad nyckel.
        </p>
      </div>
    </div>

    <!-- Mobil: vertikalt flöde med centrerade pilar -->
    <div class="md:hidden">
      <div class="flex flex-col items-center text-center">
        <!-- 1 -->
        <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <Smartphone class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Din enhet</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Du väljer filen du vill skicka.</p>

        <ArrowRight class="my-4 h-6 w-6 rotate-90 opacity-70" aria-hidden="true" />

        <!-- 2 -->
        <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <Shield class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Kryptering i webbläsaren</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Filen krypteras lokalt. Nyckeln stannar hos dig.</p>

        <ArrowRight class="my-4 h-6 w-6 rotate-90 opacity-70" aria-hidden="true" />

        <!-- 3 -->
        <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <Server class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Hemli.se-servern</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Endast den <em>oläsliga</em> krypterade filen lagras tillfälligt.</p>

        <ArrowRight class="my-4 h-6 w-6 rotate-90 opacity-70" aria-hidden="true" />

        <!-- 4 -->
        <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-neutral-200/70 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <ShieldCheck class="h-7 w-7" aria-hidden="true" />
        </div>
        <h3 class="mt-3 text-base font-semibold">Dekryptering i webbläsaren</h3>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">Mottagarens webbläsare dekrypterar med delad nyckel.</p>
      </div>
    </div>

    <p class="mt-8 text-center text-xs text-neutral-500 dark:text-neutral-400">
      Hemli.se följer en <strong>zero-knowledge</strong>-modell: vi ser aldrig dina nycklar och kan inte läsa dina filer.
    </p>
  </section>
</template>
