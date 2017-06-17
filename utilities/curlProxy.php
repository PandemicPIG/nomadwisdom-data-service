<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$output = '';

$file = '../cache/' . md5($_GET['url']) . '.tmp';

if (file_exists($file)) {
    $content = file_get_contents($file);
    echo $content;
}
elseif (isset($_GET['url'])) {
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

    $data = preg_replace('/\s\s+/m', ' ', $data);

    $rss_replace = [
        '/<a[\w\d\s=\\":\/\.\\\-]+>/',
        '/\<\\\\\/a\>/',
        '/\<b\>/',
        '/\<\\\\\/b\>/',
        '/\<i\>/',
        '/\<\\\\\/i\>/',
        '/\<p\>/',
        '/\<\\\\\/p\>/',
        '/\\\n/',
        '/<img[\s\d\w=":\.~\/\\\-]+>/',
        '/\sappeared\sfirst\son\sFuturism/',
    ];

    foreach ($rss_replace as $regex) {
        $data = preg_replace($regex . 'mi', '', $data);
    }

    $newFile = fopen($file, "w");
    fwrite($newFile, html_entity_decode($data));
    fclose($newFile);
    
    echo html_entity_decode($data);
}
else {
    echo '{ "error": "no url provided" }';
}