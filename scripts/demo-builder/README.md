# سازنده دموهای ۴ و ۵ (Demo builder)

این پوشه دموی **فروش فایل («آکام فایل»)** و دموی **دوره‌های روانشناسی («آکام ذهن»)** را از روی دموهای ۲ و ۳ می‌سازد: تصاویر (کاور محصولات، بنرها، استوری‌ها، لوگو)، محصولات، نوشته‌ها، مدرسین/فروشندگان، منوها، صفحات المنتور و رنگ‌بندی.

Builds the file-shop demo (4, based on demo 2) and the psychology demo (5, based on demo 3): generated images, products, posts, teachers/sellers, menus, Elementor pages and colours.

## پیش‌نیازها

- دموهای پایه با `../setup-demo.sh` (دمو ۲ و ۳) و MariaDB
- Node.js (Playwright و Chromium) و Python 3 با Pillow (`pip install pillow`)
- فونت‌ها از `akam/assets/fonts` و آیکون‌ها از بسته‌های npm `@iconify-json` (MIT)

## اجرا

```bash
cd scripts/demo-builder
./clone-base.sh ../../../akam-demo-2 akam_demo2 http://localhost:8082 4 8084   # پایه دمو ۴
./clone-base.sh ../../../akam-demo   akam_demo  http://localhost:8080 5 8085   # پایه دمو ۵
./build-demo.sh 4
./build-demo.sh 5
```

اسکرین‌شات‌های دموهای ۱ تا ۳ که روی کاور قالب‌ها استفاده می‌شوند در `assets/site-shots` هستند (برای به‌روزرسانی: `tools/vshot.js`).

اجرای دوباره `build-demo.sh` امن است: محصولات و نوشته‌ها دوباره ساخته می‌شوند و تصاویر بر اساس نام فایل دوباره استفاده می‌شوند.

## ساخت بسته نصب آسان

```bash
../build-release.sh 1.5.0                                                        # قالب اینکدشده در dist/1.5.0
./package-copy.sh 4 ../../../akam-demo-2/pkg/archive.zip https://tadris.webmz.ir/demo-04
./dup-package.sh 4 1.5.0                                                         # dist/1.5.0/akam-1.5.0-demo-4.zip
```

بسته کامل محصول با `../build-product-package.sh` و گردش‌کار `Product package` در GitHub Actions ساخته می‌شود.

## ساختار

| فایل | کار |
|---|---|
| `d4_data.py`، `d5_data.py` | کاتالوگ محصولات/دوره‌ها، مدرسین و مقالات |
| `gen_d4.py`، `gen_d4b.py`، `gen_d5.py` + `lib/render.js` | ساخت تصاویر از HTML با Playwright |
| `d4_content.php`، `d5_content.php`، `d4_extra.php` | درون‌ریزی محتوا با `wp eval-file` |
| `d4_pages.py`، `d4_sections.py`، `d5_pages.py` | ساخت JSON صفحات المنتور (پایه‌ها در `base/`) |
| `d4_apply.php`، `d5_apply.php` | اعمال صفحات، منوها و لایه‌ها |
| `palette.php`، `globals.php`، `brandfix.py`، `set_css.php` | یکسان‌سازی رنگ اصلی در کیت المنتور، تنظیمات قالب و ویجت‌ها |
| `package-copy.sh`، `dup-package.sh`، `dup-name.php` | ساخت بسته نصب آسان Duplicator با قالب اینکدشده |
| `tools/` | اسکرین‌شات کامل صفحات (`shoot.js`)، بررسی رنگ‌ها (`coloraudit.js`)، فهرست کنترل‌های ویجت‌ها |

نکته: چند ویجت قالب رنگ ثابت (قرمز، نارنجی، آبی پیش‌فرض) دارند؛ `brandfix.py` برای دموها CSS جایگزین با `var(--webmz-color-primary)` می‌سازد و `globals.php` کنترل‌هایی را که پیش‌فرضشان `#0878f9` است به رنگ اصلی سراسری المنتور وصل می‌کند.
