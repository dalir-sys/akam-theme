// node shoot.js <base> <outdir> <prefix> <width> path1 path2 ...   → stitched full-page PNGs (via PIL)
const { chromium } = require('playwright');
const { execFileSync } = require('child_process');
const fs = require('fs');
(async () => {
  const [base, outdir, prefix, width, ...paths] = process.argv.slice(2);
  const W = +width, mob = W < 600, VH = mob ? 844 : 900;
  const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || undefined });
  const ctx = await b.newContext({ viewport: { width: W, height: VH }, deviceScaleFactor: mob ? 2 : 1, isMobile: mob, hasTouch: mob });
  await ctx.route(u => !/^(localhost|127\.0\.0\.1)$/.test(u.hostname), r => r.abort());
  for (const path of paths) {
    if (path.startsWith('!')) { const q = await ctx.newPage(); await q.goto(base + path.slice(1), { waitUntil: 'networkidle', timeout: 180000 }).catch(() => {}); await q.close(); console.log('visited', path); continue; }
    const p = await ctx.newPage();
    const errs = []; p.on('pageerror', e => errs.push(e.message));
    const r = await p.goto(base + path, { waitUntil: 'networkidle', timeout: 180000 }).catch(() => null);
    await p.evaluate(() => document.querySelectorAll('img[loading="lazy"]').forEach(i => { i.loading = 'eager'; }));
    await p.evaluate(() => document.querySelectorAll('body *').forEach(e => { if (getComputedStyle(e).position === 'sticky') e.style.setProperty('position', 'relative', 'important'); }));
    let H = await p.evaluate(() => document.documentElement.scrollHeight);
    for (let y = 0; y < H; y += 450) { await p.evaluate(yy => window.scrollTo(0, yy), y); await p.waitForTimeout(110); }
    await p.evaluate(() => Promise.race([Promise.all([...document.images].filter(i => !i.complete).map(i => new Promise(r => { i.onload = i.onerror = r; }))), new Promise(r => setTimeout(r, 20000))]));
    await p.evaluate(() => window.scrollTo(0, 0)); await p.waitForTimeout(1200);
    H = await p.evaluate(() => document.documentElement.scrollHeight);
    const name = (process.env.NAMES && JSON.parse(process.env.NAMES)[path]) || (path.replace(/[^a-z0-9]+/gi, '_').replace(/^_|_$/g, '') || 'home');
    const frames = [];
    for (let y = 0, i = 0; y < H; y += VH, i++) {
      const top = Math.min(y, H - VH);
      await p.evaluate(yy => window.scrollTo(0, yy), top);
      await p.waitForTimeout(500);
      if (i === 1) await p.addStyleTag({ content: '.akd-hide-fixed{visibility:hidden!important;opacity:0!important}.webmz-sticky-element.webmz-is-sticky,.webmz-sticky-element[class*=is-fixed]{visibility:hidden!important}' });
      if (i >= 1) await p.evaluate(() => document.querySelectorAll('body *').forEach(e => { const cs = getComputedStyle(e); if ((cs.position === 'fixed' || cs.position === 'sticky') && e.getBoundingClientRect().height < 500) e.classList.add('akd-hide-fixed'); }));
      await p.waitForTimeout(250);
      const f = `${outdir}/.f-${name}-${i}.png`;
      await p.screenshot({ path: f });
      frames.push(`${f}:${top}`);
    }
    const out = `${outdir}/${prefix}-${name}.png`;
    execFileSync('python3', ['-I', __dirname + '/stitch.py', out, String(H), String(mob ? 2 : 1), ...frames]);
    frames.forEach(f => fs.unlinkSync(f.split(':')[0]));
    console.log(path, r ? r.status() : 'ERR', 'h=' + H, errs.length ? 'JSERR ' + errs.slice(0, 2).join(' | ').slice(0, 300) : '');
    await p.close();
  }
  await b.close();
})();
