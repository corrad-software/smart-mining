import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1500,height:950}})
// login (video bg)
await p.goto('http://localhost:5178/login',{waitUntil:'domcontentloaded',timeout:30000}); await p.waitForTimeout(3500)
await p.screenshot({path:`${OUT}/v2-login.png`})
await p.click('button:has-text("Submit")'); await p.waitForTimeout(3000)
// user management
await p.goto('http://localhost:5178/user-management',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(2000)
await p.screenshot({path:`${OUT}/v2-users.png`,fullPage:false})
// site management
await p.goto('http://localhost:5178/site-management',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(2000)
await p.screenshot({path:`${OUT}/v2-sites.png`})
// profile dropdown
await p.goto('http://localhost:5178/dashboard',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(1500)
await p.click('.profile-btn'); await p.waitForTimeout(500)
await p.screenshot({path:`${OUT}/v2-menu.png`})
await b.close();console.log('done')
