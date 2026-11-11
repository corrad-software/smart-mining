import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:950}})
await p.addInitScript(()=>localStorage.setItem('sm_auth','1'))
await p.goto('http://localhost:5178/monitoring',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(1800)
// open actions menu
await p.click('tbody tr:first-child .rounded-circle').catch(()=>{})
await p.waitForTimeout(500); await p.screenshot({path:`${OUT}/mon2-menu.png`})
// click View -> preview
await p.click('.act-item:has-text("View")').catch(()=>{})
await p.waitForTimeout(600); await p.screenshot({path:`${OUT}/mon2-preview.png`})
await p.keyboard.press('Escape'); await p.click('.preview-card .btn-close-x').catch(()=>{})
await p.waitForTimeout(300)
// open upload
await p.click('button:has-text("Upload Files")').catch(()=>{})
await p.waitForTimeout(600); await p.screenshot({path:`${OUT}/mon2-upload.png`})
await b.close();console.log('done')
