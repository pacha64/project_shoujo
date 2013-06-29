<?php
function get_all_variables($shoujo_id)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$return = array();
	$query = query("SELECT * FROM shoujo_variables WHERE id=$shoujo_id");
	if(!$query) return false;
	while($fetch=mysqli_fetch_assoc($query))
	{
		$return[$fetch["name"]]=$fetch["value"];
	}
	return $return;
}
function add_variable($shoujo_id, $name, $value)
{
	if(has_variable($shoujo_id, $name))
	{
		return update_variable($shoujo_id, $name, $value);
	}
	else
	{
		$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
		$name = mysqli_real_escape_string(mysql_helper::$mysql, $name);
		$value = mysqli_real_escape_string(mysql_helper::$mysql, $value);
		return query("INSERT INTO shoujo_variables VALUES($shoujo_id, '$name', '$value')");
	}
}
function update_variable($shoujo_id, $name, $value)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$name = mysqli_real_escape_string(mysql_helper::$mysql, $name);
	$value = mysqli_real_escape_string(mysql_helper::$mysql, $value);
	return query("UPDATE shoujo_variables SET value='$value' WHERE id=$shoujo_id AND name='$name' AND value='$value'");
}
function has_variable($shoujo_id, $name)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$name = mysqli_real_escape_string(mysql_helper::$mysql, $name);
	$return = query("SELECT 1 FROM shoujo_variables WHERE id=$shoujo_id AND name='$name' LIMIT 1");
	return mysqli_fetch_array($return) ? true : false;
}
function remove_variable($shoujo_id, $name)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$name = mysqli_real_escape_string(mysql_helper::$mysql, $name);
	return query("DELETE FROM shoujo_variables WHERE id=$shoujo_id AND name='$name'");
}
function valid_variable_name($name)
{
	if(strlen($name) <= constants::shoujo_constants_max_lenght_name && preg_match('/^[a-zA-Z0-9_]+$/',$name)) return true; else return false;
}
function valid_variable_value($value)
{
	if(strlen($value) <= constants::shoujo_constants_max_lenght_value) return true; else return false;
}
?>
