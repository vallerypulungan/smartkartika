<template>
  <div class="container">
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

    <div class="class-table-container">
      <div class="top-bar">
        <h1>Daftar Siswa</h1>
        <button class="add-class-button" @click="addClass">+ Tambah Kelas</button>
      </div>

      <div class="tabs">
        <button
          v-for="kelas in sortedTabs"
          :key="kelas.id_class || kelas.class"
          :class="['tab-button', { active: activeTab === kelas.class }]"

          @click="() => switchTab(kelas.class)"
        >
          {{ kelas.class }}
        </button>
      </div>

      <div class="filter-bar">
        <input
          type="text"
          placeholder="Cari nama siswa..."
          v-model="searchQuery"
          class="search-input"
        />
        <select
          v-model="selectedTahunAjaran"
          @change="fetchSiswaByKelasTahun"
          class="tahun-dropdown"
        >
          <option value="Semua">Semua Tahun Ajaran</option>
          <option v-for="tahun in tahunAjaranOptions" :key="tahun.id" :value="tahun.id">
            {{ tahun.nama }}
          </option>
        </select>
        <!-- Modal Tambah Tahun Ajaran -->
        <div v-if="showTambahTahunModal" class="modal-overlay">
          <div class="modal-box">
            <h2>Tambah Tahun Ajaran</h2>

            <input v-model="newTahunAjaran" type="text" placeholder="Contoh: 2025/2026" />

            <div class="modal-buttons">
              <button class="btn-cancel" @click="batalTambahTahun">Batal</button>
              <button class="btn-save" @click="simpanTahunAjaran">Simpan</button>
            </div>
          </div>
        </div>
        <button
          class="promote-button"
          v-if="!isLulusTab"
          @click="confirmPromoteOrLulus"
          :disabled="selectedStudents.length === 0"
        >
          {{ isLastRegularClass ? 'Lulus' : 'Naik Kelas' }}
        </button>
      </div>

      <div class="table-section">
        <table class="styled-table">
          <thead>
            <tr>
              <th><input type="checkbox" @change="toggleAll($event)" /></th>
              <th>No</th>
              <th>Nama</th>
              <th>NIS</th>
              <th>Tahun Ajaran</th>
              <th>Jenis Kelamin</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(student, index) in filteredData" :key="index">
              <td>
                <input type="checkbox" :value="student" v-model="selectedStudents" />
              </td>
              <td>{{ index + 1 }}</td>
              <td>{{ student.name }}</td>
              <td>{{ student.nis }}</td>
              <td>
                {{ student.tahunAjaran ? student.tahunAjaran.nama : '-' }}
              </td>
              <td>{{ student.gender }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bottom-bar">
        <button
          class="delete-class-button"
          @click="showDeleteClass = true"
          :disabled="activeTab === 'Lulus Tk' || classList.length <= 1"
        >
          🗑 Hapus Kelas Ini
        </button>
      </div>
    </div>

    <div class="popup-wrapper" :style="{ zIndex: 9999 }">
      <PopupConfirm
        v-if="showConfirmPromote"
        :title="'NAIK KELAS'"
        :message="'Apakah anda yakin untuk menaikkan kelas siswa ini?'"
        :konfirmasi="'IYA'"
        :batalkan="'BATAL'"
        @confirm="saveActivity"
        @cancel="cancelSave"
      />

      <PopupConfirm
        v-if="showConfirmLulus"
        :title="'MELULUSKAN SISWA'"
        :message="'Apakah anda yakin untuk meluluskan siswa ini?'"
        :konfirmasi="'IYA'"
        :batalkan="'BATAL'"
        @confirm="lulusActivity"
        @cancel="cancelSave"
      />

      <PopupConfirm
        v-if="showDeleteClass"
        :title="'HAPUS KELAS'"
        :message="'Apakah anda yakin untuk menghpaus kelas ini?'"
        :konfirmasi="'HAPUS'"
        :batalkan="'BATAL'"
        @confirm="deleteActivity"
        @cancel="cancelSave"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import PopupConfirm from '@/components/BlokPopup.vue'

const router = useRouter()

const classList = ref([]) // Data kelas dari database
const activeTab = ref('')
const classData = ref({}) // Data siswa per kelas

onMounted(async () => {
  await fetchClasses()
  await fetchTahunAjaran()
  if (classList.value.length > 0) {
    activeTab.value = classList.value[0].class
  }
})

async function fetchClasses() {
  try {
    const response = await axios.get('http://localhost:8000/api/classes')
    classList.value = response.data.data.filter(kls => kls.class !== 'Lulus TK')
    // Inisialisasi classData kosong per kelas
    classList.value.forEach(kls => {
      if (!classData.value[kls.class]) {
        classData.value[kls.class] = []
      }
    })
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error)
  }
}

