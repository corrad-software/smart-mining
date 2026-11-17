import { chromium } from 'playwright'
const OUT = process.argv[2]; const errs = []; const B = 'http://localhost:5178'
const b = await chromium.launch({ args: ['--use-fake-device-for-media-stream', '--use-fake-ui-for-media-stream'] })
const W = { viewport: { width: 1600, height: 900 }, deviceScaleFactor: 1.5 }
async function login(ctx, id, dest = '**/ess') {
  const p = await ctx.newPage()
  p.on('pageerror', e => errs.push(`${id}: ${e.message}`))
  await p.goto(`${B}/login`, { waitUntil: 'domcontentloaded' }); await p.waitForTimeout(2500)
  await p.fill('.glass input >> nth=0', id); await p.fill('.glass input[type=password]', 'password'); await p.click('button:has-text("Submit")')
  await p.waitForURL(dest, { timeout: 45000 }); await p.waitForTimeout(2000); return p
}
async function shot(p, name, fn) { try { if (fn) await fn(); await p.screenshot({ path: `${OUT}/${name}.png` }); console.log('ok', name) } catch (e) { console.log('FAIL', name, e.message.split('\n')[0]) } }
async function go(p, path, sel, wait = 2500) { await p.goto(B + path, { waitUntil: 'domcontentloaded' }); if (sel) await p.waitForSelector(sel, { timeout: 30000 }); await p.waitForTimeout(wait) }

// ── seed a few demo clock-ins so the HR alerts / map have content (cleaned afterwards)
async function clockIn(id, geo) {
  const ctx = await b.newContext({ ...W, permissions: ['geolocation', 'camera'], geolocation: geo })
  const p = await login(ctx, id)
  try { await p.click('.punch-btn'); await p.waitForSelector('.sc video'); await p.waitForTimeout(1200)
    await p.click('.sc .btn-pri'); await p.waitForSelector('.sc img', { timeout: 10000 }); await p.click('.sc .btn-pri'); await p.waitForSelector('.punch-msg', { timeout: 15000 }); await p.waitForTimeout(600)
    console.log(id, '->', await p.textContent('.punch-msg')) } catch (e) { console.log('clock fail', id, e.message.split('\n')[0]) }
  await ctx.close()
}
// KS0337 already done
// await clockIn('KS0337', { latitude: 3.4115, longitude: 101.3700, accuracy: 12 })
await clockIn('KS0054', { latitude: 3.41152, longitude: 101.37002, accuracy: 15 })
await clockIn('KS0268', { latitude: 3.1478, longitude: 101.6953, accuracy: 30 })

// ── login page
let ctx = await b.newContext(W); let p = await ctx.newPage()
await p.goto(`${B}/login`, { waitUntil: 'domcontentloaded' }); await p.waitForTimeout(3500); await shot(p, '01-login'); await ctx.close()

// ── admin console
ctx = await b.newContext(W); p = await login(ctx, 'admin@kssb.local', '**/map')
await go(p, '/map', '.leaflet-container', 5000); await shot(p, '02-map')
await shot(p, '03-map-site', async () => { const m = await p.$$('.leaflet-marker-icon'); await m[Math.min(5, m.length - 1)].click(); await p.waitForTimeout(3500) })
await go(p, '/dashboard', '.brief', 4000); await shot(p, '04-dash')
await shot(p, '05-dash-2', async () => { await p.mouse.wheel(0, 850); await p.waitForTimeout(1500) })
await shot(p, '06-dash-3', async () => { await p.mouse.wheel(0, 850); await p.waitForTimeout(1500) })
await go(p, '/monitoring', '.utab', 4000); await shot(p, '07-mon')
await shot(p, '08-mon-2', async () => { await p.mouse.wheel(0, 850); await p.waitForTimeout(1500) })
await shot(p, '09-mon-repo', async () => { await p.evaluate(() => scrollTo(0, 0)); await p.click('.utab >> nth=1'); await p.waitForTimeout(2500) })
await go(p, '/attendance', null, 5000); await shot(p, '10-att')
await shot(p, '11-att-2', async () => { await p.mouse.wheel(0, 850); await p.waitForTimeout(1500) })
await shot(p, '12-kb', async () => { await p.evaluate(() => scrollTo(0, 0)); await p.click('.kb-btn'); await p.waitForSelector('.kb-item'); await p.waitForTimeout(1200) })
await shot(p, '13-ask', async () => { await p.keyboard.press('Escape'); const x = await p.$('.x-btn'); if (x) await x.click().catch(() => {}); await p.waitForTimeout(500)
  await p.click('.ask-fab'); await p.waitForTimeout(800); await p.click('.sug >> nth=0'); await p.waitForSelector('.bubble.bot:not(.typing)', { timeout: 60000 }); await p.waitForTimeout(2500) })
