<?php
/**
 * Global function , You can execute at any position
 *
 * author : saeed
 * date   : 2013-7-9
 */


function load_model($model){
    $modelfile = $model.'.mod.php';
    if(file_exists(APPLICATION.'/'.MOD.'/'.$modelfile)){
        require(APPLICATION.'/'.MOD.'/'.$modelfile);
    }else{
        exit($model.'is not exists');
    }
}

function load_helper($helper){
    $helperfile = $helper.'.hel.php';
    if(file_exists(APPLICATION.'/'.HEL.'/'.$helperfile)){
        require(APPLICATION.'/'.HEL.'/'.$helperfile);
    }else{
        exit($helper.' is not exists');
    }
}

function load_library($library){
    $libraryfile = $library.'.lib.php';
    if(file_exists(APPLICATION.'/'.LIB.'/'.$libraryfile)){
        require(APPLICATION.'/'.LIB.'/'.$libraryfile);
    }else{
        exit($library.' is not exists');
    }
}

function load_route_from_cfg($app){
    if(file_exists(APPLICATION.'/'.CONFIG.'/'.'app.cfg.php')){
        $appcfg = require(APPLICATION.'/'.CONFIG.'/'.'app.cfg.php');
        if(isset($appcfg['routes'])&&is_array($appcfg['routes'])){
            foreach($appcfg['routes'] as $route){
                if(file_exists(APPLICATION.'/'.ROUTE.'/'.$route.'.rou.php')){
                    require(APPLICATION.'/'.ROUTE.'/'.$route.'.rou.php');
                }
            }
        }
    }else{
        exit('app.cfg.php is not exists');
    }
}
function load_cfg($config){
    if(file_exists(APPLICATION.'/'.CONFIG.'/'.$config.'.cfg.php')){
        $config = require(APPLICATION.'/'.CONFIG.'/'.$config.'.cfg.php');
        return $config;
    }

}

function load_db_cfg($dbconfig){
    if(file_exists(APPLICATION.'/'.CONFIG.'/'.'db.cfg.php')){
        $config = require(APPLICATION.'/'.CONFIG.'/'.'db.cfg.php');
        return $config[$dbconfig];
    }

}

/*pre loaded*/
function pre_load(){
     if(file_exists(APPLICATION.'/'.CONFIG.'/'.'app.cfg.php')){
        $appcfg = require(APPLICATION.'/'.CONFIG.'/'.'app.cfg.php');
        if(isset($appcfg['default_load'])&&is_array($appcfg['default_load'])){
            $default_load = $appcfg['default_load'];
            /*load helper library*/
            if(isset($default_load['helper'])){
                $helpers = $default_load['helper'];
                foreach($helpers as $helper){
                    load_helper($helper);
                }
            }
            if(isset($default_load['library'])){
                $libraries = $default_load['library'];
                foreach($libraries as $library){
                    load_library($library);
                }
            }
        }
    }else{
        exit('app.cfg.php is not exists');
    }

}

