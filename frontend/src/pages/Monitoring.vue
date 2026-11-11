<template>
  <div class="page-wrap">
    <h1 class="page-h1">Monitoring Report</h1>
    <p class="page-sub">Site monitoring documentation</p>
    <div class="utabs"><button class="utab on">Report</button></div>

    <div class="row g-3 mt-2">
      <div class="col-lg-8">
        <div class="card p-3">
          <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <div class="input-group" style="max-width:280px"><span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input class="form-control" v-model="q" placeholder="Search files by name..."/></div>
            <div class="ms-auto" style="width:190px"><DropdownSelect v-model="typeFilter" :options="typeOptions" :divider-after="0"/></div>
            <button class="btn btn-primary" @click="showUpload=true"><i class="fa-regular fa-file me-2"></i>Upload Files</button>
          </div>
          <div class="list-scroll">
            <table class="table align-middle mb-0">
              <thead><tr><th>Type</th><th>File name</th><th>Date uploaded</th><th>Size</th><th class="text-end">Actions</th></tr></thead>
              <tbody>
                <tr v-for="f in filtered" :key="f.id">
                  <td><span class="file-ico" :class="f.isPdf?'pdf':'doc'"><i :class="f.isPdf?'fa-solid fa-file-pdf':'fa-regular fa-file'"></i></span></td>
                  <td><div class="fw-medium">{{ f.name }}</div><span class="pill" :class="typeMeta(f.type).cls">{{ typeMeta(f.type).label }}</span></td>
                  <td class="text-muted2">{{ fmtDate(f.published_at) }}</td>
                  <td class="text-muted2">{{ fmtSize(f.size, f.has_file) }}</td>
                  <td class="text-end position-relative">
                    <button class="btn btn-sm btn-light rounded-circle" @click.stop="menuId = menuId===f.id?null:f.id"><i class="fa-solid fa-ellipsis"></i></button>
                    <div v-if="menuId===f.id" class="act-menu card">
                      <button class="act-item" @click="openPreview(f)"><i class="fa-regular fa-eye me-2 text-secondary"></i>View</button>
                      <button class="act-item" @click="download(f)"><i class="fa-solid fa-download me-2 text-secondary"></i>Download</button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!filtered.length"><td colspan="5" class="text-center text-muted2 py-4">No files found</td></tr>
              </tbody>
            </table>
          </div>
          <div class="small text-muted2 mt-2 px-1">Showing {{ filtered.length }} of {{ all.length }} files</div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card p-4">
          <h5 class="fw-bold mb-3">Report Types</h5>
          <div v-for="t in typeBreakdown" :key="t.key" class="mb-3">
            <div class="d-flex justify-content-between"><b>{{ t.label }}</b><span class="text-muted2">{{ t.n }} files</span></div>
            <div class="progress mt-1" style="height:6px"><div class="progress-bar" :style="{width:t.pct+'%',background:t.c}"></div></div>
          </div>
          <hr/>
          <div class="d-flex justify-content-between"><span class="text-muted2">Total Files</span><h5 class="fw-bold mb-0">{{ all.length }}</h5></div>
        </div>
      </div>
    </div>

    <!-- Upload modal -->
    <div v-if="showUpload" class="modal-backdrop-c" @click.self="showUpload=false">
      <div class="modal-card">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="d-flex align-items-center gap-2"><span class="up-ic"><i class="fa-solid fa-file-arrow-up"></i></span><h5 class="fw-bold mb-0">Upload Monitoring Report</h5></div>
          <button class="btn-close-x" @click="showUpload=false"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <label class="lbl">Name <span class="text-danger">*</span></label>
        <input class="form-control mb-3" v-model="up.name" placeholder="e.g. ISO Audit 2026"/>
        <label class="lbl">Type <span class="text-danger">*</span></label>
        <div class="mb-3"><DropdownSelect v-model="up.type" :options="uploadTypeOptions" placeholder="Select type"/></div>
        <div class="row">
          <div class="col-6"><label class="lbl">Category</label><input class="form-control" v-model="up.category" placeholder="Optional"/></div>
          <div class="col-6"><label class="lbl">Version</label><input class="form-control" v-model="up.version" placeholder="Optional"/></div>
        </div>
        <label class="lbl mt-3">File <span class="text-danger">*</span></label>
        <input type="file" class="form-control" @change="onFile"/>
        <div class="small text-muted2 mt-1">Accepted: PDF, Word, Excel, PowerPoint, TXT, CSV</div>
        <div class="d-flex justify-content-end gap-2 mt-4">
          <button class="btn btn-light px-3" @click="showUpload=false">Cancel</button>
          <button class="btn btn-primary px-3" :disabled="!up.name||!up.type||!up.file" @click="doUpload"><i class="fa-solid fa-upload me-2"></i>Upload</button>
        </div>
      </div>
    </div>

    <!-- Preview modal -->
    <div v-if="preview" class="modal-backdrop-c" @click.self="preview=null">
      <div class="preview-card">
        <div class="preview-head d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2"><span class="file-ico pdf"><i class="fa-solid fa-file-pdf"></i></span>
            <div><div class="fw-bold">{{ preview.name }}</div><div class="small text-muted2">{{ (preview.ext||'PDF').toUpperCase().replace('.','') }}</div></div></div>
          <div class="d-flex gap-2"><button class="btn btn-sm btn-light" @click="download(preview)"><i class="fa-solid fa-arrow-up-right-from-square"></i></button>
            <button class="btn-close-x" @click="preview=null"><i class="fa-solid fa-xmark"></i></button></div>
        </div>
        <iframe v-if="preview.url" :src="preview.url" class="preview-frame"></iframe>
        <div v-else class="preview-body d-flex flex-column align-items-center justify-content-center">
          <div class="warn-ic"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <h5 class="fw-bold mt-3">Unable to Preview Document</h5>
          <p class="text-muted2 text-center" style="max-width:560px">The file '<b>{{ preview.file_name||preview.name }}</b>' has no stored file URL in this snapshot.</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { load, fmtDate } from '../services/api'
