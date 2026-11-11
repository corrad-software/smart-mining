import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:1000}})
await p.addInitScript(()=>localStorage.setItem('sm_auth','1'))
await p.goto('http://localhost:5178/monitoring',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(1800)
const count=await p.evaluate(()=>document.querySelectorAll('tbody tr').length)
const showing=await p.evaluate(()=>{const e=[...document.querySelectorAll('.text-muted2')].find(x=>x.textContent.includes('Showing'));return e?e.textContent:''})
console.log('rows rendered:',count,'|',showing)
await p.screenshot({path:`${OUT}/mon-all.png`,fullPage:true})
await b.close()
