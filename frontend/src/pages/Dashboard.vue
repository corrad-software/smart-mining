<template>
  <div class="page-wrap">
    <div class="d-flex justify-content-between align-items-start flex-wrap">
      <div>
        <h1 class="page-h1">Operation Sites</h1>
        <p class="page-sub">Real-time insights, sales analytics, and live site monitoring
          <span v-if="live" class="badge-live ms-2"><span class="dot"></span>Live · PostgreSQL</span></p>
      </div>
      <div class="dropdown-wrap" ref="yearWrap">
        <button class="card px-3 py-2 d-flex flex-row align-items-center gap-2 year-btn" @click="yearOpen=!yearOpen">
          <i class="fa-regular fa-calendar text-secondary"></i><b>{{ year }}</b><i class="fa-solid fa-chevron-down small ms-1"></i>
        </button>
        <div v-if="yearOpen" class="year-menu card">
          <button v-for="y in years" :key="y" class="year-item" :class="{sel:y===year}" @click="selectYear(y)">
            <i class="fa-regular fa-calendar me-2"></i>{{ y }} <i v-if="y===year" class="fa-solid fa-check ms-auto"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <!-- left -->
      <div class="col-lg-4">
        <div class="row g-3">
          <div class="col-4"><div class="stat-tile"><div class="tile-label">Current Active Sites</div><div class="tile-num">{{ active }}</div></div></div>
          <div class="col-4"><div class="stat-tile"><div class="tile-label">Northern Region</div><div class="tile-num">{{ north }}</div></div></div>
          <div class="col-4"><div class="stat-tile"><div class="tile-label">Southern Region</div><div class="tile-num">{{ south }}</div></div></div>
        </div>
        <div class="card p-4 mt-3">
          <div class="d-flex justify-content-between align-items-center mb-3"><h5 class="fw-bold mb-0">Sites Category</h5>
            <span class="pill pill-green" style="font-family:monospace">Updated 11 Nov 2026, 12:01 PM</span></div>
          <div class="row g-4 text-center">
            <div v-for="c in categories" :key="c.k" class="col-4"><div class="tile-label">{{ c.k }}</div><div class="tile-num">{{ c.v }}</div></div>
          </div>
        </div>
      </div>

      <!-- overall sales bar -->
      <div class="col-lg-4">
        <div class="card p-4 h-100">
          <h5 class="fw-bold">Overall Sales <span class="text-muted2 fw-normal">({{ fmt(sales.total) }} MT)</span></h5>
          <div class="chart-wrap"><canvas ref="barEl"></canvas></div>
        </div>
      </div>

      <!-- region doughnut -->
      <div class="col-lg-4">
        <div class="card p-4 h-100">
          <h5 class="fw-bold mb-0">Region Performance Sales</h5>
          <p class="text-muted2 small">Click a doughnut chart segment to view the region percentage.</p>
          <div class="chart-wrap d-flex align-items-center justify-content-center"><canvas ref="doughEl"></canvas></div>
        </div>
      </div>
    </div>

    <!-- live streaming header + drone + address -->
    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
      <div class="d-flex align-items-center gap-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-video text-danger me-2"></i>Live Streaming</h5>
        <div class="dropdown-wrap" ref="droneWrap">
          <button class="card px-3 py-2 d-flex flex-row align-items-center gap-2 drone-btn" @click="droneOpen=!droneOpen">
            <i class="fa-solid fa-circle text-danger" style="font-size:9px"></i>{{ selectedDrones.length }} drone{{ selectedDrones.length===1?'':'s' }} selected
          </button>
          <div v-if="droneOpen" class="drone-menu card">
            <div class="text-muted2 small mb-2">Select up to 4 drones</div>
            <button v-for="d in droneList" :key="d" class="drone-item" :class="{sel:selectedDrones.includes(d)}" @click="toggleDrone(d)">
              {{ d }} <i v-if="selectedDrones.includes(d)" class="fa-solid fa-check"></i></button>
            <div class="d-flex justify-content-end gap-2 mt-2"><button class="btn btn-sm btn-light" @click="droneOpen=false">Cancel</button><button class="btn btn-sm btn-primary" @click="droneOpen=false">Apply</button></div>
          </div>
        </div>
      </div>
      <!-- address / site selector -->
      <div class="addr-wrap" ref="addrWrap">
        <button class="card px-3 py-2 d-flex flex-row align-items-center gap-2 addr-btn" @click="addrOpen=!addrOpen">
          <i class="fa-solid fa-location-dot text-secondary"></i>{{ selectedSite? trunc(selectedSite.name) : 'Select site' }}<i class="fa-solid fa-chevron-down small ms-1"></i>
        </button>
        <div v-if="addrOpen" class="addr-menu card">
          <div class="input-group mb-2"><span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input class="form-control" v-model="siteQ" placeholder="Search site..."/></div>
          <div class="addr-list">
            <button v-for="s in filteredSites" :key="s.id" class="site-row" :class="{sel:selectedSite&&selectedSite.id===s.id}" @click="selectSite(s)">
              <div><i class="fa-solid fa-location-dot me-2" :class="selectedSite&&selectedSite.id===s.id?'text-white':'text-secondary'"></i><b>{{ trunc(s.name) }}</b><div class="small" :class="selectedSite&&selectedSite.id===s.id?'text-white-50':'text-muted2'">{{ s.region }}</div></div>
              <span class="avail" :class="selectedSite&&selectedSite.id===s.id?'avail-on':''">Available</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 3-col: video | monthly | daily -->
    <div class="row g-3 mt-1">
      <div class="col-lg-4">
        <div class="feed-grid h-100" :style="{gridTemplateColumns:`repeat(${Math.min(selectedDrones.length||1,2)},1fr)`}">
          <div v-for="d in (selectedDrones.length?selectedDrones:['Drone 1'])" :key="d" class="feed">
            <div class="feed-label">{{ d }}</div>
            <div class="feed-body"><i class="fa-solid fa-video" style="font-size:24px"></i><div class="small mt-2">Live feed will start when connected</div>
              <button class="btn btn-primary btn-sm mt-3"><i class="fa-solid fa-play me-1"></i>Start</button></div>
          </div>
        </div>
      </div>
      <div class="col-lg-4"><div class="card p-4 h-100">
        <h5 class="fw-bold">2026 Monthly <span class="text-muted2 fw-normal">({{ fmt(selectedSiteTotal) }} MT)</span></h5>
        <p class="text-muted2 small">Click on the bar to view the daily data.</p>
        <div class="chart-wrap"><canvas ref="siteBarEl"></canvas></div>
      </div></div>
      <div class="col-lg-4"><div class="card p-4 h-100">
        <h5 class="fw-bold">2026 {{ MONTHS[selectedMonth] }} Daily <span class="text-muted2 fw-normal">({{ fmt(dailyTotal) }} MT)</span></h5>
        <p class="text-muted2 small">{{ selectedSite? trunc(selectedSite.name) : 'All sites' }}</p>
        <div class="chart-wrap"><canvas ref="dailyEl"></canvas></div>
      </div></div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted, watch, nextTick, onBeforeUnmount } from 'vue'
