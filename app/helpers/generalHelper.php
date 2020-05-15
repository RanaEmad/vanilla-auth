<?php
function baseUrl($uri=NULL){
    if($uri){
        return BASE_URL."/".trim($uri,"/");
    }
    return BASE_URL;
}

function redirect($uri=NULL){
    header('Location: '.baseUrl($uri));
}