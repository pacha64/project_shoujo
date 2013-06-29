<?php
	require_once "config/include-all.php";
	if(!isset($_SESSION["username"]))
	{
		header("Location: ".constants::site_main."/login.php");
	}
	elseif(!isset($_GET["shoujo"]))
	{
		header("Location: ".constants::site_main);
	}
	elseif(!user_has_shoujo($_SESSION["username"], $_GET["shoujo"]))
	{
		header("Location: ".constants::site_main);
	}
	$shoujo_id = $_GET["shoujo"];
	$username = $_SESSION["username"];
	/*if(isset($_POST["new-variable-name"], $_POST["new-variable-value"]))
	{
		if (substr($_POST["new-variable-name"], 0, 1) == '%')
		{
			$_POST["new-variable-name"] = substr($_POST["new-variable-name"], 1);
		}
		if(valid_variable_name($_POST["new-variable-name"]) && valid_variable_value($_POST["new-variable-value"]))
		{
			$_POST["new-variable-name"] = strtoupper($_POST["new-variable-name"]);
			add_variable($shoujo_id, $_POST["new-variable-name"], $_POST["new-variable-value"]);
			echo mysqli_error(mysql_helper::$mysql);
		}
	}
	else if(isset($_GET["del-variable"]))
	{
		remove_variable($shoujo_id, $_GET["del-variable"]);
	}
	else if(isset($_POST["parent"], $_POST["new-submenu-name"], $_POST["new-submenu-type"], $_POST["new-submenu-time"]))
	{
        $time = 0;
        $one_per_day = 0;
        if (isset($_POST['one_per_day']))
        {
            $one_per_day = 1;
        }
        if(is_array($_POST["new-submenu-time"]))
        {
            foreach($_POST["new-submenu-time"] as $flag)
            {
                if($flag >= 0 && $flag <= 3)
                {
                    $time ^= 1 << $flag;
                }
            }
        }
        if ($time != 0)
        {
            $name = $_POST["new-submenu-name"];
            $parent = $_POST['parent'];
            $type = $_POST['new-submenu-type'];
            escape_string($name);
            escape_string($parent);
            escape_string($type);
            query("INSERT INTO shoujo_menu VALUES($shoujo_id, $time, $one_per_day, $parent, ".get_latest_submenu_number($shoujo_id)."+1, $type, '$name', ".(get_latest_activity_id($shoujo_id)."+1)"));
        }
	}*/
	$face_url = get_avatar_url($shoujo_id);
	$shoujo_information = shoujo_get_all_infromation($shoujo_id);
	$shoujo_variables = get_all_variables($shoujo_id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage <?= $shoujo_information["name"] ?></title>
	<link rel="stylesheet" type="text/css" href="css/site.css" />
	<link rel="stylesheet" type="text/css" href="jquery-ui/jquery-ui-1.10.1.custom.min.css" />
	<link rel="stylesheet" type="text/css" href="css/manage.css" />
    <meta name="description" content="Project Shoujo">
	<script src="javascript/underscore-min.js"></script>
	<script src="javascript/jquery-1.9.1-min.js"></script>
	<script src="javascript/url_params.js"></script>
	<script src="javascript/jquery-ui-1.10.1.custom.min.js"></script>
    <script src="javascript/object_manager.js?no-cache=<?= microtime() ?>"></script>
	<script src="javascript/ajax.js?no-cache=<?= microtime() ?>"></script>
	<script src="javascript/manage.js?no-cache=<?= microtime() ?>"></script>
	<script src="javascript/manage_parser.js?no-cache=<?= microtime() ?>"></script>
	<script>
        // globals
        var URL_PROCESSING = '<?= constants::site_main ?>/processing.php'
        // global config loaded from PHP
        var MAX_DESCRIPTION_LENGTH = '<?= constants::shoujo_description_max_lenght ?>'
        var MAX_NAME_LENGTH = '<?= constants::shoujo_name_max_lenght ?>'
        var DEFAULT_NAME = '<?= constants::shoujo_default_name ?>'
        var DEFAULT_DESCRIPTION = '<?= constants::shoujo_default_description ?>'
        // status
        var STATUS_DECREASE_NONE = '<?= constants_status::decrease_none ?>'
        var STATUS_DECREASE_HOUR = '<?= constants_status::decrease_hourly ?>'
        var STATUS_DECREASE_DAY = '<?= constants_status::decrease_day ?>'
        // menu
        var MENU_MAX_LENGTH = '<?= constants_menu::max_length_name ?>'
        // activities
        var ACTIVITY_TIME_MAX = '<?= constants_activity::countdown_max_seconds ?>'
        var ACTIVITY_STATUS_MAX_VALUE = '<?= constants_activity::status_max_value ?>'
        
		var textarea_interval_id
		$(document).ready(function()
		{
			init()
			$("#add-activity-child-button").click(function(e) {
				$("#activity-holder").append($("#cloning-container #clone-new-activity").clone())
			})
			$(document).on('change', '.test select', function(e) {
				switch($(this).children("option:selected").val())
				{
					case ACTIVITY_TYPE_TEXT:
						$(this).parent().children('div').first().empty()
						$(this).parent().children('div').first().append($("#clone-new-activity-text").children().clone())
						$(this).parent().children('div').first().data('type', 'text')
						break
					case ACTIVITY_TYPE_COUNTDOWN:
						$(this).parent().children('div').first().empty()
						$(this).parent().children('div').first().append($("#clone-new-activity-countdown").children().clone())
						$(this).parent().children('div').first().data('type', 'countdown')
						break
					case ACTIVITY_TYPE_STATUS:
						$(this).parent().children('div').first().empty()
						$(this).parent().children('div').first().append($("#clone-new-activity-status").children().clone())
						$(this).parent().children('div').first().data('type', 'status')
						repopulate_status()
						break
					case ACTIVITY_TYPE_OPTION:
						$(this).parent().children('div').first().empty()
						$(this).parent().children('div').first().append($("#clone-new-activity-option").children().clone())
						$(this).parent().children('div').first().data('type', 'option')
						break
					default:
						$(this).parent().children('div').first().empty()
						$(this).parent().children('div').first().text("Select activity type:")
						$(this).parent().children('div').first().data('type', 'select-type')
						break
				}
			})
			$('#activity-submit-helper').click(function()
			{
				$('#info-form').empty()
				$('#info-form').append('<input autocomplete="off" type="hidden" name="action-type" value="new-activity" />')
				$('#info-form').append('<input autocomplete="off" type="hidden" name="shoujo" value="'+$(document).getUrlParam('shoujo')+'" />')
				var counter = 0
				$('#activity-holder').children('div').each(function()
				{
					var div = $(this).children('div').first()
					switch(div.data('type'))
					{
					case 'text':
						var val = div.children('textarea').first().val()
						$('#info-form').append('<input autocomplete="off" type="hidden" name="type[]" value="text" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="number[]" value="'+(counter++)+'" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="value-1[]" value="'+val+'" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="value-2[]" value="" />')
						break
					case 'countdown':
						var val = div.children('input').first().val()
						$('#info-form').append('<input autocomplete="off" type="hidden" name="type[]" value="countdown" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="number[]" value="'+(counter++)+'" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="value-1[]" value="'+val+'" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="value-2[]" value="" />')
						break
					case 'status':
						var val = div.children('input').first().val()
						var status = div.children('select').find(':selected').text()
						$('#info-form').append('<input autocomplete="off" type="hidden" name="type[]" value="status" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="number[]" value="'+(counter++)+'" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="value-1[]" value="'+status+'" />')
						$('#info-form').append('<input autocomplete="off" type="hidden" name="value-2[]" value="'+val+'" />')
						break
					case 'option':
						var option_number = 1
						$('#info-form').append('<input autocomplete="off" type="hidden" name="type[]" value="option" />')
						div.children('div').each(function(){
							if ($(this).find('input[type="checkbox"]').first().is(':checked') && $(this).find('input[type="text"]').first().val())
							{
								var text = $(this).find('input[type="text"]').first().val()
								$('#info-form').append('<input autocomplete="off" type="hidden" name="option-value-'+(option_number++)+'" value="'+text+'" />')
							}
						})
						return
					}
				})
				var form = $('#info-form')
				$.post(
					form.attr('action'),
					form.serialize(),
					function(){}
				)
			})
			textarea_interval_id = setInterval(function()
			{
			}, 50)
		})
	</script>
</head>
<body>
<iframe name="dummy-iframe" id="dummy-iframe" style="display:none;"></iframe>
<div id="manage-loading">
	<p>Getting information from server, please wait</p>
</div>
<div id="manage-container">
    <a id="manage-back">back</a>
    <h1 id="manage-title-name">Manage <span class="name-global"><?= $shoujo_information["name"] ?></span></h1>
  <div id="manage-options">
	<div class="shoujo-info-container">
		<div class="shoujo-info-container-img">
			<img src="<?= $face_url ?>" id="avatar-main-menu" />
		</div>
		<h3 class="name-global"><?= $shoujo_information["name"] ?></h3>
		<p class="description-global"><?= $shoujo_information["description"] ?></p>
		<span class="shoujo-info-container-play"><a target="_blank" href="new.php?shoujo=4">Play!</a></span>
	</div>
      <form id="root-save-form" method="post" action="processing.php">
          <input autocomplete="off" type="hidden" name="shoujo-id" value="<?= $shoujo_id ?>" />
          <input autocomplete="off" type="hidden" name="action-type" value="save-information" />
          <input autocomplete="off" type="hidden" name="xml" />
      </form>
      <form id="root-back-up-form" target="dummy-iframe" method="post" action="processing.php">
          <input autocomplete="off" type="hidden" name="shoujo-id" value="<?= $shoujo_id ?>" />
          <input autocomplete="off" type="hidden" name="action-type" value="download-back-up" />
      </form>
      <p class="manage-root-buttons"><a id="root-save">Save</a></p>
      <p class="manage-root-buttons"><a id="root-save-back-up">Save and download back-up</a></p>
      <p class="manage-root-buttons"><a id="root-back-up">Download back-up of last saved</a></p>
      <p class="manage-root-buttons" id="root-last-saved">last saved: never</p>
    <p><a id="show-information">Manage basic information</a></p>
    <p><a id="show-images">Manage faces/backgrounds</a></p>
    <p><a id="show-menu">Manage menu</a></p>
    <p><a id="show-activities">Manage activities</a></p>
    <p><a id="show-variables">Manage variables</a></p>
    <p><a id="show-status">Manage status</a></p>
    <p><a id="show-events">Manage events</a></p>
  </div>
    <div id="manage-information">
        <h2>Manage information</h2>
        <img src="<?= $face_url ?>" id="information-avatar" />
        <br />
        <form method="post" style="display:none;" id="manage-information-change-avatar" enctype="multipart/form-data" >
            <input autocomplete="off" type="file" name="new-avatar" id="manage-information-file-avatar" />
            <input autocomplete="off" type="hidden" name="action-type" value="new-avatar" />
            <input autocomplete="off" type="hidden" name="shoujo-id" value="<?= $shoujo_id; ?>" />
        </form>
        <p><a id="manage-information-file-anchor-upload">change avatar</a><span id="manage-information-file-status-upload"></span></p>
        <p><input autocomplete="off" type="text" id="information-name" value="<?= $shoujo_information["name"] ?>" maxlength="<?= constants::shoujo_name_max_lenght; ?>" /> <span id="information-name-characters-left"></span> characters left</p>
        <textarea id="information-description"><?= $shoujo_information["description"] ?></textarea>
        <p><span id="information-description-characters-left"></span> characters left</p>
    </div>
    <div id="manage-images">
        <h2>Faces</h2>
        <h3>Upload new face</h3>
        <form method="post" enctype="multipart/form-data" id="manage-images-face-new-form">
            <input autocomplete="off" type="hidden" name="shoujo-id" value="<?= $shoujo_id ?>" />
            <input autocomplete="off" type="hidden" name="action-type" value="new-face" />
            <input autocomplete="off" type="file" name="face" style="display:none;" id="manage-images-face-file" />
            <p>Face name: <input autocomplete="off" type="text" name="face-name" /> - <a id="manage-images-face-select-image-anchor">select image</a> - <a id="manage-images-face-upload-anchor">upload</a><span id="manage-images-face-error"></span><span id="manage-images-face-filename"></span></p>
        </form>
        <h3>View/modify/delete faces</h3>
        <div id="manage-images-modify-delete-faces-container-no-faces" style="display:none;">
            <p>You haven't uploaded any face yet</p>
        </div>
        <div id="manage-images-modify-delete-faces-container" style="display:none;">
            <p>Be careful when you delete an image, all the references in the activities will be deleted as well (there is no going back).</p>
            <select id="manage-images-modify-delete-faces-select">
            </select>
            <p>Name: <input autocomplete="off" type="text" id="manage-face-name" /> - <a id="manage-face-change-name">change</a></p>
            <div id="manage-images-modify-delete-faces-container-not-deleted" style="display:none;">
                <p><a target="_blank" id="manage-face-open-current">open</a><div id="manage-face-delete-container"> - <a id="manage-face-delete">delete</a></div></p>
            </div>
            <div id="manage-images-modify-delete-faces-container-deleted" style="display:none;">
                <p><a id="manage-face-undo-deletion">undo deletion</a></p>
            </div>
        </div>
        <h2>Backgrounds</h2>
    </div>
  <div id="manage-menu">
    <h2>Manage menu</h2>
    <div id="menu-container">
      <ol>
      </ol>
    </div>
    <div id="menu-add-option">
    	<h3>Add a new option</h3>
        <p>New activity name: <input autocomplete="off" type="text" name="new-submenu-name" maxlength="<?= constants_menu::max_length_name ?>" /> - <a id="menu-add-new-create-button">create</a></p>
        <p><span id="new-submenu-characters-left"><?= constants_menu::max_length_name ?></span> characters left</p>
        <select name="new-submenu-type">
            <option value="<?= constants_menu::option_submenu ?>">Sub-options</option>
            <option value="<?= constants_menu::option_activity ?>">Activity</option>
        </select>
        <p>Morning? <input autocomplete="off" class="checkbox" name="new-submenu-time" type="checkbox" value="0" /> Afternoon? <input autocomplete="off" class="checkbox" name="new-submenu-time" type="checkbox" value="1" /> Evening? <input autocomplete="off" class="checkbox" name="new-submenu-time" type="checkbox" value="2" /> Night? <input autocomplete="off" class="checkbox" name="new-submenu-time" type="checkbox" value="3" /></p>
        <p>Once per day?<input autocomplete="off" class="checkbox" name="new-submenu-once-per-day" type="checkbox" value="1" /></p>
    </div>
    <div id="menu-modify-option">
        <h3>Modify current <span id="modify-submenu-current-name"></span></h3>
        <p>Be careful when you delete a submenu, all the childs of the submenu will be also deleted, the activities will remain, though. Remove them later from the activity menu.</p>
        <div id="menu-modify-option-container">
            <p>Change name: <input autocomplete="off" type="text" id="menu-modify-option-name" /> - <a id="menu-modify-option-change">change</a> - <a id="menu-modify-option-delete">delete</a></p>
            <p>Morning? <input autocomplete="off" class="checkbox" name="modify-submenu-time" type="checkbox" value="0" /> Afternoon? <input autocomplete="off" class="checkbox" name="modify-submenu-time" type="checkbox" value="1" /> Evening? <input autocomplete="off" class="checkbox" name="modify-submenu-time" type="checkbox" value="2" /> Night? <input autocomplete="off" class="checkbox" name="modify-submenu-time" type="checkbox" value="3" /></p>
        <p>Once per day?<input autocomplete="off" class="checkbox" name="modify-submenu-once-per-day" type="checkbox" value="1" /></p>
        </div>
        <div id="menu-modify-option-suboption-container" style="display:none;">
        </div>
        <div id="menu-modify-option-activity-container" style="display:none;">
            <p>Assign an activity:</p>
            <select name="modify-submenu-activity">
            </select> - <a id="modify-submenu-assign-activity-anchor">assign</a>
            <p id="menu-modify-option-activity-alter-activity" display="hidden">Click <a id="menu-modify-option-activity-alter-activity-anchor">here</a> to modify the activity assigned to this option</p>
            <p id="menu-modify-option-activity-new-activity" display="hidden">Click <a id="menu-modify-option-activity-new-activity-anchor">here</a> to create an activity for this option</p>
        </div>
    </div>
  </div>
  <div id="manage-variables">
    <h2>Manage variables</h2>
    <h3>Add a new variable</h3>
    <p>Adding % to the name is not necessary. Variable names are always in uppercase.</p>
    <p>Name: <input autocomplete="off" type="text" maxlength="<?= constants_variables::max_lenght_name ?>" name="new-variable-name" /> - <a id="variable-add-new">add new</a></p>
    <p>Value: <input autocomplete="off" type="text" maxlength="<?= constants_variables::max_lenght_value ?>" name="new-variable-value" /></p>
      
    <h3>Modify a variable</h3>
    <p>All references to variables you delete in the activities will be broken, be careful when you erase them.</p>
    <p>To use them in the activities, type their name (for example, $SHOUJO_NAME or %DEFINED_VARIABLE) and it will be replaced for the value while playing</p>
    <div id="variables-user-defined-container-modify">
        
    </div>
    <h3>List of variables</h3>
    <h4>Game defined</h4>
    <ul>
      <li>$<?= constants_variables::shoujo_name ?></li>
      <li>$<?= constants_variables::current_username ?></li>
      <li>$<?= constants_variables::current_time ?></li>
      <li>$<?= constants_variables::current_day ?></li>
      <li>$<?= constants_variables::current_day_number ?></li>
      <li>$<?= constants_variables::current_month ?></li>
      <li>$<?= constants_variables::current_month_number ?></li>
      <li>$<?= constants_variables::submenu_name ?></li>
    </ul>
    <h4>User defined</h4>
    <div id="variables-user-defined-container-list">
    </div>
  </div>
  <div id="manage-status">
    <h2>Manage status</h2>
        <h3>Add a new status</h3>
        <p>Hidden status can't be seen by the player. Status can have values from 0 to <?= constants_status::max_points ?>, and can be decreased every hour, every day or never. The only way to increase the value of the status is using activities.</p>
        <p>Name: <input autocomplete="off" type="text" maxlength="<?= constants_status::name_length ?>" name="new-variable-name" id="new-variable-name" /><a class="submit-button" id="status-add-new">Add new</a></p>
        <p>Hidden? <input autocomplete="off" class="checkbox" type="checkbox" name="new-status-hidden" id="new-status-hidden" /></p>
        <p>
            Never decrease:
            <input autocomplete="off" checked="checked" class="radio" value="<?= constants_status::decrease_none ?>" type="radio" name="new-status-decrease-time"  />
            decrease once per hour:
            <input autocomplete="off" class="radio"value="<?= constants_status::decrease_hourly ?>" type="radio" name="new-status-decrease-time" />
            decrease once per day:
            <input autocomplete="off" class="radio" value="<?= constants_status::decrease_day ?>" type="radio" name="new-status-decrease-time" />
        </p>
        <h3>Modify a status</h3>
        <p>All references to the status you delete in the activities will be broken and won't appear in the game, be careful when you erase them. </p>
        <div id="status-container-modify">

        </div>
        <h3>List of status</h3>
        <div id="status-container-list">

        </div>
  </div>
  <div id="manage-events">
    <h2>Manage events</h2>
  </div>
    <div id="manage-activities">
        <h2>Manage activities</h2>
        <p>This is only for editing activities, you can add them from the different menus.</p>
        <select id="activities-list-selection"></select>
        <div id="current-activity-modify">
            <p>The friendly name is used for better management, it doesn't affect the activity itself. Also, users who aren't administrators can't see it either.</p>
            <p>Friendly name: <input autocomplete="off" type="text" id="modify-activity-friendly-name" maxlength="<?= constants_activity::activity_alias_max_length ?>" /> - <a id="modify-activity-friendly-name-anchor">change friendly name</a></p>
            <p><span id="modify-activity-name-characters-left"><?= constants_activity::activity_alias_max_length ?></span> characters left</p>
            <div id="current-activity-container-parent">
                <div id="current-activity-container">
                    
                </div>
                <p><a href="#bottom-location" id="manage-activity-append-new-child">append new child</a> - <a id="manage-activity-save-anchor">save</a> - <span id="manage-activity-saved-status"></span></p>
            </div>
        </div>
    </div>
</div>
<div id="cloning-container" style="display:none;">
    <div id="clone-status-modify">
        <p>Name: <input autocomplete="off" type="text" /> - <a name="status-change-value">change</a> - <a name="status-delete">delete</a></p>
        <p>Hidden? <input autocomplete="off" class="checkbox" type="checkbox" name="modify-status-hidden" /></p>
        <p>
            Never decrease:
            <input autocomplete="off" checked="checked" class="radio" value="<?= constants_status::decrease_none ?>" type="radio" name="modify-status-decrease-time"  />
            decrease once per hour:
            <input autocomplete="off" class="radio"value="<?= constants_status::decrease_hourly ?>" type="radio" name="modify-status-decrease-time" />
            decrease once per day:
            <input autocomplete="off" class="radio" value="<?= constants_status::decrease_day ?>" type="radio" name="modify-status-decrease-time" />
        </p>
    </div>
  <div id="clone-new-activity-general">
    <div class="new-activity-style-box">
        <div class="clone-new-activity-general">
            <p><a class="new-activity-anchor-delete-this">delete this child</a></p>
            <p>Change face:
                <select class="new-activity-face-list">
                    <optgroup label="Predefined">
                        <option value="no-change">no-change</option>
                        <option value="none">none</option>
                    </optgroup>
                    <optgroup class="new-activity-face-list-optgroup-user-defined" label="User defined">
                        
                    </optgroup>
                </select>
            </p>
            <p>Change background:
                <select class="new-activity-background-list">
                    <optgroup label="Predefined">
                        <option value="no-change">no change</option>
                        <option value="none">none</option>
                    </optgroup>
                    <optgroup class="new-activity-background-list-optgroup-user-defined" label="User defined">
                        
                    </optgroup>
                </select>
            </p>
            <p>Select activity type:</p>
            <select class="new-activity-select-type">
                <option selected="selected" value="undefined">None</option>
                <option value="<?= constants_activity::type_text ?>">Text</option>
                <option value="<?= constants_activity::type_countdown ?>">Countdown</option>
                <option value="<?= constants_activity::type_status ?>">Status</option>
                <option value="<?= constants_activity::type_option ?>">Option</option>
            </select>
        </div>
    </div>
  </div>
  <div id="clone-new-activity-text">
      <div class="clone-new-activity-type-container">
    <input autocomplete="off" type="hidden" class="new-activity-textarea-changer-helper" />
  	<textarea class="new-activity-textarea"></textarea>
    <p></p>
      </div>
  </div>
  <div id="clone-new-activity-countdown">
    <div class="clone-new-activity-type-container">
        <p>Time in seconds. Max time: <?= constants_activity::countdown_max_seconds ?> seconds, (<?= constants_activity::countdown_max_seconds/60 ?> minutes, <?= constants_activity::countdown_max_seconds/60/60 ?> hours)</p>
        <p>Time:<input autocomplete="off" class="new-activity-countdown-time" type="text" value="0" /></p>
    </div>
  </div>
  <div id="clone-new-activity-option">
    <div class="clone-new-activity-type-container">
        <div class="new-activity-option-container-ind">
            <p class="activity-option-text">
            <input autocomplete="off" class="checkbox" type="checkbox" />1st:
            <input autocomplete="off" type="text" maxlength="<?= constants_activity::option_max_characters ?>" />
            </p>
        </div>
        <div class="new-activity-option-container-ind">
            <p class="activity-option-text">
            <input autocomplete="off" class="checkbox" type="checkbox" />2nd:
            <input autocomplete="off" type="text" maxlength="<?= constants_activity::option_max_characters ?>" />
            </p>
        </div>
        <div class="new-activity-option-container-ind">
            <p class="activity-option-text">
            <input autocomplete="off" class="checkbox" type="checkbox" />3rd:
            <input autocomplete="off" type="text" maxlength="<?= constants_activity::option_max_characters ?>" />
            </p>
        </div>
        <div class="new-activity-option-container-ind">
            <p class="activity-option-text">
            <input autocomplete="off" class="checkbox" type="checkbox" />4th:
            <input autocomplete="off" type="text" maxlength="<?= constants_activity::option_max_characters ?>" />
            </p>
        </div>
        <div class="new-activity-option-container-ind">
            <p class="activity-option-text">
            <input autocomplete="off" class="checkbox" type="checkbox" />5th:
            <input autocomplete="off" type="text" maxlength="<?= constants_activity::option_max_characters ?>" />
            </p>
        </div>
        <div class="new-activity-option-container-ind">
            <p class="activity-option-text">
            <input autocomplete="off" class="checkbox" type="checkbox" />6th:
            <input autocomplete="off" type="text" maxlength="<?= constants_activity::option_max_characters ?>" />
            </p>
        </div>
    </div>
  </div>
  <div id="clone-new-activity-status">
      <div class="clone-new-activity-type-container">
  	<p>Values accepted: from -<?= constants_status::max_points; ?> to <?= constants_status::max_points; ?>. If you put value 5, the value gets increased 5 points. If you put value -5, the value gets decreased 5 points. The maximum value is still <?= constants_status::max_points; ?>,  has <?= constants_status::max_points - 5; ?> points in X status, and you add 10, X will be <?= constants_status::max_points; ?>, not <?= constants_status::max_points + 5; ?>.</p>
    <p>Value:<input autocomplete="off" type="text" class="new-activity-value" value="0" /></p>
    <p>Status:
    <select class="new-activity-status">
    	
    </select></p>
    </div>
  </div>
    <div id="clone-new-activity-disabled-overlay">
        <div class="disabled-activity">
            <p>disabled</p>
        </div>
    </div>
    <div id="clone-variable-modify">
        <p><input autocomplete="off" type="text" maxvalue="<?= constants_variables::max_lenght_value ?>" /> - <a name="variable-change-value">change value</a> - <a name="variable-delete">delete</a></p>
    </div>
</div>
<style>
#activity-holder
{
	padding:20px;
}
.test
{
	margin-bottom:5px;
	border:1px black solid;
}
.new-activity-textarea
{
	width:500px;
	margin:15px;
	height:100px;
}
.activity-option-text
{
	margin:0px;
	padding:0px;
}
</style>
<div id="bottom-location"></div>
<body>