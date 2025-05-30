<template>
  <div class="upload-berita-container">
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

    <div class="form-upload">
      <h2 class="title">UNGGAH BERITA</h2>

      <div class="upload-wrapper">
        <!-- Image Upload Area -->
        <div class="upload-area" @click="$refs.fileInput.click()">
          <div class="upload-box">
            <img v-if="previewImage" :src="previewImage" alt="Preview" class="preview-image" />
            <div v-else class="upload-texts">
              <div class="icon">
                <img src="@/assets/arrow-up-sm.png" alt="Up" />
              </div>
              <p class="upload-instruction">Drop Your File</p>
              <p class="upload-note">(Maksimal 10mb)</p>
            </div>
          </div>
          <input
            type="file"
            accept="image/*"
            ref="fileInput"
            @change="handleFileUpload"
            style="display: none"
          />
        </div>
        <!-- Tombol hijau Add Files -->
      </div>
      <!-- Judul Input -->
      <div class="title-wrapper">
        <label class="title-label">Judul Berita</label>
        <input
          v-model="title"
          type="text"
          placeholder="Masukkan judul berita"
          class="title-input"
        />
      </div>

      <!-- Text Area -->
      <div class="description-wrapper">
        <label class="description-label">Rincian Kegiatan</label>
        <textarea
          v-model="description"
          placeholder="Isi rincian kegiatan"
          class="description-textarea"
        ></textarea>
      </div>

      <!-- Submit Button -->
      <button @click="validateBeforeUpload" :class="['submit-button', { active: isFormValid }]">
        Unggah
      </button>
    </div>

    <!-- Popup Components -->
    <PopupConfirm
      v-if="showConfirmUpload"
      :title="'KONFIRMASI UNTUK MENGUNGGAH'"
      :message="'Yakin ingin mengunggah berita ini?'"
      :konfirmasi="'UNGGAH'"
      :batalkan="'BATAL'"
      @confirm="submitUpload"
      @cancel="showConfirmUpload = false"
    />

    <PopupConfirmBack
      v-if="showConfirmBack"
      :title="'KEMBALI KE HALAMAN SEBELUMNYA'"
      :message="'Apakah anda yakin untuk kembali ke halaman sebelumnya?'"
      :konfirmasi="'YA'"
      :batalkan="'BATAL'"
      @confirm="confirmBack"
      @cancel="showConfirmBack = false"
    />

    <PopupSuccess v-if="showSuccess" :title="'File berhasil diunggah'" @close="resetForm" />

    <PopupFoto
      v-if="showWarningFoto"
      :title="'Pilih foto terlebih dahulu'"
      @close="showWarningFoto = false"
    />

    <PopupJudul
      v-if="showWarningTitle"
      :title="'Isi judul terlebih dahulu'"
      @close="showWarningTitle = false"
    />

    <PopupDeskripsi
      v-if="showWarningRincian"
      :title="'Isi deskripsi terlebih dahulu'"
      @close="showWarningRincian = false"
    />

    <PopupDeskripsi
      v-if="showFailUp"
      :title="'Gagal Upload'"
      @close="showFailUp = false"
    />

    <PopupDeskripsi
      v-if="showAlertFile"
      :title="'File tidak ditemukan'"
      @close="showAlertFile = false"
    />
  </div>
</template>

<script setup>
import axios from 'axios'

import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

import PopupConfirm from '@/components/BlokPopup.vue'
import PopupConfirmBack from '@/components/BlokPopup.vue'
import PopupSuccess from '@/components/MessagePopup.vue'
import PopupFoto from '@/components/MessagePopup.vue'
import PopupDeskripsi from '@/components/MessagePopup.vue'
import PopupJudul from '@/components/MessagePopup.vue'

const previewImage = ref(null)
const title = ref('')
const description = ref('')
const showConfirmUpload = ref(false)
const showConfirmBack = ref(false)
const showSuccess = ref(false)
const showWarningFoto = ref(false)
const showWarningRincian = ref(false)
const showWarningTitle = ref(false) 
const nip = localStorage.getItem('nip');

const router = useRouter()