import { dashboard, apiGet, load } from '../services/api'
import Chart from 'chart.js/auto'
const live=ref(false)
const addrOpen=ref(false), addrWrap=ref(null)
const year=ref(2026), yearOpen=ref(false), yearWrap=ref(null)
const years=[2026,2025,2024,2023,2022]
const selectedMonth=ref(0), dailyData=ref([]), dailyTotal=ref(0)
const dailyEl=ref(null); let dailyChart
const active=ref(0), north=ref(0), south=ref(0)
const categories=ref([{k:'Tanah Kerajaan',v:1},{k:'GLC',v:6},{k:'Private Mines',v:1},{k:'River',v:11},{k:'Stockpiles (Sand)',v:22},{k:'Clay',v:1},{k:'Quarry',v:3},{k:'KSMM',v:8}])
const sales=ref({total:0,monthly:[],by_region:{},sites:[]})
const droneList=['Drone 1','Drone 2','Drone 3','Drone 4']
const selectedDrones=ref(['Drone 1']), droneOpen=ref(false), droneWrap=ref(null)
const selectedSite=ref(null), siteQ=ref('')
const barEl=ref(null), doughEl=ref(null), siteBarEl=ref(null)
let barChart,doughChart,siteChart
const MONTHS=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
const fmt=n=> (n||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})
const trunc=s=> s&&s.length>34? s.slice(0,34)+'…':s
const filteredSites=computed(()=> sales.value.sites.filter(s=>!siteQ.value||(s.name||'').toLowerCase().includes(siteQ.value.toLowerCase())))
const selectedSiteTotal=computed(()=> selectedSite.value? selectedSite.value.total : 0)
function toggleDrone(d){ const i=selectedDrones.value.indexOf(d)
  if(i>=0) selectedDrones.value.splice(i,1); else if(selectedDrones.value.length<4) selectedDrones.value.push(d) }
