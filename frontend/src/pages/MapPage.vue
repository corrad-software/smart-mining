<template>
  <div class="map-wrap">
    <div id="map" class="leaflet"></div>

    <!-- search + date -->
    <div class="float-tl">
      <div class="card p-1 mb-2"><div class="input-group border-0">
        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
        <input class="form-control border-0" v-model="q" placeholder="Search site name..." @input="filter"/></div></div>
      <div v-if="region!=='All Region'" class="card p-2 d-inline-flex flex-row align-items-center gap-2"><i class="fa-regular fa-calendar text-secondary"></i><span>11/12/2026</span></div>
    </div>

    <!-- segmented -->
    <div class="float-seg"><div class="seg">
      <button v-for="r in regions" :key="r" :class="{on:region===r}" @click="setRegion(r)">{{ r }}</button>
    </div></div>

    <!-- permit status + rain legend -->
    <div class="float-legend card p-3">
      <b class="d-block mb-2">Permit Status</b>
      <div class="d-flex gap-3 mb-3 small">
        <span><i class="fa-solid fa-circle-check text-primary me-1"></i>ACTIVE</span>
        <span><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>WARNING</span>
        <span><i class="fa-solid fa-circle-exclamation text-danger me-1"></i>EXPIRED</span>
      </div>
      <div class="d-flex justify-content-between"><b>Rain Intensity</b><span class="text-muted2 small">mm/h</span></div>
      <div class="rain-bar my-2"></div>
      <div class="d-flex justify-content-between tiny text-uppercase text-muted2">
        <span>Light<br>0</span><span>Moderate<br>0</span><span>Heavy<br>0</span><span>Violent<br>0</span>
      </div>
    </div>

    <!-- site detail panel -->
    <transition name="slide">
      <div v-if="selected" class="site-panel card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="btn-group btn-group-sm"><button class="btn btn-primary">Activate</button><button class="btn btn-light">Deactivate</button></div>
          <button class="btn btn-sm btn-light rounded-circle" @click="selected=null"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="d-flex gap-2">
          <span class="loc-ic"><i class="fa-solid fa-location-dot"></i></span>
          <h5 class="fw-bold mb-0">{{ selected.site_code }} ·{{ selected.name }}</h5>
        </div>
        <div class="mt-2"><span class="pill pill-amber">● EXPIRING SOON</span><span class="text-muted2 small ms-2">{{ reg(selected) }}</span></div>
        <div class="row g-2 mt-2 small">
          <div class="col-7"><div class="tile-label">Contractor</div><div>{{ reln(selected.contractor) }}</div></div>
          <div class="col-5"><div class="tile-label">Location</div><div class="mono">{{ selected.lat }}° N, {{ selected.lng }}° E</div></div>
          <div class="col-7 mt-2"><div class="tile-label">Permit Date</div><div class="mono">{{ fmtDate(selected.permit_date) }} – {{ fmtDate(selected.permit_date_end) }}</div></div>
          <div class="col-5 mt-2"><div class="tile-label">Luas Permit</div><div class="mono">{{ fmtDate(selected.luas_permit_date) }}</div></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div><b>Permit Details</b><div class="tiny text-muted2">Site authorization & compliance</div></div>
          <button class="btn btn-light btn-sm" :disabled="!permitUrl" @click="downloadPermit"><i class="fa-solid fa-download me-1"></i>Download</button>
        </div>
        <div class="warn-box mt-2"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i><b class="text-warning">WARNING</b>
          <div class="small">Permit expires in 48 day(s). Please renew your permit as soon as possible.</div></div>
        <div class="mt-3"><b>F001 Weekly Reports</b><div class="tiny text-muted2">Latest report submission status</div></div>
        <router-link class="btn btn-light w-100 mt-2" :to="`/site/${selected.id}?tab=reports`"><i class="fa-regular fa-file-lines me-2"></i>View Daily Report</router-link>
        <router-link class="btn btn-primary w-100 mt-2" :to="`/site/${selected.id}`"><i class="fa-regular fa-circle-info me-2"></i>View Site Details Page</router-link>
      </div>
    </transition>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { load, fmtDate, apiGet } from '../services/api'
