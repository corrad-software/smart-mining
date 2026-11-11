import { chromium } from 'playwright'
import fs from 'fs'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const APP='https://smart-mining.selgem.com'
const browser=await chromium.launch()
const page=await browser.newPage({viewport:{width:1440,height:900}})
// login page first (logged out)
await page.goto(APP+'/login',{waitUntil:'domcontentloaded',timeout:60000}); await page.waitForTimeout(3500)
await page.screenshot({path:`${OUT}/real-login.png`})
// now login
const em=await page.$('input[type=email]')||(await page.$$('input'))[0]
const pw=await page.$('input[type=password]')||(await page.$$('input'))[1]
await em.fill(process.env.LEGACY_EMAIL); await pw.fill(process.env.LEGACY_PASSWORD)
await (await page.$('button[type=submit]')||(await page.$$('button'))[0]).click()
await page.waitForTimeout(5000)
for(const r of ['site-management','user-management']){
  await page.goto(`${APP}/${r}`,{waitUntil:'domcontentloaded'}); await page.waitForTimeout(4000)
  await page.screenshot({path:`${OUT}/real-${r}.png`,fullPage:true})
  // capture structural html + buttons
  const html=await page.evaluate(()=>document.querySelector('main,.container-fluid,#app>div')?.innerText.slice(0,500))
  fs.writeFileSync(`${OUT}/real-${r}.txt`, html||'')
}
console.log('captured login, site-management, user-management')
await browser.close()