const fetchTahunAjaran = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/tahun-ajaran')
    tahunAjaranOptions.value = response.data.data // [{id, nama}, ...]
  } catch (error) {
    console.error('Gagal mengambil tahun ajaran:', error)
  }
}

const fetchSiswaByKelasTahun = async () => {
  try {
    const kelasObj = classList.value.find(k => k.class === activeTab.value)
    const id_class = kelasObj ? kelasObj.id_class : null

    const params = {}
    if (id_class) params.kelas = id_class
    if (selectedTahunAjaran.value !== 'Semua') params.tahun = selectedTahunAjaran.value

    const response = await axios.get('http://localhost:8000/api/children', { params })
    // Simpan hanya untuk kelas aktif
    classData.value[activeTab.value] = response.data.data.map(siswa => ({
      ...siswa,
      tahunAjaran: siswa.tahun_ajaran, // mapping ke objek tahun_ajaran
    }))
  } catch (error) {
    console.error('Gagal mengambil data siswa:', error)
  }
}

const searchQuery = ref('')
const selectedTahunAjaran = ref('Semua')
const selectedStudents = ref([])
const showConfirmPromote = ref(false)
const showConfirmLulus = ref(false)
const showDeleteClass = ref(false)
const tahunAjaranOptions = ref([])
const showTambahTahunModal = ref(false)
const newTahunAjaran = ref('')

const filteredData = computed(() => {
  let data = classData.value[activeTab.value] || []

  if (searchQuery.value) {
    data = data.filter((siswa) =>
      siswa.nama.toLowerCase().includes(searchQuery.value.toLowerCase()),
    )
  }

  if (selectedTahunAjaran.value !== 'Semua') {
    data = data.filter((siswa) =>
      (siswa.tahunAjaran?.id || siswa.tahun_ajaran?.id) == selectedTahunAjaran.value
    )
  }

  return data
})

async function promoteStudents() {
  const regularClasses = classList.value
    .filter(k => k.class !== 'Lulus Tk')
    .sort((a, b) => a.id_class - b.id_class)

  const lulusClass = classList.value.find(k => k.class === 'Lulus Tk')

  for (const siswa of selectedStudents.value) {
    const idx = regularClasses.findIndex(k => k.id_class === siswa.id_class)
    let nextClassId = siswa.id_class

    if (idx === regularClasses.length - 1 && lulusClass) {
      nextClassId = lulusClass.id_class
    } else if (idx !== -1 && idx < regularClasses.length - 1) {
      nextClassId = regularClasses[idx + 1].id_class
    }

    // Ambil data siswa asli dari database
    let dbSiswa = {}
    try {
      const res = await axios.get(`http://localhost:8000/api/children/${siswa.id_child}`)
      dbSiswa = res.data.data || {}
    } catch (e) {
      dbSiswa = {}
    }

    // Siapkan payload: gunakan data lama, hanya ubah kelas/id_class
    const payload = {
      nama: dbSiswa.name || dbSiswa.nama,
      nis: dbSiswa.nis,
      tempatLahir: dbSiswa.tempat_lahir || dbSiswa.tempatLahir,
      tanggalLahir: dbSiswa.tanggal_lahir || dbSiswa.tanggalLahir,
      gender: dbSiswa.gender,
      kelas: nextClassId,
      tahunAjaran:
        dbSiswa.id_tahun_ajaran
        || (dbSiswa.tahun_ajaran && dbSiswa.tahun_ajaran.id)
        || (dbSiswa.tahunAjaran && dbSiswa.tahunAjaran.id),
      namaWali: dbSiswa.parent?.name || dbSiswa.namaWali,
      email: dbSiswa.parent?.email || dbSiswa.email,
      alamat: dbSiswa.parent?.alamat || dbSiswa.alamat,
      telepon: dbSiswa.parent?.num_telp || dbSiswa.telepon,
      username: dbSiswa.parent?.username || dbSiswa.username,
      kode: dbSiswa.parent?.password || dbSiswa.kode,
    }

    // Update siswa hanya jika id_class berubah
    if (nextClassId !== siswa.id_class) {
      await axios.put(`http://localhost:8000/api/children/${siswa.id_child}`, payload)
    }
  }

  await fetchSiswaByKelasTahun()
  selectedStudents.value = []
}

