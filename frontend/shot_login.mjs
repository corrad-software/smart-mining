import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1500,height:950}})
await p.goto('http://localhost:5178/login',{waitUntil:'domcontentloaded',timeout:30000})
await p.waitForTimeout(5000)
await p.screenshot({path:`${OUT}/login-clean.png`})
await b.close();console.log('done')
