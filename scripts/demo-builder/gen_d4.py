import sys, json, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'lib'))
from kit import ico, esc, page
OUT = os.path.join(os.path.dirname(__file__), 'd4img')
SRC = 'file://' + OUT + '/src'
PRI, NAVY, AMB = '#3b5bfd', '#0b1b4d', '#ff9f1c'
jobs = []
def job(name, w, h, body, css='', bg='transparent', transparent=False, scale=2):
    jobs.append(dict(out=f'{OUT}/{name}', w=w, h=h, html=page(body, css, bg), transparent=transparent, scale=scale))

CHIP = '.chip{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:999px;background:rgba(255,255,255,.16);color:#fff;font:600 14px dana;backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.25)}'
def chips(items):
    return '<div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-start">' + ''.join(f'<span class="chip">{esc(i)}</span>' for i in items) + '</div>'

def bg(c1, c2):
    return f'background:radial-gradient(circle at 15% 20%, {c2} 0, transparent 45%),radial-gradient(circle at 90% 90%, rgba(255,255,255,.18) 0, transparent 40%),linear-gradient(135deg,{c1},{c2});'

# ---------- product covers ----------
def theme_cover(name, slug, title, sub, site, c1, c2, tags):
    body = f'''<div class="wrap" style="{bg(c1,c2)}">
  <div class="dots"></div>
  <div class="txt"><div class="kicker">قالب وردپرس</div><h1>{esc(title)}</h1><p>{esc(sub)}</p>{chips(tags)}</div>
  <div class="dev">
    <div class="browser"><div class="bar"><i></i><i></i><i></i><span></span></div><img src="{SRC}/{site}-d.png"></div>
    <div class="phone"><img src="{SRC}/{site}-m.png"></div>
  </div></div>'''
    css = CHIP + '''.wrap{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.dots{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.18) 1.2px,transparent 1.2px);background-size:22px 22px;mask-image:linear-gradient(90deg,transparent,#000 60%)}
.txt{position:absolute;right:44px;top:70px;width:330px}
.kicker{display:inline-block;font:700 14px dana;background:#fff;color:#0f172a;padding:5px 12px;border-radius:8px;margin-bottom:16px}
h1{font:900 40px/1.35 peyda;margin-bottom:10px;text-shadow:0 2px 20px rgba(0,0,0,.15)}
p{font:500 16px/1.8 dana;opacity:.92;margin-bottom:20px}
.dev{position:absolute;left:-40px;top:64px;width:470px;transform:perspective(1400px) rotateY(16deg) rotateX(4deg);transform-origin:left center}
.browser{border-radius:12px;overflow:hidden;background:#fff;box-shadow:0 40px 80px -20px rgba(0,0,0,.45)}
.bar{height:26px;background:#eef1f7;display:flex;align-items:center;gap:6px;padding:0 10px;direction:ltr}.bar i{width:9px;height:9px;border-radius:50%;background:#f87171}.bar i:nth-child(2){background:#fbbf24}.bar i:nth-child(3){background:#34d399}.bar span{flex:1;height:12px;margin-left:12px;border-radius:6px;background:#dde3ee}
.browser img{display:block;width:100%}
.phone{position:absolute;left:380px;top:120px;width:118px;padding:6px;border-radius:22px;background:#0f172a;box-shadow:0 30px 60px -10px rgba(0,0,0,.5)}
.phone img{display:block;width:100%;border-radius:16px;height:220px;object-fit:cover;object-position:top}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def plugin_cover(slug, title, sub, emoji, c1, c2, tags):
    body = f'''<div class="wrap" style="{bg(c1,c2)}">
  <div class="ring r1"></div><div class="ring r2"></div>
  <div class="tile">{ico(emoji, 190)}</div>
  <div class="txt"><div class="kicker">{ico('brand-wordpress',18,'tabler',color='currentColor')} افزونه وردپرس</div><h1>{esc(title)}</h1><p>{esc(sub)}</p>{chips(tags)}</div></div>'''
    css = CHIP + '''.wrap{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.ring{position:absolute;border-radius:50%;border:2px solid rgba(255,255,255,.18)}.r1{width:420px;height:420px;left:20px;top:25px}.r2{width:300px;height:300px;left:80px;top:85px;border-style:dashed}
.tile{position:absolute;left:110px;top:115px;width:240px;height:240px;border-radius:56px;background:linear-gradient(145deg,rgba(255,255,255,.4),rgba(255,255,255,.08));border:1px solid rgba(255,255,255,.45);box-shadow:0 40px 70px -20px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.6);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px)}
.txt{position:absolute;right:48px;top:96px;width:360px}
.kicker{display:inline-flex;align-items:center;gap:6px;font:700 14px dana;background:#fff;color:#0f172a;padding:5px 12px;border-radius:8px;margin-bottom:16px}
h1{font:900 42px/1.3 peyda;margin-bottom:10px}p{font:500 16px/1.8 dana;opacity:.92;margin-bottom:20px}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def ebook_cover(slug, title, sub, emoji, c1, c2, bookc1, bookc2, author):
    body = f'''<div class="wrap" style="{bg(c1,c2)}">
  <div class="book"><div class="spine"></div><div class="front" style="background:linear-gradient(160deg,{bookc1},{bookc2})">
     <div class="em">{ico(emoji, 96)}</div><h2>{esc(title)}</h2><small>{esc(author)}</small></div></div>
  <div class="shadow"></div>
  <div class="txt"><div class="kicker">کتاب الکترونیک · PDF</div><h1>{esc(title)}</h1><p>{esc(sub)}</p>{chips(['نسخه PDF و EPUB','به‌روزرسانی رایگان'])}</div></div>'''
    css = CHIP + '''.wrap{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.book{position:absolute;left:120px;top:48px;width:230px;height:330px;transform:perspective(1200px) rotateY(24deg) rotateZ(-2deg);transform-style:preserve-3d}
.front{position:absolute;inset:0;border-radius:6px 14px 14px 6px;padding:28px 22px;display:flex;flex-direction:column;align-items:center;text-align:center;box-shadow:inset 6px 0 12px rgba(0,0,0,.25), 0 30px 60px -10px rgba(0,0,0,.5)}
.spine{position:absolute;right:100%;top:4px;bottom:4px;width:26px;background:linear-gradient(90deg,rgba(0,0,0,.45),rgba(0,0,0,.15));transform-origin:right;transform:rotateY(-80deg)}
.em{margin:18px 0 22px}.front h2{font:900 25px/1.5 peyda;color:#fff}.front small{margin-top:auto;font:600 13px dana;opacity:.85}
.shadow{position:absolute;left:110px;top:372px;width:280px;height:30px;border-radius:50%;background:rgba(0,0,0,.28);filter:blur(14px)}
.txt{position:absolute;right:48px;top:96px;width:360px}
.kicker{display:inline-block;font:700 14px dana;background:#fff;color:#0f172a;padding:5px 12px;border-radius:8px;margin-bottom:16px}
h1{font:900 36px/1.4 peyda;margin-bottom:10px}p{font:500 16px/1.8 dana;opacity:.92;margin-bottom:20px}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def font_cover(slug, word, family, weight, sub, c1, c2):
    body = f'''<div class="wrap" style="background:{c1}">
  <div class="big" style="font-family:{family};font-weight:{weight}">{esc(word)}</div>
  <div class="row"><span>ابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی</span></div>
  <div class="nums" style="font-family:{family}">۰۱۲۳۴۵۶۷۸۹</div>
  <div class="tag" style="background:{c2}">فونت فارسی · {esc(sub)}</div>
  <div class="weights">{''.join(f'<b style="font-family:{family};font-weight:{w}">آ</b>' for w in (200,300,400,500,700,900))}</div></div>'''
    css = f'''.wrap{{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}}
.big{{position:absolute;right:50px;top:30px;font-size:190px;line-height:1.25;letter-spacing:-2px}}
.row{{position:absolute;right:56px;bottom:96px;left:56px;font:400 26px {family};opacity:.75;white-space:nowrap;overflow:hidden}}
.nums{{position:absolute;left:56px;top:70px;font-size:34px;opacity:.85;letter-spacing:6px}}
.tag{{position:absolute;right:56px;bottom:36px;padding:8px 16px;border-radius:10px;font:700 15px dana;color:#111}}
.weights{{position:absolute;left:50px;bottom:30px;display:flex;gap:14px;font-size:40px;opacity:.9}}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def icons_cover(slug, title, sub, emojis, c1, c2):
    grid = ''.join(f'<div class="t">{ico(e, 62)}</div>' for e in emojis)
    body = f'''<div class="wrap" style="{bg(c1,c2)}"><div class="grid">{grid}</div>
    <div class="txt"><div class="kicker">فایل گرافیکی</div><h1>{esc(title)}</h1><p>{esc(sub)}</p>{chips(['PNG','SVG','Blender','Figma'])}</div></div>'''
    css = CHIP + '''.wrap{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.grid{position:absolute;left:40px;top:40px;width:400px;display:grid;grid-template-columns:repeat(4,1fr);gap:14px;transform:perspective(1200px) rotateY(18deg) rotateX(8deg)}
.t{height:88px;border-radius:22px;background:rgba(255,255,255,.9);display:flex;align-items:center;justify-content:center;box-shadow:0 14px 30px -12px rgba(0,0,0,.35)}
.txt{position:absolute;right:48px;top:100px;width:330px}
.kicker{display:inline-block;font:700 14px dana;background:#fff;color:#0f172a;padding:5px 12px;border-radius:8px;margin-bottom:16px}
h1{font:900 38px/1.4 peyda;margin-bottom:10px}p{font:500 16px/1.8 dana;opacity:.92;margin-bottom:20px}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def uikit_cover(slug, title, sub, c1, c2, accent):
    def phone(i):
        cards = ''.join(f'<div class="card"><div class="im" style="background:linear-gradient(135deg,{accent},{c2})">{ico(e,34)}</div><div class="ln"><b></b><i></i></div><span>{p}</span></div>' for e, p in [('t-shirt','۲۴۹'),('running-shoe','۸۹۰'),('handbag','۵۴۰'),('watch','۱٬۲۰۰')][i:i+3])
        return f'''<div class="ph"><div class="top"><b>سبدک</b>{ico('shopping-cart',22)}</div><div class="hero" style="background:linear-gradient(135deg,{c2},{accent})">تخفیف ۴۰٪<small>کالکشن جدید پاییزه</small></div>{cards}<div class="btn" style="background:{accent}">افزودن به سبد</div></div>'''
    body = f'''<div class="wrap" style="{bg(c1,c2)}"><div class="phones">{phone(0)}{phone(1)}</div>
    <div class="txt"><div class="kicker">{ico('brand-figma',16,'tabler',color='currentColor')} UI Kit · Figma</div><h1>{esc(title)}</h1><p>{esc(sub)}</p>{chips(['+۸۰ صفحه','حالت تاریک','کامپوننت‌محور'])}</div></div>'''
    css = CHIP + '''.wrap{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.phones{position:absolute;left:40px;top:40px;display:flex;gap:22px;transform:rotate(-8deg)}
.ph{width:180px;height:390px;border-radius:30px;background:#fff;padding:14px 12px;border:6px solid #0f172a;box-shadow:0 30px 60px -12px rgba(0,0,0,.5);color:#0f172a;font-family:dana}
.ph:nth-child(2){margin-top:60px}
.top{display:flex;justify-content:space-between;align-items:center;font:900 16px peyda;margin-bottom:10px}
.hero{border-radius:14px;color:#fff;padding:12px;font:900 18px peyda;margin-bottom:10px}.hero small{display:block;font:500 10px dana;opacity:.9}
.card{display:flex;align-items:center;gap:8px;padding:7px;border-radius:12px;background:#f3f5fb;margin-bottom:7px}
.im{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center}
.ln{flex:1}.ln b{display:block;height:7px;width:70%;background:#cbd5e1;border-radius:4px;margin-bottom:6px}.ln i{display:block;height:6px;width:45%;background:#e2e8f0;border-radius:4px}
.card span{font:800 11px dana}
.btn{margin-top:6px;border-radius:10px;color:#fff;text-align:center;padding:8px;font:700 12px dana}
.txt{position:absolute;right:48px;top:96px;width:340px}
.kicker{display:inline-flex;gap:6px;align-items:center;font:700 14px dana;background:#fff;color:#0f172a;padding:5px 12px;border-radius:8px;margin-bottom:16px}
h1{font:900 38px/1.4 peyda;margin-bottom:10px}p{font:500 16px/1.8 dana;opacity:.92;margin-bottom:20px}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def mockup_cover(slug, title, sub, c1, c2):
    body = f'''<div class="wrap" style="{bg(c1,c2)}">
    <div class="scene">
      <div class="paper"><div class="lg">{ico('gem-stone',30)} <b>نگار</b></div><i></i><i></i><i></i><i class="s"></i></div>
      <div class="card c1"><div class="lg">{ico('gem-stone',26)}<b>نگار</b></div><small>استودیو طراحی</small></div>
      <div class="card c2"><b>سارا نیک‌نام</b><small>مدیر هنری</small><small>۰۲۱-۸۸۰۰۰۰۰۰</small></div>
      <div class="cup"><div class="lg">{ico('gem-stone',30)}</div></div>
      <div class="bag"><div class="h"></div><div class="lg">{ico('gem-stone',44)}<b>نگار</b></div></div>
    </div>
    <div class="txt"><div class="kicker">موکاپ · PSD</div><h1>{esc(title)}</h1><p>{esc(sub)}</p>{chips(['لایه‌باز','Smart Object','4K'])}</div></div>'''
    css = CHIP + '''.wrap{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.scene{position:absolute;left:30px;top:20px;width:450px;height:430px}
.lg{display:flex;align-items:center;gap:6px;font:900 18px peyda;color:#1e293b}
.paper{position:absolute;left:30px;top:40px;width:200px;height:270px;background:#fff;border-radius:6px;transform:rotate(-10deg);box-shadow:0 25px 50px -15px rgba(0,0,0,.45);padding:20px}
.paper i{display:block;height:7px;background:#e2e8f0;border-radius:4px;margin-top:14px}.paper i.s{width:50%}
.card{position:absolute;width:190px;height:110px;border-radius:10px;padding:16px;box-shadow:0 20px 40px -12px rgba(0,0,0,.45)}
.c1{left:180px;top:250px;background:#111827;color:#fff;transform:rotate(8deg)}.c1 .lg{color:#fff}.c1 small{display:block;margin-top:24px;font:500 12px dana;opacity:.7}
.c2{left:210px;top:300px;background:#fff;color:#0f172a;transform:rotate(-4deg);font-family:dana}.c2 b{display:block;font:800 15px peyda}.c2 small{display:block;font:500 11px dana;color:#64748b;margin-top:4px}
.cup{position:absolute;left:250px;top:40px;width:100px;height:120px;border-radius:8px 8px 22px 22px;background:linear-gradient(90deg,#e2e8f0,#fff 45%,#cbd5e1);display:flex;align-items:center;justify-content:center;box-shadow:0 20px 40px -12px rgba(0,0,0,.4)}
.bag{position:absolute;left:340px;top:110px;width:140px;height:170px;background:linear-gradient(90deg,#fde68a,#fef3c7 50%,#fcd34d);border-radius:6px;display:flex;align-items:center;justify-content:center;box-shadow:0 25px 50px -12px rgba(0,0,0,.45)}
.bag .h{position:absolute;top:-30px;left:40px;width:60px;height:40px;border:5px solid #b45309;border-bottom:0;border-radius:30px 30px 0 0}.bag .lg{flex-direction:column}
.txt{position:absolute;right:48px;top:96px;width:330px}
.kicker{display:inline-block;font:700 14px dana;background:#fff;color:#0f172a;padding:5px 12px;border-radius:8px;margin-bottom:16px}
h1{font:900 36px/1.4 peyda;margin-bottom:10px}p{font:500 16px/1.8 dana;opacity:.92;margin-bottom:20px}'''
    job(f'cover-{slug}.jpg', 836, 470, body, css)

def thumb(slug, emoji, c1, c2):
    body = f'<div class="t" style="background:linear-gradient(140deg,{c1},{c2})">{ico(emoji, 96)}</div>'
    css = '.t{width:160px;height:160px;border-radius:36px;display:flex;align-items:center;justify-content:center;box-shadow:inset 0 2px 0 rgba(255,255,255,.35)}'
    job(f'thumb-{slug}.png', 160, 160, body, css, transparent=True)

# catalogue (slug, kind, args, thumb emoji, colors)
P = json.load(open(os.path.join(os.path.dirname(__file__), 'd4_products.json')))
for p in P:
    k, a = p['kind'], p['cover']
    if k == 'theme': theme_cover(p['title'], p['slug'], a['title'], a['sub'], a['site'], p['c1'], p['c2'], a['tags'])
    elif k == 'plugin': plugin_cover(p['slug'], a['title'], a['sub'], p['emoji'], p['c1'], p['c2'], a['tags'])
    elif k == 'ebook': ebook_cover(p['slug'], a['title'], a['sub'], p['emoji'], p['c1'], p['c2'], a['b1'], a['b2'], a['author'])
    elif k == 'font': font_cover(p['slug'], a['word'], a['family'], a['weight'], a['sub'], p['c1'], p['c2'])
    elif k == 'icons': icons_cover(p['slug'], a['title'], a['sub'], a['emojis'], p['c1'], p['c2'])
    elif k == 'uikit': uikit_cover(p['slug'], a['title'], a['sub'], p['c1'], p['c2'], a['accent'])
    elif k == 'mockup': mockup_cover(p['slug'], a['title'], a['sub'], p['c1'], p['c2'])
    thumb(p['slug'], p['emoji'], p['c1'], p['c2'])

# ---------- category icons (transparent) ----------
for slug, e in [('cat-themes','laptop'),('cat-plugins','electric-plug'),('cat-graphics','artist-palette'),('cat-ebooks','books'),('cat-fonts','fountain-pen'),('why-1','money-bag'),('why-2','headphone'),('why-3','high-voltage'),('why-4','shield'),('dl-1','package'),('dl-2','locked-with-key'),('dl-3','rocket'),('dl-4','glowing-star'),('dl-5','trophy'),('dl-6','wrapped-gift')]:
    job(f'{slug}.png', 200, 200, f'<div style="width:200px;height:200px;display:flex;align-items:center;justify-content:center">{ico(e,170)}</div>', transparent=True)

# ---------- banners ----------
def banner(slug, title, sub, emoji, c1, c2, cta):
    body = f'''<div class="b" style="{bg(c1,c2)}"><div class="e">{ico(emoji,150)}</div><div class="t"><h3>{esc(title)}</h3><p>{esc(sub)}</p><span>{esc(cta)} ←</span></div></div>'''
    css = '''.b{position:relative;width:560px;height:300px;border-radius:0;overflow:hidden;color:#fff}
.e{position:absolute;left:28px;top:60px;filter:drop-shadow(0 20px 25px rgba(0,0,0,.3));transform:rotate(-8deg)}
.t{position:absolute;right:34px;top:54px;width:300px}h3{font:900 34px/1.35 peyda;margin-bottom:10px}p{font:500 15px/1.8 dana;opacity:.92;margin-bottom:18px}
span{display:inline-block;background:#fff;color:#0f172a;font:800 14px dana;padding:9px 18px;border-radius:12px}'''
    job(f'banner-{slug}.jpg', 560, 300, body, css)
banner('sale', 'جشنواره پاییزه آکام فایل', 'تا ۵۰٪ تخفیف روی قالب‌ها و افزونه‌های پرفروش', 'fire', '#ff6b2c', '#ff9f1c', 'مشاهده تخفیف‌ها')
banner('themes', 'قالب‌های آموزشی و فروشگاهی', 'سایت خود را در کمتر از یک روز راه‌اندازی کنید', 'laptop', '#3b5bfd', '#6c8cff', 'مشاهده قالب‌ها')
banner('fonts', 'فونت‌های فارسی اورجینال', 'با لایسنس تجاری و پشتیبانی کامل از اعداد فارسی', 'fountain-pen', '#0f9f8f', '#38c9b4', 'مشاهده فونت‌ها')
banner('ebooks', 'کتاب‌های الکترونیک کسب‌وکار', 'از راه‌اندازی فروشگاه تا سئو، قدم‌به‌قدم', 'books', '#8b3dff', '#c084fc', 'مشاهده کتاب‌ها')

# ---------- blog covers ----------
B = json.load(open(os.path.join(os.path.dirname(__file__), 'd4_posts.json')))
for b in B:
    body = f'''<div class="w" style="{bg(b['c1'],b['c2'])}"><div class="grid"></div><div class="e">{ico(b['emoji'],170)}</div>
    <div class="t"><span>مقاله آموزشی</span><h2>{esc(b['cover'])}</h2><small>akamfile.ir</small></div></div>'''
    css = '''.w{position:relative;width:836px;height:470px;overflow:hidden;color:#fff}
.grid{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.08) 1px,transparent 1px);background-size:40px 40px}
.e{position:absolute;left:80px;top:130px;filter:drop-shadow(0 25px 30px rgba(0,0,0,.3))}
.t{position:absolute;right:60px;top:110px;width:430px}.t span{display:inline-block;background:#fff;color:#0f172a;font:800 14px dana;padding:6px 14px;border-radius:9px;margin-bottom:16px}
h2{font:900 40px/1.45 peyda}small{display:block;margin-top:18px;font:600 14px dana;opacity:.75;direction:ltr;text-align:right}'''
    job(f"post-{b['slug']}.jpg", 836, 470, body, css)

# ---------- stories ----------
for i, (title, sub, emoji, c1, c2) in enumerate([
    ('تخفیف هفته', '۵۰٪ روی قالب نوا', 'fire', '#ff6b2c', '#ff9f1c'),
    ('محصول جدید', 'افزونه پیامکس ۲', 'sparkles', '#3b5bfd', '#7c8cff'),
    ('آموزش نصب', 'نصب قالب در ۵ دقیقه', 'laptop', '#0f9f8f', '#38c9b4'),
    ('فونت رایگان', 'فونت نسیم لایت', 'fountain-pen', '#8b3dff', '#c084fc'),
    ('کد تخفیف', 'AKAMFILE20', 'admission-tickets', '#e11d48', '#fb7185')], 1):
    body = f'''<div class="s" style="{bg(c1,c2)}"><div class="e">{ico(emoji,200)}</div><h2>{esc(title)}</h2><p>{esc(sub)}</p><span>آکام فایل</span></div>'''
    css = '''.s{position:relative;width:420px;height:746px;overflow:hidden;color:#fff;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center}
.e{filter:drop-shadow(0 25px 30px rgba(0,0,0,.3));margin-bottom:30px}h2{font:900 48px peyda}p{font:600 22px dana;opacity:.92;margin-top:10px;direction:rtl}span{position:absolute;bottom:40px;font:800 16px peyda;background:rgba(255,255,255,.2);padding:8px 16px;border-radius:12px}'''
    job(f'story-{i}.jpg', 420, 746, body, css)

# ---------- logo ----------
logo_body = f'''<div class="l"><div class="m">{ico('folder-filled',34,'tabler',color='#fff')}</div><div class="w"><b>آکام</b><small>فایل</small></div></div>'''
logo_css = f'''.l{{display:flex;align-items:center;gap:10px;height:72px;padding:0 4px}}.m{{width:56px;height:56px;border-radius:16px;background:linear-gradient(140deg,{PRI},#7a8dff);display:flex;align-items:center;justify-content:center;box-shadow:0 10px 20px -8px {PRI}}}
.w{{display:flex;align-items:baseline;gap:6px}}.w b{{font:900 40px peyda;color:#0b1b4d}}.w small{{font:800 22px peyda;color:{PRI}}}'''
job('logo.png', 220, 72, logo_body, logo_css, transparent=True)
job('favicon.png', 64, 64, f'<div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(140deg,{PRI},#7a8dff);display:flex;align-items:center;justify-content:center">{ico("folder-filled",38,"tabler",color="#fff")}</div>', transparent=True, scale=4)

# ---------- hero collage ----------
hero = f'''<div class="h">
  <div class="blob"></div>
  <div class="browser"><div class="bar"><i></i><i></i><i></i></div><img src="{SRC}/site1-d.png"></div>
  <div class="card dl"><div class="ic">{ico('package',40)}</div><div><b>دانلود فایل</b><small>قالب نوا · ۱۲.۴ مگابایت</small><div class="pb"><i></i></div></div></div>
  <div class="card st"><small>فروش این ماه</small><b>+۲٬۴۸۰</b><div class="bars">{''.join(f'<i style="height:{h}px"></i>' for h in (18,30,24,40,34,52,46))}</div></div>
  <div class="card rt">{ico('glowing-star',28)}<div><b>۴٫۹ از ۵</b><small>رضایت خریداران</small></div></div>
  <div class="fl f1">{ico('electric-plug',64)}</div><div class="fl f2">{ico('artist-palette',58)}</div><div class="fl f3">{ico('books',56)}</div>
</div>'''
hero_css = f'''.h{{position:relative;width:640px;height:520px}}
.blob{{position:absolute;left:60px;top:40px;width:520px;height:440px;border-radius:44% 56% 52% 48%/48% 42% 58% 52%;background:linear-gradient(140deg,#dfe6ff,#fff1dc)}}
.browser{{position:absolute;left:70px;top:110px;width:470px;border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 40px 80px -24px rgba(15,23,42,.35);transform:perspective(1400px) rotateY(-10deg) rotateX(4deg)}}
.bar{{height:26px;background:#eef1f7;display:flex;gap:6px;align-items:center;padding:0 10px;direction:ltr}}.bar i{{width:9px;height:9px;border-radius:50%;background:#f87171}}.bar i:nth-child(2){{background:#fbbf24}}.bar i:nth-child(3){{background:#34d399}}
.browser img{{display:block;width:100%}}
.card{{position:absolute;background:#fff;border-radius:18px;padding:14px 16px;box-shadow:0 24px 50px -18px rgba(15,23,42,.35);font-family:dana;color:#0f172a}}
.card b{{display:block;font:800 16px peyda}}.card small{{display:block;font:500 12px dana;color:#64748b}}
.dl{{right:10px;top:70px;display:flex;gap:12px;align-items:center;width:250px}}.ic{{width:56px;height:56px;border-radius:14px;background:#eef2ff;display:flex;align-items:center;justify-content:center}}
.pb{{height:7px;border-radius:5px;background:#e2e8f0;margin-top:8px;width:150px}}.pb i{{display:block;height:100%;width:72%;border-radius:5px;background:linear-gradient(90deg,{PRI},#7a8dff)}}
.st{{left:0;bottom:40px;width:200px}}.st b{{font-size:24px;color:{PRI};margin:4px 0 8px}}.bars{{display:flex;gap:6px;align-items:flex-end;height:52px}}.bars i{{flex:1;border-radius:4px;background:linear-gradient({PRI},#9fb0ff)}}
.rt{{right:30px;bottom:60px;display:flex;gap:10px;align-items:center}}
.fl{{position:absolute;filter:drop-shadow(0 18px 20px rgba(15,23,42,.25))}}.f1{{left:20px;top:40px;transform:rotate(-14deg)}}.f2{{right:210px;top:6px;transform:rotate(10deg)}}.f3{{right:0;bottom:170px;transform:rotate(12deg)}}'''
job('hero.png', 640, 520, hero, hero_css, transparent=True)

# ---------- developer CTA art ----------
job('dev-cta.png', 260, 200, f'''<div class="c"><div class="bar"><i></i><i></i><i></i></div><pre><b>&lt;?php</b>
<span>add_action</span>( <em>'init'</em>,
  <span>function</span>() {{
    <em>// سلام آکام!</em>
  }} );</pre><div class="b">{ico('money-with-wings',54)}</div></div>''',
 '''.c{position:relative;width:240px;height:170px;margin:15px 10px;border-radius:16px;background:#0b1b4d;box-shadow:0 20px 40px -14px rgba(11,27,77,.6);direction:ltr;padding:12px 14px;color:#cbd5e1}
.bar{display:flex;gap:5px;margin-bottom:10px}.bar i{width:8px;height:8px;border-radius:50%;background:#f87171}.bar i:nth-child(2){background:#fbbf24}.bar i:nth-child(3){background:#34d399}
pre{font:500 12px/1.7 monospace}pre b{color:#f472b6}pre span{color:#7dd3fc}pre em{color:#fcd34d;font-style:normal}
.b{position:absolute;right:-6px;bottom:-14px;transform:rotate(12deg)}''', transparent=True)

json.dump(jobs, open(os.path.join(OUT, 'jobs.json'), 'w'), ensure_ascii=False)
print(len(jobs), 'jobs')

# ---------- menu svg icons (written directly) ----------
import kit
for slug, name in [('themes','layout-dashboard'),('plugins','plug'),('graphics','palette'),('ebooks','book'),('fonts','typography')]:
    svg = kit.ico(name, 24, 'tabler', color='#81879E').replace('stroke-width="2"', 'stroke-width="1.7"')
    open(f'{OUT}/menu-{slug}.svg', 'w').write('<?xml version="1.0" encoding="UTF-8"?>\n' + svg)
