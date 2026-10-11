# Custom "why us", newsletter banner and footer for the file-shop demo (imported by d4_pages.py).
from el import w, c, url, img, icon, pad

def build(M, BASE, I, cat_url, heading, text):
    PRI, NAVY, AMB = '#3b5bfd', '#0b1b4d', '#ff9f1c'
    gap = lambda n: {'column': str(n), 'row': str(n), 'isLinked': True, 'unit': 'px', 'size': n}
    rad = lambda n: {'unit': 'px', 'top': str(n), 'right': str(n), 'bottom': str(n), 'left': str(n), 'isLinked': True}
    wpct = lambda n, m=100: dict(width={'unit': '%', 'size': n, 'sizes': []}, width_mobile={'unit': '%', 'size': m, 'sizes': []})

    # ---------------- 1) why us
    tiles = [('why-1.png', 'ضمانت بازگشت وجه', 'تا ۷ روز پس از خرید، اگر فایل با توضیحاتش مطابقت نداشت، پولتان را پس می‌گیرید.'),
             ('why-2.png', 'پشتیبانی واقعی', 'کارشناسان آکام فایل و خود فروشنده در کمتر از ۴ ساعت پاسخ می‌دهند.'),
             ('why-3.png', 'دانلود فوری و دائمی', 'لینک دانلود بلافاصله پس از پرداخت فعال می‌شود و همیشه در حساب شما می‌ماند.'),
             ('why-4.png', 'فایل‌های بازبینی‌شده', 'هر محصول پیش از انتشار از نظر امنیت، کیفیت کد و مستندات بررسی می‌شود.')]
    tile = lambda f, t, d: c(
        w('image', image=I(f), image_size='full', width={'unit': 'px', 'size': 64, 'sizes': []}, align='right', _css_classes='akf-why__icon'),
        heading(t, size=18, tag='h3', color=NAVY, weight='800', _margin=pad(6, 0, 0, 0)),
        text(f'<p>{d}</p>', size=14),
        inner=True, flex_direction='column', flex_gap=gap(6), css_classes='akf-why__tile',
        padding=pad(22), background_background='classic', background_color='#f6f8ff', border_radius=rad(22))
    stat = lambda n, t: c(heading(n, size=30, tag='div', color=PRI, weight='900'), text(f'<p>{t}</p>', size=13),
                          inner=True, flex_direction='column', flex_gap=gap(0), css_classes='akf-why__stat', padding=pad(14, 18))
    why = c(
        c(
            c(heading('چرا آکام فایل؟', size=14, tag='div', color=PRI, weight='800', _css_classes='akf-pill'),
              heading('خرید فایل دیجیتال، این بار با خیال راحت', size=34, tag='h2', color=NAVY),
              text('<p>بیش از ۲۵ هزار خریدار به آکام فایل اعتماد کرده‌اند؛ چون پشت هر خرید، ضمانت، پشتیبانی و فایلی بازبینی‌شده ایستاده است.</p>', size=16),
              c(stat('+۳٬۱۰۰', 'فایل باکیفیت'), stat('+۲۵هزار', 'خرید موفق'), stat('۴٫۸', 'رضایت خریداران'),
                inner=True, flex_direction='row', flex_gap=gap(12), flex_wrap='wrap', _margin=pad(10, 0, 0, 0)),
              inner=True, flex_direction='column', flex_gap=gap(14), justify_content='center', **wpct(40)),
            c(*[tile(*t) for t in tiles], inner=True, container_type='grid',
              grid_columns_grid={'unit': 'fr', 'size': 2, 'sizes': []}, grid_columns_grid_mobile={'unit': 'fr', 'size': 1, 'sizes': []},
              grid_rows_grid={'unit': 'fr', 'size': 2, 'sizes': []}, grid_gaps=gap(16), **wpct(60)),
            inner=True, flex_direction='row', flex_direction_mobile='column', flex_gap=gap(40), align_items='center', css_classes='akf-why',
            padding=pad(44), padding_mobile=pad(24), background_background='classic', background_color='#ffffff', border_radius=rad(32)),
        padding=pad(40, 0, 20, 0))

    # ---------------- 2) newsletter banner
    news = c(
        c(
            c(heading('خبرنامه آکام فایل', size=14, tag='div', color='#ffffff', weight='800', _css_classes='akf-pill akf-pill--light'),
              heading('اول از همه باخبر شو!', size=36, tag='h2', color='#ffffff'),
              text('<p>تخفیف‌های هفتگی، فایل‌های رایگان و جدیدترین قالب‌ها و افزونه‌ها را هر هفته در ایمیل خود دریافت کنید. با عضویت، کد ۱۵٪ تخفیف اولین خرید هم هدیه می‌گیرید.</p>', color='rgba(255,255,255,.88)', size=16),
              w('webmz-zhaket-footer-newsletter', title='', show_badge='', email_placeholder='ایمیل خود را وارد کنید…', submit_display='text', button_text='عضویت رایگان',
                success_message='عضویت شما ثبت شد؛ کد تخفیف به ایمیل‌تان ارسال می‌شود.', _css_classes='akf-news__form'),
              text('<p>✓ بدون اسپم  ·  ✓ لغو عضویت با یک کلیک  ·  ✓ +۱۸٬۰۰۰ عضو</p>', color='rgba(255,255,255,.75)', size=13),
              inner=True, flex_direction='column', flex_gap=gap(12), justify_content='center', **wpct(58)),
            c(w('image', image=I('news-art.png'), image_size='full', width={'unit': '%', 'size': 100, 'sizes': []}),
              inner=True, flex_direction='column', justify_content='center', **wpct(42)),
            inner=True, flex_direction='row', flex_direction_mobile='column', flex_gap=gap(30), align_items='center', css_classes='akf-news',
            padding=pad(44, 52), padding_mobile=pad(28, 22), border_radius=rad(32),
            background_background='gradient', background_color=PRI, background_color_b='#7a8dff', background_gradient_angle={'unit': 'deg', 'size': 135, 'sizes': []}),
        padding=pad(20, 0, 60, 0))

    # ---------------- 3) footer
    flinks = lambda title, links: w('webmz-footer-links', title=title, show_accent='yes', links=[{'_id': f'fl{abs(hash(l)) % 99999}', 'link_title': l, 'link_url': u} for l, u in links], _css_classes='akf-flinks')
    cats = [('قالب‌های وردپرس', 'wordpress-themes'), ('افزونه‌های وردپرس', 'wordpress-plugins'), ('فایل‌های گرافیکی', 'graphic-files'), ('کتاب الکترونیک', 'ebooks'), ('فونت فارسی', 'persian-fonts')]
    badge = lambda iid, src: c(w('image', image={'url': src, 'id': iid, 'size': '', 'alt': 'نماد اعتماد', 'source': 'library'}, image_size='full', width={'unit': 'px', 'size': 64, 'sizes': []}),
                               inner=True, css_classes='akf-badge', padding=pad(8), background_background='classic', background_color='#ffffff', border_radius=rad(16), _flex_size='none')
    footer = [
        c(
            c(
                c(w('webmz-site-logo', logo_image=I('logo-light.png'), logo_width={'unit': 'px', 'size': 160, 'sizes': []}, light_sweep_effect=''),
                  text('<p>آکام فایل، بازار امن خرید و فروش فایل‌های دیجیتال فارسی است؛ قالب، افزونه، فونت، فایل گرافیکی و کتاب الکترونیک از بهترین طراحان و توسعه‌دهندگان ایرانی، با ضمانت بازگشت وجه.</p>', color='rgba(255,255,255,.7)', size=14),
                  w('social-icons', social_icon_list=[{'_id': f's{i}', 'social_icon': icon(v, 'fa-brands'), 'link': url('#')} for i, v in enumerate(['fab fa-telegram-plane', 'fab fa-instagram', 'fab fa-linkedin-in', 'fab fa-youtube'])],
                    shape='rounded', align='right', icon_color='custom', icon_primary_color='rgba(255,255,255,.08)', icon_secondary_color='#ffffff',
                    icon_size={'unit': 'px', 'size': 16, 'sizes': []}, icon_padding={'unit': 'px', 'size': 12, 'sizes': []}, icon_spacing={'unit': 'px', 'size': 8, 'sizes': []}, _css_classes='akf-social'),
                  c(badge(2255, f'{BASE}/wp-content/uploads/2026/06/Group-60.png'), badge(2254, f'{BASE}/wp-content/uploads/2026/06/Group-58.png'),
                    inner=True, flex_direction='row', flex_gap=gap(10)),
                  inner=True, flex_direction='column', flex_gap=gap(18), css_classes='akf-fcol akf-fcol--brand'),
                c(flinks('دسته‌بندی محصولات', [(n, cat_url(s)) for n, s in cats]), inner=True, css_classes='akf-fcol'),
                c(flinks('دسترسی سریع', [('فروشگاه', url(BASE + '/shop/')), ('وبلاگ آموزشی', url(BASE + '/blog/')), ('فروشندگان برتر', url(BASE + '/mentors/')),
                                         ('درباره ما', url(BASE + '/about-us/')), ('تماس با ما', url(BASE + '/contact-us/')), ('حساب کاربری', url(BASE + '/my-account/'))]), inner=True, css_classes='akf-fcol'),
                c(flinks('فروشندگان', [('فروشنده شوید', url(BASE + '/my-account/')), ('راهنمای انتشار محصول', url(BASE + '/blog/')), ('قوانین و کارمزد فروش', url(BASE + '/about-us/')),
                                       ('تسویه حساب فروشندگان', url(BASE + '/my-account/')), ('ضمانت بازگشت وجه', url(BASE + '/about-us/'))]), inner=True, css_classes='akf-fcol'),
                c(heading('پشتیبانی آکام فایل', size=17, tag='h3', color='#ffffff', weight='800'),
                  w('icon-list', icon_list=[{'_id': f'c{i}', 'text': t, 'selected_icon': icon(ic), 'link': url(u)} for i, (t, ic, u) in enumerate([
                      ('۰۲۱-۹۱۰۰۰۰۰۰', 'fas fa-phone-alt', 'tel:02191000000'), ('support@akamfile.ir', 'fas fa-envelope', 'mailto:support@akamfile.ir'),
                      ('@akamfile_support', 'fab fa-telegram-plane', '#'), ('شنبه تا پنجشنبه، ۹ تا ۲۱', 'fas fa-clock', '')])],
                    icon_color=PRI, text_color='rgba(255,255,255,.78)', space_between={'unit': 'px', 'size': 12, 'sizes': []}, icon_size={'unit': 'px', 'size': 15, 'sizes': []},
                    _css_classes='akf-contact'),
                  w('tadris_advanced_button', button_text='ارسال تیکت پشتیبانی', button_link=url(BASE + '/my-account/'), selected_icon=icon('fas fa-headset'), _css_classes='akf-ticket'),
                  inner=True, flex_direction='column', flex_gap=gap(16), css_classes='akf-fcol'),
                inner=True, container_type='grid', grid_columns_grid={'unit': 'fr', 'size': 5, 'sizes': []}, grid_columns_grid_tablet={'unit': 'fr', 'size': 3, 'sizes': []},
                grid_columns_grid_mobile={'unit': 'fr', 'size': 1, 'sizes': []}, grid_rows_grid={'unit': 'fr', 'size': 1, 'sizes': []}, grid_gaps=gap(32), css_classes='akf-fgrid'),
            c(text('<p>© ۱۴۰۵ کلیه حقوق مادی و معنوی این وبسایت برای <strong>آکام فایل</strong> محفوظ است.</p>', color='rgba(255,255,255,.6)', size=13),
              text('<p>پرداخت امن با درگاه‌های بانکی معتبر  ·  طراحی‌شده با پوسته آکام</p>', color='rgba(255,255,255,.6)', size=13),
              inner=True, flex_direction='row', flex_direction_mobile='column', justify_content='space-between', align_items='center', css_classes='akf-fbottom', padding=pad(22, 0, 10, 0)),
            css_classes='akf-footer', padding=pad(64, 0, 20, 0), padding_mobile=pad(44, 16, 16, 16), flex_gap=gap(36),
            background_background='gradient', background_color='#0b1b4d', background_color_b='#081236', background_gradient_angle={'unit': 'deg', 'size': 180, 'sizes': []}),
        c(w('webmz-back-to-top-2', label='رفتن به بالای صفحه'), padding=pad(0)),
    ]
    return why, news, footer

