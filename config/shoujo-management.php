<?php
function shoujo_create($username, $shoujo_name)
{
	escape_string($shoujo_name);
	escape_string($username);
	return query("INSERT INTO shoujo_info(name, owner, description) VALUES('$shoujo_name', '$username', '".mysql_real_escape_string(constants::shoujo_default_description)."')");
}
function shoujo_exists($shoujo_id)
{
	escape_string($shoujo_id);
	$query = query("SELECT id FROM shoujo_info WHERE id='$shoujo_id'");
	if (mysqli_num_rows($query) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
function basic_shoujo_config_enabled($shoujo_id)
{
    if (!shoujo_exists($shoujo_id))
    {
        return false;
    }
    if (!has_face($shoujo_id, 'default'))
    {
        return false;
    }
    if (!has_background($shoujo_id, 'default'))
    {
        return false;
    }
    $info = shoujo_get_all_infromation($shoujo_id);
    if ($info['name'] == '' || $info['description'] == '')
    {
        return false;
    }
    return true;
}
function shoujo_change_name($shoujo_id, $new_name)
{
	$new_name = mysqli_real_escape_string(mysql_helper::$mysql, $new_name);
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	return query("UPDATE shoujo_info SET name='$new_name' WHERE id=$shoujo_id");
}
function shoujo_user_get_all_information($username)
{
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	$return = query("SELECT * FROM shoujo_info WHERE owner='$username'");
	if ($return)
	{
		$array_return = array();
		$arraypos = 0;
		while ($values = mysqli_fetch_assoc($return))
		{
			$array_return[$arraypos++] = shoujo_get_all_infromation($values["id"]);
		}
		return $array_return;
	}
	return false;
}
function shoujo_get_all_infromation($shoujo_id)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	return mysqli_fetch_assoc(query("SELECT * FROM shoujo_info WHERE id=$shoujo_id"));
}
function user_has_shoujo($username, $shoujo_id)
{
	escape_string($username);
	escape_string($shoujo_id);
	$return = query("SELECT * FROM shoujo_info WHERE owner='$username' AND id=$shoujo_id");
	if($return && mysqli_fetch_row($return))
	{
		return true;
	}
	return false;
}
function user_add_priviledge($username, $shoujo_id, $priviledge_level)
{
	if(shoujo_user_has_priviledge($username, $shoujo_id, $priviledge_level))
	{
		return true;
	}
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$priviledge_level = mysqli_real_escape_string($priviledge_level);
	return query("INSERT INTO shoujo_privilege(id, username, privilege) VALUES($shoujo_id, '$username', $priviledge_level)");
}
function user_remove_priviledge($username, $shoujo_id, $priviledge_level)
{
	if(!shoujo_user_has_priviledge($username, $shoujo_id, $priviledge_level))
	{
		return true;
	}
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$priviledge_level = mysqli_real_escape_string(mysql_helper::$mysql, $priviledge_level);
	return query("DELETE FROM shoujo_privilege(id, username, privilege) VALUES($shoujo_id, '$username', $priviledge_level)");
}
function user_get_all_priviledges($username)
{
	$username = mysqli_real_escape_string($username);
	$return = query("SELECT privilege, id FROM shoujo_privilege WHERE username='$username'");
	if(!$return)
	{
		echo mysql_error();
		return null;
	}
	$array_return = array();
	while($return_ind = mysql_fetch_row($return))
	{
		$array_return[$return_ind[1]]=$return_ind[0];
	}
	return $array_return;
}
function user_get_priviledges($username, $shoujo_id)
{
	$return = user_get_all_priviledges($username);
	return $return[$shoujo_id];
}
function shoujo_user_has_priviledge($username, $shoujo_id, $priviledge_level)
{
	if(user_has_shoujo($username, $shoujo_id))
	{
		return true;
	}
	$priviledges = user_get_priviledges($username, $shoujo_id);
	if(!$priviledges) return false;
	foreach($priviledges as $priviledge_ind)
	{
		if ($priviledge_ind == $priviledge_level)
		{
			return true;
		}
	}
	return false;
}
function shoujo_change_description($shoujo_id, $description)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$description = mysqli_real_escape_string(mysql_helper::$mysql, $description);
	return query("UPDATE shoujo_info SET description='$description' WHERE id=$shoujo_id");
}
function shoujo_valid_name($name)
{
	return (strlen($name) <= constants::shoujo_name_max_lenght);
}
function shoujo_valid_description($description)
{
	return (strlen($description) <= constants::shoujo_description_max_lenght);
}
function add_status($shoujo_id, $name, $is_visible)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$name = mysqli_real_escape_string(mysql_helper::$mysql, $name);
	if ($is_visible)
	{
		$is_visible = 1;
	}
	else
	{
		$is_visible = 0;
	}
	$query = query("INSERT INTO shoujo_status VALUES($shoujo_id, '$name', $is_visible)");
}
function has_status($shoujo_id, $name)
{
	escape_string($shoujo_id);
	escape_string($name);
	$query = query("SELECT name FROM shoujo_status WHERE name='$name' AND shoujo_id=$shoujo_id");
	if (mysqli_fetch_assoc($query))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function get_all_status($shoujo_id)
{
	escape_string($shoujo_id);
	$query = query("SELECT * FROM shoujo_status WHERE shoujo_id=$shoujo_id");
	$array = array();
	$loop = 0;
	while($result=mysqli_fetch_assoc($query))
	{
		$array[$loop++] = $result;
	}
	return $array;
}
function add_submenu($shoujo_id, $name, $type, $parent)
{
}
function get_latest_activity_id($shoujo_id)
{
	$return = -1;
	escape_string($shoujo_id);
	$query = query("SELECT MAX(activity_id) FROM shoujo_activity_array WHERE shoujo_id=$shoujo_id LIMIT 1");
	if (mysqli_num_rows($query) > 0)
	{
		$result = mysqli_fetch_array($query);
		$return = $result[0];
	}
	$query = query("SELECT MAX(activity_id) FROM shoujo_menu WHERE shoujo_id=$shoujo_id LIMIT 1");
	if (mysqli_num_rows($query) > 0)
	{
		$result = mysqli_fetch_array($query);
		if($return < $result[0])
		{
			$return = $result[0];
		}
	}
	return $return;
}
function get_latest_submenu_number($shoujo_id)
{
	escape_string($shoujo_id);
	$query = query("SELECT MAX(number) FROM shoujo_menu WHERE shoujo_id=$shoujo_id LIMIT 1");
	if (mysqli_num_rows($query) > 0)
	{
		$result = mysqli_fetch_array($query);
		return $result[0];
	}
	return -1;
}
function xml_information_update($shoujo_id, &$xml)
{
    escape_string($shoujo_id);
    escape_string($xml);
    query("DELETE FROM shoujo_xml_buffer WHERE shoujo_id=$shoujo_id");
    query("INSERT INTO shoujo_xml_buffer VALUES($shoujo_id, '$xml')");
}
function get_buffer_xml($shoujo_id)
{
    // todo
    return false;
}
?>