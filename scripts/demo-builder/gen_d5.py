import sys, json, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'lib'))
from kit import ico, esc, page
import kit
ND = os.path.dirname(os.path.abspath(__file__))
OUT = ND + '/d5img'; os.makedirs(OUT, exist_ok=True)
# Uploads of the demo 5 site (a copy of demo 3): people photos and the counselling photo used in the hero.
UPLOADS = os.environ.get('DEMO5_UPLOADS', '/home/user/akam-demo-5/site/wp-content/uploads')
UP = 'file://' + UPLOADS + '/2026/07'
GAL = 'file://' + UPLOADS + '/2026/06'
PRI, NAVY, ACC = '#0d9488', '#12324a', '#f4845f'
D = json.load(open(ND + '/d5_data.json'))
T = {t['slug']: t for t in D['teachers']}
jobs = []
def job(name, w, h, body, css='', transparent=False, scale=2):
    jobs.append(dict(out=f'{OUT}/{name}', w=w, h=h, html=page(body, css), transparent=transparent, scale=scale))
def bg(c1, c2):
    return f'background:radial-gradient(circle at 12% 18%, rgba(255,255,255,.35) 0, transparent 38%),radial-gradient(circle at 88% 92%, rgba(255,255,255,.22) 0, transparent 40%),linear-gradient(135deg,{c1},{c2});'
LV = {'beginner': 'مقدماتی', 'intermediate': 'متوسط', 'advanced': 'پیشرفته'}
FA = str.maketrans('0123456789', '۰۱۲۳۴۵۶۷۸۹')

for c in D['courses']:
    t = T[c['teacher']]
    body = f'''<div class="w" style="{bg(c['c1'], c['c2'])}"><div class="dots"></div><div class="blob"></div>
    <div class="ph"><img src="{UP}/{t['photo']}"></div><div class="em">{ico(c['emoji'], 92)}</div>
    <div class="tx"><span class="k">دوره ویدیویی · {str(c['sessions']).translate(FA)} جلسه · {LV[c['level']]}</span><h1>{esc(c['title'])}</h1><p>{esc(c['sub'])}</p>
    <div class="t"><img src="{UP}/{t['photo']}"><b>{esc(t['name'])}</b><small>{esc(t['job'])}</small></div></div></div>'''
    css = '''.w{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.22) 1.3px,transparent 1.3px);background-size:22px 22px;mask-image:linear-gradient(90deg,#000,transparent 55%)}
.blob{position:absolute;left:40px;top:70px;width:330px;height:330px;border-radius:46% 54% 50% 50%/52% 46% 54% 48%;background:rgba(255,255,255,.22)}
.ph{position:absolute;left:62px;top:88px;width:290px;height:290px;border-radius:50%;overflow:hidden;border:8px solid rgba(255,255,255,.9);box-shadow:0 30px 60px -20px rgba(0,0,0,.35)}
.ph img{width:100%;height:100%;object-fit:cover;object-position:50% 22%}
.em{position:absolute;left:290px;top:70px;width:120px;height:120px;border-radius:34px;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 20px 40px -14px rgba(0,0,0,.35);transform:rotate(8deg)}
.tx{position:absolute;right:46px;top:70px;width:370px}
.k{display:inline-block;font:700 13px dana;background:rgba(255,255,255,.95);color:#12324a;padding:6px 12px;border-radius:9px;margin-bottom:16px}
h1{font:900 37px/1.45 peyda;margin-bottom:10px;text-shadow:0 2px 18px rgba(0,0,0,.12)}p{font:500 16px/1.8 dana;opacity:.95;margin-bottom:22px}
.t{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.35);border-radius:16px;padding:8px 12px;width:max-content}
.t img{width:42px;height:42px;border-radius:50%;object-fit:cover;object-position:50% 20%;border:2px solid #fff}.t b{font:800 15px peyda}.t small{font:500 12px dana;opacity:.9;margin-right:6px}'''
    job(f"cover-{c['slug']}.jpg", 836, 470, body, css)

