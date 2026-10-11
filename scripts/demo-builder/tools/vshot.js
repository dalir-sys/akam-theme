const { chromium } = require('playwright');
(async () => { const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || undefined });
 const [out, ...urls] = process.argv.slice(2);
 for (const [i, u] of urls.entries()) for (const [tag, vw, vh, mob] of [['d', 1440, 900, false], ['m', 390, 844, true]]) {
   const ctx = await b.newContext({ viewport: { width: vw, height: vh }, deviceScaleFactor: 1, isMobile: mob, hasTouch: mob });
   await ctx.route(/google|gravatar|s\.w\.org/, r => r.abort());
   const p = await ctx.newPage(); await p.goto(u, { waitUntil: 'networkidle', timeout: 120000 }).catch(() => {});
   await p.waitForTimeout(1500); try { await p.addStyleTag({ content: '.webmz-ai-chat,.webmz-floating-contact,[class*=floating-contact]{display:none!important}' }); } catch (e) {}
   await p.waitForTimeout(1500); await p.screenshot({ path: `${out}/site${i + 1}-${tag}.png` }); await ctx.close(); }
 await b.close(); })();
