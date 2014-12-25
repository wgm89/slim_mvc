<?php
/**
 * @date 2014-12-25
 */

namespace Slim;

class Loader {
    
    protected $_init_ob_level;

    /**
     * @var array
     */
    protected $_view_data;

    public function __construct() {
        $this->_init_ob_level = ob_get_level();
    }

    public function __get($name) {
        if (property_exists(\Slim\Slim::getInstance(), $name)) {
            return \Slim\Slim::getInstance()->$name;
        }
        throw new Exception($name.' not exists');
    }

    public function view($tpl, $data=array(), $return=false) {
        $view_file = APPLICATION.'/'.VIEW.'/'.$tpl.'.view.php';
        foreach ($data as $key=>$val) {
            $this->_view_data[$key] = $val;
        }
        extract($this->_view_data);
        if (file_exists($view_file)) {
            ob_start();
            require($view_file);
            if ($return) {
                return ob_get_clean();
            } else {
                if (ob_get_level() > $this->_init_ob_level + 1) {// assume first level is 1
                    ob_end_flush();
                } else {
                    echo ob_get_contents();
                    @ob_end_clean();
                }
            }
        } else {
            throw new Exception($tpl.' not exists');
        }
    }

}
