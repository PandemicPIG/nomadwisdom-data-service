<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

$response = '';

header('Content-Type: application/json');

if (isset($_POST['token']) && $_POST['token'] === 'mamaliga') {
    header('Access-Control-Allow-Origin: *');
}

if (isset($_POST['url'])) {
    $ch = curl_init(); 

    // set url 
    curl_setopt($ch, CURLOPT_URL, $_POST['url']); 

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

    // $output contains the output string 
    $output = curl_exec($ch); 
    
    if (isset($_POST['type']) && $_POST['type'] === 'rss') {
        header('Filter-Data-Type: xml/rss');
        $output = preg_replace('/dc:(?!title)/mi', '', $output);
        $output = preg_replace('/prism:/mi', '', $output);
    }
    
    // close curl resource to free up system resources 
    curl_close($ch);
    
    $data = json_encode(simplexml_load_string($output, "SimpleXMLElement", LIBXML_NOCDATA));
    
    $data = preg_replace('/\\\n/m', '', $data);
    $data = preg_replace('/\s+/m', ' ', $data);
    
    if (isset($_POST['type']) && $_POST['type'] === 'rss') {        
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
    }
    
    $response = html_entity_decode($data);
}
else {
    $response = '{ "error": "no url provided" }';
}

echo $response;
?>