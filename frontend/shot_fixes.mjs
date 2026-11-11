import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1200); await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
// monitoring preview real pdf
await p.goto('http://localhost:5178/monitoring',{waitUntil:'networkidle'}); await p.waitForTimeout(1800)
await p.click('tbody tr:first-child .rounded-circle').catch(()=>{}); await p.waitForTimeout(400)
await p.click('.act-item:has-text("View")').catch(()=>{}); await p.waitForTimeout(2500)
await p.screenshot({path:`${OUT}/fx-mon-preview.png`})
await p.keyboard.press('Escape'); await p.click('.preview-card .btn-close-x').catch(()=>{}); await p.waitForTimeout(300)
// attendance office filter
await p.goto('http://localhost:5178/attendance',{waitUntil:'networkidle'}); await p.waitForTimeout(1500)
await p.evaluate(()=>{const i=document.querySelector('input[type=date]');i.value='2026-11-10';i.dispatchEvent(new Event('change'))}); await p.waitForTimeout(1200)
await p.click('.dd-trigger'); await p.waitForTimeout(400)
await p.click('.dd-item:has-text("Pejabat Satelit Selatan")').catch(()=>{}); await p.waitForTimeout(1000)
await p.screenshot({path:`${OUT}/fx-att-filter.png`})
// dashboard year dropdown
await p.goto('http://localhost:5178/dashboard',{waitUntil:'networkidle'}); await p.waitForTimeout(2500)
await p.click('.year-btn').catch(()=>{}); await p.waitForTimeout(500)
await p.screenshot({path:`${OUT}/fx-year.png`})
await b.close();console.log('done')
