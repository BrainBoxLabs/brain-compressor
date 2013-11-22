<?php
$base_path = dirname(__FILE__).'/';

include $base_path.'functions.php';

$js_path = '../';
if(isset($_GET['path'])){
    $js_path = rtrim($_GET['path'],'/').'/';
}else{
    header("HTTP/1.0 501 Missing Relative Path Variable");
    echo 'please provide a ?path= query string on the request string.';exit;
}

$debug = false;
if(isset($_GET['debug'])){
    $debug = true;
}

$load_first = array();
if(isset($_GET['load_first'])){
    $load_first = explode(',',$_GET['load_first']);
}

$js_files = glob_recursive($base_path.$js_path.'*.js');

if(count($load_first) > 0 && $load_first[0] != ''){
    $load_first_js_files = array();
    $load_whenever = array();

    foreach($load_first as $lf){
        foreach($js_files as $key => $js_file){
            if(strpos($js_file,$lf) !== false){
                $load_first_js_files[$key] = $js_file;
                unset($js_files[$key]);
            }
        }
    }

    $js_files = array_merge($load_first_js_files,$js_files);
}

$production = '';
$code = '';
foreach($js_files as $js_file){

    if($debug){
        $debug_code = 'console.log("script loaded: '.$js_file.'");'."\n\n";
    }

    $this_code = trim(file_get_contents($js_file))."\n\n";
    $production .= $this_code;

    $code .= $debug_code.$this_code;

}

file_put_contents($base_path.'production/main.js',$production);

header("content-type: application/javascript");
echo $code;