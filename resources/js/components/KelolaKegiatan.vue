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
      <h2 class="title">KELOLA KEGIATAN</h2>
      <button class="laporan-button add" @click="showForm(false)">Tambah Kegiatan</button>

      <!-- FORM POPUP -->
      <div v-if="formVisible" class="modal-overlay" @click.self="cancelForm">
        <div class="modal-content">
          <h3 class="modal-title">{{ isEditing ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h3>
          <form @submit.prevent="submitForm" class="modal-form">
            <input v-model="formItem.judul" placeholder="Judul" required/>
            <input v-model="formItem.tanggal" type="date" required/>
            <textarea v-model="formItem.deskripsi" placeholder="Deskripsi" required></textarea>
            <div class="file-wrapper">
              <input
                type="file"
                id="fileInput"
                ref="fileInput"
                class="hidden"
                @change="handleImageUpload"
                accept="image/*"
                required
              />
            </div>
            <div class="modal-actions">
              <button class="laporan-button save" type="submit">
                {{ isEditing ? 'Update' : 'Simpan' }}
              </button>
              <button class="laporan-button batal" type="button" @click="cancelForm">Batal</button>
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
              <th>Judul</th>
              <th>Tanggal</th>
              <th>Gambar</th>
              <th>Deskripsi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in items" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ item.judul }}</td>
              <td>{{ item.tanggal }}</td>
              <td>{{ item.imageName }}</td>
              <td v-html="formatNewlines(item.deskripsi)"></td>
              <td>
                <button class="laporan-button edit" @click="editItem(index)">Edit</button>
                <button class="laporan-button delete" @click="deleteItem(index)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <PopupConfirm
      v-if="showSaveConfirm"
      :title="'HAPUS KEGIATAN'"
      :message="'Apakah anda yakin untuk menghpaus kegiatan ini?'"
      :konfirmasi="'HAPUS'"
      :batalkan="'BATAL'"
      @confirm="saveActivity"
      @cancel="cancelSave"
    />

    <div class="popup-wrapper" :style="{ zIndex: 9999 }">
      <PopupGagal
        v-if="showAlert"
        :title="'Gagal Menambahkan Kegiatan'"
        @close="cancelMessage"
      />
    </div>

    <PopupSuccess
      v-if="showSuccess"
      :title="'Kegiatan berhasil ditambahkan'"
      @close="cancelMessage"
    />

    <PopupSuccess
      v-if="showSuccessEdit"
      :title="'Perubahan berhasil disimpan'"
      @close="cancelMessage"
    />

    <PopupSuccess
      v-if="showSuccessDelete"
      :title="'Kegiatan berhasil dihapus'"
      @close="cancelMessage"
    />
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, reactive, onMounted } from 'vue'
import PopupConfirm from '@/components/BlokPopup.vue'
import PopupSuccess from '@/components/MessagePopup.vue'
import { useRouter } from 'vue-router'

const items = ref([])
const router = useRouter()

async function fetchActivities() {
  const res = await axios.get('http://localhost:8000/api/activities')
  items.value = res.data.data.map(item => ({
    id_activity: item.id_activity,
    judul: item.activity_tittle,
    tanggal: item.date,
    deskripsi: item.description,
    imageName: item.image_url ? item.image_url.split('/').pop() : '',
    imageUrl: item.image_url
  }))
}

onMounted(fetchActivities)

const formVisible = ref(false)
const isEditing = ref(false)
const editedIndex = ref(null)
const showSaveConfirm = ref(false)
const showSuccess = ref(false)
const showSuccessDelete = ref(false)
const showSuccessEdit = ref(false)
const showAlert = ref(false)
const indexToDelete = ref(null)
const nip = localStorage.getItem('nip');

const formItem = reactive({
  judul: '',
  tanggal: '',
  deskripsi: '',
  imageName: '',
  imageUrl: '', 
})

function showForm(editMode) {
  formVisible.value = true
  isEditing.value = editMode
  if (!editMode) {
    formItem.judul = ''
    formItem.tanggal = ''
    formItem.deskripsi = ''
    editedIndex.value = null
  }
}

function cancelForm() {
  formVisible.value = false
  isEditing.value = false
  editedIndex.value = null
}

async function submitForm() {
  const formData = new FormData();
  formData.append('activity_tittle', formItem.judul);
  formData.append('description', formItem.deskripsi);
  formData.append('date', formItem.tanggal);
  formData.append('nip', nip);

  const fileInput = document.querySelector('#fileInput');
  if (fileInput && fileInput.files[0]) {
    formData.append('image', fileInput.files[0]);
  }

  try {
    if (isEditing.value && editedIndex.value !== null) {
      const id = items.value[editedIndex.value].id_activity;
      await axios.post(`http://localhost:8000/api/activities/${id}?_method=PUT`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      showSuccessEdit.value = true;
    } else {
      await axios.post('http://localhost:8000/api/activities', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      showSuccess.value = true;
    }
    await fetchActivities(); 
    formVisible.value = false;
  } catch (error) {
    showAlert.value = true;
  }
}

function handleImageUpload(event) {
  const file = event.target.files[0]
  if (file) {
    formItem.imageName = file.name
  }
}

async function saveActivity() {
  if (indexToDelete.value !== null) {
    try {
      const id = items.value[indexToDelete.value].id_activity;
      await axios.delete(`http://localhost:8000/api/activities/${id}`);
      await fetchActivities(); 
      showSuccessDelete.value = true;
    } catch (error) {
      showAlert.value = true;
    } finally {
      indexToDelete.value = null;
      showSaveConfirm.value = false;
    }
  }
}

function cancelSave() {
  showSaveConfirm.value = false
  indexToDelete.value = null
}

function cancelMessage() {
  showSuccess.value = false
  showSuccessDelete.value = false
  showSuccessEdit.value = false
  showAlert.value = false
}

function editItem(index) {
  const item = items.value[index]
  formItem.judul = item.judul
  formItem.tanggal = item.tanggal
  formItem.deskripsi = item.deskripsi
  formItem.imageName = '' 
  formItem.imageUrl = item.imageUrl 
  editedIndex.value = index
  showForm(true)
}

function deleteItem(index) {
  indexToDelete.value = index
  showSaveConfirm.value = true
}

function formatNewlines(text) {
  return text.replace(/\n/g, '<br>')
}

const goBack = () => {
  router.push('home')
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
}

.laporan-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  font-size: 0.85rem;
  table-layout: fixed;
}

.laporan-table th,
.laporan-table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
  word-break: break-word;
  max-width: 200px;
  white-space: normal;
  color: #000;
}

.laporan-table th:nth-child(1),
.laporan-table td:nth-child(1) {
  width: 3%;
}

.laporan-table th:nth-child(2),
.laporan-table td:nth-child(2) {
  width: 12%;
}

.laporan-table th:nth-child(3),
.laporan-table td:nth-child(3) {
  width: 8%;
}

.laporan-table th:nth-child(4),
.laporan-table td:nth-child(4) {
  width: 12%;
}

.laporan-table th:nth-child(5),
.laporan-table td:nth-child(5) {
  width: 25%;
}

.laporan-table th:nth-child(6),
.laporan-table td:nth-child(6) {
  width: 15%;
}

.laporan-table th {
  background-color: #f2f2f2;
  color: #333;
  font-weight: 600;
}

.laporan-table tr:hover {
  background-color: #f9f9f9;
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
  background-color: #31d249;
  padding: 0.4rem;
  font-size: 0.8rem;
  margin-bottom: 0.5rem;
}
.laporan-button.save {
  background-color: #31d249;
}
.laporan-button.add:hover,
.laporan-button.save:hover {
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
  z-index: 1000;
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

.modal-form input,
textarea {
  display: block;
  width: 100%;
  margin-bottom: 1rem;
  padding: 0.6rem;
  font-size: 0.9rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  color: #000;
  font-family: inherit;
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
}

@media (max-width: 768px) {
  .table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .laporan-table th:nth-child(1),
  .laporan-table td:nth-child(1) {
    width: 3%;
  }

  .laporan-table th:nth-child(2),
  .laporan-table td:nth-child(2) {
    width: 13%;
  }

  .laporan-table th:nth-child(3),
  .laporan-table td:nth-child(3) {
    width: 10%;
  }

  .laporan-table th:nth-child(4),
  .laporan-table td:nth-child(4) {
    width: 15%;
  }

  .laporan-table th:nth-child(5),
  .laporan-table td:nth-child(5) {
    width: 20%;
  }

  .laporan-table th:nth-child(6),
  .laporan-table td:nth-child(6) {
    width: 15%;
  }

  .laporan-table {
    min-width: 900px;
  }

  .table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
  }
}
</style>
