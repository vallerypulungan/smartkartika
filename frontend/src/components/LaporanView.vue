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
      <!-- Tombol Tambah -->
      <button class="laporan-button add" @click="showForm('add')">Tambah Laporan</button>

      <!-- FORM POPUP MENGAMBANG -->
      <div v-if="formVisible" class="modal-overlay">
        <div class="modal-content">
          <h3 class="modal-title">{{ isEditing ? 'Edit Laporan' : 'Tambah Laporan' }}</h3>
          <form class="modal-form">
            <select v-model="selectedTahunAjaran" required>
              <option value="" disabled>Pilih Tahun Ajaran</option>
              <option v-for="tahun in tahunAjaranOptions" :key="tahun.id" :value="tahun.id">
                {{ tahun.nama }}
              </option>
            </select>

            <div>
              <select
                v-model="selectedKelas"
                required
                :disabled="!selectedTahunAjaran"
              >
                <option value="" disabled>Pilih Kelas</option>
                <option v-for="kelas in kelasOptionsRaw" :key="kelas.id_class" :value="kelas.id_class">
                  {{ kelas.class }}
                </option>
              </select>

              <select v-model="selectedSiswa" required :disabled="!selectedKelas">
                <option value="" disabled>Pilih Siswa</option>
                <option v-for="siswa in siswaOptions" :key="siswa.id_child" :value="siswa.id_child">
                  {{ siswa.name }} ({{ siswa.nis }})
                </option>
              </select>
            </div>

            <label class="custom-file-upload" :class="{ 'file-selected': formData.file }">
              <div class="upload-content">
                <template v-if="!formData.file">
                  <div class="icon">
                    <img src="@/assets/arrow-up-sm.png" alt="Upload Icon" class="upload-icon" />
                  </div>
                  <span class="upload-text">Pilih File</span>
                </template>
                <template v-else>
                  <span class="file-name">{{ fileNameDisplay }}</span>
                </template>
              </div>
              <input type="file" name="file" @change="handleFile" accept="application/pdf"  required/>
            </label>
            <div class="modal-actions">
              <button type="button" class="laporan-button save" @click="submitForm">
                {{ isEditing ? 'Simpan Perubahan' : 'Unggah' }}
              </button>
              <button type="button" class="laporan-button" @click="cancelForm">Batal</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tabel -->
      <div class="table-wrapper">
        <table class="laporan-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>NIS</th>
              <th>Kelas</th>
              <th>Tahun Ajaran</th>
              <th>File</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(laporan, index) in daftarLaporan" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ laporan.nama }}</td>
              <td>{{ laporan.nis }}</td>
              <td>{{ laporan.kelas }}</td>
              <td>{{ laporan.tahunAjaran }}</td>
              <td>
                <a
                  v-if="laporan.file"
                  :href="laporan.file"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  {{ laporan.fileName || 'Lihat File' }}
                </a>
                <span v-else>-</span>
              </td>

              <td>
                <button class="laporan-button unduh" @click="unduhLaporan(index)">Unduh</button>
                <button class="laporan-button edit" @click="editLaporan(index)">Edit</button>
                <button class="laporan-button delete" @click="mintaKonfirmasiHapus(index)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Komponen Konfirmasi -->
    <ConfirmModal
      v-if="confirmVisible"
      :title="'Hapus Laporan'"
      :message="'Yakin ingin menghapus laporan ini?'"
      :konfirmasi="'IYA'"
      :batalkan="'BATAL'"
      @confirm="konfirmasiHapus"
      @cancel="confirmVisible = false"
    />
    <div class="popup-wrapper" :style="{ zIndex: 1000 }">
      <PopupMessage
          v-if="showPilihPdf"
          :title="'Silakan pilih file PDF terlebih dahulu!'"
          @close="showPilihPdf = false"
      />
      <PopupMessage
          v-if="showSuksesUnggah"
          :title="'Laporan berhasil diunggah!'"
          @close="showSuksesUnggah = false"
      />
      <PopupMessage
          v-if="showSuksesEdit"
          :title="'Laporan berhasil diupdate!'"
          @close="showSuksesEdit = false"
      />
      <PopupMessage
          v-if="showFailUp"
          :title="'Gagal upload/update laporan'"
          @close="showFailUp = false"
      />
      <PopupMessage
          v-if="showSuksesHapus"
          :title="'Laporan berhasil dihapus'"
          @close="showSuksesHapus = false"
      />
      <PopupMessage
          v-if="showFailDelete"
          :title="'Gagal menghapus laporan'"
          @close="showFailDelete = false"
      />
      <PopupMessage
          v-if="showFileNotExist"
          :title="'File tidak tersedia'"
          @close="showFileNotExist = false"
      />
      <PopupMessage
          v-if="showAlertUnggah"
          :title="'Harap mengisi semua masukan'"
          @close="showAlertUnggah = false"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ConfirmModal from '@/components/BlokPopup.vue'
