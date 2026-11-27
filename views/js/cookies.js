/**
 * m4p_cookies
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

(function () {
    'use strict';

    // A theme that also bundles this file must not bind the banner twice.
    if (window.__m4pCookiesLoaded) { return; }
    window.__m4pCookiesLoaded = true;

    var COOKIE_NAME = 'm4p_cookies_consent';
    var CONSENT_VERSION = 1;

    function readCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function writeCookie(name, value, days) {
        var d = new Date();
        d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie =
            name + '=' + encodeURIComponent(value) +
            '; expires=' + d.toUTCString() +
            '; path=/; SameSite=Lax';
    }

    function parseConsent(raw) {
        if (!raw) { return null; }
        try {
            var parsed = JSON.parse(raw);
            if (parsed && parsed.v === CONSENT_VERSION && parsed.c) {
                return parsed;
            }
        } catch (e) { /* stale/invalid cookie */ }
        return null;
    }

    function getCategoryCheckboxes(root) {
        return Array.prototype.slice.call(
            root.querySelectorAll('[data-m4p-cookies-category]')
        );
    }

    function setView(root, name) {
        var views = root.querySelectorAll('[data-m4p-cookies-view]');
        for (var i = 0; i < views.length; i++) {
            views[i].hidden = views[i].getAttribute('data-m4p-cookies-view') !== name;
        }
        if (name === 'prefs') {
            root.classList.add('m4p-cookies--modal');
            var backdrop = root.querySelector('.m4p-cookies__backdrop');
            if (backdrop) { backdrop.hidden = false; }
            document.body.classList.add('m4p-cookies-no-scroll');
        } else {
            root.classList.remove('m4p-cookies--modal');
            var bd = root.querySelector('.m4p-cookies__backdrop');
            if (bd) { bd.hidden = true; }
            document.body.classList.remove('m4p-cookies-no-scroll');
        }
    }

    var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])';
    var returnFocus = null;

    function isModal(root) {
        return root.getAttribute('aria-modal') === 'true';
    }

    function showBanner(root) {
        root.hidden = false;
        if (isModal(root)) {
            returnFocus = document.activeElement;
        }
        requestAnimationFrame(function () {
            root.classList.add('m4p-cookies--visible');
            if (isModal(root)) {
                (root.querySelector('[role="document"]') || root).focus({ preventScroll: true });
            }
        });
    }

    function hideBanner(root) {
        root.classList.remove('m4p-cookies--visible');
        document.body.classList.remove('m4p-cookies-no-scroll');
        setTimeout(function () { root.hidden = true; }, 260);
        if (returnFocus && typeof returnFocus.focus === 'function') {
            returnFocus.focus({ preventScroll: true });
        }
        returnFocus = null;
    }

    function trapTab(root, event) {
        var items = Array.prototype.filter.call(root.querySelectorAll(FOCUSABLE), function (el) {
            return el.offsetParent !== null;
        });
        if (!items.length) { return; }
        var first = items[0];
        var last = items[items.length - 1];
        var inside = root.contains(document.activeElement);
        if (event.shiftKey && (document.activeElement === first || !inside)) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && (document.activeElement === last || !inside)) {
            event.preventDefault();
            first.focus();
        }
    }

    function collectCategories(root) {
        var checkboxes = getCategoryCheckboxes(root);
        var result = {};
        for (var i = 0; i < checkboxes.length; i++) {
            result[checkboxes[i].getAttribute('data-m4p-cookies-category')] =
                !!checkboxes[i].checked;
        }
        return result;
    }

    function syncCheckboxes(root, consent) {
        var checkboxes = getCategoryCheckboxes(root);
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].disabled) { continue; }
            var id = checkboxes[i].getAttribute('data-m4p-cookies-category');
            checkboxes[i].checked = !!(consent && consent.c && consent.c[id]);
        }
    }

    function applyChoice(root, choice) {
        var checkboxes = getCategoryCheckboxes(root);
        for (var i = 0; i < checkboxes.length; i++) {
            var id = checkboxes[i].getAttribute('data-m4p-cookies-category');
            var required = checkboxes[i].disabled;
            if (choice === 'all') {
                checkboxes[i].checked = true;
            } else if (choice === 'none') {
                checkboxes[i].checked = required;
            }
            /* 'current' leaves state as-is */
        }
    }

    function save(root, days, categories) {
        var payload = {
            v: CONSENT_VERSION,
            t: Date.now(),
            c: categories
        };
        writeCookie(COOKIE_NAME, JSON.stringify(payload), days);

        window.M4PCookies.consent = payload;

        document.dispatchEvent(new CustomEvent('m4pCookiesConsent', {
            detail: { consent: payload, categories: categories }
        }));

        hideBanner(root);
    }

    // Public API
    window.M4PCookies = {
        consent: null,
        has: function (cat) {
            return !!(this.consent && this.consent.c && this.consent.c[cat]);
        },
        open: function () {
            var root = document.getElementById('m4p-cookies');
            if (!root) { return; }
            syncCheckboxes(root, this.consent);
            setView(root, 'prefs');
            showBanner(root);
        }
    };

    function init() {
        var root = document.getElementById('m4p-cookies');
        if (!root) { return; }

        var days = parseInt(root.getAttribute('data-expires-days'), 10) || 365;

        root.addEventListener('click', function (event) {
            var target = event.target.closest('[data-m4p-cookies-action]');
            if (!target) { return; }
            event.preventDefault();

            var action = target.getAttribute('data-m4p-cookies-action');

            switch (action) {
                case 'customize':
                    setView(root, 'prefs');
                    break;

                case 'accept-all':
                    applyChoice(root, 'all');
                    save(root, days, collectCategories(root));
                    break;

                case 'reject-all':
                    applyChoice(root, 'none');
                    save(root, days, collectCategories(root));
                    break;

                case 'save-prefs':
                    save(root, days, collectCategories(root));
                    break;

                case 'close':
                    if (window.M4PCookies.consent) {
                        hideBanner(root);
                    } else {
                        setView(root, 'banner');
                    }
                    break;
            }
        });

        // Escape counts as rejecting the optional categories, the same as the reject button.
        document.addEventListener('keydown', function (event) {
            if (root.hidden || !root.classList.contains('m4p-cookies--visible')) { return; }
            if (event.key === 'Escape') {
                event.preventDefault();
                applyChoice(root, 'none');
                save(root, days, collectCategories(root));
            } else if (event.key === 'Tab' && isModal(root)) {
                trapTab(root, event);
            }
        });

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('[data-m4p-cookies-open], a[href$="#cookies-settings"]');
            if (!trigger) { return; }
            event.preventDefault();
            window.M4PCookies.open();
        });

        var existing = parseConsent(readCookie(COOKIE_NAME));
        if (existing) {
            window.M4PCookies.consent = existing;
            // fire an event so dependent scripts know the current state
            document.dispatchEvent(new CustomEvent('m4pCookiesConsent', {
                detail: { consent: existing, categories: existing.c, restored: true }
            }));
            if (window.location.hash === '#cookies-settings') {
                window.M4PCookies.open();
            }
            return;
        }

        syncCheckboxes(root, null);
        setView(root, 'banner');
        showBanner(root);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
