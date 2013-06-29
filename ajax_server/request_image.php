<?php
if (!isset($_GET['shoujo'], $_GET['is_face'], $_GET['name']))
{
	die("Invalid image");
}
require_once '../config/include-all.php';
$image = false;
$id = $_GET['shoujo'];
if(!valid_shoujo_hash($id))
{
    die("Shoujo doesn't exist");
}
else
{
    $id = reverse_shoujo_hash($id);
}
if ($_GET['is_face'])
{
	$image = get_face_url($id, $_GET['name']);
}
else
{
	$image = get_background_url($id, $_GET['name']);
}
if (!$image)
{
	die("Invalid image");
}
header('Content-Type: '.$image['mime-type']);
readfile($image['url']);
?>
