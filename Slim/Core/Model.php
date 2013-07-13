<?php
/**
 * author : saeed
 * date   : 2013-7-9
 */
require(__DIR__.DS.'Database'.DS.'Medoo.php');
class Model extends Medoo{
    
    public function __construct(){
        parent::__construct();
        $this->loadconfig($this->dbconfig);
    }

}
