import sys, os, json, re
ND = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, ND + '/lib')
from el import w, c, url, img, icon, pad, dump
M = json.load(open(ND + '/d5_map.json'))
D = json.load(open(ND + '/d5_data.json'))
BASE = 'http://localhost:8085'
OUT = ND + '/d5out'; os.makedirs(OUT, exist_ok=True)
I = lambda f: img(*M['img'][f])
SVG = lambda f: {'value': {'url': M['img'][f][1], 'id': M['img'][f][0]}, 'library': 'svg'}
cat_url = lambda s: url(f'{BASE}/product-category/{s}/')
COLORS = {'#D22022': '#0D9488', '#d22022': '#0d9488', '#AE1E1E': '#0F766E', '#ae1e1e': '#0f766e', '#FFE5E5': '#E0F5F1', '#ffe5e5': '#e0f5f1', '#FF8A8A': '#5EEAD4'}

def load(pid):
    s = open(f'{ND}/base/d5-{pid}.json').read()
    for a, b in COLORS.items(): s = s.replace(a, b)
    return json.loads(s)
def walk(els, fn):
    for e in els:
        fn(e); walk(e.get('elements', []), fn)
def by_id(els, fn_map):
    def f(e):
        if e.get('id') in fn_map: fn_map[e['id']](e.setdefault('settings', {}) if isinstance(e.get('settings'), dict) else e.__setitem__('settings', {}) or e['settings'])
    walk(els, f)

BLURB = '<p>آکام ذهن، آکادمی آنلاین روانشناسی است که با همراهی روانشناسان دارای پروانه نظام روانشناسی، دوره‌های کاربردی سلامت روان، روابط و فرزندپروری را به زبانی ساده در اختیار شما قرار می‌دهد.</p>'

# ------------------------------------------------------------------ HOME
home = load(103)
def drop(els, ids):
    out = []
    for e in els:
        if e.get('id') in ids: continue
        e['elements'] = drop(e.get('elements', []), ids); out.append(e)
    return out
home = drop(home, {'2ed794c'})
testis = [('t-1.png', 'نگار محمدی', 'دوره مدیریت اضطراب', 'بعد از سال‌ها اضطراب، بالاخره تکنیک‌هایی یاد گرفتم که واقعاً جواب می‌دهند. حملات اضطرابم خیلی کمتر شده.'),
          ('t-4.png', 'محمد کریمی', 'دوره فرزندپروری مثبت', 'رابطه‌ام با پسرم کاملاً عوض شد. دیگر خبری از فریاد و دعوا نیست و او هم آرام‌تر شده.'),
          ('t-3.png', 'علی رضایی', 'دوره هوش هیجانی', 'در محیط کار خیلی بهتر احساساتم را مدیریت می‌کنم؛ همکارانم هم متوجه تغییر شدند.'),
          ('t-2.png', 'مریم حسینی', 'دوره خواب بهتر', 'برنامه شش هفته‌ای را کامل انجام دادم و بعد از مدت‌ها، شب‌ها راحت می‌خوابم.'),
          ('t-1.png', 'سمیرا نادری', 'دوره ذهن‌آگاهی', 'تمرین‌های ده دقیقه‌ای را هر صبح انجام می‌دهم؛ روزم با آرامش شروع می‌شود.'),
          ('t-3.png', 'حامد صالحی', 'دوره مهارت‌های ارتباطی', 'من و همسرم با هم دوره را دیدیم و یاد گرفتیم بدون دعوا درباره مشکلاتمان حرف بزنیم.')]
icon_boxes = [('ib-license.svg', 'روانشناسان دارای پروانه', 'همه مدرسان عضو سازمان نظام روانشناسی هستند'), ('ib-private.svg', 'محرمانه و امن', 'اطلاعات و جلسات شما کاملاً محرمانه می‌ماند'),
              ('ib-online.svg', 'آموزش و مشاوره آنلاین', 'از هر نقطه ایران، در هر زمان که بخواهید'), ('ib-forever.svg', 'دسترسی همیشگی', 'جلسات دوره همیشه در دسترس شما هستند')]