function selectSite(s){ selectedSite.value=s; addrOpen.value=false; drawSiteChart(); loadDaily() }
function onDoc(e){ if(droneWrap.value && !droneWrap.value.contains(e.target)) droneOpen.value=false
  if(addrWrap.value && !addrWrap.value.contains(e.target)) addrOpen.value=false
  if(yearWrap.value && !yearWrap.value.contains(e.target)) yearOpen.value=false }
async function selectYear(y){
  year.value=y; yearOpen.value=false
  const s=await apiGet(`sales?year=${y}`); sales.value=s||{total:0,monthly:[],by_region:{},sites:[]}
  selectedSite.value=sales.value.sites?.[0]||null
  const firstM=(sales.value.monthly||[]).findIndex(v=>v>0); selectedMonth.value=firstM>=0?firstM:0
  await nextTick(); drawBar(); drawDough(); drawSiteChart(); await loadDaily()
}
async function loadDaily(){
  const m=selectedMonth.value+1; const site=selectedSite.value?.id
  const d=await apiGet(`sales/daily?month=${m}&year=${year.value}${site?`&site=${site}`:''}`)
  if(d){ dailyData.value=d.daily; dailyTotal.value=d.total } else { dailyData.value=[]; dailyTotal.value=0 }
  drawDaily()
}
function drawDaily(){ if(!dailyEl.value) return; dailyChart?.destroy()
  const labels=Array.from({length:dailyData.value.length},(_,i)=>String(i+1).padStart(2,'0'))
  dailyChart=new Chart(dailyEl.value,{type:'bar',data:{labels,datasets:[{data:dailyData.value,backgroundColor:'#c7d7fe',borderColor:'#93b4fd',borderWidth:1}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{maxTicksLimit:11}},y:{ticks:{callback:v=>v>=1e3?(v/1e3)+'K':v}}}}}) }

function drawBar(){ if(!barEl.value) return; barChart?.destroy()
  barChart=new Chart(barEl.value,{type:'bar',data:{labels:MONTHS,datasets:[{data:sales.value.monthly,backgroundColor:'#c7d7fe',borderColor:'#93b4fd',borderWidth:1}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v>=1e6?(v/1e6)+'M':v>=1e3?(v/1e3)+'K':v}}}}}) }
function drawDough(){ if(!doughEl.value) return; doughChart?.destroy()
  const r=sales.value.by_region||{}; const labels=Object.keys(r); const vals=Object.values(r); const tot=vals.reduce((a,b)=>a+b,0)||1
  const top=labels.length?labels[vals.indexOf(Math.max(...vals))]:''; const topPct=Math.round(Math.max(...vals,0)/tot*100)
  doughChart=new Chart(doughEl.value,{type:'doughnut',data:{labels,datasets:[{data:vals,backgroundColor:['#2563eb','#cbd5e1','#60a5fa']}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'70%',plugins:{legend:{display:false},
      tooltip:{callbacks:{label:c=>`${c.label}: ${(c.raw/tot*100).toFixed(1)}%`}}}},
    plugins:[{id:'center',afterDraw(ch){const{ctx,chartArea:{left,right,top:t,bottom}}=ch;ctx.save();const x=(left+right)/2,y=(t+bottom)/2
      ctx.textAlign='center';ctx.fillStyle='#111827';ctx.font='700 22px Inter';ctx.fillText(topPct+'%',x,y-6)
      ctx.font='600 12px Inter';ctx.fillStyle='#6b7280';ctx.fillText(top,x,y+14);ctx.restore()}}]}) }
function drawSiteChart(){ if(!siteBarEl.value) return; siteChart?.destroy()
  const data=selectedSite.value? selectedSite.value.months : sales.value.monthly
  const colors=data.map((_,i)=> i===selectedMonth.value? '#86efac':'#c7d7fe')
  siteChart=new Chart(siteBarEl.value,{type:'bar',data:{labels:MONTHS,datasets:[{data,backgroundColor:colors,borderColor:'#93b4fd',borderWidth:1}]},
    options:{responsive:true,maintainAspectRatio:false,onClick:(e,els)=>{ if(els.length){ selectedMonth.value=els[0].index; drawSiteChart(); loadDaily() } },
      plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>v>=1e3?(v/1e3)+'K':v}}}}}) }

