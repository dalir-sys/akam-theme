// node render.js jobs.json  — jobs: [{out, w, h, html, scale?, transparent?}]
const { chromium } = require('playwright');
const fs = require('fs');
(async () => {
  const jobs = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
  const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || undefined });
  const groups = {};
  for (const j of jobs) { const k = `${j.w}x${j.h}x${j.scale || 2}`; (groups[k] = groups[k] || []).push(j); }
  for (const k of Object.keys(groups)) {
    const [w, h, s] = k.split('x').map(Number);
    const ctx = await b.newContext({ viewport: { width: w, height: h }, deviceScaleFactor: s });
    const p = await ctx.newPage();
    for (const j of groups[k]) {
      const f = j.out.replace(/\.[a-z]+$/, '.html');
      fs.writeFileSync(f, j.html);
      await p.goto('file://' + f, { waitUntil: 'load' });
      await p.evaluate(() => document.fonts.ready);
      await p.waitForTimeout(80);
      await p.screenshot({ path: j.out, omitBackground: !!j.transparent, type: j.out.endsWith('.jpg') ? 'jpeg' : 'png', ...(j.out.endsWith('.jpg') ? { quality: 88 } : {}) });
      fs.unlinkSync(f);
    }
    await ctx.close();
  }
  await b.close();
  console.log('rendered', jobs.length);
})();