ib = iter(icon_boxes)
def ibox(s):
    f, t, d = next(ib); s.update(icon=SVG(f), title=t, description=d)
details = [('id-psych.svg', '+۲۰', 'روانشناس متخصص'), ('id-years.svg', '+۱۲ سال', 'میانگین سابقه'), ('id-clients.svg', '+۱۲٬۰۰۰', 'مراجع راضی')]
cats3 = [('mental-health', 'سلامت روان', 'اضطراب، افسردگی و خواب', '۴ دوره'), ('relationships', 'روابط و خانواده', 'زوج‌درمانی و ارتباط مؤثر', '۱ دوره'), ('child-psychology', 'کودک و نوجوان', 'فرزندپروری و نوجوانی', '۲ دوره')]
fixes = {
    'fdd54ad': lambda s: s.update(story_box_id=str(M['story_box'])),
    'c4d98a1': lambda s: s.update(title='ذهنی آرام‌تر،<br>\nزندگی‌ای روشن‌تر'),
    '4e107bb': lambda s: s.update(editor='<p>در آکام ذهن، با دوره‌های علمی و کاربردی روانشناسی، اضطراب را مدیریت کنید، روابط سالم‌تری بسازید و به خودتان نزدیک‌تر شوید؛ همراه با بهترین روانشناسان کشور.</p>'),
    '84b9858': lambda s: s.update(button_text='مشاوره رایگان', button_link=url(BASE + '/contact-us/')),
    '049d2ea': lambda s: s.update(button_text='مشاهده دوره‌ها', button_link=url(BASE + '/shop/')),
    '5c85fb5': lambda s: [l.update(light_title='نور سبزآبی', light_color='#5EEAD4') for l in s.get('lights', [])],
    '4bcd783': lambda s: s.update(image=I('hero.png')),
    '6c2f3c3': ibox, '1e1a83a': ibox, '663b76a': ibox, 'a0e5883': ibox,
    '66e8495': lambda s: s.update(subtitle='دسته‌بندی دوره‌ها', title='مسیر رشد خود را انتخاب کنید'),
    '91aa8cf': lambda s: s.update(editor='<p>از مدیریت استرس و اضطراب تا فرزندپروری و روابط زناشویی؛ دوره‌ها بر اساس نیازهای واقعی زندگی دسته‌بندی شده‌اند.</p>'),
    '3b4091c': lambda s: s.update(button_text='مشاهده همه دوره‌ها', button_link=url(BASE + '/shop/')),
    '36a8861': lambda s: s.update(items=[{'_id': f'k{i}', 'background_image': I({'mental-health': 'cat-mental.jpg', 'relationships': 'cat-family.jpg', 'child-psychology': 'cat-child.jpg'}[sl]),
                                           'title': t, 'subtitle': st, 'level': lv, 'button_text': 'مشاهده دوره‌ها', 'link': cat_url(sl)} for i, (sl, t, st, lv) in enumerate(cats3)]),
    '4e90244': lambda s: s.update(category=[str(x) for x in M['blog_cats']], playlist_title='کارگاه رایگان: آرامش در ۷ قدم', posts_per_page=7),
    '3f2eaa9': lambda s: s.update(title='دوره‌های محبوب', description='پرطرفدارترین دوره‌های روانشناسی آکام ذهن', button_text='مشاهده همه دوره‌ها', button_link=url(BASE + '/shop/')),
    '87fcbf9': lambda s: s.update(title='مقالات روانشناسی', description='جدیدترین مقالات و نکات کاربردی سلامت روان', button_text='مشاهده همه مقالات', button_link=url(BASE + '/blog/')),
    '0ac99f5': lambda s: s.update(icon_image=I('gmail.png'), subtitle='جدیدترین مقاله‌ها، کارگاه‌ها و تخفیف‌ها را از دست ندهید.'),
    '1fdc85a': lambda s: s.update(icon_start=I('3d-book.png'), icon_end=I('headphone-smile.png'), title='سوال روانشناسی دارید؟', subtitle_prefix='درخواست', subtitle_highlight='مشاوره رایگان', subtitle_suffix='خود را ثبت کنید.', button_text='ثبت درخواست', button_url=url(BASE + '/contact-us/')),
    'd195b3a': lambda s: s.update(subtitle='تیم متخصص', title='روانشناسان آکام ذهن'),
    '1db23b0': lambda s: s.update(editor='<p>دوره‌ها توسط روانشناسان بالینی، زوج‌درمانگران و متخصصان کودک با سال‌ها تجربه درمانی تدریس می‌شوند.</p>'),
    '226b3c3': lambda s: s.update(items=[{'_id': f'd{i}', 'icon': SVG(f), 'title': t, 'subtitle': st} for i, (f, t, st) in enumerate(details)]),
    'c05ec20': lambda s: s.update(button_text='مشاهده همه روانشناسان', button_link=url(BASE + '/mentors/')),
    '87d6b1a': lambda s: s.update(subtitle='نظرات مراجعان', title='تجربه شرکت‌کنندگان دوره‌ها'),
    'e06280e': lambda s: s.update(testimonials=[{'_id': f'q{i}', 'avatar': I(a), 'name': n, 'job_title': j, 'rating': 5, 'content': t} for i, (a, n, j, t) in enumerate(testis)]),
}
by_id(home, fixes)
pricing = c(w('webmz-membership-pricing', columns='3', columns_tablet='2', columns_mobile='1', subtitle='مشاوره فردی', title='بسته‌ای مناسب برای شروع تغییر', description='جلسات آنلاین با روانشناس دارای پروانه؛ زمان جلسه را خودتان انتخاب می‌کنید و هر زمان بخواهید می‌توانید بسته را تغییر دهید.',
                plans=[{'_id': 'p1', 'plan_name': 'جلسه تکی', 'plan_description': 'برای آشنایی و ارزیابی اولیه', 'price': '۴۵۰٬۰۰۰', 'price_suffix': 'تومان / جلسه ۵۰ دقیقه‌ای', 'button_text': 'رزرو جلسه', 'button_link': url(BASE + '/contact-us/'),
                        'features': 'جلسه آنلاین تصویری\nارزیابی اولیه و برنامه درمان\nپیگیری کتبی پس از جلسه'},
                       {'_id': 'p2', 'plan_name': 'بسته ۴ جلسه‌ای', 'plan_description': 'پرطرفدارترین انتخاب مراجعان', 'price': '۱٬۵۹۰٬۰۰۰', 'price_suffix': 'تومان / یک ماه', 'button_text': 'خرید بسته', 'button_link': url(BASE + '/contact-us/'),
                        'is_featured': 'yes', 'badge_text': 'محبوب‌ترین', 'features': '۴ جلسه آنلاین با روانشناس ثابت\nدسترسی رایگان به یک دوره\nپشتیبانی پیام‌رسان بین جلسات\nتمرین‌های شخصی‌سازی‌شده'},
                       {'_id': 'p3', 'plan_name': 'بسته ۸ جلسه‌ای', 'plan_description': 'برای تغییرات عمیق و پایدار', 'price': '۲٬۹۹۰٬۰۰۰', 'price_suffix': 'تومان / دو ماه', 'button_text': 'خرید بسته', 'button_link': url(BASE + '/contact-us/'),
                        'features': '۸ جلسه آنلاین با روانشناس ثابت\nدسترسی رایگان به دو دوره\nپشتیبانی پیام‌رسان بین جلسات\nگزارش پیشرفت ماهانه'}]),
            padding=pad(30, 0, 30, 0))
