# دموهای آکام روی VPS (tadris.webmz.ir)

`tadris-demos.sh` installs the five Akam demos at `https://tadris.webmz.ir/demo-01` … `/demo-05` on an
Ubuntu 24.04 server that already runs other sites, without changing them:

| | |
|---|---|
| Files | `/var/www/tadris` (sites, logs, backups), `/etc/tadris-demos`, `/opt/tadris-demos` |
| PHP | own PHP-FPM master `tadris-php-fpm.service` (system `php-fpm8.3` binary, own `php.ini`, ionCube loaded only here, `open_basedir` limited to `/var/www/tadris`) |
| nginx | one new site file for `tadris.webmz.ir`, checked with `nginx -t` before a graceful reload |
| MySQL | databases and users `tadris_demo1` … `tadris_demo5` only |
| apt | adds missing packages only; stops if anything installed would be upgraded; package scripts may not restart services; the default `php` stays the same |
| SSL | Let's Encrypt (webroot) once the DNS record points at the server |

## Install

1. DNS: `A  tadris  →  <server IP>` (with Cloudflare, keep the proxy off until the certificate is issued).
2. On the server, as root:

```bash
mkdir -p /opt/tadris-demos && cd /opt/tadris-demos
curl -fsSLO https://raw.githubusercontent.com/dalir-sys/akam-theme/refs/heads/claude/festive-babbage-fgdvz8/scripts/vps/tadris-demos.sh
chmod +x tadris-demos.sh
EMAIL=you@example.com ./tadris-demos.sh install
```

The admin login of every demo (user `tadris` by default) is in `/root/tadris-demos-credentials.txt`.
If the DNS change was not live yet, the demos run on http; run `./tadris-demos.sh ssl` later.

If the server cannot download from GitHub or ioncube.com, copy the files there yourself and run with
`PKG_DIR=/root/akam` (the `akam-1.5.0-demo-N.zip` files and `SHA256SUMS*.txt` from release v1.5.0) and
`IONCUBE_TGZ=/root/ioncube_loaders_lin_x86-64.tar.gz`.

## Other commands

```bash
./tadris-demos.sh status
./tadris-demos.sh reinstall 3                       # fresh copy of demo 3 (drops its database)
./tadris-demos.sh update-theme /var/www/site        # encoded akam 1.5.0 into another WordPress site
./tadris-demos.sh update-theme /var/www/site --child
./tadris-demos.sh uninstall --yes                   # removes everything above
```
