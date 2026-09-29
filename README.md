# قالب وردپرس آکام (WebMZ)

این مخزن شامل قالب اصلی، قالب فرزند، و آرشیو فایل‌های اینکدشده برای انتشار در راست‌چین است.

## ساختار

- **akam/** = قالب اصلی با فایل‌های RAW (مسیر توسعه)
- **akam-child/** = قالب فرزند
- **encoded/VERSION/** = آرشیو فایل‌های اینکدشده (فقط نگهداری، در توسعه استفاده نشوند)

## گردش کار توسعه

1. از `main` برنچ feature بساز
2. روی فایل‌های raw داخل `akam/` کار کن
3. وقتی فیچر خوب بود: merge، ورژن را در `style.css` بالا ببر (با `scripts/bump-version.sh`)، commit، tag بزن (`vX.Y.Z` باید با Version در `style.css` یکی باشد)، push

## ارسال به راست‌چین

1. از روی نسخه raw داخل `akam/` پروژه را در راست‌چین اینکد کن (۱۷ فایل)
2. فایل‌های اینکدشده را در `encoded/X.Y.Z/` کپی کن و commit کن برای آرشیو (اسکریپت کمکی: `scripts/sync-encoded.sh`)
3. هرگز فایل اینکدشده را جایگزین raw داخل `akam/` نکن مگر برای تست موقت مارکت

## قوانین ورژن

GitHub tag (مثلاً `v1.0.1`) باید با Version در `style.css` یکی باشد.

اسکریپت‌ها:

```bash
./scripts/bump-version.sh 1.0.1
./scripts/sync-encoded.sh 1.0.1 /path/to/encoded-output
```

## محیط توسعه محلی (دمو ۳)

بسته Duplicator دمو ۳ در ریلیز [`akam-installer-demo3`](https://github.com/dalir-sys/akam-theme/releases/tag/akam-installer-demo3) قرار دارد. اسکریپت زیر آن را دانلود و روی MariaDB بازگردانی می‌کند و به‌جای نسخه اینکدشده داخل بسته، پوشه‌های raw `akam/` و `akam-child/` همین مخزن را symlink می‌کند:

```bash
./scripts/setup-demo.sh            # پیش‌فرض: ../akam-demo
php -d memory_limit=512M -S 127.0.0.1:8080 -t ../akam-demo/site ../akam-demo/router.php
```

آدرس: `http://localhost:8080` — مدیریت: `/wp-admin` با `localadmin` / `localadmin`

## افکت سه‌بعدی (Three.js)

فایل `akam/assets/js/three-background.min.js` خروجی بیلدشده است. بعد از ویرایش `akam/assets/js/src/three-background.js` دوباره بسازید (نیاز به Node.js):

```bash
./scripts/build-three.sh
```

