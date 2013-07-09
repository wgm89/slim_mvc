<?php
/**
 * home page
 *
 * author : saeed
 * date   : 2013-7-9
 */
class indexController extends Controller{

    public function index(){
        $this->render('index',array('data'=>'test'));
    }
    public function none(){
        $this->response->status('404');
    
    }

}
