<template>
  <div class="container-auth">
    <div class="panel">
      <h2>Daftar Sebagai Guru</h2>
      <form @submit.prevent="handleRegister">
        <div class="input-container">
          <label class="input-label">Nama</label>
          <input v-model="form.name" type="text" class="input-box" required />

          <label class="input-label">NIP</label>
          <input v-model="form.nip" type="text" class="input-box" required />

          <label class="input-label">Email</label>
          <input v-model="form.email" type="email" class="input-box" required />

          <label class="input-label">Password</label>
          <input v-model="form.password" type="password" class="input-box" required />

          <label class="input-label">No Telepon</label>
          <input v-model="form.num_telp" type="text" class="input-box" required />
        </div>
        <div class="login-wrapper">
          <button type="submit" class="login-button">Daftar</button>
        </div>
      </form>
      <div class="login-redirect">
        <span>
          Sudah punya akun?
          <router-link to="/login" class="login-link">Masuk di sini</router-link>
        </span>
      </div>
      <Popup v-if="showPopup" :title="popupMessage" @close="showPopup = false" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import Popup from '@/components/MessagePopup.vue'

const showPopup = ref(false)
const popupMessage = ref('')

const form = ref({
  name: '',
  nip: '',
  email: '',
  password: '',
  num_telp: '',
})

const handleRegister = async () => {
  // Validasi sederhana
  if (!form.value.name || !form.value.nip || !form.value.email || !form.value.password || !form.value.num_telp) {
    popupMessage.value = 'Harap isi semua kolom terlebih dahulu.'
    showPopup.value = true
    return
  }
  try {
    const response = await axios.post('http://localhost:8000/api/teachers', form.value)
    popupMessage.value = response.data.message || 'Registrasi berhasil.'
    showPopup.value = true
    // Reset form jika ingin
    form.value = { name: '', nip: '', email: '', password: '', num_telp: '' }
  } catch (err) {
    popupMessage.value = err.response?.data?.message || 'Registrasi gagal.'
    showPopup.value = true
  }
}
</script>

<style scoped>
.container-auth {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.atas {
  flex: 1;
  background-size: cover;
  background-position: center;
  background-color: #e2e2e2;
}

.bawah-wrapper {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.panel {
  width: 100%;
  max-width: 400px;
  background-color: white;
  border-radius: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 15px;
}

.panel h2 {
  font-size: 1.3rem;
  font-weight: 900;
  text-align: center;
  color: #000;
  margin-bottom: 15px;
}

.input-container {
  display: flex;
  flex-direction: column;
  margin-bottom: 5px;
}

.input-label {
  color: #000;
  font-size: 12px;
  margin-bottom: 2px;
}

.input-box {
  width: 100%;
  padding: 10px;
  margin-bottom: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.login-wrapper {
  display: flex;
  justify-content: center;
}

.login-button {
  background-color: #2c3930;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  width: 50%;
  font-weight: bold;
  margin-top: 1rem;
}

.login-button:hover {
  background-color: #1f2922;
}

.login-redirect {
  margin-top: 1.2rem;
  text-align: center;
  font-size: 14px;
  color: #000;
}

.login-link {
  color: #2c3930;
  font-weight: bold;
  text-decoration: none;
}

.login-link:hover {
  text-decoration: underline;
}

@media (min-width: 768px) {
  .container-auth {
    flex-direction: row;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }

  .atas {
    display: none;
  }

  .bawah-wrapper {
    padding: 40px;
  }

  .panel {
    width: 100%;
    max-width: 800px;
  }
  .login-button {
    width: 40%;
  }
}
</style>
