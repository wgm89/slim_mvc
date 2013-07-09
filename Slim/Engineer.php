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
        $this->_parseall($group);
        return true;
    }
    protected function _parseall($group){
        if(count($group)>1){
            $action = array_pop($group);
            $classname = array_pop($group);
            $classfile = $classname.".php";
            if(!empty($group)){
                $classfile = implode(DS,$group).DS.$classfile;
            }
            if(!file_exists(APPLICATION.DS.CTL.DS.$classfile)){
                return false;
            }
            require(APPLICATION.DS.CTL.DS.$classfile);
            $classname .= 'Controller';
            call_user_func_array(array($classname,$action),$this->param);
        }else{
            return false;
        }
    }

}
