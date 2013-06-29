<?php
	require_once "../config/include-all.php";
	if(!isset($_SESSION["username"]))
	{
		die("Not logged in");
	}
	if(!isset($_GET['shoujo']))
	{
		die("No shoujo selected");
	}
    $id = $_GET['shoujo'];
    if(!valid_shoujo_hash($id))
    {
		die("Shoujo doesn't exist");
    }
    else
    {
        $id = reverse_shoujo_hash($id);
    }
	if(!user_is_playing_with_shoujo($id, $_SESSION["username"]))
	{
		die("You are not playing with this shoujo");
	}
	header('Content-type: text/xml');
	$status = user_get_all_status($id, $_SESSION["username"]);
?>
<status_collection id="<?= $_GET['shoujo']; ?>" player="<?= $_SESSION["username"]; ?>">
<?php
	foreach($status as $stat_ind):
		if (status_is_private($_GET['id'], $status)) continue;
?>
	<status>
  	<name><?= $stat_ind["status"]; ?></name>
  	<value><?= $stat_ind["value"]; ?></value>
  </status>
<?php
	endforeach;
?>
</status_collection>