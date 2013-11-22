<?php
$base_path = dirname(__FILE__).'/';

include $base_path.'functions.php';
include $base_path.'YUICompressor.php';

$js_path = '../';
if(isset($_GET['path'])){
    $js_path = rtrim($_GET['path'],'/').'/';
}
$js_files = glob_recursive($base_path.$js_path.'*.js');

$yui = new YUICompressor($base_path.'yuicompressor-2.4.8.jar',$base_path.'tmp/',array(
    'linebreak' => 1000
));

foreach($js_files as $js_file){
    if(is_file($js_file)){
        $yui->addFile($js_file);
    }
}

// COMPRESS
$code = $yui->compress();

header("content-type: application/javascript");
echo $code;