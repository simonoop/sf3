<?php

/*####################################################################################
##
## FRONTEND
##
## sf3 - 3.0.0 Build 0052
##
####################################################################################*/

class ControllerModuleSf3 extends Controller
{

    protected $settings;
    protected $module;
    protected $debug;
    protected $language_id;
    protected $layout_id;
    protected $category_id;
    protected $currency_id;
    protected $manufacturer_id;
    protected $model;
    protected $isHome;
    protected $isManufacturerPage;
    public $OC2 = false;
    public $dataConf = array();
    public $moduleLoaded = false;

    /*####################################################################################
    ##
    ## Saca o categoryID
    ##
    ####################################################################################*/
    function getCategoryID()
    {
        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
            return (int)array_pop($parts);
        } else {
            $product_id = isset($this->request->get["product_id"]) ? $this->request->get["product_id"] : 0;
            if ($product_id != 0) {
                $sql = "SELECT pc.category_id FROM " . DB_PREFIX . "product_to_category pc WHERE pc.product_id = '" . (int)$product_id . "'";
                $query = $this->db->query($sql);
                if ($query->num_rows) {
                    return $query->row['category_id'];
                }
            }
        }
        return 0;
    }//getCategoryID()

    /*####################################################################################
    ##
    ## Saca o manufacturer_id
    ##
    ####################################################################################*/
    function getManufacturerID()
    {
        if (isset($this->request->get['manufacturer_id'])) {
            return (int)$this->request->get['manufacturer_id'];
        } elseif (isset($this->request->get['man_id'])) {
            return (int)$this->request->get['man_id'];
        }
        return '0';
    }//getManufacturerID


    /*####################################################################################
    ##
    ## The constructor!
    ##
    ####################################################################################*/
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->OC2 = preg_match('/^2.*/', VERSION);

        $this->dataConf['OC2'] = $this->OC2;

        $this->dataConf['settings'] = $this->config->get("sf3_general");
        $this->dataConf['module'] = $this->config->get("sf3_module");
        $this->dataConf['js'] = $this->config->get("sf3_js");

        $this->dataConf['currency'] = array(
            'id' => $this->currency->getId(),
            'code' => $this->currency->getCode(),
            'symbol_left' => $this->currency->getSymbolLeft(),
            'symbol_right' => $this->currency->getSymbolRight()
        );

        //not needed
        $this->load->model("simonoop/sf3tools");


        $_category_id = $this->getCategoryID();
        $_manufacturer_id = $this->getManufacturerID();
        $_limit = $this->OC2 ? $this->config->get('config_product_limit') : $this->config->get('config_catalog_limit');

        $this->load->model("simonoop/sf3tools");
        if (!$this->model_simonoop_sf3tools->loadsf3()) return;
        $this->moduleLoaded = true;

        $this->dataConf['language_id'] = $this->config->get('config_language_id');
        $this->dataConf['currency_id'] = $this->currency->getID();
        $this->dataConf['debug'] = isset($_GET['d']);
        $this->dataConf['category_id'] = $_category_id;
        $this->dataConf['manufacturer_id'] = $_manufacturer_id;
        $this->dataConf['layout_id'] = $this->model_simonoop_sf3->getLayoutID();
        $this->dataConf['isHome'] = (isset ($this->request->get['route'])) ? preg_match('/home/', $this->request->get['route']) : 1;
        $this->dataConf['isManufacturerPage'] = (isset ($this->request->get['route'])) ? preg_match('/manufacturer/', $this->request->get['route']) : 0;
        $this->dataConf['limit'] = $_limit;

    }

    /*####################################################################################
    ##
    ## Render the bastard
    ##
    ####################################################################################*/
    public function index($setting)
    {
        if (!$this->moduleLoaded) return;

        $bootstrap = array(
            'cat_id' => $this->dataConf['category_id'],
            'man_id' => $this->dataConf['manufacturer_id'],
            'limit' => $this->dataConf['limit'],
            'search' => isset($_GET['search']) ? $_GET['search'] : '',
        );

        $this->dataConf['bootstrap'] = $this->model_simonoop_sf3->loadfilters($bootstrap);

        if ($this->OC2) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/sf3.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/module/sf3.tpl', $this->dataConf);
            } else {
                return $this->load->view('default/template/module/sf3.tpl', $this->dataConf);
            }
        } else {
            $this->data = $this->dataConf;
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/sf3.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/sf3.tpl';
            } else {
                $this->template = 'default/template/module/sf3.tpl';
            }
            $this->render();
        }
    }//index()


    /*####################################################################################
    ##
    ## load the bastards!!!
    ##
    ####################################################################################*/
    public function loadfilters()
    {
        $this->load->model("simonoop/sf3tools");
        echo $this->model_simonoop_sf3->loadfilters();
    }//loadfilters

}