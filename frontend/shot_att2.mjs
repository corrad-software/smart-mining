import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const B='http://localhost:5178'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.addInitScript(()=>localStorage.setItem('sm_auth','1'))
await p.goto(`${B}/attendance`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(1800)
await p.screenshot({path:`${OUT}/att2-daily.png`})
// open VIEW modal on first row
await p.click('.btn-view').catch(()=>{})
await p.waitForTimeout(800); await p.screenshot({path:`${OUT}/att2-view.png`})
await p.keyboard.press('Escape')
await p.click('.modal-card .btn-secondary').catch(()=>{})
await p.waitForTimeout(300)
// open upload modal
await p.click('button:has-text("Upload Attendance")').catch(()=>{})
await p.waitForTimeout(700); await p.screenshot({path:`${OUT}/att2-upload.png`})
await b.close();console.log('done')