for pl in pricing['elements'][0]['settings']['plans']:
    pl['features'] = [{'_id': f"{pl['_id']}f{j}", 'text': t} for j, t in enumerate(pl['features'].split('\n'))]
home.insert(len(home) - 1, pricing)
dump(home, OUT + '/home.json')

# ------------------------------------------------------------------ HEADER / FOOTER
h = load(15)
def fh(e):
    s = e.get('settings') if isinstance(e.get('settings'), dict) else {}
    t = e.get('widgetType')
    if t == 'webmz-site-logo': s['logo_image'] = I('logo.png'); s.setdefault('logo_width', {'unit': 'px', 'size': 150, 'sizes': []}); s['logo_width'] = {'unit': 'px', 'size': 150, 'sizes': []}; s['logo_width_mobile'] = {'unit': 'px', 'size': 118, 'sizes': []}
    if t == 'webmz-mobile-menu': s['caption'] = 'ارتباط با آکام ذهن'; s['logo_image'] = I('logo.png')
walk(h, fh); dump(h, OUT + '/header.json')

f = load(565)
def ff(e):
    s = e.get('settings') if isinstance(e.get('settings'), dict) else {}
    t = e.get('widgetType')
    if t == 'webmz-site-logo': s['logo_image'] = I('logo.png')
    if t == 'text-editor': s['editor'] = BLURB
    if t == 'image': s['image'] = I('footer-art.png')
    if t == 'webmz-footer-links':
        if s.get('title') == 'دوره‌ها':
            s['links'] = [{'_id': f'l{i}', 'link_title': n, 'link_url': cat_url(sl)} for i, (sl, n, _) in enumerate(D['cats'])] + [{'_id': 'la', 'link_title': 'همه دوره‌ها', 'link_url': url(BASE + '/shop/')}]
        elif s.get('title') == 'تدریس پلاس':
            s['title'] = 'آکام ذهن'
            for l in s['links']:
                if l['link_title'] == 'اساتید': l['link_title'] = 'روانشناسان'
        elif s.get('title') == 'پشتیبانی':
            for l in s['links']:
                if l['link_title'].strip() == 'مشاوره': l['link_title'] = 'رزرو مشاوره'
    if t == 'heading' and 'حقوق' in s.get('title', ''): s['title'] = '<b>آکام ذهن</b>. تمامی حقوق محفوظ است.'