import DropdownSelect from '../components/DropdownSelect.vue'

const all=ref([]), q=ref(''), typeFilter=ref('all'), menuId=ref(null)
const showUpload=ref(false), preview=ref(null)
const up=ref({name:'',type:'',category:'',version:'',file:null})
const TYPES={
  iso:{label:'ISO',cls:'pill-iso',c:'#0d6efd'},
  docket_termination:{label:'Docket Cancellation',cls:'pill-amber',c:'#f59e0b'},
  complaint:{label:'Complaint',cls:'pill-red',c:'#dc2626'},
  f001:{label:'F001',cls:'pill-green',c:'#2e9865'},
}
const typeMeta=t=> TYPES[t] || {label:(t||'other').toUpperCase(),cls:'pill-gray',c:'#64748b'}
const typeOptions=computed(()=>[{value:'all',label:'All Types'},...Object.keys(TYPES).map(k=>({value:k,label:TYPES[k].label}))])
const uploadTypeOptions=Object.keys(TYPES).map(k=>({value:k,label:TYPES[k].label}))
const filtered=computed(()=> all.value.filter(f=>(typeFilter.value==='all'||f.type===typeFilter.value)&&(!q.value||(f.name||'').toLowerCase().includes(q.value.toLowerCase()))))
const typeBreakdown=computed(()=>{ const c={}; all.value.forEach(f=>c[f.type]=(c[f.type]||0)+1); const mx=Math.max(1,...Object.values(c))
  return Object.keys(TYPES).map(k=>({key:k,label:TYPES[k].label,n:c[k]||0,pct:(c[k]||0)/mx*100,c:TYPES[k].c})) })
function fmtSize(b,h){ if(!h||b==null) return '—'; if(b<1024) return b.toFixed(2)+' B'; if(b<1048576) return (b/1024).toFixed(1)+' KB'; return (b/1048576).toFixed(2)+' MB' }
function onFile(e){ up.value.file=e.target.files[0]||null }
function doUpload(){ // prototype: add to list locally
  all.value.unshift({ id:'new-'+Date.now(), name:up.value.name, type:up.value.type, file_name:up.value.file?.name,
    size:up.value.file?.size, has_file:!!up.value.file, published_at:new Date().toISOString(), isPdf:/pdf/i.test(up.value.file?.name||'') })
  showUpload.value=false; up.value={name:'',type:'',category:'',version:'',file:null}
}
function openPreview(f){ menuId.value=null; preview.value=f }
function download(f){ menuId.value=null; if(f?.url) window.open(f.url,'_blank') }
function onDoc(){ menuId.value=null }
onMounted(async()=>{
  document.addEventListener('click',onDoc)
  const d=await load('monitoring-reports')
  all.value=(d||[]).map(r=>{ const f0=Array.isArray(r.files)&&r.files.length?r.files[0]:null
    return {...r, file_name:r.file_name??f0?.name, size:r.size??f0?.size, has_file:r.has_file??!!f0, ext:r.ext??f0?.ext, url:r.url??f0?.url, isPdf:/pdf/i.test((r.ext??f0?.ext)||(r.file_name??f0?.name)||'')} })
})
onBeforeUnmount(()=> document.removeEventListener('click',onDoc))
</script>
<style scoped>
.file-ico{width:34px;height:34px;border-radius:8px;display:grid;place-items:center;font-size:14px}
.file-ico.pdf{background:#fee2e2;color:#dc2626}.file-ico.doc{background:#f1f5f9;color:#64748b}
.list-scroll{max-height:62vh;overflow:visible}
.act-menu{position:absolute;right:10px;top:40px;width:170px;padding:8px;z-index:1200;text-align:left}
.act-item{display:flex;align-items:center;width:100%;border:0;background:transparent;border-radius:9px;padding:.55rem .7rem;font-weight:600;color:#1f2937}
.act-item:hover{background:#f3f4f6}
.modal-backdrop-c{position:fixed;inset:0;background:rgba(15,23,42,.45);display:grid;place-items:center;z-index:2000;padding:20px}
.modal-card{background:#fff;border-radius:18px;padding:24px;width:100%;max-width:560px;box-shadow:0 30px 80px rgba(0,0,0,.3)}
.lbl{font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;display:block}
.up-ic{width:38px;height:38px;border-radius:10px;background:#2563eb;color:#fff;display:grid;place-items:center}
.btn-close-x{border:0;background:transparent;font-size:18px;color:#6b7280}
.preview-card{background:#fff;border-radius:16px;width:100%;max-width:1000px;height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.35)}
.preview-head{background:#eef1f5;padding:16px 20px}
.preview-body{flex:1;background:#fff}
.preview-frame{flex:1;width:100%;border:0}
.warn-ic{width:72px;height:72px;border-radius:50%;background:#e0eaff;color:#2563eb;display:grid;place-items:center;font-size:26px}
</style>
