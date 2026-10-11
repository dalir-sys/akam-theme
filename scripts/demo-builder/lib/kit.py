"""Shared helpers for generated demo images."""
import json, os, html as _h
ROOT = os.path.dirname(os.path.abspath(__file__))
BUILDER = os.path.dirname(ROOT)
FONTS = os.path.normpath(os.path.join(BUILDER, '..', '..', 'akam', 'assets', 'fonts'))
SCR = next((d for d in (BUILDER, os.path.dirname(BUILDER)) if os.path.isdir(os.path.join(d, 'node_modules', '@iconify-json'))), BUILDER)
_sets = {}

def _set(name):
    if name not in _sets:
        _sets[name] = json.load(open(f'{SCR}/node_modules/@iconify-json/{name}/icons.json'))
    return _sets[name]

def ico(name, size=64, iset='fluent-emoji', color=None, style=''):
    d = _set(iset)
    n = d['aliases'][name]['parent'] if name not in d['icons'] and name in d.get('aliases', {}) else name
    i = d['icons'][n]
    wv, hv = i.get('width', d.get('width', 16)), i.get('height', d.get('height', 16))
    body = i['body']
    if color: body = body.replace('currentColor', color)
    return f'<svg xmlns="http://www.w3.org/2000/svg" width="{size}" height="{size}" viewBox="0 0 {wv} {hv}" style="{style}">{body}</svg>'

def esc(s):
    return _h.escape(s)

def page(body, css='', bg='transparent'):
    fonts = ''.join(f'<link rel="stylesheet" href="file://{FONTS}/{f}/font.css">' for f in ('peyda', 'dana', 'kalameh', 'farhang', 'anjoman', 'modam'))
    return f'''<!doctype html><html dir="rtl" lang="fa"><head><meta charset="utf-8">{fonts}<style>
*{{box-sizing:border-box;margin:0;padding:0}}
html,body{{width:100%;height:100%;background:{bg};overflow:hidden}}
body{{font-family:peyda,dana,sans-serif;color:#0f172a;-webkit-font-smoothing:antialiased}}
{css}</style></head><body>{body}</body></html>'''