import PopupMessage from '@/components/MessagePopup.vue'
import axios from 'axios'

const showPilihPdf = ref(false)
const showSuksesUnggah = ref(false)
const showSuksesEdit = ref(false)
const showFailUp = ref(false)
const showSuksesHapus = ref(false)
const showFailDelete = ref(false)
const showFileNotExist = ref(false)
const showAlertUnggah = ref(false)

const router = useRouter()
const daftarLaporan = ref([])
const formVisible = ref(false)
const isEditing = ref(false)
const editingIndex = ref(null)
const confirmVisible = ref(false)
const indexToDelete = ref(null)
const isRestoring = ref(false)
const tahunAjaranOptions = ref([])
const kelasOptionsRaw = ref([])
const siswaOptions = ref([])
const selectedTahunAjaran = ref('')
const selectedKelas = ref('')
const selectedSiswa = ref('')
const file = ref(null)
const formData = ref({
  nama: '',
  kelas: '',
  tahunAjaran: '',
  file: null,
  fileName: '',
})

async function fetchLaporan() {
  try {
    const res = await axios.get('http://localhost:8000/api/laporan')
    // Mapping agar tabel mudah dibaca
    daftarLaporan.value = res.data.data.map(laporan => ({
      id: laporan.id,
      nama: laporan.child?.name || '-',
      nis: laporan.child?.nis || '-',
      kelas: laporan.class?.class || '-',
      kelas_id: laporan.class?.id_class,
      // Tampilkan nama tahun ajaran, bukan id
      tahunAjaran: laporan.child?.tahun_ajaran?.nama || '-', // <-- ini yang diubah
      id_tahun_ajaran: laporan.child?.tahun_ajaran?.id || '',
      id_child: laporan.child?.id_child,
      file: laporan.file,
      fileName: laporan.file ? laporan.file.split('/').pop() : '',
    }))
  } catch (err) {
    console.error('Gagal mengambil daftar laporan:', err)
  }
}

// Panggil saat mounted dan setelah upload
onMounted(() => {
  fetchLaporan()
})

// Ambil tahun ajaran saat mounted
onMounted(async () => {
  const tahunRes = await axios.get('http://localhost:8000/api/tahun-ajaran')
  tahunAjaranOptions.value = tahunRes.data.data // [{id, nama}, ...]

  // Jika ingin langsung ambil kelas
  const kelasRes = await axios.get('http://localhost:8000/api/classes')
  kelasOptionsRaw.value = kelasRes.data.data // [{id_class, class, ...}]
})

// Ambil siswa berdasarkan filter
watch([selectedTahunAjaran, selectedKelas], async () => {
  if (selectedTahunAjaran.value && selectedKelas.value) {
    const res = await axios.get('http://localhost:8000/api/children', {
      params: {
        kelas: selectedKelas.value,
        tahun: selectedTahunAjaran.value,
      }
    })
    siswaOptions.value = res.data.data // [{id_child, name, nis, id_parent, ...}]
  } else {
    siswaOptions.value = []
  }
})

const kelasOptions = computed(() => {
  const tahun = formData.value.tahunAjaran
  return tahun ? Object.keys(dataSiswa.value[tahun] || {}) : []
})

const namaOptions = computed(() => {
  const tahun = formData.value.tahunAjaran
  const kelas = formData.value.kelas
  return tahun && kelas ? dataSiswa.value[tahun]?.[kelas]?.map((siswa) => siswa.nama) || [] : []
})

watch(
  () => formData.value.tahunAjaran,
  () => {
    if (!isRestoring.value) {
      formData.value.kelas = ''
      formData.value.nama = ''
    }
  },
)

watch(
  () => formData.value.kelas,
  () => {
    if (!isRestoring.value) {
      formData.value.nama = ''
    }
  },
)

watch(
  () => formData.value.nama,
  (val) => {
    const tahun = formData.value.tahunAjaran
    const kelas = formData.value.kelas
    const siswa = dataSiswa.value[tahun]?.[kelas]?.find((s) => s.nama === val)
    formData.value.nis = siswa?.nis || '' // Menarik nis berdasarkan nama yang dipilih
  },
)