const isLastRegularClass = computed(() => {
  const regularClasses = sortedTabs.value.filter((k) => k !== 'Lulus Tk')
  return activeTab.value === regularClasses[regularClasses.length - 1]
})

const isLulusTab = computed(() => activeTab.value === 'Lulus Tk')

function toggleAll(event) {
  if (event.target.checked) {
    selectedStudents.value = [...filteredData.value]
  } else {
    selectedStudents.value = []
  }
}

function switchTab(kelas) {
  activeTab.value = kelas
}

const sortedTabs = computed(() => {
  const regular = classList.value.filter(k => k.class !== 'Lulus Tk')
  const lulus = classList.value.find(k => k.class === 'Lulus Tk')
  return lulus ? [...regular, lulus] : regular
})

async function addClass() {
  // Ambil semua kelas regular (tanpa "Lulus Tk")
  const regular = classList.value.filter(k => k.class !== 'Lulus Tk')
  // Cari huruf berikutnya setelah kelas terakhir
  let nextCharCode = 67 // 'C'
  let newName = ''
  let exists = true
  do {
    newName = `TK ${String.fromCharCode(nextCharCode)}`
    exists = regular.some(k => k.class === newName)
    nextCharCode++
  } while (exists)

  // Tambah ke database
  await axios.post('http://localhost:8000/api/classes', {
    class: newName,
    class_level: 'C'
  })

  await fetchClasses()

  // Urutkan ulang regular berdasarkan id_class ASC
  const sortedRegular = classList.value
    .filter(k => k.class !== 'Lulus Tk')
    .sort((a, b) => a.id_class - b.id_class)

  // Kelas baru pasti yang namanya newName
  const kelasBaru = sortedRegular.find(k => k.class === newName)
  // Kelas sebelum kelas baru (kelas lama yang akan dipindah)
  if (sortedRegular.length > 1 && kelasBaru) {
    const idxBaru = sortedRegular.findIndex(k => k.class === newName)
    const lastClassBeforeNew = idxBaru > 0 ? sortedRegular[idxBaru - 1].class : null
    if (lastClassBeforeNew) {
      const siswaToMove = classData.value[lastClassBeforeNew] || []
      // Pindahkan semua siswa ke kelas baru
      for (const siswa of siswaToMove) {
        await axios.put(`http://localhost:8000/api/children/${siswa.id_child}`, {
          ...siswa,
          id_class: kelasBaru.id_class
        })
      }
      // Refresh data siswa
      await fetchSiswaByKelasTahun()
    }
  }

  // Set tab aktif ke kelas baru
  activeTab.value = newName
}

async function deleteClass() {
  if (activeTab.value === 'Lulus Tk') return // Tidak bisa hapus Lulus TK
  const current = activeTab.value
  const keys = Object.keys(classData.value)
  if (keys.length <= 1) return

  const index = keys.indexOf(current)
  const nextTab = keys[index === 0 ? 1 : index - 1]
  activeTab.value = nextTab
  delete classData.value[current]

  // Hapus kelas dari server
  const kelasObj = classList.value.find(k => k.class === current)
  const id_class = kelasObj ? kelasObj.id_class : null
  if (id_class) {
    await axios.delete(`http://localhost:8000/api/classes/${id_class}`)
  }
}

function confirmPromoteOrLulus() {
  if (isLastRegularClass.value) {
    showConfirmLulus.value = true
  } else {
    showConfirmPromote.value = true
  }
}

function handleTahunAjaranChange(event) {
  const selected = event.target.value
  if (selected === 'Tambah') {
    showTambahTahunModal.value = true
  }
}

function simpanTahunAjaran() {
  if (newTahunAjaran.value.trim()) {
    tahunAjaranOptions.value.push(newTahunAjaran.value.trim())
    selectedTahunAjaran.value = newTahunAjaran.value.trim()
    newTahunAjaran.value = ''
    showTambahTahunModal.value = false
  }
}

