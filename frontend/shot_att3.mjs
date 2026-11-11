import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:950}})
// REAL login
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1200); await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
await p.goto('http://localhost:5178/attendance',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(2500)
// set date to 2026-11-10
await p.evaluate(()=>{const i=document.querySelector('input[type=date]'); i.value='2026-11-10'; i.dispatchEvent(new Event('change'))})
await p.waitForTimeout(1500)
await p.screenshot({path:`${OUT}/att3-daily.png`})
await p.click('.btn-view').catch(()=>{}); await p.waitForTimeout(1200)
await p.screenshot({path:`${OUT}/att3-view.png`})
await b.close();console.log('done')
