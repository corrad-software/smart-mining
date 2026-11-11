import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1500,height:950}})
await p.goto('http://localhost:5178/login',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(1500)
await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
await p.goto('http://localhost:5178/map',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(4000)
await p.click('button:has-text("Northern Region")'); await p.waitForTimeout(2500)
await p.screenshot({path:`${OUT}/pills-north.png`})
await b.close();console.log('done')
