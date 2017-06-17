<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$output = '';

if (isset($_GET['url'])) {
    $ch = curl_init(); 

    // set url 
    curl_setopt($ch, CURLOPT_URL, $_GET['url']); 

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

    // $output contains the output string 
    $output = curl_exec($ch); 

    // close curl resource to free up system resources 
    curl_close($ch);
    
    $output = preg_replace('/dc:(?!title)/mi', '', $output);
    $output = preg_replace('/prism:/mi', '', $output);
    //print_r($output);die();
    
    $data = json_encode(simplexml_load_string($output, "SimpleXMLElement", LIBXML_NOCDATA));
    //print_r($data);die();
    
    $data = preg_replace('/<a[\w\d\s=\\":\/\.\\\-]+>/mi', '', $data);
    $data = preg_replace('/\<\\\\\/a\>/mi', '', $data);
    $data = preg_replace('/\<b\>/mi', '', $data);
    $data = preg_replace('/\<\\\\\/b\>/mi', '', $data);
    $data = preg_replace('/\<i\>/mi', '', $data);
    $data = preg_replace('/\<\\\\\/i\>/mi', '', $data);
    $data = preg_replace('/\<p\>/mi', '', $data);
    $data = preg_replace('/\<\\\\\/p\>/mi', '', $data);
    $data = preg_replace('/\\\n/m', '', $data);
    $data = preg_replace('/\s+/m', ' ', $data);
    $data = preg_replace('/<img[\s\d\w=":\.~\/\\\-]+>/mi', '', $data);
    $data = preg_replace('/\sappeared\sfirst\son\sFuturism/', '', $data);
    
    echo html_entity_decode($data);
}
else {
    echo '{ "error": "no url provided" }';
}

?>