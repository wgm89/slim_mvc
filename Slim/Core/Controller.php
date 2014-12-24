<?php
/**
 * author : saeed
 * date   : 2013-7-9
 *
 */
class Controller{

    public function __construct(){
        $this->view = new \Slim\View();
        /*
        $this->response = new \Slim\Http\Response();
        $this->environment = \Slim\Environment::getInstance();
        $this->request = new \Slim\Http\Request($this->environment);
         */
    }

    public function __get($name) {
        $app = \Slim\Slim::getInstance();
        return $app->$name();
    }

    public function render($template, $data = array())
    {
        //$this->response->status($status);
        $this->view->setTemplatesDirectory(APPLICATION.'/'.VIEW);
        $this->view->appendData($data);
        $this->view->display($template);
    }
    
    public function filter($data){
        if(is_array($data)){
            foreach($data as $key=>$val){
                if(is_array($val)){
                    $data[$key] = $this->filter($val);
                }else{
                    $data[$key] = addslashes(htmlspecialchars($val));
                }
            }
        }else{
            return addslashes(htmlspecialchars($data));
        }
        return $data;
    }


}