CSS = '''
/* ---- Akam File demo: custom "why us", newsletter and footer ---- */
.akf-pill .elementor-heading-title{display:inline-block;padding:6px 14px;border-radius:999px;background:var(--webmz-color-primary-light)}
.akf-pill--light .elementor-heading-title{background:rgba(255,255,255,.16)}
.akf-why{box-shadow:0 30px 60px -40px rgba(11,27,77,.35);border:1px solid #e6ebff}
.akf-why__tile{transition:translate .3s ease,box-shadow .3s ease}
.akf-why__tile:hover{translate:0 -4px;box-shadow:0 18px 34px -20px rgba(59,91,253,.45)}
.akf-why__icon img{filter:drop-shadow(0 10px 14px rgba(11,27,77,.18))}
.akf-why__stat.e-con{--width:auto;flex:1 1 0;min-width:110px;background:#fff;border:1px solid #e6ebff;border-radius:18px}
.akf-why__stat p{margin:0}
.akf-news{position:relative;overflow:hidden;box-shadow:0 40px 70px -40px rgba(59,91,253,.8)}
.akf-news::before{content:"";position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.18) 1.3px,transparent 1.3px);background-size:22px 22px;mask-image:linear-gradient(90deg,transparent,#000 70%);pointer-events:none}
.akf-news::after{content:"";position:absolute;left:-80px;bottom:-120px;width:340px;height:340px;border-radius:50%;background:rgba(255,255,255,.12);pointer-events:none}
.akf-news > .e-con-inner,.akf-news > .e-con{position:relative;z-index:1}
.akf-news .webmz-zfn{background:transparent;box-shadow:none;padding:0;max-width:520px}
.akf-news .webmz-zfn__header{display:none}
.akf-news .webmz-zfn__input-bar{background:#fff!important;border-radius:18px;padding:8px 8px 8px 8px;box-shadow:0 18px 40px -20px rgba(11,27,77,.6)}
.akf-news .webmz-zfn__submit{background:#0b1b4d!important;color:#fff!important;border-radius:14px!important;padding:0 22px!important;height:46px;font-weight:800}
.akf-news .webmz-zfn__submit:hover{background:var(--webmz-color-primary-hover)!important}
.akf-news .webmz-zfn__message{color:#fff}
.akf-footer{position:relative;overflow:hidden}
.akf-footer::before{content:"";position:absolute;right:-160px;top:-160px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(59,91,253,.35),transparent 70%);pointer-events:none}
.akf-footer .webmz-footer-links{background:transparent!important;box-shadow:none!important;padding:0!important}
.akf-footer .webmz-footer-links__title{color:#fff!important;font-size:17px}
.akf-footer .webmz-footer-links__accent{background:var(--webmz-color-primary)!important}
.akf-footer .webmz-footer-links__link{color:rgba(255,255,255,.72)!important;transition:color .2s ease,padding .2s ease}
.akf-footer .webmz-footer-links__link:hover{color:#fff!important;padding-inline-start:4px}
.akf-footer .akf-contact .elementor-icon-list-icon svg{fill:#7f95ff!important}.akf-footer .akf-contact .elementor-icon-list-icon i{color:#7f95ff!important}
.akf-footer .akf-contact a,.akf-footer .akf-contact .elementor-icon-list-text{color:rgba(255,255,255,.78)!important}
.akf-footer .akf-contact .elementor-icon-list-text{direction:rtl}
.akf-footer .akf-ticket .tadris-advanced-button{background:none!important;border:0!important;padding:0!important;box-shadow:none!important}
.akf-footer .akf-ticket .tadris-advanced-button__link{background:rgba(255,255,255,.08)!important;color:#fff!important;border:1px solid rgba(255,255,255,.18)!important;border-radius:14px!important}
.akf-footer .akf-ticket .tadris-advanced-button__link:hover{background:var(--webmz-color-primary)!important;border-color:var(--webmz-color-primary)!important}
.akf-badge.e-con{--width:auto;flex:0 0 auto}
.akf-footer .akf-social .elementor-social-icon{border:1px solid rgba(255,255,255,.14)}
.akf-footer .akf-social .elementor-social-icon:hover{background:#3b5bfd!important}
.akf-fbottom{border-top:1px solid rgba(255,255,255,.1)}
.akf-fbottom p{margin:0}
.webmz-sos__button{background:var(--webmz-color-primary)!important}
.webmz-sos__button:hover{background:var(--webmz-color-primary-hover)!important}
'''
