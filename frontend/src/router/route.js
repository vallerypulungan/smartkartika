import { createRouter, createWebHistory } from 'vue-router'
import FormPswd from '@/views/FormPswd.vue'
import ProfilView from '@/views/ProfilView.vue'
import DashboardGuru from '@/views/DasboardGuru.vue'
import UpNews from '@/components/UpNews.vue'
import MainHome from '@/components/MainHome.vue'
import DashboardOrtu from '@/views/DashboardOrtu.vue'
import LaporanOrtu from '@/components/LaporanOrtu.vue'
import ProfilOrtu from '@/components/ProfilOrtu.vue'
import WelcomePage from '@/views/WelcomePage.vue'
import LaporanView from '@/components/LaporanView.vue'
import RegisterTeacher from '@/views/RegisterTeacher.vue'
import KelolaKegiatan from '@/components/KelolaKegiatan.vue'
import ViewKegiatan from '@/components/ViewKegiatan.vue'
import TabelKelas from '@/components/TabelKelas.vue'
import InformasiSiswa from '@/components/InformasiSiswa.vue'
import InformasiGuru from '@/components/InformasiGuru.vue'
import { usePopupStore } from '@/stores/popupStores'
import { createPinia } from 'pinia'

const pinia = createPinia()

const routes = [
  { path: '/', component: WelcomePage },
  { path: '/login', name: 'login', component: FormPswd },
  { path: '/registerteach', component: RegisterTeacher },
  {
    path: '/dashboard-guru',
    name: 'dashboardGuru',
    component: DashboardGuru,
    meta: { role: 'guru' },
    children: [
      { path: 'home', name: 'home', component: MainHome },
      { path: 'profil', name: 'profil', component: ProfilView },
      { path: 'upload', name: 'upload', component: LaporanView },
      { path: 'upberita', name: 'upberita', component: UpNews },
      { path: 'manage', name: 'manage', component: KelolaKegiatan },
      { path: 'kelas', name: 'kelas', component: TabelKelas },
      { path: 'infosiswa', name: 'infosiswa', component: InformasiSiswa },
      { path: 'infoguru', name: 'infoguru', component: InformasiGuru }
    ],
   },
  { path: '/dashboardortu', component: DashboardOrtu, meta: { role: 'ortu' } },
  { path: '/rapor', component: LaporanOrtu, meta: { role: 'ortu' } },
  { path: '/profilp', component: ProfilOrtu, meta: { role: 'ortu' } },
  { path: '/viewkegiatan', component: ViewKegiatan, meta: { role: 'ortu' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true'
  const userRole = localStorage.getItem('role')

  const popup = usePopupStore()

  if (to.meta.role) {
    if (!isLoggedIn) {
      next('/login')
    } else if (to.meta.role !== userRole) {
      popup.open('Akses ditolak! Anda tidak memiliki izin untuk halaman ini.')
      setTimeout(() => {
        next(false)
      }, 300)
    } else {
      next()
    }
  } else {
    next()
  }
})


export default router
