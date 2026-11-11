import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1200); await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
// map all region
await p.goto('http://localhost:5178/map',{waitUntil:'networkidle'}); await p.waitForTimeout(3500)
await p.screenshot({path:`${OUT}/map3-all.png`})
// northern region
await p.click('button:has-text("Northern Region")'); await p.waitForTimeout(2000)
await p.screenshot({path:`${OUT}/map3-north.png`})
// site details media files (RD36 id 34)
await p.goto('http://localhost:5178/site/34?tab=media',{waitUntil:'networkidle'}); await p.waitForTimeout(2500)
await p.screenshot({path:`${OUT}/sd-gallery.png`})
// switch to files
await p.click('.tab-btn:has-text("Media")').catch(()=>{}); await p.waitForTimeout(300)
await p.click('.dropdown-item:has-text("Files")').catch(()=>{}); await p.waitForTimeout(1200)
await p.screenshot({path:`${OUT}/sd-files.png`})
await b.close();console.log('done')
