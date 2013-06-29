<?php
	require_once "../config/include-all.php";
	if(!isset($_GET['shoujo']))
	{
		die("No shoujo selected");
	}
	$id = $_GET['shoujo'];
    $hash = $id;
    if(!valid_shoujo_hash($id))
    {
        die('here');
		die("Shoujo doesn't exist");
    }
    else
    {
        $id = reverse_shoujo_hash($id);
    }
    $manage_mode = false;
    if (isset($_GET['manage-mode']))
    {
        $manage_mode = true;
    }
    if(!shoujo_exists($id))
	{
		die("Shoujo doesn't exist");
	}
	$info_array = shoujo_get_all_infromation($id);
	$xml = new SimpleXMLElement("<shoujo></shoujo>");
    if($manage_mode)
    {
        $xml->addAttribute('manage-mode', '1');
    }
	$information = $xml->addChild("information");
	$information->addChild('name', $info_array['name']);
	$information->addChild('description', $info_array['description']);
	$information->addChild('id', $hash);
	$status_collection = get_all_status($id);
	$stats = $xml->addChild("status_collection");
	foreach($status_collection as $status_ind)
	{
		if($status_ind['is_visible'])
		{
			$status_xml = $stats->addChild('name', $status_ind['name']);
            if ($manage_mode)
            {
                $status_xml->addAttribute('hidden', $status_ind['is_visible'] ? '0' : '1');
                $status_xml->addAttribute('decrease-time', $status_ind['decrease_time']);
            }
		}
	}
	recursive_add_submenu($xml->addChild("options"), get_menu_array($id));
	$events = $xml->addChild("events");
	foreach(get_event_array($id) as $event_ind)
	{
		$event = $events->addChild("event");
		$event->addChild('name', $event_ind['name']);
		$event->addChild('description', $event_ind['description']);
		$event->addChild('activity_id', $event_ind['activity_number']);
		$event->addChild('date_start', $event_ind['start_date']);
		$event->addChild('date_finish', $event_ind['finish_date']);
		$event->addChild('time', $event_ind['time']);
	}
	$activities = $xml->addChild("activities");
	foreach(get_activity_array_collection($id) as $key=>$activity_array)
	{
		$activity = $activities->addChild("activity_array");
		$activity->addAttribute('id', $key);
		foreach ($activity_array as $key_child=>$action)
		{
			$action_xml = $activity->addChild('activity');
			$action_xml->addAttribute('number', $key_child);
			if($action['face'] != '')
			{
				$action_xml->addChild('face', $action['face']);
			}
			if($action['background'] != '')
			{
				$action_xml->addChild('background', $action['background']);
			}
			$action_xml->addChild('type', $action['type']);
			switch($action['type'])
			{
			case constants_activity::type_text:
				$action_xml->addChild('text', $action['text']);
				break;
			case constants_activity::type_countdown:
				$action_xml->addChild('time', $action['time']);
				break;
			case constants_activity::type_status:
				$action_xml->addChild('status', $action['status']);
				$action_xml->addChild('value', $action['value']);
				break;
			case constants_activity::type_option:
				foreach($action['options'] as $option)
				{
					$option_xml = $action_xml->addChild('option');
					$option_xml->addAttribute('number', $option['number']+1);
					$option_xml->addChild('text', $option['text']);
					$option_xml->addChild('activity_id', $option['activity_id']);
				}
				break;
			}
		}
	}
	$images = $xml->addChild('images');
	$image_array = get_all_images($id);
	$face_xml = $images->addChild('faces');
	$background_xml = $images->addChild('backgrounds');
	foreach($image_array['face'] as $face_ind)
	{
		$face_xml->addChild('face', $face_ind);
	}
	foreach($image_array['background'] as $background_ind)
	{
		$background_xml->addChild('background', $background_ind);
	}
	$variables = $xml->addChild('variables');
    foreach(get_all_variables($id) as $name=>$value)
    {
        $var = $variables->addChild('variable');
        $var->addChild('name', $name);
        $var->addChild('value', $value);
    }
	header('Content-type: text/xml');
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    echo $dom->saveXML();
	
	function recursive_add_submenu($xml_to_append, $array)
	{
		foreach($array as $element)
		{
			$option = $xml_to_append->addChild("option");
            if ($element['once_per_day'] == 1)
            {
                $option->addAttribute('once-per-day', '1');
            }
			$option->addChild("text", $element["text"]);
			$option->addChild("type", $element["type"]);
			$option->addChild("number", $element["number"]);
			$option->addChild("time", $element["time"]);
			switch($element["type"])
			{
				case constants_menu::option_submenu:
					recursive_add_submenu($option->addChild("options"), $element["child"]);
					break;
				case constants_menu::option_activity:
					$option->addChild("activity_id", $element["activity_id"]);
					break;
				case constants_menu::option_event:
				case constants_menu::option_status:
					break;
			}
		}
	}
?>