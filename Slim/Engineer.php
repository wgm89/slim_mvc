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
        $group = explode(".",trim($str));
        $this->_parseall($group);
    }
    public function receiveurl($url){
        $url = trim(trim($url),'/');
        if(empty($url)){
            $group = array(HOME_CTL,'index');
        }else{
            $group = explode("/",$url);
            if(!(count($group)>1)){
                $group[] = 'index';
            }
        }
        return $this->_parseall($group);
    }
    protected function _parseall($group){
        if(count($group)>1){
            $action = array_pop($group);
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
        }else{
            return false;
        }
    }

}
