import sys, os, json, copy
ND = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, ND + '/lib'); sys.path.insert(0, ND)
from el import w, c, row, col, url, img, icon, pad, dump
M = json.load(open(ND + '/d4_map.json'))
BASE = 'http://localhost:8084'
OUT = ND + '/d4out'; os.makedirs(OUT, exist_ok=True)
I = lambda f: img(*M['img'][f])
cat = lambda s: s
cat_url = lambda s: url(f'{BASE}/product-category/{s}/')
PRI, NAVY, AMB = '#3b5bfd', '#0b1b4d', '#ff9f1c'

def walk(els, fn):
    for e in els:
        fn(e)
        walk(e.get('elements', []), fn)

def section(*children, pt=30, pb=30, **kw):
    return c(*children, padding=pad(pt, 0, pb, 0), **kw)

def heading(text, size=44, color=NAVY, weight='900', tag='h1', align=None, **kw):
    s = dict(title=text, header_size=tag, title_color=color, typography_typography='custom',
             typography_font_size={'unit': 'px', 'size': size, 'sizes': []}, typography_font_weight=weight,
             typography_line_height={'unit': 'em', 'size': 1.45, 'sizes': []},
             typography_font_size_mobile={'unit': 'px', 'size': int(size * .68), 'sizes': []})
    if align: s['align'] = align
    s.update(kw)
    return w('heading', **s)

def text(html, color='#64748b', size=17):
    return w('text-editor', editor=html, text_color=color, typography_typography='custom',
             typography_font_size={'unit': 'px', 'size': size, 'sizes': []},
             typography_line_height={'unit': 'em', 'size': 1.95, 'sizes': []})

# ------------------------------------------------------------------ HOME
hero = section(
    row(
        col(
            heading('بیش از ۳٬۱۰۰ فایل دیجیتال باکیفیت', size=15, color=PRI, weight='800', tag='div',
                    _padding=pad(6, 14), _background_background='classic', _background_color='#eaf0ff', _border_radius=pad(999), _element_width='auto'),
            w('webmz-tadris-heading-animation', prefix='خرید ', words_list=[{'word': w_, '_id': f'w{i}'} for i, w_ in enumerate(['قالب وردپرس', 'افزونه حرفه‌ای', 'فونت فارسی', 'کتاب الکترونیک'])],
              suffix='', title_tag='h1'),
            heading('با خیال راحت و دانلود فوری', size=40, color=NAVY, tag='h2'),
            text('<p>آکام فایل بازاری مطمئن برای خرید و فروش قالب، افزونه، فونت، فایل گرافیکی و کتاب الکترونیک است. همه محصولات پیش از انتشار بررسی می‌شوند و با ضمانت بازگشت وجه و پشتیبانی فروشنده ارائه می‌شوند.</p>'),
            w('webmz-ajax-search', placeholder='نام قالب، افزونه یا فونت را جستجو کنید…', button_label='جستجو', products_title='محصولات مرتبط', posts_title='مقالات مرتبط'),
            w('webmz-zhaket-icon-list', items=[
                {'_id': 'i1', 'icon': icon('fas fa-award'), 'text': 'ضمانت بازگشت وجه'},
                {'_id': 'i2', 'icon': icon('fas fa-bolt'), 'text': 'دانلود فوری پس از پرداخت'},
                {'_id': 'i3', 'icon': icon('fas fa-headset'), 'text': 'پشتیبانی تیکتی فروشنده'}]),
            width=54, flex_gap={'column': '14', 'row': '14', 'isLinked': True, 'unit': 'px', 'size': 14}, justify_content='center'),
        col(
            w('webmz-light-source', lights=[
                {'_id': 'l1', 'light_title': 'آبی', 'light_color': PRI, 'light_opacity': {'size': 0.35}, 'light_x': {'size': 30}, 'light_y': {'size': 30}, 'light_size': {'size': 520, 'unit': 'px'}, 'light_blur': {'size': 120, 'unit': 'px'}},
                {'_id': 'l2', 'light_title': 'آبی روشن', 'light_color': '#7a8dff', 'light_opacity': {'size': 0.3}, 'light_x': {'size': 75}, 'light_y': {'size': 70}, 'light_size': {'size': 420, 'unit': 'px'}, 'light_blur': {'size': 120, 'unit': 'px'}}],
              render_mode='mesh', behind_content='yes', _position='absolute', _offset_x={'unit': 'px', 'size': 0}, _offset_y={'unit': 'px', 'size': 0}),
            w('image', image=I('hero.png'), image_size='full', width={'unit': '%', 'size': 100, 'sizes': []}),
            width=46, position='relative', justify_content='center'),
        gap=40, align_items='center'),
    pt=40, pb=20)

