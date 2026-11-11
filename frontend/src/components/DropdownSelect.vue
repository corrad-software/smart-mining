<template>
  <div class="dd" ref="root">
    <button type="button" class="dd-trigger" :class="{open}" @click="open=!open">
      <span class="dd-label">{{ selectedLabel }}</span>
      <i class="fa-solid" :class="open?'fa-chevron-up':'fa-chevron-down'"></i>
    </button>
    <div v-if="open" class="dd-menu">
      <div v-if="searchable" class="dd-search">
        <i class="fa-solid fa-magnifying-glass text-secondary"></i>
        <input v-model="q" placeholder="Search..." @click.stop />
      </div>
      <ul>
        <li v-for="(o,i) in filtered" :key="o.value">
          <button type="button" class="dd-item" :class="{sel: o.value===modelValue}" @click="pick(o)">
            <span class="text-truncate">{{ o.label }}</span>
            <i v-if="o.value===modelValue" class="fa-solid fa-check"></i>
          </button>
          <div v-if="dividerAfter===i" class="dd-divider"></div>
        </li>
        <li v-if="!filtered.length" class="dd-empty">No matches</li>
      </ul>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
const props = defineProps({
  modelValue: {}, options: { type: Array, default: () => [] },
  searchable: Boolean, dividerAfter: { type: Number, default: -1 },
  placeholder: { type: String, default: 'Select' },
})
const emit = defineEmits(['update:modelValue'])
const open = ref(false), q = ref(''), root = ref(null)
const selectedLabel = computed(() => props.options.find(o => o.value === props.modelValue)?.label || props.placeholder)
const filtered = computed(() => !q.value ? props.options
  : props.options.filter(o => o.label.toLowerCase().includes(q.value.toLowerCase())))
function pick(o) { emit('update:modelValue', o.value); open.value = false; q.value = '' }
function onDoc(e) { if (root.value && !root.value.contains(e.target)) open.value = false }
onMounted(() => document.addEventListener('click', onDoc))
onBeforeUnmount(() => document.removeEventListener('click', onDoc))
</script>
<style scoped>
.dd { position: relative; }
.dd-trigger { display:flex; align-items:center; justify-content:space-between; gap:10px; width:100%;
  background:#fff; border:1px solid #dee2e6; border-radius:12px; padding:.6rem .9rem; font-weight:600;
  color:#374151; font-size:14px; min-height:42px; cursor:pointer; }
.dd-trigger.open { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,.15); }
.dd-trigger i { color:#6b7280; font-size:12px; }
.dd-menu { position:absolute; top:calc(100% + 6px); left:0; right:0; min-width:230px; background:#fff;
  border-radius:14px; box-shadow:0 10px 30px rgba(16,24,40,.16); padding:10px; z-index:1200; }
.dd-search { display:flex; align-items:center; gap:8px; border:1px solid #e5e7eb; border-radius:12px;
  padding:.5rem .75rem; margin-bottom:8px; }
.dd-search input { border:0; outline:0; width:100%; font-size:14px; }
.dd ul { list-style:none; margin:0; padding:0; max-height:280px; overflow:auto; }
.dd-item { display:flex; align-items:center; justify-content:space-between; gap:10px; width:100%;
  background:transparent; border:0; border-radius:10px; padding:.6rem .8rem; font-size:15px; color:#1f2937;
  cursor:pointer; text-align:left; }
.dd-item:hover { background:#f3f4f6; }
.dd-item.sel { background:#0d6efd; color:#fff; font-weight:600; }
.dd-divider { height:1px; background:#eef0f3; margin:8px 4px; }
.dd-empty { padding:.6rem .8rem; color:#9aa3b2; font-size:13px; }
</style>
