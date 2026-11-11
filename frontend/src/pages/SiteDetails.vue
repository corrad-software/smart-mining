<template>
  <div v-if="site">
    <!-- breadcrumb -->
    <div class="px-4 pt-3 d-flex align-items-center gap-2 small">
      <i class="fa-solid fa-bars text-secondary me-2"></i>
      <router-link to="/map" class="text-muted2">Sites</router-link><span class="text-muted2">/</span>
      <span class="text-muted2">{{ regionName }}</span><span class="text-muted2">/</span>
      <span class="text-dark">{{ tabLabel }} - {{ site.site_code }} · {{ shortName }}</span>
    </div>

    <!-- header -->
    <div class="px-4 pt-2 d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h1 class="page-h1">{{ site.site_code }} · {{ site.name }}</h1>
        <div class="d-flex align-items-center gap-3 mt-1 small">
          <span class="pill" :class="expiring?'pill-amber':'pill-green'">● {{ expiring?'EXPIRING SOON':'ACTIVE' }}</span>
          <span class="text-muted2 mono"><i class="fa-solid fa-location-dot me-1"></i>{{ site.lat }}° N, {{ site.lng }}° E</span>
          <span class="text-muted2 mono"><i class="fa-regular fa-calendar me-1"></i>{{ fmtDate(site.permit_date) }} → {{ fmtDate(site.permit_date_end) }}</span>
        </div>
      </div>
      <div class="d-flex align-items-start gap-2">
        <div v-if="expiring" class="warn-box">
          <div class="fw-bold text-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i>EXPIRING SOON</div>
          <div class="small">Permit expires in {{ daysLeft }} day(s). Please renew your permit as soon as possible.</div>
        </div>
        <button class="btn btn-light rounded-circle"><i class="fa-solid fa-gear text-secondary"></i></button>
      </div>
    </div>

    <!-- tab bar -->
    <div class="px-4 mt-3 d-flex gap-2 flex-wrap">
      <div v-for="t in tabs" :key="t.k" class="dropdown">
        <button class="btn tab-btn" :class="tab===t.k?'btn-primary':'btn-light'" @click="t.subs? null : pick(t.k)"
          :data-bs-toggle="t.subs?'dropdown':null">
          <i :class="t.icon" class="me-2"></i>{{ t.label }} <i v-if="t.subs" class="fa-solid fa-chevron-down ms-1 small"></i>
        </button>
        <ul v-if="t.subs" class="dropdown-menu">
          <li v-for="s in t.subs" :key="s.k"><button class="dropdown-item" :class="{'active':tab===t.k&&sub===s.k}" @click="pick(t.k,s.k)">{{ s.label }}</button></li>
        </ul>
      </div>
    </div>

    <div class="px-4 pb-5 mt-3">
      <!-- SPATIAL -->
      <template v-if="tab==='spatial'">
        <div v-if="sub==='3d'" class="spatial-3d card">
          <div class="controls">
            <div class="ctl-h">Appearance</div>
            <div class="small mb-1">Field of view: 60</div><input type="range" class="form-range"/>
            <div class="ctl-h mt-2">Eye-Dome-Lighting</div>
            <div class="form-check small"><input class="form-check-input" type="checkbox"/><label class="form-check-label">Enable</label></div>
            <div class="small mt-1">Radius: 1.4</div><input type="range" class="form-range"/>
            <div class="small">Strength: 0.4</div><input type="range" class="form-range"/>
            <div class="ctl-h mt-2">Background</div>
            <div class="btn-group btn-group-sm w-100"><button class="btn btn-outline-secondary">Skybox</button><button class="btn btn-secondary">Gradient</button><button class="btn btn-outline-secondary">Black</button></div>
          </div>
          <div class="viewer d-flex align-items-center justify-content-center">
            <div class="position-absolute top-0 end-0 m-3 d-flex gap-2"><button class="btn btn-light btn-sm"><i class="fa-solid fa-rotate me-1"></i>Refresh</button><button class="btn btn-primary btn-sm"><i class="fa-solid fa-pen me-1"></i>Update Data</button></div>
            <div class="splat"></div><div class="splat splat2"></div>
            <div class="text-secondary small position-absolute bottom-0 mb-3">3D point-cloud model (potree viewer)</div>
          </div>
        </div>
        <div v-else class="spatial-map card">
          <div id="sdmap" class="sd-leaflet"></div>
          <div class="position-absolute top-0 end-0 m-3 d-flex gap-2" style="z-index:500">
            <button class="btn btn-dark btn-sm"><i class="fa-solid fa-satellite me-1"></i>Satellite</button>
            <button class="btn btn-dark btn-sm"><i class="fa-solid fa-expand me-1"></i>Expand</button>
          </div>
          <div class="playbar" v-if="sub==='temporal'">
            <button class="rbtn"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="rbtn play"><i class="fa-solid fa-play"></i></button>
            <button class="rbtn"><i class="fa-solid fa-chevron-right"></i></button>
            <div class="ms-3"><div class="tiny text-uppercase">Playback</div><b>1.0x</b></div>
            <div class="track mx-3"><span class="knob"></span></div>
            <div class="text-end"><div class="tiny text-uppercase">Current View</div><b>Jan 18 '23</b><div class="tiny">1 of 43</div></div>
          </div>
        </div>
      </template>

      <!-- REPORTS -->
      <template v-else-if="tab==='reports'">
        <div v-if="sub==='landowner'">
          <div class="row g-3">
            <div class="col-md-3" v-for="s in loStats" :key="s.k"><div class="stat-tile d-flex align-items-center gap-3"><span class="icon-sq" :style="{background:s.bg,color:s.fg}"><i :class="s.ic"></i></span><div><div class="tile-num" style="font-size:24px">{{ s.v }}</div><div class="tile-label">{{ s.k }}</div></div></div></div>
          </div>
          <div class="card mt-3 p-0 overflow-hidden">
            <div class="cal-head d-flex justify-content-between align-items-center px-4 py-3"><b><i class="fa-regular fa-calendar me-2"></i>Monthly Overview — 2026</b><span>‹ 2026 ›</span></div>
            <div class="d-flex text-center">
              <div v-for="(m,i) in months" :key="m" class="flex-fill py-3" :class="{'cal-cur':i===10}"><div class="tiny text-uppercase">{{ m }}</div><span class="dot2" :class="{on:i===6}"></span></div>
            </div>
          </div>
          <div class="text-center text-muted2 py-5"><i class="fa-regular fa-file fa-2x d-block mb-2 opacity-50"></i>No report submitted<div class="small">No report for November 2026.</div></div>
        </div>
        <div v-else>
          <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <div class="input-group" style="max-width:260px"><span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input class="form-control" placeholder="Search report..."/></div>
            <select class="form-select ms-auto" style="max-width:150px"><option>All Months</option></select>
            <select class="form-select" style="max-width:150px"><option>All Statuses</option></select>
            <button class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>New F001 Report</button>
          </div>
          <div class="card p-0 overflow-hidden">
            <table class="table mb-0 align-middle">
              <thead><tr><th>#</th><th>Document</th><th>Name</th><th>Prepared By</th><th>Reviewed By</th><th>Effective Date</th><th>Status</th><th class="text-end">Action</th></tr></thead>
              <tbody>
                <tr v-for="(w,i) in f001" :key="w.id">
                  <td class="text-muted2">{{ i+1 }}</td>
                  <td class="mono small">{{ w.document_no||'KSSB/OPS/F001' }}</td>
                  <td class="small"><b>{{ shortName }}</b></td>
                  <td class="small">{{ cap(name(w.prepared_by)) }}</td>
                  <td class="small">{{ cap(name(w.reviewed_by))||'Mridzuan' }}</td>
                  <td class="small mono">{{ fmtDate(w.start_effective_date) }} – {{ fmtDate(w.end_effective_date) }}</td>
                  <td><span class="status-pill" :class="stClass(w.status)">{{ (w.status||'submitted').toUpperCase() }}</span></td>
                  <td class="text-end"><button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis"></i></button></td>
                </tr>
                <tr v-if="!f001.length"><td colspan="8" class="text-center text-muted2 py-4">No F001 reports for this site.</td></tr>
              </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
              <span class="small text-muted2">Showing 1–{{ Math.min(10,f001.length) }} of {{ f001.length }} reports</span>
              <div><button class="btn btn-sm btn-light me-1">Previous</button><button class="btn btn-sm btn-light me-1">1 / {{ Math.max(1,Math.ceil(f001.length/10)) }}</button><button class="btn btn-sm btn-primary">Next</button></div>
            </div>
          </div>
        </div>
      </template>

      <!-- MEDIA -->
      <template v-else-if="tab==='media'">
        <div v-if="sub==='files'" class="row g-3">
          <div class="col-lg-8"><div class="card p-3">
            <div class="d-flex gap-2 mb-2"><div class="input-group" style="max-width:280px"><span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input class="form-control" v-model="fileQ" placeholder="Search files by name..."/></div><select class="form-select ms-auto" style="max-width:150px"><option>All Categories</option></select><button class="btn btn-primary"><i class="fa-regular fa-file me-2"></i>Upload Files</button></div>
            <div style="max-height:62vh;overflow:visible">
            <table class="table mb-0 align-middle"><thead><tr><th>Type</th><th>File name</th><th>Date uploaded</th><th>Size</th><th class="text-end">Actions</th></tr></thead>
              <tbody><tr v-for="(f,i) in filesFiltered" :key="i"><td><span class="file-ico" :class="isPdf(f)?'pdf':'doc'"><i :class="isPdf(f)?'fa-solid fa-file-pdf':'fa-regular fa-file'"></i></span></td>
                <td><div class="fw-medium">{{ f.name }}</div><span class="pill" :class="catPill(f.category)">{{ f.category.toUpperCase() }}</span></td><td class="text-muted2">{{ fmtDate(f.date) }}</td><td class="text-muted2">{{ sizeFmt(f.size) }}</td>
                <td class="text-end position-relative"><button class="btn btn-sm btn-light rounded-circle" @click.stop="menuId=menuId===('f'+i)?null:('f'+i)"><i class="fa-solid fa-ellipsis"></i></button>
                  <div v-if="menuId===('f'+i)" class="act-menu card"><button class="act-item" @click="previewFile(f)"><i class="fa-regular fa-eye me-2 text-secondary"></i>View</button><button class="act-item" @click="openFile(f)"><i class="fa-solid fa-download me-2 text-secondary"></i>Download</button></div></td></tr>
                <tr v-if="!filesFiltered.length"><td colspan="5" class="text-center text-muted2 py-4">No files</td></tr></tbody></table>
            </div>
          </div></div>
          <div class="col-lg-4"><div class="card p-4"><h5 class="fw-bold mb-3">File Categories</h5>
            <div v-for="c in fileCatsReal" :key="c.k" class="mb-3"><div class="d-flex justify-content-between"><b>{{ c.k }}</b><span class="text-muted2">{{ c.n }} files</span></div><div class="progress mt-1" style="height:6px"><div class="progress-bar" :style="{width:c.pct+'%',background:c.c}"></div></div></div>
            <hr/><div class="d-flex justify-content-between"><span class="text-muted2">Total Files</span><h5 class="fw-bold mb-0">{{ media.total_files }}</h5></div></div></div>
        </div>
        <div v-else>
          <div class="d-flex align-items-center gap-2 mb-3"><h5 class="fw-bold mb-0 me-2">Media <span class="text-muted2 fw-normal">({{ media.gallery_count }} items)</span></h5>
            <button class="btn btn-light btn-sm active">All</button><button class="btn btn-light btn-sm">Images ({{ media.gallery_count }})</button>
            <button class="btn btn-light btn-sm ms-auto">All Years</button><button class="btn btn-primary btn-sm"><i class="fa-solid fa-cloud-arrow-up me-1"></i>Upload Media</button></div>
          <div class="row g-3">
            <div class="col-md-3 col-6" v-for="(g,i) in media.gallery.slice(0,24)" :key="i"><div class="card overflow-hidden">
              <div class="gallery-img" @click="previewImg=g" :style="{backgroundImage:`url(${g.url})`}"></div>
              <div class="p-2"><b class="small">{{ site.site_code }} · {{ g.gallery }}</b><div class="tiny text-muted2">{{ fmtDate(g.date) }}</div><div class="tiny text-muted2 mt-1"><i class="fa-regular fa-image me-1"></i>{{ g.name }} · {{ sizeFmt(g.size) }}</div></div>
            </div></div>
            <div v-if="!media.gallery.length" class="text-muted2 px-2">No media for this site.</div>
          </div>
        </div>
      </template>

      <!-- CCTV -->
      <template v-else-if="tab==='cctv'">
        <div v-if="cams.length" class="row g-3"><div class="col-lg-4" v-for="c in cams" :key="c.id"><div class="card overflow-hidden"><div class="ratio ratio-16x9" style="background:#0b0f16"></div><div class="p-3"><b>{{ c.name }}</b></div></div></div></div>
        <div v-else class="text-center text-muted2 py-5"><i class="fa-solid fa-video fa-2x d-block mb-2 opacity-50"></i>No cameras found<div class="small">No cameras assigned to this site.</div></div>
      </template>

      <!-- OTHERS -->
      <template v-else>
        <div class="utabs mb-3"><button class="utab on">Audit HSE</button><button class="utab">Audit TL</button><button class="utab">Aduan/Tuntutan/isu</button></div>
        <div class="card p-0 overflow-hidden"><table class="table mb-0"><thead><tr><th>#</th><th>Date</th><th>Finding Type</th><th>HSE Suggestion</th><th>Status</th></tr></thead><tbody><tr><td colspan="5" class="text-center text-muted2 py-5"><i class="fa-regular fa-file fa-2x d-block mb-2 opacity-50"></i>No records found<div class="small">Try adjusting the year, month or status filter.</div></td></tr></tbody></table></div>
      </template>
    </div>

    <!-- file preview modal -->
    <div v-if="preview" class="modal-backdrop-c" @click.self="preview=null">
      <div class="preview-card">
        <div class="preview-head d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2"><span class="file-ico pdf"><i class="fa-solid fa-file-pdf"></i></span><div><div class="fw-bold">{{ preview.name }}</div><div class="small text-muted2">{{ (preview.ext||'PDF').toUpperCase().replace('.','') }}</div></div></div>
          <div class="d-flex gap-2"><button class="btn btn-sm btn-light" @click="openFile(preview)"><i class="fa-solid fa-arrow-up-right-from-square"></i></button><button class="btn-close-x" @click="preview=null"><i class="fa-solid fa-xmark"></i></button></div>
        </div>
        <div class="preview-body d-flex flex-column align-items-center justify-content-center">
          <div class="warn-ic"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <h5 class="fw-bold mt-3">Unable to Preview Document</h5>
          <p class="text-muted2 text-center" style="max-width:560px">The file '<b>{{ preview.name }}</b>' could not be rendered in the browser preview. Open it in a new tab to view or download.</p>
          <button class="btn btn-primary mt-2" @click="openFile(preview)"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Open in New Tab</button>
        </div>
      </div>
    </div>
    <!-- image preview -->
    <div v-if="previewImg" class="modal-backdrop-c" @click.self="previewImg=null">
      <div class="img-card">
        <div class="d-flex justify-content-between align-items-center p-3"><b>{{ previewImg.name }}</b>
          <div class="d-flex gap-2"><a class="btn btn-sm btn-light" :href="previewImg.url" target="_blank"><i class="fa-solid fa-download"></i></a><button class="btn-close-x" @click="previewImg=null"><i class="fa-solid fa-xmark"></i></button></div></div>
        <img :src="previewImg.url" class="img-full"/>
      </div>
    </div>
  </div>
  <div class="page-wrap text-muted2" v-else>Loading…</div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useRoute } from 'vue-router'