stories = section(w('webmz-stories', story_box_id=str(M.get('story_box', ''))), pt=10, pb=10)

cats = [('wordpress-themes', 'قالب‌های وردپرس', 'cat-themes.png', 120, 'قالب‌های فروشگاهی، آموزشی، شرکتی و خبری با پشتیبانی کامل'),
        ('wordpress-plugins', 'افزونه‌های وردپرس', 'cat-plugins.png', 340, 'فرم‌ساز، پیامک، سئو، حسابداری و صدها افزونه کاربردی'),
        ('graphic-files', 'فایل‌های گرافیکی', 'cat-graphics.png', 1600, 'یو آی کیت، آیکون سه‌بعدی، موکاپ و قالب‌های لایه‌باز'),
        ('ebooks', 'کتاب الکترونیک', 'cat-ebooks.png', 260, 'کتاب‌های کسب‌وکار آنلاین، سئو و بازاریابی دیجیتال'),
        ('persian-fonts', 'فونت فارسی', 'cat-fonts.png', 90, 'فونت‌های اورجینال با لایسنس تجاری و اعداد فارسی')]
categories = section(
    w('webmz-tadris-heading-2', subtitle='دسته‌بندی محصولات', title='هر فایلی که نیاز دارید، اینجاست', description='از قالب و افزونه وردپرس تا فونت و کتاب الکترونیک؛ محصولات را بر اساس نیاز خود مرور کنید.'),
    c(*[w('webmz-service-box', image=I(f), image_size='full', title=t, start_number=0, end_number=n, counter_prefix='+', description=d, link_text='مشاهده محصولات', link_url=cat_url(s), link_icon=icon('fas fa-arrow-left'))
        for s, t, f, n, d in cats],
      inner=True, flex_direction='row', flex_wrap='wrap', flex_gap={'column': '20', 'row': '20', 'isLinked': True, 'unit': 'px', 'size': 20},
      _flex_size='none', presetTitle='شبکه ای', container_type='grid', grid_columns_grid={'unit': 'fr', 'size': 5, 'sizes': []},
      grid_columns_grid_tablet={'unit': 'fr', 'size': 3, 'sizes': []}, grid_columns_grid_mobile={'unit': 'fr', 'size': 2, 'sizes': []},
      grid_rows_grid={'unit': 'fr', 'size': 1, 'sizes': []}, grid_gaps={'column': '20', 'row': '20', 'isLinked': True, 'unit': 'px', 'size': 20}),
    pt=40)

newest = section(
    w('webmz-zhaket-product-loop', filter_by='category', count=8, order_by='date', section_title='جدیدترین فایل‌های آکام فایل', badge_icon=icon('far fa-calendar-alt', 'fa-regular'),
      button_text='مشاهده همه', button_link=url(BASE + '/shop/'), add_to_cart_text='افزودن به سبد', preview_text='پیش‌نمایش', hide_preview_no_url='yes',
      free_text='رایگان', display_type='slider', slider_columns_desktop=4), pt=30)

special = section(
    w('webmz-special-offer-slider', filter_by='category', count=5, order_by='sale_end', section_title='پیشنهادهای شگفت‌انگیز این هفته',
      timer_label='پایان تخفیف تا', timer_ended_label='تخفیف به پایان رسید', features_title='ویژگی‌های کلیدی', author_label='فروشنده:',
      sales_label='فروش موفق', button_text='مشاهده و خرید فایل'), pt=30)