const regions=['All Region','Northern Region','Southern Region']
const region=ref('All Region'), q=ref(''), selected=ref(null), permitUrl=ref(null)
async function openSite(s){
  selected.value=s; permitUrl.value=null
  const m=await apiGet(`sites/${s.id}/media`)
  if(m?.files?.length){
    const permits=m.files.filter(f=>f.category==='Permit & License Approval')
    // pick the most recent permit document
    permits.sort((a,b)=>(b.date||'').localeCompare(a.date||''))
    permitUrl.value=(permits[0]||null)?.url || null
  }
}
function downloadPermit(){ if(permitUrl.value) window.open(permitUrl.value,'_blank') }
let map, layer, allSites=[]
const reln=v=> v&&(v.name||v)||'—'
const reg=s=> s&&(s.region?.name||s.region)||'Region'
function setRegion(r){ region.value=r; draw() }
function filter(){ draw() }
const REGION_CENTROID={'Northern Region':[3.44,101.52],'Southern Region':[3.02,101.52]}
// deterministic permit status icon from permit end date
function permitIcon(s){
  if(!s.permit_date_end) return 'fa-circle-exclamation wexc'           // expired/unknown
  const days=Math.ceil((new Date(s.permit_date_end)-new Date())/864e5)
  if(days<0) return 'fa-circle-exclamation wexc'
  if(days<=90) return 'fa-triangle-exclamation wtri'                   // warning
  return 'fa-circle-check wok'                                         // active
}
function draw(){
  if(!layer) return; layer.clearLayers()
  const L=window.L
  if(region.value==='All Region'){
    // show only the region cluster ovals
    for(const [name,ll] of Object.entries(REGION_CENTROID)){
      const n=allSites.filter(s=>reg(s)===name).length
      L.marker(ll,{icon:L.divIcon({className:'',iconSize:[0,0],
        html:`<div class="region-pill"><span class="rp-ic"><i class="fa-solid fa-location-dot"></i></span><div><b>${name}</b><div class="small text-muted2">${n} sites</div></div></div>`})})
        .addTo(layer).on('click',()=>setRegion(name))
    }
    return
  }
  // specific region -> individual site pills
  let list=allSites.filter(s=>s.lat&&s.lng && reg(s)===region.value)
  if(q.value) list=list.filter(s=>(s.name||'').toLowerCase().includes(q.value.toLowerCase())||(s.site_code||'').toLowerCase().includes(q.value.toLowerCase()))
  list.forEach(s=>{
    const icon=L.divIcon({className:'',iconSize:[0,0],html:
      `<div class="site-pill"><div class="wx-col"><i class="fa-solid fa-sun"></i><span class="mmh">0 mm/h</span></div>`+
      `<span class="nm">${s.site_code||''} · ${s.name||''}</span>`+
      `<i class="fa-solid ${permitIcon(s)} st-ic"></i></div>`})
    const m=L.marker([+s.lat,+s.lng],{icon}).addTo(layer)
    m.on('click',()=> openSite(s))
    L.circleMarker([+s.lat,+s.lng],{radius:3,color:'#64748b',fillOpacity:.8}).addTo(layer)
  })
}
onMounted(async()=>{
  const L=window.L
  map=L.map('map',{zoomControl:false}).setView([3.2,101.5],10)
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map)
  L.control.zoom({position:'bottomright'}).addTo(map)
  layer=L.layerGroup().addTo(map)
  allSites=await load('sites'); draw()
})
</script>
<style scoped>
.map-wrap{position:relative;height:calc(100vh - 64px - 40px)}
.leaflet{position:absolute;inset:0}
.float-tl{position:absolute;top:18px;left:24px;width:330px;z-index:500}
.float-seg{position:absolute;top:18px;left:0;right:0;display:flex;justify-content:center;z-index:500;pointer-events:none}
.float-seg .seg{pointer-events:auto}
.float-legend{position:absolute;bottom:24px;left:24px;width:320px;z-index:500}
.rain-bar{height:8px;border-radius:6px;background:linear-gradient(90deg,#2b7fff,#36c,#7bd,#cd7,#e9b84a,#e06b3a,#e73e40)}
.site-panel{position:absolute;top:18px;right:24px;width:380px;max-height:calc(100% - 36px);overflow:auto;z-index:600;padding:18px}
.loc-ic{width:40px;height:40px;border-radius:12px;background:#0d6efd;color:#fff;display:grid;place-items:center;flex-shrink:0}
.warn-box{background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;padding:10px 14px}
.mono{font-family:monospace}.tiny{font-size:10px}
.slide-enter-from,.slide-leave-to{transform:translateX(20px);opacity:0}
.slide-enter-active,.slide-leave-active{transition:all .2s}
</style>
<style>
.site-pill{background:#fff;border-radius:999px;box-shadow:0 4px 16px rgba(16,24,40,.22);padding:7px 16px;display:inline-flex;align-items:center;gap:12px;white-space:nowrap;transform:translate(-50%,-130%);width:max-content}
.site-pill .wx-col{display:flex;flex-direction:column;align-items:center;line-height:1}
.site-pill .wx-col i{color:#f59e0b;font-size:16px}
.site-pill .mmh{color:#94a3b8;font-size:9px;margin-top:2px}
.site-pill .nm{color:#111827;font-weight:700;font-size:14px}
.site-pill .st-ic{font-size:16px}
.site-pill .wtri{color:#f59e0b}.site-pill .wexc{color:#e73e40}.site-pill .wok{color:#2563eb}
.region-pill{background:#fff;border-radius:999px;box-shadow:0 2px 12px rgba(16,24,40,.18);padding:10px 20px;display:inline-flex;align-items:center;gap:12px;white-space:nowrap;transform:translate(-50%,-50%);width:max-content}
.region-pill .rp-ic{width:34px;height:34px;border-radius:50%;border:2px solid #64748b;display:grid;place-items:center;color:#334155;font-size:14px}
.region-pill b{color:#0f172a;font-size:16px}
.leaflet-tile-pane{filter:grayscale(1) brightness(1.05) contrast(.95)}
</style>
