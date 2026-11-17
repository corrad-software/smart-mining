import { chromium } from 'playwright'
const OUT = process.argv[2]; const B = 'http://localhost:5178'
const b = await chromium.launch(); const W = { viewport: { width: 1600, height: 900 }, deviceScaleFactor: 1.5 }
async function login(id, dest = '**/ess') { const ctx = await b.newContext(W); const p = await ctx.newPage()
  await p.goto(`${B}/login`, { waitUntil: 'domcontentloaded' }); await p.waitForTimeout(2500)
  await p.fill('.glass input >> nth=0', id); await p.fill('.glass input[type=password]', 'password'); await p.click('button:has-text("Submit")'); await p.waitForURL(dest, { timeout: 45000 }); await p.waitForTimeout(1500); return p }
// map site panel
let p = await login('admin@kssb.local', '**/map')
await p.goto(`${B}/map`); await p.waitForSelector('.leaflet-container'); await p.waitForTimeout(4000)
await p.fill('input[placeholder^="Search site"]', 'BJ04'); await p.waitForTimeout(1200)
await p.click('[class*="res"] >> text=BJ04 >> nth=0').catch(async () => { await p.keyboard.press('Enter') }); await p.waitForTimeout(4500)
await p.screenshot({ path: `${OUT}/03-map-site.png` })
// ask on attendance page with a specific question
await p.goto(`${B}/attendance`); await p.waitForTimeout(5000); await p.click('.ask-fab'); await p.waitForTimeout(800)
await p.fill('.ask-input textarea', 'Who is late most often and what should we do?'); await p.keyboard.press('Enter')
await p.waitForSelector('.bubble.bot:not(.typing)', { timeout: 60000 }); await p.waitForTimeout(2500); await p.screenshot({ path: `${OUT}/13-ask.png` })
// staff submits a leave request
p = await login('KS0294'); await p.waitForSelector('.hero'); await p.click('.qa-i >> nth=0'); await p.waitForSelector('.rf')
await p.fill('.rf input[type=date] >> nth=0', '2026-11-26'); await p.fill('.rf input[type=date] >> nth=1', '2026-11-27'); await p.fill('.rf textarea', 'Urusan keluarga di kampung')
await p.click('.rf .btn-pri'); await p.waitForTimeout(2500)
await p.goto(`${B}/ess/requests`); await p.waitForTimeout(2500); await p.screenshot({ path: `${OUT}/37-ess-requests.png` })
// supervisor approvals
p = await login('A12930'); await p.goto(`${B}/ess/approvals`); await p.waitForTimeout(3000); await p.screenshot({ path: `${OUT}/36-hr-approvals.png` })
await b.close(); console.log('done')