tabs = section(
    w('webmz-zhaket-product-tabs', section_title='کسب‌وکار خود را آنلاین کنید', view_all_text='مشاهده همه', view_all_link=url(BASE + '/shop/'),
      tab_items=[{'_id': 't0', 'tab_label': 'همه', 'filter_type': 'all'},
                 {'_id': 't1', 'tab_label': 'قالب‌ها', 'filter_type': 'category', 'category': cat('wordpress-themes')},
                 {'_id': 't2', 'tab_label': 'افزونه‌ها', 'filter_type': 'category', 'category': cat('wordpress-plugins')},
                 {'_id': 't3', 'tab_label': 'گرافیک', 'filter_type': 'category', 'category': cat('graphic-files')}],
      count=8, add_to_cart_text='افزودن به سبد', preview_text='پیش‌نمایش'), pt=30)

banners = section(
    w('webmz-zhaket-special-banners', header_title='آکام فایل، انتخاب‌های متنوع', header_description='جستجو کنید، بهترین محصولات را پیدا کنید و کسب‌وکار خود را مجهز کنید.\nبیش از سه هزار فایل متنوع در دسترس شماست!',
      banners=[{'_id': f'b{i}', 'banner_image': I(f'banner-{s}.jpg'), 'alt_text': a, 'enable_link': 'yes', 'link': u}
               for i, (s, a, u) in enumerate([('sale', 'جشنواره تخفیف', url(BASE + '/shop/')), ('themes', 'قالب‌های وردپرس', cat_url('wordpress-themes')),
                                               ('fonts', 'فونت‌های فارسی', cat_url('persian-fonts')), ('ebooks', 'کتاب‌های الکترونیک', cat_url('ebooks'))])],
      banner_width={'unit': 'px', 'size': 312, 'sizes': []}, banner_width_mobile={'unit': 'px', 'size': 260, 'sizes': []}, banner_height={'unit': 'px', 'size': 167, 'sizes': []}, banner_height_mobile={'unit': 'px', 'size': 139, 'sizes': []}, header_icon=icon('fas fa-th-large'),
      banner_radius={'unit': 'px', 'top': '18', 'right': '18', 'bottom': '18', 'left': '18', 'isLinked': True}, space_between={'unit': 'px', 'size': 16, 'sizes': []}), pt=30)

best = section(
    w('webmz-zhaket-bestsellers-column', columns=[
        {'_id': 'c1', 'column_title': 'پرفروش‌ترین قالب‌ها', 'show_badge': 'yes', 'badge_icon': icon('fas fa-desktop'), 'source': 'category', 'category': cat('wordpress-themes'), 'count': 4, 'order_by': 'popularity', 'show_button': 'yes', 'button_text': 'مشاهده همه قالب‌ها', 'button_link': cat_url('wordpress-themes')},
        {'_id': 'c2', 'column_title': 'پرفروش‌ترین افزونه‌ها', 'show_badge': 'yes', 'badge_icon': icon('fas fa-plug'), 'source': 'category', 'category': cat('wordpress-plugins'), 'count': 4, 'order_by': 'popularity', 'show_button': 'yes', 'button_text': 'مشاهده همه افزونه‌ها', 'button_link': cat_url('wordpress-plugins')},
        {'_id': 'c3', 'column_title': 'پرفروش‌ترین فایل‌های گرافیکی', 'show_badge': 'yes', 'badge_icon': icon('fas fa-palette'), 'source': 'category', 'category': cat('graphic-files'), 'count': 4, 'order_by': 'popularity', 'show_button': 'yes', 'button_text': 'مشاهده همه فایل‌ها', 'button_link': cat_url('graphic-files')}]),
    pt=30)

