import { chromium } from 'playwright'
const OUT = process.argv[2]; const B = 'http://localhost:5178'
const b = await chromium.launch(); const ctx = await b.newContext({ viewport: { width: 1600, height: 900 }, deviceScaleFactor: 1.5 }); const p = await ctx.newPage()
await p.goto(`${B}/login`, { waitUntil: 'domcontentloaded' }); await p.waitForTimeout(2500)
await p.fill('.glass input >> nth=0', 'KS0197'); await p.fill('.glass input[type=password]', 'password'); await p.click('button:has-text("Submit")'); await p.waitForURL('**/ess'); await p.waitForTimeout(1500)
await p.goto(`${B}/ess/hr?tab=heat`); await p.waitForSelector('.hm', { timeout: 30000 }); await p.waitForTimeout(2000); await p.screenshot({ path: `${OUT}/32-hr-heat.png` })
await p.goto(`${B}/ess/hr?tab=trend`); await p.waitForSelector('canvas', { timeout: 30000 }); await p.waitForTimeout(2500); await p.screenshot({ path: `${OUT}/33-hr-trend.png` })
await b.close(); console.log('done')
