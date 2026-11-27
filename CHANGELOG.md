# Changelog

All notable changes to this module are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the module uses
[semantic versioning](https://semver.org/).

## [1.2.0] - 2026-11-25

### Added

- Escape rejects the optional categories, the same as the reject button.
- With `aria-modal="true"` on `#m4p-cookies` (set in a template override) the banner works as a modal window:
  focus moves into it when it opens, Tab stays inside it and focus returns to the previous element when it closes.

## [1.1.1] - 2026-11-19

### Changed

- Buttons use the theme's small radius `--r-sm` (4 px by default) and a 1.5 px line; the preferences window
  and the category boxes use the theme's `--r-lg` (10 px by default).

## [1.1.0] - 2026-11-12

First release under the MIT license, as `m4p_cookies`. Versions 1.0.x were published inside a shop as
`m4pcookies`; the configuration keys, the consent cookie and the JavaScript API are the same.

### Added

- English and Polish translation catalogues; the default banner texts are written in the language of
  each shop language on installation.
- CSS custom properties on `.m4p-cookies` (`--m4p-cookies-primary`, `--m4p-cookies-link`,
  `--m4p-cookies-text` and others), so a theme restyles the banner without overriding its rules.
- `prefers-reduced-motion: reduce` turns the banner transitions off.

### Changed

- The module directory, class and technical name are `m4p_cookies`.
- Installation keeps the settings already stored under the `M4P_COOKIES_*` keys and writes defaults
  only for the missing ones.
- The policy address accepts a full `http://` or `https://` address.
- The banner title is a paragraph, so the page keeps its own heading structure.
- The script URL carries the module version.

## [1.0.1] - 2026-11-12

### Added

- A link to `#cookies-settings` or an element with `data-m4p-cookies-open` opens the preference
  panel; a page opened with `#cookies-settings` shows it straight away.

### Changed

- Optional categories start unticked; the panel shows the consent stored for the visitor.
- Shorter default texts of the banner and the category descriptions.

## [1.0.0] - 2026-06-07

### Added

- A cookie banner with "accept all", "reject optional" and "customise", and a preference panel with
  the necessary, analytics and marketing categories.
- The consent stored in the `m4p_cookies_consent` cookie, the `m4pCookiesConsent` event and the
  `window.M4PCookies` object for other scripts.
- Banner and category texts per language, the policy link, the consent lifetime and the banner
  position in the configuration.
