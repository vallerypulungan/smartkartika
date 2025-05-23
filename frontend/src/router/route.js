import { createRouter, createWebHistory } from 'vue-router'
import FormPswd from '@/views/FormPswd.vue'
import loginPage from '@/views/loginPage.vue'
import ProfilView from '@/views/ProfilView.vue'
import DashboardGuru from '@/views/DasboardGuru.vue'
import ReportForm from '@/components/ReportForm.vue'
import UpNews from '@/components/UpNews.vue'
import MainHome from '@/components/MainHome.vue'
import DashboardOrtu from '@/views/DashboardOrtu.vue'
import LaporanOrtu from '@/components/LaporanOrtu.vue'
import ProfilOrtu from '@/components/ProfilOrtu.vue'
import WelcomePage from '@/views/WelcomePage.vue'
import LaporanView from '@/components/LaporanView.vue'
import RegisterParent from '@/views/RegisterParent.vue'
import RegisterTeacher from '@/views/RegisterTeacher.vue'
import KelolaKegiatan from '@/components/KelolaKegiatan.vue'
import ViewKegiatan from '@/components/ViewKegiatan.vue'
import TabelKelas from '@/components/TabelKelas.vue'
import InformasiSiswa from '@/components/InformasiSiswa.vue'
import InformasiGuru from '@/components/InformasiGuru.vue'

const routes = [
  { path: '/', component: WelcomePage },
  { path: '/auth', name: 'auth', component: loginPage },
  { path: '/berita', name: 'home', component: MainHome },
  { path: '/login', name: 'login', component: FormPswd },
  { path: '/profil', name: 'profil', component: ProfilView },
  { path: '/dashboard-guru', name: 'dashboardGuru', component: DashboardGuru,
      children: [
      { path: 'home', name: 'home', component: MainHome },
      { path: 'upload', name: 'upload', component: LaporanView },
      { path: 'upberita', name: 'upberita', component: UpNews },
      { path: 'manage', name: 'manage', component: KelolaKegiatan },
      { path: 'kelas', name: 'kelas', component: TabelKelas},
      { path : 'infosiswa', name: 'infosiswa', component: InformasiSiswa},
      { path: 'infoguru', name: 'infoguru', component: InformasiGuru}
    ],
   },
  { path: '/laporan', name: 'laporan', component: ReportForm },
  { path: '/dashboardortu', component: DashboardOrtu },
  { path: '/rapor', component: LaporanOrtu },
  { path: '/profilp', component: ProfilOrtu },
  { path: '/registerteach', component: RegisterTeacher },
  { path: '/registerparent', component: RegisterParent },
  { path: '/viewkegiatan', component: ViewKegiatan },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