await ctx.close()

// ── staff ESS
ctx = await b.newContext({ ...W, permissions: ['geolocation', 'camera'], geolocation: { latitude: 3.4115, longitude: 101.37, accuracy: 12 } }); p = await login(ctx, 'KS0294')
await shot(p, '20-ess-home')
await shot(p, '21-ess-selfie', async () => { await p.click('.punch-btn'); await p.waitForSelector('.sc video'); await p.waitForTimeout(1800) })
await go(p, '/ess/attendance', '.cal .c', 3000); await shot(p, '22-ess-att')
await shot(p, '23-ess-att-map', async () => { await p.evaluate(() => document.querySelector('.lm-map')?.scrollIntoView({ block: 'center' })); await p.waitForTimeout(2000) })
await go(p, '/ess/leave', '.bal-grid', 2000); await shot(p, '24-ess-leave')
await shot(p, '25-ess-form', async () => { await go(p, '/ess', '.hero', 1500); await p.click('.qa-i >> nth=0'); await p.waitForSelector('.rf'); await p.waitForTimeout(1000) })
await go(p, '/ess/policies', '.pc', 2000); await shot(p, '26-ess-policy')
await shot(p, '27-ess-ask', async () => { await p.click('.pc >> nth=0'); await p.waitForTimeout(800); await p.click('.sugs button >> nth=0'); await p.waitForSelector('.bubble.bot:not(.typing)', { timeout: 60000 }); await p.waitForTimeout(2500) })
await go(p, '/ess/reports', '.kp', 2500); await shot(p, '28-ess-reports')
await go(p, '/ess/memos', null, 2500); await shot(p, '29-ess-memos')
await ctx.close()

// ── HR monitor
ctx = await b.newContext(W); p = await login(ctx, 'KS0197')
await go(p, '/ess/hr', '.lm-map', 4000); await shot(p, '30-hr-map')
await go(p, '/ess/hr?tab=alerts', '.card-a', 2500); await shot(p, '31-hr-alerts')
await shot(p, '32-hr-heat', async () => { await go(p, '/ess/hr', '.lm-map', 1500); await p.click('.hra .bar .ess-tabs >> nth=0 >> button >> nth=1'); await p.waitForSelector('.hm'); await p.waitForTimeout(1500) })
await shot(p, '33-hr-trend', async () => { await p.click('.hra .bar .ess-tabs >> nth=0 >> button >> nth=2'); await p.waitForSelector('.io-chart canvas'); await p.waitForTimeout(2000) })
await go(p, '/ess/hr?tab=manpower', '.tb tbody tr', 2500); await shot(p, '34-hr-manpower')
await go(p, '/ess/hr?tab=geofence', '.off', 3000); await shot(p, '35-hr-geofence')
await go(p, '/ess/approvals', null, 2500); await shot(p, '36-hr-approvals')
await ctx.close()

// ── mobile
const M = { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true, deviceScaleFactor: 2 }
ctx = await b.newContext(M); p = await login(ctx, 'KS0294')
await shot(p, '40-m-home'); await go(p, '/ess/attendance', '.cal .c', 2500); await shot(p, '41-m-att')
await go(p, '/ess/leave', '.bal-grid', 2000); await shot(p, '42-m-leave'); await ctx.close()
ctx = await b.newContext(M); p = await login(ctx, 'admin@kssb.local', '**/map')
await go(p, '/map', '.leaflet-container', 5000); await shot(p, '43-m-map')
await go(p, '/dashboard', '.brief', 4000); await shot(p, '44-m-dash'); await ctx.close()

console.log(errs.length ? errs.join('\n') : 'no page errors'); await b.close()
