import { chromium } from 'playwright'
import fs from 'fs'
const OUT='/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
const APP='https://smart-mining.selgem.com'
const browser=await chromium.launch()
const ctx=await browser.newContext({viewport:{width:1440,height:900}})
const page=await ctx.newPage()
await page.goto(APP+'/login',{waitUntil:'domcontentloaded',timeout:60000})
await page.waitForTimeout(2500)
const em=await page.$('input[type=email]')||(await page.$$('input'))[0]
const pw=await page.$('input[type=password]')||(await page.$$('input'))[1]
await em.fill(process.env.LEGACY_EMAIL); await pw.fill(process.env.LEGACY_PASSWORD)
await (await page.$('button[type=submit]')||(await page.$$('button'))[0]).click()
await page.waitForTimeout(5000)
console.log('url after login:',page.url())
if(page.url().includes('login')){
  await em.fill(process.env.LEGACY_USER); await pw.fill(process.env.LEGACY_PASSWORD)
  await (await page.$$('button'))[0].click(); await page.waitForTimeout(5000)
  console.log('url retry:',page.url())
}
for(const r of ['dashboard','account','monitoring','attendance','map']){
  await page.goto(`${APP}/${r}`,{waitUntil:'domcontentloaded'}); await page.waitForTimeout(3000)
  await page.screenshot({path:`${OUT}/auth-${r}.png`,fullPage:true})
}
const nav=await page.evaluate(()=>{
  const cand=[...document.querySelectorAll('header,nav,.navbar')].sort((a,b)=>a.getBoundingClientRect().top-b.getBoundingClientRect().top)[0]
  return cand?cand.outerHTML.slice(0,6000):'NONE'
})
fs.writeFileSync(`${OUT}/auth-navbar.html`,nav)
const navLinks=await page.evaluate(()=>[...document.querySelectorAll('header a,nav a,a')].map(a=>({t:a.innerText.trim(),href:a.getAttribute('href')})).filter(x=>x.t).slice(0,25))
const btns=await page.evaluate(()=>[...document.querySelectorAll('button')].slice(0,15).map(b=>{const s=getComputedStyle(b);return{t:b.innerText.trim().slice(0,22),bg:s.backgroundColor,color:s.color,radius:s.borderRadius}}))
const navCss=await page.evaluate(()=>{const h=[...document.querySelectorAll('header,nav,.navbar')].sort((a,b)=>a.getBoundingClientRect().top-b.getBoundingClientRect().top)[0];if(!h)return null;const s=getComputedStyle(h);return{height:h.getBoundingClientRect().height,bg:s.backgroundColor,borderBottom:s.borderBottom,boxShadow:s.boxShadow}})
fs.writeFileSync(`${OUT}/auth-styles.json`,JSON.stringify({navLinks,btns,navCss},null,2))
console.log('NAVLINKS',JSON.stringify(navLinks))
console.log('NAVCSS',JSON.stringify(navCss))
console.log('BTNS',JSON.stringify(btns))
await browser.close()
