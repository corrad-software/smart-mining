import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1500,height:1000}})
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1200); await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
await p.goto('http://localhost:5178/dashboard',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(3000)
await p.screenshot({path:`${OUT}/dash-charts.png`,fullPage:true})
console.log('done')
await b.close()
