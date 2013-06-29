<?php
require_once "../config/include-all.php";
if(!isset($_GET['shoujo']))
{
	die('None selected');
}
else
{
	$id = $_GET['shoujo'];
    $bool_false = false;
    if(!valid_shoujo_hash($id))
    {
        $bool_false = true;
    }
    else
    {
        $id = reverse_shoujo_hash($id);
    }
	$xml = new SimpleXMLElement("<preload></preload>");
    $xml->addAttribute('id', $_GET['shoujo']);
	if ($bool_false == false && shoujo_exists($id))
	{
		$xml->addChild('exists', 1);
        if(user_is_playing_with_shoujo($id, $_SESSION['username']))
        {
            $xml->addChild('is_playing', 1);
        }
        else
        {
            $xml->addChild('is_playing', 0);
        }
        if (basic_shoujo_config_enabled($id))
        {
            $xml->addChild('is_available', 1);
        }
        else
        {
            $xml->addChild('is_available', 0);
        }
	}
	else
	{
		$xml->addChild('exists', 0);
		$xml->addChild('is_playing', 0);
		$xml->addChild('is_available', 0);
	}
	echo $xml->asXML();
}
?>