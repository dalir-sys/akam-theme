(function () {
    'use strict';

    var cfg = window.webmzOtpAuth || {};

    function qs(root, selector) { return (root || document).querySelector(selector); }
    function qsa(root, selector) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); }

    function currentUrl() {
        return (window.location.href || '').split('#')[0];
    }

    function normalizeRedirect(redirect) {
        redirect = redirect || '';
        if (!redirect || redirect === '#') {
            redirect = currentUrl();
        }
        return redirect;
    }

    function setFormRedirect(root, redirect) {
        if (!root) { return; }
        redirect = normalizeRedirect(redirect);
        root.setAttribute('data-redirect', redirect);

        var googleLink = qs(root, '.webmz-otp-google a');
        if (googleLink) {
            try {
                var googleUrl = new URL(googleLink.getAttribute('href'), window.location.origin);
                googleUrl.searchParams.set('redirect', encodeURIComponent(redirect));
                googleLink.setAttribute('href', googleUrl.toString());
            } catch (e) {}
        }
    }

    function message(root, text, type, debug) {
        var box = qs(root, '[data-webmz-otp-message]');
        if (!box) { return; }
        var finalText = text || '';
        if (debug) {
            finalText += '\n\nجزئیات تست:\n' + debug;
        }
        box.hidden = !finalText;
        box.textContent = finalText;
        box.className = 'webmz-otp-auth__message is-' + (type || 'info');
        box.classList.remove('is-demo');
    }

    function demoCodeBoxHtml(otp) {
        otp = otp || '0000';
        return '<span class="webmz-otp-demo-code" aria-label="کد دمو">' + otp + '</span>';
    }

    function showDemoSuccess(root, otp) {
        var box = qs(root, '[data-webmz-otp-message]');
        if (!box) { return; }
        box.hidden = false;
        box.className = 'webmz-otp-auth__message is-success is-demo';
        box.innerHTML = 'کد در حالت دمو ' + demoCodeBoxHtml(otp) + ' است.';
    }

    function showDemoHint(hint, otp) {
        if (!hint) { return; }
        hint.classList.add('is-demo');
        hint.innerHTML = 'کد در حالت دمو ' + demoCodeBoxHtml(otp) + ' است.';
    }

    function setStep(root, step) {
        qsa(root, '.webmz-otp-step').forEach(function (el) {
            el.classList.toggle('is-active', el.getAttribute('data-step') === step);
        });
    }

    function countdown(root, seconds) {
        var btn = qs(root, '[data-webmz-otp-resend]');
        if (!btn) { return; }
        var left = parseInt(seconds || cfg.resendSeconds || 60, 10);
        btn.disabled = true;
        clearInterval(btn.webmzTimer);
        var tick = function () {
            if (left <= 0) {
                btn.disabled = false;
                btn.textContent = cfg.resendText || 'ارسال مجدد کد';
                clearInterval(btn.webmzTimer);
                return;
            }
            btn.textContent = (cfg.resendWaitText || 'ارسال مجدد تا {s} ثانیه').replace('{s}', left);
            left--;
        };
        tick();
        btn.webmzTimer = setInterval(tick, 1000);
    }

    function firewallPayload(root) {
        var payload = {};
        if (cfg.firewallToken) {
            payload.webmz_otp_fw_token = cfg.firewallToken;
            payload.webmz_otp_fw_issued = String(cfg.firewallIssued || '');
        }
        var hp = root ? qs(root, '[data-webmz-otp-hp]') : null;
        if (!hp) {
            hp = document.querySelector('[data-webmz-otp-hp]');
        }
        if (hp) {
            payload.webmz_otp_hp = hp.value;
        }
        return payload;
    }

    function post(action, data, root) {
        var form = new FormData();
        var merged = Object.assign({}, data || {}, firewallPayload(root));
        Object.keys(merged).forEach(function (key) { form.append(key, merged[key]); });
        form.append('action', action);
        form.append('nonce', cfg.nonce || '');
        return fetch(cfg.ajaxUrl || '', { method: 'POST', credentials: 'same-origin', body: form }).then(function (r) { return r.json(); });
    }

    function extractDemoOtp(res) {
        if (!res || !res.data) { return ''; }
        var otp = res.data.demo_otp || res.data.otp || res.data.code || '';
        if (otp) { return String(otp); }
        if (res.data.mock) { return '0000'; }
        var msg = String(res.data.message || '');
        if (msg.indexOf('دمو') !== -1 || msg.indexOf('0000') !== -1) {
            return '0000';
        }
        return '';
    }

    function fillOtpInput(input, otp) {
        if (!input || !otp) { return; }
        input.removeAttribute('readonly');
        input.value = '';
        input.value = String(otp);
        input.setAttribute('value', String(otp));
        try {
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (e) {}
    }

    function forceFillDemoOtp(root, otp) {
        if (!otp) { return; }
        fillOtpInput(qs(root, '[data-webmz-otp-code]'), otp);
        [0, 50, 150, 300].forEach(function (delay) {
            setTimeout(function () {
                fillOtpInput(qs(root, '[data-webmz-otp-code]'), otp);
            }, delay);
        });
    }

    function bindForm(root) {
        if (!root || root.getAttribute('data-webmz-otp-ready') === 'yes') { return; }
        root.setAttribute('data-webmz-otp-ready', 'yes');
        var mobileInput = qs(root, '[data-webmz-otp-mobile]');
        var codeInput = qs(root, '[data-webmz-otp-code]');
        var sendBtn = qs(root, '[data-webmz-otp-send]');
        var verifyBtn = qs(root, '[data-webmz-otp-verify]');
        var resendBtn = qs(root, '[data-webmz-otp-resend]');
        var changeBtn = qs(root, '[data-webmz-otp-change]');
        setFormRedirect(root, root.getAttribute('data-redirect') || currentUrl());

        function getRedirect() {
            return normalizeRedirect(root.getAttribute('data-redirect') || currentUrl());
        }

        function sendCode() {
            var mobile = mobileInput ? mobileInput.value : '';
            if (!mobile) { message(root, cfg.invalidMobile || 'شماره موبایل را وارد کنید.', 'error'); return; }
            sendBtn.disabled = true;
            message(root, cfg.sending || 'در حال ارسال کد...', 'info');
            post('webmz_otp_request', { mobile: mobile }, root).then(function (res) {
                if (!res || !res.success) { throw res; }
                setStep(root, 'code');
                var hint = qs(root, '[data-webmz-otp-hint]');
                var otp = extractDemoOtp(res);
                if (otp) {
                    showDemoSuccess(root, otp);
                    if (hint) {
                        hint.classList.remove('is-demo');
                        hint.textContent = '';
                    }
                    forceFillDemoOtp(root, otp);
                } else {
                    if (hint) {
                        hint.classList.remove('is-demo');
                        hint.textContent = (res.data.mode === 'register' ? (cfg.registerHint || 'برای این شماره حسابی وجود ندارد؛ بعد از تأیید ثبت‌نام انجام می‌شود.') : (cfg.loginHint || 'کد ورود برای شماره شما ارسال شد.'));
                    }
                    message(root, res.data.message || cfg.sent || 'کد تأیید ارسال شد.', 'success');
                }
                countdown(root, res.data.resend || cfg.resendSeconds || 60);
                var activeCodeInput = qs(root, '[data-webmz-otp-code]');
                if (activeCodeInput) { activeCodeInput.focus(); }
            }).catch(function (err) {
                var data = err && err.data ? err.data : {};
                message(root, data.message || cfg.error || 'خطایی رخ داد.', 'error', data.debug || '');
            }).finally(function () { sendBtn.disabled = false; });
        }

        function verifyCode() {
            verifyBtn.disabled = true;
            message(root, cfg.verifying || 'در حال بررسی کد...', 'info');
            var liveCodeInput = qs(root, '[data-webmz-otp-code]') || codeInput;
            post('webmz_otp_verify', { mobile: mobileInput ? mobileInput.value : '', code: liveCodeInput ? liveCodeInput.value : '', redirect: getRedirect() }, root).then(function (res) {
                if (!res || !res.success) { throw res; }
                message(root, res.data.message || cfg.loggedIn || 'ورود موفق بود.', 'success');
                window.location.href = res.data.redirect || getRedirect() || window.location.href;
            }).catch(function (err) {
                var data = err && err.data ? err.data : {};
                message(root, data.message || cfg.error || 'کد تأیید صحیح نیست.', 'error', data.debug || '');
            }).finally(function () { verifyBtn.disabled = false; });
        }

        if (sendBtn) { sendBtn.addEventListener('click', sendCode); }
        if (resendBtn) { resendBtn.addEventListener('click', sendCode); }
        if (verifyBtn) { verifyBtn.addEventListener('click', verifyCode); }
        if (changeBtn) { changeBtn.addEventListener('click', function () { setStep(root, 'mobile'); message(root, '', 'info'); }); }
        [mobileInput, codeInput].forEach(function (input) { if (input) { input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); input === mobileInput ? sendCode() : verifyCode(); } }); } });
    }

    function openPopup(redirect) {
        var pop = qs(document, '[data-webmz-otp-popup]');
        if (!pop) { return false; }
        redirect = normalizeRedirect(redirect || currentUrl());
        qsa(pop, '[data-webmz-otp-auth]').forEach(function (form) {
            setFormRedirect(form, redirect);
        });
        clearTimeout(pop.webmzOtpCloseTimer);
        pop.hidden = false;
        pop.classList.remove('is-closing');
        document.documentElement.classList.add('webmz-otp-popup-open');
        requestAnimationFrame(function () {
            pop.classList.add('is-open');
        });
        var input = qs(pop, '[data-webmz-otp-mobile]');
        if (input) { setTimeout(function () { input.focus(); }, 220); }
        return true;
    }

    function closePopup() {
        var pop = qs(document, '[data-webmz-otp-popup]');
        if (!pop) { return; }
        pop.classList.remove('is-open');
        pop.classList.add('is-closing');
        document.documentElement.classList.remove('webmz-otp-popup-open');
        clearTimeout(pop.webmzOtpCloseTimer);
        pop.webmzOtpCloseTimer = setTimeout(function () {
            pop.hidden = true;
            pop.classList.remove('is-closing');
        }, 260);
    }

    window.webmzOtpOpen = openPopup;
    window.webmzOtpClose = closePopup;

    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-webmz-otp-open], a[href*="webmz-login=1"]');
        if (opener) {
            e.preventDefault();
            var redirect = opener.getAttribute('data-webmz-otp-redirect') || opener.getAttribute('data-redirect') || currentUrl();
            openPopup(redirect);
            return;
        }
        if (e.target.closest('[data-webmz-otp-close]')) {
            e.preventDefault();
            closePopup();
            return;
        }

        var sendBtn = e.target.closest('[data-webmz-account-mobile-send]');
        var verifyBtn = e.target.closest('[data-webmz-account-mobile-verify]');
        if (!sendBtn && !verifyBtn) {
            return;
        }

        var root = e.target.closest('[data-webmz-otp-account-mobile]');
        if (!root) {
            return;
        }

        e.preventDefault();
        if (sendBtn) {
            requestAccountMobileCode(root);
        } else {
            verifyAccountMobileCode(root);
        }
    });


    function accountMobileMessage(root, text, type, debug) {
        var msgBox = qs(root, '[data-webmz-account-mobile-message]');
        if (!msgBox) { return; }
        var finalText = text || '';
        if (debug) {
            finalText += '\n\nجزئیات تست:\n' + debug;
        }
        msgBox.hidden = !finalText;
        msgBox.textContent = finalText;
        msgBox.className = 'webmz-otp-account-mobile__message is-' + (type || 'info');
    }

    function requestAccountMobileCode(root) {
        if (!root || root.getAttribute('data-webmz-otp-account-busy') === 'yes') { return; }

        var mobileInput = qs(root, '[data-webmz-account-new-mobile]');
        var codeInput = qs(root, '[data-webmz-account-mobile-code]');
        var sendBtn = qs(root, '[data-webmz-account-mobile-send]');
        var verifyBox = qs(root, '[data-webmz-account-mobile-verify-box]');
        var mobile = mobileInput ? mobileInput.value : '';

        if (!mobile) {
            accountMobileMessage(root, cfg.invalidMobile || 'شماره موبایل را به‌درستی وارد کنید.', 'error');
            return;
        }

        root.setAttribute('data-webmz-otp-account-busy', 'yes');
        if (sendBtn) { sendBtn.disabled = true; }
        accountMobileMessage(root, cfg.sending || 'در حال ارسال کد...', 'info');
        post('webmz_otp_account_mobile_request', { mobile: mobile }, root).then(function (res) {
            if (!res || !res.success) { throw res; }
            if (verifyBox) { verifyBox.hidden = false; }
            accountMobileMessage(root, res.data.message || cfg.sent || 'کد تأیید ارسال شد.', 'success');
            var otp = extractDemoOtp(res);
            if (otp) {
                fillOtpInput(codeInput, otp);
                setTimeout(function () { fillOtpInput(qs(root, '[data-webmz-account-mobile-code]'), otp); }, 50);
            }
            if (codeInput) { codeInput.focus(); }
        }).catch(function (err) {
            var data = err && err.data ? err.data : {};
            accountMobileMessage(root, data.message || cfg.error || 'خطایی رخ داد.', 'error', data.debug || '');
        }).finally(function () {
            root.removeAttribute('data-webmz-otp-account-busy');
            if (sendBtn) { sendBtn.disabled = false; }
        });
    }

    function verifyAccountMobileCode(root) {
        if (!root || root.getAttribute('data-webmz-otp-account-busy') === 'yes') { return; }

        var currentInput = qs(root, '[data-webmz-account-current-mobile]');
        var mobileInput = qs(root, '[data-webmz-account-new-mobile]');
        var codeInput = qs(root, '[data-webmz-account-mobile-code]');
        var verifyBtn = qs(root, '[data-webmz-account-mobile-verify]');
        var verifyBox = qs(root, '[data-webmz-account-mobile-verify-box]');
        var mobile = mobileInput ? mobileInput.value : '';
        var code = codeInput ? codeInput.value : '';

        if (!mobile || !code) {
            accountMobileMessage(root, 'شماره موبایل و کد تأیید را وارد کنید.', 'error');
            return;
        }

        root.setAttribute('data-webmz-otp-account-busy', 'yes');
        if (verifyBtn) { verifyBtn.disabled = true; }
        accountMobileMessage(root, cfg.verifying || 'در حال بررسی کد...', 'info');
        post('webmz_otp_account_mobile_verify', { mobile: mobile, code: code }, root).then(function (res) {
            if (!res || !res.success) { throw res; }
            if (currentInput) { currentInput.value = res.data.mobile || mobile; }
            if (mobileInput) { mobileInput.value = ''; }
            if (codeInput) { codeInput.value = ''; }
            if (verifyBox) { verifyBox.hidden = true; }
            accountMobileMessage(root, res.data.message || 'شماره موبایل با موفقیت تغییر کرد.', 'success');
        }).catch(function (err) {
            var data = err && err.data ? err.data : {};
            accountMobileMessage(root, data.message || cfg.error || 'کد تأیید صحیح نیست.', 'error', data.debug || '');
        }).finally(function () {
            root.removeAttribute('data-webmz-otp-account-busy');
            if (verifyBtn) { verifyBtn.disabled = false; }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') { return; }

        var input = e.target.closest('[data-webmz-account-new-mobile], [data-webmz-account-mobile-code]');
        if (!input) { return; }

        var root = input.closest('[data-webmz-otp-account-mobile]');
        if (!root) { return; }

        e.preventDefault();
        if (input.matches('[data-webmz-account-new-mobile]')) {
            requestAccountMobileCode(root);
        } else {
            verifyAccountMobileCode(root);
        }
    });

    function bootOtpAuth() {
        qsa(document, '[data-webmz-otp-auth]').forEach(bindForm);
        if (window.location.search.indexOf('webmz-login=1') !== -1) { openPopup(currentUrl()); }
    }

    document.addEventListener('webmz:login-required', function (event) {
        var redirect = event && event.detail && event.detail.redirect ? event.detail.redirect : currentUrl();
        openPopup(redirect);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootOtpAuth);
    } else {
        bootOtpAuth();
    }
})();
