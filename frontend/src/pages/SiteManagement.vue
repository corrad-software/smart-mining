<template>
  <div class="page-wrap">
    <h1 class="page-h1">Site Management</h1>
    <p class="page-sub">Generate, manage and monitoring document site.</p>

    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
      <div class="input-group" style="max-width:300px"><span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input class="form-control" v-model="q" placeholder="Search sites..."/></div>
      <select class="form-select ms-auto" style="max-width:160px"><option>All Regions</option></select>
      <select class="form-select" style="max-width:180px"><option>All Permit Statuses</option></select>
      <select class="form-select" style="max-width:160px"><option>All Site Statuses</option></select>
      <div class="input-group" style="max-width:220px"><span class="input-group-text bg-white"><i class="fa-regular fa-calendar text-secondary"></i></span><input class="form-control" placeholder="Filter by date range"/></div>
      <button class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Create New Site</button>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-lg-8">
        <div class="card p-0 overflow-hidden">
          <table class="table mb-0 align-middle">
            <thead><tr><th style="width:40px">#</th><th>Site Name</th><th>Contractor</th><th>Permit Date</th><th>Luas Permit Date</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr v-for="(s,i) in filtered.slice(0,30)" :key="s.id">
                <td class="text-muted2">{{ i+1 }}</td>
                <td>
                  <div class="mb-1"><span class="dot"></span><span class="pill pill-iso ms-1">Activate</span>
                    <span class="text-muted2 small ms-1">{{ String(s._id||s.id||'').slice(0,24) }}</span></div>
                  <div><span class="code">{{ s.site_code||'—' }}</span> · <b>{{ trunc(s.name) }}</b></div>
                  <div class="text-muted2 small">{{ name(s.region) }} · {{ s.type||s.site_type||'—' }}</div>
                  <div class="text-muted2 small mono">{{ s.lat }}, {{ s.lng }}</div>
                </td>
                <td class="small">{{ name(s.contractor) }}</td>
                <td class="small"><b>48</b><div>{{ fmtDate(s.permit_date) }}</div><div class="text-muted2">→ {{ fmtDate(s.permit_date_end) }}</div></td>
                <td class="small">{{ fmtDate(s.luas_permit_date) }}</td>
                <td class="text-end"><button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card p-4">
          <h5 class="fw-bold mb-3">Permit Status</h5>
          <div v-for="r in permit" :key="r.region" class="mb-4">
            <div class="d-flex justify-content-between mb-2"><b>{{ r.region }}</b><span class="text-muted2">{{ r.total }} Total</span></div>
            <div v-for="b in r.bars" :key="b.k" class="mb-2">
              <div class="d-flex justify-content-between small"><span>{{ b.k }}</span><span class="text-muted2">{{ b.pct }}% ({{ b.n }})</span></div>
              <div class="progress mt-1" style="height:6px"><div class="progress-bar" :style="{width:b.pct+'%',background:b.c}"></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { load, fmtDate } from '../services/api'
const sites=ref([]), q=ref('')
const name=v=> v&&(v.name||v)||'—'
const trunc=s=> s? (s.length>34? s.slice(0,34)+'…':s):'—'
const filtered=computed(()=> sites.value.filter(s=>!q.value||(s.name||'').toLowerCase().includes(q.value.toLowerCase())))
// real permit status computed from each site's permit_date_end
function permitState(s){
  if(!s.permit_date_end) return 'expired'
  const days=Math.ceil((new Date(s.permit_date_end)-new Date())/864e5)
  if(days<0) return 'expired'; if(days<=90) return 'warning'; return 'active'
}
const permit=computed(()=>{
  const regs=['Northern Region','Southern Region','Central Region']
  return regs.map(region=>{
    const list=sites.value.filter(s=>name(s.region)===region)
    const c={active:0,warning:0,expired:0}; list.forEach(s=>c[permitState(s)]++)
    const total=list.length||0
    const pct=n=> total? Math.round(n/total*100):0
    return {region,total,bars:[
      {k:'Active',pct:pct(c.active),n:c.active,c:'#0d6efd'},
      {k:'Warning',pct:pct(c.warning),n:c.warning,c:'#f59e0b'},
      {k:'Expired',pct:pct(c.expired),n:c.expired,c:'#dc2626'}]}
  })
})
onMounted(async()=> sites.value=await load('sites'))
</script>
<style scoped>
.dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#f59e0b;vertical-align:middle}
.code{background:#f1f5f9;color:#475569;font-size:11px;font-weight:700;padding:.1rem .4rem;border-radius:6px}
.mono{font-family:monospace;font-size:11px}
</style>
