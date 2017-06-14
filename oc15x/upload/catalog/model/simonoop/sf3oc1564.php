<?php

/*####################################################################################
##
## FRONTEND
##
## sf3 - 1.0.0 Build 0113
##
####################################################################################*/

class ModelSimonoopSF3oc1564 extends Model
{

    protected $filterdata;
    protected $settings;
    protected $memcache;
    protected $debug;
    protected $language_id;
    protected $cat_id;
    protected $currency_id;
    protected $search;
    protected $categories;
    public $caller;
    public $OCFunction;
    public $finalCounter;
    public $ymm = false;
    public $SOURCE;

    public $validcallers = array('ControllerProductCategory', 'ControllerModuleSimonCustomFilter', 'ControllerProductSearch', 'ControllerModuleLatest');

    public $validOCFunctions = array('getProducts',
        'getTotalProducts'
    );

    /*####################################################################################
    ##
    ## helpers
    ##
    ####################################################################################*/
    #public function log($title,$message){if($this->debug){$backt = debug_backtrace()[1];printf("<fieldset style='padding:10px'><legend> (%s::%s)-%s </legend><pre>%s</pre></fieldset>", $backt['class'], $backt['function'], is_array($title)?join('::',$title):$title, is_array($message)?print_r($message,1):nl2br($message));}}
    public function setCaller($caller)
    {
        $this->caller = $caller;
    }

    public function setOCFunction($OCFunction)
    {
        $this->OCFunction = $OCFunction;
    }

    public function isValidCaller()
    {
        return in_array($this->caller, $this->validcallers);
    }

    public function isValidOCFunction()
    {
        return in_array($this->OCFunction, $this->validOCFunctions);
    }

    public function hasTagTable()
    {
        return $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product_description LIKE 'tag';")->num_rows == 0;
    }

    /*####################################################################################
    ##
    ## TODO!?
    ##
    ####################################################################################*/
    public function getLocationType()
    {

    }

    /*####################################################################################
    ##
    ## The constructor!
    ##
    ####################################################################################*/
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->settings = $this->config->get("sf3_general");
        if (isset($this->settings['cache']['status']) && $this->settings['cache']['status'] == 'memcache') {
            $this->memcache = new Memcache;
            $this->memcache->connect($this->settings['cache']['memcache']['host'], $this->settings['cache']['memcache']['port']) or die ("Could not connect");
        }

        if (isset($this->settings['ymm']) && $this->settings['ymm'] == 'yes') {
            $this->load->model('catalog/product');
            if (method_exists($this->model_catalog_product, 'sf3_get_ymm_where')) {
                $this->ymm = $this->model_catalog_product->sf3_get_ymm_where();
            }
        }

        if (isset($this->settings['method']) && $this->settings['method'] == 'GET') {
            $this->SOURCE = $_GET;
        } else {
            $data = json_decode((file_get_contents('php://input')), 1);
            $this->SOURCE = $data;
        }

        $this->debug = isset($this->SOURCE['d']);
        $this->language_id = $this->config->get('config_language_id');
        $this->currency_id = $this->currency->getID();
        $this->search = isset($this->SOURCE['search']) ? $this->SOURCE['search'] : "";
        $this->search = preg_replace('/ /', '%', $this->search);
        $this->finalCounter = 0;
        $this->categories = array();

