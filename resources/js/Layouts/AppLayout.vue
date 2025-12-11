<script setup>
// Minimal layout for Inertia pages
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">
    <main class="container mx-auto p-6">
      <slot />
    </main>
  </div>
</template>

<style scoped>
/* minimal reset */
:root { font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
</style>
