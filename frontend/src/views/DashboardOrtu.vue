<template>
  <div class="dashboard">
    <!-- Header -->
    <HeaderDashboard
      v-if="(!isMobile && !selectedBerita) || (isMobile && !selectedBerita)"
      :isMobile="isMobile"
      :isLoggedIn="true"
      @toggleSidebar="showSidebar = !showSidebar"
    />

    <div v-if="selectedBerita" class="page-header">
      <div class="left">
        <button class="back-button" @click="goBack">
          <img src="@/assets/arrow-left.png" alt="kembali" />
        </button>
      </div>
      <div class="center">
        <h1 class="app-title">Detail Berita</h1>
      </div>
      <div class="right"></div>
    </div>

    <!-- Main content -->
    <div class="main-content">
      <!-- Sidebar atau Back Button -->
      <div class="sidebar-area">
        <Sidebar
          v-if="showSidebar && !selectedBerita"
          :isMobile="isMobile"
          :isLoggedIn="isLoggedIn"
          :userName="userName"
          @close="showSidebar = false"
          @select="(menu) => (selectedMenu = menu)"
        />
      </div>

      <!-- Konten utama -->
      <div class="content-area">
        <div v-if="!selectedBerita" class="greeting-section">
          <div class="greeting-text">
            <p>
              Halo, <span class="user">{{ userName }}</span>
            </p>
            <p>Selamat Datang</p>
          </div>
        </div>
        <div v-if="!selectedBerita" class="berita-list">
          <div
            v-for="berita in beritaList"
            :key="berita.id"
            class="berita-item"
            @click="selectBerita(berita)"
          >
            <img :src="berita.image" alt="berita" class="berita-image" />
            <div class="berita-info">
              <h3 class="berita-title">{{ berita.title }}</h3>
              <p class="berita-subtitle">{{ berita.subtitle }}</p>
            </div>
          </div>
        </div>

        <div v-else class="berita-detail">>
          <div class="berita-card">
            <img :src="selectedBerita.image" alt="detail" class="detail-image" />
          </div>

          <!-- Label Deskripsi dan Box -->
          <div class="detail-description">
            <div class="berita-card-text">
              <h2 class="detail-title">{{ selectedBerita.title }}</h2>
              <p class="detail-subtitle">{{ selectedBerita.subtitle }}</p>
            </div>
            <div class="description-box">
              <textarea
                class="description-textarea"
                :value="selectedBerita.description"
                readonly
              ></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Sidebar from '@/components/SideOrtu.vue'
import HeaderDashboard from '@/components/HeaderOrangTua.vue'

const beritaList = ref([])
const selectedBerita = ref(null)
const isMobile = ref(window.innerWidth <= 768)
const showSidebar = ref(false)
const isLoggedIn = ref(true)
const userName = ref(localStorage.getItem('userName') || 'User')
const selectedMenu = ref('')

const selectBerita = (berita) => {
  selectedBerita.value = berita
}

const goBack = () => {
  selectedBerita.value = null
}

// Fetch berita dari API, sama seperti MainHome
const fetchBerita = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/documentations');
    beritaList.value = response.data.data.map((item) => ({
      id: item.id_document,
      title: item.title,
      subtitle: '', // Jika tidak ada subtitle di backend, bisa dikosongkan
      description: item.description,
      image: item.file_url,
    }));
  } catch (error) {
    console.error('Gagal memuat berita:', error);
  }
}

onMounted(() => {
  fetchBerita()
})

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth <= 768
})
</script>

<style scoped>
.dashboard {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header saat lihat detail berita */
.page-header {
  margin-bottom: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem;
  background:
    linear-gradient(rgba(162, 123, 92, 0.8), rgba(162, 123, 92, 0.8)), url('@/assets/bg.png');
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

.back-button img {
  filter: invert(1);
}

.app-title {
  font-size: 1.3rem;
  font-weight: bold;
  color: #fff;
  margin: 0 auto;
}

/* Main */
.main-content {
  display: flex;
  flex: 1;
  height: 100%;
  overflow: hidden;
  position: relative;
  flex-direction: column;
}

.content-area {
  flex: 1;
  overflow-y: auto; /* scrollable di sini */
  display: flex;
  flex-direction: column;
  background-color: #fff;
}

/* Welcome Section */
.greeting-section {
  position: sticky;
  top: 0;
  background-color: #fff;
  z-index: 10;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  border-bottom: 1px solid #ccc;
  margin-top: 0;
  margin-bottom: 0.5rem;
}

.greeting-text {
  font-size: 0.9rem;
  color: #2c3930;
  margin-left: 0.8rem;
}
.greeting-text p {
  font-weight: bold;
  color: #626262;
}

.greeting-text .user {
  font-weight: bold;
  font-size: 1rem;
  color: #2c3930;
}


/* Berita List */
.berita-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.berita-item {
  width: 60%;
  height: 175px;
  background: #a27b5c;
  border-radius: 5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  margin-bottom: 0.5rem;
}

.berita-image {
  width: 100%;
  height: 140px;
  object-fit: cover;
  border-radius: 5px;
}

.berita-info {
  padding: 6px;
  color: white;
  margin-left: 0.7rem;
}

.berita-title {
  font-size: 14px;
  font-weight: bold;
  margin-bottom: 4px;
}

.berita-subtitle {
  font-size: 10px;
  color: white;
}

/* Berita Detail */
.berita-detail {
  display: flex;
  flex-direction: column;
  padding: 1rem;
  height: 100%;
  overflow-y: auto;
  margin: 0 auto;
  width: 100%;
}

/* Kartu berita */
.berita-card {
  border-radius: 12px;
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 0.5rem;
}

.detail-image {
  width: 425px;
  height: 350px;
  object-fit: cover;
  border-radius: 5px;
}

.detail-title {
  font-size: 15px;
  font-weight: bold;
  color: #000;
  margin-bottom: 8px;
}

/* Deskripsi */
.detail-description {
  padding: 5px;
}

.description-textarea {
  width: 100%;
  min-height: 120px;
  resize: none;
  font-size: 12px;
  border: none;
  background-color: transparent;
  overflow-y: auto;
  border-radius: 8px;
  color: #333;
  box-sizing: border-box;
  outline: none;
  font-family: inherit;
}


@media (min-width: 769px) {
  .berita-item {
    display: flex;
    flex-direction: column;
    width: 50%;
    height: 337px;
    margin-bottom: 1rem;
  }
  .berita-title {
    font-size: 14px;
  }
  .berita-image {
    height: 300px;
  }
  .berita-detail {
    flex-direction: row;
  }
  .berita-card {
    flex: 2;
    width: 700px;
    margin: 0;
    gap: 1rem;
  }
  .detail-image {
    height: 440px;
    width: 775px;
  }
  .detail-description {
    flex: 1;
    width: 60%;
  }
  .detail-title {
    font-size: 1.5rem;
  }
}
</style>
