<?php
error_reporting(0);
database_connect();
@session_start();
if(!isset($_SESSION["username"]) && isset($_COOKIE["username"], $_COOKIE["hashed_password"]))
{
	$username = $_COOKIE["username"];
	if (user_validate_md5($username, $_COOKIE["hashed_password"]))
	{
		start_session($username, user_get_name($username), $_COOKIE["hashed_password"], true);
	}
}
?>