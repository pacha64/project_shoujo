<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/include-all.php';
$type = false;
if(isset($_GET['action-type']))
{
	$type = $_GET['action-type'];
}
if(isset($_POST['action-type']))
{
	$type = $_POST['action-type'];
}
if(!$type)
{
	die('No action');
}
switch($type)
{
case 'save-information':
    $shoujo_id = $_POST['shoujo-id'];
    $_POST['xml'] = str_replace("\\\"", "\"", $_POST['xml']);
    if (!($xml = simplexml_load_string($_POST['xml'])))
    {
        die('error');
    }
    update_with_xml(4, $_POST['xml']);
    xml_information_update($shoujo_id, $xml->asXML());
    break;
case 'download-back-up':
    $shoujo_id = $_POST['shoujo-id'];
	$shoujo_information = shoujo_get_all_infromation($shoujo_id);
    header('Content-disposition: attachment; filename='.$shoujo_information['name'].'-'.date('m/d').'.xml');
    header('Content-type: text/xml');
    if ($buffer_xml = get_buffer_xml($shoujo_id))
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadHTML($buffer_xml);
        echo $dom->saveXML();
    }
    else
    {
        $_GET['id'] = $shoujo_id;
        chdir('ajax_server');
        require_once 'shoujo_info.php';
    }
    break;
case 'new-face':
    $shoujo_id = $_POST['shoujo-id'];
    $name = $_POST['face-name'];
    $file = $_FILES['face'];
    list($width, $height) = getimagesize($file['tmp_name']);
    $new_dimensions = resize_dimensions(constants_images::face_max_width, constants_images::face_max_height, $width, $height);
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $image = false;
    $new_image = imagecreatetruecolor($new_dimensions['width'], $new_dimensions['height']);
    switch($ext)
    {
    case 'jpg':
    case 'jpeg':
        $image = imagecreatefromjpeg($file['tmp_name']);
        break;
    case 'png':
        $image = imagecreatefrompng($file['tmp_name']);
        break;
    case 'gif':
        $image = imagecreatefromgif($file['tmp_name']);
        break;
    }
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_dimensions['width'], $new_dimensions['height'], $width, $height);
    if(imagejpeg($new_image, 'images/face/'.$shoujo_id.'-'.$name.'.'.$ext, 100))
    {
        add_face($shoujo_id, $name, $ext);
        echo 'ok';
    }
    else
    {
        echo 'fail';
    }
    break;
case 'new-background':
    $shoujo_id = $_POST['shoujo-id'];
    $name = $_POST['background-name'];
    $file = $_FILES['background'];
    if ($name == 'none' || $name == 'no-change')
    {
        die("can't use that name (".$name.")");
    }
    else if(has_face($shoujo_id, $name))
    {
        die('face name already in use');
    }
    list($width, $height) = getimagesize($file['tmp_name']);
    $new_dimensions = resize_dimensions(constants_images::background_max_width, constants_images::background_max_height, $width, $height);
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $image = false;
    $new_image = imagecreatetruecolor($new_dimensions['width'], $new_dimensions['height']);
    switch($ext)
    {
    case 'jpg':
    case 'jpeg':
        $image = imagecreatefromjpeg($file['tmp_name']);
        break;
    case 'png':
        $image = imagecreatefrompng($file['tmp_name']);
        break;
    case 'gif':
        $image = imagecreatefromgif($file['tmp_name']);
        break;
    }
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_dimensions['width'], $new_dimensions['height'], $width, $height);
    if(imagejpeg($new_image, 'images/background/'.$shoujo_id.'-'.$name.'.'.$ext, 100))
    {
        add_background($shoujo_id, $name, $ext);
        echo 'ok';
    }
    else
    {
        echo 'fail';
    }
    break;
