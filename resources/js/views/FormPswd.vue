<template>
  <div class="container">
    <!-- Versi Mobile -->
    <div class="mobile-panel">
      <div class="atas">
        <h2>SMARTKARTIKA</h2>
        <p>Semua Kegiatan Dapat Kemudahan</p>
      </div>
      <div class="bawah">
        <h2>Masuk Ke Smartkartika</h2>
        <div class="input-container">
          <label for="username-input" class="input-label">Username</label>
          <input
            id="username-input"
            v-model="username"
            type="username"
            class="input-box"
            placeholder="Username"
            required
          />
          <label for="password-input" class="input-label">Kode</label>
          <div class="password-wrapper">
            <input
              id="password-input"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              class="input-box"
              placeholder="Password"
              required
            />
            <button type="button" class="toggle-password" @click="showPassword = !showPassword">
              <img :src="showPassword ? eyeOff : eye" alt="Toggle Password" />
            </button>
          </div>
        </div>

        <button @click="handleLogin" class="login-button">Masuk</button>
      </div>
    </div>
    <PopupMessage
      v-if="showAlert"
      :title="'Login gagal. <br>Periksa username dan password.'"
      @close="showAlert = false"
    />
    <PopupMessage
      v-if="showLoginGuru"
      :title="'Login Guru Berhasil'"
      @close="showLogin = false"
    />
    <PopupMessage
      v-if="showLoginOrtu"
      :title="'Login Orang Tua Berhasil'"
      @close="showAlert = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import eye from '@/assets/eye.png'
import eyeOff from '@/assets/eye-off.png'
import PopupMessage from '@/components/MessagePopup.vue'

const router = useRouter()
const showAlert = ref(false)
const showPassword = ref(false)
const username = ref('')
const password = ref('')
const showLoginGuru = ref(false)
const showLoginOrtu = ref(false)

const handleLogin = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      username: username.value,
      password: password.value
    })

    if (response.data.status === 'success') {
      localStorage.setItem('userName', response.data.name)


      localStorage.setItem('isLoggedIn', 'true')
      localStorage.setItem('role', response.data.role)

      if (response.data.role === 'guru') {
        localStorage.setItem('user', JSON.stringify({
          name: response.data.name,
          email: response.data.email,
          nip: response.data.nip,
          telepon: response.data.num_telp
        }))
        localStorage.setItem('userName', response.data.name)
        showLoginGuru.value = true
        setTimeout(() => {
          router.push('/dashboard-guru')
        }, 1500)
      } else if (response.data.role === 'ortu') {
        localStorage.setItem('user', JSON.stringify({
          id_parent: response.data.id_parent,
          username: response.data.username,
          email: response.data.email,
          name: response.data.name,
          num_telp: response.data.num_telp,
          alamat: response.data.alamat
        }))
        localStorage.setItem('userName', response.data.name)
        showLoginOrtu.value = true
        setTimeout(() => {
          router.push('/dashboardortu')
        }, 1500)
      }

      localStorage.setItem('nip', response.data.nip)
      localStorage.setItem('id_teacher', response.data.id_teacher)
    }
  } catch (error) {
    showAlert.value = true
  }
}
</script>

<style scoped>
.container {
  font-family: Arial, Helvetica, sans-serif;
  display: flex;
  height: 100vh;
}

.mobile-panel {
  flex-direction: column;
  height: 100vh;
}

.mobile-panel .atas {
  display: none;
}

.mobile-panel .bawah {
  flex: 1;
  background-color: white;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  margin: 0;
  border-radius: 0;
}

.mobile-panel .bawah h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #000;
  margin-top: 0;
  margin-bottom: 50px;
  text-align: center;
}

.mobile-panel .input-container {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 80%;
  margin-bottom: 20px;
}

.mobile-panel .input-label {
  color: #000;
  font-size: 14px;
  margin-bottom: 7px;
}

.mobile-panel .input-box {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-bottom: 1rem;
}

.mobile-panel .input-box:focus::placeholder {
  color: transparent;
}

.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}

.password-wrapper input {
  flex: 1;
}

.toggle-password {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.toggle-password img {
  width: 12px;
  height: 12px;
  margin-bottom: 0.7rem;
}

.mobile-panel .login-button {
  margin-top: 20px;
  background-color: #2c3930;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 50%;
  margin-bottom: 1rem;
}

.mobile-panel .login-button:hover {
  background-color: #1f2922;
}

@media (max-width: 768px) {
  .container {
    display: block;
  }

  .mobile-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
}

@media (min-width: 768px) {
  .container {
    display: flex;
    flex-direction: row;
    height: 100vh;
  }

  .mobile-panel {
    display: flex;
    flex-direction: row;
    width: 100%;
    height: 100vh;
    background-color: #2c3930;
  }

  .mobile-panel .atas {
    flex: 1;
    width: 100%;
    background-color: #2c3930;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #fff;
  }

  .mobile-panel .atas h2 {
    font-weight: bold;
    margin-bottom: 0.5rem;
  }

  .mobile-panel .bawah {
    flex: 2;
    width: 100%;
    max-width: 750px;
    background-color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2rem;
    border-radius: 0;
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
  }

  .mobile-panel .bawah h2 {
    font-size: 1.5rem;
  }

  .mobile-panel .input-container {
    width: 70%;
  }

  .mobile-panel .login-button {
    width: 40%;
  }
}
</style>
