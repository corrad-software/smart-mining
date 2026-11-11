import { chromium } from 'playwright'
import fs from 'fs'

const OUT = '/private/tmp/claude-501/-Users-rosliamir/a631b6de-d1a5-436b-bc66-d43f1560af84/scratchpad/sm/capture'
fs.mkdirSync(OUT, { recursive: true })
const APP = 'https://smart-mining.selgem.com'

const browser = await chromium.launch()
const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } })
const page = await ctx.newPage()

async function cssOf(sel, props){
  return await page.evaluate(([sel, props])=>{
    const el = document.querySelector(sel); if(!el) return null
    const s = getComputedStyle(el); const o={}; props.forEach(p=>o[p]=s.getPropertyValue(p));
    o._text = (el.innerText||'').slice(0,60); o._rect = el.getBoundingClientRect().height
    return o
  }, [sel, props])
}

try {
  // LOGIN
  await page.goto(APP + '/login', { waitUntil: 'domcontentloaded', timeout: 60000 })
  // find inputs
  const inputs = await page.$$('input')
  if (inputs[0]) await inputs[0].fill(process.env.LEGACY_USER)
  if (inputs[1]) await inputs[1].fill(process.env.LEGACY_PASSWORD)
  await page.screenshot({ path: `${OUT}/00-login.png`, fullPage: true })
  // click submit button
  const btn = await page.$('button[type=submit]') || (await page.$$('button'))[0]
  if (btn) await btn.click()
  await page.waitForTimeout(4000)
  console.log('after login url:', page.url())

  const routes = ['dashboard','map','monitoring','attendance','reports','site-management','user-management','account']
  const report = {}
  for (const r of routes){
    try{
      await page.goto(`${APP}/${r}`, { waitUntil:'domcontentloaded', timeout:45000 })
      await page.waitForTimeout(2500)
      await page.screenshot({ path:`${OUT}/${r}.png`, fullPage:true })
      report[r] = { url: page.url() }
    }catch(e){ report[r] = { error: String(e).slice(0,120) } }
  }

  // Capture the top navbar HTML + styles (from account page which we know has it)
  await page.goto(`${APP}/account`, { waitUntil:'domcontentloaded' })
  await page.waitForTimeout(2000)
  const navHtml = await page.evaluate(()=>{
    const nav = document.querySelector('nav, header, .navbar, [class*=nav], [class*=header]')
    return nav ? nav.outerHTML.slice(0, 4000) : 'NO NAV'
  })
  fs.writeFileSync(`${OUT}/navbar.html`, navHtml)

  // global/body + key element styles
  const styles = {
    body: await cssOf('body', ['font-family','font-size','color','background-color']),
    firstButton: await cssOf('button', ['background-color','color','font-size','font-weight','border-radius','padding','font-family']),
    firstInput: await cssOf('input', ['border','border-radius','padding','font-size','background-color','height']),
    firstLabel: await cssOf('label', ['font-size','font-weight','color']),
    h1: await cssOf('h1', ['font-size','font-weight','color']),
  }
  // nav links text
  const navLinks = await page.evaluate(()=> Array.from(document.querySelectorAll('a')).map(a=>a.innerText.trim()).filter(Boolean).slice(0,20))
  // all buttons text+color
  const buttons = await page.evaluate(()=> Array.from(document.querySelectorAll('button')).slice(0,12).map(b=>{const s=getComputedStyle(b);return {t:b.innerText.trim().slice(0,24),bg:s.backgroundColor,color:s.color,radius:s.borderRadius}}))
  fs.writeFileSync(`${OUT}/styles.json`, JSON.stringify({ styles, navLinks, buttons, report }, null, 2))
  console.log('NAV LINKS:', navLinks.join(' | '))
  console.log('BUTTONS:', JSON.stringify(buttons))
  console.log('STYLES:', JSON.stringify(styles, null, 1))
} catch(e){
  console.log('ERR', e)
} finally {
  await browser.close()
}
