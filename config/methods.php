<?php
function query($string)
{
	return mysqli_query(mysql_helper::$mysql, $string);
}
function escape_string(&$string)
{
	$string = mysqli_real_escape_string(mysql_helper::$mysql, $string);
}
function database_connect()
{
	mysql_helper::$mysql = mysqli_connect(constants::mysql_server, constants::mysql_username, constants::mysql_password);
	mysqli_select_db(mysql_helper::$mysql, constants::mysql_database);
}
function generate_shoujo_box($id, $type)
{
	$information = shoujo_get_all_infromation($id);
?>
	<div class="shoujo-info-container">
		<div class="shoujo-info-container-img">
			<img src="<?= get_avatar_url($information['id']); ?>" />
		</div>
		<h3><?= $information['name'] ?></h3>
		<p><?= $information['description'] ?></p>
		<span class="shoujo-info-container-owner">Owner: <?= $information['owner'] ?></span>
        <span class="shoujo-info-container-play"><a href="new.php?shoujo=<?= generate_shoujo_hash($information['id']) ?>">Play!</a></span>
	</div>
<?php
}
function shuffle_bits($bits)
{
    $positions = array(20, 35, 33, 21, 10, 34, 37, 11, 1, 3, 2, 6, 0, 27, 32, 39, 29, 18, 4, 9, 38, 17, 24, 5, 8, 15, 16, 19, 25, 14, 28, 12, 31, 22, 23, 13, 26, 36, 30, 7);
    $to_return = '';
    for($loop = 0; $loop < 40; $loop++)
    {
        $to_return .= $bits{$positions[$loop]};
    }
    return $to_return;
}
function unshuffle_bits($bits)
{
    $positions = array(20, 35, 33, 21, 10, 34, 37, 11, 1, 3, 2, 6, 0, 27, 32, 39, 29, 18, 4, 9, 38, 17, 24, 5, 8, 15, 16, 19, 25, 14, 28, 12, 31, 22, 23, 13, 26, 36, 30, 7);
    $to_return = array();
    for($loop = 0; $loop < 40; $loop++)
    {
        $to_return[$positions[$loop]] = $bits{$loop};
    }
    $helper = '';
    for($loop = 0; $loop < 40; $loop++)
    {
        $helper .= $to_return[$loop];
    }
    return $helper;
}
function generate_shoujo_hash($id)
{
    $array = array('1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W');
    $random_byte = '11111111';
    $string = decbin($id);
    $string = str_pad($string, 32, '0', STR_PAD_LEFT);
    $string = shuffle_bits($random_byte.$string);
    $bitarray = str_split($string, 5);
    $to_return = '';
    for($loop = 7; $loop >= 0; $loop--)
    {
        $to_return .= $array[bindec($bitarray[$loop])];
    }
    return $to_return;
}
function reverse_shoujo_hash($hash)
{
    $array = array('1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W');
    $random_byte = '11111111';
    $hash_array = str_split($hash, 1);
    $bits = '';
    for($loop = 7; $loop >= 0; $loop--)
    {
        $bits .= str_pad(decbin(array_search($hash_array[$loop], $array)), 5, '0', STR_PAD_LEFT);
    }
    return bindec(substr(unshuffle_bits($bits), 8));
}
function valid_shoujo_hash($hash)
{
    if (
        strlen($hash) == 8 &&
        ctype_alnum($hash) &&
        ctype_upper(preg_replace("/[0-9]/", "", $hash))
        && stripos($hash, '0') === false &&
        stripos($hash, 'X') === false &&
        stripos($hash, 'Y') === false &&
        stripos($hash, 'Z') === false)
    {
        return true;
    }
    else
    {
        return false;
    }
}
function timestamp_to_shoujo_time($timestamp)
{
    $hour = date('G', $timestamp);
    $to_return = false;
	if ($hour >= 6 && $hour < 12)
	{
		$to_return = 1;
	}
	else if ($hour >= 12 && $hour < 18)
	{
		$to_return = 2;
	}
	else if ($hour >= 18 || $hour < 0)
	{
		$to_return = 4;
	}
	else
	{
		$to_return = 8;
	}
    return $to_return;
}
class mysql_helper
{
	static $mysql;
}
?>