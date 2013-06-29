var basic_information
var submenu_stack = new Array()
var previous_submenu_stack_position = new Array()
var submenu_stack_position = 0
var current_submenu = {}
var modify_submenu
var current_activity_modify = 0
var option_array
var variable_current_modify_selected = null
var status_current_modify_selected = null
var TIMEOUT_MILISECONDS = 35
var activity_saved = true
var back_up_lock = false
var save_lock = false
var avatar_uploading_lock = false
var face_uploading_lock = false
var background_uploading_lock = false
var current_face_selected = ''

function init()
{
	$('#current-activity-container').sortable({axis:'y', containment: '#current-activity-container', cursor: 'crosshair', forceHelperSize: true, forcePlaceholderSize: true, placeholder: 'modify-activity-sortable-placeholder', distance:5, stop: function(){activity_saved=false;activity_update_option_disable();}})
//	$("#activity-holder").sortable({axis:'y', containment: '#activity-holder-container', cursor: 'crosshair', forceHelperSize: true, forcePlaceholderSize: true, placeholder:'test'})
	$('body').data('test', 'test')
	$("#manage-container").hide()
	basic_information = new Object()
	if(!requestBasicInfoAjax($(document).getUrlParam('shoujo'), basic_information, true))
	{
		$('#manage-loading p').text('Error loading information')
		return
	}
	console.log(basic_information)
	option_array = basic_information.options
	current_submenu = option_array
	$("#manage-loading").hide()
	$("#manage-container").show()
	$("#manage-back").click(function()
	{
		hideAll()
		$("#manage-options").show()
	})
	hideAll()
	$("#manage-options").show()
	$("#show-variables").click(function()
		{
			selectOption("manage-variables")
		}
	)
	$("#show-images").click(function()
		{
			selectOption("manage-images")
		}
	)
	$("#show-activities").click(function()
		{
			selectOption("manage-activities")
		}
	)
	$("#show-status").click(function()
		{
			selectOption("manage-status")
		}
	)
	$("#show-events").click(function()
		{
			selectOption("manage-events")
		}
	)
	$("#show-menu").click(function()
		{
			selectOption("manage-menu")
		}
	)
	$("#show-information").click(function()
		{
			selectOption("manage-information")
		}
	)
	$("#menu-container ol").on("click", "li", function() {
			click_menu_button($(this).index());
	})
	$(document).on('click', 'a', on_anchor_click)
	$(document).on('change', 'select', on_select_change)
	$(document).on('blur', 'input, textarea', on_input_blur)
	refresh_variables()
	refresh_menu()
	refresh_status()
	refresh_activites()
	refresh_images()
	start_manage_loop()
	//selectOption("manage-menu")
}
function on_anchor_click()
{
	$anchor = $(this)
	switch($anchor.attr('id'))
	{
	case 'root-save':
		save()
		break
	case 'root-save-back-up':
		save()
		download_back_up()
		break
	case 'root-back-up':
		download_back_up()
		break
	case 'manage-information-file-anchor-upload':
		$('#manage-information-file-avatar').click()
		break
	case 'manage-face-delete':
		var face_name = $("#manage-images-modify-delete-faces-select").val()
		basic_information.images.faces_manage[face_name].is_deleted = true
		refresh_faces()
		break
	case 'manage-face-undo-deletion':
		var face_name = $("#manage-images-modify-delete-faces-select").val()
		basic_information.images.faces_manage[face_name].is_deleted = false
		refresh_faces()
		break
	case 'manage-images-face-select-image-anchor':
		$('#manage-images-face-file').click()
		break
	case 'manage-face-change-name':
		var new_name = $('#manage-face-name').val()
		if (new_name.length == 0)
		{
			
		}
		else
		{
			basic_information.images.faces_manage[$('#manage-images-modify-delete-faces-select').val()].new_name = new_name
			refresh_faces()
		}
		break
	case 'manage-images-face-upload-anchor':
		if (!face_uploading_lock && $('#manage-images-face-file').val() != '')
		{
			var face_name = $('input[name="face-name"]').val()
			if (face_name == '')
			{
					$('#manage-images-face-error').text(' - no name selected')
					break
			}
			else if($.inArray(name, basic_information.images.faces) !== -1)
			{
					$('#manage-images-face-error').text(' - name is already in use ('+face_name+')')
					break
			}
			face_uploading_lock = true
			var formData = new FormData($('#manage-images-face-new-form')[0])
			$.ajax({
				url: URL_PROCESSING,
				type: 'POST',
				xhr: function() {
						var myXhr = $.ajaxSettings.xhr()
						if(myXhr.upload){
								//myXhr.upload.addEventListener('progress',progressHandlingFunction, false)
						}
						return myXhr
				},
				beforeSend: function()
				{
					$('#manage-images-face-error').text(' - uploading')
				},
				success: function()
				{
					var date = new Date()
				},
				error: function()
				{
					$('#manage-images-face-error').text(' - upload error')
				},
				complete: function(data)
				{
					var $file = $('#manage-images-face-new-form')
					$file.replaceWith($file = $file.val('').clone(true))
					face_uploading_lock = false
					if (data.responseText == 'ok')
					{
						$('#manage-images-face-error').text('')
						var name = $('input[name="face-name"]').val()
						basic_information.images.faces.push(name)
						basic_information.images.faces_manage[name] = new Object()
						basic_information.images.faces_manage[name].new_name = name
						current_face_selected = name
						refresh_faces()
					}
					else
					{
						$('#manage-images-face-error').text(' - '+data.responseText)
					}
					$('input[name="face-name"]').val('')
				},
				dataType: 'text',
				data: formData,
				cache: false,
				contentType: false,
				processData: false
			});
		}
		break
	case 'variable-add-new':
		var name = $('[name="new-variable-name"]').val()
		var value = $('[name="new-variable-value"]').val()
		if (name in basic_information.variables_defined)
		{
			alert('Variable name ('+name+') is already used')
		}
		else
		{
			$('[name="new-variable-name"]').val('')
			$('[name="new-variable-value"]').val('')
			basic_information.variables_defined[name] = value
			basic_information.variables_deleted[name] = new Object()
			basic_information.variables_deleted[name].is_deleted = false
		}
		refresh_variables()
		break
	case 'status-add-new':
		var status = $('#new-variable-name').val()
		var length = basic_information.status.length
		for(var loop = 0; loop < length; loop++)
		{
			if (typeof basic_information.status[loop] == 'undefined') continue
			if (basic_information.status[loop] == status)
			{
				return
			}
		}
		basic_information.status.push(status)
		basic_information.status_manage[status] = new Object()
		var is_hidden = $('#new-status-hidden').prop('checked') ? '1' : '0'
		basic_information.status_manage[status].is_hidden = is_hidden
		basic_information.status_manage[status].is_deleted = false
		basic_information.status_manage[status].decrease_time = $('input[name="new-status-decrease-time"]:checked').val()
		refresh_status()
		break
	case 'menu-add-new-create-button':
		if($('[name="new-submenu-name"]').val() == '')
		{
			return
		}
		var new_option = new Object()
		new_option.once_per_day = $(this).attr('once-per-day') == 1 ? true : false
		new_option.text = $('[name="new-submenu-name"]').val()
		$('[name="new-submenu-name"]').val('')
		new_option.type = $('[name="new-submenu-type"]').find(':selected').val()
		new_option.deleted = false
		new_option.number = basic_information.request_new_option_number()
		console.log(new_option.number)
		new_option.time = get_time_value(
			$('[name="new-submenu-time"][value="0"]').is(':checked'),
			$('[name="new-submenu-time"][value="1"]').is(':checked'),
			$('[name="new-submenu-time"][value="2"]').is(':checked'),
			$('[name="new-submenu-time"][value="3"]').is(':checked')
		)
		if (new_option.type == OPTION_SUBOPTIONS)
		{
			new_option.options = new Array()
		}
		else if (new_option.type == OPTION_ACTIVITY)
		{
			new_option.activity_id = -1
		}
		submenu_stack[submenu_stack_position-1][previous_submenu_stack_position[submenu_stack_position]].options.push(new_option)
		refresh_menu()
		$('[name="new-submenu-name"]').val('')
		break
	case 'menu-modify-option-change':
		//modify_submenu = submenu_stack[submenu_stack_position-1][previous_submenu_stack_position[submenu_stack_position]]
		modify_submenu.text = $('#menu-modify-option-name').val()
		modify_submenu.time = get_time_value(
			$('[name="modify-submenu-time"][value="0"]').is(':checked'),
			$('[name="modify-submenu-time"][value="1"]').is(':checked'),
			$('[name="modify-submenu-time"][value="2"]').is(':checked'),
			$('[name="modify-submenu-time"][value="3"]').is(':checked')
		)
		modify_submenu.once_per_day = $('[name="modify-submenu-once-per-day"]').is(':checked')
		refresh_menu()
		break
	case 'menu-modify-option-delete':
		delete_submenu(modify_submenu.number, basic_information.options)
		if (modify_submenu.type == OPTION_SUBOPTIONS)
		{
			current_submenu = submenu_stack[--submenu_stack_position]
		}
		refresh_menu()
		break
	case 'modify-submenu-assign-activity-anchor':
		var activity_id = $('select[name="modify-submenu-activity"]').val()
		modify_submenu.activity_id = activity_id
		break
	case 'menu-modify-option-activity-new-activity-anchor':
		var new_id = basic_information.request_new_activity_id()
		basic_information.create_activity(new_id)
		modify_submenu.activity_id = new_id
		current_activity_modify = new_id
		refresh_activites()
		selectOption("manage-activities")
		break
	case 'menu-modify-option-activity-alter-activity-anchor':
		current_activity_modify = modify_submenu.activity_id
		refresh_activites()
		selectOption("manage-activities")
		break
	case 'manage-activity-append-new-child':
		if (current_activity_modify == 0 || current_activity_modify >= 10)
		{
			$('#current-activity-container').append($('#clone-new-activity-general').children().clone())
			refresh_images()
			activity_saved = false
		}
		break
	case 'manage-activity-save-anchor':
		if (current_activity_modify == 0 || current_activity_modify >= 10)
		{
			save_current_activity_modify()
		}
		break
	case 'modify-activity-friendly-name-anchor':
		var friendly_name = $('#modify-activity-friendly-name').val()
		if (current_activity_modify >= 10)
		{
			basic_information.activity_manage[current_activity_modify].friendly_name = friendly_name
			if (friendly_name != '')
			{
				$('#activities-list-selection').find(':selected').text(friendly_name+' (id: '+current_activity_modify+')')
			}
			else
			{
				$('#activities-list-selection').find(':selected').text(current_activity_modify)
			}
		}
		break
	}
	switch($anchor.attr('class'))
	{
	case 'variable-list-undo-deletion-anchor':
		var name = $anchor.data('variable_name')
		basic_information.variables_deleted[name].is_deleted = false
		refresh_variables()
		break
	case 'status-list-undo-deletion-anchor':
		var name = $anchor.data('status_name')
		basic_information.status_manage[name].is_deleted = false
		refresh_status()
		break
	case 'menu-option-deleted-undo-deletion':
		var number = $anchor.attr('number')
		undo_submenu_deletion(number, basic_information.options)
		refresh_menu()
		break
	case 'new-activity-anchor-delete-this':
		$anchor.closest('.new-activity-style-box').remove()
		activity_saved = false
		break
	}
	switch($anchor.attr('name'))
	{
	case 'variable-change-value':
		var $select = $('#variables-modify-user-defined-list')
		basic_information.variables_defined[variable_current_modify_selected] = $select.parent().find('input').first().val()
		refresh_variables()
		break
	case 'variable-delete':
		basic_information.variables_deleted[variable_current_modify_selected].is_deleted = true
		refresh_variables()
		break
	case 'status-change-value':
		var $select = $('#status-modify-list')
		var new_value = $select.parent().find('input').first().val()
		basic_information.status_manage[new_value] = basic_information.status_manage[status_current_modify_selected]
		basic_information.status_manage[new_value].is_hidden = $select.parent().find('[name="modify-status-hidden"]').prop('checked') ? '1' : '0'
		basic_information.status_manage[new_value].decrease_time = $select.parent().find('[name="modify-status-decrease-time"]:checked').val()
		if (status_current_modify_selected != new_value)
		{
			delete basic_information.status_manage[status_current_modify_selected]
		}
		basic_information.status[$.inArray(status_current_modify_selected, basic_information.status)] = new_value
		status_current_modify_selected = new_value
		refresh_status()
		break
	case 'status-delete':
		basic_information.status_manage[status_current_modify_selected].is_deleted = true
		refresh_status()
		break
	
	}
}
function on_select_change()
{
	$select = $(this)
	switch($select.attr('id'))
	{
	case 'variables-modify-user-defined-list':
		update_modify_variable_value()
		break
	case 'status-modify-list':
		update_modify_status_value()
		break
	case 'activities-list-selection':
		current_activity_modify = $('#activities-list-selection').val()
		refresh_activites()
		break
	case 'manage-images-modify-delete-faces-select':
		refresh_images_inputs()
		break
	}
	switch($select.attr('class'))
	{
	case 'new-activity-face-list':
		activity_saved = false
		break
	case 'new-activity-background-list':
		activity_saved = false
		break
	case 'new-activity-select-type':
		$select.parent().find('.clone-new-activity-type-container').remove()
		switch($select.val()+'')
		{
			case ACTIVITY_TYPE_TEXT:
				$select.parent().append($('#clone-new-activity-text').children().clone())
				break
			case ACTIVITY_TYPE_COUNTDOWN:
				$select.parent().append($('#clone-new-activity-countdown').children().clone())
				break
			case ACTIVITY_TYPE_STATUS:
				$select.parent().append($('#clone-new-activity-status').children().clone())
				var $status_select = $select.parent().find('.new-activity-status')
				var length = basic_information.status.length
				for(var loop = 0; loop < length; loop++)
				{
					var name = basic_information.status[loop]
					if (!basic_information.status_manage[name].is_deleted)
					{
						$status_select.append($('<option />').val(name).text(name))
					}
				}
				break
			case ACTIVITY_TYPE_OPTION:
				$select.parent().append($('#clone-new-activity-option').children().clone())
				break
		}
		activity_update_option_disable()
		activity_saved = false
	}
}
function on_input_blur()
{
	$input = $(this)
	switch($input.attr('id'))
	{
	case 'information-name':
		if ($input.val() == '')
		{
			$input.val(DEFAULT_NAME)
		}
		break
	case 'information-description':
		if ($input.val() == '')
		{
			$input.val(DEFAULT_DESCRIPTION)
		}
	}
}
function start_manage_loop()
{
	var timer = setInterval(function()
	{
		if ($('#manage-images-face-file').val() != '')
		{
			var text_image = ' - image: '+$('#manage-images-face-file').val().split('\\').pop()
			$('#manage-images-face-filename').text(text_image)
		}
		else
		{
			$('#manage-images-face-filename').text('')
		}
		if (!avatar_uploading_lock && $('#manage-information-file-avatar').val() != '')
		{
			avatar_uploading_lock = true
			var formData = new FormData($('#manage-information-change-avatar')[0])
			$.ajax({
				url: URL_PROCESSING,
				type: 'POST',
				xhr: function() {
						var myXhr = $.ajaxSettings.xhr()
						console.log(myXhr)
						if(myXhr.upload){
								//myXhr.upload.addEventListener('progress',progressHandlingFunction, false)
						}
						return myXhr
				},
				beforeSend: function()
				{
					$('#manage-information-file-status-upload').text(' - uploading')
				},
				success: function(status)
				{
					$('#manage-information-file-status-upload').text('')
					var date = new Date()
					$('#information-avatar, #avatar-main-menu').attr('src', '/images/avatar/'+$(document).getUrlParam('shoujo')+'.jpg?'+date.getTime())
					$('#manage-information-file-status-upload').text('')
				},
				error: function()
				{
					$('#manage-information-file-status-upload').text(' - upload error')
				},
				complete: function()
				{
					avatar_uploading_lock = false
				},
				data: formData,
				cache: false,
				contentType: false,
				processData: false
			});
			var $file = $('#manage-information-file-avatar')
			$file.replaceWith($file = $file.val('').clone(true))
		}
		var string
		$(".new-activity-textarea").each(function() {
			new_activity_text_parse($(this), $(this).next('p:first'))
		})
		$(".status-change-value").each(function() {
			status_value_parse($(this))
		})
		// description
		string = $('#information-description').val().substring(0, MAX_DESCRIPTION_LENGTH)
		string = string.replace('\n', '')
		basic_information.information.description = string
		var left = MAX_DESCRIPTION_LENGTH - $('#information-description').val().length
		if (left < 0) left = 0
		$('#information-description-characters-left').text(left)
		$('#information-description').val(string)
		$('.description-global').text(string)
		// name
		var old_string = $('#information-name').val()
		string = $('#information-name').val().substring(0, MAX_NAME_LENGTH)
		basic_information.information.name = string
		left = MAX_NAME_LENGTH - $('#information-name').val().length
		if (left < 0) left = 0
		$('#information-name-characters-left').text(left)
		$('.name-global').text(string)
		document.title = 'Manage '+string
		// variable
		string = $('[name="new-variable-name"]').val()
		string = string.replace(/[^\w]/g, '').toUpperCase()
		if (string != $('[name="new-variable-name"]').val())
		{
			$('[name="new-variable-name"]').val(string)
		}
		// menu
		$('#new-submenu-characters-left').text(MENU_MAX_LENGTH - $('[name="new-submenu-name"]').val().length)
		// activity
		$(".new-activity-textarea").each(function() {
			new_activity_text_parse($(this), $(this).next('p:first'))
		})
		$(".status-change-value").each(function() {
			status_value_parse($(this))
		})
		$('.new-activity-countdown-time').each(function()
		{
			time_value_parse($(this))
		})
		if (activity_saved)
		{
			$('#manage-activity-saved-status').text('saved!').removeClass().addClass('saved')
		}
		else
		{
			$('#manage-activity-saved-status').text('changes not saved yet').removeClass().addClass('not-saved')
		}
		$('.disabled-activity').each(function()
		{
			var width = $(this).parent().innerWidth()
			var height = $(this).parent().innerHeight()
			$(this).width(width)
			$(this).height(height)
			$(this).find('p').css('line-height', height+'px')
		})
	}, TIMEOUT_MILISECONDS)
}
function showOptions()
{
	hideAll()
	$("#manage-options").show()
}
function selectOption(id)
{
	hideAll()
	$("#"+id).show()
	$("#manage-back").show()
}
function hideAll()
{
	$("#manage-back").hide()
	$("#manage-images").hide()
	$("#manage-activities").hide()
	$("#manage-variables").hide()
	$("#manage-information").hide()
	$("#manage-menu").hide()
	$("#manage-status").hide()
	$("#manage-events").hide()
	$("#manage-options").hide()
	$("#manage-activity").hide()
}
// misc
function save()
{
	if (!save_lock)
	{
		save_lock = true
		var xml = generate_xml_information(basic_information)
		$('#root-save-form').find('[name="xml"]').val(xml)
		//$('#root-save-form').submit()
		var formData = new FormData($('#root-save-form')[0])
		$.ajax({
			url: URL_PROCESSING,
			type: 'POST',
			beforeSend: function()
			{
				$('#manage-information-file-status-upload').text(' - uploading')
			},
			success: function(status)
			{
				var date = new Date()
				$('#root-last-saved').text('last saved: '+date.getHours()+':'+date.getMinutes())
			},
			error: function()
			{
				var date = new Date()
				$('#root-last-saved').text('error while saving')
			},
			complete: function()
			{
				save_lock = false
			},
			data: formData,
			cache: false,
			contentType: false,
			processData: false
		})
	}
}
function download_back_up()
{
	var timer = setTimeout(function()
	{
		if (!save_lock)
		{
			$('#root-back-up-form').submit()
			window.clearTimeout(timer)
		}
	}, 200)
}
// menu
function click_menu_button(index)
{
	if (submenu_stack_position > 0 && index == 0)
	{
		current_submenu = submenu_stack[--submenu_stack_position]
		if (submenu_stack_position > 0)
		{
				modify_submenu = submenu_stack[submenu_stack_position-1][previous_submenu_stack_position[submenu_stack_position]]
		}
	}
	else
	{
		var index_menu = index
		if (submenu_stack_position > 0)
		{
			index_menu--
		}
		if (!current_submenu[index_menu].deleted)
		{
			modify_submenu = current_submenu[index_menu]
			switch(current_submenu[index_menu].type)
			{
				case OPTION_SUBOPTIONS:
					$('[name="parent"]').val(current_submenu[index_menu].number)
					previous_submenu_stack_position[submenu_stack_position+1] = index_menu
					submenu_stack[submenu_stack_position++] = current_submenu
					current_submenu = current_submenu[index_menu].options
				case OPTION_ACTIVITY:
			}
		}
	}
	refresh_menu()
}
function refresh_menu()
{
	if (typeof modify_submenu != 'undefined' && modify_submenu.deleted == false)
	{
		$('#menu-modify-option-activity-container').hide()
		$('#menu-modify-option-suboption-container').hide()
		$('#menu-modify-option-name').val(modify_submenu.text)
		if (modify_submenu.once_per_day)
		{
			$('[name="modify-submenu-once-per-day"]').prop("checked", true)
		}
		else
		{
			$('[name="modify-submenu-once-per-day"]').prop("checked", false)
		}
		if (is_in_time(modify_submenu.time, 9)) // 9 = 9am, example time for morning
		{
			$('[name="modify-submenu-time"][value="0"]').prop("checked", true)
		}
		else
		{
			$('[name="modify-submenu-time"][value="0"]').prop("checked", false)
		}
		if (is_in_time(modify_submenu.time, 15))
		{
			$('[name="modify-submenu-time"][value="1"]').prop("checked", true)
		}
		else
		{
			$('[name="modify-submenu-time"][value="1"]').prop("checked", false)
		}
		if (is_in_time(modify_submenu.time, 20))
		{
			$('[name="modify-submenu-time"][value="2"]').prop("checked", true)
		}
		else
		{
			$('[name="modify-submenu-time"][value="2"]').prop("checked", false)
		}
		if (is_in_time(modify_submenu.time, 3))
		{
			$('[name="modify-submenu-time"][value="3"]').prop("checked", true)
		}
		else
		{
			$('[name="modify-submenu-time"][value="3"]').prop("checked", false)
		}
		switch (modify_submenu.type)
		{
		case OPTION_SUBOPTIONS:
			$('#modify-submenu-current-name').text('suboption ('+modify_submenu.text+')')
			$('#menu-modify-option-suboption-container').show()
			break
		case OPTION_ACTIVITY:
			$('#modify-submenu-current-name').text('activity ('+modify_submenu.text+')')
			$('#menu-modify-option-activity-container').show()
			break
		}
	}
	if(submenu_stack_position == 0)
	{
		$("#menu-add-option").hide()
		$("#menu-modify-option").hide()
	}
	else if (submenu_stack_position == 1)
	{
		$("#menu-add-option").show()
		if (modify_submenu.type == OPTION_ACTIVITY)
		{
			$("#menu-modify-option").show()
		}
		else
		{
			$("#menu-modify-option").hide()
		}
	}
	else
	{
		$("#menu-add-option").show()
		if (modify_submenu.deleted == false)
		{
			$("#menu-modify-option").show()
		}
		else
		{
			$("#menu-modify-option").hide()
		}
	}
	$("#menu-container ol").empty()
	if (submenu_stack_position != 0)
	{
		$("#menu-container ol").append("<li value=\"0\">..</li>");
	}
	for(var loop = 0; loop < current_submenu.length; loop++)
	{
		if(submenu_stack_position == 0)
		{
			$("#menu-container ol").append($('<li />').text(current_submenu[loop].text))
		}
		else
		{
			var once_per_day = current_submenu[loop].once_per_day ? '(once per day) - ' : ''
			var is_deleted = false
			var classname = 'deleted-variable'
			var $link_delete = undefined
			if (current_submenu[loop].deleted)
			{
				is_deleted = true
				$link_delete = $('<a />').text('undo deletion').addClass('menu-option-deleted-undo-deletion').attr('number', current_submenu[loop].number)
			} 

			switch(current_submenu[loop].type)
			{
				case OPTION_SUBOPTIONS:
					if (is_deleted)
					{
						$("#menu-container ol").append($('<li />').append($('<span />').text(current_submenu[loop].text+' (sub-option) - '+once_per_day+'('+get_time_string(current_submenu[loop].time)+')').addClass(classname)).append(' - ').append($link_delete))
					}
					else
					{
						$("#menu-container ol").append($('<li />').text(current_submenu[loop].text+' (sub-option) - '+once_per_day+'('+get_time_string(current_submenu[loop].time)+')'))
					}
					break
				case OPTION_ACTIVITY:
					if (is_deleted)
					{
						$("#menu-container ol").append($('<li />').append($('<span />').text(current_submenu[loop].text+' (activity) - '+once_per_day+'('+get_time_string(current_submenu[loop].time)+')').addClass(classname)).append(' - ').append($link_delete))
					}
					else
					{
						$("#menu-container ol").append($('<li />').text(current_submenu[loop].text+' (activity) - '+once_per_day+'('+get_time_string(current_submenu[loop].time)+')'))
					}
					break
				case OPTION_EVENT:
					if (is_deleted)
					{
						$("#menu-container ol").append($('<li />').append($('<span />').text(current_submenu[loop].text+' (event)').addClass(classname)).append(' - ').append($link_delete))
					}
					else
					{
						$("#menu-container ol").append($('<li />').text(current_submenu[loop].text+' (event)</li>'))
					}
					break
				case OPTION_STATUS:
					if (is_deleted)
					{
						$("#menu-container ol").append($('<li />').append($('<span />').text(current_submenu[loop].text+' (status)').addClass(classname)).append(' - ').append($link_delete))
					}
					else
					{
						$("#menu-container ol").append($('<li />').text(current_submenu[loop].text+' (status)'))
					}
					break
			}
			//$("#menu-container ol").append("<li>"+current_submenu[loop].text+' - <a class="menu-item-remove">remove</a> - <a class="menu-item-manage">manage</a></li>')
		}
	}
	if (typeof modify_submenu != 'undefined' && modify_submenu.type == OPTION_SUBOPTIONS)
	{
		$('[name="new-submenu-time"]').prop('checked', false)
		/*if (is_in_time(modify_submenu.time, 9)) // 9 = 9am, example time for morning
		{
			$('[name="new-submenu-time"][value="0"]').removeAttr("disabled")
		}
		else
		{
			$('[name="new-submenu-time"][value="0"]').attr("disabled", true)
		}
		if (is_in_time(modify_submenu.time, 15))
		{
			$('[name="new-submenu-time"][value="1"]').removeAttr("disabled")
		}
		else
		{
			$('[name="new-submenu-time"][value="1"]').attr("disabled", true)
		}
		if (is_in_time(modify_submenu.time, 20))
		{
			$('[name="new-submenu-time"][value="2"]').removeAttr("disabled")
		}
		else
		{
			$('[name="new-submenu-time"][value="2"]').attr("disabled", true)
		}
		if (is_in_time(modify_submenu.time, 3))
		{
			$('[name="new-submenu-time"][value="3"]').removeAttr("disabled")
		}
		else
		{
			$('[name="new-submenu-time"][value="3"]').attr("disabled", true)
		}*/
	}
}
function undo_submenu_deletion(number, option_array)
{
	var length = option_array.length
	for (var loop = 0; loop < length; loop++)
	{
		if (option_array[loop].number == number)
		{
			option_array[loop].deleted = false
			return true
		}
		else if(option_array[loop].type == OPTION_SUBOPTIONS)
		{
			if (undo_submenu_deletion(number, option_array[loop].options))
			{
				return true
			}
		}
	}
	return false
}
function delete_submenu(number, option_array)
{
	var length = option_array.length
	for (var loop = 0; loop < length; loop++)
	{
		if (option_array[loop].number == number)
		{
			option_array[loop].deleted = true
			return true
		}
		else if(option_array[loop].type == OPTION_SUBOPTIONS)
		{
			if (delete_submenu(number, option_array[loop].options))
			{
				return true
			}
		}
	}
	return false
}
function new_activity_text_parse(textarea, counter)
{
	var old_value = textarea.val()
	var string = textarea.val().substring(0, 256)
	string = string.replace('\n', '')
	counter.text(256 - textarea.val().length + ' characters left')
	if (old_value != string)
	{
		textarea.val(string)
	}
	var hidden_input = textarea.parent().find('.new-activity-textarea-changer-helper')
	if (string != hidden_input.val())
	{
		hidden_input.val(string)
		activity_saved = false
	}
}
function activity_status_change_value(event, ui)
{
	$('p').text(ui.value)
}
function repopulate_status()
{
	$('.status-name-activity').empty()
	for (var loop = 0; loop < basic_information.status.length; loop++)
	{
		$('.status-name-activity').each(function()
		{
			$(this).append($('<option />').val(basic_information.status[loop]).text(basic_information.status[loop]))
		})
	}
}
function fill_this_status(objectJquery)
{
	for (var loop = 0; loop < basic_information.status.length; loop++)
	{
		objectJquery.empty()
		objectJquery.each(function()
		{
			objectJquery.append($('<option />').val(basic_information.status[loop]).text(basic_information.status[loop]))
		})
	}
}
function status_value_parse(objectJquery)
{
	if (objectJquery.val().length > 0 && /^(.)\1+$/.test(objectJquery.val()) && objectJquery.val().substr(0, 1) == 0)
	{
		objectJquery.val('0')
	}
	if (objectJquery.val() != '' && objectJquery.val() != '-')
	{
		var number = objectJquery.val().replace(/[^\d.-]/g,'')
		number = Math.floor(number)
		if (number > ACTIVITY_STATUS_MAX_VALUE)
		{
			number = ACTIVITY_STATUS_MAX_VALUE
		}
		else if (number < -ACTIVITY_STATUS_MAX_VALUE)
		{
			number = -ACTIVITY_STATUS_MAX_VALUE
		}
		if (number != objectJquery.val())
			objectJquery.val(number)
	}
}
function time_value_parse($input)
{
	if ($input.val().length > 0 && /^(.)\1+$/.test($input.val()) && $input.val().substr(0, 1) == 0)
	{
		$input.val('0')
	}
	if ($input.val() != '' && $input.val() != '-')
	{
		var number = $input.val().replace(/[^\d.-]/g,'')
		number = Math.floor(number)
		if (number > ACTIVITY_TIME_MAX)
		{
			number = ACTIVITY_TIME_MAX
		}
		else if (number < 1)
		{
			number = 0
		}
		if (number != $input.val())
		{
			$input.val(number)
			activity_saved = false
		}
	}
}
// variables
function refresh_variables()
{
	// list
	var $container = $('#variables-user-defined-container-list')
	$container.empty()
	var variable_flag = false
	for(var name in basic_information.variables_defined)
	{
		if(!variable_flag)
		{
			$container.append($('<ul />').attr('id', 'variables-user-defined-list'))
		}
		var $list = $('#variables-user-defined-list')
		if (!basic_information.variables_deleted[name].is_deleted)
		{
			$list.append($('<li />').text('%'+name+' = '+' '+basic_information.variables_defined[name]))
		}
		else
		{
			$list.append($('<li />').html('<span class="deleted-variable">%'+name+' = '+' '+basic_information.variables_defined[name]+'</span> - <a data-variable_name="'+name+'" class="variable-list-undo-deletion-anchor">undo deletion</a>'))
		}
		variable_flag = true
	}
	if (!variable_flag)
	{
		$container.html('<p>You haven\'t defined any variable yet</p>')
	}
	// modify
	$container = $('#variables-user-defined-container-modify')
	$container.empty()
	variable_flag = false
	for(var name in basic_information.variables_defined)
	{
		if (basic_information.variables_deleted[name].is_deleted) continue
		if(!variable_flag)
		{
			$container.append($('<select />').attr('id', 'variables-modify-user-defined-list'))
		}
		var flag_select = false
		if (variable_current_modify_selected == null || (name == variable_current_modify_selected && !basic_information.variables_deleted[name].is_deleted))
		{
			variable_current_modify_selected = name
			flag_select = true
		}
		var $list = $('#variables-modify-user-defined-list')
		var $option_append = $('<option />').attr('class', 'variable-modify-option').val(name).text('%'+name)
		if (flag_select)
		{
			$option_append.attr('selected', true)
		}
		$list.append($option_append)
		variable_flag = true
	}
	if (!variable_flag)
	{
		$container.html('<p>You haven\'t defined any variable yet</p>')
	}
	else
	{
		$container.append($('#clone-variable-modify').children().clone())
	}
	update_modify_variable_value()
}
function update_modify_variable_value()
{
	var $select = $('#variables-modify-user-defined-list')
	variable_current_modify_selected = $select.find(':selected').val()
	$select.parent().find('input').first().val(basic_information.variables_defined[$select.find(':selected').val()])
}
// status
function decrease_time_to_string(decrease_time)
{
	switch(decrease_time)
	{
	case STATUS_DECREASE_NONE:
		return 'never'
	case STATUS_DECREASE_HOUR:
		return 'hourly'
	case STATUS_DECREASE_DAY:
		return 'daily'
	}
	return 'error'
}
function refresh_status()
{
	// list
	var $container = $('#status-container-list')
	$container.empty()
	$('.new-activity-status').empty()
	var variable_flag = false
	var length = basic_information.status.length
	for(var loop = 0; loop < length; loop++)
	{
		var name = basic_information.status[loop]
		if(!variable_flag)
		{
			$container.append($('<ul />').attr('id', 'status-list'))
		}
		var $list = $('#status-list')
		if (!basic_information.status_manage[name].is_deleted)
		{
			$list.append($('<li />').text(name+' - '+(basic_information.status_manage[name].is_hidden == '1' ? 'hidden' : 'visible')+' - decreases: '+decrease_time_to_string(basic_information.status_manage[name].decrease_time)))
		}
		else
		{
			$list.append($('<li />').html('<span class="deleted-variable">'+name+' - '+(basic_information.status_manage[name].is_hidden == '1' ? 'hidden' : 'visible')+' - decreases: '+decrease_time_to_string(basic_information.status_manage[name].decrease_time)+'</span> - <a data-status_name="'+name+'" class="status-list-undo-deletion-anchor">undo deletion</a>'))
			$('.new-activity-status').each(function()
			{
				$(this).append($('<option />').val(name).text(name))
			})
		}
		variable_flag = true
	}
	if (!variable_flag)
	{
		$container.html('<p>You haven\'t defined any variable yet</p>')
	}
	// modify
	$container = $('#status-container-modify')
	$container.empty()
	variable_flag = false
	for(var loop = 0; loop < length; loop++)
	{
		var name = basic_information.status[loop]
		if (basic_information.status_manage[name].is_deleted) continue
		if(!variable_flag)
		{
			$container.append($('<select />').attr('id', 'status-modify-list'))
		}
		var flag_select = false
		if (status_current_modify_selected == null || (name == status_current_modify_selected && !basic_information.status_manage[name].is_deleted))
		{
			status_current_modify_selected = name
			flag_select = true
		}
		var $list = $('#status-modify-list')
		var $option_append = $('<option />').attr('class', 'status-modify-option').val(name).text(name)
		if (flag_select)
		{
			$option_append.attr('selected', true)
		}
		$list.append($option_append)
		variable_flag = true
	}
	if (!variable_flag)
	{
		$container.html('<p>You haven\'t defined any status yet</p>')
	}
	else
	{
		$container.append($('#clone-status-modify').children().clone())
	}
	update_modify_status_value()
}
function update_modify_status_value()
{
	var $select = $('#status-modify-list')
	status_current_modify_selected = $select.find(':selected').val()
	if ($.inArray(status_current_modify_selected, basic_information.status_manage) !== -1)
	{
		$select.parent().find('input').first().val($select.find(':selected').val())
		$select.parent().find('[name="modify-status-hidden"]').attr('checked', basic_information.status_manage[status_current_modify_selected].is_hidden == '1' ? true : false)
		$select.parent().find('[name="modify-status-decrease-time"][value="'+(basic_information.status_manage[status_current_modify_selected].decrease_time)+'"]').attr('checked', true)
	}
}
// activities
function refresh_activites()
{
	var $list = $('#activities-list-selection')
	var $list_menu = $('select[name="modify-submenu-activity"]')
	var $activity_container = $('#current-activity-container')
	$list.empty()
	$list_menu.empty()
	$activity_container.empty()
	$('#current-activity-container-parent').hide()
	var length = basic_information.activity_array.length
	var loop = 0
	var value_array_flag = false
	var first_value = false
	while (loop < length)
	{
		if (typeof basic_information.activity_array[loop] == 'undefined')
		{
			loop++
			continue
		}
		if (first_value == false)
		{
			first_value = loop
		}
		if (!value_array_flag)
		{
			if (current_activity_modify == loop)
			{
				value_array_flag = true
			}
		}
		$to_append = $('<option />')
		$to_append.val(loop)
		if (basic_information.activity_manage[loop].friendly_name != '')
		{
			$to_append.text(basic_information.activity_manage[loop].friendly_name+' (id: '+loop+')')
		}
		else
		{
			$to_append.text('id: '+loop)
		}
		$list.append($to_append)
		if (loop >= 10)
		{
			$list_menu.append($to_append.clone())
		}
		loop++
	}
	if (!value_array_flag && first_value != false)
	{
		current_activity_modify = first_value
	}
	if (value_array_flag || first_value != false)
	{
		$list.val(current_activity_modify)
		$('#current-activity-container-parent').show()
		var activity = basic_information.activity_array[current_activity_modify]
		$('#modify-activity-friendly-name').val(basic_information.activity_manage[current_activity_modify].friendly_name)
		var length = activity.length
		for (var loop = 0; loop < length; loop++)
		{
			var $clone_general = $('#clone-new-activity-general').children().clone()
			$clone_general.find('.new-activity-select-type').val(activity[loop].type)
			switch(activity[loop].type)
			{
			case ACTIVITY_TYPE_TEXT:
				$clone_general.children().first().append($('#clone-new-activity-text').children().clone())
				$clone_general.find('.new-activity-textarea').val(activity[loop].text)
				$clone_general.find('.new-activity-textarea-changer-helper').val(activity[loop].text)
				break
			case ACTIVITY_TYPE_COUNTDOWN:
				$clone_general.children().first().append($('#clone-new-activity-countdown').children().clone())
				$clone_general.find('.new-activity-countdown-time').val(activity[loop].time)
				break
			case ACTIVITY_TYPE_STATUS:
				$clone_general.children().first().append($('#clone-new-activity-status').children().clone())
				$clone_general.find('.status-change-value').val(activity[loop].value)
				break
			case ACTIVITY_TYPE_OPTION:
				$clone_general.children().first().append($('#clone-new-activity-text').children().clone())
				$clone_general.find('.new-activity-textarea').val(activity[loop].text)
				break
			}
			$activity_container.append($clone_general)
		}
		if (current_activity_modify == 0 || current_activity_modify >= 10)
		{
			if (current_activity_modify == 0)
			{
				$('#modify-activity-friendly-name').prop('disabled', true)
				$('#modify-activity-friendly-name-anchor').addClass('disabled-anchor')
			}
			else
			{
				$('#modify-activity-friendly-name').prop('disabled', false)
				$('#modify-activity-friendly-name-anchor').removeClass('disabled-anchor')
			}
			$('#manage-activity-save-anchor, #manage-activity-append-new-child').removeClass('disabled-anchor')
		}
		else
		{
			$('#modify-activity-friendly-name').prop('disabled', true)
			$('#modify-activity-friendly-name-anchor, #manage-activity-save-anchor, #manage-activity-append-new-child').addClass('disabled-anchor')
		}
	}
}
function save_current_activity_modify()
{
	var activity_id = $('#activities-list-selection').val()
	basic_information.activity_array[activity_id] = new Array()
	var activity = basic_information.activity_array[activity_id]
	var number = 0
	var option_flag = false
	$('#current-activity-container').children().each(function()
	{
		var to_push = new Object()
		to_push.type = $(this).find('.new-activity-select-type').val()
		if (option_flag || $(this).find('.disabled-activity').length > 0 || to_push.type == 'undefined')
		{
			$(this).remove()
			return
		}
		switch(to_push.type)
		{
			case ACTIVITY_TYPE_TEXT:
				to_push.text = $(this).find(".new-activity-textarea").val()
				if (to_push.text.length == 0 || to_push.text == '')
				{
					$(this).remove()
					return
				}
				break
			case ACTIVITY_TYPE_COUNTDOWN:
				to_push.time = $(this).find(".new-activity-countdown-time").val()
				if (to_push.time == 0)
				{
					$(this).remove()
					return
				}
				break
			case ACTIVITY_TYPE_STATUS:
				to_push.status = $(this).find(".new-activity-status").val()
				to_push.value = $(this).find(".new-activity-value").val()
				if (to_push.value == 0)
				{
					$(this).remove()
					return
				}
				break
			case ACTIVITY_TYPE_OPTION:
				to_push.options = new Array()
				$(this).find(".new-activity-option-container-ind").each(
					function()
					{
						var is_checked = $(this).find('input[type="checkbox"]').is(":checked")
						var value_option = $(this).find('input[type="text"]').val()
						if(is_checked && value_option)
						{
							var option_push = new Object()
							option_push.text = value_option
							option_push.activity_id = -1 // todo
							to_push.options.push(option_push)
						}
					}
				)
				if (to_push.options.length == 0)
				{
					$(this).remove()
					return // #current-activity-container each
				}
				option_flag = true
				break
		}
		to_push.number = number++
		var face = $(this).find('.new-activity-face-list').val()
		if (face != 'no-change')
		{
			to_push.face = face
		}
		activity.push(to_push)
	})
	console.log(activity)
	activity_saved = true
	//refresh_activities()
}
function activity_update_option_disable()
{
	var index = -1
	var $activity_container = $('#current-activity-container')
	$activity_container.find('.disabled-activity').remove()
	$activity_container.children().each(function()
	{
		if($(this).find('.new-activity-select-type').val() == ACTIVITY_TYPE_OPTION)
		{
			index = $(this).index()
			return false
		}
	})
	if (index != -1)
	{
		$activity_container.children(':gt('+index+')').each(function()
		{
			$(this).append($('#clone-new-activity-disabled-overlay').children().clone())
		})
	}
}
// images
function refresh_images()
{
	refresh_backgrounds()
	refresh_faces()
}
function refresh_backgrounds()
{
	// todo: manage backgrounds, activities
}
function refresh_faces()
{
	var length = basic_information.images.faces.length
	if(length == 0)
	{
		$('#manage-images-modify-delete-faces-container-no-faces').show()
		$('#manage-images-modify-delete-faces-container').hide()
	}
	else
	{
		$('#manage-images-modify-delete-faces-container-no-faces').hide()
		$('#manage-images-modify-delete-faces-container').show()
		var $image_manager_list = $('#manage-images-modify-delete-faces-select')
		var $activity_list = $('.new-activity-face-list-optgroup-user-defined')
		/*$image_manager_list.find('option').each(function()
		{
			if (basic_information.images.faces_manage[$(this).val()].is_deleted)
			{
				$(this).text(basic_information.images.faces_manage[$(this).val()].new_name+' - deleted')
			}
		})
		$activity_list.each(function()
		{
			$(this).find('option').each(function()
			{
				if (basic_information.images.faces_manage[$(this).val()].is_deleted)
				{
					if ($(this).parent('select').val() == $(this).val())
					{
						$(this).parent('select').val('no-change')
					}
					$(this).remove()
				}
			})
		})*/
		for(var loop = 0; loop < length; loop++)
		{
			var name = basic_information.images.faces[loop]
			var name_manage = basic_information.images.faces_manage[name].new_name
			var $option = $('<option />').val(name)
			if (basic_information.images.faces_manage[name].is_deleted)
			{
				$option.text(basic_information.images.faces_manage[name].new_name+' - deleted')
			}
			else
			{
				$option.text(basic_information.images.faces_manage[name].new_name)
			}
			if ($image_manager_list.find('option[value="'+name+'"]').length == 0)
			{
				$image_manager_list.append($option)
			}
			else
			{
				if (basic_information.images.faces_manage[name].is_deleted)
				{
					$image_manager_list.find('option[value="'+name+'"]').text(basic_information.images.faces_manage[name].new_name+' - deleted')
				}
				else
				{
					$image_manager_list.find('option[value="'+name+'"]').text(basic_information.images.faces_manage[name].new_name)
				}
			}
			$activity_list.each(function()
			{
				if ($(this).find('option[value="'+name+'"]').length == 0 && !basic_information.images.faces_manage[name].is_deleted)
				{
					$(this).append($option.clone())
				}
				else
				{
					if (basic_information.images.faces_manage[name].is_deleted)
					{
						$(this).find('option[value="'+name+'"]').remove()
					}
					else
					{
						$(this).find('option[value="'+name+'"]').text(basic_information.images.faces_manage[name].new_name)
					}
				}
			})
		}
		if (!current_face_selected || current_face_selected == '')
		{
			current_face_selected = $image_manager_list.val()
		}
		$image_manager_list.val(current_face_selected)
		$('#manage-face-open-current').attr('href', image_url($(document).getUrlParam('shoujo'), current_face_selected, 1))
		current_face_selected = ''
		refresh_images_inputs()
	}
}
function refresh_images_inputs()
{
	var face_name = $('#manage-images-modify-delete-faces-select').val()
	var value = $('#manage-images-modify-delete-faces-select').val()
	$('#manage-face-name').val(basic_information.images.faces_manage[value].new_name)
	$('#manage-images-modify-delete-faces-container-not-deleted').hide()
	$('#manage-images-modify-delete-faces-container-deleted').hide()
	if (basic_information.images.faces_manage[value].is_deleted)
	{
		$('#manage-images-modify-delete-faces-container-deleted').show()
	}
	else
	{
		$('#manage-images-modify-delete-faces-container-not-deleted').show()
		if (face_name == 'default')
		{
			$('#manage-face-delete-container').hide()
		}
		else
		{
			$('#manage-face-delete-container').show()
		}
		$('#manage-face-open-current').attr('href', image_url($(document).getUrlParam('shoujo'), face_name, 1))
	}
}