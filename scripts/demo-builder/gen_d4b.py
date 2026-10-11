import sys, os, json
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'lib'))
from kit import ico, page
OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'd4img')
PRI, AMB = '#3b5bfd', '#ff9f1c'
jobs = []
def job(name, w, h, body, css='', scale=2):
    jobs.append(dict(out=f'{OUT}/{name}', w=w, h=h, html=page(body, css), transparent=True, scale=scale))
job('logo-light.png', 220, 72, f'''<div class="l"><div class="m">{ico('folder-filled',34,'tabler',color='#fff')}</div><div class="w"><b>آکام</b><small>فایل</small></div></div>''',
    f'''.l{{display:flex;align-items:center;gap:10px;height:72px;padding:0 4px}}.m{{width:56px;height:56px;border-radius:16px;background:linear-gradient(140deg,{PRI},#7a8dff);display:flex;align-items:center;justify-content:center}}
.w{{display:flex;align-items:baseline;gap:6px}}.w b{{font:900 40px peyda;color:#fff}}.w small{{font:800 22px peyda;color:#8ea0ff}}''')
job('news-art.png', 420, 320, f'''<div class="a">
  <div class="card"><div class="t"><b>کد تخفیف عضویت</b><small>اولین خرید شما</small></div><div class="c">AKAM15</div></div>
  <div class="env">{ico('envelope-with-arrow', 190)}</div><div class="g">{ico('wrapped-gift', 96)}</div><div class="s">{ico('sparkles', 54)}</div><div class="p">{ico('party-popper', 70)}</div></div>''',
    f'''.a{{position:relative;width:420px;height:320px}}
.env{{position:absolute;left:110px;top:40px;filter:drop-shadow(0 24px 30px rgba(11,27,77,.35));transform:rotate(-6deg)}}
.g{{position:absolute;left:24px;top:170px;filter:drop-shadow(0 16px 20px rgba(11,27,77,.3));transform:rotate(-10deg)}}
.s{{position:absolute;right:40px;top:20px}}.p{{position:absolute;right:20px;top:170px;transform:rotate(10deg);filter:drop-shadow(0 14px 16px rgba(11,27,77,.3))}}
.card{{position:absolute;left:150px;bottom:6px;display:flex;align-items:center;gap:12px;background:#fff;border-radius:16px;padding:10px 14px;box-shadow:0 18px 40px -14px rgba(11,27,77,.45);direction:rtl}}
.t b{{display:block;font:800 13px peyda;color:#0b1b4d}}.t small{{font:500 11px dana;color:#64748b}}.c{{font:900 15px/1 monospace;letter-spacing:1px;color:{PRI};border:2px dashed {PRI};border-radius:10px;padding:7px 10px;background:#eef2ff}}''')
json.dump(jobs, open(OUT + '/jobs-b.json', 'w'), ensure_ascii=False)
print(len(jobs))
