"""Generate demo CSS that maps hard-coded accent colours in theme widget styles to the theme palette variables."""
import re, glob, os
P, L, H = 'var(--webmz-color-primary)', 'var(--webmz-color-primary-light)', 'var(--webmz-color-primary-hover)'
ALWAYS = {  # replaced even as var() fallbacks (their variables default to red)
    r'#(?:f21e3f|e53935|d32f2f|e03131|d22022|dc2626|ef4444|c62828|b71c1c|ff3b30|e11d48|f03e3e|fa5252|c92a2a)\b': P,
}
PLAIN = {  # replaced only when used directly (not as a var() fallback)
    r'#(?:0878f9|f97316|ea580c|ff7812|fd7e14|ff9800|ff8a00|00cb82|ec4899|db2777|9a3412|7c3aed|5e4485|472f6a)\b': P,
    r'#(?:0662cc|0565d6|c2410c)\b': H,
    r'#(?:ffebee|fdecea|fff5f5|ffe3e3|ffcdd2|ffe5e5|fee2e2|fef2f2|ffc9c9|fed7aa|ffedd5|fff7ed|fff4e7|e6f1ff|e7f1ff|eaf3ff|fce7f3|f0e5ff)\b': L,
    r'#f81918\b': '#94a3b8',
}
SKIP = re.compile(r'error|logout|is-over|not-installed|unknown|child-theme|danger|delete|remove|:root|invalid|warning|ticket-status|status--|star|rating|success|whatsapp|telegram|instagram|youtube|aparat|ai-chat', re.I)

def rules(text, wrap=None):
    i = 0
    while True:
        m = re.search(r'([^{}]+)\{', text[i:])
        if not m: return
        sel = m.group(1).strip(); start = i + m.end(); depth = 1; j = start
        while depth and j < len(text):
            depth += {'{': 1, '}': -1}.get(text[j], 0); j += 1
        body = text[start:j - 1]
        if sel.startswith(('@media', '@supports')): yield from rules(body, sel)
        elif not sel.startswith('@'): yield wrap, sel, body
        i = j

out = []
for f in sorted(glob.glob(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'akam', 'assets', 'css', '*.css'))):
    css = re.sub(r'/\*.*?\*/', '', open(f).read(), flags=re.S)
    for wrap, sel, body in rules(css):
        if SKIP.search(sel) or 'body.' in sel or 'wp-admin' in sel: continue
        decls = []
        for d in body.split(';'):
            if ':' not in d: continue
            prop, val = (x.strip() for x in d.split(':', 1))
            if prop.startswith('--webmz') or not val: continue
            new = val
            for pat, rep in ALWAYS.items(): new = re.sub(pat, rep, new, flags=re.I)
            if 'var(' not in val:
                for pat, rep in PLAIN.items(): new = re.sub(pat, rep, new, flags=re.I)
                new = re.sub(r'rgba\(\s*(?:8,\s*120,\s*249|249,\s*115,\s*22|236,\s*72,\s*153)\s*,\s*([\d.]+)\s*\)',
                             lambda m: f"color-mix(in srgb,var(--webmz-color-primary) {round(float(m.group(1)) * 100)}%,transparent)", new)
            if new != val:
                decls.append(f"{prop}:{new.replace('!important', '').strip()} !important")
        grey = [d for d in decls if '#94a3b8' in d]
        if grey:
            decls = [d for d in decls if d not in grey]
            del_sels = [x.strip() for x in sel.split(',') if 'del' in x]
            if del_sels:
                r = f"{','.join(del_sels)}{{{';'.join(grey)}}}"
                out.append(f"{wrap}{{{r}}}" if wrap else r)
        if decls:
            rule = f"{sel}{{{';'.join(decls)}}}"
            out.append(f"{wrap}{{{rule}}}" if wrap else rule)
out.append(':root{--webmz-red:var(--webmz-color-primary)}')
out.append('.webmz-stories__ring{background:linear-gradient(45deg,var(--webmz-color-primary),color-mix(in srgb,var(--webmz-color-primary) 45%,#fff),var(--webmz-color-primary-hover))!important}')
print('/* Demo palette: map hard-coded accent colours in widget styles to the theme primary colour. */')
print('\n'.join(out))
