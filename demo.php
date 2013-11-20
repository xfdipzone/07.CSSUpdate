<?php

require_once "CSSUpdate.class.php";

define('ROOT_PATH', dirname(__FILE__));

$css_path = ROOT_PATH.'/css';
$csstmpl_path = ROOT_PATH.'/csstmpl';
$replacetags = array('.png', '.jpg', '.gif');

$cssobj = new CSSUpdate($csstmpl_path, $css_path, $replacetags);
$cssobj->update();

?>