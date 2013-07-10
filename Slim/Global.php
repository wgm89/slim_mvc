<?php
/**
 * Global function , You can execute at any position
 *
 * author : saeed
 * date   : 2013-7-9
 */


function load_model($model){
    $modelfile = $model.'.php';
    if(file_exists($modelfile)){
        require(APPLICATION.'/'.MOD.'/'.$modfile);
    }else{
        exit($model.'is not exists');
    }
}

function load_helper($helper){
    $helperfile = $helper.'.php';
    if(file_exists($helperfile)){
        require(APPLICATION.'/'.HEL.'/'.$helperfile);
    }else{
        exit($helper.'is not exists');
    }
}

function load_library($library){
    $libraryfile = $library.'.php';
    if(file_exists($libraryfile)){
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
                require(APPLICATION.'/'.ROUTE.'/'.$route.'.rou.php');
            }
        }
    }else{
        exit('app.cfg.php is not exists');
    }
}

