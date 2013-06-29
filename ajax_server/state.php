<?php
require_once "../config/include-all.php";
if(!isset($_SESSION["username"]))
{
    die_session(constants_playing_state::not_logged_in);
}
if (!isset($_GET['shoujo']))
{
    die_session(constants_playing_state::shoujo_doesnt_exist);
}
$shoujo_id = $_GET['shoujo'];
if(!valid_shoujo_hash($shoujo_id) && !shoujo_exists($shoujo_id))
{
    die_session(constants_playing_state::shoujo_doesnt_exist);
}
else
{
    $shoujo_id = reverse_shoujo_hash($shoujo_id);
}
if (isset($_GET['start_playing']) && user_is_playing_with_shoujo($shoujo_id, $_SESSION["username"]))
{
    if (!is_array($_SESSION["playing-shoujos"]))
    {
        $_SESSION["playing-shoujos"] = array();
    }
    if (array_key_exists($shoujo_id, $_SESSION["playing-shoujos"]))
    {
        die_session(constants_playing_state::already_playing);
    }
    $_SESSION["playing-shoujos"][$shoujo_id] = array();
    die_session(constants_playing_state::ok);
}
if(!array_key_exists($shoujo_id, $_SESSION["playing-shoujos"]))
{
    die_session(constants_playing_state::not_playing_session);
}
$state = $_SESSION["playing-shoujos"][$shoujo_id];
if (isset($_GET["start-activity"]))
{
    if (array_key_exists($state["current-activity"]))
    {
        die_session(constants_playing_state::already_playing_with_an_activity);
    }
    else
    {
        if (isset($_GET["activity-source"]))
        {
            switch($_GET["activity-source"])
            {
            case constants_activity::source_reserved:
                die_session(constants_playing_state::error);
                break;
            case constants_activity::source_option:
                if (isset($_GET['option-number']))
                {
                    if (
                        preg_match("/[0-9]/", $_GET['option-number']) &&
                        option_number_exists($shoujo_id, $_GET['option-number']))
                    {
                        if (!is_option_in_time($shoujo_id, $_GET['option-number'], timestamp_to_shoujo_time(time())))
                        {
                            die_session(constants_playing_state::not_in_time);
                        }
                        else if(user_has_done_option($shoujo_id, $_SESSION["username"], $_GET['option-number']))
                        {
                            die_session(constants_playing_state::activity_already_done);
                        }
                        else
                        {
                            $option = get_option_info($shoujo_id, $_GET['option-number']);
                            $activity = get_activity_info($shoujo_id, $option['activity_id']);
                            $state["current-activity"]["activity"] = $activity;
                            $state["current-activity"]["start"] = time();
                            $state["current-activity"]["options"] = array();
                        }
                    }
                    else
                    {
                        die_session(constants_playing_state::error);
                    }
                }
                break;
            case constants_activity::source_event:
                die_session(constants_playing_state::error);
                break;
            }
        }
    }
}
else if(isset($_GET["select-option"]))
{
    if (!array_key_exists($state["current-activity"]))
    {
        die_session(constants_playing_state::not_playing_with_activity);
    }
    else
    {
        if (valid_option_child($shoujo_id, $state["current-activity"]["activity_id"], end($state["current-activity"]["options"], $_GET["select-option"])))
        {
            $state["current-activity"]["options"][] = $_GET["select-option"];
        }
    }
}
else if(isset($_GET["end-activity"]))
{
    if (!array_key_exists($state["current-activity"]))
    {
        die_session(constants_playing_state::not_playing_with_activity);
    }
    else
    {
        $seconds = calculate_total_seconds_activity($shoujo_id, $state["current-activity"]["activity"]["activity_id"], $state["current-activity"]["options"]);
        if (time() >= $state["current-activity"]["start"] + $seconds)
        {
            update_status(get_status_array_activity($shoujo_id, $state["current-activity"]["activity"]["activity_id"], $state["current-activity"]["options"]), $_SESSION["username"]);
        }
    }
}
die_session(constants_playing_state::ok);

function die_session($status_code)
{
    if (isset($shoujo_id) && isset($_SESSION["playing-shoujos"][$shoujo_id]))
    {
        unset($_SESSION["playing-shoujos"][$shoujo_id]);
    }
    die_session($status_code);
}