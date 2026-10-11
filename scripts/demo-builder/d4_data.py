import json, os
D = os.path.dirname(os.path.abspath(__file__))
CATS = [
 ('wordpress-themes', 'قالب وردپرس', 'cat-themes'),
 ('wordpress-plugins', 'افزونه وردپرس', 'cat-plugins'),
 ('graphic-files', 'فایل گرافیکی', 'cat-graphics'),
 ('ebooks', 'کتاب الکترونیک', 'cat-ebooks'),
 ('persian-fonts', 'فونت فارسی', 'cat-fonts'),
]
T = 'wordpress-themes'; PL = 'wordpress-plugins'; G = 'graphic-files'; E = 'ebooks'; F = 'persian-fonts'
P = [
 dict(slug='nava-shop-theme', title='قالب فروشگاهی نوا', kind='theme', cat=T, price=1290000, sale=890000, emoji='shopping-cart', c1='#1d4ed8', c2='#60a5fa', vendor='استودیو نوا', sales=1840, rating=4.9, reviews=126,
      cover=dict(title='قالب فروشگاهی نوا', sub='فروشگاه ووکامرس سریع، زیبا و کاملاً راست‌چین', site='site4', tags=['ووکامرس','المنتور','RTL']),
      short='قالب فروشگاهی نوا برای راه‌اندازی یک فروشگاه اینترنتی حرفه‌ای با ووکامرس طراحی شده؛ سبک، سریع و سازگار با المنتور.',
      feats=['سازگار کامل با ووکامرس و المنتور','۸ دموی آماده با درون‌ریزی یک‌کلیکی','سبد خرید ایجکسی و جستجوی زنده','بهینه برای سئو و Core Web Vitals','به‌روزرسانی رایگان مادام‌العمر','پشتیبانی تیکتی ۶ ماهه'],
      file=('nava-theme-v3.2.zip','12.4 MB'), version='۳٫۲', compat='وردپرس ۶٫۰ به بالا', preview='https://tadris.webmz.ir/demo-01'),
 dict(slug='academy-lms-theme', title='قالب آموزشی آکادمی', kind='theme', cat=T, price=1490000, sale=0, emoji='graduation-cap', c1='#ea580c', c2='#fdba74', vendor='استودیو نوا', sales=2310, rating=4.8, reviews=204,
      cover=dict(title='قالب آموزشی آکادمی', sub='فروش دوره ویدیویی با پلیر اختصاصی و پنل دانشجو', site='site1', tags=['فروش دوره','پلیر ویدیو','المنتور']),
      short='آکادمی یک قالب کامل برای فروش دوره‌های آموزشی است؛ سرفصل، پلیر ویدیو، پنل دانشجو و صفحه مدرس را آماده دارد.',
      feats=['صفحه دوره با سرفصل و پلیر ویدیو','پنل کاربری اختصاصی دانشجو','ثبت‌نام و ورود با موبایل','سیستم تیکت پشتیبانی داخلی','قالب‌ساز هدر و فوتر','پشتیبانی تیکتی ۶ ماهه'],
      file=('academy-theme-v2.6.zip','18.1 MB'), version='۲٫۶', compat='وردپرس ۶٫۲ به بالا', preview='https://tadris.webmz.ir/demo-01'),
 dict(slug='parsa-agency-theme', title='قالب شرکتی پارسا', kind='theme', cat=T, price=990000, sale=0, emoji='briefcase', c1='#6d28d9', c2='#a78bfa', vendor='تیم پارس‌کد', sales=960, rating=4.7, reviews=88,
      cover=dict(title='قالب شرکتی پارسا', sub='برای آژانس‌های دیجیتال، استارتاپ‌ها و شرکت‌های خدماتی', site='site2', tags=['شرکتی','نمونه‌کار','المنتور']),
      short='پارسا قالبی شیک برای شرکت‌ها و آژانس‌هاست؛ صفحات خدمات، نمونه‌کار، تیم و تماس با ما را آماده دارد.',
      feats=['۱۲ صفحه داخلی آماده','بخش نمونه‌کار و خدمات','فرم تماس ایجکسی','انیمیشن‌های اسکرولی سبک','سازگار با موبایل و تبلت','پشتیبانی تیکتی ۶ ماهه'],
      file=('parsa-theme-v1.9.zip','9.8 MB'), version='۱٫۹', compat='وردپرس ۶٫۰ به بالا', preview='https://tadris.webmz.ir/demo-02'),
 dict(slug='lingo-language-theme', title='قالب آموزش زبان لینگو', kind='theme', cat=T, price=1190000, sale=790000, emoji='speech-balloon', c1='#dc2626', c2='#f87171', vendor='استودیو نوا', sales=1275, rating=4.9, reviews=97,
      cover=dict(title='قالب آموزش زبان لینگو', sub='مخصوص آموزشگاه‌ها و مدرسان زبان‌های خارجی', site='site3', tags=['آموزشگاه','استوری','پلی‌لیست']),
      short='لینگو برای آموزشگاه‌ها و مدرسان زبان طراحی شده؛ استوری، پلی‌لیست ویدیویی و دسته‌بندی دوره‌ها را دارد.',
      feats=['استوری‌های اینستاگرامی','پلی‌لیست ویدیوهای رایگان','معرفی اساتید با ویدیو','خبرنامه و درخواست مقاله','بهینه برای سئو','پشتیبانی تیکتی ۶ ماهه'],
      file=('lingo-theme-v1.4.zip','15.2 MB'), version='۱٫۴', compat='وردپرس ۶٫۲ به بالا', preview='https://tadris.webmz.ir/demo-03'),
 dict(slug='formino-form-builder', title='افزونه فرم‌ساز فرمینو', kind='plugin', cat=PL, price=490000, sale=0, emoji='memo', c1='#0891b2', c2='#67e8f9', vendor='تیم پارس‌کد', sales=3120, rating=4.8, reviews=233,
      cover=dict(title='فرم‌ساز فرمینو', sub='ساخت انواع فرم با کشیدن و رها کردن، بدون کدنویسی', tags=['۴۰+ فیلد','پرداخت آنلاین','پیامک'])),
 dict(slug='payamax-sms-login', title='افزونه ورود با موبایل پیامکس', kind='plugin', cat=PL, price=390000, sale=290000, emoji='mobile-phone', c1='#16a34a', c2='#86efac', vendor='تیم پارس‌کد', sales=4580, rating=4.9, reviews=318,
      cover=dict(title='ورود با موبایل پیامکس', sub='ثبت‌نام و ورود با کد یکبارمصرف + اطلاع‌رسانی پیامکی', tags=['۲۰+ پنل پیامک','ووکامرس','OTP'])),
 dict(slug='rotbeyar-seo', title='افزونه سئو فارسی رتبه‌یار', kind='plugin', cat=PL, price=590000, sale=0, emoji='chart-increasing', c1='#4f46e5', c2='#a5b4fc', vendor='نشر دیجیتال آکام', sales=1690, rating=4.7, reviews=142,
      cover=dict(title='سئو فارسی رتبه‌یار', sub='تحلیل محتوای فارسی، اسکیما و نقشه سایت هوشمند', tags=['تحلیل فارسی','اسکیما','ریدایرکت'])),
 dict(slug='daftarche-accounting', title='افزونه حسابداری دفترچه', kind='plugin', cat=PL, price=690000, sale=0, emoji='abacus', c1='#b45309', c2='#fcd34d', vendor='نشر دیجیتال آکام', sales=720, rating=4.6, reviews=54,
      cover=dict(title='حسابداری دفترچه', sub='گزارش سود و زیان، انبار و فاکتور رسمی برای ووکامرس', tags=['فاکتور رسمی','انبارداری','گزارش‌ساز'])),
 dict(slug='sabadak-ui-kit', title='یو آی کیت فروشگاهی سبدک', kind='uikit', cat=G, price=450000, sale=0, emoji='artist-palette', c1='#db2777', c2='#f9a8d4', vendor='گروه طراحی نگار', sales=640, rating=4.8, reviews=61,
      cover=dict(title='یو آی کیت سبدک', sub='کیت کامل اپلیکیشن فروشگاهی برای فیگما', accent='#f472b6')),
 dict(slug='3d-icon-pack', title='پک ۵۰۰ آیکون سه‌بعدی', kind='icons', cat=G, price=290000, sale=190000, emoji='gem-stone', c1='#7c3aed', c2='#c4b5fd', vendor='گروه طراحی نگار', sales=2050, rating=4.9, reviews=175,
      cover=dict(title='پک ۵۰۰ آیکون سه‌بعدی', sub='آیکون‌های رنگی برای سایت، اپلیکیشن و شبکه‌های اجتماعی', emojis=['rocket','light-bulb','trophy','gem-stone','wrapped-gift','shopping-cart','money-bag','locked','chart-increasing','fire','sparkles','package'])),
 dict(slug='branding-mockup', title='پک موکاپ برندینگ نگار', kind='mockup', cat=G, price=350000, sale=0, emoji='framed-picture', c1='#0f766e', c2='#5eead4', vendor='گروه طراحی نگار', sales=830, rating=4.7, reviews=69,
      cover=dict(title='پک موکاپ برندینگ', sub='۲۴ موکاپ لایه‌باز برای ارائه حرفه‌ای هویت بصری')),
 dict(slug='online-store-ebook', title='کتاب راه‌اندازی فروشگاه اینترنتی', kind='ebook', cat=E, price=180000, sale=0, emoji='shopping-cart', c1='#1e3a8a', c2='#3b82f6', vendor='نشر دیجیتال آکام', sales=1460, rating=4.8, reviews=112,
      cover=dict(title='راه‌اندازی فروشگاه اینترنتی از صفر', sub='۲۲۰ صفحه راهنمای عملی از انتخاب محصول تا اولین فروش', b1='#f59e0b', b2='#ea580c', author='نوشته: نیما رستگار')),
 dict(slug='seo-30-days-ebook', title='کتاب سئو در ۳۰ روز', kind='ebook', cat=E, price=150000, sale=99000, emoji='magnifying-glass-tilted-left', c1='#065f46', c2='#10b981', vendor='نشر دیجیتال آکام', sales=1980, rating=4.9, reviews=164,
      cover=dict(title='سئو در ۳۰ روز', sub='برنامه روزبه‌روز برای رسیدن به صفحه اول گوگل', b1='#0ea5e9', b2='#4f46e5', author='نوشته: مریم صادقی')),
 dict(slug='digital-marketing-ebook', title='کتاب دیجیتال مارکتینگ کسب‌وکارهای کوچک', kind='ebook', cat=E, price=0, sale=0, emoji='megaphone', c1='#9d174d', c2='#ec4899', vendor='نشر دیجیتال آکام', sales=5240, rating=4.7, reviews=289,
      cover=dict(title='دیجیتال مارکتینگ کسب‌وکارهای کوچک', sub='کتاب رایگان؛ از اینستاگرام تا ایمیل مارکتینگ', b1='#111827', b2='#4b5563', author='نوشته: تیم آکام فایل')),
 dict(slug='sepehr-font', title='فونت فارسی سپهر', kind='font', cat=F, price=390000, sale=0, emoji='fountain-pen', c1='#111827', c2='#ff9f1c', vendor='تایپ‌استودیو سپهر', sales=1120, rating=4.9, reviews=93,
      cover=dict(word='سپهر', family='peyda', weight=900, sub='۹ وزن')),
 dict(slug='nasim-font', title='فونت فارسی نسیم', kind='font', cat=F, price=250000, sale=190000, emoji='writing-hand', c1='#3b5bfd', c2='#a5f3fc', vendor='تایپ‌استودیو سپهر', sales=870, rating=4.8, reviews=71,
      cover=dict(word='نسیم', family='anjoman', weight=800, sub='۷ وزن')),
]

