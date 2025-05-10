<template>
  <div class="laporan-container">
    <!-- Mobile Header -->
    <div class="page-header">
      <div class="left">
        <button class="back-button" @click="goBack">
          <img src="@/assets/arrow-left.png" alt="Kembali" />
        </button>
      </div>
      <div class="center">
        <h1 class="app-title">Smartkartika</h1>
      </div>
      <div class="right"></div>
    </div>

    <!-- Daftar Laporan -->
    <div class="form-laporan">
      <h2 class="title">DAFTAR LAPORAN</h2>

      <div class="laporan-list">
        <div v-for="(laporan, index) in daftarLaporan" :key="index" class="laporan-card">
          <div class="laporan-content">
            <h3 class="laporan-judul">{{ laporan.judul }}</h3>
            <p class="laporan-tanggal">📅 {{ laporan.tanggal }}</p>
            <p class="laporan-pengunggah">👤 {{ laporan.pengunggah }}</p>
          </div>
          <div class="laporan-actions">
            <button class="laporan-button">Unduh</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const goBack = () => {
  router.back()
}

// Contoh data laporan statis (ganti dengan API fetch bila diperlukan)
const daftarLaporan = ref([
  {
    judul: 'Laporan Evaluasi Bulanan',
    tanggal: '08 Mei 2025',
    pengunggah: 'Admin',
  },
  {
    judul: 'Kegiatan Donor Darah',
    tanggal: '01 Mei 2025',
    pengunggah: 'Budi',
  },
  {
    judul: 'Laporan Penutupan Acara',
    tanggal: '25 April 2025',
    pengunggah: 'Siti',
  },
])
</script>

<style scoped>
.laporan-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  padding: 0;
  margin: 0;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background:
    linear-gradient(rgba(44, 57, 48, 0.93), rgba(44, 57, 48, 0.93)), url('@/assets/bg.png');
}

.left,
.center,
.right {
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
  background: transparent;
  border: none;
  padding: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.app-title {
  font-size: 1.3rem;
  font-style: italic;
  font-weight: bold;
  color: #fff;
}

.form-laporan {
  flex: 1;
  background-color: #fff;
  padding: 1rem;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  height: 100%;
  width: 100vw;
  box-sizing: border-box;
  overflow-y: auto;
}

.title {
  text-align: center;
  font-weight: bold;
  color: #1f3a2d;
  font-size: 1.3rem;
  margin-bottom: 1rem;
}

.laporan-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.laporan-card {
  background-color: #f5f5f5;
  border-radius: 10px;
  padding: 1rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.laporan-content {
  margin-bottom: 0.5rem;
}

.laporan-judul {
  font-size: 1rem;
  font-weight: bold;
  color: #333;
}

.laporan-tanggal,
.laporan-pengunggah {
  font-size: 0.85rem;
  color: #555;
  margin-top: 2px;
}

.laporan-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.laporan-button {
  padding: 6px 12px;
  font-size: 0.8rem;
  background-color: #7d726a;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.laporan-button:hover {
  background-color: #b39779;
}

@media (min-width: 768px) {
  .page-header {
    display: none;
  }

  .form-laporan {
    min-height: calc(100vh - 84px);
    width: 100%;
    max-width: 800%;
  }

  .title {
    text-align: start;
    font-size: 1.5rem;
  }
}
</style>
