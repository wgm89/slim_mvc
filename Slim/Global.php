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
        require(APPLICATION.DS.MOD.DS.$modfile);
    }else{
        exit($model.'is not exists');
    }
}

function load_helper($helper){
    $helperfile = $helper.'.php';
    if(file_exists($helperfile)){
        require(APPLICATION.DS.HEL.DS.$helperfile);
    }else{
        exit($helper.'is not exists');
    }
}

function load_library($library){
    $libraryfile = $library.'.php';
    if(file_exists($libraryfile)){
        require(APPLICATION.DS.LIB.DS.$libraryfile);
    }else{
        exit($library.'is not exists');
    }
}


