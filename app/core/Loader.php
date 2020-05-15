<?php 
namespace VanillaAuth\Core;

class Loader{

    public static function view($path,$data=NULL){
        if($data){
            extract($data,EXTR_OVERWRITE);
        }
        require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.trim($path,"/").".php";
    }
}