const resetForm = () => {
  selectedTahunAjaran.value = ''
  selectedKelas.value = ''
  selectedSiswa.value = ''
  formData.value.file = null
  formData.value.fileName = ''
  editingIndex.value = null
}

const handleFile = (e) => {
  const file = e.target.files[0];
  if (file && file.size > 10 * 1024 * 1024) { // 10 MB
    alert('Ukuran file maksimal 10 MB.');
    e.target.value = ''; // reset input file
    formData.value.file = null;
    formData.value.fileName = '';
    return;
  }
  formData.value.file = file;
  formData.value.fileName = file.name;
}


const fileNameDisplay = computed(() => formData.value.file?.name || 'Pilih File')

const submitForm = async () => {
  try {
    if (selectedTahunAjaran .value === '' || selectedKelas.value === '' || selectedSiswa.value === '') {
      showAlertUnggah.value = true
      return
    } else if (!formData.value.file) {
      showPilihPdf.value = true
      return
    }
    const siswa = siswaOptions.value.find(s => s.id_child == selectedSiswa.value)
    const idTeacher = await getTeacherIdByNip(selectedNip.value)
    const form = new FormData()
    form.append('file', formData.value.file)

    if (isEditing.value && editingIndex.value !== null) {
      // Edit laporan (PUT)
      const laporan = daftarLaporan.value[editingIndex.value]
      form.append('_method', 'PUT')
      await axios.post(`http://localhost:8000/api/laporan/${laporan.id}`, form, {} )
      showSuksesEdit.value = true
    } else {
      // Tambah laporan (POST)
      form.append('id_teacher', idTeacher)
      form.append('id_class', selectedKelas.value)
      form.append('id_parent', siswa?.id_parent)
      form.append('id_child', selectedSiswa.value)
      await axios.post('http://localhost:8000/api/laporan', form)
      showSuksesUnggah.value = true
    }
    formVisible.value = false
    resetForm()
    await fetchLaporan()
  } catch (err) {
    showFailUp.value = true
  }
}

const getFileUrl = (file) => {
  try {
    return file ? URL.createObjectURL(file) : ''
  } catch (e) {
    console.error('Gagal membuat URL file:', e)
    return ''
  }
}

const showForm = (mode) => {
  formVisible.value = true
  isEditing.value = mode === 'edit'
  if (!isEditing.value) {
    resetForm()
  }
}

const editLaporan = (index) => {
  const laporan = daftarLaporan.value[index]
  isRestoring.value = true

  // Set dropdown ke id asli
  selectedTahunAjaran.value = laporan.id_tahun_ajaran
  selectedKelas.value = laporan.kelas_id
  selectedSiswa.value = laporan.id_child


  // Kosongkan file, user harus upload ulang
  formData.value.file = null
  formData.value.fileName = ''
  editingIndex.value = index

  showForm('edit')

  setTimeout(() => {
    isRestoring.value = false
  }, 0)
}

const cancelForm = () => {
  formVisible.value = false
  resetForm()
}

const mintaKonfirmasiHapus = (index) => {
  indexToDelete.value = index
  confirmVisible.value = true
}

const konfirmasiHapus = async () => {
  if (indexToDelete.value !== null) {
    const laporan = daftarLaporan.value[indexToDelete.value]
    try {
      await axios.delete(`http://localhost:8000/api/laporan/${laporan.id}`)
      daftarLaporan.value.splice(indexToDelete.value, 1)
      showSuksesHapus.value = true
    } catch (err) {
      console.error(err)
      showFailDelete.value = true
    }
  }
  confirmVisible.value = false
  indexToDelete.value = null
}

const goBack = () => {
  router.push('home')
}

const user = JSON.parse(localStorage.getItem('user') || '{}')
const selectedNip = ref(user.nip || '')
const teacherName = ref(user.name || '')

const getTeacherIdByNip = async (nip) => {
  const res = await axios.get('http://localhost:8000/api/teachers', { params: { nip } })
  // Pastikan endpoint ini mengembalikan array guru, ambil id_teacher pertama yang cocok
  return res.data.data[0]?.id_teacher || ''
}

const unduhLaporan = (index) => {
  const laporan = daftarLaporan.value[index]
  if (laporan.file) {
    window.open(laporan.file, '_blank')
  } else {
    showFileNotExist.value = true
  }
}
</script>

<style scoped>
.laporan-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  margin: 0;
  padding: 0;
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

