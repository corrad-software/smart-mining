import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:1000}})
// REAL login via API
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1200)
await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
await p.goto('http://localhost:5178/monitoring',{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await p.waitForTimeout(2000)
const info=await p.evaluate(()=>{
  const rows=document.querySelectorAll('tbody tr').length
  const showing=[...document.querySelectorAll('.text-muted2')].find(x=>x.textContent.includes('Showing'))?.textContent||''
  const firstSize=document.querySelector('tbody tr td:nth-child(4)')?.textContent||''
  return {rows,showing,firstSize, apiMode: !!localStorage.getItem('sm_token')}
})
console.log(JSON.stringify(info))
await p.screenshot({path:`${OUT}/mon-api.png`})
await b.close()