GENERIC_FEATS = {
 'plugin': ['نصب و راه‌اندازی در کمتر از ۵ دقیقه','سازگار با آخرین نسخه وردپرس و ووکامرس','کاملاً فارسی و راست‌چین','بدون کاهش سرعت سایت','به‌روزرسانی خودکار از پیشخوان','پشتیبانی تیکتی ۶ ماهه'],
 'uikit': ['+۸۰ صفحه طراحی‌شده','حالت روشن و تاریک','کامپوننت‌ها و متغیرهای رنگی فیگما','فونت و آیکون فارسی','گرید ۸ پیکسلی استاندارد','لایسنس استفاده تجاری'],
 'icons': ['۵۰۰ آیکون در ۲۵ دسته','خروجی PNG با کیفیت 4K','فایل‌های SVG و Blender','پس‌زمینه شفاف','سبک یکدست و رنگی','لایسنس استفاده تجاری'],
 'mockup': ['۲۴ موکاپ لایه‌باز PSD','Smart Object برای جایگذاری سریع','کیفیت 4K','نور و سایه قابل‌تنظیم','راهنمای استفاده فارسی','لایسنس استفاده تجاری'],
 'ebook': ['نسخه PDF و EPUB','مثال‌ها و چک‌لیست‌های عملی','قابل مطالعه در موبایل','به‌روزرسانی رایگان نسخه‌های بعدی','پرسش و پاسخ با نویسنده','لینک دانلود دائمی'],
 'font': ['پشتیبانی از اعداد فارسی و انگلیسی','فرمت‌های WOFF2، TTF و OTF','لایسنس استفاده وب و چاپ','کرنینگ دقیق حروف','نسخه وب‌فونت آماده','به‌روزرسانی رایگان'],
}
FILES = {'plugin': ('.zip', '2.6 MB'), 'uikit': ('.zip', '48 MB'), 'icons': ('.zip', '320 MB'), 'mockup': ('.zip', '1.2 GB'), 'ebook': ('.pdf', '8.4 MB'), 'font': ('.zip', '3.1 MB')}
KIND_FA = {'theme': 'قالب', 'plugin': 'افزونه', 'uikit': 'یو آی کیت', 'icons': 'پک آیکون', 'mockup': 'موکاپ', 'ebook': 'کتاب', 'font': 'فونت'}