function handleFileUpload(event) {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const isFormValid = computed(() => {
  return previewImage.value && title.value.trim() !== '' && description.value.trim() !== ''
})

function validateBeforeUpload() {
  if (!previewImage.value) {
    showWarningFoto.value = true
  } else if (title.value.trim() === '') {
    showWarningTitle.value = true
  } else if (description.value.trim() === '') {
    showWarningRincian.value = true
  } else {
    showConfirmUpload.value = true
  }
}
const fetchBerita = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/documentations')
    beritaList.value = response.data.data.map((item) => ({
    id: item.id_document,
    title: item.title,
    description: item.description,
    image: `http://localhost:8000/${encodeURIComponent(item.file_url).replace(/%2F/g, '/')}`,
  }))
  } catch (error) {
    console.error('Gagal memuat berita:', error)
  }
}

async function submitUpload() {
  let formData = new FormData();
  showConfirmUpload.value = false
  try {
    await saveToDatabase(previewImage.value, title.value, description.value)
    showSuccess.value = true
  } catch (error) {
    showFailUp.value = true
  }
}

async function saveToDatabase(imageData, titleText, descriptionText) {
  const fileInput = document.querySelector('input[type="file"]');
  const file = fileInput?.files[0];

  if (!file) {
    showAlertFile.value = true
    return;
  }

  const formData = new FormData();
  formData.append('image', file);
  formData.append('title', titleText);
  formData.append('description', descriptionText);
  formData.append('nip', nip);
 

  await axios.post('http://localhost:8000/api/documentations', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
}

function resetForm() {
  showSuccess.value = false
  previewImage.value = null
  title.value = ''
  description.value = ''
}

const goBack = () => {
  router.push('home')
}

function confirmBack() {
  showConfirmBack.value = false
  router.push('home')
}
</script>

<style scoped>
.upload-berita-container {
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
  padding: 0.5rem;
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

.back-button img {
  filter: invert(1);
}

.app-title {
  font-size: 1.3rem;
  font-style: italic;
  font-weight: bold;
  color: #fff;
}

.form-upload {
  display: flex;
  flex-direction: column;
  background-color: #fff;
  gap: 1rem;
  padding: 1rem;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  height: 100vh;
  width: 100%;
  overflow-y: auto;
  box-sizing: border-box;
}

.title {
  text-align: center;
  font-weight: bold;
  color: #1f3a2d;
  font-size: 1.3rem;
}

.upload-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.upload-area {
  border: 2px dashed grey;
  border-radius: 8px;
  background: #c5c5c5;
  text-align: center;
  cursor: pointer;
  width: 40%;
  margin: 0 auto;
  position: relative;
}

.upload-box {
  width: 100%;
  height: auto;
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
}

.upload-icon {
  width: 100%;
  height: 100%;
}

.upload-texts {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.upload-instruction {
  color: #333;
  font-size: 12px;
}

.upload-note {
  font-size: 9px;
  color: #777;
}

.preview-image {
  width: 100%;
  height: 350px;
  object-fit: cover;
}

.title-wrapper {
  display: flex;
  flex-direction: column;
  margin-left: 1rem;
}

.title-label {
  margin-bottom: 6px;
  font-size: 12px;
  color: #333;
}

.title-input {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

.description-wrapper {
  display: flex;
  flex-direction: column;
  margin-left: 1rem;
}

.description-label {
  margin-bottom: 6px;
  font-size: 12px;
  color: #333;
}

.description-textarea {
  width: 100%;
  height: auto;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  resize: vertical;
  box-sizing: border-box;
  min-height: 80px;
  font-family: inherit;
}

.submit-button {
  width: 40%;
  padding: 10px;
  background-color: #7d726a;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  margin-bottom: 3rem;
  transition:
    background-color 0.3s,
    cursor 0.3s;
  cursor: pointer;
  margin: 0 auto;
}

.submit-button:hover {
  background-color: grey;
}

.submit-button.active {
  background-color: #dea67a;
  cursor: pointer;
}

@media (min-width: 768px) {
  .page-header {
    display: none;
  }
  .form-upload {
    box-sizing: border-box;
    width: 100%;
  }
  .title {
    text-align: start;
    font-size: 1.5rem;
  }
  .submit-button {
    margin-bottom: 1rem;
    width: 25%;
  }
}
</style>
