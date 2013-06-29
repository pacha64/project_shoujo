<?php
function update_with_xml($shoujo_id, $xml)
{
    escape_string($shoujo_id);
    query("DELETE FROM shoujo_activity_array WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_activity_image WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_activity_friendly_name WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_activity_options WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_events WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_images WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_menu WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_status WHERE shoujo_id=".$shoujo_id);
    query("DELETE FROM shoujo_variables WHERE id=".$shoujo_id);
    $xml = simplexml_load_string($xml);
    $name = $xml->information->name;
    $description = $xml->information->description;
    escape_string($name);
    escape_string($description);
    query("UPDATE shoujo_info SET name='$name', description='$description' WHERE id=$shoujo_id");
    foreach($xml->images->faces as $face)
    {
        $face = $face->face;
        escape_string($face);
        query("INSERT INTO shoujo_images VALUES($shoujo_id, 1, '$face', 'jpg')");
    }
    foreach($xml->images->backgrounds as $background)
    {
        $background = $background->background;
        escape_string($background);
        query("INSERT INTO shoujo_images VALUES($shoujo_id, 0, '$background', 'jpg')");
    }
    foreach($xml->variables->variable as $variable)
    {
        $name = $variable->name;
        $value = $variable->value;
        escape_string($name);
        escape_string($value);
        query("INSERT INTO shoujo_variables VALUES($shoujo_id, '$name', '$value')");
    }
    foreach($xml->status_collection->name as $status)
    {
        escape_string($status);
        query("INSERT INTO shoujo_status VALUES($shoujo_id, '$status', 1, 1, 1, 5)");
    }
    foreach ($xml->options->option as $opt)
    {
        $number = $opt->number;
        switch($number)
        {
            case '0':
                recursive_option_add_xml($shoujo_id, '0', $opt->options);
                break;
            case '1':
                recursive_option_add_xml($shoujo_id, '1', $opt->options);
                break;
        }
    }
    foreach($xml->activities->activity_array as $activity)
    {
        $id = $activity['id'];
        foreach($activity->activity as $individual)
        {
            $number = $individual['number'];
            $type = $individual->type;
            $first_value = '';
            $second_value = '';
            switch($type)
            {
            case constants_activity::type_text:
                $first_value = $individual->text;
                break;
            case constants_activity::type_countdown:
                $first_value = $individual->time;
                break;
            case constants_activity::type_status:
                $first_value = $individual->status;
                $second_value = $individual->value;
                break;
            case constants_activity::type_option:
                
                break;
            }
            escape_string($id);
            escape_string($number);
            escape_string($type);
            escape_string($first_value);
            escape_string($second_value);
            query("INSERT INTO shoujo_activity_array VALUES($shoujo_id, $id, $type, $number, '$first_value', '$second_value')");
            if ($individual->face != null && has_face($shoujo_id, $individual->face))
            {
                $face = $individual->face;
                escape_string($face);
                query("INSERT INTO shoujo_activity_image VALUES($shoujo_id, $id, $number, 1, '$face')");
            }
            if (isset($individual['background']) && has_background($shoujo_id, $individual['background']))
            {
                $background = $individual['background'];
                escape_string($background);
                query("INSERT INTO shoujo_activity_image VALUES($shoujo_id, $id, $number, 0, '$background')");
            }
            if ($type == constants_activity::type_option)
            {
                break;
            }
        }
    }
}

function recursive_option_add_xml($shoujo_id, $parent, $xml)
{
    foreach($xml->option as $option)
    {
        $text = $option->text;
        $number = $option->number;
        $time = $option->time;
        $type = $option->type;
        $once_per_day = 0;
        $activity_id = -1;
        if ($option['once-per-day'] == 1)
        {
            $once_per_day = 1;
        }
        switch($type)
        {
            case constants_menu::option_submenu:
                recursive_option_add_xml($shoujo_id, $number, $option->options);
                break;
            case constants_menu::option_activity:
                $activity_id = $option->activity_id;
                break;
        }
        if ($type == constants_menu::option_event || $type == constants_menu::option_status)
        {
            continue;
        }
        escape_string($text);
        escape_string($number);
        escape_string($time);
        escape_string($type);
        escape_string($once_per_day);
        escape_string($parent);
        escape_string($activity_id);
        query("INSERT INTO shoujo_menu VALUES($shoujo_id, $time, $once_per_day, $parent, $number, $type, '$text', $activity_id)");
    }
}
?>
