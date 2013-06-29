<?php
class constants
{
	const mysql_server = "localhost";
	const mysql_username = "pacha";
	const mysql_password = "asdasd123";
	const mysql_database = "pacha_shoujo";
	
	const mysql_password_salt = "@#^@#F23";
	
	const site_main = "http://localhost";
	const site_images = "http://localhost";
	const site_forum = "http://forum.projectshoujo.com/";
	
	const days_remember = 7;
	const seconds_error_page_redirect = 2;
	
	const user_name_max_lenght = 16;
	const player_name_max_lenght = 25;
	const shoujo_name_max_lenght = 25;
	
	const shoujo_default_description = "No description";	
	const shoujo_default_name = "default";	
	const shoujo_description_max_lenght = 250;
	
	const shoujo_box_big = 0;
}
class constants_playing_state
{
    const error = '-1';
    const ok = '0';
    const not_logged_in = '1';
    const not_playing_session = '2';
    const shoujo_doesnt_exist = '3';
    const already_playing = '4';
    const already_playing_with_an_activity = '11';
    const activity_already_done = '12';
    const invalid_activity = '13';
    const not_playing_with_activity = '14';
    const not_in_time = '15';
}
class constants_variables
{
	const max_lenght_name = 16;
	const max_lenght_value = 32;
	const current_username = "PLAYER";
	const current_time = "TIME";
	const current_day_number = "DAY_NUMBER";
	const current_day = "DAY";
	const current_month = "MONTH_NUMBER";
	const current_month_number = "MONTH";
	const current_year = "YEAR";
	const shoujo_name = "SHOUJO_NAME";
	const submenu_name = "SUBMENU_NAME";
}
class constants_activity
{
    const activity_alias_max_length = 32;
	const conversation_max_characters = 256;
	const option_max_characters = 64;
	const countdown_max_seconds = 7200;
    const status_max_value = 250;
	const type_text = 0;
	const type_countdown = 1;
	const type_status = 2;
	const type_option = 3;
    const source_reserved = 0; // start playing, birthday of player, etc
    const source_option = 1;
    const source_event = 2;
}
class constants_status
{
	const max_points = 250;
	const name_length = 32;
	const decrease_none = 0;
	const decrease_hourly = 1;
	const decrease_day = 2;
}
class constants_images
{
	const max_characters_face = 64;
	const max_characters_background = 64;
    const avatar_max_width = 256;
    const avatar_max_height = 256;
    const face_max_width = 3000;
    const face_max_height = 1600;
    const background_max_width = 3000;
    const background_max_height = 1600;
}
class constants_menu
{
    const max_length_name = 35;
	const option_submenu = 0;
	const option_activity = 1;
	const option_event = 2;
	const option_status = 3;
}
class constants_events
{
	const name_max_length = 32;
	const description_max_length = 256;
}
define("PRIVILEGE_MANAGE_ADD",0);
define("PRIVILEGE_MANAGE_DELETE",1); 
define("PRIVILEGE_MANAGE_EDIT",2);
define("PRIVILEGE_MANAGE_ADD_MANAGER",3);
define("PRIVILEGE_MANAGE_DELETE_MANAGER",4);
define("PRIVILEGE_MANAGE_CONFIGURATION",5);
?>