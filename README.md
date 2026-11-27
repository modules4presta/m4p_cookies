# M4P Cookies Consent for PrestaShop 8 & 9

**A cookie banner that asks before anything optional runs, remembers the answer per category and tells your other scripts what the visitor chose.**

> **Meta description (155 chars):** Cookie consent banner for PrestaShop: accept, reject or choose per category, a preference panel and a JavaScript API for your tags. Free, MIT.

---

## Why cookie consent matters for your store

In the EU, analytics and marketing cookies need the visitor's consent before they are set, and
refusing has to be as easy as accepting. A banner with "accept" and nothing else, or with optional
categories ticked in advance, does not meet that.

A consent banner is also only as useful as the scripts that read it. Analytics, session recording
and advertising tags have to wait for the answer and stop when it is withdrawn.

- **Choice per category** – necessary, analytics and marketing, with "reject optional" next to "accept all"
- **Nothing ticked in advance** – optional categories start off, and the panel shows the stored choice
- **One answer for every tag** – a cookie, an event and a JavaScript object other scripts read

## What the module does

The module shows a banner at the bottom or top of every front-office page until the visitor
chooses. "Customise" opens a panel with the categories. The choice is stored in a first-party
cookie for the number of days set in the configuration, and a link in the page reopens the panel
at any time. The banner styles and script are embedded in the page, so it works before any theme
bundle that waits for the first interaction.

### Key features

- **Three categories** – necessary (always on), analytics and marketing; either optional category can be hidden
- **Texts per language** – title, text, buttons, category names and descriptions, the policy link label
- **Reopen from anywhere** – a link to `#cookies-settings` or any element with `data-m4p-cookies-open` opens the panel
- **Consent API** – the `m4p_cookies_consent` cookie, the `m4pCookiesConsent` event and `window.M4PCookies`
- **Light** – one small script with `defer`, no library, no requests to third parties
- **Themeable** – colours are CSS custom properties on `.m4p-cookies`
- **Accessible** – a labelled dialog, real buttons and checkboxes, transitions off with `prefers-reduced-motion`
- **Keyboard** – Escape rejects the optional categories, the same as the reject button

### What it does not do

The module does not block other scripts by itself and sends no Google Consent Mode signals. A tag
has to read the consent before it loads: the modules of the M4P family (Microsoft Clarity, PostHog,
conversion tags) do it already, and your own script can use the API below.

## Compatibility

| | |
|---|---|
| PrestaShop | 1.7.6 – 9.x |
| PHP | 7.4+ |
| Requirements | none |
| Multistore | Compatible – settings follow the shop context of the configuration page |
| Themes | Works with any theme – the banner is rendered on `displayBeforeBodyClosingTag` |

The module performs no core overrides and adds no database tables. It stores 18 settings.

## Installation

1. Upload and install the module from **Modules → Module Manager**.
2. In the configuration, enter the address of your privacy or cookie policy.
3. Check the texts in every language and switch off the categories your shop does not use.
4. Add a "Cookie settings" link to the footer pointing at `#cookies-settings`.

Settings stored under the `M4P_COOKIES_*` keys before the installation are kept; defaults are
written only for the missing ones.

## Configuration options

| Setting | Description |
|---|---|
| **Policy address** | Full `http://` or `https://` address of the policy page. Empty hides the link. |
| **Consent valid for (days)** | 365 by default. After that the banner asks again. |
| **Banner position** | Bottom (default) or top of the screen. |
| **Show the "Analytics" category** | On by default. Off when the shop runs no analytics. |
| **Show the "Marketing" category** | On by default. Off when the shop runs no advertising tags. |
| **Banner texts** | Title, text, the four buttons and the policy link label, per language. |
| **Cookie categories** | Name and description of each category, per language. |

## Reading the consent from your script

The cookie `m4p_cookies_consent` holds JSON: `{"v":1,"t":<timestamp>,"c":{"necessary":true,"analytics":false,"marketing":true}}`.

```js
// Current state, once the banner script has run
if (window.M4PCookies && window.M4PCookies.has('analytics')) {
    loadAnalytics();
}

// Every change, and the stored choice on page load (detail.restored is true then)
document.addEventListener('m4pCookiesConsent', function (event) {
    if (event.detail.categories.analytics) {
        loadAnalytics();
    }
});

// Open the preference panel
window.M4PCookies.open();
```

## Theming

Set the custom properties on `.m4p-cookies` in the theme stylesheet:

```css
.m4p-cookies {
    --m4p-cookies-primary: #0b69f6;
    --m4p-cookies-primary-hover: #0952c4;
    --m4p-cookies-link: #0b69f6;
}
```

The full list is at the top of `views/css/cookies.css`. Markup changes go into a template override
in `themes/<theme>/modules/m4p_cookies/views/templates/hook/banner.tpl`.

A template override that sets `aria-modal="true"` on `#m4p-cookies` turns the banner into a modal window:
focus moves into it when it opens, Tab stays inside it and focus returns to the previous element when it closes.

## Frequently asked questions

**Does it make my shop GDPR compliant on its own?**
No. It collects and stores consent; the policy text, the list of cookies and the tags that wait for
consent are up to the shop.

**Does it block Google Analytics or Meta Pixel automatically?**
No. A tag has to check the consent before it loads. The M4P modules for Clarity, PostHog and
conversion tags do; for other tags, use the event or `window.M4PCookies.has()`.

**Where is the consent stored?**
In the visitor's browser, in the `m4p_cookies_consent` cookie. The module stores nothing about
visitors on the server.

**How do visitors change their mind?**
With a link to `#cookies-settings`, usually in the footer. It opens the panel with the stored choice.

**What happens when I uninstall the module?**
Its settings are deleted. Visitors keep the cookie until it expires, and nothing reads it anymore.

---

**Keywords:** PrestaShop cookie banner, PrestaShop GDPR cookie consent, cookie consent PrestaShop 8, PrestaShop 9 cookies, RODO cookies PrestaShop, cookie preferences panel.

## License

MIT – see [LICENSE](LICENSE). Free to use commercially, fork and modify; keep the copyright notice.

## Contributing

Bug reports and pull requests are welcome – see [CONTRIBUTING.md](CONTRIBUTING.md). For security
issues, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

---

Built by [Nice Code](https://nice-code.com/pl/produkty/sklep-b2b-prestashop) – we build and maintain PrestaShop stores.

© Nice Code sp. z o.o. (Modules4Presta) – released under the MIT license.