# category cards (portrait)
for slug, title, f in D['cats']:
    meta = {'mental-health': ('leaf-fluttering-in-wind', '#0d9488', '#5eead4', 'اضطراب، افسردگی، خواب'), 'relationships': ('people-hugging', '#7c3aed', '#c4b5fd', 'زوج‌درمانی و ارتباط مؤثر'),
            'child-psychology': ('teddy-bear', '#f59e0b', '#fde68a', 'فرزندپروری و نوجوانی'), 'personal-growth': ('seedling', '#ea580c', '#fdba74', 'عزت نفس و هوش هیجانی')}[slug]
    body = f'<div class="w" style="{bg(meta[1], meta[2])}"><div class="r r1"></div><div class="r r2"></div><div class="e">{ico(meta[0], 230)}</div></div>'
    css = '''.w{position:relative;width:543px;height:724px;overflow:hidden}.r{position:absolute;border-radius:50%;border:2px solid rgba(255,255,255,.35)}.r1{width:520px;height:520px;left:12px;top:40px}.r2{width:380px;height:380px;left:82px;top:110px;border-style:dashed}
.e{position:absolute;left:150px;top:180px;filter:drop-shadow(0 30px 30px rgba(0,0,0,.25))}'''
    job(f'{f}.jpg', 543, 724, body, css)

for b in D['posts']:
    body = f'''<div class="w" style="{bg(b['c1'], b['c2'])}"><div class="blob"></div><div class="e">{ico(b['emoji'], 180)}</div>
    <div class="t"><span>مقاله روانشناسی</span><h2>{esc(b['cover'])}</h2><small>آکام ذهن</small></div></div>'''
    css = '''.w{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}.blob{position:absolute;left:40px;top:70px;width:320px;height:320px;border-radius:46% 54% 50% 50%/52% 46% 54% 48%;background:rgba(255,255,255,.25)}
.e{position:absolute;left:110px;top:140px;filter:drop-shadow(0 25px 30px rgba(0,0,0,.25))}
.t{position:absolute;right:60px;top:120px;width:420px}.t span{display:inline-block;background:#fff;color:#12324a;font:800 14px dana;padding:6px 14px;border-radius:9px;margin-bottom:16px}
h2{font:900 42px/1.45 peyda}small{display:block;margin-top:18px;font:700 15px peyda;opacity:.85}'''
    job(f"post-{b['slug']}.jpg", 836, 470, body, css)

for i, (title, sub, emoji, c1, c2) in enumerate([
    ('درباره ما', 'آکادمی روانشناسی آکام ذهن', 'brain', '#0d9488', '#5eead4'), ('مشاوره رایگان', 'اولین جلسه ۱۵ دقیقه‌ای', 'speech-balloon', '#7c3aed', '#c4b5fd'),
    ('تست شخصیت', 'خودت را بهتر بشناس', 'thinking-face', '#f59e0b', '#fde68a'), ('کارگاه آنلاین', 'مدیریت استرس · پنجشنبه', 'calendar', '#0284c7', '#7dd3fc'),
    ('نظرات مراجعان', '+۱۲٬۰۰۰ مراجع راضی', 'sparkling-heart', '#e11d48', '#fda4af')], 1):
    body = f'<div class="s" style="{bg(c1, c2)}"><div class="e">{ico(emoji, 200)}</div><h2>{esc(title)}</h2><p>{esc(sub)}</p><span>آکام ذهن</span></div>'
    css = '''.s{position:relative;width:420px;height:746px;overflow:hidden;color:#fff;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center}
.e{filter:drop-shadow(0 25px 30px rgba(0,0,0,.25));margin-bottom:30px}h2{font:900 46px peyda}p{font:600 21px dana;opacity:.95;margin-top:10px}span{position:absolute;bottom:40px;font:800 16px peyda;background:rgba(255,255,255,.22);padding:8px 16px;border-radius:12px}'''
    job(f'story-{i}.jpg', 420, 746, body, css)

lg = f'<div class="m">{ico("brain",36,"tabler",color="#fff")}</div>'
job('logo.png', 210, 72, f'<div class="l">{lg}<div class="w"><b>آکام</b><small>ذهن</small></div></div>',
    f'''.l{{display:flex;align-items:center;gap:10px;height:72px;padding:0 4px}}.m{{width:56px;height:56px;border-radius:18px;background:linear-gradient(140deg,{PRI},#2dd4bf);display:flex;align-items:center;justify-content:center;box-shadow:0 10px 20px -8px {PRI}}}
.w{{display:flex;align-items:baseline;gap:6px}}.w b{{font:900 40px peyda;color:{NAVY}}}.w small{{font:800 22px peyda;color:{PRI}}}''', transparent=True)
job('logo-light.png', 210, 72, f'<div class="l">{lg}<div class="w"><b>آکام</b><small>ذهن</small></div></div>',
    f'''.l{{display:flex;align-items:center;gap:10px;height:72px;padding:0 4px}}.m{{width:56px;height:56px;border-radius:18px;background:linear-gradient(140deg,{PRI},#2dd4bf);display:flex;align-items:center;justify-content:center}}
.w{{display:flex;align-items:baseline;gap:6px}}.w b{{font:900 40px peyda;color:#fff}}.w small{{font:800 22px peyda;color:#5eead4}}''', transparent=True)
job('favicon.png', 64, 64, f'<div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(140deg,{PRI},#2dd4bf);display:flex;align-items:center;justify-content:center">{ico("brain",40,"tabler",color="#fff")}</div>', transparent=True, scale=4)

