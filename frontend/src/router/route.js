import { createRouter, createWebHistory } from 'vue-router'
import FormPswd from '@/views/FormPswd.vue'
import loginPage from '@/views/loginPage.vue'
import ProfilView from '@/views/ProfilView.vue'
import DashboardGuru from '@/views/DasboardGuru.vue'
import ReportForm from '@/components/ReportForm.vue'
import UpNews from '@/components/UpNews.vue'
import AddAct from '@/components/AddAct.vue'
import EditAct from '@/components/EditAct.vue'
import EditKegiatan from '@/components/EditKegiatan.vue'
import RemoveAct from '@/components/RemoveAct.vue'
import RmAct from '@/components/RmAct.vue'
import ManageAct from '@/components/ManageAct.vue'
import MainHome from '@/components/MainHome.vue'
import CommentTeach from '@/components/CommentTeach.vue'

const routes = [
  { path: '/', name: 'auth', component: loginPage },
  { path: '/berita', name: 'home', component: MainHome },
  { path: '/login', name: 'login', component: FormPswd },
  { path: '/profil', name: 'profil', component: ProfilView },
  { path: '/dashboard-guru', name: 'dashboardGuru', component: DashboardGuru },
  { path: '/laporan', name: 'laporan', component: ReportForm },
  { path: '/kelola', name: 'manage', component: ManageAct },
  { path: '/unggah', name: 'uploadNews', component: UpNews },
  { path: '/add', name: 'upload', component: AddAct },
  { path: '/edit', name: 'edit', component: EditAct },
  { path: '/edit/:id', name: 'EditKegiatan', component: EditKegiatan },
  { path: '/remove', name: 'remove', component: RemoveAct },
  { path: '/remove/:id', name: 'HapusKegiatan', component: RmAct },
  { path: '/comment', name: 'comment', component: CommentTeach }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