import { load, fmtDate, apiGet } from '../services/api'
const route=useRoute()
const site=ref(null), f001=ref([]), cams=ref([])
const media=ref({files:[],gallery:[],file_categories:{},total_files:0,gallery_count:0})
const menuId=ref(null), preview=ref(null)
const tab=ref('spatial'), sub=ref('temporal')
let sdmap=null
const tabs=[
  {k:'spatial',label:'Spatial',icon:'fa-solid fa-layer-group',subs:[{k:'temporal',label:'Temporal'},{k:'orthomosaic',label:'Orthomosaic'},{k:'3d',label:'3D Model'}]},
  {k:'reports',label:'Reports',icon:'fa-regular fa-file-lines',subs:[{k:'site',label:'Site Report'},{k:'f001',label:'F001 Daily Report'},{k:'landowner',label:'Land Owner Report'}]},
  {k:'media',label:'Media',icon:'fa-regular fa-image',subs:[{k:'gallery',label:'Media Gallery'},{k:'files',label:'Files'}]},
  {k:'cctv',label:'CCTV',icon:'fa-solid fa-video'},
  {k:'others',label:'Others',icon:'fa-regular fa-folder-open',subs:[{k:'audit',label:'Audit HSE'},{k:'compliances',label:'Compliances'},{k:'contractors',label:'Contractors'},{k:'complaints',label:'Complaints'}]},
]
const name=v=> v&&(v.name||v.username||v)||''
const cap=s=> s? s.charAt(0).toUpperCase()+s.slice(1):''
const shortName=computed(()=> site.value? site.value.name : '')
const regionName=computed(()=> site.value&&(site.value.region?.name||site.value.region)||'Region')
const tabLabel=computed(()=> tabs.find(t=>t.k===tab.value)?.label||'')
const daysLeft=computed(()=>{ if(!site.value?.permit_date_end) return 48; const d=Math.ceil((new Date(site.value.permit_date_end)-new Date())/(864e5)); return d })
const expiring=computed(()=> daysLeft.value<=90 && daysLeft.value>0)
function stClass(s){return {submitted:'st-green',pending_review:'st-amber',pending_approval:'st-blue',returned:'st-red',draft:'st-gray'}[s]||'st-green'}
const months=['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']
const loStats=[{k:'Approved',v:0,ic:'fa-solid fa-circle-check',bg:'#dcfce7',fg:'#15803d'},{k:'Pending',v:1,ic:'fa-solid fa-clock',bg:'#fef3c7',fg:'#b45309'},{k:'Returned',v:0,ic:'fa-solid fa-rotate-left',bg:'#fee2e2',fg:'#b91c1c'}]
const siteFiles=[
  {name:'Lesen AA - Junhern Resources 25.6.2026-24.6.2027.pdf',cat:'LUAS APPROVAL',cls:'pill-iso',date:'Oct 30, 2026',size:'214.1 B'},
  {name:'lesen LED - Junhern Resources 20.4.2026 - 19.04.2027.pdf',cat:'LUAS APPROVAL',cls:'pill-iso',date:'Oct 30, 2026',size:'1.5 KB'},
  {name:'F001-RD36-202606.pdf',cat:'SITE REPORT',cls:'pill-green',date:'Jul 6, 2026',size:'17.9 KB'},
  {name:'F001-RD36-202605.pdf',cat:'SITE REPORT',cls:'pill-green',date:'Jun 8, 2026',size:'15.1 KB'},
  {name:'Lesen KB - Junhern Resources.pdf',cat:'LUAS APPROVAL',cls:'pill-iso',date:'Jun 8, 2026',size:'334.85 B'},
]
const fileCats=[{k:'LUAS Approval',n:8,pct:28,c:'#0d6efd'},{k:'Permit & License Approval',n:2,pct:7,c:'#0d6efd'},{k:'Site Report',n:18,pct:62,c:'#2e9865'},{k:'OMS',n:1,pct:4,c:'#f59e0b'},{k:'EIA',n:0,pct:0,c:'#dc2626'}]
function grad(n){const g=['#6b8e5a','#7a9b63','#5f7d4e','#8aa06a','#6e9158','#789a60','#647f52','#82a065'];return `linear-gradient(135deg,${g[n%8]},#3f5536)`}
function pick(t,s){ tab.value=t; if(s) sub.value=s; else if(t==='spatial') sub.value='temporal'; else if(t==='reports') sub.value='f001'; else if(t==='media') sub.value='gallery'; else if(t==='others') sub.value='audit'; nextTick(initMap) }
function initMap(){
  if(!['spatial'].includes(tab.value)||sub.value==='3d'){ return }
  const el=document.getElementById('sdmap'); if(!el||!window.L) return
  if(sdmap){ sdmap.remove(); sdmap=null }
  sdmap=window.L.map('sdmap',{zoomControl:false,attributionControl:false}).setView([+site.value.lat||3.7,+site.value.lng||101.47],13)
  window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(sdmap)
  window.L.circleMarker([+site.value.lat,+site.value.lng],{radius:6,color:'#6ee7ff',fillOpacity:.9}).addTo(sdmap)
  setTimeout(()=>sdmap&&sdmap.invalidateSize(),200)
}
function catColor(c){return {'LUAS Approval':'#0d6efd','Permit & License Approval':'#0d6efd','Site Report':'#2e9865','OMS':'#f59e0b','EIA':'#dc2626','HSE':'#8b5cf6'}[c]||'#64748b'}
function catPill(c){return {'LUAS Approval':'pill-iso','Permit & License Approval':'pill-iso','Site Report':'pill-green','OMS':'pill-amber','EIA':'pill-red','HSE':'pill-gray'}[c]||'pill-gray'}
const fileCatsReal=computed(()=>{ const fc=media.value.file_categories||{}; const mx=Math.max(1,...Object.values(fc))
  return Object.entries(fc).map(([k,n])=>({k,n,pct:n/mx*100,c:catColor(k)})) })
