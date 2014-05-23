<?php
require '/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require '/inc/utils.php';
session_start();
checkLogin();
?>
