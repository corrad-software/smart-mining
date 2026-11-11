<template>
  <div class="page-wrap">
    <h1 class="page-h1">User Management</h1>
    <p class="page-sub">Manage system users, roles, regions, and permissions.</p>

    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
      <div class="input-group" style="max-width:300px"><span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input class="form-control" v-model="q" placeholder="Search users..."/></div>
      <select class="form-select ms-auto" style="max-width:150px"><option>All Regions</option></select>
      <select class="form-select" style="max-width:150px"><option>All Statuses</option></select>
      <select class="form-select" style="max-width:130px"><option>All Users</option></select>
      <button class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Create New User</button>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-lg-8">
        <div class="card p-0 overflow-hidden">
          <table class="table mb-0 align-middle">
            <thead><tr><th style="width:40px">#</th><th>User / Role</th><th>Regions / Sites</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr v-for="(u,i) in filtered" :key="u.id">
                <td class="text-muted2 align-top pt-3">{{ i+1 }}</td>
                <td class="align-top pt-3" style="min-width:230px">
                  <div class="d-flex align-items-start gap-2">
                    <span class="avatar2">{{ (u.username||'?')[0].toUpperCase() }}</span>
                    <div><span class="rolebadge" :class="roleClass(u.role)">{{ (u.role||'').toUpperCase() }}</span>
                      <b class="ms-1">{{ u.username }}</b>
                      <div class="tiny text-muted2 mt-1">ID: {{ String(u.id||'').slice(0,24) }}</div>
                      <div class="tiny text-muted2">Email: {{ u.email }}</div></div>
                  </div>
                </td>
                <td class="align-top pt-3">
                  <div v-for="r in u.regions" :key="r.name" class="mb-2">
                    <div class="small"><b>{{ r.name }}</b> · <span class="text-success fw-bold">{{ r.active }} ACTIVE</span> / <span class="text-muted2 fw-bold">{{ r.inactive }} INACTIVE</span></div>
                    <div class="chips mt-1"><span v-for="(c,ci) in r.codes.slice(0,40)" :key="ci" class="chip" :class="c.active?'chip-on':'chip-off'">{{ c.code }}</span></div>
                  </div>
                  <span v-if="!u.regions.length" class="text-muted2">—</span>
                </td>
                <td class="text-end align-top pt-3"><button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card p-4">
          <h5 class="fw-bold mb-3">User Role Summary</h5>
          <div v-for="r in summary" :key="r.k" class="mb-3">
            <div class="d-flex justify-content-between"><b>{{ r.k }}</b><span class="text-muted2">{{ r.n }}</span></div>
            <div class="progress mt-1" style="height:6px"><div class="progress-bar" :style="{width:r.pct+'%',background:r.c}"></div></div>
          </div>
          <hr/>
          <div class="d-flex justify-content-between"><span class="text-muted2">Total Users</span><h5 class="fw-bold mb-0">{{ users.length }}</h5></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
const users=ref([]), q=ref('')
function roleClass(r){const t=(r||'').toLowerCase()
  return t.includes('super')?'rb-red':t.includes('auth')?'rb-blue':t.includes('site')?'rb-orange':t.includes('exec')?'rb-amber':t.includes('manager')?'rb-blue':t.includes('po')?'rb-blue':'rb-gray'}
const filtered=computed(()=> users.value.filter(u=>!q.value||(u.username||'').toLowerCase().includes(q.value.toLowerCase())||(u.email||'').toLowerCase().includes(q.value.toLowerCase())))
const COL={'Super Admin':'#dc2626','Authenticated':'#0d6efd','Site Officer':'#f59e0b','Executive':'#eab308','Manager':'#0d6efd','PO':'#0d6efd','Public':'#94a3b8'}
const summary=computed(()=>{
  const c={}; users.value.forEach(u=>c[u.role]=(c[u.role]||0)+1)
  const order=['Super Admin','Authenticated','Site Officer','Executive','Manager','PO','Public']
  const mx=Math.max(1,...order.map(k=>c[k]||0))
  return order.map(k=>({k,n:c[k]||0,pct:(c[k]||0)/mx*100,c:COL[k]||'#64748b'}))
})
onMounted(async()=>{
  try{ const r=await fetch('/data/users-rich.json'); users.value=await r.json() }catch{ users.value=[] }
})
</script>
<style scoped>
.avatar2{width:40px;height:40px;border-radius:50%;background:#eef2f7;color:#475569;display:grid;place-items:center;font-weight:700;flex-shrink:0}
.tiny{font-size:11px}
.rolebadge{font-size:10px;font-weight:700;padding:.15rem .45rem;border-radius:7px;letter-spacing:.03em}
.rb-red{background:#fee2e2;color:#b91c1c}.rb-blue{background:#e0f2fe;color:#0369a1}.rb-orange{background:#ffedd5;color:#c2410c}.rb-amber{background:#fef9c3;color:#a16207}.rb-gray{background:#f1f5f9;color:#64748b}
.chips{display:flex;flex-wrap:wrap;gap:4px}
.chip{font-size:10px;font-weight:700;padding:.12rem .4rem;border-radius:6px}
.chip-on{background:#334155;color:#fff}.chip-off{background:#f1f5f9;color:#94a3b8}
</style>
