import { chromium } from 'playwright'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const B='http://localhost:5178'
const browser=await chromium.launch()
const page=await browser.newPage({viewport:{width:1440,height:900}})
// login page (no auth)
await page.goto(`${B}/login`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
await page.waitForTimeout(2000); await page.screenshot({path:`${OUT}/proto-login.png`})
// authed pages
await page.addInitScript(()=>localStorage.setItem('sm_auth','1'))
for(const r of ['site-management','user-management']){
  await page.goto(`${B}/${r}`,{waitUntil:'networkidle',timeout:30000}).catch(()=>{})
  await page.waitForTimeout(1800)
  await page.screenshot({path:`${OUT}/proto-${r}.png`,fullPage:true})
  console.log('shot',r)
}
await browser.close()
