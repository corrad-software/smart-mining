import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1500,height:950}})
const errs=[]; p.on('pageerror',e=>errs.push(e.message)); p.on('console',m=>{if(m.type()==='error')errs.push(m.text())})
await p.goto('http://localhost:5178/login',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(1500)
await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
await p.goto('http://localhost:5178/site-management',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(4000)
const rows=await p.evaluate(()=>document.querySelectorAll('tbody tr').length)
console.log('rows:',rows,'| errors:',errs.slice(0,3))
await p.screenshot({path:`${OUT}/v2-sites.png`})
await b.close()