function openFile(f){ menuId.value=null; if(f.url) window.open(f.url,'_blank') }
function previewFile(f){ menuId.value=null; preview.value=f }
function sizeFmt(b){ if(b==null) return '—'; if(b<1024) return b.toFixed(2)+' B'; if(b<1048576) return (b/1024).toFixed(1)+' KB'; return (b/1048576).toFixed(2)+' MB' }
const fileQ=ref(''), previewImg=ref(null)
const isPdf=f=> /pdf/i.test(f.ext||f.name||'')
const filesFiltered=computed(()=> (media.value.files||[]).filter(f=>!fileQ.value||(f.name||'').toLowerCase().includes(fileQ.value.toLowerCase())))

onMounted(async()=>{
  const qtab=route.query.tab
  if(qtab==='reports'){ tab.value='reports'; sub.value='f001' }
  else if(qtab==='media'){ tab.value='media'; sub.value='gallery' }
  const [sites,wr,cameras]=await Promise.all(['sites','weekly-reports','cameras'].map(load))
  site.value=sites.find(s=>String(s.id)===route.params.id)||sites.find(s=>s.site_code==='RD36')||sites[0]
  const sid=site.value?.id, snm=site.value?.name
  const match=(rel)=> rel && ((rel.id||rel.name||rel)==sid || (rel.name||rel)===snm)
  f001.value=wr.filter(w=>match(w.site)).slice(0,10)
  cams.value=cameras.filter(c=>match(c.site))
  const m=await apiGet(`sites/${site.value.id}/media`); if(m) media.value=m
  document.addEventListener('click',()=>menuId.value=null)
  nextTick(initMap)
})
</script>

