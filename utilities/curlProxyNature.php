<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$output = '';

if (isset($_GET['url'])) {
    $url = escapeshellarg($_GET['url']);
    $cmd = "curl -i $url";
    exec($cmd, $output);

    $location_string = "";
    foreach ($output as $string) {
        if (strpos($string, "location") === 0) {
            $location_string = explode(" ", $string)[1];
            break;
        }
    }

    $location_string = escapeshellcmd($location_string);
    $location_string = preg_replace('/\=/mi', '\=', $location_string);

    $cmd2 = "curl -i ".$location_string;
    exec($cmd2, $output2);
    $cookie_data = "";
    foreach ($output2 as $string) {
        if (strpos($string, "set-cookie: idp_session=") === 0) {
            $cookie_data = explode(" ", $string)[1];
            break;
        }
    }

    $cmd3 = "curl --cookie \"$cookie_data\" $url";
    // print_r($cmd3);
    exec($cmd3, $output3);

    $output3 = implode("\n", $output3);

    $output = preg_replace('/dc:(?!title)/mi', '', $output3);
    $output = preg_replace('/prism:/mi', '', $output3);

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
