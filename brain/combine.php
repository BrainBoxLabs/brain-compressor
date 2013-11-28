<?php
/**
 * ?options=
 * path (required)
 * The relative path to this file. eg. ../js/
 *
 * load_first (optional)
 * load first allows you to include app code in a specific order by passing in a comma delimeted string of file or directory patterns.
 * eg. &load_first=/models/,/services/
 *
 * debug (optional)
 * turning debug on will concatenate files without running them through YUI Compressor
 *
 * log (optional)
 * If debug is turned on and log the concatenated files will console.log(file_path) when the file is "loaded"
 *
 * only (optional)
 * Instead of loading all files from a given directory, turning on only will only include the comma separated list provided.
 * eg. &only=../js/jquery.js,../js/bootstrap.js
 */
$base_path = dirname(__FILE__).'/';

include $base_path.'functions.php';

$js_path = '../';
if(isset($_GET['path'])){
    $js_path = rtrim($_GET['path'],'/').'/';
}else{
    header("HTTP/1.0 501 Missing Relative Path Variable");
    echo 'please provide a ?path= query string on the request string.';exit;
}

$log = false;
if(isset($_GET['log'])){
    $log = true;
}

$debug = false;
if(isset($_GET['debug'])){
    $debug = true;
}

$load_first = array();
if(isset($_GET['load_first'])){
    $load_first = explode(',',$_GET['load_first']);
}

$only = array();
if(isset($_GET['only'])){
    $only = explode(',',$_GET['only']);
}

if(count($only) < 1){
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
}else{
    $js_files = array();
    foreach($only as $o){
        $o = $base_path.$o;
        if(is_file($o)){
            $js_files[] = $o;
        }
    }
}

$production = '';
$code = '';

if($debug){

    foreach($js_files as $js_file){

        if($log){
            $debug_code = 'console.log("script loaded: '.$js_file.'");'."\n\n";
        }

        $this_code = trim(file_get_contents($js_file))."\n\n";
        $production .= $this_code;

        $code .= $debug_code.$this_code;
    }

}else{

    include $base_path.'YUICompressor.php';

    $yui = new YUICompressor($base_path.'yuicompressor-2.4.8.jar',$base_path.'tmp/',array(
        'linebreak' => 1000
    ));

    foreach($js_files as $js_file){
        $yui->addFile($js_file);
    }

    $code = $yui->compress();
    $production = $code;
}

file_put_contents($base_path.'production/main.js',$production);

header("content-type: application/javascript");
echo $code;