<style scoped>
.warn-box{background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;padding:10px 16px;max-width:360px}
.mono{font-family:monospace}.tiny{font-size:10.5px}
.tab-btn{border-radius:12px;font-weight:600}
.spatial-map,.spatial-3d{position:relative;height:620px;overflow:hidden;border-radius:16px}
.sd-leaflet{position:absolute;inset:0;filter:brightness(.7) saturate(.6) hue-rotate(5deg)}
.playbar{position:absolute;left:16px;right:16px;bottom:16px;z-index:500;background:#0f172a;border-radius:14px;padding:14px 18px;display:flex;align-items:center;color:#fff}
.rbtn{width:40px;height:40px;border-radius:50%;border:1px solid #334155;background:transparent;color:#fff}
.rbtn.play{background:#0d6efd;border-color:#0d6efd;width:46px;height:46px}
.track{flex:1;height:4px;background:#334155;border-radius:4px;position:relative}
.knob{position:absolute;left:12%;top:50%;transform:translateY(-50%);width:14px;height:14px;border-radius:50%;background:#fff}
.spatial-3d{display:flex;background:#0e1420}
.controls{width:230px;background:#141b26;color:#cbd5e1;padding:14px;overflow:auto;flex-shrink:0}
.ctl-h{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#8aa;border-top:1px solid #243;padding-top:6px;margin-top:6px}
.viewer{flex:1;position:relative;background:radial-gradient(circle at 50% 40%,#1a2433,#0b1018)}
.splat{width:120px;height:160px;background:linear-gradient(160deg,#5f7d4e,#2f3d26);border-radius:40% 50% 45% 55%;opacity:.85;transform:rotate(-8deg)}
.splat2{width:90px;height:200px;margin-left:40px;margin-top:60px}
.status-pill{font-size:10px;font-weight:700;padding:.25rem .6rem;border-radius:7px}
.st-green{background:#dcfce7;color:#15803d}.st-amber{background:#fef3c7;color:#b45309}.st-blue{background:#e0f2fe;color:#0369a1}.st-red{background:#fee2e2;color:#b91c1c}.st-gray{background:#f1f5f9;color:#64748b}
.icon-sq{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;font-size:16px}
.cal-head{background:#0f172a;color:#fff}
.cal-cur{background:#0f172a;color:#fff;border-radius:10px}
.dot2{display:inline-block;width:7px;height:7px;border-radius:50%;background:#cbd5e1;margin-top:6px}
.dot2.on{background:#f59e0b}
.gallery-img{height:150px;background-size:cover;background-position:center;cursor:pointer;background-color:#e5e7eb}
.act-menu{position:absolute;right:10px;top:40px;width:170px;padding:8px;z-index:1200;text-align:left}
.act-item{display:flex;align-items:center;width:100%;border:0;background:transparent;border-radius:9px;padding:.55rem .7rem;font-weight:600;color:#1f2937}
.act-item:hover{background:#f3f4f6}
.modal-backdrop-c{position:fixed;inset:0;background:rgba(15,23,42,.5);display:grid;place-items:center;z-index:2000;padding:20px}
.preview-card{background:#fff;border-radius:16px;width:100%;max-width:900px;height:70vh;display:flex;flex-direction:column;overflow:hidden}
.preview-head{background:#eef1f5;padding:14px 18px}
.preview-body{flex:1}
.warn-ic{width:70px;height:70px;border-radius:50%;background:#e0eaff;color:#2563eb;display:grid;place-items:center;font-size:26px}
.btn-close-x{border:0;background:transparent;font-size:18px;color:#6b7280}
.img-card{background:#fff;border-radius:16px;max-width:900px;max-height:90vh;overflow:hidden}
.img-full{max-width:100%;max-height:78vh;display:block;margin:0 auto}
.file-ico{width:34px;height:34px;border-radius:8px;display:grid;place-items:center}.file-ico.pdf{background:#fee2e2;color:#dc2626}
.page-h1{font-size:26px}
</style>
