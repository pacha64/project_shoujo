<?php
include_once "config/include-all.php";
if(!isset($_SESSION["username"]))
{
	header("Location: ".constants::site_main."/login.php");
    die();
}
if (isset($_GET['shoujo']))
{
    $shoujo_id = $_GET['shoujo'];
    if(!valid_shoujo_hash($shoujo_id))
    {
		die("Shoujo doesn't exist");
    }
    else
    {
        $shoujo_id = reverse_shoujo_hash($shoujo_id);
    }
    if (!shoujo_exists($shoujo_id))
    if(!user_is_playing_with_shoujo($shoujo_id, $_SESSION["username"]))
    {
        user_start_new($_SESSION["username"], $shoujo_id);
    }
	header("Location: ".constants::site_main."/play.php?shoujo=".$_GET['shoujo']);
    die();
}
error_page('Shoujo doesn\'t exist', constants::site_main);
function error_page($error, $redirect)
{
?><head>
	<title>Notice</title>
    <meta http-equiv="refresh" content="<?= constants::seconds_error_page_redirect ?>;url=<?= $redirect; ?>"> 
</head>
<style>
p
{
	text-align:center;
	font-size:50px;
	margin-top:100px;
}
</style>
<body>
	<p><?= $error; ?></p>
</body>
<?php
	die();
}
?>
