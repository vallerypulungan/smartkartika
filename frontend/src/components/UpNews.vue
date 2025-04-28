<template>
  <div class="upload-berita-container">
    <!-- Mobile Header -->
    <div class="page-header">
      <div class="left">
        <button class="back-button" @click="goBack">←</button>
      </div>
      <div class="center">
        <h1 class="app-title">Smartkartika</h1>
      </div>
      <div class="right"></div> <!-- dummy agar kiri dan kanan seimbang -->
    </div>

    <div class="form-upload">
      <h2 class="title">Unggah Berita</h2>

      <div class="upload-wrapper">
        <!-- Image Upload Area -->
        <div class="upload-area" @click="$refs.fileInput.click()">
          <input type="file" accept="image/*" ref="fileInput" @change="handleFileUpload" style="display: none;" />
          <div class="upload-box">
            <img v-if="previewImage" :src="previewImage" alt="Preview" class="preview-image" />
            <img v-else src="@/assets/camera-plus.png" alt="Upload" class="upload-icon" />
          </div>
        </div>

        <div class="upload-texts">
          <p class="upload-instruction">Pilih File Dari Perangkat Anda</p>
          <p class="upload-note">(Maksimal 10mb)</p>
        </div>
      </div>

      <!-- Text Area -->
      <div class="description-warpper">
        <label class="description-label">Rincian Kegiatan</label>
        <input type="text" v-model="description" placeholder="isi rincian kegiatan" class="description-input" />
      </div>

      <!-- Submit Button -->
      <button
        @click="validateBeforeUpload"
        :class="['submit-button', { 'active': isFormValid }]"
      >
        Unggah
      </button>
    </div>

    <!-- Confirm Dialog Component -->
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

    <!-- Success Dialog Component -->
    <PopupSuccess
      v-if="showSuccess"
      :title="'FILE BERHASIL DIUNGGAH'"
      @close="resetForm"
    />

    <!-- Warning Dialog Component -->
    <PopupFoto
      v-if="showWarningFoto"
      :title="'PILIH FOTO DAHULU'"
      @close="showWarningFoto = false"
    />

    <PopupDeskripsi
      v-if="showWarningRincian"
      :title="'ISI RINCIAN KEGIATAN DAHULU'"
      @close="showWarningRincian = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { computed } from 'vue';
import { useRouter } from 'vue-router';

import PopupConfirm from '@/components/BlokPopup.vue';
import PopupConfirmBack from '@/components/BlokPopup.vue';
import PopupSuccess from '@/components/MessagePopup.vue';
import PopupFoto from '@/components/MessagePopup.vue';
import PopupDeskripsi from '@/components/MessagePopup.vue';

const previewImage = ref(null);
const description = ref('');
const showConfirmUpload = ref(false);
const showConfirmBack = ref(false);
const showSuccess = ref(false);
const showWarningFoto = ref(false);
const showWarningRincian = ref(false);
const router = useRouter();

// Menangani upload file
function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

const isFormValid = computed(() => {
  return previewImage.value && description.value.trim() !== '';
});


function validateBeforeUpload() {
  if (!previewImage.value) {
    showWarningFoto.value = true;
  } else if (description.value.trim() === '') {
    showWarningRincian.value = true;
  } else {
    showConfirmUpload.value = true;
  }
}

async function submitUpload() {
  showConfirmUpload.value = false;

  try {
    await saveToDatabase(previewImage.value, description.value);
    showSuccess.value = true;
  } catch (error) {
    console.error('Gagal upload:', error);
  }
}

function saveToDatabase(imageData, descriptionText) {
  return new Promise((resolve) => {
    setTimeout(() => {
      console.log('Mengirim ke database:', { imageData, descriptionText });
      resolve('success');
    }, 1500);
  });
}

function resetForm() {
  showSuccess.value = false;
  previewImage.value = null;
  description.value = '';
}

function goBack() {
  showConfirmBack.value = true;
}
function confirmBack() {
  showConfirmBack.value = false;
  router.push('/dashboard');
}
</script>

<style scoped>
.upload-berita-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  padding: 0;
  margin: 0;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
  padding: 1.5rem 1rem;
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
}

.app-title {
  font-size: 1.3rem;
  font-style: italic;
  font-weight: bold;
  color: #fff;
}

.form-upload {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background-color: #fff;
  padding: 2rem;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  height: 100%;
  width: 100vw;
  box-sizing: border-box;
}

.title {
  text-align: center;
  font-weight: bold;
  color: #1f3a2d;
}

.upload-area {
  border: 2px dashed black;
  border-radius: 8px;
  padding: 1rem;
  background: #C5C5C5;
  text-align: center;
  cursor: pointer;
  width: 75%;
  margin: 0 auto;
}

.upload-box {
  width: 150px;
  height: 100px;
  margin: 0 auto;
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}

.upload-icon {
  width: 100%;
  height: 100%;
}

.upload-text {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 0.5rem;
}

.upload-instruction {
  display: flex;
  justify-content: center;
  font-weight: 500;
  margin-bottom: 4px;
  color: #333;
  margin-top: 0.5rem;
}

.upload-note {
  display: flex;
  justify-content: center;
  font-size: 12px;
  color: #777;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.description-wrapper {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
}

.description-label {
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

.description-input {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.submit-button {
  width: 100%;
  padding: 10px;
  background-color: #7d726a;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  margin-bottom: 3rem;
  transition: background-color 0.3s, cursor 0.3s;
  cursor: pointer;
}

.submit-button.active {
  background-color: #dea67a;
  cursor: pointer;
}

@media(min-width: 768px) {
  .page-header {
    display: none;
  }
}
</style>
