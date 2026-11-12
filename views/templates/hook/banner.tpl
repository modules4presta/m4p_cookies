{**
 * m4p_cookies
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 *}

{* Inline CSS and a deferred script of its own: the banner has to be styled and working before any
   theme bundle that waits for the first interaction, and a stylesheet link would block rendering. *}
<style>{file_get_contents($m4p_cookies.css_file) nofilter}</style>
<script defer src="{$m4p_cookies.js_url|escape:'html':'UTF-8'}"></script>

<div
    id="m4p-cookies"
    class="m4p-cookies m4p-cookies--{$m4p_cookies.position|escape:'html':'UTF-8'}"
    data-expires-days="{$m4p_cookies.expires_days|intval}"
    aria-live="polite"
    role="dialog"
    aria-labelledby="m4p-cookies-title"
    hidden
>
    <div class="m4p-cookies__backdrop" data-m4p-cookies-action="close" hidden></div>

    <div class="m4p-cookies__window" role="document">
        <div class="m4p-cookies__view m4p-cookies__view--banner" data-m4p-cookies-view="banner">
            <div class="m4p-cookies__body">
                <p id="m4p-cookies-title" class="m4p-cookies__title">
                    <span class="m4p-cookies__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                            <path d="M21.95 10.99a9 9 0 11-8.94-8.94 3.5 3.5 0 003.48 3.48 3.5 3.5 0 003.48 3.48c.71 0 1.37-.2 1.98-.54.07.33.11.67.11 1zM8 13a1.5 1.5 0 11-1.5-1.5A1.5 1.5 0 018 13zm2-5a1 1 0 11-1-1 1 1 0 011 1zm5 7a1.5 1.5 0 11-1.5-1.5 1.5 1.5 0 011.5 1.5zm-3 3.5a1 1 0 11-1-1 1 1 0 011 1zm6-3a1 1 0 11-1-1 1 1 0 011 1z"/>
                        </svg>
                    </span>
                    {$m4p_cookies.intro_title|escape:'html':'UTF-8'}
                </p>

                <p class="m4p-cookies__text">
                    {$m4p_cookies.intro_text|escape:'html':'UTF-8'}
                    {if $m4p_cookies.policy_url}
                        <a
                            class="m4p-cookies__policy"
                            href="{$m4p_cookies.policy_url|escape:'html':'UTF-8'}"
                            target="_blank"
                            rel="noopener"
                        >
                            {$m4p_cookies.policy_label|escape:'html':'UTF-8'}
                        </a>
                    {/if}
                </p>
            </div>

            <div class="m4p-cookies__actions">
                <button type="button" class="btn m4p-cookies__btn m4p-cookies__btn--ghost" data-m4p-cookies-action="customize">
                    {$m4p_cookies.customize_label|escape:'html':'UTF-8'}
                </button>
                <button type="button" class="btn m4p-cookies__btn m4p-cookies__btn--secondary" data-m4p-cookies-action="reject-all">
                    {$m4p_cookies.reject_all_label|escape:'html':'UTF-8'}
                </button>
                <button type="button" class="btn m4p-cookies__btn m4p-cookies__btn--primary" data-m4p-cookies-action="accept-all">
                    {$m4p_cookies.accept_all_label|escape:'html':'UTF-8'}
                </button>
            </div>
        </div>

        <div class="m4p-cookies__view m4p-cookies__view--prefs" data-m4p-cookies-view="prefs" hidden>
            <div class="m4p-cookies__body">
                <p class="m4p-cookies__title">{$m4p_cookies.customize_label|escape:'html':'UTF-8'}</p>

                <div class="m4p-cookies__categories">
                    {foreach from=$m4p_cookies.categories item=cat}
                        <div class="m4p-cookies__category">
                            <div class="m4p-cookies__category-head">
                                <span class="m4p-cookies__category-title">
                                    {$cat.title|escape:'html':'UTF-8'}
                                </span>

                                <label class="m4p-cookies__toggle {if $cat.required}m4p-cookies__toggle--locked{/if}">
                                    <input
                                        type="checkbox"
                                        data-m4p-cookies-category="{$cat.id|escape:'html':'UTF-8'}"
                                        {if $cat.required}checked disabled{/if}
                                    >
                                    <span class="m4p-cookies__toggle-slider" aria-hidden="true"></span>
                                </label>
                            </div>
                            {if $cat.desc}
                                <p class="m4p-cookies__category-desc">
                                    {$cat.desc|escape:'html':'UTF-8'}
                                </p>
                            {/if}
                        </div>
                    {/foreach}
                </div>
            </div>

            <div class="m4p-cookies__actions">
                <button type="button" class="btn m4p-cookies__btn m4p-cookies__btn--ghost" data-m4p-cookies-action="reject-all">
                    {$m4p_cookies.reject_all_label|escape:'html':'UTF-8'}
                </button>
                <button type="button" class="btn m4p-cookies__btn m4p-cookies__btn--secondary" data-m4p-cookies-action="save-prefs">
                    {$m4p_cookies.save_label|escape:'html':'UTF-8'}
                </button>
                <button type="button" class="btn m4p-cookies__btn m4p-cookies__btn--primary" data-m4p-cookies-action="accept-all">
                    {$m4p_cookies.accept_all_label|escape:'html':'UTF-8'}
                </button>
            </div>
        </div>
    </div>
</div>
