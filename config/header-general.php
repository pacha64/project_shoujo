<?php
require_once "include-all.php";
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Project Shoujo</title>
	<link rel="stylesheet" href="css/site.css" />
</head>
<body>
	<div id="content">
		<div id="header">
			<a href="<?= constants::site_main ?>"><img src="static/logo-white-big.png" /></a>
		</div>
		<div id="option-strip">
			<ul id="option-strip-container">
<?php
if(isset($_SESSION["username"])):
?>
	<li><a href="<?= constants::site_main ?>">Home</a></li>
	<li><a href="<?= constants::site_main ?>/account.php">Account</a></li>
	<li><a href="<?= constants::site_main ?>/logout.php">Logout</a></li>
	<li><a target="_blank" href="<?= constants::site_main ?>/play.php?shoujo=4">Play</a></li>
	<li><a href="<?= constants::site_forum ?>">Forum</a></li>
<?php
else:
?>
	<li><a href="<?= constants::site_main ?>">Home</a></li>
	<!--<li>Browse</li>-->
	<li><a href="<?= constants::site_main ?>/login.php">Login</a></li>
	<li><a href="<?= constants::site_main ?>/register.php">Register</a></li>
	<li><a href="<?= constants::site_forum ?>">Forum</a></li>
<?php
endif;
?>
			</ul>
		</div>