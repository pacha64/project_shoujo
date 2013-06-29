<?php
$type = constants_status::decrease_hourly;
$time = date('H');
if ($time == 6)
{
	$type = constants_status::decrease_day;
}
switch($type)
{
	case constants_status::decrease_day:
		query("UPDATE user_shoujo_status AS user INNER JOIN shoujo_status AS shoujo ON user.status = shoujo.name SET user.value = user.value - shoujo.decrease_amount WHERE user.status = shoujo.name AND shoujo.decrease_time=".constants_status::decrease_hourly." OR shoujo.decrease_time=".constants_status::decrease_day);
		query("TRUNCATE user_shoujo_common_activities_done");
		break;
	case constants_status::decrease_hourly:
		query("UPDATE user_shoujo_status AS user INNER JOIN shoujo_status AS shoujo ON user.status = shoujo.name SET user.value = user.value - shoujo.decrease_amount WHERE user.status = shoujo.name AND shoujo.decrease_time=".constants_status::decrease_hourly);
		break;
}
?>