// constants
var OPTION_SUBOPTIONS = "0"
var OPTION_ACTIVITY = "1"
var OPTION_EVENT = "2"
var OPTION_STATUS = "3"
var ACTIVITY_TYPE_TEXT = "0"
var ACTIVITY_TYPE_COUNTDOWN = "1"
var ACTIVITY_TYPE_STATUS = "2"
var ACTIVITY_TYPE_OPTION = "3"
var ACTIVITY_TYPE_YES_NO = "4" // implement later
var ACTIVITY_TYPE_HIDE_MENU = "5" // implement later
var VARIABLE_PLAYER = "PLAYER";
var VARIABLE_TIME = "TIME";
var VARIABLE_DAY_NUMBER = "DAY_NUMBER";
var VARIABLE_DAY = "DAY";
var VARIABLE_MONTH = "MONTH";
var VARIABLE_MONTH_NUMBER = "MONTH_NUMBER";
var VARIABLE_YEAR = "YEAR";
var VARIABLE_SHOUJO_NAME = "SHOUJO_NAME";
var VARIABLE_SUBMENU_NAME = "SUBMENU_NAME";
var STATUS_MAX_POINTS = 250

function readPreloadXml(xml, objectReference)
{
	objectReference.exists = $(xml).find("exists").text() == '0' ? false : true
	objectReference.is_playing = $(xml).find("is_playing").text() == '0' ? false : true
	objectReference.is_available = $(xml).find("is_available").text() == '0' ? false : true
}
function readBasicInfoXml(xml, arrayReference)
{
	var manage_mode = $(xml).find('shoujo').attr('manage-mode') ? true : false
	arrayReference.information = {}
	arrayReference.information.name = $(xml).find("information").find("name").text()
	arrayReference.information.description = $(xml).find("information").find("description").text()
	arrayReference.information.id = ($(xml).find("information").find("id").text())
	arrayReference.status = new Array()
	arrayReference.status_manage = new Array()
	$(xml).find("status_collection").children("name").each(
		function()
		{
			var text = $(this).text()
			arrayReference.status.push(text)
			if (manage_mode)
			{
				arrayReference.status_manage[text] = new Object()
				arrayReference.status_manage[text].is_deleted = false
				arrayReference.status_manage[text].is_hidden = $(this).attr('hidden') == '0' ? '0' : '1'
				arrayReference.status_manage[text].decrease_time = $(this).attr('decrease-time')
			}
		}
	)
	arrayReference.variables_defined = new Object()
	$(xml).find("variables").children("variable").each(
		function()
		{
			arrayReference.variables_defined[$(this).find("name").text()] = $(this).find("value").text()
		}
	)
	if (manage_mode)
	{
		arrayReference.variables_deleted = new Object()
		for(var name in arrayReference.variables_defined)
		{
			arrayReference.variables_deleted[name] = new Object()
			arrayReference.variables_deleted[name].is_deleted = false
		}
	}
	arrayReference.activity_array = new Array()
	if (manage_mode)
	{
		arrayReference.activity_manage = new Array()
	}
	$(xml).find("activities").children("activity_array").each(
		function()
		{
			arrayReference.activity_array[$(this).attr("id")] = readActivity(this)
			if (manage_mode)
			{
				arrayReference.activity_manage[$(this).attr("id")] = new Object()
				arrayReference.activity_manage[$(this).attr("id")].friendly_name = ''
				if ($(this).attr('friendly_name') != undefined)
				{
					arrayReference.activity_manage[$(this).attr("id")].friendly_name = $(this).attr("friendly_name")
				}
			}
		}
	)
	arrayReference.images = new Object()
	arrayReference.images.faces = new Array()
	arrayReference.images.faces_manage = new Object()
	$(xml).find("faces").children("face").each(
		function()
		{
			arrayReference.images.faces.push($(this).text())
			if (manage_mode)
			{
				arrayReference.images.faces_manage[$(this).text()] = new Object()
				arrayReference.images.faces_manage[$(this).text()].new_name = $(this).text()
				arrayReference.images.faces_manage[$(this).text()].is_deleted = false
			}
		}
	)
	arrayReference.images.backgrounds = new Array()
	$(xml).find("backgrounds").children("background").each(
		function()
		{
			arrayReference.images.backgrounds.push($(this).text())
		}
	)
	arrayReference.events = new Array()
	var counter = 0
	$(xml).find("events").children("event").each(
		function()
		{
			arrayReference.events[counter++] = readEvent(this)
		}
	)
	sortEvents(arrayReference.events)
	arrayReference.options = new Array()
	readOptions($(xml).find("options").first().children("option"), arrayReference.options, manage_mode)
	arrayReference.variables_game = new Object()
	arrayReference.variables_game[VARIABLE_TIME] = variable_getTime
	arrayReference.variables_game[VARIABLE_DAY_NUMBER] = variable_getDayNumber
	arrayReference.variables_game[VARIABLE_DAY] = variable_getDay
	arrayReference.variables_game[VARIABLE_MONTH] = variable_getMonth
	arrayReference.variables_game[VARIABLE_MONTH_NUMBER] = variable_getMonthNumber
	arrayReference.variables_game[VARIABLE_YEAR] = variable_getYear
	arrayReference.variables_game[VARIABLE_SHOUJO_NAME] = arrayReference.information.name
	arrayReference.variables_game[VARIABLE_SUBMENU_NAME] = variable_getSubmenuName
	if (manage_mode)
	{
		arrayReference.request_new_option_number = function()
		{
			var greatest_number = 0
			var recursive_lookup = function(options)
			{
				var length = options.length
				for (var loop = 0; loop < length; loop++)
				{
					if (options[loop].number > greatest_number)
					{
						greatest_number = options[loop].number
					}
					if(options[loop].type == OPTION_SUBOPTIONS)
					{
						recursive_lookup(options[loop].options)
					}
				}
			}
			recursive_lookup(this.options)
			return parseInt(greatest_number + 1)
		}
		arrayReference.request_new_activity_id = function()
		{
			var to_return = 5
			var length = this.activity_array.length
			if (length < to_return) return to_return
			for(; to_return < length; to_return++)
			{
				if (typeof this.activity_array[to_return] == 'undefined')
				{
					return to_return
				}
			}
			return to_return + 1
		}
		arrayReference.create_activity = function(activity_id)
		{
			this.activity_array[activity_id] = new Array()
			this.activity_manage[activity_id] = new Object()
			this.activity_manage[activity_id].friendly_name = ''
		}
		if (typeof arrayReference.activity_array[0] == 'undefined')
		{
			arrayReference.create_activity(0)
			arrayReference.activity_manage[0].friendly_name = 'first_time_playing'
		}
		for (var loop = 0; loop < 10; loop++)
		{
			if (typeof arrayReference.activity_array[loop] == 'undefined')
			{
				arrayReference.create_activity(loop)
				arrayReference.activity_manage[loop].friendly_name = 'reserved'
			}
		}
	}
}
function readActivity(xmlParent)
{
	var array_return = new Array()
	$(xmlParent).children("activity").each(
		function()
		{
			var number = $(this).attr("number")
			array_return[number] = {}
			if($(this).children('face').length == 1)
			{
				array_return[number].face = $(this).children('face').text()
			}
			if($(this).children('background').length == 1)
			{
				array_return[number].background = $(this).children('background').text()
			}
			array_return[number].type = $(this).children("type").text()
			switch(array_return[number].type)
			{
				case ACTIVITY_TYPE_TEXT:
					array_return[number].text = $(this).children("text").text()
					break
				case ACTIVITY_TYPE_COUNTDOWN:
					array_return[number].time = $(this).children("time").text()
					break
				case ACTIVITY_TYPE_STATUS:
					array_return[number].status = $(this).children("status").text()
					array_return[number].value = $(this).children("value").text()
					break
				case ACTIVITY_TYPE_OPTION:
					array_return[number].options = new Array()
					$(this).children("option").each(
						function()
						{
							var num_aux_option = $(this).attr("number")
							array_return[number].options[num_aux_option] = new Object()
							array_return[number].options[num_aux_option].text = $(this).children("text").text()
							array_return[number].options[num_aux_option].activity_id = $(this).children("activity_id").text()
						}
					)
					break
			}
		}
	)
	return array_return
}
function readOptions(xmlParent, arrayReference, manage_mode)
{
	var pos = 0
	$(xmlParent).each(
		function()
		{
			arrayReference[pos] = new Object()
			arrayReference[pos].once_per_day = $(this).attr('once-per-day') == 1 ? true : false
			arrayReference[pos].text = $(this).find("text:first").text()
			arrayReference[pos].type = $(this).find("type:first").text()
			if(manage_mode)
			{
				arrayReference[pos].deleted = false
			}
			arrayReference[pos].number = parseInt($(this).find("number:first").text())
			arrayReference[pos].time = parseInt($(this).find("time:first").text())
			if (arrayReference[pos].type == OPTION_SUBOPTIONS)
			{
				arrayReference[pos].options = new Array()
				readOptions($(this).find("options").first().children("option"), arrayReference[pos].options, manage_mode)
			}
			else if (arrayReference[pos].type == OPTION_ACTIVITY)
			{
				arrayReference[pos].activity_id = $(this).find("activity_id:first").text()
			}
			pos++
		}
	)
}
function readEvent(xml)
{
	var event = new Object()
	var date_array = $(xml).find("date_start").first().text().split(/[-| |:]/)
	event.date_start = new Object()
	event.date_start.year = date_array[0]
	event.date_start.month = date_array[1]
	event.date_start.day = date_array[2]
	date_array = $(xml).find("date_finish").first().text().split(/[-| |:]/)
	event.date_finish = new Object()
	event.date_finish.year = date_array[0]
	event.date_finish.month = date_array[1]
	event.date_finish.day = date_array[2]
	event.time = parseInt($(xml).find("time").first().text())
	event.name = $(xml).find("name").text()
	event.description = $(xml).find("description").text()
	event.activity_id = parseInt($(xml).find("activity_id").text())
	return event
}
// status
function readStatus(xml, arrayReference)
{
	arrayReference.status = new Object()
	$(xml).find("status_collection").children("status").each(
		function()
		{
			arrayReference.status[$(this).find("name").text()] = parseInt($(this).find("value").text())
		}
	)
}
// variables
function variable_getTime()
{
	date = new Date
	return date.getHours() + ":" + date.getMinutes()
}
function variable_getDay()
{
	date = new Date
	weekday=new Array(7);
	weekday[0]="sunday";
	weekday[1]="monday";
	weekday[2]="tuesday";
	weekday[3]="wednesday";
	weekday[4]="thursday";
	weekday[5]="friday";
	weekday[6]="saturday";
	return weekday[date.getDay()];
}
function variable_getMonth()
{
	var d=new Date();
	var month=new Array();
	month[0]="january";
	month[1]="february";
	month[2]="march";
	month[3]="april";
	month[4]="may";
	month[5]="june";
	month[6]="july";
	month[7]="august";
	month[8]="september";
	month[9]="october";
	month[10]="november";
	month[11]="december";
	return month[d.getMonth()]; 
}
function variable_getDayNumber()
{
	var d=new Date()
	return d.getDate()
}
function variable_getMonthNumber()
{
	var d=new Date()
	return d.getMonth() + 1
}
function variable_getYear()
{
	var d=new Date()
	return d.getFullYear()
}
function variable_getSubmenuName()
{
	return submenu_last_click
}
// misc
function replaceTextWithVariable(text, gameVariables, userVariables)
{
	var length = gameVariables.length
	for (var key in gameVariables)
	{
		var toReplace
		if (is_function(gameVariables[key]))
		{
			toReplace = gameVariables[key]()
		}
		else
		{
			toReplace = gameVariables[key]
		}
		text = text.replace("$"+key, toReplace)
	}
	for (var key in userVariables)
	{
		var toReplace
		if (is_function(userVariables[key]))
		{
			toReplace = userVariables[key]()
		}
		else
		{
			toReplace = userVariables[key]
		}
		text = text.replace("%"+key, toReplace)
	}
	return text
}
function is_function(obj)
{
	return !!(obj && obj.constructor && obj.call && obj.apply);	
}
function toHHMMSS_countdown(seconds) {
	sec_numb    = parseInt(seconds);
	var hours   = Math.floor(sec_numb / 3600);
	var minutes = Math.floor((sec_numb - (hours * 3600)) / 60);
	var seconds = sec_numb - (hours * 3600) - (minutes * 60);

	if (hours > 0 && minutes < 10) {minutes = "0"+minutes;}
	if (minutes > 0 && seconds < 10) {seconds = "0"+seconds;}
	var time = seconds
	if (minutes > 0)
	{
		time = minutes+':'+ time
	}
	if (hours > 0)
	{
		time = hours+':'+ time
	}
	return time;
}
function toHHMM_event(minutes) {
	var hours   = Math.floor(minutes / 60);
	var minutes = Math.floor((minutes - (hours * 60)));

	if (hours < 10) {hours = "0"+hours;}
	if (minutes < 10) {minutes = "0"+minutes;}
	return hours+":"+minutes
}
function sortEvents(eventArray)
{
	var length = eventArray.length
	var swapped = true
	while (swapped)
	{
		swapped = false
		for (var i = 1; i < length; i++)
		{
			// clean this
			if (eventArray[i - 1].date_start.year > eventArray[i].date_start.year || (eventArray[i - 1].date_start.year == eventArray[i].date_start.year && eventArray[i - 1].date_start.month > eventArray[i].date_start.month) || (eventArray[i - 1].date_start.year == eventArray[i].date_start.year && eventArray[i - 1].date_start.month == eventArray[i].date_start.month && eventArray[i - 1].date_start.day > eventArray[i].date_start.day))
			{
				var event_helper = eventArray[i - 1]
				eventArray[i - 1] = eventArray[i]
				eventArray[i] = event_helper
				swapped = true
			}
		}
	}
}
function get_time_value(morning, afternoon, evening, night)
{
	var to_return = 0
	if (morning)
	{
		to_return |= 1
	}
	if (afternoon)
	{
		to_return |= 2
	}
	if (evening)
	{
		to_return |= 4
	}
	if (night)
	{
		to_return |= 8
	}
	return to_return
}
function get_time_string(time)
{
	var to_return = ''
	if (time & 1)
	{
		to_return += 'morning '
	}
	if (time & 2)
	{
		to_return += 'afternoon '
	}
	if (time & 4)
	{
		to_return += 'evening '
	}
	if (time & 8)
	{
		to_return += 'night '
	}
	if (to_return == '')
	{
		return 'never'
	}
	else
	{
		return to_return.substring(0, to_return.length - 1)
	}
}
function is_in_time(time, current_time)
{
	var date = new Date
	var binary
	if (typeof current_time == 'undefined')
	{
		current_time = date.getHours()
	}
	if (current_time >= 6 && current_time < 12)
	{
		binary = 1
	}
	else if (current_time >= 12 && current_time < 18)
	{
		binary = 2
	}
	else if (current_time >= 18 || current_time < 0)
	{
		binary = 4
	}
	else
	{
		binary = 8
	}
	return !!(time & binary)
}
function image_url(shoujo_id, name, is_face)
{
	var date = new Date()
	return "/ajax_server/request_image.php?shoujo="+shoujo_id+"&is_face="+(is_face ? '1' : '0')+"&name="+name+"&"+date.getTime()
}