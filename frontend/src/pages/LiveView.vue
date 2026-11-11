<template>
  <div class="page-wrap">
    <h1 class="page-h1">Live View</h1><p class="page-sub">{{ cams.length }} cameras · CCTV & drone feeds</p>
    <div class="row g-3 mt-1">
      <div class="col-lg-4 col-md-6" v-for="c in cams" :key="c.id">
        <div class="card overflow-hidden">
          <div class="ratio ratio-16x9 position-relative" style="background:#0b0f16">
            <div class="position-absolute top-0 start-0 m-2 text-danger small fw-bold">● LIVE</div>
            <div class="position-absolute top-0 end-0 m-2"><span class="pill" :class="c.type==='drone'?'pill-iso':'pill-gray'">{{ c.type }}</span></div>
            <div class="d-flex align-items-center justify-content-center text-secondary"><i class="fa-solid fa-play" style="font-size:28px"></i></div>
          </div>
          <div class="p-3"><b>{{ c.name }}</b><div class="text-muted2 small text-truncate">{{ c.location||name(c.site) }}</div></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { load } from '../services/api'
const cams=ref([])
const name=v=> v&&(v.name||v)||'—'
onMounted(async()=> cams.value=await load('cameras'))
</script>
