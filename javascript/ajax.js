var URL_BASIC_INFO = "/ajax_server/shoujo_info.php"
var URL_STATUS = "/ajax_server/request_status.php"
var URL_PRELOAD = "/ajax_server/shoujo_preload.php"
var URL_STATE = "/ajax_server/state.php"

function requestPreload(shoujo_id, objectReference)
{
	var to_return = false
	$.ajax({
		type: "GET",
		url: URL_PRELOAD+"?shoujo="+shoujo_id,
		dataType: "xml",
		success: function(xml)
		{
			to_return = true
			readPreloadXml(xml, objectReference)
		},
		error: function()
		{
			to_return = false
		},
		async: false
	})
	return to_return
}
function requestBasicInfoAjax(shoujo_id, objectReference, manage_mode)
{
	var to_return = false
	var url = URL_BASIC_INFO+"?shoujo="+shoujo_id
	if(manage_mode)
	{
		url += "&manage-mode"
	}
	$.ajax({
		type: "GET",
		url: url,
		dataType: "xml",
		success: function(xml)
		{
			to_return = true
			readBasicInfoXml(xml, objectReference)
		},
		error: function()
		{
			to_return = false
		},
		async: false
	})
	return to_return
}
function requestStatusAjax(shoujo_id, objectReference)
{
	var to_return = false
	$.ajax({
		type: "GET",
		url: URL_STATUS+"?shoujo="+shoujo_id,
		dataType: "xml",
		success: function(xml)
		{
			readStatus(xml, objectReference)
		},
		error: function()
		{
			to_return = false
		},
		async: false
	})
	return to_return
}
function startActivity(shoujo_id, activity_id)
{
	var to_return = false
	$.ajax({
		type: "GET",
		url: URL_STATUS+"?shoujo="+shoujo_id,
		dataType: "xml",
		success: function(xml)
		{
			readStatus(xml, objectReference)
		},
		error: function()
		{
			to_return = false
		},
		async: false
	})
	return to_return
}
function sendState(shoujo_id, get_string)
{
	var to_return = -1
	$.ajax({
		type: "GET",
		url: URL_STATE+"?shoujo="+shoujo_id+"&"+get_string,
		dataType: "text",
		success: function(response)
		{
			console.log(response)
			to_return = response
		},
		error: function()
		{
			to_return = -1
		},
		async: false
	})
	return to_return
}
function translate_state_error_message(message)
{
	switch(message)
	{
		case AJAX_STATE_OK: // shouldn't happen
			return 'ok'
			break
		case AJAX_STATE_NOT_LOGGED_IN:
			return 'You are not logged in'
			break
		case AJAX_STATE_NOT_PLAYING_SESSION:
			return 'You are not playing with this character'
			break
		case AJAX_STATE_SHOUJO_DOESNT_EXIST:
			return 'This character doesn\'t exist'
			break
		case AJAX_STATE_ALREADY_PLAYING_WITH_AN_ACTIVITY:
			return 'You are already playing with an activity'
			break
		case AJAX_STATE_ACTIVITY_ALREADY_DONE:
			return 'The activity you are trying to use has already been done with your account'
			break
		case AJAX_STATE_INVALID_ACTIVITY:
			return 'The activity you selected is invalid'
			break
		case AJAX_STATE_NOT_PLAYING_WITH_ACTIVITY:
			return 'You are not playing with this activity'
			break
	}
	return 'Unkown error'
}