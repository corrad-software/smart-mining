import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const B='http://localhost:5178'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.addInitScript(()=>localStorage.setItem('sm_auth','1'))
// monitoring with dropdown open
await p.goto(`${B}/monitoring`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(1500)
await p.click('.dd-trigger').catch(()=>{})
await p.waitForTimeout(600); await p.screenshot({path:`${OUT}/fix-monitoring.png`})
// attendance daily with site dropdown open
await p.goto(`${B}/attendance`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(1500)
await p.click('.dd-trigger').catch(()=>{})
await p.waitForTimeout(600); await p.screenshot({path:`${OUT}/fix-att-dd.png`})
await p.keyboard.press('Escape')
// attendance monthly
await p.click('button:has-text("Monthly")').catch(()=>{})
await p.waitForTimeout(1200); await p.screenshot({path:`${OUT}/fix-att-monthly.png`})
await b.close();console.log('done')
