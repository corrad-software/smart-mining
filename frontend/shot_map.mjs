import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.addInitScript(()=>localStorage.setItem('sm_auth','1'))
await p.goto('http://localhost:5178/map',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(5000)
await p.screenshot({path:`${OUT}/proto-map2.png`})
await b.close();console.log('done')