dev = section(
    w('webmz-zhaket-top-developer', author_id=str(M['user']['استودیو نوا']), section_title='فروشنده برتر هفته', name_source='manual', developer_name_manual='استودیو نوا',
      avatar_source='custom', avatar_custom=I('thumb-nava-shop-theme.png'), profile_link=url(BASE + '/shop/'),
      cta_code_image=I('dev-cta.png'), cta_title='دنبال کسب درآمد هستید؟', cta_subtitle='در آکام فایل فروشنده شوید', cta_button_text='ثبت‌نام فروشنده',
      cta_button_link=url(BASE + '/my-account/'), cta_more_text='اطلاعات بیشتر', cta_more_link=url(BASE + '/about-us/')), pt=30)

fresh = section(w('webmz-file-loop', filter_by='category', count=10, order_by='date', section_title='تازه‌ترین فایل‌های ایرانی', default_badge_text='محصول ویژه', jacket_style='yes'), pt=30)

testi_people = [('img-4.jpg', 'رضا کاظمی', 'مدیر فروشگاه اینترنتی', 'قالب نوا را خریدم و در یک روز فروشگاهم بالا آمد. پشتیبانی هم واقعاً سریع جواب می‌داد.'),
                ('img-3.jpg', 'مریم یزدانی', 'طراح رابط کاربری', 'یو آی کیت سبدک کار تیم ما را چند هفته جلو انداخت؛ کامپوننت‌ها خیلی تمیز و استاندارد هستند.'),
                ('img-2.jpg', 'حسین فاضلی', 'توسعه‌دهنده وردپرس', 'افزونه پیامکس را روی چند سایت مشتری نصب کردم؛ بدون حتی یک مشکل. لایسنس هم شفاف است.'),
                ('img-1.jpg', 'نگار امینی', 'مدیر بازاریابی', 'کتاب سئو در ۳۰ روز ساده و کاربردی بود. بعد از دو ماه ترافیک سایت‌مان دو برابر شد.')]
testi = section(
    w('webmz-tadris-heading-2', subtitle='نظرات خریداران', title='تجربه کاربران از خرید در آکام فایل', description='بیش از ۲۵ هزار خرید موفق و رضایت ۹۶ درصدی خریداران'),
    w('webmz-customer-testimonials-slider-2', testimonials=[{'_id': f'q{i}', 'avatar': img(*M['img'].get(a, [0, ''])) if a in M['img'] else {'url': f'{BASE}/wp-content/uploads/2026/07/{a}', 'id': ''}, 'name': n, 'job_title': j, 'rating': 5, 'content': t}
                                                            for i, (a, n, j, t) in enumerate(testi_people)], slides_desktop=4), pt=40)

blog = section(w('webmz-zhaket-blog-loop', heading_text='آموزش‌ها و مقالات آکام فایل', button_text='مشاهده وبلاگ', button_link=url(BASE + '/blog/'), posts_per_page=4), pt=30)

why = section(
    w('webmz-zhaket-why-buy-footer', heading_title='چرا از آکام فایل بخریم؟', heading_subtitle='مرجع مطمئن خرید فایل‌های دیجیتال فارسی', rating_value='+۴٫۸', rating_label='رضایت خریداران',
      products_count='+۳٬۱۰۰', products_label='فایل متنوع',
      product_logos=[{'_id': f'p{i}', 'logo_image': I(f'thumb-{s}.png'), 'logo_alt': s} for i, s in enumerate(['nava-shop-theme', 'payamax-sms-login', 'sepehr-font'])],
      guarantee_label='ضمانت بازگشت وجه', security_label='امنیت و به‌روزرسانی',
      security_badges=[{'_id': 's1', 'badge_icon': icon('fas fa-lock'), 'badge_text': 'SAFE', 'badge_gradient_start': PRI, 'badge_gradient_end': '#7a8dff'},
                       {'_id': 's2', 'badge_icon': icon('fas fa-sync-alt'), 'badge_text': 'UPDATE', 'badge_gradient_start': AMB, 'badge_gradient_end': '#ffc56b'}]),
    w('webmz-zhaket-footer-newsletter', title='خبرنامه آکام فایل', email_placeholder='ایمیل خود را وارد کنید', button_text='عضویت', success_message='عضویت شما با موفقیت ثبت شد.'),
    pt=30, pb=50, flex_gap={'column': '24', 'row': '24', 'isLinked': True, 'unit': 'px', 'size': 24})

