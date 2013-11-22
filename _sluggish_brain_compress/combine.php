<?php
$base_path = dirname(__FILE__).'/';

include $base_path.'functions.php';

$js_path = '../';
if(isset($_GET['path'])){
    $js_path = rtrim($_GET['path'],'/').'/';
}
$js_files = glob_recursive($base_path.$js_path.'*.js');


$code = '';
foreach($js_files as $js_file){
    if(is_file($js_file)){
        $code .= trim(file_get_contents($js_file))."\n\n";
    }
}

header("content-type: application/javascript");
echo $code;