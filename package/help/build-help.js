// node build-help.js [out.pdf] — renders help.html to an A4 PDF (needs playwright, e.g. NODE_PATH=scripts/demo-builder/node_modules).
const path = require('path');
const { chromium } = require('playwright');
(async () => {
  const out = path.resolve(process.argv[2] || path.join(__dirname, 'help.pdf'));
  const b = await chromium.launch({ executablePath: process.env.CHROME_PATH || undefined });
  const p = await b.newPage();
  await p.goto('file://' + path.join(__dirname, 'help.html'), { waitUntil: 'load' });
  await p.evaluate(() => document.fonts.ready);
  await p.pdf({ path: out, format: 'A4', printBackground: true, preferCSSPageSize: true,
    displayHeaderFooter: true, headerTemplate: '<span></span>',
    footerTemplate: '<div style="width:100%;font-size:8px;color:#94a3b8;text-align:center;font-family:sans-serif"><span class="pageNumber"></span> / <span class="totalPages"></span></div>' });
  await b.close();
  console.log('help written to', out);
})();
