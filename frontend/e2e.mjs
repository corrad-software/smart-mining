import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
const reqs=[]
p.on('response', r=>{ if(r.url().includes(':8008/api')) reqs.push(r.status()+' '+r.url().split('/api/')[1]) })
await p.goto('http://localhost:5178/login',{waitUntil:'networkidle',timeout:30000})
await p.waitForTimeout(1500)
// fill real creds (already prefilled) and submit
await p.click('button:has-text("Submit")')
await p.waitForTimeout(3500)
console.log('url after login:',p.url())
console.log('token set:', await p.evaluate(()=>!!localStorage.getItem('sm_token')))
await p.goto('http://localhost:5178/dashboard',{waitUntil:'networkidle'}); await p.waitForTimeout(2500)
await p.screenshot({path:`${OUT}/e2e-dashboard.png`})
await p.goto('http://localhost:5178/site-management',{waitUntil:'networkidle'}); await p.waitForTimeout(2000)
await p.screenshot({path:`${OUT}/e2e-sites.png`})
console.log('API calls observed:'); reqs.slice(0,12).forEach(r=>console.log('  '+r))
await b.close()