walk(f, ff); dump(f, OUT + '/footer.json')

# ------------------------------------------------------------------ MEGA MENU
mm = load(1185)
icons_svg = [m['icon'] for m in mm[0]['elements'][0]['settings']['main_items']]
main_items, sub_items = [], []
for i, (sl, t, _) in enumerate(D['cats']):
    key = f'item-{i + 1}'
    main_items.append({'_id': f'm{i}', 'item_key': key, 'title': t, 'link': cat_url(sl), 'icon': icons_svg[i % len(icons_svg)]})
    for cr in [x for x in D['courses'] if x['cat'] == sl]:
        sub_items.append({'_id': 's' + cr['slug'][:6] + str(i), 'parent_key': key, 'title': cr['title'], 'link': url(f"{BASE}/product/{cr['slug']}/")})
mm[0]['elements'][0]['settings']['main_items'] = main_items
mm[0]['elements'][0]['settings']['submenu_items'] = sub_items
dump(mm, OUT + '/mega.json')

# ------------------------------------------------------------------ LAYOUTS
sp = load(976)
def fsp(e):
    s = e.get('settings') if isinstance(e.get('settings'), dict) else {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام ذهن'
    if t == 'webmz-spw-purchase-box':
        for it, txt in zip(s.get('bottom_items', []), ['دسترسی همیشگی به جلسات', 'کاربرگ‌ها و تمرین‌های قابل دانلود', 'گواهی پایان دوره آکام ذهن']): it['text'] = txt
        s['extra_button_mode'] = 'product'
    if t == 'webmz-spw-course-features': s['source'] = 'product'
    if t == 'webmz-spw-instructor-box': s.update(job_title='مدرس دوره', profile_button='مشاهده پروفایل روانشناس')
walk(sp, fsp); dump(sp, OUT + '/single-product.json')

side = iter(['cover-anxiety-stress-management.jpg', 'cover-mindfulness-meditation.jpg'])
side_links = iter(['/product/anxiety-stress-management/', '/product/mindfulness-meditation/'])
po = load(632)
def fpo(e):
    s = e.get('settings') if isinstance(e.get('settings'), dict) else {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام ذهن'
    if t == 'image':
        nxt = next(side, None)
        if nxt: s['image'] = I(nxt); s['link_to'] = 'custom'; s['link'] = url(BASE + next(side_links))
    if t == 'webmz-post-download-box': s['header_subtitle'] = 'کاربرگ‌ها و فایل‌های ضمیمه این مقاله'
walk(po, fpo); dump(po, OUT + '/single-post.json')

ab = load(37)
team_pos = ['مدیر آموزش', 'روانشناس بالینی', 'مدیر محتوا', 'کارشناس پشتیبانی', 'مشاور خانواده', 'مدیر بازاریابی', 'هماهنگ‌کننده جلسات', 'طراح محتوای آموزشی']
def fab(e):
    s = e.get('settings') if isinstance(e.get('settings'), dict) else {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام ذهن'
    if t == 'webmz-page-hero':
        s.update(eyebrow='آکادمی آنلاین روانشناسی', title='با آکام ذهن <strong>آشنا</strong> شوید',
                 description='ما باور داریم مراقبت از سلامت روان باید ساده، در دسترس و بدون قضاوت باشد. آکام ذهن با همراهی روانشناسان متخصص، آموزش و مشاوره را به خانه شما می‌آورد.',
                 primary_button_text='مشاهده دوره‌ها', primary_button_link=url(BASE + '/shop/'), secondary_button_text='رزرو مشاوره', secondary_button_link=url(BASE + '/contact-us/'))
    if t == 'heading' and 'درباره' in s.get('title', ''): s['title'] = 'داستان آکام ذهن'
    if t == 'text-editor':
        s['editor'] = ('<p>آکام ذهن در سال ۱۳۹۹ توسط گروهی از روانشناسان بالینی و مشاوران خانواده راه‌اندازی شد تا آموزش‌های علمی روانشناسی را از کلینیک‌ها و کتاب‌های تخصصی بیرون بیاورد و به زبانی ساده در اختیار همه قرار دهد.</p>'
                       '<p>امروز بیش از ۱۲ هزار نفر در دوره‌ها و جلسات مشاوره آنلاین ما شرکت کرده‌اند. هر دوره زیر نظر کمیته علمی آکام ذهن طراحی و بازبینی می‌شود و همه مدرسان، عضو سازمان نظام روانشناسی و مشاوره هستند.</p>')
    if t == 'webmz-team-list':
        for m, p in zip(s.get('members', []), team_pos): m['position'] = p
    if t == 'webmz-jobs-callout': s['description'] = 'اگر روانشناس هستید و به آموزش علاقه دارید، به خانواده آکام ذهن بپیوندید.'
walk(ab, fab); dump(ab, OUT + '/about.json')

ct = load(1309)
offices = [('دفتر مرکزی تهران', 'تهران، خیابان شریعتی، ساختمان سلامت، طبقه ۴'), ('مشاوره آنلاین', 'جلسات تصویری، تلفنی و چت؛ هر روز از ۹ تا ۲۱'), ('شعبه شیراز', 'شیراز، بلوار چمران، مجتمع پزشکی آرامش')]
def fct(e):
    s = e.get('settings') if isinstance(e.get('settings'), dict) else {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام ذهن'
    if t == 'webmz-page-hero':
        s.update(eyebrow='همیشه کنارتان هستیم', title='با آکام ذهن در <strong>تماس</strong> باشید',
                 description='برای رزرو جلسه مشاوره، راهنمایی در انتخاب دوره یا هر سوال دیگری با ما در ارتباط باشید. اولین جلسه ۱۵ دقیقه‌ای مشاوره رایگان است.')
    if t == 'webmz-contact-tabs':
        for o, (ti, ad) in zip(s.get('offices', []), offices): o['title'] = ti; o['address'] = ad
    if t == 'webmz-ajax-contact-form': s['description'] = 'برای رزرو مشاوره رایگان یا دریافت راهنمایی، اطلاعات تماس خود را وارد کنید؛ کارشناسان ما با شما تماس می‌گیرند.'
walk(ct, fct); dump(ct, OUT + '/contact.json')

dump([c(w('webmz-teachers-archive', title_text='روانشناسان و مدرسان آکام ذهن'), padding=pad(20, 0, 40, 0))], OUT + '/teacher-archive.json')

# archives keep their structure; only recolour
for pid, name in ((1115, 'blog-archive'), (1153, 'shop-archive')):
    dump(load(pid), f'{OUT}/{name}.json')
print('built')
