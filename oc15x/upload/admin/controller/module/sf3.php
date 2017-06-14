<?php

//admin
class ControllerModuleSf3 extends Controller
{
    public $OC2 = false;
    public $language_id = null;
    private $error = array();
    private $version = 'sf3 - 3.0.0 Build 0052';
    private $build = 'simonbuild';

    /*##################################################################################################################
    ##
    */

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->OC2 = preg_match('/^2.*/', VERSION);
        $this->language_id = $this->config->get('config_language_id');
    }

    /*##################################################################################################################
    ##
    */
    public function checkSuhosin()
    {
        $max_value_length = (int)ini_get("suhosin.get.max_value_length");
        if ($max_value_length > 0 && $max_value_length<2048) {
            echo json_encode(array(
                'present' => 1,
                'max_value_length' => $max_value_length,
            ));
        } else {
            echo json_encode(array(
                'present' => 0,
                'max_value_length' => 0,
            ));
        }
    }

    /*##################################################################################################################
    ##
    */
    public function about()
    {
        echo json_encode(array(
            'version' => $this->version,
        ));
    }

    /*##################################################################################################################
    ##
    */
    public function getjsfiles()
    {
        $this->load->model('simonoop/sf3');
        echo $this->model_simonoop_sf3->getjsfiles();
    }

    /*##################################################################################################################
    ##
    */
    public function getjs()
    {
        $this->load->model('simonoop/sf3');
        $data = $this->model_simonoop_sf3->get('sf3', 'sf3_js', 0);
        echo json_encode($data);
    }

    /*##################################################################################################################
    ##
    */
    public function getAllTables()
    {
        $this->load->model('simonoop/sf3');
        echo $this->model_simonoop_sf3->getAllTables();
    }

    public function getStockStatus(){
        $this->load->model('simonoop/sf3');

        $rows = $this->db->query(sprintf("SELECT * FROM ". DB_PREFIX ."stock_status WHERE language_id='%d' ORDER BY name", $this->language_id))->rows;
        echo json_encode($rows);
    }

    /*##################################################################################################################
    ##
    */
    public function testmemcache()
    {
        $this->load->model('simonoop/sf3');
        echo $this->model_simonoop_sf3->testmemcache();
    }

    /*##################################################################################################################
    ##
    */
    public function clearcache()
    {
        $data = json_decode((file_get_contents('php://input')), true);
        $this->load->model('simonoop/sf3');
        echo $this->model_simonoop_sf3->clearcache($data);
    }

    /*##################################################################################################################
    ##
    */
    public function getCategories()
    {
        $language_id = $this->config->get('config_language_id');
        $rows = $this->db->query("
			SELECT
				cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' > ') AS name, c.parent_id, c.sort_order
			FROM
				" . DB_PREFIX . "category_path cp
				LEFT JOIN " . DB_PREFIX . "category c ON (cp.path_id = c.category_id)
				LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (c.category_id = cd1.category_id)
				LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id)
			WHERE
				cd1.language_id = '$language_id' AND cd2.language_id = '$language_id'
				AND cp.category_id IN (SELECT distinct category_id FROM " . DB_PREFIX . "product_to_category)
			GROUP BY cp.category_id
			ORDER BY name")->rows;
        echo json_encode($rows);
    }

    /*##################################################################################################################
    ##
    */
    public function getManufacturers()
    {
        $rows = $this->db->query("
			SELECT distinct
				m.name, m.manufacturer_id
			FROM
				" . DB_PREFIX . "manufacturer m
				JOIN " . DB_PREFIX . "product p ON p.manufacturer_id = m.manufacturer_id
			ORDER BY
				m.name
			")->rows;
        echo json_encode($rows);
    }

    /*##################################################################################################################
    ##
    */
    public function getSettings()
    {
        $this->load->model('simonoop/sf3');
        $sf3_general = $this->model_simonoop_sf3->get('sf3', 'sf3_general', 0);

        echo json_encode(array_merge(array(
            'status' => 'Disabled',
            'ajax' => 'yes',
            'auto' => 'yes',
            'hash' => 'yes',
            'sliderskin' => 'Modern',
            'cache' => array('status' => 'none', 'memcache' => array('host' => 'localhost', 'port' => '11211')),
            'logic' => 'AND',
            'ymm' => 'no',
            'children' => 'no',
            'ajaxcache' => 'no',
            'method' => 'GET',
            'scrollto' => 'NONE',
        ), is_array($sf3_general)?$sf3_general:array()));
    }

    /*##################################################################################################################
    ##
    */
    public function getLayouts()
    {
        $this->load->model('design/layout');
        $this->load->model('simonoop/sf3');
        $sf3_module = $this->model_simonoop_sf3->get('sf3', 'sf3_module', 0);
        $data = $this->model_design_layout->getLayouts();
        foreach ($data as $k => $v) {
            $data[$k]['status'] = '0';
            $data[$k]['position'] = '';
            $data[$k]['sort_order'] = '';
            foreach ($sf3_module as $m) {
                if ($m['status'] == '1' && $m['layout_id'] == $v['layout_id']) {
                    $data[$k]['status'] = '1';
                    $data[$k]['position'] = $m['position'];
                    $data[$k]['sort_order'] = $m['sort_order'];
                    $data[$k]['domid'] = isset($m['domid']) ? $m['domid'] : '';
                }
            }
        }
        echo json_encode($data);
    }

    /*##################################################################################################################
    ##
    */
    public function getLanguages()
    {

        $this->load->model('localisation/language');
        echo json_encode($this->model_localisation_language->getLanguages());
    }

    /*##################################################################################################################
    ##
    */
    public function getCurrencies()
    {
        $this->load->model('localisation/currency');
        echo json_encode($this->model_localisation_currency->getCurrencies());
    }

    /*##################################################################################################################
    ##
    */
    public function getStores()
    {
        $this->load->model('setting/store');
        $stores = array();
        $stores[] = array(
            'store_id' => 0,
            'name' => $this->config->get('config_name') . $this->language->get('text_default')
        );
        $results = $this->model_setting_store->getStores();

        foreach ($results as $result) {
            $stores[] = array(
                'store_id' => $result['store_id'],
                'name' => $result['name']
            );
        }
        echo json_encode($stores);
    }

    /*##################################################################################################################
    ##
    */
    public function getTaxes(){
        $this->load->model('localisation/tax_class');
        echo json_encode($this->model_localisation_tax_class->getTaxClasses());
    }

    /*##################################################################################################################
    ##
    */
    public function getOptions()
    {
        $modifier = isset($_GET['modifier']) ? $_GET['modifier'] : 'default';
        $language_id = $this->config->get('config_language_id');

        switch ($modifier) {
            case 'default':
                $s = $this->config->get('config_language_id');
                $rows = $this->db->query("
                   SELECT od.option_id as id, name FROM " . DB_PREFIX . "option_description od WHERE od.language_id='{$language_id}' ORDER BY name
                ")->rows;
                echo json_encode($rows);
                break;
            case 'byOptionID':
                $option_id = (int)$_GET['option_id'];
                $data = array();
                $rows = $this->db->query("
                    SELECT od.option_id as id, name, od.language_id FROM " . DB_PREFIX . "option_description od WHERE od.option_id='{$option_id}' ORDER BY name
                ")->rows;
                foreach ($rows as $row) {
                    $data[$row['language_id']] = $row['name'];
                }
                echo json_encode($data);
        }
    }

    /*##################################################################################################################
    ##
    */
    public function getAttributeGroups()
    {
        $language_id = $this->config->get('config_language_id');
        $rows = $this->db->query("
			SELECT 
				ag.attribute_group_id, agd.name 
			FROM 
				" . DB_PREFIX . "attribute_group ag
				JOIN " . DB_PREFIX . "attribute_group_description agd
					on ag.attribute_group_id = agd.attribute_group_id AND agd.language_id= '$language_id'
		")->rows;
        echo json_encode($rows);
    }

    /*##################################################################################################################
    ##
    */
    public function getAttributes()
    {
        $modifier = isset($_GET['modifier']) ? $_GET['modifier'] : 'default';

        switch ($modifier) {
            case 'default':
                $language_id = $this->config->get('config_language_id');
                $rows = $this->db->query("
                    select distinct
						a.attribute_group_id, 
						ad.attribute_id as id, 
						ad.name 
					from 
						" . DB_PREFIX . "attribute a
						join " . DB_PREFIX . "attribute_description ad
							on a.attribute_id = ad.attribute_id 
						/*join " . DB_PREFIX . "product_attribute pa
							on ad.attribute_id = pa.attribute_id AND ad.language_id = pa.language_id*/ 
                    where ad.language_id = '$language_id' order by ad.name
                ")->rows;
                echo json_encode($rows);
                break;
            case 'byAttributeID':
                $attribute_id = (int)$_GET['attribute_id'];
                $data = array();
                $rows = $this->db->query("
                    select distinct ad.attribute_id as id, ad.name, ad.language_id from " . DB_PREFIX . "attribute_description ad
                    WHERE ad.attribute_id='$attribute_id' order by ad.name
                ")->rows;
                foreach ($rows as $row) {
                    $data[$row['language_id']] = $row['name'];
                }
                echo json_encode($data);
        }

    }

    /*##################################################################################################################
    ##
    */
    public function save()
    {
        $this->load->model('simonoop/sf3');
        echo $this->model_simonoop_sf3->save();
    }

    /*##################################################################################################################
    ##
    */
    public function get()
    {
        $this->load->model('simonoop/sf3');
        $key = $_GET['key'];
        echo json_encode($this->model_simonoop_sf3->get('sf3', $key, 0));
    }

    /**
     *
     */
    public function index()
    {

        $valid = true;
        $this->load->model('simonoop/sf3');

        /*###########################################################################
        ##
        ## OC2
        ##
        ###########################################################################*/
        if ($this->OC2) {
            $data['valid'] = $valid;
            $this->load->language('module/sf3');

            $this->document->setTitle($this->language->get('heading_title'));

            $this->load->model('extension/module');

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                $this->model_setting_setting->editSetting('sf3', $this->request->post);

                $this->session->data['success'] = $this->language->get('text_success');

                $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            }

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_edit'] = $this->language->get('text_edit');
            $data['text_enabled'] = $this->language->get('text_enabled');
            $data['text_disabled'] = $this->language->get('text_disabled');

            $data['entry_name'] = $this->language->get('entry_name');
            $data['entry_limit'] = $this->language->get('entry_limit');
            $data['entry_width'] = $this->language->get('entry_width');
            $data['entry_height'] = $this->language->get('entry_height');
            $data['entry_status'] = $this->language->get('entry_status');

            $data['button_save'] = $this->language->get('button_save');
            $data['button_cancel'] = $this->language->get('button_cancel');

            if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            } else {
                $data['error_warning'] = '';
            }

            if (isset($this->error['name'])) {
                $data['error_name'] = $this->error['name'];
            } else {
                $data['error_name'] = '';
            }

            if (isset($this->error['width'])) {
                $data['error_width'] = $this->error['width'];
            } else {
                $data['error_width'] = '';
            }

            if (isset($this->error['height'])) {
                $data['error_height'] = $this->error['height'];
            } else {
                $data['error_height'] = '';
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_module'),
                'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('module/banner', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['action'] = $this->url->link('module/sf3', 'token=' . $this->session->data['token'], 'SSL');

            $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
            $data['token'] = $this->session->data['token'];
            $data['memcache'] = extension_loaded('memcache') ? '1' : '0';
            $config = new Config();

            $data['language_id'] = $this->config->get('config_language_id');
            $data['OC2'] = true;

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('module/sf3.tpl', $data));

        } else { //OC2
            /*###########################################################################
            ##
            ## OC1.x
            ##
            ###########################################################################*/
            $this->data['valid'] = $valid;
            $this->language->load('module/sf3');

            $this->document->setTitle($this->language->get('heading_title'));

            $this->load->model('setting/setting');

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                $this->model_setting_setting->editSetting('sf3', $this->request->post);

                $this->session->data['success'] = $this->language->get('text_success');

                $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            }

            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_enabled'] = $this->language->get('text_enabled');
            $this->data['text_disabled'] = $this->language->get('text_disabled');
            $this->data['text_content_top'] = $this->language->get('text_content_top');
            $this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
            $this->data['text_column_left'] = $this->language->get('text_column_left');
            $this->data['text_column_right'] = $this->language->get('text_column_right');

            $this->data['entry_product'] = $this->language->get('entry_product');
            $this->data['entry_limit'] = $this->language->get('entry_limit');
            $this->data['entry_image'] = $this->language->get('entry_image');
            $this->data['entry_layout'] = $this->language->get('entry_layout');
            $this->data['entry_position'] = $this->language->get('entry_position');
            $this->data['entry_status'] = $this->language->get('entry_status');
            $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

            $this->data['button_save'] = $this->language->get('button_save');
            $this->data['button_cancel'] = $this->language->get('button_cancel');
            $this->data['button_add_module'] = $this->language->get('button_add_module');
            $this->data['button_remove'] = $this->language->get('button_remove');

            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } else {
                $this->data['error_warning'] = '';
            }

            if (isset($this->error['image'])) {
                $this->data['error_image'] = $this->error['image'];
            } else {
                $this->data['error_image'] = array();
            }

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
                'separator' => false
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_module'),
                'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
                'separator' => ' :: '
            );

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('module/sf3', 'token=' . $this->session->data['token'], 'SSL'),
                'separator' => ' :: '
            );

            $this->data['action'] = $this->url->link('module/sf3', 'token=' . $this->session->data['token'], 'SSL');
            $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
            $this->data['token'] = $this->session->data['token'];

            $this->data['modules'] = array();

            if (isset($this->request->post['sf3_module'])) {
                $this->data['modules'] = $this->request->post['sf3_module'];
            } elseif ($this->config->get('sf3_module')) {
                $this->data['modules'] = $this->config->get('sf3_module');
            }

            $this->load->model('design/layout');

            $this->data['layouts'] = $this->model_design_layout->getLayouts();

            $this->template = 'module/sf3.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );
            $this->data['memcache'] = extension_loaded('memcache') ? '1' : '0';
            $this->data['language_id'] = $this->config->get('config_language_id');
            $this->data['OC2'] = false;

            $this->response->setOutput($this->render());

        }
    }


    /*##################################################################################################################
    ##
    */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/sf3')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['sf3_module'])) {
            foreach ($this->request->post['sf3_module'] as $key => $value) {
                if (!$value['image_width'] || !$value['image_height']) {
                    $this->error['image'][$key] = $this->language->get('error_image');
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}

?>
