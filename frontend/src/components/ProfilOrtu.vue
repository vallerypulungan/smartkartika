<template>
  <div class="profile-container">
    <!-- Header -->
    <div class="profile-header">
      <button class="back-button" @click="goBack">
        <img src="@/assets/arrow-left.png" alt="" />
      </button>

      <p class="username">{{ userName }}</p>
      <p class="email">{{ userEmail || 'email@gmail.com' }}</p>
    </div>

    <!-- Info Section -->
    <div class="profile-info">
      <div class="info-item">
        <label>Nama</label>
        <p>{{ userName }}</p>
      </div>
      <div class="info-item">
        <label>Nama Siswa</label>
        <p>{{ anak.name || 'Nama Siswa' }}</p>
      </div>
      <div class="info-item">
        <label>No Telepon</label>
        <p>{{ userTelp || 'No Telepon' }}</p>
      </div>
      <div class="info-item">
        <label>Alamat</label>
        <p>{{ userAlamat || 'Alamat' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref({})
const anak = ref({})

let userStorage = {}
try {
  userStorage = JSON.parse(localStorage.getItem('user') || '{}')
} catch (e) {
  userStorage = {}
}
const userName = ref(userStorage.name || localStorage.getItem('userName') || 'Pengguna')
const userEmail = ref(userStorage.email || 'aku')
const userTelp = ref(userStorage.num_telp || '')
const userAlamat = ref(userStorage.alamat || '')

onMounted(async () => {
  if (userStorage.id_parent) {
    const res = await axios.get(`https://smarkatika-si.my.id/api/children?parent=${userStorage.id_parent}`)
    if (res.data.data.length > 0) {
      anak.value = res.data.data[0]
      user.value = anak.value.parent
      if (!userName.value) userName.value = user.value.name
      if (!userEmail.value) userEmail.value = user.value.email
      if (!userTelp.value) userTelp.value = user.value.num_telp
      if (!userAlamat.value) userAlamat.value = user.value.alamat
    }
  }
})

const goBack = () => {
  router.back()
}
</script>

<style scoped>
.profil-container {
  height: 100vh;
  margin: 0;
  padding: 0;
}

.profile-header {
  background-color: #a27b5c;
  color: #fff;
  text-align: center;
  padding: 0.8rem 0.5rem;
  position: relative;
}

.username {
  font-weight: bold;
  font-size: 1.3rem;
  margin-top: 0.5rem;
}

.email {
  font-size: 0.7rem;
  color: #ccc;
  margin-top: 0.2rem;
}

.back-button {
  position: absolute;
  left: 1rem;
  top: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: white;
  cursor: pointer;
}

.back-button img {
  filter: invert(1);
}

.profile-info {
  display: flex;
  flex-direction: column;
  padding: 1rem;
  width: 100%;
  margin: auto;
  background-color: #fff;
  display: flex;
  flex-direction: column;
  height: calc(100vh - 90px);
}

.info-item {
  border-bottom: 1px solid #ccc;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
}

.info-item label {
  display: block;
  font-size: 0.85rem;
  color: #555;
}

.info-item p {
  font-weight: bold;
  margin: 0.2rem 0 0;
  color: #000;
}

@media (min-width: 768px) {
  .profile-info {
    padding: 3rem;
    padding-top: 1rem;
  }
}
</style>