function batalTambahTahun() {
  newTahunAjaran.value = ''
  showTambahTahunModal.value = false
}

function saveActivity() {
  promoteStudents()
  showConfirmPromote.value = false
}

function lulusActivity() {
  promoteStudents()
  showConfirmLulus.value = false
}

function cancelSave() {
  showConfirmPromote.value = false
  showConfirmLulus.value = false
  showDeleteClass.value = false
}

function deleteActivity() {
  deleteClass()
  showDeleteClass.value = false
}

const goBack = () => {
  router.push('home')
}

watch([activeTab, selectedTahunAjaran], () => {
  fetchSiswaByKelasTahun()
})
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  margin: 0;
  padding: 0;
}
.page-header {
  display: none;
}

.class-table-container {
  background-color: #ffffff;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  max-width: 100%;
  height: calc(100vh - 61px);
  display: flex;
  flex-direction: column;
}

/* Top Bar */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.top-bar h1 {
  margin: 0;
  color: #000;
  font-size: 1.3rem;
  font-weight: bold;
}

.add-class-button {
  background-color: #31d249;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}

.add-class-button:hover {
  background-color: #27c04d;
}

/* Tabs */
.tabs {
  display: flex;
  border-bottom: 2px solid #cbc8c8;
  margin-bottom: 16px;
}

.tab-button {
  padding: 6px 12px;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-weight: bold;
  font-size: 16px;
  color: #000;
  transition: all 0.2s;
}

.tab-button:hover {
  background-color: #e5e5e5;
}

.tab-button.active {
  border-bottom: 3px solid #27c04d;
  color: #27c04d;
}

.filter-bar {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin-bottom: 12px;
  gap: 12px;
}

.search-input {
  width: 20%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.tahun-dropdown {
  padding: 8px 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

/* Overlay latar belakang */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* Kotak modal */
.modal-box {
  background-color: #fff;
  width: 100%;
  max-width: 400px;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
}

/* Judul modal */
.modal-box h2 {
  margin-bottom: 16px;
  font-size: 18px;
  text-align: center;
  color: #2c3930;
}

/* Input */
.modal-box input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 14px;
}

/* Tombol */
.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.modal-buttons button {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.modal-buttons .btn-cancel {
  background-color: #cecccc;
}

.modal-buttons .btn-save {
  background-color: #31d249;
  color: white;
}

.modal-buttons .btn-save:hover {
  background-color: #27c04d;
}

.promote-button {
  background-color: #4c9ef5;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  margin-left: 8px;
}

.promote-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

/* Table */
.table-section {
  overflow-x: auto;
  flex-grow: 1;
  -webkit-overflow-scrolling: touch;
}

.styled-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  color: #333;
}

.styled-table thead {
  background-color: #f0f4f8;
  text-align: left;
  position: sticky;
  top: 0;
  z-index: 2;
  font-size: 0.85rem;
}

.styled-table th {
  font-weight: 525;
}

.styled-table th,
.styled-table td {
  padding: 10px 14px;
  border-bottom: 1px solid #e0e0e0;
  border: 1px solid #ccc;
  color: #000;
}

.styled-table th:nth-child(1),
.styled-table td:nth-child(1) {
  width: 3%;
}

.styled-table th:nth-child(2),
.styled-table td:nth-child(2) {
  width: 3%;
}

.styled-table th:nth-child(3),
.styled-table td:nth-child(3) {
  width: 30%;
}

.styled-table th:nth-child(4),
.styled-table td:nth-child(4) {
  width: 20%;
}

.styled-table tbody tr:hover {
  background-color: #f9fafb;
  transition: background-color 0.2s;
}

/* Bottom Bar */
.bottom-bar {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}

.delete-class-button {
  background-color: #ff5b5b;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}

.delete-class-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.delete-class-button:hover:enabled {
  background-color: #e54848;
}

@media (max-width: 768px) {
  .class-table-container {
    height: 100vh;
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

  .table-section {
    -webkit-overflow-scrolling: touch;
    width: 100%;
    overflow-x: auto;
    table-layout: fixed;
  }

  .styled-table {
    min-width: 800px;
  }
}
</style>
