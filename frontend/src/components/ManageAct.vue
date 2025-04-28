<template>
  <div class="container">
    <!-- Header -->
    <div class="page-header">
      <div class="left">
        <button class="back-button" @click="goBack">⬅</button>
      </div>
      <div class="center">
        <h1 class="app-title">Smartkartika</h1>
      </div>
      <div class="right"></div> <!-- dummy agar kiri dan kanan seimbang -->
    </div>

    <!-- Konten -->
    <main class="content">
      <h2 class="subtitle">KELOLA KEGIATAN</h2>

      <div class="menu-list">
        <div class="menu-item" @click="navigateTo('add')">
          <div class="icon">✏️</div>
          <span class="menu-text">TAMBAH KEGIATAN</span>
        </div>

        <div class="menu-item" @click="navigateTo('edit')">
          <div class="icon">📝</div>
          <span class="menu-text">EDIT KEGIATAN</span>
        </div>

        <div class="menu-item" @click="navigateTo('remove')">
          <div class="icon">🗑️</div>
          <span class="menu-text">HAPUS KEGIATAN</span>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goBack() {
  router.push('/dashboard')
}

function navigateTo(page) {
  router.push(`/${page}`)
}
</script>

<style scoped>
/* Container full height */
.container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
}

/* Header */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  padding: 1rem;
}

.left, .center, .right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.left {
  justify-content: flex-start;
}

.right {
  justify-content: flex-end;
}

.back-button {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.5rem;
  cursor: pointer;
  font-weight: bold;
}

.app-title {
  font-size: 1.3rem;
  font-style: italic;
  font-weight: bold;
  color: #fff;
}

/* Main Content */
.content {
  flex: 1;
  padding: 1.5rem 1rem;
}

.subtitle {
  text-align: center;
  font-size: 1.5rem;
  margin-bottom: 2rem;
  font-weight: bold;
}

/* Menu List */
.menu-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Menu Item */
.menu-item {
  background-color: #a07250;
  color: white;
  padding: 1rem;
  border-radius: 12px;
  text-align: center;
  font-weight: bold;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.menu-item:hover {
  background-color: #8b5e3f;
}

.icon {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  background-color: #fff;
  border-radius: 15px;
  width: 30%;
}

.menu-text {
  font-size: 1.5rem;
  font-weight: bold;
}

/* Responsive for desktop */
@media (min-width: 768px) {
  .page-header {
    display: none;
  }

  .content {
    padding: 2rem 6rem; /* padding lebih besar saat desktop */
    max-width: 100%; /* supaya tidak terlalu lebar di layar besar */
    margin: 0 auto; /* tengahin konten */
    border-radius: 12px; /* opsional: kasih radius sudut */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* opsional: kasih bayangan biar elegan */
  }

  .menu-list {
    flex-direction: row;
    justify-content: center;
    width: 100%;
  }

  .menu-item {
    width: 100%;
  }
}
</style>
