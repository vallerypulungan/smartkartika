<template>
  <div class="container">
    <div class="page-header">
      <div class="left">
        <button class="back-button" @click="goBack">
          <img src="@/assets/arrow-left.png" alt="kembali" />
        </button>
      </div>
      <div class="center">
        <h1 class="app-title">Kegiatan</h1>
      </div>
      <div class="right"></div>
    </div>
    <div class="news-container">
      <!-- Artikel Utama -->
      <div class="main-article">
        <img :src="mainArticle.image" alt="Main" class="main-image" />
        <div class="main-content">
          <p class="main-date">{{ mainArticle.date }}</p>
          <h2 class="main-title">{{ mainArticle.title }}</h2>
          <textarea
            class="main-description"
            :value="mainArticle.description"
            readonly
          ></textarea>
        </div>
      </div>

      <!-- Artikel Lainnya -->
      <div class="wrapper">
        <div class="side-articles">
          <div
            class="side-article"
            v-for="(article, index) in sideArticles"
            :key="index"
             @click="switchMainArticle(article.realIndex)"
          >
            <img :src="article.image" alt="Side" class="side-image" />
            <div class="side-content">
              <p class="side-date">{{ article.date }}</p>
              <h3 class="side-title">{{ article.title }}</h3>
            </div>
          </div>
        </div>
        <div class="pagination-buttons">
          <button
            class="prev"
            @click="isMobile ? prevPageMobile() : prevPage()"
            :disabled="isPrevDisabled"
          >
            Previous
          </button>
          <button @click="isMobile ? nextPageMobile() : nextPage()" :disabled="isNextDisabled">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const isMobile = ref(window.innerWidth <= 768)
const articlesPerPage = ref(5)
const currentPage = ref(0)
const previousIsMobile = ref(isMobile.value)
const selectedArticleIndex = ref(0)

const allArticles = ref([])

const mainArticle = computed(() => allArticles.value[selectedArticleIndex.value] || {})

const sideArticles = computed(() => {
  const start = currentPage.value * articlesPerPage.value
  const end = start + articlesPerPage.value

  return allArticles.value.slice(start, end)
    .map((article, idx) => ({ ...article, realIndex: start + idx }))
    .filter(article => article.realIndex !== selectedArticleIndex.value)
})



function switchMainArticle(realIndex) {
  selectedArticleIndex.value = realIndex
}


function nextPageMobile() {
  if ((currentPage.value + 1) * articlesPerPage.value < allArticles.value.length) {
    currentPage.value++
    selectedArticleIndex.value = currentPage.value * articlesPerPage.value
  }
}

function prevPageMobile() {
  if (currentPage.value > 0) {
    currentPage.value--
    selectedArticleIndex.value = currentPage.value * articlesPerPage.value
  }
}

function handleResize() {
  const wasMobile = previousIsMobile.value
  isMobile.value = window.innerWidth <= 768
  articlesPerPage.value = isMobile.value ? 1 : 5
  if (wasMobile !== isMobile.value) {
    const newPage = Math.floor(selectedArticleIndex.value / articlesPerPage.value)
    currentPage.value = newPage
  }
  previousIsMobile.value = isMobile.value
}

onMounted(async () => {
  handleResize()
  window.addEventListener('resize', handleResize)
  const res = await axios.get('https://smarkatika-si.my.id/api/activities')
  allArticles.value = res.data.data.map(item => ({
    image: item.image_url,
    title: item.activity_tittle,
    date: item.date,
    description: item.description,
  }))
})

function nextPage() {
  if ((currentPage.value + 1) * articlesPerPage.value < allArticles.value.length) {
    currentPage.value++
  }
}

function prevPage() {
  if (currentPage.value > 0) {
    currentPage.value--
  }
}

const isNextDisabled = computed(() => {
  return (currentPage.value + 1) * articlesPerPage.value >= allArticles.value.length
})

const isPrevDisabled = computed(() => {
  return currentPage.value === 0
})

const goBack = () => {
  router.back()
}
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #dcd0d0;
  overflow-x: auto;
  width: 100%;
}


.page-header {
  margin-bottom: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 0.5rem;
  background:
    linear-gradient(rgba(162, 123, 92, 0.8), rgba(162, 123, 92, 0.8)), url('@/assets/bg.png');
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
  filter: invert(1);
}

.app-title {
  font-size: 1.3rem;
  font-weight: bold;
  color: #fff;
  margin: 0 auto;
}

.news-container {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  width: 100%;
  margin: 1rem auto;
  padding: 0 2rem;
}

.main-article {
  flex: 2;
  background-color: #fff;
  border-radius: 12px;
  cursor: default;
  max-width: 900px;
}

.main-image {
  width: 900px;
  height: 300px;
  object-fit: cover;
}

.main-content {
  padding: 1rem;
  overflow-y: auto;
}

.main-date {
  font-size: 0.7rem;
  color: #888;
}

.main-title {
  font-size: 1.2rem;
  color: #222;
  font-weight: bold;
}

.main-description {
  font-size: 0.8rem;
  color: #555;
  min-height: 70px;
  width: 100%;
  resize: vertical;
  border: none;
  background-color: transparent;
  outline: none;
  font-family: inherit;
}


.side-articles {
  flex: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
  gap: 0.5rem;
}


.side-article {
  display: flex;
  gap: 1rem;
  background-color: #fff;
  padding: 0.6rem;
  border-radius: 10px;
  align-items: center;
  cursor: pointer;
  transition: background-color 0.2s;
  width: 275px;
}

.side-article:hover {
  background-color: #f2f2f2;
}

.side-image {
  width: 100px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.side-content {
  flex: 1;
}

.side-date {
  font-size: 0.6rem;
  color: #888;
}

.side-title {
  font-size: 0.8rem;
  font-weight: bold;
  color: #333;
}

.pagination-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
}

.pagination-buttons button {
  padding: 0.5rem 1rem;
  background-color: #32b13b;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s;
}

.pagination-buttons .prev {
  background-color: #555;
}

.pagination-buttons button:disabled {
  background-color: #989393;
  cursor: not-allowed;
}

.pagination-buttons button:hover:not(:disabled) {
  background-color: #2980b9;
}

@media (max-width: 768px) {
  .news-container {
    flex-direction: column;
  }

  .main-article {
    flex: 1;
    width: 400px;
    height: 150px;
    margin: 0 auto;
  }

  .main-date {
    font-size: 0.5rem;
    margin-top: 0;
  }

  .main-image {
    width: 400px;
    height: 250px;
    object-fit: cover;
  }

  .main-title {
    font-size: 0.8rem;
    font-weight: bold;
  }

  .main-description {
    font-size: 0.6rem;
  }
  .pagination-buttons {
    gap: 1rem;
    width: 40%;
  }

  .pagination-buttons button {
    flex: 1;
    padding: 0.4rem 0;
    font-size: 0.6rem;
  }
}
</style>
