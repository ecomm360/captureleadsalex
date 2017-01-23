<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Captureleadsalex extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'captureleadsalex';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Alex Rey Rosa';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('captureleadsalex');
        $this->description = $this->l('Modulo para ecomm360');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('CAPTURELEADSALEX_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('displayRightColumn');
    }

    public function uninstall()
    {
        Configuration::deleteByName('CAPTURELEADSALEX_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCaptureleadsalexModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCaptureleadsalexModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'CAPTURELEADSALEX_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    /*
                     * Mostrar las el modulo en la columna izquierda.
                     * */

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar en columna izquierda'),
                        'name' => 'CAPTURELEADSALEX_COL_LEFT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    /*
                     * Mostrar las el modulo en la columna izquierda.
                     * */

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar en columna derecha'),
                        'name' => 'CAPTURELEADSALEX_COL_RIGHT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'CAPTURELEADSALEX_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'CAPTURELEADSALEX_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CAPTURELEADSALEX_LIVE_MODE' => Configuration::get('CAPTURELEADSALEX_LIVE_MODE', true),
            'CAPTURELEADSALEX_ACCOUNT_EMAIL' => Configuration::get('CAPTURELEADSALEX_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'CAPTURELEADSALEX_ACCOUNT_PASSWORD' => Configuration::get('CAPTURELEADSALEX_ACCOUNT_PASSWORD', null),
            'CAPTURELEADSALEX_COL_LEFT' => Configuration::get('CAPTURELEADSALEX_COL_LEFT', true),
            'CAPTURELEADSALEX_COL_RIGHT' => Configuration::get('CAPTURELEADSALEX_COL_RIGHT', false),
            'CAPTURELEADSALEX_DIS_ITEMS_COUNT' => Configuration::get('CAPTURELEADSALEX_DIS_ITEMS_COUNT', 3)
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    private function showLastVieweds($params) {
        $productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(array_reverse(explode(',', $params['cookie']->viewed)), 0, Configuration::get('CAPTURELEADSALEX_DIS_ITEMS_COUNT')) : array();
        if (count($productsViewed))
        {
            $defaultCover = Language::getIsoById($params['cookie']->id_lang).'-default';
            $productIds = implode(',', array_map('intval', $productsViewed));
            $productsImages = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT MAX(image_shop.id_image) id_image, p.id_product, il.legend, product_shop.active, pl.name, pl.description_short, pl.link_rewrite, cl.link_rewrite AS category_rewrite
			FROM '._DB_PREFIX_.'product p
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').')
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product)'.
                Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (il.id_image = image_shop.id_image AND il.id_lang = '.(int)($params['cookie']->id_lang).')
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
			WHERE p.id_product IN ('.$productIds.')
			AND pl.id_lang = '.(int)($params['cookie']->id_lang).'
			AND cl.id_lang = '.(int)($params['cookie']->id_lang).'
			GROUP BY product_shop.id_product'
            );
            $productsImagesArray = array();
            foreach ($productsImages as $pi)
                $productsImagesArray[$pi['id_product']] = $pi;
            $productsViewedObj = array();
            foreach ($productsViewed as $productViewed)
            {
                $obj = (object)'Product';
                if (!isset($productsImagesArray[$productViewed]) || (!$obj->active = $productsImagesArray[$productViewed]['active']))
                    continue;
                else
                {
                    $obj->id = (int)($productsImagesArray[$productViewed]['id_product']);
                    $obj->id_image = (int)$productsImagesArray[$productViewed]['id_image'];
                    $obj->cover = (int)($productsImagesArray[$productViewed]['id_product']).'-'.(int)($productsImagesArray[$productViewed]['id_image']);
                    $obj->legend = $productsImagesArray[$productViewed]['legend'];
                    $obj->name = $productsImagesArray[$productViewed]['name'];
                    $obj->description_short = $productsImagesArray[$productViewed]['description_short'];
                    $obj->link_rewrite = $productsImagesArray[$productViewed]['link_rewrite'];
                    $obj->category_rewrite = $productsImagesArray[$productViewed]['category_rewrite'];
                    // $obj is not a real product so it cannot be used as argument for getProductLink()
                    $obj->product_link = $this->context->link->getProductLink($obj->id, $obj->link_rewrite, $obj->category_rewrite);
                    if (!isset($obj->cover) || !$productsImagesArray[$productViewed]['id_image'])
                    {
                        $obj->cover = $defaultCover;
                        $obj->legend = '';
                    }
                    $productsViewedObj[] = $obj;
                }
            }
            if (!count($productsViewedObj))
                return;
            $this->smarty->assign(array(
                'productsViewedObj' => $productsViewedObj,
                'mediumSize' => Image::getSize('medium')));
            return $this->display(__FILE__, 'lastViewer.tpl');
        }
        return;
    }

    private function displayModule() {
        $this->context->smarty->assign(
            array(
                'tittle_txt' => $this->l('MI PRIMER MODULO'),
                'message_txt' => $this->l('Hello World!'),
                'link_txt' => $this->l('https://www.google.com')
            )
        );
        return $this->display(__FILE__, 'columnas.tpl');
    }

    public function hookDisplayLeftColumn($params)
    {
        if (Configuration::get('CAPTURELEADSALEX_COL_LEFT')==true) {
            return $this->showLastVieweds($params);
        }
    }

    public function hookDisplayRightColumn($params)
    {
        if (Configuration::get('CAPTURELEADSALEX_COL_RIGHT')==true) {
            return $this->showLastVieweds($params);
        }
    }
}