        /*
         * experimental
         */
        /*
        if (count($_GET)) {
            $_GET = array();
            $params = explode('&', $_SERVER['QUERY_STRING']);

            foreach ($params as $pair) {
                list($key, $value) = explode('=', $pair);
                $key = str_replace('amp;', '', $key);
                $_GET[urldecode($key)] = urldecode($value);
            }
        }
        */
    }

    /*####################################################################################
    ##
    ## log
    ##
    ####################################################################################*/
    public function log($str)
    {
        if ($this->debug) {
            printf("<fieldset><legend>log</legend>%s</fieldset>", $str);
        }
    }

    /*####################################################################################
    ##
    ## get cat children
    ##
    ####################################################################################*/
    public function getCatChildren($cat_id)
    {
        $out = array();
        $out[] = $cat_id;
        $children = $this->query(sprintf("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id='%s'", $cat_id));
        if ($children->num_rows) {
            foreach ($children->rows as $row) {
                foreach (self::getCatChildren($row['category_id']) as $cat) {
                    $out[] = (int)$cat;
                }
            }
        }
        return $out;
    }

    /*####################################################################################
    ##
    ## The cache decider!
    ##
    ####################################################################################*/
    public function query($sql)
    {
        $cache_settings = '';
        if($this->settings['cache']['status']){
            $cache_settings = $this->settings['cache']['status'];
        }

        switch ($cache_settings) {
            case 'memcache':
                $key = md5($sql);
                if ($gotten = $this->memcache->get($key)) {
                    $this->log('Memcache: ' . $key . " retrieved from memcached </br> ");
                    return $gotten;
                } else {
                    $this->log("Memcache:  from database (was NOT cached)");
                    $gotten = $this->db->query($sql);
                    $this->memcache->set($key, $gotten);
                    return $gotten;
                }
                break;
            case 'opencart':
                $cache = md5($sql);
                $key = 'sf3';
                $query = $this->cache->get($key . '.' . $cache);
                if (!$query) {
                    $this->log("opencart cache:  from database (was NOT cached)");
                    $query = $this->db->query($sql);
                    $this->cache->set($key . '.' . $cache, $query);
                } else {
                    $this->log('opencart cache: ' . $cache . " retrieved from opencart cache </br> ");
                    if (preg_match('/^2.*/', VERSION)) {
                        $_query = new stdClass();
                        $_query->num_rows = $query['num_rows'];
                        $_query->row = $query['row'];
                        $_query->rows = $query['rows'];
                        $query = $_query;
                    }
                }
                return $query;
                break;
            default:
                $this->log("nocache: from database (was NOT cached)");
                return $this->db->query($sql);
                break;
        }
    }//query

    /*####################################################################################
    ##
    ## Render the bastard
    ##
    ####################################################################################*/
    public function index()
    {
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/simonoop/sf3/sf3.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/simonoop/sf3/sf3.tpl';
        } else {
            $this->template = 'default/template/simonoop/sf3/sf3.tpl';
        }
        $this->data['settings'] = $this->settings;
        $this->render();
    }//index()


    /*####################################################################################
    ##
    ## Saca o LayoutID
    ##
    ####################################################################################*/
    public function getLayoutID()
    {
        $layout_id = 0;

        if (isset ($this->request->get ['route'])) {
            $route = $this->request->get ['route'];
        } else {
            $route = 'common/home';
        }
        
        if(isset($this->request->get ['ishome'])){
            $route = 'common/home';
        }
       
        if(!$layout_id) {

            if (substr($route, 0, 16) == 'product/category' && isset ($this->request->get ['path'])) {
                $path = explode('_', ( string )$this->request->get ['path']);
                $layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
            }

            if (substr($route, 0, 15) == 'product/product' && isset ($this->request->get ['product_id'])) {
                $layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get ['product_id']);
            }

            if (substr($route, 0, 23) == 'information/information' && isset ($this->request->get ['information_id'])) {
                $layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get ['information_id']);
            }
        }

        if (!$layout_id) {
            $this->load->model('design/layout');
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }
        return $layout_id;
    }

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
        } elseif (isset($this->SOURCE['man_id'])) {
            return (int)$this->SOURCE['man_id'];
        }
        return '0';
    }//getManufacturerID

    /*####################################################################################
    ##
    ## is this me??
    ##
    ####################################################################################*/
    public function isSelf($source, $f)
    {
        if (is_object($source) || is_array($source)) {
            if (isset($source['data'][$source['type']]['id']) && isset($f->source->id)) {
                return $source['type'] == $f->type && $source['data'][$source['type']]['id'] == $f->source->id;
            } else {
                return $source['type'] == $f->type;
            }
        } else {
            return $source == 'FINAL_PARSE' ? false : true;
        }
    }

    /*####################################################################################
    ##
    ## Retrieve full filter data from payload
    ##
    ####################################################################################*/
    public function getFullFilterDataFromPayload($f)
    {
        foreach ($this->filterdata as $full) {
            if ($this->isSelf($full['source'], $f)) {
                return $full;
            }
        }
        return null;
    }

    /*####################################################################################
    ##
    ## is this mutually exclusive?
    ##
    ####################################################################################*/
    public function isExclusive($f)
    {
        $full = $this->getFullFilterDataFromPayload($f);
        return isset($full['settings']['exclusive']) ? $full['settings']['exclusive'] == 'yes' : false;
    }

    /*####################################################################################
    ##
    ## rows para um lado, totais para outro. por causa do watch
    ##
    ####################################################################################*/
    public function part($rows)
    {
        $out = array('rows' => array(), 'totals' => array());
        foreach ($rows as $k => $row) {

            $out['rows'][] = array(
                'idx' => $k,
                'text' => $row['text'],
                'value' => $row['value'],
                'level' => isset($row['level']) ? $row['level'] : -1,
                'image' => isset($row['image']) ? $row['image'] : '',
                'external' => isset($row['external']) ? $row['external'] : '',
                'uid' => isset($row['uid']) ? $row['uid'] : '',
            );
            $out['totals'][] = array(
                'total' => $row['total']
            );
        }
        return array($out['rows'], $out['totals']);
    }//part

    /*####################################################################################
    ##
    ## loadOptions
    ##
    ####################################################################################*/
    protected function loadOptions($payload, $fullFilterData)
    {
        $source = $fullFilterData->source;

        //protection
        if (!isset($source['data'])) return array(array(), array());

        $id = $source['data'][$source['type']]['id'];
        $catsql = $this->cat_id > 0 ? array(
            0 => " JOIN " . DB_PREFIX . "product_to_category ptc ON p.product_id = ptc.product_id JOIN " . DB_PREFIX . "category c ON c.category_id = ptc.category_id ",
            1 => " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        $mansql = $this->manufacturer_id > 0 ? " AND p.manufacturer_id='" . $this->manufacturer_id . "'" : "";
        #$search = $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%')", $this->search) : "";
        $search = $this->searcher();
        /*#######################
        ## first pass
        #######################*/
        $sql = "/*" . __FUNCTION__ . ":" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT DISTINCT 
                ovd.name AS text, ovd.option_value_id AS value, count(distinct p2.product_id) as total, ov.image
            FROM " . DB_PREFIX . "product p
                JOIN " . DB_PREFIX . "product_option_value pov ON pov.product_id = p.product_id
                JOIN " . DB_PREFIX . "option_value_description ovd ON ovd.option_value_id = pov.option_value_id
                JOIN " . DB_PREFIX . "option_value ov ON ovd.option_value_id = ov.option_value_id AND ovd.language_id='%s' /*language_id*/
				JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = ovd.language_id
                %s /*cats0*/
                LEFT JOIN " . DB_PREFIX . "product p2 ON p.product_id = p2.product_id %s /*payload*/
            WHERE 
            	" . $this->ymm . "
                p.status=1
                AND pov.option_id='%s' /*id*/ %s /*cats1*/ %s /*man*/ %s /*search*/
            GROUP BY 
                pov.option_value_id
        ", $this->language_id, $catsql[0], $this->generateDependencySQL($payload, $source), $id, $catsql[1], $mansql, $search);

        $msc = microtime(true);
        $rows = $this->query($sql)->rows;
        $msc = microtime(true) - $msc;
        $msc = round($msc * 1000, 10);
#echo "<pre>";print_r(array(__FILE__.':'.__LINE__, $rows ));echo "</pre>";//Simon
        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }

        return array($rows, $msc);
    }//loadOptions

    /*####################################################################################
    ##
    ## loadPrices
    ##
    ####################################################################################*/
    protected function loadPrices($payload, $fullFilterData, $self)
    {
        $source = $fullFilterData->source;
        $catsql = $this->cat_id > 0 ? array(
            0 => " JOIN " . DB_PREFIX . "product_to_category ptc ON p2.product_id = ptc.product_id JOIN " . DB_PREFIX . "category c ON c.category_id = ptc.category_id ",
            1 => " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        $mansql = $this->manufacturer_id > 0 ? " AND p2.manufacturer_id='" . $this->manufacturer_id . "'" : "";
        #$search = $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%')", $this->search) : "";
        $search = $this->searcher();
        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
                SELECT distinct p2.product_id, p2.price as text, 'none' as id, p2.tax_class_id, 'regular' as source
                FROM " . DB_PREFIX . "product p2
                JOIN " . DB_PREFIX . "product_description pd ON p2.product_id = pd.product_id AND pd.language_id = '%s'/*language_id*/
                %s /*cats0*/
                WHERE
                " . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
                p2.status=1 %s /*cats1*/ %s /*man*/ %s /*search*/", $this->language_id, $catsql[0], $catsql[1], $mansql, $search);
        //p2.status=1 %s %s", $catsql[0], $catsql[1], $this->generateDependencySQL($payload,$source, $render));
        #($self['dynamic']['status']=='yes')
        if ($self['specials']['status'] == 'yes') {
            $sql .= sprintf("
                UNION SELECT ps.product_id, ps.price as text, 'none' as id, p2.tax_class_id, 'special' as source
                FROM " . DB_PREFIX . "product_special ps
                JOIN " . DB_PREFIX . "product p2 ON ps.product_id = p2.product_id
                JOIN " . DB_PREFIX . "product_description pd ON p2.product_id = pd.product_id AND pd.language_id = %s
                %s                           
                WHERE
                " . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
                p2.status=1
                AND ps.customer_group_id = '1' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
                %s %s %s", $this->language_id, $catsql[0], $catsql[1], '', $search);
            //p2.status=1 %s %s",$catsql[0], $catsql[1], $this->generateDependencySQL($payload,$source, $render));
        }
        if (isset($self['discount']) && $self['discount']['status'] == 'yes') {
            $sql .= sprintf("
                UNION SELECT pdisc.product_id, pdisc.price as text, 'none' as id, p2.tax_class_id, 'discount' as source
                FROM " . DB_PREFIX . "product_discount pdisc
                JOIN " . DB_PREFIX . "product p2 ON pdisc.product_id = p2.product_id
                JOIN " . DB_PREFIX . "product_description pd ON p2.product_id = pd.product_id AND pd.language_id = %s
                %s
                WHERE
                " . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
                p2.status=1
                AND pdisc.customer_group_id = '1' AND ((pdisc.date_start = '0000-00-00' OR pdisc.date_start < NOW()) AND (pdisc.date_end = '0000-00-00' OR pdisc.date_end > NOW()))
                %s %s %s", $this->language_id, $catsql[0], $catsql[1], '', $search);
            //p2.status=1 %s %s",$catsql[0], $catsql[1], $this->generateDependencySQL($payload,$source, $render));
        }
        $msc = microtime(true);
        $sql = $sql . " ORDER BY text";
        $result = $this->query($sql);

        $rows = array();

        function cmp_pricetexts($a, $b)
        {
            if ($a['text'] == $b['text']) {
                return 0;
            }
            return ($a['text'] < $b['text']) ? -1 : 1;
        }

        if ($result->num_rows) {

            $msc = microtime(true) - $msc;
            $msc = round($msc * 1000, 10);
            if (count($result->rows) == 1) {
                array_push($result->rows, $result->rows[0]);
            }

            $_d = array();
            foreach ($result->rows as $_r) {
                $_product_id = $_r['product_id'];
                $_source = $_r['source'];
                if (!isset($_d[$_product_id])) {
                    $_d[$_product_id] = $_r;
                } else {
                    if ($_d[$_product_id]['text'] > $_r['text']) {
                        $_d[$_product_id] = $_r;
                    }
                }
            }

            usort($_d, "cmp_pricetexts");

            $rows = array(
                array_shift($_d),
                array_pop($_d)
            );


            if ($fullFilterData->self['price']['tax']['status'] == 'yes') {
                $rows[0]['text'] = $this->tax->calculate($rows[0]['text'], $rows[0]['tax_class_id'], true);
                $rows[1]['text'] = $this->tax->calculate($rows[1]['text'], $rows[1]['tax_class_id'], true);
            }

            if ($fullFilterData->render != 'slider') {
                $nintervals = (int)$fullFilterData->self['price']['intervals'][$this->currency_id];
                $min = doubleval($rows[0]['text']);
                $max = doubleval($rows[1]['text']);
                $step = ($max - $min) / doubleVal($nintervals);

                $rows = array();
                $range = range($min, $max, $step);
                for ($i = 0; $i < count($range) - 1; $i++) {
                    #echo $range[$i]."!<br>";
                    $bottom = ceil($range[$i]);
                    $top = floor($range[$i + 1]);
                    $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
                    $sql .= sprintf("
                        SELECT count(distinct p2.product_id) as total
                        FROM " . DB_PREFIX . "product p2
                        LEFT JOIN " . DB_PREFIX . "product_special ps ON p2.product_id = ps.product_id AND ps.customer_group_id = '1' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
                        WHERE
						" . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
						p2.status=1
                        AND (p2.price between %s AND %s OR ( (ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) AND (ps.price between %s AND %s))) %s LIMIT 1
                        ", $bottom, $top, $bottom, $top, $this->generateDependencySQL($payload, $source));
                    $row = $this->query($sql)->rows[0];

                    $rows[] = array(
                        'text' => $bottom . ' - ' . $top,
                        'value' => $bottom . ' - ' . $top,
                        'id' => 'none',
                        'total' => $row['total']
                    );
                }
            }
        }

        foreach ($rows as $_k => $_row) {
            $rows[$_k]['text'] = $this->currency->convert($rows[$_k]['text'], $this->config->get('config_currency'), $this->currency->getCode());
        }
        return array($rows, $msc);

    }//loadPrices

    /*####################################################################################
    ##
    ## loadTags
    ##
    ####################################################################################*/
    protected function loadTags($payload, $fullFilterData)
    {
        $source = $fullFilterData->source;
        $catsql = $this->cat_id > 0 ? array(
            0 => " JOIN " . DB_PREFIX . "product_to_category ptc ON p2.product_id = ptc.product_id JOIN " . DB_PREFIX . "category c ON c.category_id = ptc.category_id ",
            1 => " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        $mansql = $this->manufacturer_id > 0 ? " AND p2.manufacturer_id='" . $this->manufacturer_id . "'" : "";
        #$search = $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%')", $this->search) : "";
        $search = $this->searcher();
        $rows = array();
        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT
                pd.tag, p2.product_id
            FROM
                " . DB_PREFIX . "product p2
                JOIN " . DB_PREFIX . "product_description pd on p2.product_id = pd.product_id
                %s
            WHERE
            	" . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
                p2.status=1  AND pd.tag<>'' AND pd.language_id='%s' %s %s %s", $catsql[0], $this->language_id, $catsql[1], $mansql, $search);
        $rowsdata = $this->query($sql)->rows;
        foreach ($rowsdata as $row) {
            $tags = preg_replace('/\s*,\s*/i', ',', $row['tag']);
            foreach (preg_split('/,/', $tags) as $tag) {

                $tag = trim($tag);
                if (!isset($rows[$tag])) {
                    $rows[$tag] = array('text' => $tag, 'value' => $tag, 'total' => 0);
                }
            }
        }

        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT
                pd.tag, p2.product_id
            FROM
                " . DB_PREFIX . "product p2
                JOIN " . DB_PREFIX . "product_description pd on p2.product_id = pd.product_id
                %s
            WHERE
            	" . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
                p2.status=1  AND pd.tag<>'' AND pd.language_id='%s' %s %s %s
        ", $catsql[0], $this->language_id, $catsql[1], $mansql, $this->generateDependencySQL($payload, $source, $search));
        $msc = microtime(true);
        $rowsdata = $this->query($sql)->rows;
        $msc = microtime(true) - $msc;
        $msc = round($msc * 1000, 10);

        foreach ($rowsdata as $row) {
            $tags = preg_replace('/\s*,\s*/i', ',', $row['tag']);
            foreach (preg_split('/,/', $tags) as $tag) {

                $tag = trim($tag);
                $rows[$tag]['total']++;
            }
        }

        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }

        return array(array_values($rows), $msc);
    }//loadTags

    /*####################################################################################
    ##
    ## loadManufacturers
    ##
    ####################################################################################*/
    protected function loadManufacturers($payload, $fullFilterData)
    {

        if ($this->getManufacturerID() != 0 && $fullFilterData->settings['showmaninmanpage']['status'] == 'no') {
            return array(array(), array());
        }

        $source = $fullFilterData->source;
        $catsql = $this->cat_id > 0 ? array(
            0 => " JOIN " . DB_PREFIX . "product_to_category ptc ON p.product_id = ptc.product_id JOIN " . DB_PREFIX . "category c ON c.category_id = ptc.category_id ",
            1 => " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        #$search = $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%')", $this->search) : "";
        $search = $this->searcher();
        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT
                m.name as text, m.manufacturer_id as value, m.image, count(distinct p2.product_id) as total
            FROM
                " . DB_PREFIX . "product p
                JOIN " . DB_PREFIX . "manufacturer m ON p.manufacturer_id = m.manufacturer_id
                JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = %s
                %s
                LEFT JOIN " . DB_PREFIX . "product p2 ON p.product_id = p2.product_id %s
            WHERE
            	" . $this->ymm . "
                p.status=1 %s %s
            GROUP BY p.manufacturer_id
        ", $this->language_id, $catsql[0], $this->generateDependencySQL($payload, $source), $catsql[1], $search);

        $msc = microtime(true);
        $rows = $this->query($sql)->rows;
        $msc = microtime(true) - $msc;
        $msc = round($msc * 1000, 10);

        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }
        return array($rows, $msc);
    }//loadManufacturers

    /*####################################################################################
    ##
    ## loadStock
    ##
    ####################################################################################*/
    protected function loadStock($payload, $fullFilterData){

        #echo "<pre>";print_r(array(__FILE__.':'.__LINE__, $fullFilterData ));echo "</pre>";die("...");//Simon
        $in_stock_stock_status_id = isset($fullFilterData->source['data']['stock']['in_stock_stock_status_id'])?$fullFilterData->source['data']['stock']['in_stock_stock_status_id']:0;

        if(!$in_stock_stock_status_id ){
            return array();
        }

        if ($this->getManufacturerID() != 0 && $fullFilterData->settings['showmaninmanpage']['status'] == 'no') {
            return array(array(), array());
        }

        $source = $fullFilterData->source;
        $catsql = $this->cat_id > 0 ? array(
            0 => " JOIN " . DB_PREFIX . "product_to_category ptc ON p.product_id = ptc.product_id JOIN " . DB_PREFIX . "category c ON c.category_id = ptc.category_id ",
            1 => " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        #$search = $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%')", $this->search) : "";
        $search = $this->searcher();
        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT
                CASE 
                  WHEN p.quantity>0 THEN s2.name
                  WHEN p.quantity=0 THEN s.name
                END as text,
                CASE 
                  WHEN p.quantity>0 THEN s2.stock_status_id
                  WHEN p.quantity=0 THEN s.stock_status_id
                END as value,	            
                '' as image, count(distinct p2.product_id) as total
            FROM
                " . DB_PREFIX . "product p
                JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = %s
                JOIN oc_stock_status s ON p.stock_status_id = s.stock_status_id AND s.language_id = pd.language_id                
                JOIN oc_stock_status s2 ON s2.stock_status_id = '%d' AND s2.language_id = pd.language_id
                %s
                LEFT JOIN " . DB_PREFIX . "product p2 ON p.product_id = p2.product_id %s
            WHERE
            	" . $this->ymm . "
                p.status=1 %s %s
            GROUP BY 
                CASE 
                  WHEN p.quantity>0 THEN s2.stock_status_id
                  WHEN p.quantity=0 THEN s.stock_status_id
                END
        ", $this->language_id, $in_stock_stock_status_id, $catsql[0], $this->generateDependencySQL($payload, $source), $catsql[1], $search);

        $msc = microtime(true);
        $rows = $this->query($sql)->rows;
        $msc = microtime(true) - $msc;
        $msc = round($msc * 1000, 10);

        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }
        return array($rows, $msc);
    }

    /*####################################################################################
    ##
    ## loadAttributes
    ##
    ####################################################################################*/
    protected function loadAttributes($payload, $fullFilterData)
    {
        $source = $fullFilterData->source;
        if (!isset($source['data'][$source['type']]['id'])) {
            return array(array(), 0);
        }
        $id = $source['data'][$source['type']]['id'];
        $catsql = $this->cat_id > 0 ? array(
            0 => " JOIN " . DB_PREFIX . "product_to_category ptc ON p.product_id = ptc.product_id JOIN " . DB_PREFIX . "category c ON c.category_id = ptc.category_id ",
            1 => " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        #$search = $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%')", $this->search) : "";
        $search = $this->searcher();

        $mansql = $this->manufacturer_id > 0 ? " AND p.manufacturer_id='" . $this->manufacturer_id . "'" : "";
        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT
                pa.text, pa.text as value, count(distinct p2.product_id) as total
            FROM
                " . DB_PREFIX . "product p
                JOIN " . DB_PREFIX . "product_attribute pa ON p.product_id = pa.product_id AND pa.language_id='%s'
                JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = pa.language_id
                %s
                LEFT JOIN " . DB_PREFIX . "product p2 ON p.product_id = p2.product_id %s
            WHERE
            	" . $this->ymm . "
                p.status=1 AND pa.attribute_id='%s' %s %s %s
            GROUP BY 
                pa.text
        ", $this->language_id, $catsql[0], $this->generateDependencySQL($payload, $source), $id, $catsql[1], $mansql, $search);
        $msc = microtime(true);
        $rows = $this->query($sql)->rows;


        if ($fullFilterData->self['attributes']['split']['status'] == 'yes') {
            $char = $fullFilterData->self['attributes']['split']['char'];
            $newRows = array();

            foreach ($rows as $key => $row) {

                $split = preg_split("/$char/", $row['text']);
                $split = explode($char, $row['text']);

                foreach ($split as $s) {
                    $newRows[trim($s)] = array(
                        'text' => $s,
                        'value' => $s,
                        'total' => 1
                    );
                }

            }

            $rows = array();
            foreach ($newRows as $key => $row) {
                $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
                $sql .= sprintf("
		            SELECT
		                count(distinct p2.product_id) as total
		            FROM
		                " . DB_PREFIX . "product p
		                JOIN " . DB_PREFIX . "product_attribute pa ON p.product_id = pa.product_id AND pa.language_id='%s'
		                JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = pa.language_id
		                %s
		                LEFT JOIN " . DB_PREFIX . "product p2 ON p.product_id = p2.product_id %s
		            WHERE
		            	" . $this->ymm . "
		                p.status=1 AND pa.text LIKE '%%%s%%' AND pa.attribute_id='%s' %s %s %s
		        ", $this->language_id, $catsql[0], $this->generateDependencySQL($payload, $source), addslashes(trim($row['text'])), $id, $catsql[1], $mansql, $search);
                $row_total = $this->query($sql)->row;
                $rows[] = array_merge($row, array('total' => $row_total['total']));

            }


            #echo "<pre>";print_r(array(__FILE__.':'.__LINE__, $rows ));echo "</pre>";//Simon"

        }

        $msc = microtime(true) - $msc;
        $msc = round($msc * 1000, 10);

        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }

        return array($rows, $msc);
    }//loadAttributes

    /*####################################################################################
    ##
    ## loadOCFilters
    ##
    ####################################################################################*/
    protected function loadOCFilters($payload, $fullFilterData)
    {
        $source = $fullFilterData->source;
        $implode = array();
        $msc = microtime(true);
        $query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . $this->cat_id . "'");

        foreach ($query->rows as $result) {
            $implode[] = (int)$result['filter_id'];
        }

        $rows = array();
        $filter_group_data = array();

        if ($implode) {
            $filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

            foreach ($filter_group_query->rows as $filter_group) {
                $filter_data = array();

                $sql = sprintf("
                    SELECT DISTINCT f.filter_id, fd.name, count(distinct p2.product_id) as total
                    FROM " . DB_PREFIX . "filter f
                    LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id)
                
                    LEFT JOIN " . DB_PREFIX . "product_filter pf ON pf.filter_id = f.filter_id
                    LEFT JOIN " . DB_PREFIX . "product p2 on p2.product_id = pf.product_id %s
                
                    WHERE 
                    	" . preg_replace('/p.product_id/', 'p2.product_id', $this->ymm) . "
						f.filter_id IN (%s) AND f.filter_group_id = '%s' AND fd.language_id = '%s'
                    GROUP BY f.filter_id
                    ORDER BY f.sort_order, LCASE(fd.name)
                ", $this->generateDependencySQL($payload, $source), implode(',', $implode), (int)$filter_group['filter_group_id'], $this->language_id);
                $filter_query = $this->db->query($sql);
                foreach ($filter_query->rows as $filter) {
                    $rows[] = array(
                        'text' => $filter['name'],
                        'value' => $filter['filter_id'],
                        'total' => $filter['total']
                    );
                }
            }
        }
        $msc = microtime(true) - $msc;

        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }

        return array($rows, $msc);
    }//loadOCFilters

    /*####################################################################################
    ##
    ## searcher
    ##
    ####################################################################################*/
    public function searcher(){
        return $this->search ? sprintf(" AND (pd.name LIKE '%%%s%%' OR pd.tag LIKE '%%%s%%')", $this->search, $this->search) : "";
    }


    /*####################################################################################
    ##
    ## loadCategories
    ##
    ####################################################################################*/
    protected function loadCategories($payload, $fullFilterData)
    {
        $source = $fullFilterData->source;
        $catsql = $this->cat_id > 0 ? array(
            0 => "",
            1 => (isset($this->settings['children']) && $this->settings['children'] == 'yes') ? " AND (c.category_id in(" . $this->categories . ") OR c.parent_id in(" . $this->categories . ") ) " : " AND (c.category_id in(" . $this->categories . ") ) "
        ) : array("", "");
        //1=>(@$fullFilterData->settings['showcurrentcategory']['status']=='yes'?" AND (c.category_id='". $this->category_id ."' OR c.parent_id='". $this->category_id ."') ":" AND (c.parent_id='". $this->category_id ."') ")
        $search = $this->searcher();
        $mansql = $this->manufacturer_id > 0 ? " AND p.manufacturer_id='" . $this->manufacturer_id . "'" : "";
        $sql = "/*" . __FILE__ . ":" . __LINE__ . "*/";
        $sql .= sprintf("
            SELECT
                cd.name as text, cd.category_id as value, count(distinct p2.product_id) as total, c.parent_id>0 as level,c.category_id, c.parent_id
            FROM
                " . DB_PREFIX . "product p
                JOIN " . DB_PREFIX . "product_to_category ptc on p.product_id = ptc.product_id
                JOIN " . DB_PREFIX . "category_description cd ON cd.category_id = ptc.category_id
                JOIN " . DB_PREFIX . "category c ON cd.category_id=c.category_id
                JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = cd.language_id
                LEFT JOIN " . DB_PREFIX . "product p2 ON p.product_id = p2.product_id %s
            WHERE
            	" . $this->ymm . "
                p.status=1 AND cd.language_id='%s' %s %s %s
            GROUP BY
                cd.name
            ORDER BY 
                level, cd.name
                
        ", $this->generateDependencySQL($payload, $source), $this->language_id, $catsql[1], $mansql, $search);
        $msc = microtime(true);
        $rows = $this->query($sql)->rows;

        if (@$fullFilterData->settings['showcurrentcategory']['status'] == 'no') {
            $_rows = array();
            foreach ($rows as $_key => $_row) {
                if ($_row["value"] != $this->cat_id) {
                    $_rows[] = $_row;
                }
            }
            $rows = $_rows;
        }

        if (@$fullFilterData->settings['groupchildcategories']['status'] == 'no' || @$fullFilterData->settings['showcurrentcategory']['status'] == 'no') {
            foreach ($rows as $_key => $_row) {
                $rows[$_key]['level'] = 0;
            }
        }

        $msc = microtime(true) - $msc;
        $msc = round($msc * 1000, 10);

        foreach ($rows as $_k => $_v) {
            unset($_v['total']);
            $rows[$_k]['uid'] = md5(json_encode($_v));
        }

        return array($rows, $msc);
    }//loadCategories

    /*####################################################################################
    ##
    ## initFilterSubCategories
    ##
    ####################################################################################*/
    public function initFilterSubCategories($data)
    {
        if (isset($this->settings['children']) && $this->settings['children'] == 'yes') {
            $data['filter_sub_category'] = 1;
        }
        return $data;
    }


    /*####################################################################################
    ##
    ## initEmptySettings fields
    ##
    ####################################################################################*/
    public function initEmptySettings($f)
    {

        if (!isset($f->settings['showcurrentcategory']['status'])) $f->settings['showcurrentcategory']['status'] = 'no';
        if (!isset($f->settings['groupchildcategories']['status'])) $f->settings['groupchildcategories']['status'] = 'no';
        if (!isset($f->settings['showmaninmanpage']['status'])) $f->settings['showmaninmanpage']['status'] = 'yes';

        return $f;
    }

    /*####################################################################################
    ##
    ## generate SQL for inter-filter dependency
    ##
    ####################################################################################*/
    public function generateDependencyData($payload, $source, $final)
    {
        $joins = array();
        $wheres = array();
        if ($final) {
            $extrak = $this->finalCounter++;
        } else {
            $extrak = '';
        }

        if ($payload) {

            foreach ($payload as $k => $f) {
                switch ($f->type) {
                    /*####################################################################################
                    ##
                    ## attributes
                    ##
                    ####################################################################################*/
                    case 'attributes':
                        $id = $f->source->id;
                        $data = $f->data;
                        $values = "'" . join($data, "','") . "'";
                        $fullFilterData = $this->getFullFilterDataFromPayload($f);

                        if ($this->isSelf($source, $f) && !$this->isExclusive($f) && !$final) {

                        } else if (!$this->isExclusive($f)) {

                            if ($fullFilterData['self']['attributes']['split']['status'] == 'yes') {
                                $sql = " JOIN " . DB_PREFIX . "product_attribute pa" . $extrak . $k . "
                                    ON pa" . $extrak . $k . ".product_id = p3.product_id
                                    AND pa" . $extrak . $k . ".language_id='" . $this->language_id . "'
                                    AND pa" . $extrak . $k . ".attribute_id='" . $id . "'
									AND (";

                                $sqls = array();
                                foreach ($data as $value) {
                                    $sqls[] = "pa" . $extrak . $k . ".text LIKE '%" . trim(addslashes($value)) . "%'";
                                }
                                $sql .= join(' OR ', $sqls);
                                $sql .= ")";

                                $joins[] = $sql;
                            } else {
                                $joins[] = " JOIN " . DB_PREFIX . "product_attribute pa" . $extrak . $k . "
                                    ON pa" . $extrak . $k . ".product_id = p3.product_id
                                    AND pa" . $extrak . $k . ".language_id='" . $this->language_id . "'
                                    AND pa" . $extrak . $k . ".attribute_id='" . $id . "'
                                    AND pa" . $extrak . $k . ".text IN(" . $values . ")";
                            }
                        } else {
                            if ($fullFilterData['self']['attributes']['split']['status'] == 'yes') {
                                foreach ($data as $_k => $value) {
                                    $joins[] = " JOIN " . DB_PREFIX . "product_attribute pa" . $extrak . $k . $_k . "
                                    	ON pa" . $extrak . $k . $_k . ".product_id = p3.product_id
                                    	AND pa" . $extrak . $k . $_k . ".language_id='" . $this->language_id . "'
                                    	AND pa" . $extrak . $k . $_k . ".attribute_id='" . $id . "'
                                    	AND pa" . $extrak . $k . $_k . ".text ='" . $value . "'";
                                }
                            } else {
                                foreach ($data as $_k => $value) {
                                    $joins[] = " JOIN " . DB_PREFIX . "product_attribute pa" . $extrak . $k . $_k . "
                                    	ON pa" . $extrak . $k . $_k . ".product_id = p3.product_id
                                    	AND pa" . $extrak . $k . $_k . ".language_id='" . $this->language_id . "'
                                    	AND pa" . $extrak . $k . $_k . ".attribute_id='" . $id . "'
                                    	AND pa" . $extrak . $k . $_k . ".text  LIKE '%" . $value . "'%";
                                }

                            }
                        }
                        break;
                    /*####################################################################################
                    ##
                    ## options
                    ##
                    ####################################################################################*/
                    case 'options':
                        $id = $f->source->id;
                        $data = $f->data;

                        $values = "'" . join($data, "','") . "'";

                        if ($this->isSelf($source, $f) && !$this->isExclusive($f) && !$final) {
                        } else if (!$this->isExclusive($f)) {
                            $joins[] = " JOIN " . DB_PREFIX . "product_option_value pov" . $extrak . $k . "
                                    ON pov" . $extrak . $k . ".product_id = p3.product_id
                                    AND pov" . $extrak . $k . ".option_id='" . $id . "'
                                    AND pov" . $extrak . $k . ".option_value_id IN(" . $values . ")";
                        } else {
                            foreach ($data as $_k => $value) {
                                $joins[] = " JOIN " . DB_PREFIX . "product_option_value pov" . $extrak . $k . $_k . "
                                    ON pov" . $extrak . $k . $_k . ".product_id = p3.product_id
                                    AND pov" . $extrak . $k . $_k . ".option_id='" . $id . "'
                                    AND pov" . $extrak . $k . $_k . ".option_value_id ='" . $value . "'";
                            }
                        }

                        break;
                    /*####################################################################################
                    ##
                    ## price
                    ##
                    ####################################################################################*/
                    case 'price':
                        if (!$this->isSelf($source, $f) || $final) {
                            $tempWheres = array();
                            $fullFilterData = $this->getFullFilterDataFromPayload($f);
                            foreach ($f->data as $interval) {
                                if ($fullFilterData['self']['price']['tax']['status'] == 'yes' and isset($fullFilterData['self']['price']['tax']['tax_id'])) {
                                    $tax = array(
                                        $this->tax->calculate($interval[0], $fullFilterData['self']['price']['tax']['tax_id'], true),
                                        $this->tax->calculate($interval[1], $fullFilterData['self']['price']['tax']['tax_id'], true)
                                    );
                                    $rate = array(
                                        $tax[0] / ($interval[0] ? $interval[0] : 1),
                                        $tax[1] / ($interval[1] ? $interval[1] : 1),
                                    );
                                    $interval = array(
                                        $interval[0] / ($rate[0] ? $rate[0] : 1),
                                        $interval[1] / ($rate[1] ? $rate[1] : 1),
                                    );
                                }
                                $interval[0] = floor($this->currency->convert($interval[0], $this->currency->getCode(), $this->config->get('config_currency')));
                                $interval[1] = ceil($this->currency->convert($interval[1], $this->currency->getCode(), $this->config->get('config_currency')));

                                if ($fullFilterData['self']['price']['specials']['status'] == 'yes' && $fullFilterData['self']['price']['discount']['status'] == 'yes') {
                                    $tempWheres[] = sprintf(" (
                                        p3.price BETWEEN %s AND %s OR
                                        ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) AND ps.price between %s AND %s))
                                        OR
                                        ((pdisc.date_start = '0000-00-00' OR pdisc.date_start < NOW()) AND (pdisc.date_end = '0000-00-00' OR pdisc.date_end > NOW()) AND pdisc.price between %s AND %s))"
                                        , $interval[0], $interval[1], $interval[0], $interval[1], $interval[0], $interval[1]);
                                } elseif ($fullFilterData['self']['price']['specials']['status'] == 'yes') {
                                    $tempWheres[] = sprintf(" ( p3.price BETWEEN %s AND %s OR ( (ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) AND ps.price between %s AND %s) )", $interval[0], $interval[1], $interval[0], $interval[1]);
                                } elseif ($fullFilterData['self']['price']['discount']['status'] == 'yes') {
                                    $tempWheres[] = sprintf(" ( pdisc.price BETWEEN %s AND %s OR ( (pdisc.date_start = '0000-00-00' OR pdisc.date_start < NOW()) AND (pdisc.date_end = '0000-00-00' OR pdisc.date_end > NOW()) AND pdisc.price between %s AND %s) )", $interval[0], $interval[1], $interval[0], $interval[1]);
                                } else {
                                    $tempWheres[] = sprintf(" ( p3.price BETWEEN %s AND %s )", $interval[0], $interval[1]);
                                }

                            }
                            $wheres[] = " ( " . join(' OR ', $tempWheres) . " ) ";
                        }
                        break;
                    /*####################################################################################
                    ##
                    ## categories
                    ##
                    ####################################################################################*/
                    case 'categories':
                        if (!$this->isSelf($source, $f) || $final) {
                            $data = $f->data;
                            $values = "'" . join($data, "','") . "'";
                            $joins[] = " JOIN " . DB_PREFIX . "product_to_category ptc on ptc.product_id = p3.product_id AND ptc.category_id IN(" . $values . ") ";
                        }
                        break;
                    /*####################################################################################
                    ##
                    ## tags
                    ##
                    ####################################################################################*/
                    case 'tags':
                        if (!$this->isSelf($source, $f) || $final) {
                            $data = $f->data;
                            foreach ($data as $_k => $value) {
                                $joins[] = " JOIN " . DB_PREFIX . "product_description pd" . $extrak . $k . $_k . " ON pd" . $extrak . $k . $_k . ".product_id =p3.product_id AND pd" . $extrak . $k . $_k . ".tag LIKE '%" . $value . "%'";
                            }
                        }
                        break;
                    /*####################################################################################
                    ##
                    ## manufacturers
                    ##
                    ####################################################################################*/
                    case 'ocfilters':
                        if (!$this->isSelf($source, $f) || $final) {
                            $data = $f->data;
                            $values = "'" . join($data, "','") . "'";
                            $joins[] = " JOIN " . DB_PREFIX . "product_filter pf ON pf.product_id = p3.product_id AND pf.filter_id in(" . $values . ")";
                        }
                        break;
                    /*####################################################################################
                    ##
                    ## manufacturers
                    ##
                    ####################################################################################*/
                    case 'manufacturers':
                        if (!$this->isSelf($source, $f) || $final) {
                            $data = $f->data;
                            $values = "'" . join($data, "','") . "'";
                            $wheres[] = " p3.manufacturer_id in(" . $values . ")";
                        }
                        break;
                    /*####################################################################################
                    ##
                    ## stock
                    ##
                    ####################################################################################*/
                    case 'stock':

                        if (!$this->isSelf($source, $f) || $final) {

                            $data = $f->data;
                            $in_stock_stock_status_id = isset($f->source->in_stock_stock_status_id)?$f->source->in_stock_stock_status_id:0;

                            if(!$in_stock_stock_status_id ){
                                break;
                            }

                            foreach($data as $stock_status_id){
                                if($stock_status_id != $in_stock_stock_status_id) {
                                    $wheres[] = "(p3.quantity = 0 AND p3.stock_status_id=$stock_status_id)";
                                }else{
                                    $wheres[] = "(p3.quantity > 0)";
                                }
                            }
                        }
                        break;
                }
            }
        }
        return array($joins, $wheres);
    }//generateDependencyData

    public function generateDependencySQL($payload, $source)
    {
        list($joins, $wheres) = $this->generateDependencyData($payload, $source, 0);

        $out = count($joins) || count($wheres) ?
            "AND p2.product_id IN ( 
				SELECT 
					DISTINCT p3.product_id 
				FROM 
					" . DB_PREFIX . "product p3
					LEFT JOIN " . DB_PREFIX . "product_special ps ON p3.product_id = ps.product_id
					LEFT JOIN " . DB_PREFIX . "product_discount pdisc ON p3.product_id = pdisc.product_id
                " . join($joins, "\n\n") . " " . (count($wheres) ? " WHERE " . join(' AND ', $wheres) : '') . "\n ) " :
            "";
        if ($this->debug) {
            echo "<pre>";
            print_r(array(__FILE__ . ':' . __LINE__, $out));
            echo "</pre>";//Simon
        }
        return $out;
    }

    /*####################################################################################
   ##
   ## return correct replace this OC version
   ##
   ####################################################################################*/
    protected function replaceSQL($sql, $replacement)
    {
        $preg_replace_nth = function ($pattern, $replacement, $subject, $nth = 1) {
            return preg_replace_callback($pattern, function ($found) use (&$pattern, &$replacement, &$nth) {
                $nth--;
                if ($nth == 0) return preg_replace($pattern, $replacement, reset($found));
                return reset($found);
            }, $subject, $nth);
        };

        $i = array();

        switch (VERSION) {
            case '1.5.1.3':
            case '1.5.2':
            case '1.5.2.1':
            case '1.5.2.2':
            case '1.5.3':
            case '1.5.3.1':
            case '1.5.4':
            case '1.5.4.1':
                $i = array(
                    'getProducts' => 2,
                    'getTotalProducts' => 1,
                    'getProductSpecials' => 0,
                    'iSearchStandard' => 3,
                );
                break;
            case '1.5.5':
            case '1.5.5.1':
            case '1.5.5.1.2':
            case '1.5.6':
            case '1.5.6.1':
            case '1.5.6.2':
            case '1.5.6.3':
            case '1.5.6.3.1':
            case '1.5.6.4':
                $i = array(
                    'getProducts' => 4,
                    'getTotalProducts' => 1,
                    'getProductSpecials' => 0,
                    'iSearchStandard' => 3,
                );
                break;
        }
        return $preg_replace_nth('/where/i', " WHERE " . $replacement, $sql, $i[$this->OCFunction]);
    }

    /*####################################################################################
    ##
    ## the core!
    ##
    ####################################################################################*/
    public function addFilteringSQL($coreSQL)
    {

        $this->filterdata = $this->config->get("sf3_filterdata");

        $payload = null;
        if (isset($this->SOURCE['payload'])) {
            $payload = $this->SOURCE['payload'];
            $payload = base64_decode($payload);
            $payload = urldecode($payload);
            $payload = json_decode($payload);
        }
        if (isset($this->SOURCE['cat_id'])) $this->cat_id = (int)$this->SOURCE['cat_id'];

        if ($this->debug) echo "<pre>Payload:" . print_r($payload, 1) . "</pre>";

        $joins = array();
        $wheres = array();


        if ($this->debug) {
            printf("<fieldset><legend>Original SQL (" . $this->OCFunction . ")</legend>$coreSQL</fieldset>");
        }

        if ($payload || $this->search) {

            list($joins, $wheres) = $objects = $this->generateDependencyData($payload, 'FINAL_PARSE', 1);

            $sql = count($joins) || count($wheres) ?
                "\n\n /* SIMON */\n
                p.product_id IN ( SELECT * FROM (SELECT DISTINCT p3.product_id FROM " . DB_PREFIX . "product p3
                LEFT JOIN " . DB_PREFIX . "product_special ps ON p3.product_id = ps.product_id
                LEFT JOIN " . DB_PREFIX . "product_discount pdisc ON p3.product_id = pdisc.product_id
                \n" . join($joins, "\n\n") . " " . (count($wheres) ? " WHERE " . join(' AND ', $wheres) : '') . "\n) as dummy ) AND
                \n\n /*SIMON */\n
                " :
                "";

            $new = $this->replaceSQL($coreSQL, $sql);
            if ($this->debug) {
                printf("<fieldset><legend>Modified SQL</legend>%s</fieldset>", $new);
            }
            return $new;
        }
        return $coreSQL;

    }//loadfilters

    /*####################################################################################
    ##
    ## load the bastards!!!
    ##
    ####################################################################################*/
    public function loadfilters($bootstrap = null)
    {

        if (!function_exists('sf3_cmp_alphabetically')) {
            function sf3_cmp_alphabetically($a, $b)
            {
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            }
        }

        $this->filterdata = $this->config->get("sf3_filterdata") ? $this->config->get("sf3_filterdata") : array();

        $payload = null;

        if (isset($this->SOURCE['payload'])) {
            $payload = $this->SOURCE['payload'];
            $payload = base64_decode($payload);
            $payload = urldecode($payload);
            #$payload  = str_replace('&quot;', '"', $payload );
            $payload = json_decode($payload);
        }

        if ($bootstrap) $this->SOURCE = $bootstrap;

        if (isset($this->SOURCE['cat_id'])) {
            $this->cat_id = (int)$this->SOURCE['cat_id'];
        } else {
            $this->cat_id = 0;
        }

        if (isset($this->SOURCE['search'])) {
            $this->search = $this->SOURCE['search'];
        }

        $this->manufacturer_id = $this->getManufacturerID();

        if ($this->cat_id) {
            if (isset($this->settings['children']) && $this->settings['children'] == 'yes') {
                $this->categories = "'" . join("','", self::getCatChildren($this->cat_id)) . "'";
            } else {
                $this->categories = $this->cat_id;
            }
        }
        //echo "<pre>";print_r(array(__FILE__.':'.__LINE__, $cats ));echo "</pre>";//Simon


        if ($this->debug) echo "<pre>" . print_r($payload, 1) . "</pre>";

        $objects = array();

        foreach ($this->filterdata as $fkey => $f) {
            $f = (object)$f;
            $f = $this->initEmptySettings($f);
            $rows = array();
            $f->time = 0;

            #var_dump($f->placement);
            $allowed = true;
            $allowed = ($f->status == 'yes');

            if ($allowed && $this->cat_id > 0) {

                if ($f->placement['categories']['status'] == 'custom' && isset($f->placement['categories']['data'])) {
                    $allowed = false;
                    foreach ($f->placement['categories']['data'] as $allowedCategory) {
                        if (isset($allowedCategory['category_id']) && $allowedCategory['category_id'] == $this->cat_id) {
                            $allowed = true;
                        }
                    }
                }
            }

            if ($allowed && $this->manufacturer_id > 0) {
                #var_dump($f->placement['manufacturers']['status']);die("1");
                if ($f->placement['manufacturers']['status'] == 'custom') {
                    $allowed = false;
                    foreach ($f->placement['manufacturers']['data'] as $allowedManufacturer) {
                        if ($allowedManufacturer['manufacturer_id'] == $this->manufacturer_id) {
                            $allowed = true;
                        }
                    }
                }
            }

            $layout_id = $this->getLayoutID();
            if ($allowed && $layout_id > 0 && isset($f->placement['layouts'])) {
                if ($f->placement['layouts']['status'] == 'custom') {
                    $allowed = false;

                    foreach ($f->placement['layouts']['data'] as $allowedLayout) {
                        if ($allowedLayout['layout_id'] == $layout_id) {
                            $allowed = true;
                        }
                    }
                }
            }

            if ($allowed) {
                switch ($f->source['type']) {
                    case 'options':
                        list($rows, $f->time) = $this->loadOptions($payload, $f);
                        break;
                    case 'attributes':
                        list($rows, $f->time) = $this->loadAttributes($payload, $f);

                        if ($f->self['attributes']['external']['status'] == 'yes') {
                            $link_field = trim($f->self['attributes']['external']['link_field']);
                            $link_data = trim($f->self['attributes']['external']['link_data']);
                            $table = trim($f->self['attributes']['external']['table']);

                            $lookupValuesAri = array();
                            foreach ($rows as $row) {
                                $lookupValuesAri[] = $row['text'];
                            }
                            $lookupValues = "'" . join("','", $lookupValuesAri) . "'";

                            $sql = sprintf("SELECT %s,%s FROM %s WHERE %s IN(%s)",
                                $link_field, $link_data, $table, $link_field, $lookupValues
                            );
                            $externalData = $this->db->query($sql)->rows;

                            foreach ($rows as $key => $row) {
                                foreach ($externalData as $ext) {
                                    #var_dump(array($row,$ext));
                                    if ($row['text'] == $ext[$link_field]) {
                                        $rows[$key]['external'] = $ext[$link_data];
                                    }
                                }
                            }

                        }
                        break;
                    case 'stock':
                        list($rows, $f->time) = $this->loadStock($payload, $f);
                        break;
                    case 'categories':
                        list($rows, $f->time) = $this->loadCategories($payload, $f);
                        break;
                    case 'tags':
                        list($rows, $f->time) = $this->loadTags($payload, $f);
                        break;
                    case 'manufacturers':
                        list($rows, $f->time) = $this->loadManufacturers($payload, $f);
                        break;
                    case 'price':
                        list($rows, $f->time) = $this->loadPrices($payload, $f, $f->self['price']);
                        break;
                    case 'ocfilters':
                        list($rows, $f->time) = $this->loadOCFilters($payload, $f);
                        break;

                }//switch


                if ($f->settings['sorting']['status']) {
                    $sorder = (isset($f->settings['sorting']['order'])) ? $f->settings['sorting']['order'] : 'ASC';
                    switch ($f->settings['sorting']['status']) {
                        case 'numeric':
                        case 'alphabetically':
                            usort($rows, "sf3_cmp_alphabetically");
                            if ($sorder == 'DESC') $rows = array_reverse($rows);
                            break;
                    }
                }


                if (count($rows)) {
                    if ($f->render == 'slider' && $f->source['type'] != 'price') {
                        array_unshift($rows, array('text' => $f->settings['sliderSelectAll'][$this->config->get('config_language_id')], 'value' => 'sf3none', 'total' => '-1'));
                    }
                    if ($f->render == 'dropdown') {
                        array_unshift($rows, array('text' => $f->self['dropdown']['defaultText'][$this->config->get('config_language_id')], 'value' => 'sf3none', 'total' => '-1'));
                    }

                    if ($f->render != 'slider') {
                        list($f->filterdata, $f->totals) = $this->part($rows);
                    } else {
                        $f->filterdata = $rows;
                    }
                    $objects[$fkey] = $f;
                }
            }//if allowed

        }//foreach
        foreach ($this->filterdata as $fkey => $f) {
            if (isset($objects[$fkey])) {
                $this->filterdata[$fkey] = $objects[$fkey];

                //remove unwanted data
                unset($this->filterdata[$fkey]->aux_oc_table_fields);
                unset($this->filterdata[$fkey]->placement);

            } else {
                unset($this->filterdata[$fkey]);
            }
        }

        //post-production
        $out = array();
        foreach ($this->filterdata as $_k => $_f) {
            //disable help link if empty
            if (@$_f->settings['help']['status'] == 'link' && trim(@$_f->settings['help']['link']) == '') {
                $this->filterdata[$_k]->settings['help']['status'] = 'no';
            }
        }

        return json_encode(array_values($this->filterdata));
    }//loadfilters


}