from d4_sections import build as build_sections
why2, news2, footer2 = build_sections(M, BASE, I, cat_url, heading, text)
dump([hero, stories, categories, newest, special, tabs, banners, best, dev, fresh, testi, blog, why2, news2], OUT + '/home.json')

# ------------------------------------------------------------------ HEADER
h = json.load(open(ND + '/base/d4-15.json'))
def fix_header(e):
    s = e.get('settings') or {}
    t = e.get('widgetType')
    if t == 'webmz-header-contact-box': s.update(text_brand='آکام فایل', text_before='کارشناسان', text_after='پاسخگوی سوالات شما هستند')
    if t == 'webmz-site-logo':
        s['logo_image'] = I('logo.png'); s['logo_width'] = {'unit': 'px', 'size': 150, 'sizes': []}; s['logo_max_height'] = {'unit': 'px', 'size': 60, 'sizes': []}
        s['logo_width_mobile'] = {'unit': 'px', 'size': 120, 'sizes': []}
    if t == 'webmz-mobile-menu': s['logo_image'] = I('logo.png'); s['caption'] = 'ارتباط با آکام فایل'
    if t == 'webmz-ajax-search-popup': s['popular_title'] = 'جستجوهای پرطرفدار'
walk(h, fix_header)
dump(h, OUT + '/header.json')

# ------------------------------------------------------------------ FOOTER
f = json.load(open(ND + '/base/d4-565.json'))
def fix_footer(e):
    s = e.get('settings') or {}
    t = e.get('widgetType')
    if t == 'heading':
        s['title'] = {'ارتباط با وب پرداز': 'ارتباط با آکام فایل', 'کمی درباره ما بخوانید': 'درباره آکام فایل', 'دسترسی سریع': 'دسترسی سریع'}.get(s.get('title'), s.get('title'))
    if t == 'webmz-footer-contact':
        s['text_brand'] = 'آکام فایل'
        for it in s.get('items', []):
            if 'مشهد' in it.get('text', ''): it['text'] = 'تهران، خیابان ولیعصر، برج فناوری آکام'
            if 'تلگرام' in it.get('text', ''): it['text'] = 'پشتیبانی تلگرام: akamfile_support@'
    if t == 'text-editor':
        if 'کلیه حقوق' in s.get('editor', ''): s['editor'] = '<p>کلیه حقوق مادی و معنوی این وبسایت برای آکام فایل محفوظ می‌باشد.</p>'
        elif 'لورم' in s.get('editor', ''): s['editor'] = '<p>آکام فایل از سال ۱۳۹۸ با هدف ایجاد بازاری امن برای فایل‌های دیجیتال فارسی فعالیت می‌کند. همه محصولات پیش از انتشار بررسی می‌شوند و خریداران از ضمانت بازگشت وجه و پشتیبانی فروشنده بهره‌مند هستند.</p>'
    if t == 'webmz-footer-menu':
        s['items'] = [{'_id': f'f{i}', 'label': l, 'link': u} for i, (l, u) in enumerate([
            ('قالب‌های وردپرس', cat_url('wordpress-themes')), ('افزونه‌های وردپرس', cat_url('wordpress-plugins')), ('فایل‌های گرافیکی', cat_url('graphic-files')),
            ('کتاب الکترونیک', cat_url('ebooks')), ('فونت فارسی', cat_url('persian-fonts')), ('وبلاگ آموزشی', url(BASE + '/blog/'))])]
dump(footer2, OUT + '/footer.json')

