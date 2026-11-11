import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1600,height:1000}})
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1200); await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
await p.goto('http://localhost:5178/dashboard',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(3500)
await p.screenshot({path:`${OUT}/final-dash.png`})
// attendance VIEW present count check
await p.goto('http://localhost:5178/attendance',{waitUntil:'networkidle'}); await p.waitForTimeout(2000)
await p.evaluate(()=>{const i=document.querySelector('input[type=date]');i.value='2026-11-10';i.dispatchEvent(new Event('change'))})
await p.waitForTimeout(1500); await p.click('.btn-view').catch(()=>{}); await p.waitForTimeout(1200)
await p.screenshot({path:`${OUT}/final-view.png`})
await b.close();console.log('done')
