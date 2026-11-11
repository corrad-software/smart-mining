<template>
  <div class="page-wrap" v-if="w">
    <router-link :to="`/site/${$route.params.id}`" class="text-muted2 small"><i class="fa-solid fa-arrow-left me-1"></i>Site</router-link>
    <div class="d-flex justify-content-between align-items-center mt-2">
      <div><h1 class="page-h1">F001 Weekly Report</h1><p class="page-sub">{{ w.document_no||w.id }} · {{ name(w.site) }} · {{ fmtDate(w.start_effective_date) }} → {{ fmtDate(w.end_effective_date) }}</p></div>
      <span class="pill pill-gray">{{ w.status }}</span>
    </div>
    <div class="card p-3 mt-2 d-flex flex-row gap-2 flex-wrap">
      <div v-for="(s,i) in flow" :key="s" class="d-flex align-items-center gap-2 flex-grow-1" :class="idx>=i?'text-primary fw-bold':'text-muted2'">
        <span class="step-dot" :class="{on:idx>=i}">{{ i+1 }}</span>{{ s }}
      </div>
    </div>
    <div class="row g-3 mt-1">
      <div class="col-md-4" v-for="k in kv" :key="k.l"><div class="stat-tile"><div class="tile-label">{{ k.l }}</div><div class="fw-bold mt-1">{{ k.v }}</div></div></div>
    </div>
    <div class="card p-3 mt-3"><h6 class="fw-bold mb-3">Approval Activity</h6>
      <table class="table mb-0"><thead><tr><th>From</th><th>To</th><th>By</th><th>Role</th></tr></thead>
        <tbody><tr v-for="(a,i) in acts" :key="i"><td><span class="pill pill-gray">{{ a.from_status||'∅' }}</span></td>
          <td><span class="pill pill-iso">{{ a.to_status }}</span></td><td>{{ a.action_by_name||'—' }}</td><td class="text-muted2">{{ a.action_by_role||'—' }}</td></tr>
        <tr v-if="!acts.length"><td colspan="4" class="text-muted2">No recorded activity in snapshot.</td></tr></tbody></table>
    </div>
  </div>
  <div class="page-wrap text-muted2" v-else>Loading…</div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'; import { useRoute } from 'vue-router'
import { load, fmtDate } from '../services/api'
const route=useRoute(); const w=ref(null), acts=ref([])
const flow=['draft','pending_review','pending_approval','submitted']
const name=v=> v&&(v.name||v.username||v)||'—'
const idx=computed(()=> Math.max(0,flow.indexOf(w.value?.status)))
const kv=computed(()=>w.value?[
  {l:'Contractor',v:name(w.value.contractor)},{l:'Cycle Type',v:w.value.cycle_type||'—'},
  {l:'Prepared By',v:name(w.value.prepared_by)}]:[])
onMounted(async()=>{
  const [wr,ra]=await Promise.all(['weekly-reports','report-activities'].map(load))
  w.value=wr.find(x=>String(x.id)===route.params.weeklyId)||wr[0]
  acts.value=ra.filter(a=>(a.weekly_report&&(a.weekly_report.id||a.weekly_report))==w.value?.id).slice(0,30)
})
</script>
<style scoped>.step-dot{width:26px;height:26px;border-radius:50%;background:#eef2f7;display:grid;place-items:center;font-size:12px}
.step-dot.on{background:#0d6efd;color:#fff}</style>
