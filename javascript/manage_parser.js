function generate_xml_information(basic_information)
{
	var $root = $('<shoujo />')
	$root.append
	(
		$('<information>').append(
			$('<name>').text(basic_information.information.name),
			$('<description>').text(basic_information.information.description),
			$('<id>').text(basic_information.information.id)
		)
	)
	$root.append
	(
		$('<status_collection />').append(function()
		{
			var length = basic_information.status.length
			for(var loop = 0; loop < length; loop++)
			{
				var name = basic_information.status[loop]
				$(this).append($('<name />').text(name))
			}
		})
	)
	$root.append
	(
		$('<activities />').append(function()
		{
			var length = basic_information.activity_array.length
			for(var loop = 0; loop < length; loop++)
			{
				var array = basic_information.activity_array[loop]
				if (typeof array == 'undefined') continue
				$activity = $('<activity_array />')
				$activity.attr('id', loop)
				var length_activity = array.length
				for(var loop2 = 0; loop2 < length_activity; loop2++)
				{
					$activity.append(function()
					{
						var $to_append = $('<activity />')
						$to_append.attr('number', loop2)
						$to_append.append(function()
						{
							if ('face' in array[loop2])
							{
								$to_append.append($('<face />').text(array[loop2].face))
							}
							if ('background' in array[loop2])
							{
								$to_append.append($('<background />').text(array[loop2].background))
							}
							var type = array[loop2].type
							$to_append.append($('<type />').text(type))
							switch(type)
							{
							case ACTIVITY_TYPE_TEXT:
								$to_append.append($('<text />').text(array[loop2].text))
								break
							case ACTIVITY_TYPE_COUNTDOWN:
								$to_append.append($('<time />').text(array[loop2].time))
								break
							case ACTIVITY_TYPE_STATUS:
								$to_append.append($('<status />').text(array[loop2].status))
								$to_append.append($('<value />').text(array[loop2].value))
								break
							case ACTIVITY_TYPE_OPTION:
								var length3 = array[loop2].options.length
								for(var loop3 = 0; loop3 < length3; loop3++)
								{
									if (typeof array[loop2].options[loop3] == 'undefined') continue
									var $option = $('<option />')
									$option.attr('number', loop3+1)
									$option.append
									(
										$('<text />').text(array[loop2].options[loop3].text),
										$('<activity_id />').text(array[loop2].options[loop3].activity_id )
									)
									$to_append.append($option)
								}
								break
							}
						})
						$(this).append($to_append)
					}
					)
				}
				$(this).append($activity)
			}
		})
	)
	$root.append
	(
		$('<events />').append(function()
		{
			var length = basic_information.events.length
			for(var loop = 0; loop < length; loop++)
			{
				var event = basic_information.events[loop]
				var $event = $('<event />')
				var date_helper_start = event.date_start.year +'-'+ event.date_start.month +'-'+ event.date_start.day
				var date_helper_finish = event.date_finish.year +'-'+ event.date_finish.month +'-'+ event.date_finish.day
				$event.append
				(
					$('<name />').text(event.name),
					$('<description />').text(event.description),
					$('<activity_id />').text(event.activity_id),
					$('<date_start />').text(date_helper_start),
					$('<date_finish />').text(date_helper_finish),
					$('<time />').text(event.time)
				)
				$(this).append($event)
			}
		})
	)
	$root.append(recursive_option_add(basic_information.options))
	$root.append
	(
		$('<images />').append
		(
			$('<faces />').append(function()
			{
				var length = basic_information.images.faces.length
				var faces = basic_information.images.faces
				for(var loop = 0; loop < length; loop++)
				{
					$(this).append($('<face />').text(faces[loop]))
				}
			}),
			$('<backgrounds />').append(function()
			{
				var length = basic_information.images.backgrounds.length
				var backgrounds = basic_information.images.backgrounds
				for(var loop = 0; loop < length; loop++)
				{
					$(this).append($('<background />').text(backgrounds[loop]))
				}
			})
		)
	)
	$root.append
	(
		$('<variables />').append(function()
		{
			for(var name in basic_information.variables_defined)
			$(this).append($('<variable />').append($('<name />').text(name), $('<value />').text(basic_information.variables_defined[name])))
		})
	)
	return $('<dummy>').append($root.clone()).remove().html()
}
function recursive_option_add(option_array)
{
	var to_return = $('<options />')
	var length = option_array.length
	for(var loop = 0; loop < length; loop++)
	{
		var to_append = $('<option />')
		var opt = option_array[loop]
		if (opt.deleted) continue
		switch(opt.type)
		{	
		case OPTION_SUBOPTIONS:
			to_append.append
			(
				$('<text />').text(opt.text),
				$('<number />').text(opt.number),
				$('<time />').text(opt.time),
				$('<type />').text(opt.type)
			)
			to_append.append(recursive_option_add(opt.options))
			break
		case OPTION_ACTIVITY:
			to_append.append
			(
				$('<text />').text(opt.text),
				$('<number />').text(opt.number),
				$('<time />').text(opt.time),
				$('<type />').text(opt.type),
				$('<activity_id />').text(opt.activity_id)
			)
			break
		case OPTION_EVENT:
		case OPTION_STATUS:
			to_append.append
			(
				$('<text />').text(opt.text),
				$('<number />').text(opt.number),
				$('<time />').text(opt.time),
				$('<type />').text(opt.type)
			)
			break
		}
		to_return.append(to_append)
	}
	return to_return
}