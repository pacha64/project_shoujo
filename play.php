<?php
require_once "config/include-all.php";
?><!doctype html>
<html>
  <head>
    <title></title>
    <meta charset="utf-8" />
	<link rel="stylesheet" href="css/play.css" type="text/css" />
	<script src="http://code.jquery.com/jquery-1.9.0.min.js"></script>
	<script src="javascript/url_params.js"></script>
	<script src="javascript/object_manager.js"></script>
	<script src="javascript/ajax.js"></script>
	<script src="javascript/play.js"></script>
	<script>
// PHP-generated variables
var PLAYER_NAME = '<?= $_SESSION['username'] ?>'
var AJAX_STATE_OK = '<?= constants_playing_state::ok ?>'
var AJAX_STATE_NOT_LOGGED_IN = '<?= constants_playing_state::not_logged_in ?>'
var AJAX_STATE_NOT_PLAYING_SESSION = '<?= constants_playing_state::not_playing_session ?>'
var AJAX_STATE_SHOUJO_DOESNT_EXIST = '<?= constants_playing_state::shoujo_doesnt_exist ?>'
var AJAX_STATE_ALREADY_PLAYING_WITH_AN_ACTIVITY = '<?= constants_playing_state::already_playing_with_an_activity ?>'
var AJAX_STATE_ACTIVITY_ALREADY_DONE = '<?= constants_playing_state::activity_already_done ?>'
var AJAX_STATE_INVALID_ACTIVITY = '<?= constants_playing_state::invalid_activity ?>'
var AJAX_STATE_NOT_PLAYING_WITH_ACTIVITY = '<?= constants_playing_state::not_playing_with_activity ?>'

$(document).ready(function()
	{
		preinit()
		load()
	}
)
	</script>
  </head>
  <body>
    <div id="error-container">
        <h1>Error</h1>
        <p id="error-description"></p>
    </div>
	<div id="loading-container">
		<h1>Loading...</h1>
		<div id="loading-progress">
			<p id="loading-shoujo-information">Load shoujo information</p>
			<p id="loading-shoujo-state">Load shoujo state</p>
			<p id="loading-images">Loading images</p>
		</div>
		<p id="loading-play-button">Please wait</p>
	</div>
	<div id="play-container">
		<div id="sound-effects" style="display:none;">
			<audio id="click-sound-effect">
			<source src="/static/click.ogg" type="audio/ogg" />
		   </audio>
		</div>
		<div id="image-container">
			<div id="background-container">
			</div>
			<div id="face-container">
			</div>
		</div>
		<div id="play-container">
			<div id="play-menu">
				<div id="play-menu-4-options" class="play-options-container">
					<div class="option-button-4-style"><span class="option-button-1"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-3"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-2"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-4"><a></a></span></div>
				</div>
				<!--<div id="play-menu-6-options" class="play-options-container">
					<div class="option-button-4-style"><span class="option-button-1"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-3"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-5"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-2"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-4"><a></a></span></div>
					<div class="option-button-4-style"><span class="option-button-6"><a></a></span></div>
				</div>-->
				<div id="play-text">
					<p><span id="conversation-text"></span></p>
				</div>
				<div id="play-countdown">
					<p>Time left for this activity to finish:</p>
					<p id="countdown-seconds-left"></p>
				</div>
				<div id="play-options">
					<ol>
						<li><a id="option-1"></a></li>
						<li><a id="option-2"></a></li>
						<li><a id="option-3"></a></li>
						<li><a id="option-4"></a></li>
						<li><a id="option-5"></a></li>
						<li><a id="option-6"></a></li>
					</ol>
				</div>
				<div id="play-buttons">
					<a id="button-1" class="button-general"></a>
					<a id="button-2" class="button-general"></a>
					<a id="button-3" class="button-general"></a>
				</div>
				<div id="play-change-status">
					<p>Status have changed</p>
					<p id="status-changed"></p>
				</div>
			</div>
			<div id="play-event-container" class="status-event-container">
				<div id="play-event" class="play-event-status">
					<a class="event-status-back" id="event-back-button">back</a>
					<h2>Events</h2>
					<div id="play-event-buttons">
						<a id="event-change-today">Today</a>
						<a id="event-change-week">This week</a>
						<a id="event-change-month">Month</a>
					</div>
					<div id="play-event-list-container" class="status-event-list-container">
					</div>
				</div>
			</div>
			<div id="play-status-container" class="status-event-container">
				<div id="play-status" class="play-event-status">
					<a class="event-status-back" id="status-back-button">back</a>
					<h2>Status</h2>
					<div id="play-status-list-container" class="status-event-list-container">
					</div>
				</div>
			</div>
		</div>
	</div>
    <div id="settings-container">
        <a href="<?= constants::site_main ?>">home</a>
    </div>
  </body>
</html>