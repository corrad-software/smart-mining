import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const B='http://localhost:5178'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.addInitScript(()=>localStorage.setItem('sm_auth','1'))
// site details tabs
const sid='63f88ce021f98c3c28871176'
await p.goto(`${B}/site/${sid}`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(3000); await p.screenshot({path:`${OUT}/proto-sd-spatial.png`})
// reports f001
await p.evaluate(()=>{document.querySelectorAll('.tab-btn').forEach(b=>{if(b.innerText.includes('Reports'))b.click()})})
await p.waitForTimeout(400)
await p.evaluate(()=>{document.querySelectorAll('.dropdown-item').forEach(b=>{if(b.innerText.includes('F001'))b.click()})})
await p.waitForTimeout(800); await p.screenshot({path:`${OUT}/proto-sd-f001.png`})
// media gallery
await p.evaluate(()=>{document.querySelectorAll('.tab-btn').forEach(b=>{if(b.innerText.includes('Media'))b.click()})})
await p.waitForTimeout(800); await p.screenshot({path:`${OUT}/proto-sd-media.png`})
// map with panel
await p.goto(`${B}/map`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(4000)
await p.evaluate(()=>{const m=document.querySelector('.leaflet-marker-icon .site-pill');if(m)m.click()})
await p.waitForTimeout(1200); await p.screenshot({path:`${OUT}/proto-map-panel.png`})
await b.close();console.log('done')
