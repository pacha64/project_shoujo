<?php
function hash_password($password)
{
    return md5(constants::mysql_password_salt.$password);
}
function user_create($username, $password, $email)
{
	escape_string($username);
	$password = hash_password($password);
	escape_string($email);
	return query("INSERT INTO users(username, email, password, name) VALUES('$username', '$email', '$password', '$username')");
}
function user_exists($username)
{
	escape_string($username);
	$query = query("SELECT username FROM users WHERE LOWER(username)=LOWER('$username')");
	if ($query)
	{
		$query = mysqli_fetch_row($query);
		return $query[0];
	}
	else
	{
		return false;
	}
}
function user_get_name($username)
{
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	$return = query("SELECT name FROM users WHERE username='$username'");
	if ($return)
	{
		$return = mysqli_fetch_row($return);
		return $return[0];
	}
	return null;
}
function user_get_email($username)
{
}
function user_valid_password($username, $password)
{
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	$return = query("SELECT password FROM users WHERE LOWER(username)=LOWER('$username')");
	if ($return)
	{
		$return = mysqli_fetch_row($return);
		if($return[0]==hash_password($password))
		{
			return true;
		}
	}
	return false;
}
function user_validate_md5($username, $password_md5)
{
	$password_md5 = mysqli_real_escape_string(mysql_helper::$mysql, $password_md5);
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	$return = query("SELECT username FROM users WHERE password='$password_md5' AND username='$username'");
	if (mysqli_fetch_row($return))
	{
		return true;
	}
	return false;
}
function user_change_password($username, $new_password)
{
	$new_password = hash_password($new_password);
	$username = mysqli_real_escape_string(mysql_helper::$mysql, $username);
	return query("UPDATE users SET password='$new_password' where username='$username'");
}
function user_delete($username)
{
}
function start_session($username, $name, $password, $remember)
{
	$_SESSION["username"] = $username;
	$_SESSION["player_name"] = $name;
	$_SESSION["playing_shoujos"] = array();
	
	if ($remember)
	{
		setcookie("username", $username, time()+60*60*24*constants::days_remember, '/');
		setcookie("hashed_password", $password, time()+60*60*24*constants::days_remember, '/');
	}
}
function user_start_new($username, $id)
{
	escape_string($username);
	escape_string($id);
	query("INSERT INTO user_shoujo_playing VALUES('$username', $id)");
}
function get_all_currently_playing($username)
{
	escape_string($username);
	escape_string($id);
	$query = query("SELECT * FROM user_shoujo_playing WHERE username='$username'");
	$return = array();
	while($result = mysqli_fetch_assoc($query))
	{
		$return[$result['shoujo_id']] = shoujo_get_all_infromation($result['shoujo_id']);
	}
	return $return;
}
function user_update_status($username, $status_object)
{
	
}
function change_name($username, $name)
{
    escape_string($username);
    escape_string($name);
    query("UPDATE users SET name='$name' WHERE username='$username'");
}
function valid_player_name($name)
{
    if (strlen($name) > 0 && strlen($name) <= constants::player_name_max_lenght)
    {
        return true;
    }
    else
    {
        return false;
    }
}
?>