def short_for(p):
    return p.get('short') or f"{p['title']} {p['cover']['sub']}؛ یک محصول باکیفیت از {p['vendor']} با پشتیبانی و به‌روزرسانی رایگان."

for p in P:
    if 'feats' not in p: p['feats'] = GENERIC_FEATS[p['kind']]
    if 'file' not in p:
        ext, size = FILES[p['kind']]; p['file'] = (p['slug'] + ext, size)
    p.setdefault('version', '۱٫۰'); p.setdefault('compat', 'وردپرس ۶٫۰ به بالا' if p['kind'] == 'plugin' else ('ویندوز، مک و لینوکس' if p['kind'] == 'font' else ('Figma' if p['kind'] == 'uikit' else 'همه دستگاه‌ها')))
    p.setdefault('preview', '')
    p['short'] = short_for(p)
    k = KIND_FA[p['kind']]
    p['faq'] = [
        dict(question=f'پس از خرید، {k} را چطور دریافت می‌کنم؟', answer='بلافاصله پس از پرداخت، لینک دانلود در صفحه سفارش و بخش «دانلودها» در حساب کاربری شما فعال می‌شود و به ایمیل‌تان هم ارسال می‌شود.'),
        dict(question='آیا به‌روزرسانی‌های بعدی رایگان است؟', answer='بله، تمام نسخه‌های بعدی این محصول بدون هزینه اضافه از بخش دانلودهای حساب کاربری قابل دریافت است.'),
        dict(question='اگر محصول با نیاز من سازگار نبود چه کنم؟', answer='تا ۷ روز پس از خرید، در صورت وجود مشکل فنی که توسط پشتیبانی برطرف نشود، مبلغ پرداختی بدون کسر هزینه بازگردانده می‌شود.'),
        dict(question='پشتیبانی چگونه انجام می‌شود؟', answer='پشتیبانی از طریق سیستم تیکت در حساب کاربری انجام می‌شود و کارشناسان در ساعات کاری حداکثر ظرف ۴ ساعت پاسخ می‌دهند.'),
    ]
    feats_li = ''.join(f'<li>{f}</li>' for f in p['feats'])
    p['desc'] = (f"<h2>معرفی {p['title']}</h2><p>{p['short']} این محصول با تمرکز بر کیفیت، سرعت و تجربه کاربری فارسی‌زبانان آماده شده و همه فایل‌ها پیش از انتشار توسط تیم بازبینی آکام فایل بررسی شده‌اند.</p>"
        f"<h3>ویژگی‌های کلیدی</h3><ul>{feats_li}</ul>"
        f"<h3>مناسب چه کسانی است؟</h3><p>اگر به دنبال راهی سریع و مطمئن برای ارتقای کسب‌وکار آنلاین خود هستید، {p['title']} گزینه‌ای حرفه‌ای است. مستندات کامل فارسی و ویدیوی آموزشی نصب همراه فایل ارائه می‌شود تا بدون نیاز به دانش فنی، کار را شروع کنید.</p>"
        f"<h3>مشخصات فایل</h3><ul><li>نسخه: {p['version']}</li><li>سازگاری: {p['compat']}</li><li>حجم فایل: {p['file'][1]}</li><li>توسعه‌دهنده: {p['vendor']}</li></ul>")
    p['spw'] = [
        dict(icon='fas fa-sync-alt', title='آخرین نسخه', subtitle=f"نسخه {p['version']}"),
        dict(icon='fas fa-download', title='حجم فایل', subtitle=p['file'][1]),
        dict(icon='fas fa-check-double', title='سازگاری', subtitle=p['compat']),
        dict(icon='fas fa-headset', title='پشتیبانی', subtitle='۶ ماه تیکتی'),
        dict(icon='fas fa-shield-alt', title='ضمانت', subtitle='۷ روز بازگشت وجه'),
    ]
