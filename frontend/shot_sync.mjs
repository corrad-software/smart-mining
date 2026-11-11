import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1400,height:900}})
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1000); await p.click('button:has-text("Submit")'); await p.waitForTimeout(2500)
await p.goto('http://localhost:5178/dashboard',{waitUntil:'networkidle'}); await p.waitForTimeout(1500)
await p.click('.profile-btn'); await p.waitForTimeout(500)
await p.click('button:has-text("Sync Data")'); await p.waitForTimeout(600)
await p.screenshot({path:`${OUT}/sync-modal.png`})
await b.close();console.log('done')
