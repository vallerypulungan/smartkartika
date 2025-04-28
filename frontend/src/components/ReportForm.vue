<template>
  <div class="page-wrapper">
    <!-- Header -->
    <div class="page-header">
      <div class="left">
        <button class="back-button" @click="goBack">←</button>
      </div>
      <div class="center">
        <h1 class="app-title">Smartkartika</h1>
      </div>
      <div class="right"></div> <!-- dummy agar kiri dan kanan seimbang -->
    </div>

    <!-- Form Container -->
    <div class="form-wrapper">
      <h2 class="title">BUAT LAPORAN</h2>

      <!-- Pilih Kelas -->
      <div class="form-group">
        <label for="kelas">Pilih Kelas</label>
        <select id="kelas" v-model="selectedClass" @change="fetchStudents">
          <option disabled value="">Kelas</option>
          <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">
            {{ kelas.name }}
          </option>
        </select>
      </div>

      <!-- Pilih Siswa -->
      <div class="form-group">
        <label for="siswa">Pilih Nama Siswa</label>
        <select id="siswa" v-model="selectedStudent">
          <option disabled value="">Nama Siswa</option>
          <option v-for="siswa in siswaList" :key="siswa.id" :value="siswa.id">
            {{ siswa.name }}
          </option>
        </select>
      </div>

      <!-- Upload File -->
      <div class="form-file">
        <label>Capaian kegiatan pembelajaran</label>
        <label class="custom-file-upload" :class="{ 'file-selected': fileName }">
          {{ fileName || 'Pilih file' }}
          <input type="file" @change="handleFile" accept="application/pdf" />
        </label>
        <small>Unggah file berupa pdf (maksimal 10MB)</small>
      </div>

      <!-- Tombol Kirim -->
      <button class="submit-button" @click="confirmSubmit">Kirim</button>
    </div>

    <!-- Modal Konfirmasi -->
    <ModalKonfirmasi
      v-if="showConfirm"
      :title="'KIRIM LAPORAN'"
      :message="'Apakah Anda yakin ingin mengirim laporan ini?'"
      :konfirmasi="'SIMPAN'"
      :batalkan="'BATAL'"
      @confirm="submitForm"
      @cancel="showConfirm = false"
    />

    <!-- Modal Sukses -->
    <ModalSukses
      v-if="showSuccess"
      :title="'LAPORAN BERHASIL DIKIRIM'"
      @close="showSuccess = false"
    />
  </div>
</template>

<script>
import axios from 'axios';
import ModalKonfirmasi from '@/components/BlokPopup.vue';
import ModalSukses from '@/components/MessagePopup.vue';

export default {
  components: {
    ModalKonfirmasi,
    ModalSukses,
  },
  data() {
    return {
      kelasList: [],
      siswaList: [],
      selectedClass: '',
      selectedStudent: '',
      file: null,
      fileName: '',
      showConfirm: false,
      showSuccess: false,
    };
  },
  mounted() {
    this.fetchKelas();
  },
  methods: {
    goBack() {
      this.$router.push('/dashboard');
    },
    async fetchKelas() {
      try {
        const response = await axios.get('/api/kelas');
        this.kelasList = response.data;
      } catch (error) {
        console.error('Gagal mengambil data kelas:', error);
      }
    },
    async fetchStudents() {
      try {
        const response = await axios.get(`/api/kelas/${this.selectedClass}/siswa`);
        this.siswaList = response.data;
      } catch (error) {
        console.error('Gagal mengambil data siswa:', error);
      }
    },
    handleFile(event) {
      const uploadedFile = event.target.files[0];
      if (uploadedFile && uploadedFile.size > 10 * 1024 * 1024) {
        alert('Ukuran file melebihi 10MB!');
        event.target.value = '';
        this.file = null;
        this.fileName = '';
      } else {
        this.file = uploadedFile;
        this.fileName = uploadedFile.name;
      }
    },
    confirmSubmit() {
      if (!this.selectedClass || !this.selectedStudent || !this.file) {
        alert('Harap lengkapi semua data!');
        return;
      }
      this.showConfirm = true;
    },
    async submitForm() {
      try {
        const formData = new FormData();
        formData.append('kelas_id', this.selectedClass);
        formData.append('siswa_id', this.selectedStudent);
        formData.append('file', this.file);

        await axios.post('/api/laporan', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        this.showConfirm = false;
        this.showSuccess = true;
        this.resetForm();
      } catch (error) {
        alert('Gagal mengunggah file!');
        console.error(error);
      }
    },
    resetForm() {
      this.selectedClass = '';
      this.selectedStudent = '';
      this.file = null;
      this.fileName = '';
    },
  },
};
</script>

<style scoped>
.page-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  padding: 0;
  margin: 0;
}

/* Header */
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

/* Form Container */
.form-wrapper {
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

.form-group {
  margin-bottom: 0,5rem;
}

.form-group label {
  color: #1f3a2d;
  display: block;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-group select {
  color: #1f3a2d;
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #aaa;
  border-radius: 4px;
}

.form-file label {
  color: #1f2922;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: 400;
  margin-bottom: 1rem;
}

.custom-file-upload {
  color: #fff;
  display: inline-block;
  background-color: #7d726a;
  padding: 0.75rem 1.5rem;
  border-radius: 15px;
  cursor: pointer;
  text-align: center;
  width: 100%;
  font-weight: bold;
}

.custom-file-upload input[type="file"] {
  display: none;
}

.custom-file-upload.file-selected {
  background-color: #2c3930;
  color: #fff;
}

.form-file small {
  margin-top: 1rem;
  color: #1f3a2d;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 1rem;
}

.submit-button {
  width: 100%;
  padding: 0.75rem;
  background-color: #7d726a;
  color: white;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  margin-bottom: 1rem;
}

.submit-button:hover {
  background-color: #4d4d4d;
}
</style>