case 'new-avatar':
    $shoujo_id = $_POST['shoujo-id'];
    $file = $_FILES['new-avatar'];
    list($width, $height) = getimagesize($file['tmp_name']);
    $new_dimensions = resize_dimensions(constants_images::avatar_max_width, constants_images::avatar_max_height, $width, $height);  
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $image = false;
    $new_image = imagecreatetruecolor($new_dimensions['width'], $new_dimensions['height']);
    switch($ext)
    {
    case 'jpg':
    case 'jpeg':
        $image = imagecreatefromjpeg($file['tmp_name']);
        break;
    case 'png':
        $image = imagecreatefrompng($file['tmp_name']);
        break;
    case 'gif':
        $image = imagecreatefromgif($file['tmp_name']);
        break;
    }
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_dimensions['width'], $new_dimensions['height'], $width, $height);
    if(imagejpeg($new_image, 'images/avatar/'.$shoujo_id.'.jpg', 100))
    {
        echo 'ok';
    }
    else
    {
        echo 'images/avatar/'.$shoujo_id.'.jpg';
    }
    break;
case 'new-activity':
	$id = $_POST['shoujo'];
	escape_string($id);
	$current_pos = 0;
	$activity_id = get_latest_activity_id($id);
	$activity_id++;
	foreach($_POST['type'] as $key=>$type)
	{
		switch($type)
		{
		case 'text':
			$text = $_POST['value-1'][$key];
			escape_string($text);
			query("INSERT INTO shoujo_activity_array VALUES($id, $activity_id, ".constants_activity::type_text.", ".($current_pos++).", '', '', '$text', '')");
			break;
		case 'countdown':
			$time = $_POST['value-1'][$key];
			escape_string($time);
			if ((string)(int)$time != $time)
			{
				break;
			}
			if ($time > constants_activity::countdown_max_seconds)
			{
				$time = constants_activity::countdown_max_seconds;
			}
			else if ($time < 1)
			{
				$time = 1;
			}
			query("INSERT INTO shoujo_activity_array VALUES($id, $activity_id, ".constants_activity::type_countdown.", ".($current_pos++).", '', '', '$time', '')");
			break;
		case 'status':
			$status = $_POST['value-1'][$key];
			$value = $_POST['value-2'][$key];
			if (!has_status($id, $status))
			{
				break;
			}
			if ((string)(int)$value != $value)
			{
				break;
			}
			if ($value > constants_status::max_points)
			{
				$value = constants_status::max_points;
			}
			else if ($value < -constants_status::max_points)
			{
				$value = -constants_status::max_points;
			}
			escape_string($status);
			escape_string($value);
			query("INSERT INTO shoujo_activity_array VALUES($id, $activity_id, ".constants_activity::type_status.", ".($current_pos++).", '', '', '$status', '$value')");
			break;
		case 'option':
			$option_array = array();
			$offset = 1;
			while(isset($_POST['option-value-'.$offset]))
			{
				escape_string($_POST['option-value-'.$offset]);
				$option_array[] = $_POST['option-value-'.$offset];
				$offset++;
			}
			if (count($option_array) == 0) break;
			query("INSERT INTO shoujo_activity_array VALUES($id, $activity_id, ".constants_activity::type_option.", ".$current_pos.", '', '', '', '')");
			foreach ($option_array as $key=>$option)
			{
				query("INSERT INTO shoujo_activity_options VALUES($id, $activity_id, 0, $key, '$option')");
			}
			break;
		}
		if ($type == 'option') break;
	}
	break;
}
function resize_dimensions($goal_width,$goal_height,$width,$height) { 
    $return = array('width' => $width, 'height' => $height); 

    if ($width/$height > $goal_width/$goal_height && $width > $goal_width) { 
        $return['width'] = $goal_width; 
        $return['height'] = $goal_width/$width * $height; 
    }
    else if ($height > $goal_height) { 
        $return['width'] = $goal_height/$height * $width; 
        $return['height'] = $goal_height; 
    }
    return $return; 
}