# ------------------------------------------------------------------ MEGA MENU
mm = json.load(open(ND + '/base/d4-1185.json'))
P = json.load(open(ND + '/d4_products.json'))
icons_svg = [m['icon'] for m in mm[0]['elements'][0]['settings']['main_items']]
groups = [('wordpress-themes', 'قالب‌های وردپرس'), ('wordpress-plugins', 'افزونه‌های وردپرس'), ('graphic-files', 'فایل‌های گرافیکی'), ('ebooks', 'کتاب الکترونیک'), ('persian-fonts', 'فونت فارسی')]
main_items, sub_items = [], []
for i, (s, t) in enumerate(groups):
    key = f'item-{i + 1}'
    main_items.append({'_id': f'm{i}', 'item_key': key, 'title': t, 'link': cat_url(s), 'icon': icons_svg[i % len(icons_svg)]})
    for p in [p for p in P if p['cat'] == s]:
        sub_items.append({'_id': 's' + p['slug'][:6] + str(i), 'parent_key': key, 'title': p['title'], 'link': url(f"{BASE}/product/{p['slug']}/")})
    sub_items.append({'_id': f'sa{i}', 'parent_key': key, 'title': 'مشاهده همه ' + t, 'link': cat_url(s)})
mm[0]['elements'][0]['settings']['main_items'] = main_items
mm[0]['elements'][0]['settings']['submenu_items'] = sub_items
dump(mm, OUT + '/mega.json')

# ------------------------------------------------------------------ SINGLE PRODUCT
sp = json.load(open(ND + '/base/d4-976.json'))
def drop_curriculum(els):
    out = []
    for e in els:
        st = e.get('settings') or {}
        if e.get('widgetType') == 'webmz-spw-course-curriculum': continue
        if e.get('widgetType') == 'webmz-spw-section-heading' and 'سرفصل' in st.get('title', ''): continue
        e['elements'] = drop_curriculum(e.get('elements', []))
        out.append(e)
    return out
