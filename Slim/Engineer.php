<?php
/**
 * Witness a miracle
 *
 * author : saeed
 * date   : 2013-7-9
 */

namespace Slim;

class Engineer{

    public $param = array();//function param

    public function receivestr($str,$param){
        $this->param = $param;
        $group = explode(".", trim($str));
        return $this->_parseOnMatchRoute($group);
    }

    public function receiveurl($url){
        $url = trim(trim($url),'/');
        if(empty($url)){
            $group = array(HOME_CTL,'index');
        }else{
            $group = explode("/",$url);
        }
        return $this->_parseOnNoMatchRoute($group);
    }

    protected function _parseOnNoMatchRoute($group){
        if (!(count($group)>1)) {
            $group[] = 'index';
        }
        $dir_queue = array();
        while(!empty($group)) {
            $param = array_shift($group);
            if (file_exists(APPLICATION.'/'.CTL.'/'.$param.'.php')){
                $classname = $param;
                break;
            } elseif (is_dir(APPLICATION.'/'.CTL.'/'.$param)) {
                $dir_queue[] = $param;    
            } else {
                return false;
            }
        }
        if (empty($classname)) return false;
        $action = empty($group) ? 'index' : array_shift($group);
        $classfile = trim(implode('/', $dir_queue).'/'.$classname.".php", '/');
        $this->param = $group;
        require(APPLICATION.'/'.CTL.'/'.$classfile);
        $classname .= 'Controller';
        $classinstance = new $classname;
        call_user_func_array(array($classinstance,$action),$this->param);
        return true;
    }

    private function _parseOnMatchRoute($group) {
        if (count($group)>1) {
            $action = array_pop($group);
        } else {
            $action = 'index';
        }
        $classname = array_pop($group);
        $classfile = $classname.".php";
        if(!empty($group)){
            $classfile = implode('/',$group).'/'.$classfile;
        }
        if(!file_exists(APPLICATION.'/'.CTL.'/'.$classfile)){
            return false;
        }
        require(APPLICATION.'/'.CTL.'/'.$classfile);
        $classname .= 'Controller';
        $classinstance = new $classname;
        call_user_func_array(array($classinstance,$action),$this->param);
        return true;
    }

}
