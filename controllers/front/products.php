<?php

/**
 * @author Mahdi Shad ( ramtin2025@yahoo.com )
 * @copyright Copyright Nitron.pro 2021-2023
 * @link https://Nitron.pro
 */

class nitron_all_productsproductsModuleFrontController extends ModuleFrontController
{
    public $lang;
    public $shop;
    public $showImage;
    public $showQty;
    public $categories;
    public $filter = false;
    public $filterCategory = false;
    public $nb;

    public function __construct()
    {
        parent::__construct();
        $this->lang = $this->context->language->id;
        $this->shop = $this->context->shop->id;
        $this->context = Context::getContext();
        if (!$this->module) {
            $this->module = new Nitron_All_Products();
        }
        $this->ajax = Tools::getValue('ajax');
        $this->showImage = (bool)Configuration::get('NITRON_NAP_SHOW_IMAGES');
        $this->showQty = (bool)Configuration::get('NITRON_NAP_SHOW_QTY');

        $this->categories = Category::getSimpleCategories($this->lang);
        array_unshift($this->categories, [
            'id_category' => 0,
            'name' => $this->trans('Select category', [], 'Modules.Nitronallproducts.Admin')]);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/' . $this->module->theme . '.products.css');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/' . $this->module->theme . '.products.js');

        switch (Tools::getShopProtocol()) {
            case 'http://':
                $url = Tools::getShopDomain(true);
                break;
            default:
                $url = Tools::getShopDomainSsl(true);
        }
        Media::addJsDef([
            'baseLink' => $url.'/upload/',
        ]);
    }

    public function postProcess()
    {
        $this->checkFilter();
        $page = Tools::getValue('filter_page') ?: 1;
        $this->nb = Configuration::get('NITRON_NAP_SHOW_NB');
        $sql = new DbQuery();
        $sql->from('product', 'p');
        if ($this->filter && $this->filterCategory) {
            $sql->where('p.id_product IN(
            SELECT id_product FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE id_category = ' . $this->filterCategory . '
            )');
        }
        $sql2 = $sql;
        $sql2->select('COUNT(*)')
            ->where('1');
        $total = Db::getInstance()->getValue($sql2);
        $sql->select('p.id_product, pl.description_short, pl.name, sa.quantity, i.id_image, pl.link_rewrite')
            ->leftJoin('product_lang', 'pl', 'pl.id_product=p.id_product AND id_lang = ' . $this->lang)
            ->leftJoin('stock_available', 'sa', 'sa.id_product=p.id_product AND sa.id_product_attribute = 0')
            ->leftJoin('image', 'i', 'i.id_product=p.id_product AND i.cover = 1')
            ->where('p.active = 1')
            ->groupBy('sa.id_product');
        if (!$this->ajax) {
            $sql->limit($this->nb, ($page - 1) * $this->nb);
        }
        $result = Db::getInstance()->executeS($sql);
        $products = [];
        foreach ($result as $item) {
            $products[$item['id_product']]['quantity'] = $item['quantity'];
            $products[$item['id_product']]['description_short'] = $item['description_short'];
            $products[$item['id_product']]['name'] = $item['name'];
            $products[$item['id_product']]['image'] = $item['id_image'] ? $this->context->link->getImageLink(
                $item['link_rewrite'], $item['id_image'], Configuration::get('NITRON_NAP_SHOW_IMG_TYPE') ?: 'home_default') : null;
            $products[$item['id_product']]['link'] = $this->context->link->getProductLink((int)$item['id_product']);
            $i = null;
            $products[$item['id_product']]['price'] = (int)Product::priceCalculation(
                $this->shop, $item['id_product'], 0, $this->context->country->id,null, 0, $this->context->currency->id,
                $this->context->customer->id_default_group, 1, 0, 0, 0, 1, 0, $i, 1);
        }

        if ($this->ajax) {
            $this->exportList($products);
        }

        $this->setPagination($total);
        $this->addJqueryPlugin('chosen');
        $this->context->smarty->assign(
            [
                'products' => $products,
                'categories' => $this->categories,
                'showImage' => $this->showImage,
                'showQty' => $this->showQty,
                'currency' => $this->context->currency,
                'selected_category' => (int)Tools::getValue('category') ?: 0,
                'theme_directory' => _PS_THEME_DIR_,
            ]
        );
        parent::postProcess();
    }

    public function checkFilter()
    {
        if (Tools::isSubmit('submit_filter')) {
            $this->filter = true;
            $this->filterCategory = (int)Tools::getValue('category');
        }
    }

    private function exportList($products)
    {
        require_once _PS_MODULE_DIR_ . $this->module->name . '/classes/xlsxwriterCore.php';
        $header = [
            'ID' => 'string',
            'Image' => 'string',
            'Name' => 'string',
            'Price' => 'price',
            'Quantity' => 'integer',
            'Link' => 'string',
        ];
        $data = [];
        if (!empty($products)) {
            $object = json_decode(json_encode($products));
            foreach ($object as $key => $row) {
                $data[] = [
                    $key,
                    $row->image,
                    $row->name,
                    $row->price,
                    $row->quantity,
                    $row->link,
                ];
            }
        }
        $filename = "nt_products".(Tools::getValue('category') ? '_'.Tools::getValue('category') : '').".xlsx";
        header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $writer = new XLSXWriter();
        $writer->setAuthor('Nitron.Pro');
        $writer->writeSheet($data, 'sheet1', $header);
        $writer->writeToFile(_PS_UPLOAD_DIR_.$filename);
        //$writer->writeToStdOut();
        die(
            json_encode([
                'status' => 200,
                'download' => $filename
            ])
        );
    }

    public function setPagination($total)
    {
        $default_content_per_page = max(1, $this->nb);
        $nArray = array($default_content_per_page, $default_content_per_page * 2, $default_content_per_page * 5);
        if ((int)Tools::getValue('n') && (int)$total > 0)
            $nArray[] = $total;
        $this->n = $default_content_per_page;
        if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $nArray))
            $this->n = (int)Tools::getValue('n');
        $_POST['p'] = Tools::getValue('filter_page') ?: 1;
        $this->p = (int)Tools::getValue('p', 1);
        $current_url = preg_replace('/(\?)?(&amp;)?p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));
        if ($this->n != $default_content_per_page || isset($this->context->cookie->nb_item_per_page))
            $this->context->cookie->nb_item_per_page = $this->n;
        $pages_nb = ceil($total / (int)$this->n);
        $range = 1; /* how many pages around page selected */
        $start = $this->p - $range;
        if ($start < 1)
            $start = 1;
        $stop = $this->p + $range;
        if ($stop > $pages_nb)
            $stop = (int)$pages_nb;
        $this->context->smarty->assign([
            'nb_contents' => $total,
            'contents_per_page' => $this->n,
            'pages_nb' => $pages_nb,
            'p' => $this->p,
            'n' => $this->n,
            'nArray' => $nArray,
            'range' => $range,
            'start' => $start,
            'stop' => $stop,
            'current_url' => $current_url,
            'no_follow' => 1,
        ]);
    }

    public function initContent() //2
    {
        parent::initContent();
        /** @var Smarty_Variable $page */
        $this->context->smarty->tpl_vars['page']->value['meta']['robots'] = 'NOFOLLOW, NOINDEX';

        $this->template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/' . $this->module->theme . '.products.tpl';;
    }
}