<template>
  <header class="header">
    <!-- Tombol garis tiga hanya muncul di mobile -->
    <button
      class="hamburger"
      @click="$emit('toggleSidebar')"
      v-if="isMobile">
      ☰
    </button>
    <h1 class="app-name">Smartkartika</h1>
  </header>
</template>

<script>
export default {
  props: ['isMobile']
}
</script>

<style scoped>
.header {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px 16px;
  color: white;
  position: relative;
}

.hamburger {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  margin-right: 12px;
  cursor: pointer;
  font-weight: bold;
}

.app-name {
  font-style: italic;
  font-size: 24px;
  font-weight: bold;
}

@media(min-width: 769px) {
  .header {
    justify-content: flex-start;
  }
  .app-name {
    font-size: 2.5rem;
    font-style: italic;
    margin: 0;
    text-align: left;
  }
}
</style>
