<?php

/**
 * Nitron Prestashop (https://nitron.pro)
 *
 * @copyright Copyright (c) Nitron.LTD 2023.
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @link https://nitron.pro
 * @author Mahdi Shad <ramtin2025@yahoo.com>
 * @update 13/07/23, 18:38 PM
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Nitron_All_Products extends Module
{
    public $ps_url;
    public $templateFile;
    public $theme;

    const THEMES = [
        [
            'name' => 'default',
        ],
        [
            'name' => 'panda',
        ]
    ];
    public function __construct()
    {
        $this->name = 'nitron_all_products';
        $this->tab = 'seo';
        $this->version = '1.0.13';
        $this->author = 'Nitron.Pro';
        $this->need_instance = false;
        $this->bootstrap = true;
        parent::__construct();
        $this->theme = Configuration::get('NITRON_NAP_THEME') ?? self::THEMES[0]['name'];
        if ($this->context->link == null) {
            $protocolPrefix = Tools::getCurrentUrlProtocolPrefix();
            $this->context->link = new Link($protocolPrefix, $protocolPrefix);
        }
        $this->displayName = $this->trans('All product list', [], 'Modules.Nitronallproducts.Admin');
        $this->description = $this->trans(
            'A new page to provide all your products in one page.', [],
            'Modules.Nitronallproducts.Admin');
        if (!$this->_path) {
            $this->_path = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        }

        // Confirm uninstall
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall this module?', [], 'Modules.Nitronallproducts.Admin');
        $this->ps_url = $this->context->link->getBaseLink();
        $this->ps_versions_compliancy = ['min' => '1.7.7', 'max' => _PS_VERSION_];
        $this->templateFile = 'module:' . $this->name . '/views/templates/front/products.tpl';
    }

    /**
     * install pre-config
     *
     * @return bool
     */
    public function install()
    {
        Configuration::updateValue('NITRON_NAP_SHOW_IMAGES', 1);
        Configuration::updateValue('NITRON_NAP_SHOW_QTY', 1);
        Configuration::updateValue('NITRON_NAP_SHOW_NB', 100);
        Configuration::updateValue('NITRON_NAP_SHOW_IMG_TYPE', 'home_default');
        Configuration::updateValue('NITRON_NAP_THEME', 'default');
        Configuration::updateValue('NITRON_NAP_ROUTE', 'all-products');

        // Hooks
        if (parent::install() &&
            $this->registerHook('moduleRoutes')
        ) {
            return true;
        }

        $this->_errors[] = $this->trans(
            'There was an error during the installation. Please <a href="https://github.com/Nitron/PrestaShop/issues">
                open an issue</a> on the Nitron module project.', [], 'Modules.Nitronallproducts.Admin');
        return false;
    }

    /**
     * Uninstall module configuration
     *
     * @return bool
     */
    public function uninstall()
    {
        Configuration::deleteByName('NITRON_NAP_SHOW_IMAGES');
        Configuration::deleteByName('NITRON_NAP_SHOW_QTY');
        Configuration::deleteByName('NITRON_NAP_THEME');
        Configuration::deleteByName('NITRON_NAP_ROUTE');
        Configuration::deleteByName('NITRON_NAP_SHOW_NB');
        Configuration::deleteByName('NITRON_NAP_SHOW_IMG_TYPE');
        if (parent::uninstall()) {
            return true;
        }
        $this->_errors[] = $this->trans(
            'There was an error during the uninstallation. Please <a href="https://github.com/Nitron/PrestaShop/issues">
                open an issue</a> on the Nitron module project.', [], 'Modules.Nitronallproducts.Admin');
        return false;
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getContent()
    {
        return $this->postProcess() . $this->renderForm();
    }

    public function postProcess()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {
            $languages = Language::getLanguages(1, 0, 1);
            foreach ($this->configs() as $config => $val) {
                $html = 0;
                if (!is_array($val)) {
                    $value = Tools::getValue($config);
                } else {
                    $value = array();
                    $html = substr($config, -4) == 'html' ? 1 : 0;
                    foreach ($languages as $language) {
                        $value[$language] = Tools::getValue($config . '_' . $language);
                    }
                }
                if (!is_null($value) && $value != '') {
                    Configuration::updateValue($config, $value, $html);
                }
            }
            $output .= $this->displayConfirmation($this->trans('Your settings have been updated.', [], 'Modules.Nitronallproducts.Admin'));
        }
        return $output;
    }

    private function configs()
    {
        return [
            'NITRON_NAP_SHOW_IMAGES' => 1,
            'NITRON_NAP_SHOW_QTY' => 1,
            'NITRON_NAP_THEME' => self::THEMES[0]['name'],
            'NITRON_NAP_ROUTE' => 'all-products',
            'NITRON_NAP_SHOW_NB' => 100,
            'NITRON_NAP_SHOW_IMG_TYPE' => 'home_default'
        ];
    }

    public function renderForm()
    {
        //$employees = $this->duplicateArrayKeys(Employee::getEmployees(false), 'id_employee');
        $imageTypes = ImageType::getImagesTypes();
        foreach ($imageTypes as $key => $type) {
            if (empty($type['products'])) {
                unset($imageTypes[$key]);
            }
        }


        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Nitronallproducts.Main'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Route', [], 'Modules.Nitronallproducts.Main'),
                        'name' => 'NITRON_NAP_ROUTE',
                    ], [
                        'type' => 'select',
                        'label' => $this->trans('Theme', [], 'Modules.Nitronallproducts.Main'),
                        'name' => 'NITRON_NAP_THEME',
                        'options' => [
                            'query' => self::THEMES,
                            'id' => 'name',
                            'name' => 'name'
                        ],
                    ], [
                        'type' => 'text',
                        'label' => $this->trans('Number per page', [], 'Modules.Nitronallproducts.Main'),
                        'name' => 'NITRON_NAP_SHOW_NB',
                    ], [
                        'type' => 'select',
                        'label' => $this->trans('Image type', [], 'Modules.Nitronallproducts.Main'),
                        'name' => 'NITRON_NAP_SHOW_IMG_TYPE',
                        'options' => [
                            'query' => $imageTypes,
                            'id' => 'name',
                            'name' => 'name'
                        ],
                    ], [
                        'type' => 'switch',
                        'label' => $this->trans('Display images', [], 'Modules.Nitronallproducts.Main'),
                        'name' => 'NITRON_NAP_SHOW_IMAGES',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                    ], [
                        'type' => 'switch',
                        'label' => $this->trans('Display quantities', [], 'Modules.Nitronallproducts.Main'),
                        'name' => 'NITRON_NAP_SHOW_QTY',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                    ], [
                        'type' => 'html',
                        'name' => 'html_data',
                        'html_content' => '
                            <div class="alert alert-info">
                                All rights reserved by Nitron
                            </div>
                        '
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ]
            ]
        ];
        $fields_forms = array($fields_form);
        return $this->setHelper($fields_forms);
    }

    public function setHelper($fields_forms, $submitAction = null)
    {
        $helper = new HelperForm();
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->table = $this->table;
        $helper->show_toolbar = false;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $this->fields_form = array();
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'iso_code' => $lang['iso_code'],
                'id_lang' => $lang['id_lang'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
        $helper->identifier = $this->identifier;
        $helper->submit_action = $submitAction ?: 'submit' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure='
            . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            /*'tabs' => array(
                0 => array(
                    'tab1' => $this->l('General'),
                    'tab2' => $this->l('frontend'),
                    'tab3' => $this->l('backend'),
                    'tab4' => $this->l('Email'),
                    'tab5' => $this->l('Chat'),
                ),
            ),*/
        );
        // End of helper form settings
        return $helper->generateForm($fields_forms);
    }

    private function getConfigFieldsValues()
    {
        $return = [];
        foreach ($this->configs() as $name => $value) {
            if (!is_array($value)) {
                $return = $return + [$name => Configuration::get($name)];
            } else {
                $lang_values = [];
                foreach (Language::getLanguages(1, 0, 1) as $language) {
                    $lang_values[$language] = Configuration::get($name, $language);
                }
                $return = $return + [$name => $lang_values];
            }
        }
        return $return;
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }
    public function hookModuleRoutes($params)
    {
        return [
            'nitron_nap' => [
                'controller' => 'products',
                'rule' => Configuration::get('NITRON_NAP_ROUTE'),
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'nitron_all_products',
                    'controller' => 'products',
                ]
            ]
        ];
    }
}