json.dump(P, open(os.path.join(D, 'd4_products.json'), 'w'), ensure_ascii=False, indent=1)

POSTS = [
 dict(slug='choose-wordpress-theme', title='چطور بهترین قالب وردپرس را برای کسب‌وکارمان انتخاب کنیم؟', cover='انتخاب قالب وردپرس مناسب', emoji='laptop', c1='#1d4ed8', c2='#60a5fa', cat='آموزش وردپرس'),
 dict(slug='essential-woocommerce-plugins', title='۱۰ افزونه ضروری برای هر فروشگاه ووکامرسی', cover='۱۰ افزونه ضروری ووکامرس', emoji='electric-plug', c1='#16a34a', c2='#86efac', cat='آموزش وردپرس'),
 dict(slug='font-license-guide', title='راهنمای کامل لایسنس فونت فارسی؛ وب، چاپ یا اپلیکیشن؟', cover='راهنمای لایسنس فونت فارسی', emoji='fountain-pen', c1='#111827', c2='#ff9f1c', cat='طراحی و گرافیک'),
 dict(slug='sell-digital-files', title='فروش فایل دیجیتال؛ از ایده تا اولین درآمد', cover='فروش فایل دیجیتال از صفر', emoji='money-bag', c1='#b45309', c2='#fcd34d', cat='کسب‌وکار آنلاین'),
 dict(slug='speed-up-wordpress', title='۷ روش ساده برای افزایش سرعت سایت وردپرسی', cover='افزایش سرعت سایت وردپرس', emoji='high-voltage', c1='#7c3aed', c2='#c4b5fd', cat='آموزش وردپرس'),
 dict(slug='ui-kit-workflow', title='چرا طراحان حرفه‌ای با یو آی کیت کار می‌کنند؟', cover='کار حرفه‌ای با یو آی کیت', emoji='artist-palette', c1='#db2777', c2='#f9a8d4', cat='طراحی و گرافیک'),
 dict(slug='wordpress-security', title='چک‌لیست امنیت وردپرس پیش از راه‌اندازی سایت', cover='چک‌لیست امنیت وردپرس', emoji='shield', c1='#0f766e', c2='#5eead4', cat='آموزش وردپرس'),
 dict(slug='ebook-marketing', title='بازاریابی کتاب الکترونیک؛ چطور ebook خود را بفروشیم؟', cover='فروش و بازاریابی کتاب الکترونیک', emoji='books', c1='#9d174d', c2='#ec4899', cat='کسب‌وکار آنلاین'),
]
for b in POSTS:
    b['content'] = (f"<p>{b['title']} یکی از پرتکرارترین سؤال‌هایی است که از کاربران آکام فایل دریافت می‌کنیم. در این مقاله، تجربه چندساله تیم ما و فروشندگان برتر مارکت را به زبان ساده جمع‌بندی کرده‌ایم تا بتوانید با اطمینان بیشتری تصمیم بگیرید.</p>"
        "<h2>چرا این موضوع اهمیت دارد؟</h2><p>انتخاب درست در همان قدم اول، هزینه‌ها را کاهش می‌دهد و از دوباره‌کاری جلوگیری می‌کند. بسیاری از کسب‌وکارهای کوچک به دلیل نادیده گرفتن چند نکته ساده، ماه‌ها زمان و بودجه خود را از دست می‌دهند.</p>"
        "<h2>نکات کلیدی</h2><ul><li>نیاز واقعی کسب‌وکار خود را پیش از خرید مشخص کنید.</li><li>نظرات خریداران و امتیاز محصول را با دقت بخوانید.</li><li>به سابقه به‌روزرسانی و پاسخگویی پشتیبانی توجه کنید.</li><li>نسخه نمایشی (دمو) را روی موبایل هم بررسی کنید.</li></ul>"
        "<h2>جمع‌بندی</h2><p>با رعایت همین چند نکته، احتمال یک خرید موفق چند برابر می‌شود. اگر سؤالی دارید، در بخش دیدگاه‌ها بپرسید؛ کارشناسان آکام فایل پاسخ می‌دهند.</p>")
json.dump(POSTS, open(os.path.join(D, 'd4_posts.json'), 'w'), ensure_ascii=False, indent=1)
json.dump(CATS, open(os.path.join(D, 'd4_cats.json'), 'w'), ensure_ascii=False)
print(len(P), 'products', len(POSTS), 'posts')
