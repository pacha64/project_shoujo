<?php
session_start();
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);
setcookie("username", "", 1, '/');
setcookie("hashed_password", "", 1, '/');
require_once 'config/constants.php';
header('Location: '.constants::site_main);
die();
?>