.form-laporan {
  flex: 1;
  background-color: #fff;
  padding: 1rem;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  height: 100%;
  width: 100vw;
  box-sizing: border-box;
  overflow-y: auto;
}

.title {
  text-align: center;
  font-weight: bold;
  color: #000;
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
}

.table-wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.laporan-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  font-size: 0.85rem;
  table-layout: fixed;
  min-width: 1000px;
  color: #000;
}

.laporan-table th,
.laporan-table td {
  border: 1px solid #ccc;
  padding: 6px 8px;
  text-align: left;
  word-break: break-word;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  vertical-align: middle;
}

.laporan-table th:nth-child(1),
.laporan-table td:nth-child(1) {
  width: 5%;
}

.laporan-table th:nth-child(2),
.laporan-table td:nth-child(2) {
  width: 25%;
}

.laporan-table th:nth-child(3),
.laporan-table td:nth-child(3) {
  width: 15%;
}

.laporan-table th:nth-child(4),
.laporan-table td:nth-child(4) {
  width: 12%;
}

.laporan-table th:nth-child(5),
.laporan-table td:nth-child(5) {
  width: 15%;
}

.laporan-table th:nth-child(6),
.laporan-table td:nth-child(6) {
  width: 20%;
}

.laporan-table th:nth-child(7),
.laporan-table td:nth-child(7) {
  width: 25%;
}


.laporan-table th {
  background-color: #f2f2f2;
  color: #333;
  font-weight: 600;
}

.laporan-button {
  padding: 6px 12px;
  font-size: 0.8rem;
  background-color: #a59f9f;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
  margin: 0.2rem;
}

.laporan-button.add {
  padding: 0.4rem;
  font-size: 0.8rem;
  margin-bottom: 0.5rem;
}

.laporan-button.add,
.laporan-button.save,
.laporan-button.unduh {
  background-color: #31d249;
}
.laporan-button.add:hover,
.laporan-button.save:hover,
.laporan-button.unduh:hover {
  background-color: #27c04d;
}

.laporan-button:hover {
  background-color: #bdbdbd;
}

.laporan-button.edit {
  background-color: #e4cd21;
}

.laporan-button.edit:hover {
  background-color: #fcb454;
}

.laporan-button.delete {
  background-color: #e74c3c;
}

.laporan-button.delete:hover {
  background-color: #d32f2f;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(2px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;
}

.modal-content {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease-in-out;
}

.modal-title {
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 1rem;
  text-align: center;
  color: #1f3a2d;
}

.modal-form select {
  display: block;
  width: 100%;
  margin-bottom: 1rem;
  padding: 0.6rem;
  font-size: 0.9rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.custom-file-upload {
  border: 2px dashed #aaa;
  border-radius: 10px;
  background: #f0f0f0;
  color: #333;
  cursor: pointer;
  position: relative;
  width: 80%;
  height: 100px;
  margin: 0 auto;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  box-sizing: border-box;
  margin-bottom: 0.7rem;
}

.custom-file-upload.file-selected {
  background-color: #d6f5e3;
  border: 2px solid #4caf50;
}

.custom-file-upload input[type='file'] {
  opacity: 0;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
  margin: 0 atuo;
}

.file-name {
  font-size: 0.8rem;
  font-weight: 500;
  color: #2e7d32;
  text-align: center;
}

.upload-text::after {
  content: 'Unggah file berupa pdf (Maksimal 10Mb)';
  display: block;
  font-size: 0.6rem;
  color: #666;
  margin-bottom: 0.5rem;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10%);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (min-width: 768px) {
  .page-header {
    display: none;
  }

  .form-laporan {
    min-height: calc(100vh - 84px);
    max-width: 100%;
  }

  .title {
    text-align: start;
    font-size: 1.3rem;
  }

  .laporan-table th:nth-child(1),
  .laporan-table td:nth-child(1) {
    width: 3%;
  }

  .laporan-table th:nth-child(2),
  .laporan-table td:nth-child(2) {
    width: 20%;
  }

  .laporan-table th:nth-child(3),
  .laporan-table td:nth-child(3) {
    width: 10%;
  }

  .laporan-table th:nth-child(4),
  .laporan-table td:nth-child(4) {
    width: 8%;
  }

  .laporan-table th:nth-child(5),
  .laporan-table td:nth-child(5) {
    width: 10%;
  }

  .laporan-table th:nth-child(7),
  .laporan-table td:nth-child(7) {
    width: 20%;
  }
}
</style>
