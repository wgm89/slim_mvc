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
    $helperfile = $helper.'.php';
    if(file_exists(APPLICATION.'/'.HEL.'/'.$helperfile)){
        require(APPLICATION.'/'.HEL.'/'.$helperfile);
    }else{
        exit($helper.'is not exists');
    }
}

function load_library($library){
    $libraryfile = $library.'.php';
    if(file_exists(APPLICATION.'/'.LIB.'/'.$libraryfile)){
        require(APPLICATION.'/'.LIB.'/'.$libraryfile);
    }else{
        exit($library.'is not exists');
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
function load_db_cfg(){
    if(file_exists(APPLICATION.'/'.CONFIG.'/'.'app.cfg.php')){
        return $dbcfg = require(APPLICATION.'/'.CONFIG.'/'.'db.cfg.php');
    }

}

