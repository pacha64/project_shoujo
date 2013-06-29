<?php
function get_avatar_url($shoujo_id)
{
    if (file_exists(dirname(__FILE__).'\\..\\images\\avatar\\'.$shoujo_id.'.jpg'))
    {
        return constants::site_main."/images/avatar/".$shoujo_id.".jpg";
    }
	return constants::site_main."/static/default-face.jpg";
}
function get_face_url($shoujo_id, $face_name)
{
	escape_string($shoujo_id);
	escape_string($face_name);
	$query = query("SELECT extension FROM shoujo_images WHERE shoujo_id=".$shoujo_id." AND is_face=1 AND name='".$face_name."'");
	$query = mysqli_fetch_assoc($query);
	$return = array();
	if ($query)
	{
		switch($query['extension'])
		{
		case 'jpg':
		case 'jpeg':
			$return['mime-type'] = 'image/jpeg';
			break;
		case 'png':
			$return['mime-type'] = 'image/png';
			break;
		case 'gif':
			$return['mime-type'] = 'image/gif';
			break;
		}
		$return['url'] = constants::site_images."/images/face/".$shoujo_id."-".$face_name.".".$query["extension"];
		return $return;
	}
	else
	{
		return false;
	}
}
function get_background_url($shoujo_id, $background_name)
{
	escape_string($shoujo_id);
	escape_string($background_name);
	$query = query("SELECT extension FROM shoujo_images WHERE shoujo_id=".$shoujo_id." AND is_face=0 AND name='".$background_name."'");
	$query = mysqli_fetch_assoc($query);
	$return = array();
	if ($query)
	{
		switch($query['extension'])
		{
		case 'jpg':
		case 'jpeg':
			$return['mime-type'] = 'image/jpeg';
			break;
		case 'png':
			$return['mime-type'] = 'image/png';
			break;
		case 'gif':
			$return['mime-type'] = 'image/gif';
			break;
		}
		$return['url'] = constants::site_images."/images/background/".$shoujo_id."-".$background_name.".".$query["extension"];
		return $return;
	}
	else
	{
		return false;
	}
}
function get_all_images($shoujo_id)
{
	$array = array();
	$array['face'] = get_all_faces($shoujo_id);
	$array['background'] = get_all_backgrounds($shoujo_id);
	return $array;
}
function get_all_backgrounds($shoujo_id)
{
	escape_string($shoujo_id);
	$backgrounds = array();
	$query = query("SELECT name FROM shoujo_images WHERE shoujo_id=$shoujo_id AND is_face=0");
	while($return = mysqli_fetch_row($query))
	{
		$backgrounds[] = $return[0];
	}
	return $backgrounds;
}
function get_all_faces($shoujo_id)
{
	escape_string($shoujo_id);
	$faces = array();
	$query = query("SELECT name FROM shoujo_images WHERE shoujo_id=$shoujo_id AND is_face=1");
	while($return = mysqli_fetch_row($query))
	{
		$faces[] = $return[0];
	}
	return $faces;
}
function get_face_activity($shoujo_id, $activity_id, $number)
{
    escape_string($shoujo_id);
    escape_string($activity_id);
    escape_string($number);
    $query = query("SELECT name FROM shoujo_activity_image WHERE is_face=1 AND shoujo_id=$shoujo_id AND activity_id=$activity_id AND number=$number");
    if ($result = mysqli_fetch_assoc($query))
    {
        return $result['name'];
    }
    else
    {
        return '';
    }
}
function get_background_activity($shoujo_id, $activity_id, $number)
{
    escape_string($shoujo_id);
    escape_string($activity_id);
    escape_string($number);
    $query = query("SELECT name FROM shoujo_activity_image WHERE is_face=0 AND shoujo_id=$shoujo_id AND activity_id=$activity_id AND number=$number");
    if ($result = mysqli_fetch_assoc($query))
    {
        return $result['name'];
    }
    else
    {
        return '';
    }
}
function add_face($shoujo_id, $name, $extension)
{
    escape_string($shoujo_id);
    escape_string($name);
    escape_string($extension);
    query("INSERT INTO shoujo_images VALUES($shoujo_id, 1, '$name', '$extension')");
}
function add_background($shoujo_id, $name, $extension)
{
    escape_string($shoujo_id);
    escape_string($name);
    escape_string($extension);
    query("INSERT INTO shoujo_images VALUES($shoujo_id, 0, '$name', '$extension')");
}
function has_face($shoujo_id, $name)
{
	escape_string($shoujo_id);
	escape_string($name);
	$query = query("SELECT name FROM shoujo_images WHERE shoujo_id='$shoujo_id' AND is_face=1");
	if ($query)
	{
		if(mysqli_fetch_row($query))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function has_background($shoujo_id, $name)
{
	escape_string($shoujo_id);
	escape_string($name);
	$query = query("SELECT name FROM shoujo_images WHERE shoujo_id='$shoujo_id' AND is_face=0");
	if ($query)
	{
		if(mysqli_fetch_row($query))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}