hero = f'''<div class="h"><div class="ring"></div><div class="blob"><img src="{GAL}/blog-skill-career-growth-1.jpg"></div>
  <div class="card c1"><div class="i">{ico('calendar',34)}</div><div><b>جلسه مشاوره آنلاین</b><small>امروز · ساعت ۱۸:۰۰</small></div></div>
  <div class="card c2">{ico('sparkling-heart',30)}<div><b>۴٫۹ از ۵</b><small>رضایت مراجعان</small></div></div>
  <div class="card c3"><div class="av">{''.join(f'<img src="{UP}/{p}">' for p in ('female-3.png','person-3.png','female-5.png'))}</div><div><b>+۲۰ روانشناس</b><small>دارای پروانه نظام</small></div></div>
  <div class="fl f1">{ico('brain',70)}</div><div class="fl f2">{ico('lotus',62)}</div><div class="fl f3">{ico('herb',54)}</div></div>'''
hero_css = f'''.h{{position:relative;width:640px;height:560px}}
.ring{{position:absolute;left:70px;top:30px;width:500px;height:500px;border-radius:50%;border:2px dashed #99e0d6}}
.blob{{position:absolute;left:105px;top:65px;width:430px;height:430px;border-radius:44% 56% 52% 48%/48% 42% 58% 52%;overflow:hidden;box-shadow:0 40px 80px -30px rgba(13,148,136,.55);border:10px solid #fff}}
.blob img{{width:100%;height:100%;object-fit:cover;object-position:50% 40%}}
.card{{position:absolute;background:#fff;border-radius:18px;padding:12px 16px;box-shadow:0 24px 50px -18px rgba(18,50,74,.35);display:flex;gap:10px;align-items:center;color:{NAVY}}}
.card b{{display:block;font:800 15px peyda}}.card small{{display:block;font:500 12px dana;color:#64748b}}
.c1{{right:0;top:90px}}.c1 .i{{width:50px;height:50px;border-radius:14px;background:#e0f5f1;display:flex;align-items:center;justify-content:center}}
.c2{{left:10px;top:250px}}.c3{{right:30px;bottom:40px}}
.av{{display:flex}}.av img{{width:36px;height:36px;border-radius:50%;object-fit:cover;object-position:50% 20%;border:2px solid #fff;margin-left:-10px}}
.fl{{position:absolute;filter:drop-shadow(0 16px 18px rgba(18,50,74,.22))}}.f1{{left:40px;top:40px;transform:rotate(-10deg)}}.f2{{left:70px;bottom:60px;transform:rotate(8deg)}}.f3{{right:60px;top:0;transform:rotate(14deg)}}'''
job('hero.png', 640, 560, hero, hero_css, transparent=True)

job('footer-art.png', 360, 220, f'''<div class="f"><div class="e">{ico('lotus',110)}</div><b>مشاوره آنلاین از هر نقطه ایران</b><small>تماس تصویری · تلفنی · چت</small></div>''',
    f'''.f{{width:360px;height:220px;border-radius:24px;background:linear-gradient(135deg,#e0f5f1,#fff4ee);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:{NAVY}}}
.e{{filter:drop-shadow(0 12px 14px rgba(13,148,136,.25))}}b{{font:800 18px peyda}}small{{font:500 13px dana;color:#64748b}}''', transparent=True)

# monochrome svg icons for icon boxes / menus
for slug, name, color in [('ib-license', 'certificate', PRI), ('ib-private', 'shield-lock', PRI), ('ib-online', 'device-laptop', PRI), ('ib-forever', 'infinity', PRI),
                          ('id-psych', 'users', PRI), ('id-years', 'award', PRI), ('id-clients', 'mood-smile', PRI)]:
    svg = kit.ico(name, 24, 'tabler', color=color).replace('stroke-width="2"', 'stroke-width="1.8"')
    open(f'{OUT}/{slug}.svg', 'w').write('<?xml version="1.0" encoding="UTF-8"?>\n' + svg)

json.dump(jobs, open(OUT + '/jobs.json', 'w'), ensure_ascii=False)
print(len(jobs), 'jobs')
