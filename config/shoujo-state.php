<?php
function user_is_playing_with_shoujo($shoujo_id, $username)
{
	escape_string($shoujo_id);
	escape_string($username);
	$query = query("SELECT username FROM user_shoujo_playing WHERE LOWER(username)=LOWER('$username') AND shoujo_id=$shoujo_id");
	if (mysqli_fetch_assoc($query))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function user_get_activities_done($shoujo_id, $username)
{
	escape_string($shoujo_id);
	escape_string($username);
	$query = query("SELECT activity_id FROM user_shoujo_common_activities_done WHERE LOWER(username)=LOWER('$username') AND shoujo_id='$shoujo_id'");
	$array = array();
	$loop = 0;
	while($result = mysqli_fetch_array($query))
	{
		$array[$loop++] = $result[0];
	}
	return $array;
}
function user_has_done_event($shoujo_id, $username, $event_number)
{
    return false;
}
function user_has_done_option($shoujo_id, $username, $option_number)
{
	escape_string($shoujo_id);
	escape_string($username);
	escape_string($activity_number);
	$query = query("SELECT activity_id FROM user_shoujo_common_activities_done WHERE username='$username' AND shoujo_id='$shoujo_id' AND option=$activity_number LIMIT 1");
	return mysqli_num_rows($query) > 0 ? true : false;
}
function user_get_all_status($shoujo_id, $username)
{
	escape_string($shoujo_id);
	escape_string($username);
	$query = query("SELECT status, value FROM user_shoujo_status WHERE name='$username' AND shoujo_id='$shoujo_id'");
	$array = array();
	$loop = 0;
	while($result=mysqli_fetch_assoc($query))
	{
		$array[$loop++] = $result;
	}
	return $array;
}
function update_status($status_array, $username)
{
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	foreach($status_array as $status_ind)
	{
		query("INSERT INTO user_shoujo_status (primarykeycol,col1,col2) VALUES (1,2,3) ON DUPLICATE KEY UPDATE col1=0, col2=col2+1");
	}
}
?>