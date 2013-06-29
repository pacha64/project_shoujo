<?php
function status_is_private($shoujo_id, $status)
{
	$status_collection = get_all_status($shoujo_id);
	foreach($status_collection as $status_ind)
	{
		if ($status_ind["name"] == $status && $status_ind["is_visible"])
		{
			return true;
		}
	}
	return false;
}
function get_activity_info($shoujo_id, $activity_number)
{
	escape_string($shoujo_id);
	escape_string($activity_number);
	$query = query("SELECT * FROM shoujo_common_activities WHERE shoujo_id=$shoujo_id AND activity_number=$activity_number");
	return mysqli_fetch_assoc($query);
}
function get_option_info($shoujo_id, $option_number)
{
	escape_string($shoujo_id);
	escape_string($activity_number);
	$query = query("SELECT * FROM shoujo_menu WHERE shoujo_id=$shoujo_id AND number=$activity_number");
	return mysqli_fetch_assoc($query);
}
function calculate_total_seconds_activity($shoujo_id, $activity_id, $options_selected)
{
	escape_string($shoujo_id);
	escape_string($activity_id);
    foreach($options_selected as &$selected)
    {
        escape_string($selected);
    }
	$query = query("SELECT first_value FROM shoujo_activity_array WHERE type=".constants_activity::type_countdown." AND shoujo_id=$shoujo_id AND activity_id=$activity_id");
	$time = 0;
	while($result = mysql_fetch_array($query))
	{
		$time += $result[0];
	}
	foreach($options_selected as $option_ind)
	{
		$option_ind = mysqli_real_escape_string(mysql_helper::$mysql, $option_ind);
		$query = query("SELECT first_value FROM shoujo_activity_array WHERE type=".constants_activity::type_countdown." AND shoujo_id=$shoujo_id AND activity_id=$activity_id AND parent=".$option_ind);
		while($result = mysql_fetch_array($query))
		{
			$time += $result[0];
		}
	}
	return $time;
}
function get_status_array_activity($shoujo_id, $activity_id, $options_selected)
{
	$shoujo_id = mysqli_real_escape_string(mysql_helper::$mysql, $shoujo_id);
	$activity_id = mysqli_real_escape_string(mysql_helper::$mysql, $activity_id);
	$options_selected = mysqli_real_escape_string(mysql_helper::$mysql, $options_selected);
	$query = query("SELECT first_value, second_value FROM shoujo_activity_array WHERE type=".constants_activity::type_status." AND shoujo_id=$shoujo_id AND activity_id=$activity_id");
	$status = array();
	while($result = mysql_fetch_array($query))
	{
		if (array_key_exists($result[0], $status))
		{
			$status[$result[0]] += $result[1];
		}
		else
		{
			$status[$result[0]] = $result[1];
		}
	}
	foreach($options_selected as $option_ind)
	{
		$option_ind = mysqli_real_escape_string(mysql_helper::$mysql, $option_ind);
		$query = query("SELECT first_value FROM shoujo_activity_array WHERE type=".constants_activity::type_status." AND shoujo_id=$shoujo_id AND activity_id=$activity_id AND parent=".$option_ind);
		while($result = mysql_fetch_array($query))
		{
			if (array_key_exists($result[0], $status))
			{
				$status[$result[0]] += $result[1];
			}
			else
			{
				$status[$result[0]] = $result[1];
			}
		}
	}
	return $status;
}
function valid_option_child($shoujo_id, $activity_id, $option_parent, $option_child)
{
}
function get_menu_array($shoujo_id)
{
	escape_string($shoujo_id);
	$array = array();
	$array[0]["text"] = "daily activities";
	$array[0]["type"] = constants_menu::option_submenu;
	$array[0]["child"] = get_submenu_array($shoujo_id, 0);
	$array[0]["number"] = 0;
	$array[0]["time"] = 15;
	$array[0]["once_per_day"] = '0';
	$array[1]["text"] = "special activities";
	$array[1]["type"] = constants_menu::option_submenu;
	$array[1]["child"] = get_submenu_array($shoujo_id, 1);
	$array[1]["number"] = 1;
	$array[1]["time"] = 15;
	$array[1]["once_per_day"] = '0';
	$array[2]["text"] = "status";
	$array[2]["type"] = constants_menu::option_status;
	$array[2]["number"] = 2;
	$array[2]["time"] = 15;
	$array[2]["once_per_day"] = '0';
	$array[3]["text"] = "events";
	$array[3]["type"] = constants_menu::option_event;
	$array[3]["number"] = 3;
	$array[3]["time"] = 15;
	$array[3]["once_per_day"] = '0';
	return $array;
}
function get_submenu_array($shoujo_id, $parent)
{
	$query = query("SELECT * FROM shoujo_menu WHERE parent=$parent");
	$return = array();
	if (mysqli_num_rows($query) == 0) return $return;
	while($result = mysqli_fetch_assoc($query))
	{
		$append = array();
		$append['text'] = $result['text'];
		$append['type'] = $result['type'];
		$append['number'] = $result['number'];
		$append['time'] = $result['time'];
		$append['once_per_day'] = $result['once_per_day'];
		switch($append['type'])
		{
			case constants_menu::option_submenu:
				$append['child'] = get_submenu_array($shoujo_id, $result['number']);
				break;
			case constants_menu::option_activity:
				$append['activity_id'] = $result['activity_id'];
				break;
			case constants_menu::option_status:
			case constants_menu::option_event:
				break;
		}
		$return[] = $append;
	}
	return $return;
}
function get_event_array($shoujo_id)
{
	escape_string($shoujo_id);
	$query = query("SELECT * FROM shoujo_events WHERE shoujo_id=$shoujo_id");
	$return = array();
	while($result = mysqli_fetch_assoc($query))
	{
		$return[] = $result;
	}
	return $return;
}
function get_activity_array_collection($shoujo_id)
{
	escape_string($shoujo_id);
	$query = query("SELECT * FROM shoujo_activity_options WHERE shoujo_id=$shoujo_id");
	$options = array();
	$return = array();
	while($result = mysqli_fetch_assoc($query))
	{
		$options[$result['activity_id']][$result['number']]['activity_id'] = $result['links_to'];
		$options[$result['activity_id']][$result['number']]['number'] = $result['number'];
		$options[$result['activity_id']][$result['number']]['text'] = $result['text'];
	}
	$query = query("SELECT * FROM shoujo_activity_array WHERE shoujo_id=$shoujo_id");
	while($result = mysqli_fetch_assoc($query))
	{
		$pos = $result['activity_id'];
		$number = $result['number'];
		$return[$pos][$number]['face'] = get_face_activity($shoujo_id, $pos, $number);
		$return[$pos][$number]['background'] = get_background_activity($shoujo_id, $pos, $number);
		$return[$pos][$number]['type'] = $result['type'];
		switch($result['type'])
		{
        case constants_activity::type_text:
            $return[$pos][$number]['text'] = $result['first_value'];
            break;
        case constants_activity::type_countdown:
            $return[$pos][$number]['time'] = $result['first_value'];
            break;
        case constants_activity::type_status:
            $return[$pos][$number]['status'] = $result['first_value'];
            $return[$pos][$number]['value'] = $result['second_value'];
            break;
        case constants_activity::type_option:
            $return[$pos][$number]['options'] = $options[$pos];
            break;
		}
	}
	return $return;
}
function option_number_exists($shoujo_id, $option_number)
{
	escape_string($shoujo_id);
	escape_string($option_number);
	$query = query("SELECT number FROM shoujo_menu WHERE number=$option_number AND shoujo_id=$shoujo_id");
	if (mysqli_num_rows($query) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
function is_option_in_time($shoujo_id, $option_number, $time)
{
    escape_string($shoujo_id);
    escape_string($option_number);
    escape_string($time);
    $query = query("SELECT parent, time FROM shoujo_menu WHERE shoujo_id=$shoujo_id AND number=$number");
    $result = mysqli_fetch_assoc($query);
    if (is_in_time($result['time'], $time))
    {
        if ($result['parent'] > 5)
        {
            return is_option_in_time($shoujo_id, $result['parent'], $time);
        }
        else
        {
            return true;
        }
    }
    else
    {
        return false;
    }
}
function is_in_time($time, $compare)
{
    return !!($time & $compare);
}
?>