onMounted(async()=>{
  document.addEventListener('click',onDoc)
  const d=await dashboard().catch(()=>null)
  if(d){ live.value=true; active.value=d.active_sites
    const ar=d.active_by_region||{}; north.value=ar['Northern Region']||0; south.value=ar['Southern Region']||0 }
  const s=await apiGet('sales?year='+year.value); if(s) sales.value=s
  if(sales.value.sites?.length) selectedSite.value=sales.value.sites[0]
  // default to the first month that has sales
  const firstM=(sales.value.monthly||[]).findIndex(v=>v>0); selectedMonth.value= firstM>=0? firstM:0
  await nextTick(); drawBar(); drawDough(); drawSiteChart(); await loadDaily()
})
onBeforeUnmount(()=>{ document.removeEventListener('click',onDoc); barChart?.destroy();doughChart?.destroy();siteChart?.destroy();dailyChart?.destroy() })
</script>
<style scoped>
.chart-wrap{position:relative;height:280px;margin-top:10px}
.badge-live{display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#15803d;font-weight:700;font-size:11px;padding:.15rem .55rem;border-radius:999px}
.badge-live .dot{width:7px;height:7px;border-radius:50%;background:#15803d;animation:pulse 1.4s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
.dropdown-wrap{position:relative}
.drone-btn{cursor:pointer;border:0}
.year-btn{cursor:pointer;border:0}
.year-menu{position:absolute;right:0;top:calc(100% + 6px);width:220px;padding:10px;z-index:1000}
.year-item{display:flex;align-items:center;width:100%;border:0;background:transparent;border-radius:10px;padding:.6rem .8rem;font-weight:700;color:#1f2937}
.year-item:hover{background:#f3f4f6}
.year-item.sel{background:#2563eb;color:#fff}
.drone-menu{position:absolute;right:0;top:calc(100% + 6px);width:280px;padding:14px;z-index:1000}
.drone-item{display:flex;align-items:center;justify-content:space-between;width:100%;border:0;background:transparent;border-radius:10px;padding:.6rem .8rem;font-weight:700;color:#1f2937;margin-bottom:2px}
.drone-item:hover{background:#f3f4f6}
.drone-item.sel{background:#2563eb;color:#fff}
.feed-grid{display:grid;gap:12px}
.feed{background:#0b0f16;border-radius:14px;min-height:260px;position:relative;display:grid;place-items:center;color:#8b94a3}
.feed-label{position:absolute;top:12px;left:14px;color:#cbd5e1;font-size:13px}
.feed-body{text-align:center}
.addr-wrap{position:relative}
.addr-btn{cursor:pointer;border:0;max-width:340px}
.addr-menu{position:absolute;right:0;top:calc(100% + 6px);width:420px;max-width:92vw;padding:12px;z-index:1000}
.addr-list{overflow:auto;max-height:340px;display:flex;flex-direction:column;gap:6px}
.site-list{overflow:auto;max-height:360px;display:flex;flex-direction:column;gap:6px}
.site-row{display:flex;align-items:center;justify-content:space-between;gap:8px;border:0;background:#fff;border-radius:12px;padding:.7rem .9rem;text-align:left;border:1px solid #f1f5f9}
.site-row:hover{background:#f8fafc}
.site-row.sel{background:#2563eb;color:#fff}
.avail{background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;border-radius:999px;padding:.2rem .6rem;white-space:nowrap}
.avail-on{background:rgba(255,255,255,.2);color:#fff}
</style>