sp = drop_curriculum(sp)
def fix_single(e):
    s = e.get('settings') or {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام فایل'
    if t == 'webmz-spw-participants': s['label'] = 'خریدار'
    if t in ('webmz-spw-advanced-add-to-cart', 'webmz-spw-purchase-box'): s['button_text'] = 'خرید و دانلود'
    if t == 'webmz-spw-purchase-box':
        for it, txt in zip(s.get('bottom_items', []), ['دانلود فوری پس از پرداخت', 'به‌روزرسانی رایگان', '۷ روز ضمانت بازگشت وجه']): it['text'] = txt
        s['extra_button_mode'] = 'product'
    if t == 'webmz-spw-course-features':
        s['source'] = 'product'
    if t == 'webmz-spw-instructor-box': s.update(job_title='فروشنده محصول', profile_button='مشاهده پروفایل فروشنده')
    if t == 'webmz-spw-section-heading' and 'سوالات' in s.get('title', ''): s['title'] = 'پرسش‌های متداول'
walk(sp, fix_single)
sp.append(section(w('webmz-zhaket-product-loop', filter_by='category', count=8, order_by='popularity', section_title='محصولات پرفروش آکام فایل', badge_icon=icon('fas fa-fire'),
                    button_text='مشاهده همه', button_link=url(BASE + '/shop/'), add_to_cart_text='افزودن به سبد', preview_text='پیش‌نمایش', hide_preview_no_url='yes', display_type='slider'), pt=10, pb=40))
dump(sp, OUT + '/single-product.json')

# ------------------------------------------------------------------ ABOUT / CONTACT
ab = json.load(open(ND + '/base/d4-37.json'))
positions = ['مدیرعامل', 'مدیر محصول', 'مدیر پشتیبانی', 'کارشناس بازبینی فایل', 'توسعه‌دهنده ارشد', 'مدیر بازاریابی', 'کارشناس پشتیبانی', 'طراح رابط کاربری']
def fix_about(e):
    s = e.get('settings') or {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام فایل'
    if t == 'webmz-page-hero':
        s.update(eyebrow='بازار مطمئن فایل‌های دیجیتال فارسی', title='با آکام فایل <strong>آشنا</strong> شوید',
                 description='ما در آکام فایل، طراحان و توسعه‌دهندگان ایرانی را به هزاران کسب‌وکاری که به قالب، افزونه، فونت و فایل‌های گرافیکی باکیفیت نیاز دارند وصل می‌کنیم.',
                 primary_button_text='مشاهده محصولات', primary_button_link=url(BASE + '/shop/'), secondary_button_text='تماس با ما', secondary_button_link=url(BASE + '/contact-us/'))
    if t == 'heading' and 'درباره' in s.get('title', ''): s['title'] = 'داستان آکام فایل'
    if t == 'text-editor':
        s['editor'] = ('<p>آکام فایل در سال ۱۳۹۸ با یک ایده ساده شروع شد: خرید فایل دیجیتال باید به همان اندازه خرید از یک فروشگاه معتبر، امن و قابل‌اعتماد باشد. امروز بیش از ۳٬۱۰۰ محصول از ۴۵۰ فروشنده ایرانی در آکام فایل عرضه می‌شود.</p>'
                       '<p>هر محصول پیش از انتشار توسط تیم بازبینی ما از نظر کیفیت کد، امنیت و مستندات بررسی می‌شود. خریداران با خیال راحت خرید می‌کنند، چون ضمانت بازگشت وجه و پشتیبانی مستقیم فروشنده همراه هر خرید است؛ و فروشندگان هم با ابزارهای ما، فروش و پشتیبانی محصولاتشان را ساده مدیریت می‌کنند.</p>')
    if t == 'webmz-team-list':
        for m, pos in zip(s.get('members', []), positions): m['position'] = pos
    if t == 'webmz-jobs-callout': s['description'] = 'ما همیشه به دنبال نیروهای توانمند و خلاق برای گسترش خانواده آکام فایل هستیم.'
walk(ab, fix_about)
dump(ab, OUT + '/about.json')

ct = json.load(open(ND + '/base/d4-1309.json'))
offices = [('دفتر مرکزی تهران', 'تهران، خیابان ولیعصر، برج فناوری آکام، طبقه ۸'), ('پشتیبانی خریداران', 'پاسخگویی آنلاین از طریق تیکت و تلگرام'), ('واحد فروشندگان', 'مشهد، بلوار سجاد، ساختمان نوآوری')]
def fix_contact(e):
    s = e.get('settings') or {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام فایل'
    if t == 'webmz-page-hero':
        s.update(title='با آکام فایل در <strong>تماس</strong> باشید',
                 description='اگر درباره خرید، دانلود یا لایسنس محصولات سؤالی دارید، یا می‌خواهید فروشنده آکام فایل شوید، تیم ما آماده پاسخگویی است.')
    if t == 'webmz-contact-tabs':
        for o, (ti, ad) in zip(s.get('offices', []), offices): o['title'] = ti; o['address'] = ad
    if t == 'webmz-ajax-contact-form': s['description'] = 'برای دریافت راهنمایی درباره خرید یا همکاری به‌عنوان فروشنده، فرم زیر را تکمیل کنید.'
walk(ct, fix_contact)
dump(ct, OUT + '/contact.json')
print('pages built')

# ------------------------------------------------------------------ SINGLE POST + TEACHER ARCHIVE
sp2 = json.load(open(ND + '/base/d4-632.json'))
banners_side = iter(['banner-themes.jpg', 'banner-sale.jpg'])
def fix_post(e):
    s = e.get('settings') or {}
    t = e.get('widgetType')
    if t == 'webmz-post-breadcrumb': s['home_label'] = 'آکام فایل'
    if t == 'image':
        f = next(banners_side, None)
        if f: s['image'] = I(f); s['link_to'] = 'custom'; s['link'] = url(BASE + '/shop/')
    if t == 'webmz-post-download-box': s['header_subtitle'] = 'دانلود فایل‌های ضمیمه این مقاله'
walk(sp2, fix_post)
dump(sp2, OUT + '/single-post.json')
dump([section(w('webmz-teachers-archive', title_text='فروشندگان برتر آکام فایل'), pt=20, pb=40)], OUT + '/teacher-archive.json')
