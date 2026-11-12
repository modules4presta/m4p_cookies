<?php

/**
 * m4p_cookies
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class M4p_Cookies extends Module
{
    public const CONFIG_INTRO_TITLE = 'M4P_COOKIES_INTRO_TITLE';
    public const CONFIG_INTRO_TEXT = 'M4P_COOKIES_INTRO_TEXT';

    public const CONFIG_ACCEPT_ALL_LABEL = 'M4P_COOKIES_ACCEPT_ALL_LABEL';
    public const CONFIG_REJECT_ALL_LABEL = 'M4P_COOKIES_REJECT_ALL_LABEL';
    public const CONFIG_CUSTOMIZE_LABEL = 'M4P_COOKIES_CUSTOMIZE_LABEL';
    public const CONFIG_SAVE_LABEL = 'M4P_COOKIES_SAVE_LABEL';

    public const CONFIG_NECESSARY_TITLE = 'M4P_COOKIES_NECESSARY_TITLE';
    public const CONFIG_NECESSARY_DESC = 'M4P_COOKIES_NECESSARY_DESC';
    public const CONFIG_ANALYTICS_TITLE = 'M4P_COOKIES_ANALYTICS_TITLE';
    public const CONFIG_ANALYTICS_DESC = 'M4P_COOKIES_ANALYTICS_DESC';
    public const CONFIG_MARKETING_TITLE = 'M4P_COOKIES_MARKETING_TITLE';
    public const CONFIG_MARKETING_DESC = 'M4P_COOKIES_MARKETING_DESC';

    public const CONFIG_POLICY_URL = 'M4P_COOKIES_POLICY_URL';
    public const CONFIG_POLICY_LABEL = 'M4P_COOKIES_POLICY_LABEL';
    public const CONFIG_EXPIRES_DAYS = 'M4P_COOKIES_EXPIRES_DAYS';
    public const CONFIG_POSITION = 'M4P_COOKIES_POSITION';
    public const CONFIG_ENABLE_ANALYTICS = 'M4P_COOKIES_ENABLE_ANALYTICS';
    public const CONFIG_ENABLE_MARKETING = 'M4P_COOKIES_ENABLE_MARKETING';

    private const DEFAULT_EXPIRES_DAYS = 365;
    private const POSITIONS = ['bottom', 'top'];

    /** Texts edited per language, in the order of the configuration forms. */
    private const LANG_KEYS = [
        self::CONFIG_INTRO_TITLE,
        self::CONFIG_INTRO_TEXT,
        self::CONFIG_ACCEPT_ALL_LABEL,
        self::CONFIG_REJECT_ALL_LABEL,
        self::CONFIG_CUSTOMIZE_LABEL,
        self::CONFIG_SAVE_LABEL,
        self::CONFIG_NECESSARY_TITLE,
        self::CONFIG_NECESSARY_DESC,
        self::CONFIG_ANALYTICS_TITLE,
        self::CONFIG_ANALYTICS_DESC,
        self::CONFIG_MARKETING_TITLE,
        self::CONFIG_MARKETING_DESC,
        self::CONFIG_POLICY_LABEL,
    ];

    public function __construct()
    {
        $this->name = 'm4p_cookies';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Modules4Presta';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('M4P Cookies Consent', [], 'Modules.M4pcookies.Admin');
        $this->description = $this->trans('A cookie consent banner with a preference panel and consent per category.', [], 'Modules.M4pcookies.Admin');
        $this->confirmUninstall = $this->trans('Uninstall the module? Its settings will be deleted.', [], 'Modules.M4pcookies.Admin');
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('displayBeforeBodyClosingTag')
            && $this->setDefaults();
    }

    public function uninstall(): bool
    {
        foreach ($this->getConfigKeys() as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    /**
     * @return string[]
     */
    private function getConfigKeys(): array
    {
        return array_merge(self::LANG_KEYS, [
            self::CONFIG_POLICY_URL,
            self::CONFIG_EXPIRES_DAYS,
            self::CONFIG_POSITION,
            self::CONFIG_ENABLE_ANALYTICS,
            self::CONFIG_ENABLE_MARKETING,
        ]);
    }

    /**
     * Default banner texts in the given locale, from the module's translation catalogues.
     *
     * @param string $locale
     *
     * @return array<string, string>
     */
    private function getDefaultTexts(string $locale): array
    {
        return [
            self::CONFIG_INTRO_TITLE => $this->trans('We respect your privacy', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_INTRO_TEXT => $this->trans('We use cookies to make the shop work. With your consent we also use analytics and marketing cookies. You can change your choice at any time with the "Cookie settings" link in the footer.', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_ACCEPT_ALL_LABEL => $this->trans('Accept all', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_REJECT_ALL_LABEL => $this->trans('Reject optional', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_CUSTOMIZE_LABEL => $this->trans('Customise settings', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_SAVE_LABEL => $this->trans('Save my preferences', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_NECESSARY_TITLE => $this->trans('Necessary (required)', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_NECESSARY_DESC => $this->trans('They keep the cart, the login, the choice of language and currency, and your cookie choices working. They cannot be switched off.', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_ANALYTICS_TITLE => $this->trans('Analytics and statistics', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_ANALYTICS_DESC => $this->trans('They show how visitors use the shop: which pages they view, where they come from and how long they stay. They are used to improve the shop.', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_MARKETING_TITLE => $this->trans('Marketing and advertising', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_MARKETING_DESC => $this->trans('They measure how advertising campaigns perform and let ads match your interests.', [], 'Modules.M4pcookies.Shop', $locale),
            self::CONFIG_POLICY_LABEL => $this->trans('Cookie policy', [], 'Modules.M4pcookies.Shop', $locale),
        ];
    }

    /**
     * Writes the defaults of the settings that are not stored yet, so a value saved
     * before the installation is kept.
     *
     * @return bool
     */
    private function setDefaults(): bool
    {
        $languages = Language::getLanguages(false);
        $texts = [];
        foreach ($languages as $lang) {
            $texts[(int) $lang['id_lang']] = $this->getDefaultTexts((string) $lang['locale']);
        }

        foreach (self::LANG_KEYS as $key) {
            if (Configuration::hasKey($key)) {
                continue;
            }
            $values = [];
            foreach ($texts as $idLang => $defaults) {
                $values[$idLang] = $defaults[$key];
            }
            Configuration::updateValue($key, $values, true);
        }

        $scalars = [
            self::CONFIG_POLICY_URL => '',
            self::CONFIG_EXPIRES_DAYS => self::DEFAULT_EXPIRES_DAYS,
            self::CONFIG_POSITION => 'bottom',
            self::CONFIG_ENABLE_ANALYTICS => 1,
            self::CONFIG_ENABLE_MARKETING => 1,
        ];
        foreach ($scalars as $key => $value) {
            if (!Configuration::hasKey($key)) {
                Configuration::updateValue($key, $value);
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return string
     */
    public function hookDisplayBeforeBodyClosingTag(array $params): string
    {
        $idLang = (int) $this->context->language->id;
        $categories = [[
            'id' => 'necessary',
            'title' => Configuration::get(self::CONFIG_NECESSARY_TITLE, $idLang),
            'desc' => Configuration::get(self::CONFIG_NECESSARY_DESC, $idLang),
            'required' => true,
        ]];

        if ((int) Configuration::get(self::CONFIG_ENABLE_ANALYTICS) === 1) {
            $categories[] = [
                'id' => 'analytics',
                'title' => Configuration::get(self::CONFIG_ANALYTICS_TITLE, $idLang),
                'desc' => Configuration::get(self::CONFIG_ANALYTICS_DESC, $idLang),
                'required' => false,
            ];
        }

        if ((int) Configuration::get(self::CONFIG_ENABLE_MARKETING) === 1) {
            $categories[] = [
                'id' => 'marketing',
                'title' => Configuration::get(self::CONFIG_MARKETING_TITLE, $idLang),
                'desc' => Configuration::get(self::CONFIG_MARKETING_DESC, $idLang),
                'required' => false,
            ];
        }

        $expires = (int) Configuration::get(self::CONFIG_EXPIRES_DAYS);
        $position = (string) Configuration::get(self::CONFIG_POSITION);

        $this->context->smarty->assign([
            'm4p_cookies' => [
                'css_file' => _PS_MODULE_DIR_ . $this->name . '/views/css/cookies.css',
                'js_url' => $this->_path . 'views/js/cookies.js?v=' . $this->version,
                'intro_title' => Configuration::get(self::CONFIG_INTRO_TITLE, $idLang),
                'intro_text' => Configuration::get(self::CONFIG_INTRO_TEXT, $idLang),
                'accept_all_label' => Configuration::get(self::CONFIG_ACCEPT_ALL_LABEL, $idLang),
                'reject_all_label' => Configuration::get(self::CONFIG_REJECT_ALL_LABEL, $idLang),
                'customize_label' => Configuration::get(self::CONFIG_CUSTOMIZE_LABEL, $idLang),
                'save_label' => Configuration::get(self::CONFIG_SAVE_LABEL, $idLang),
                'policy_url' => Configuration::get(self::CONFIG_POLICY_URL),
                'policy_label' => Configuration::get(self::CONFIG_POLICY_LABEL, $idLang),
                'expires_days' => $expires > 0 ? $expires : self::DEFAULT_EXPIRES_DAYS,
                'position' => in_array($position, self::POSITIONS, true) ? $position : 'bottom',
                'categories' => $categories,
            ],
        ]);

        return $this->display(__FILE__, 'views/templates/hook/banner.tpl');
    }

    public function getContent(): string
    {
        $output = '';

        if (Tools::isSubmit('submit_' . $this->name)) {
            $output .= $this->postProcess();
        }

        return $output . $this->renderForm();
    }

    private function postProcess(): string
    {
        $policyUrl = trim((string) Tools::getValue(self::CONFIG_POLICY_URL, ''));
        if ($policyUrl !== '' && (!Validate::isAbsoluteUrl($policyUrl) || !preg_match('#^https?://#i', $policyUrl))) {
            return $this->displayError($this->trans('The policy address must be a full http:// or https:// address.', [], 'Modules.M4pcookies.Admin'));
        }

        foreach (self::LANG_KEYS as $field) {
            $values = [];
            foreach (Language::getLanguages(false) as $lang) {
                $idLang = (int) $lang['id_lang'];
                $values[$idLang] = (string) Tools::getValue($field . '_' . $idLang, '');
            }
            Configuration::updateValue($field, $values, true);
        }

        Configuration::updateValue(self::CONFIG_POLICY_URL, $policyUrl);

        $expires = (int) Tools::getValue(self::CONFIG_EXPIRES_DAYS, self::DEFAULT_EXPIRES_DAYS);
        Configuration::updateValue(self::CONFIG_EXPIRES_DAYS, $expires > 0 ? $expires : self::DEFAULT_EXPIRES_DAYS);

        $position = (string) Tools::getValue(self::CONFIG_POSITION, 'bottom');
        Configuration::updateValue(self::CONFIG_POSITION, in_array($position, self::POSITIONS, true) ? $position : 'bottom');

        Configuration::updateValue(self::CONFIG_ENABLE_ANALYTICS, (int) Tools::getValue(self::CONFIG_ENABLE_ANALYTICS, 0) ? 1 : 0);
        Configuration::updateValue(self::CONFIG_ENABLE_MARKETING, (int) Tools::getValue(self::CONFIG_ENABLE_MARKETING, 0) ? 1 : 0);

        return $this->displayConfirmation($this->trans('Settings saved.', [], 'Modules.M4pcookies.Admin'));
    }

    private function renderForm(): string
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'submit_' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;

        $helper->fields_value = $this->getFormValues();
        $helper->languages = $this->context->controller->getLanguages();

        return $helper->generateForm([
            $this->getGeneralForm(),
            $this->getBannerCopyForm(),
            $this->getCategoriesForm(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function submitButton(): array
    {
        return [
            'title' => $this->trans('Save', [], 'Admin.Actions'),
            'class' => 'btn btn-default pull-right',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getGeneralForm(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('General settings', [], 'Modules.M4pcookies.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Policy address', [], 'Modules.M4pcookies.Admin'),
                        'name' => self::CONFIG_POLICY_URL,
                        'desc' => $this->trans('Full address of the privacy or cookie policy page. Leave it empty to hide the link.', [], 'Modules.M4pcookies.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Consent valid for (days)', [], 'Modules.M4pcookies.Admin'),
                        'name' => self::CONFIG_EXPIRES_DAYS,
                        'class' => 'fixed-width-sm',
                        'required' => true,
                    ],
                    [
                        'type' => 'radio',
                        'label' => $this->trans('Banner position', [], 'Modules.M4pcookies.Admin'),
                        'name' => self::CONFIG_POSITION,
                        'values' => [
                            ['id' => 'pos_bottom', 'value' => 'bottom', 'label' => $this->trans('Bottom', [], 'Modules.M4pcookies.Admin')],
                            ['id' => 'pos_top', 'value' => 'top', 'label' => $this->trans('Top', [], 'Modules.M4pcookies.Admin')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Show the "Analytics" category', [], 'Modules.M4pcookies.Admin'),
                        'name' => self::CONFIG_ENABLE_ANALYTICS,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'analytics_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                            ['id' => 'analytics_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Show the "Marketing" category', [], 'Modules.M4pcookies.Admin'),
                        'name' => self::CONFIG_ENABLE_MARKETING,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'marketing_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                            ['id' => 'marketing_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                ],
                'submit' => $this->submitButton(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getBannerCopyForm(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Banner texts', [], 'Modules.M4pcookies.Admin'),
                    'icon' => 'icon-edit',
                ],
                'input' => [
                    ['type' => 'text', 'lang' => true, 'required' => true, 'name' => self::CONFIG_INTRO_TITLE,
                        'label' => $this->trans('Title', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'textarea', 'lang' => true, 'required' => true, 'name' => self::CONFIG_INTRO_TEXT,
                        'label' => $this->trans('Text', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'required' => true, 'name' => self::CONFIG_ACCEPT_ALL_LABEL,
                        'label' => $this->trans('Button: accept all', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'required' => true, 'name' => self::CONFIG_REJECT_ALL_LABEL,
                        'label' => $this->trans('Button: reject optional', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'required' => true, 'name' => self::CONFIG_CUSTOMIZE_LABEL,
                        'label' => $this->trans('Button: customise', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'required' => true, 'name' => self::CONFIG_SAVE_LABEL,
                        'label' => $this->trans('Button: save the choice', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'name' => self::CONFIG_POLICY_LABEL,
                        'label' => $this->trans('Policy link label', [], 'Modules.M4pcookies.Admin')],
                ],
                'submit' => $this->submitButton(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getCategoriesForm(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Cookie categories', [], 'Modules.M4pcookies.Admin'),
                    'icon' => 'icon-list',
                ],
                'input' => [
                    ['type' => 'text', 'lang' => true, 'required' => true, 'name' => self::CONFIG_NECESSARY_TITLE,
                        'label' => $this->trans('Necessary – name', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'textarea', 'lang' => true, 'name' => self::CONFIG_NECESSARY_DESC,
                        'label' => $this->trans('Necessary – description', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'name' => self::CONFIG_ANALYTICS_TITLE,
                        'label' => $this->trans('Analytics – name', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'textarea', 'lang' => true, 'name' => self::CONFIG_ANALYTICS_DESC,
                        'label' => $this->trans('Analytics – description', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'text', 'lang' => true, 'name' => self::CONFIG_MARKETING_TITLE,
                        'label' => $this->trans('Marketing – name', [], 'Modules.M4pcookies.Admin')],
                    ['type' => 'textarea', 'lang' => true, 'name' => self::CONFIG_MARKETING_DESC,
                        'label' => $this->trans('Marketing – description', [], 'Modules.M4pcookies.Admin')],
                ],
                'submit' => $this->submitButton(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getFormValues(): array
    {
        $values = [
            self::CONFIG_POLICY_URL => Configuration::get(self::CONFIG_POLICY_URL),
            self::CONFIG_EXPIRES_DAYS => Configuration::get(self::CONFIG_EXPIRES_DAYS),
            self::CONFIG_POSITION => Configuration::get(self::CONFIG_POSITION),
            self::CONFIG_ENABLE_ANALYTICS => Configuration::get(self::CONFIG_ENABLE_ANALYTICS),
            self::CONFIG_ENABLE_MARKETING => Configuration::get(self::CONFIG_ENABLE_MARKETING),
        ];

        foreach (self::LANG_KEYS as $field) {
            foreach (Language::getLanguages(false) as $lang) {
                $idLang = (int) $lang['id_lang'];
                $values[$field][$idLang] = Configuration::get($field, $idLang);
            }
        }

        return $values;
    }
}
