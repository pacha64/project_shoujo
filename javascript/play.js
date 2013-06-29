// global pointers
var button_1_function
var button_2_function
var button_3_function
var basic_information
var shoujo_state
var option_array
var activity_array
var current_activity = {}
var current_activity_position = 0
var current_submenu = {}
var submenu_stack = new Array()
var submenu_stack_pos = new Array()
var submenu_stack_position = 0
var current_pos_submenu = 0
var event_array_current
var click_forward_text = false
var current_face
var current_background
var window_is_focus = false
var submenu_last_click = ''
// initizalitaion
function preinit()
{
	$(window).focus(function(){window_is_focus=true}).blur(function(){window_is_focus=false})
	$(document).on('click', 'a', function() {
		$("#click-sound-effect").get(0).play()
	})
}
function get_shoujo_id()
{
	return $(document).getUrlParam('shoujo')
}
function show_error_exit(error_message)
{
	$("#play-container").hide()
	$('#loading-container').hide()
	$("#error-container").show()
	$("#error-description").html(error_message)
}
function load()
{
	document.title = "Loading"
	$("#error-container").hide()
	$("#play-container").hide()
	$('#loading-container').hide()
	var object_preload = new Object()
	requestPreload(get_shoujo_id(), object_preload)
	if (!object_preload.exists)
	{
		show_error_exit("This character doesn't exist")
		return
	}
	else if(!object_preload.is_available)
	{
		show_error_exit("This character isn't available")
		return
	}
	else if(!object_preload.is_playing)
	{
		show_error_exit('You are not playing with this character. Click <a href="/new.php?shoujo='+get_shoujo_id()+'">here</a> to start playing with him/her')
		return
	}
	$('#loading-container').show()
	var counter = -1;
	var is_loading_images = false
	var images_loaded_all = false
	var image_count = 0
	var loadingInterval = setInterval(function(){
		counter++;
		var text = 'Loading' + Array(counter + 1).join('.') + Array(4 - counter).join('\xA0')
		$('#loading-container h1').text(text)
		if (counter == 3) counter = -1
		if (is_loading_images && !images_loaded_all)
		{
			var images_done = 0
			$('#image-container img').each(function(){
				if($(this)[0].complete) images_done++
			})
			$('#loading-images').text('Loaded '+images_done+' of '+image_count+' images')
			if (images_done == image_count)
			{
				$('#loading-images').css('color', '#5DFC0A')
				$('#loading-images').append(' - done')
				images_loaded_all = true
				clearInterval(loadingInterval)
				$('#loading-container h1').text('Done')
				$('#loading-play-button').html('<a>Play!</a>')
				$('#loading-play-button a').click(function(){
					init()
				})
			}
		}
	}, 500)
	basic_information = new Object()
	if(!requestBasicInfoAjax(get_shoujo_id(), basic_information, false))
	{
		console.log(false)
	}
	console.log(basic_information)
	$('#loading-shoujo-information').css('color', '#5DFC0A')
	$('#loading-shoujo-information').append(' - done')
	$('#loading-shoujo-state').css('color', '#5DFC0A')
	$('#loading-shoujo-state').append(' - done')
	var face_length = basic_information.images.faces.length
	var background_length = basic_information.images.backgrounds.length
	for(var loop = 0; loop < face_length; loop++)
	{
		$('#face-container').append($('<img />').hide().data('name', basic_information.images.faces[loop]).addClass("face").attr('src', image_url(basic_information.information.id, basic_information.images.faces[loop], 1)))
	}
	for(var loop = 0; loop < background_length; loop++)
	{
		$('#background-container').append($('<img />').hide().data('name', basic_information.images.backgrounds[loop]).addClass("background").attr('src', image_url(basic_information.information.id, basic_information.images.backgrounds[loop], 0)))
	}
	image_count = face_length + background_length
	is_loading_images = true
}
function init()
{
	$("#loading-container").hide()
	$("#play-container").show()
	shoujo_state = {}
	onResize()
	basic_information.variables_game[VARIABLE_PLAYER] = PLAYER_NAME
	option_array = basic_information.options
	activity_array = basic_information.activity_array
	handleEventChangeTime(2)
	$("#play-event-container").hide()
	$("#play-status-container").hide()
	$("#play-menu").show()
	$(".event-status-back").click(showMainMenu)
	$("#play-menu").click(function()
	{
		click_forward_text = true
	})
	$(".option-button-1 a").click(function()
	{
		handleMenuClick(0)
	})
	$(".option-button-2 a").click(function()
	{
		handleMenuClick(1)
	})
	$(".option-button-3 a").click(function()
	{
		handleMenuClick(2)
	})
	$(".option-button-4 a").click(function()
	{
		handleMenuClick(3)
	})
	$(".option-button-5 a").click(function()
	{
		handleMenuClick(4)
	})
	$(".option-button-6 a").click(function()
	{
		handleMenuClick(5)
	})
	$("#button-1").click(function()
	{
		if(typeof(button_1_function)=="function")
		{
			button_1_function()
		}
	})
	$("#button-2").click(function()
	{
		if(typeof(button_2_function)=="function")
		{
			button_2_function()
		}
	})
	$("#button-3").click(function()
	{
		if(typeof(button_3_function)=="function")
		{
			button_3_function()
		}
	})
	$("#option-1").click(function()
	{
		handleOptionClick(0)
	})
	$("#option-2").click(function()
	{
		handleOptionClick(1)
	})
	$("#option-3").click(function()
	{
		handleOptionClick(2)
	})
	$("#option-4").click(function()
	{
		handleOptionClick(3)
	})
	$("#option-5").click(function()
	{
		handleOptionClick(4)
	})
	$("#option-6").click(function()
	{
		handleOptionClick(5)
	})
	$("#event-change-today").click(function()
	{
		handleEventChangeTime(0)
	})
	$("#event-change-week").click(function()
	{
		handleEventChangeTime(1)
	})
	$("#event-change-month").click(function()
	{
		handleEventChangeTime(2)
	})
	$(window).resize(onResize)
	showMainMenu()
	$('#face-container img').each(function(){
		if($(this).data('name') == 'default')
		{
			current_face = $(this)
			return false
		}
	})
	$('#background-container img').each(function(){
		if($(this).data('name') == 'default')
		{
			current_background = $(this)
			return false
		}
	})
	change_background('default')
	change_face('default')
	document.title = "Playing with "+basic_information.information.name
}
function onResize()
{
	var new_width = $(window).width()
	var new_height = $(window).height()
	
	// event/status
	$(".status-event-container").css("width", (new_width - 40)+"px")
	$(".status-event-container").css("height", (new_height - 40)+"px")
	$(".play-event-status").css("width", $("#play-event-container").width()+"px")
	$(".play-event-status").css("height",$("#play-event-container").height()+"px")
	var height_event_container = $("#play-event").height() - $("#play-event").children("h2").first().outerHeight(true) - $("#play-event-buttons").outerHeight(true) - 10
	$("#play-event-list-container").css("height", height_event_container + "px")
	var height_status_container = $("#play-status").height() - $("#play-status").children("h2").first().outerHeight(true) - 10
	$("#play-status-list-container").css("height", height_status_container + "px")
	
	// images
	$(".background").css("width", new_width + "px")
	$(".background").css("height", new_height + "px")
	$(".face").each(function()
	{
		var image_width = $(this).width()
		var image_height = $(this).height()
		var ratio =  image_width / image_height
		$(this).height(new_height)
		$(this).width(new_height * ratio)
		$(this).css("margin-left", "-"+parseInt(new_height * ratio/2)+"px")
	})
}
// toggle displays
function showMainMenu()
{
	hideAll()
	clearPlayButtons()
	current_submenu = option_array
	submenu_stack_position = 0
	current_pos_submenu = 0
	subOptionsRefresh()
}
function hideAll()
{
	$("#play-menu").hide()
	$("#play-status-container").hide()
	$("#play-event-container").hide()
	$("#play-menu-4-options").hide()
	$("#play-menu-6-options").hide()
	$("#play-change-status").hide()
	$("#play-text").hide()
	$("#play-options").hide()
	$("#play-countdown").hide()
	$('#settings-container').show()
}
function show4Options()
{
	hideAll()
	$("#play-menu").show()
	$("#play-menu-4-options").show()
	for(x = 1; x <= 4; x++)
	{
		$(".option-button-"+x+' a').text("")
	}
}
function show6Options()
{
	hideAll()
	$("#play-menu").show()
	$("#play-menu-6-options").show()
	for(x = 1; x <= 6; x++)
	{
		$(".option-button-"+x+' a').text("")
	}
}
function showEvents()
{
	hideAll()
	$('#settings-container').hide()
	$("#play-event-container").show()
}
function showStatus()
{
	hideAll()
	$('#settings-container').hide()
	$("#play-status-list-container").text("")
	$("#play-status-container").show()
}
function showText()
{
	hideAll()
	$("#play-menu").show()
	$("#play-text").show()
}
function showChangeStatus()
{
	hideAll()
	$("#play-menu").show()
	$("#play-change-status").show()
	$("#status-changed").text("")
}
function clearPlayButtons()
{
	$("#button-1").text('')
	$("#button-2").text('')
	$("#button-3").text('')
	button_1_function = undefined
	button_2_function = undefined
	button_3_function = undefined
}
function showCountdown()
{
	hideAll()
	$("#play-menu").show()
	$("#play-countdown").show()
	$("#countdown-seconds-left").text("")
}
function showOptions()
{
	hideAll()
	$("#play-menu").show()
	$("#play-options").show()
	for (x = 1; x < 6; x++)
	{
		$("#option-"+x).show()
	}
}
// handle options
function handleMenuClick(id)
{
	var translated_position = calculate_option_position(current_pos_submenu, id +1)
	switch(current_submenu[translated_position].type)
	{
		case OPTION_SUBOPTIONS:
			submenu_stack_pos[submenu_stack_position] = current_pos_submenu
			submenu_stack[submenu_stack_position++] = current_submenu
			current_submenu = current_submenu[translated_position].options
			submenu_last_click = current_submenu.text
			current_pos_submenu = 0
			subOptionsRefresh()
			break
		case OPTION_EVENT:
			showEvents()
			break
		case OPTION_STATUS:
			showStatus()
			requestStatusAjax(basic_information.information.id, shoujo_state)
			refreshStatus()
			break
		case OPTION_ACTIVITY:
			current_activity = activity_array[current_submenu[calculate_option_position(current_pos_submenu, id +1)].activity_id]
			current_activity_position = 0
			var state = sendState(get_shoujo_id(), '')
			if (state != AJAX_STATE_OK)
			{
				show_error_exit(translate_state_error_message(state))
			}
			activityNext()
			break
	}
}
function handleOptionClick(number)
{
	current_activity = activity_array[current_activity[current_activity_position-1].options[number].activity_id]
	current_activity_position = 0;
	activityNext()
}
function handleEventChangeTime(time)
{
	event_array_current = new Array()
	var span_start
	var span_finish
	switch (time)
	{
		case 0:
			span_start = new Date(variable_getYear(), variable_getMonthNumber() - 1, variable_getDayNumber())
			span_finish = new Date(variable_getYear(), variable_getMonthNumber() - 1, variable_getDayNumber(), 24)
			break
		case 1:
			span_start = new Date()
			var day = variable_getDayNumber()
			var diff = day - span_start.getDay() + (day == 0 ? -6 : 1)
			span_start.setDate(diff)
			span_finish = new Date(span_start.getTime() + 7 * 24 * 60 * 60 * 1000)
			if (span_finish.day > new Date(variable_getYear(), variable_getMonthNumber()).getDate())
			{
				span_finish = new Date(variable_getYear(), variable_getMonthNumber())
			}
			break
		case 2:
			span_start = new Date(variable_getYear(), variable_getMonthNumber() - 1)
			span_finish = new Date(variable_getYear(), variable_getMonthNumber())
			break
	}
	for (var x = 0; x < basic_information.events.length; x++)
	{
		if (basic_information.events[x].date_start.day > span_finish.getDate())
		{
			continue
		}
		if (basic_information.events[x].date_finish.day < span_start.getDate())
		{
			continue
		}
		event_array_current.push(basic_information.events[x])
	}
	refreshEvents()
}
// sub-options
function calculate_option_position(current_pos, offset)
{
	var counter = 0
	while (current_pos in current_submenu)
	{
		if(is_in_time(current_submenu[current_pos].time))
		{
			counter++
			if (counter == offset) return current_pos
		}
		current_pos++
	}
	return -1
}
function get_menu_length(current_submenu)
{
	var to_return = 0
	for(var loop in current_submenu)
	{
		if (is_in_time(current_submenu[loop].time))
		{
			to_return++
		}
	}
	return to_return
}
function subOptionsRefresh()
{
	hideAll()
	clearPlayButtons()
	var length = get_menu_length(current_submenu) - current_pos_submenu
	/*if (length > 4)
	{
		show6Options()
		extra_counter = 1
		for (x = current_pos_submenu; x < 6 + current_pos_submenu; x++)
		{
			if (x in current_submenu)
			{
				$("#play-menu-6-options .option-button-"+(extra_counter++)+' a').text(current_submenu[x].text)
			}
		}
	}
	else*/
	{
		show4Options()
		var extra_counter = 1
		var x = current_pos_submenu
		while(x in current_submenu)
		{
			if(is_in_time(current_submenu[x].time))
			{
				$("#play-menu-4-options .option-button-"+(extra_counter++)+' a').text(current_submenu[x].text)
			}
			x++
			if (extra_counter == 5) break
		}
	}
	
	if (length > 4)
	{
		$("#button-1").text("next")
		button_1_function = subOptionNext
	}
	if (current_pos_submenu > 0)
	{
		$("#button-2").text("previous")
		button_2_function = subOptionPrevious
	}
	if (submenu_stack_position > 0)
	{
		$("#button-3").text("back")
		button_3_function = subOptionBackMenu
	}
}
function subOptionPrevious()
{
	//current_pos_submenu -= 6
	current_pos_submenu -= 4
	subOptionsRefresh()
}
function subOptionNext()
{
	//current_pos_submenu += 6
	current_pos_submenu += 4
	subOptionsRefresh()
}
function subOptionBackMenu()
{
	if (submenu_stack_position > 0)
	{
			current_submenu = submenu_stack[--submenu_stack_position]
			current_pos_submenu = submenu_stack_pos[submenu_stack_position]
			subOptionsRefresh()
	}
}
// events
function refreshEvents()
{
	$("#play-event-list-container").text("")
	var lenght_array = event_array_current.length
	for(var x = 0; x < lenght_array; x++)
	{
		var event_status = "Play event!"
		if (eventIsLate(event_array_current[x], new Date()))
		{
			event_status = "Event already passed"
		}
		else if (eventNotYet(event_array_current[x], new Date()))
		{
			event_status = "Too early"
		}
		else if (!is_in_time(event_array_current[x].time))
		{
			event_status = "Not in time yet"
		}
		var date_helper = event_array_current[x].date_start
		var start_string = date_helper.month + "/" + date_helper.day
		date_helper = event_array_current[x].date_finish
		var finish_string = date_helper.month + "/" + date_helper.day
		$("#play-event-list-container").append('<div class="play-event-individual"><h3>'+replaceTextWithVariable(event_array_current[x].name, basic_information.variables_game, basic_information.variables_defined)+' - '+event_status+'</h3><p>'+replaceTextWithVariable(event_array_current[x].description, basic_information.variables_game, basic_information.variables_defined)+'</p><p>'+start_string+' - '+finish_string+' ('+get_time_string(event_array_current[x].time)+')</p></div>')
	}
}
function eventIsLate(event, time)
{
	if (event.date_finish.day < time.getDate())
	{
		return true
	}
	else
	{
		return false
	}
}
function eventNotYet(event, time)
{
	if (event.date_start.day > time.getDate())
	{
		return true
	}
	else
	{
		return false
	}
}
// status
function refreshStatus()
{
	$("#play-status-list-container").text("")
	var loop = 0
	for(loop = 0; loop < basic_information.status.length; loop++)
	{
		var key = basic_information.status[loop]
		var value = 0
		if (key in shoujo_state.status)
		{
			value = shoujo_state.status[key]
		}
		$("#play-status-list-container").append('<p class="status-container-name">'+key+':</p><p class="status-container-value">'+
			value+'/'+STATUS_MAX_POINTS+'</p><br style="clear:both" />')
	}
}
// activity
function activityNext()
{
	if(current_activity != undefined && current_activity_position in current_activity)
	{
		var activity_pos = current_activity_position
		current_activity_position++;
		if('face' in current_activity[activity_pos])
		{
			change_face(current_activity[activity_pos].face)
		}
		if('background' in current_activity[activity_pos])
		{
			change_background(current_activity[activity_pos].background)
		}
		switch(current_activity[activity_pos].type)
		{
			case ACTIVITY_TYPE_TEXT:
				click_forward_text = false
				showText()
				$("#conversation-text").text("")
				scrollingText(activity_pos, "#conversation-text", replaceTextWithVariable(current_activity[activity_pos].text, basic_information.variables_game, basic_information.variables_defined), 0, 10);
				clearPlayButtons()
				$("#button-1").text("Next")
				button_1_function = activityNext
				if (activity_pos > 0 && current_activity[activity_pos-1].type == ACTIVITY_TYPE_TEXT)
				{
					$("#button-2").text("Previous")
					button_2_function = previousText
				}
				break
			case ACTIVITY_TYPE_COUNTDOWN:
				clearPlayButtons()
				showCountdown()
				var countdown = current_activity[activity_pos].time;
				$("#countdown-seconds-left").text(toHHMMSS_countdown(countdown))
				var countdown_id = setInterval(function()
				{
					countdown--
					if (countdown == 0)
					{
						if(!window_is_focus)
						{
							var title = document.title
							var exclamation_mark = true
							var countdown_focus_interval = setInterval(function()
							{
								if(window_is_focus)
								{
									document.title = title
									clearInterval(countdown_focus_interval)
								}
								else
								{
									if(exclamation_mark)
									{
										document.title = '! '+title
									}
									else
									{
										document.title = '. '+title
									}
									exclamation_mark = !exclamation_mark
								}
							}, 1000)
						}
						$("#countdown-seconds-left").text("Done!")
						clearInterval(countdown_id)
						clearPlayButtons()
						$("#button-1").text("Next")
						button_1_function = activityNext
					}
					else
					{
						$("#countdown-seconds-left").text(toHHMMSS_countdown(countdown))
					}
				}, 1000)
				break
			case ACTIVITY_TYPE_OPTION:
				clearPlayButtons()
				showOptions()
				var amount_of_options = 0
				for (x = 0; x < 6; x++)
				{
					if (x in current_activity[activity_pos].options)
					{
						amount_of_options++;
						$("#option-"+(x+1)).text(replaceTextWithVariable(current_activity[activity_pos].options[x].text, basic_information.variables_game, basic_information.variables_defined))
					}
					else
					{
						$("#option-"+(x+1)).parent().hide()
					}
				}
				$("#play-options li").css("height", 100 / amount_of_options + "%")
				$("#play-options li").css("line-height", $("#play-options li").css("height"))
				break
			case ACTIVITY_TYPE_STATUS:
				clearPlayButtons()
				showChangeStatus()
				var status_value = current_activity[activity_pos].value
				if (status_value > 0)
				{
					status_value = "+" + status_value
				}
				$("#status-changed").text(current_activity[activity_pos].status +": "+status_value)
				$("#button-1").text("Next")
				button_1_function = activityNext
				break
		}
	}
	else
	{
		change_face('default')
		change_background('default')
		showMainMenu()
	}
}
function previousText()
{
	current_activity_position -= 2
	activityNext()
}
// image management
function change_face(name)
{
	$('#face-container img').each(function(){
		if(name == $(this).data('name'))
		{
			current_face.hide()
			$(this).show()
			current_face = $(this)
			return false
		}
	})
}
function change_background(name)
{
	$('#background-container img').each(function(){
		if(name == $(this).data('name'))
		{
			current_background.hide()
			$(this).show()
			current_background = $(this)
			return false
		}
	})
}
// misc
function scrollingText(activity_pos, target, text, index, interval)
{
	if (index < 5) click_forward_text = false
	if (click_forward_text)
	{
		$(target).text(text)
		return
	}
	else if (activity_pos + 1 == current_activity_position && index < text.length) {
    $(target).append(text[index++])
    setTimeout(function () { scrollingText(activity_pos, target, text, index, interval); }, interval)
  }
}