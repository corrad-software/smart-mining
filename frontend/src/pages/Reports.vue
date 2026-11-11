<template>
  <div class="page-wrap">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div><h1 class="page-h1">Reports</h1><p class="page-sub">Incident & docket reports · {{ rows.length }} records</p></div>
      <div class="d-flex gap-2">
        <select class="form-select" v-model="sev" style="max-width:150px"><option value="">All severity</option><option>High</option><option>Normal</option><option>Low</option></select>
        <select class="form-select" v-model="typ" style="max-width:190px"><option value="">All types</option><option value="docket_cancelation">Docket cancelation</option><option value="docket_correction">Docket correction</option></select>
      </div>
    </div>
    <div class="card p-3 mt-3">
      <table class="table align-middle mb-0">
        <thead><tr><th>Date</th><th>Name</th><th>Type</th><th>Severity</th><th>Scope</th><th>Site</th><th>Status</th></tr></thead>
        <tbody>
          <tr v-for="r in filtered" :key="r.id">
            <td class="text-muted2">{{ fmtDate(r.date) }}</td><td><b>{{ r.name||'—' }}</b></td>
            <td><span class="pill pill-gray">{{ r.type||'general' }}</span></td>
            <td><span class="pill" :class="sv(r.severity)">{{ r.severity||'—' }}</span></td>
            <td class="text-muted2 text-truncate" style="max-width:260px">{{ r.scope||'—' }}</td>
            <td>{{ name(r.site) }}</td><td><span class="pill pill-iso">{{ r.status||'—' }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { load, fmtDate } from '../services/api'
const rows=ref([]), sev=ref(''), typ=ref('')
const name=v=> v&&(v.name||v)||'—'
function sv(s){return {High:'pill-amber',Normal:'pill-iso',Low:'pill-green'}[s]||'pill-gray'}
const filtered=computed(()=> rows.value.filter(r=>(!sev.value||r.severity===sev.value)&&(!typ.value||r.type===typ.value)))
onMounted(async()=> rows.value=await load('reports'))
</script>
