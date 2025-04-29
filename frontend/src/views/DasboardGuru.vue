<template>
  <div class="dashboard-container">
    <!-- Header -->
    <HeaderDashboard
      v-if="!isMobile || (isMobile && !selectedMenu)"
      :isMobile="isMobile"
      @toggleSidebar="showSidebar = !showSidebar"
    />

    <!-- MOBILE -->
    <div v-if="isMobile" class="mobile-content">
      <template v-if="!selectedMenu">
        <div class="greeting">
          <p>Halo <strong>{{ userName }}</strong></p>
          <p>Selamat Datang</p>
        </div>
        <div class="menu-grid">
          <div class="menu-item" @click="selectMenu('upload')">
            <img src="@/assets/plus.png" alt="Buat Laporan" />
            <p>BUAT LAPORAN</p>
          </div>
          <div class="menu-item" @click="selectMenu('uploadNews')">
            <img src="@/assets/file-alt.png" alt="Unggah Berita" />
            <p>UNGGAH BERITA</p>
          </div>
          <div class="menu-item" @click="selectMenu('manage')">
            <img src="@/assets/edit-alt.png" alt="Kelola Kegiatan" />
            <p>KELOLA KEGIATAN</p>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="mobile-component">
          <component :is="currentMobileComponent" />
        </div>
      </template>
    </div>

    <!-- DESKTOP -->
    <div v-else class="desktop-content">
      <div class="left-panel">
        <div
          class="menu"
          :class="{ active: selectedMenu === 'home' }"
          @click="selectMenu('home')"
        >
          <div class="icon-container">
            <img src="@/assets/home-alt.png" alt="Home" />
          </div>
          <span>Halaman Utama</span>
        </div>
        <div
          class="menu"
          :class="{ active: selectedMenu === 'upload' }"
          @click="selectMenu('upload')"
        >
          <div class="icon-container">
            <img src="@/assets/plus.png" alt="Tambah Kegiatan" />
          </div>
          <span>Tambah Kegiatan</span>
        </div>
        <div
          class="menu"
          :class="{ active: selectedMenu === 'uploadNews' }"
          @click="selectMenu('uploadNews')"
        >
          <div class="icon-container">
            <img src="@/assets/camera-plus.png" alt="Tambah Dokumentasi" />
          </div>
          <span>Unggah Berita</span>
        </div>
        <div
          class="menu"
          :class="{ active: selectedMenu === 'manage' }"
          @click="selectMenu('manage')"
        >
          <div class="icon-container">
            <img src="@/assets/edit-alt.png" alt="Kelola Kegiatan" />
          </div>
          <span>Kelola Kegiatan</span>
        </div>

        <div class="spacer"></div>

        <div class="logout" @click="logout">
          <img src="@/assets/upload-alt.png" alt="Logout" />
          <span>Logout</span>
        </div>
      </div>

      <div class="right-panel">
        <component :is="currentPageComponent" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import HeaderDashboard from '@/components/HeaderDashboard.vue'

import UploadPage from '@/components/AddAct.vue'
import UploadNewsPage from '@/components/UpNews.vue'
import ManagePage from '@/components/ManageAct.vue'

const router = useRouter()
const route = useRoute()

const userName = 'User'
const isMobile = ref(window.innerWidth <= 768)
const showSidebar = ref(true)

const selectedMenu = ref(null)

// Daftar komponen untuk mobile
const mobileComponents = {
  upload: UploadPage,
  uploadNews: UploadNewsPage,
  manage: ManagePage,
}

// Pilih komponen berdasarkan selectedMenu
const currentMobileComponent = computed(() => {
  if (selectedMenu.value === 'uploadNews') {
    return null;
  }
  return mobileComponents[selectedMenu.value] || null
})

onMounted(() => {
  window.addEventListener('resize', () => {
    isMobile.value = window.innerWidth <= 768
  })

  if (!isMobile.value && !route.name) {
    router.push({ name: 'home' })
  }
})

watch(route, (newRoute) => {
  if (isMobile.value) {
    if (newRoute.name !== 'uploadNews') {
      selectedMenu.value = newRoute.name;
    } else {
      selectedMenu.value = 'home';
    }
  } else {
    selectedMenu.value = newRoute.name || 'home';
  }
})

function selectMenu(menu) {
  router.push({ name: menu })
}

async function logout() {
  try {
    await axios.post('http://localhost:8000/api/logout');
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('role');
    router.push('/login'); // redirect ke halaman login
  } catch (error) {
    alert('Logout gagal.');
  }
}

</script>

<style scoped>
.dashboard-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  width: 100%;
  overflow: hidden;
}

/* === MOBILE === */
.mobile-content {
  padding: 0;
  height: 100%;
  overflow: hidden;
  margin: 0;
  background-color: #fff;
}

.greeting p {
  margin: 0;
}

.menu-grid {
  display: flex;
  flex-direction: column;
  margin: 20px;
  gap: 16px;
}

.menu-item {
  width: 90%;
  margin: 0 auto;
  background: #a27b5c;
  color: #fff;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: background 0.3s;
}

.menu-item:hover {
  background-color: #dea67a;
}

.menu-item img {
  width: 100px;
  height: 70px;
  object-fit: contain;
  background-color: #fff;
  border-radius: 8px;
}

.menu-item p {
  font-weight: bold;
  font-size: 1.2rem;
}

.mobile-component {
  flex-grow: 1;
  overflow-y: auto;
  min-height: 0;
}

/* === DESKTOP === */
.desktop-content {
  display: flex;
  flex-grow: 1;
  height: calc(100% - 60px);
  overflow: hidden;
  padding: 0;
  margin: 0;
}

.left-panel {
  width: 350px;
  background-color: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px;
}

.menu {
  background: #a27b5c;
  color: white;
  margin: 8px;
  height: 80px;
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 0 16px 0 0;
  border-radius: 12px;
  transition: background 0.3s;
  overflow: hidden;
}

.menu span {
  font-weight: bold;
  font-size: 1rem;
}

.menu.active,
.menu:hover {
  background: #dea67a;
}

.spacer {
  flex-grow: 1;
}

.icon-container {
  height: 100%;
  aspect-ratio: 1/1;
  background-color: #d9d9d9;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 0;
  width: 35%;
}

.icon-container img {
  width: 50%;
  height: 50%;
  object-fit: contain;
}

.logout {
  background: #d9d9d9;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  cursor: pointer;
  border-radius: 12px;
  transition: background 0.3s;
  margin-top: 5rem;
}

.logout:hover {
  background-color: gray;
}

.logout img {
  width: 40px;
  height: 40px;
  object-fit: contain;
}

.logout span {
  font-weight: bold;
  color: black;
}

.right-panel {
  flex-grow: 1;
  background-color: #f9f9f9;
  padding: 40px;
  overflow-y: auto;
  min-height: 0;
}
</style>
