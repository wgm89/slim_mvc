<?php
/**
 * saeed
 *
 * author : saeed
 * date   : 2013-7-9
 */
class SaeedController{
    
    public function index(){
        //load_model('test');
        //$testmodel = new testModel();
        //$result = $testmodel->select("*");
        //print_r($result);
        $database = new Medoo();
        $database = $database->loadconfig('test')->table('test');
        $result = $database->select("*");
        print_r($result);


    }

}
