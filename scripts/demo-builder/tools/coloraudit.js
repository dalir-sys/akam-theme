// node coloraudit.js <base> <primaryHex> path...
const { chromium } = require('playwright');
(async () => {
  const [base, pri, ...paths] = process.argv.slice(2);
  const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || undefined });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  await ctx.route(u => !/^(localhost|127\.0\.0\.1)$/.test(u.hostname), r => r.abort());
  const agg = {};
  for (const path of paths) {
    const p = await ctx.newPage();
    await p.goto(base + path, { waitUntil: 'networkidle', timeout: 120000 }).catch(() => {});
    const H = await p.evaluate(() => document.documentElement.scrollHeight);
    for (let y = 0; y < H; y += 700) { await p.evaluate(yy => window.scrollTo(0, yy), y); await p.waitForTimeout(80); }
    const res = await p.evaluate((pri) => {
      const hue = (r, g, b) => { r /= 255; g /= 255; b /= 255; const mx = Math.max(r, g, b), mn = Math.min(r, g, b), d = mx - mn; if (!d) return [0, 0, mx]; let h = mx === r ? ((g - b) / d) % 6 : mx === g ? (b - r) / d + 2 : (r - g) / d + 4; h *= 60; if (h < 0) h += 360; const l = (mx + mn) / 2; const s = d / (1 - Math.abs(2 * l - 1)); return [h, s, l]; };
      const ph = hue(parseInt(pri.slice(1, 3), 16), parseInt(pri.slice(3, 5), 16), parseInt(pri.slice(5, 7), 16))[0];
      const out = {};
      document.querySelectorAll('body *').forEach(e => {
        if (e.closest('svg') && e.tagName !== 'svg') return;
        const r = e.getBoundingClientRect(); if (r.width < 2 || r.height < 2) return;
        const cs = getComputedStyle(e); if (cs.visibility === 'hidden' || cs.display === 'none' || +cs.opacity === 0) return;
        for (const prop of ['backgroundColor', 'color', 'borderTopColor', 'fill', 'backgroundImage']) {
          const v = cs[prop]; if (!v || v === 'none') continue;
          if (prop === 'color' && !(e.childNodes.length && [...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim()))) continue;
          if (prop === 'borderTopColor' && parseFloat(cs.borderTopWidth) === 0) continue;
          for (const m of v.matchAll(/rgba?\((\d+), (\d+), (\d+)(?:, ([\d.]+))?\)/g)) {
            if (m[4] !== undefined && +m[4] < 0.25) continue;
            const [h, s, l] = hue(+m[1], +m[2], +m[3]);
            if (s < 0.45 || l < 0.18 || l > 0.9) continue;
            let dh = Math.abs(h - ph); dh = Math.min(dh, 360 - dh);
            if (dh < 22) continue;
            const cls = (e.className && e.className.toString().split(' ').filter(c => c && !c.startsWith('elementor-element-'))[0]) || e.tagName.toLowerCase();
            const k = `rgb(${m[1]},${m[2]},${m[3]}) ${prop} ${cls}`;
            out[k] = (out[k] || 0) + 1;
          }
        }
      });
      return out;
    }, pri);
    for (const [k, n] of Object.entries(res)) { agg[k] = agg[k] || { n: 0, pages: new Set() }; agg[k].n += n; agg[k].pages.add(path); }
    await p.close();
  }
  Object.entries(agg).sort((a, b) => b[1].n - a[1].n).forEach(([k, v]) => console.log(v.n, k, [...v.pages].slice(0, 3).join(' ')));
  await b.close();
})();
