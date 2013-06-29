<?
session_start();
if(!isset($_SESSION["username"]))
{
    die("Not logged in");
}
if(!isset($_GET['id']))
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
$hash = $_GET['id'];
require_once "../config/shoujo-state.php";
if(!user_is_playing_with_shoujo($id, $_SESSION["username"]))
{
    die("You are not playing with this shoujo");
}
header('Content-type: text/xml');
$common_activities = user_get_activities_done($id, $_SESSION["username"]);
?>
<done_activities id="<?= $hash; ?>" player="<?= $_SESSION["username"]; ?>">
	<common_activities>
<?
	foreach($common_activities as $activity_ind):
?>
  	<id><?= $activity_ind; ?></id>
<?
	endforeach;
?>
  </common_activities>
  <events>
  
  </events>
</done_activities>