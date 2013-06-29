<?php
@session_start();
require_once 'config/include-all.php';
if(!isset($_SESSION["username"]))
{
	header("Location: ".constants::site_main."/login.php");
	die();
}
include_once "config/header-general.php";
$username = $_SESSION["username"];
$name = $_SESSION["player_name"];
$password_change_response = "";
$new_shoujo_response = "";
$name_change_response = "";
if(isset($_POST["new-password-1"], $_POST["new-password-2"]))
{
	if($_POST["new-password-1"]==$_POST["new-password-2"])
	{
		if(user_change_password($username, $_POST["new-password-1"]))
		{
			$password_change_response = "Password changed successfully";
		}
		else
		{
			$password_change_response = "MySQL database error";
		}
	}
	else
	{
		$password_change_response = "Passwords don't match";
	}
}
if(isset($_POST["new-name"]))
{
    $name = $_POST["new-name"];
    if (!valid_player_name($name))
    {
        $name_change_response = "Invalid name, only up to ".constants::player_name_max_lenght." characters";
    }
    else
    {
        change_name($username, $name);
        $name_change_response = "Changed successfully";
    }
}
if(isset($_POST["new-shoujo-name"]) && true)
{
	$shoujo_new_name = $_POST["new-shoujo-name"];
	if (!strlen($shoujo_new_name) > constants::shoujo_name_max_lenght)
	{
		$new_shoujo_response = "Name too long (only up to ".constants::shoujo_name_max_lenght." characters)";
	}
	else
	{
		if(shoujo_create($username, $shoujo_new_name))
		{
			$new_shoujo_response = "Shoujo $shoujo_new_name created successfully";
		}
		else
		{
			$new_shoujo_response = "MySQL database error";
		}
	}
}
if(isset($_POST["change-config"], $_POST["shoujo-id"], $_POST["name"], $_POST["description"], $_POST["face-url"]))
{
	if(shoujo_user_has_priviledge($username, $_POST["shoujo-id"], PRIVILEDGE_MANAGE_CONFIGURATION))
	{
		if(shoujo_valid_name($_POST["name"]))
		{
			shoujo_change_name($_POST["shoujo-id"], $_POST["name"]);
		}
		else
		{
		}
		if(shoujo_valid_description($_POST["description"]))
		{
			shoujo_change_description($_POST["shoujo-id"], $_POST["description"]);
		}
	}
}
?>
<div id="account-holder">
	<h1>Hello, <?= $name ?></h1>
	<div class="sub-content">
		<h2>Account management</h2>
		<div class="sub-content">
			<h3>Change password</h3>
            <div class="sub-content">
                <p class="form-error-account"><?= $password_change_response; ?></p>
                <form action="" method="POST">
                    <p>New password:</p>
                    <input type="password" name="new-password-1" />
                    <p>Repeat password:</p>
                    <input type="password" name="new-password-2" />
                    <br /><a class="submit-button account-button">Submit</a>
                </form>
            </div>
			<h3>Change name</h3>
            <div class="sub-content">
                <p class="form-error-account"><?= $name_change_response; ?></p>
                <p>This is not your account name, it is the name the characters you play with will call you</p>
                <form action="" method="POST">
                    <p>New name:</p>
                    <input maxlength="<?= constants::player_name_max_lenght; ?>" type="text" name="new-name" />
                    <br /><a class="submit-button account-button">Submit</a>
                </form>
            </div>
		</div>
	</div>
	<div class="sub-content">
		<h2>Shoujo management</h2>
		<div class="sub-content">
			<h3>Currently playing</h3>
<?php
$currently_playing = get_all_currently_playing($username);
if(count($currently_playing) == 0):
?>
            <p>You aren't playing with anyone</p>
        </div>
    </div>
<?php
else:
    ?>
        </div>
    </div>
<?php
	foreach($currently_playing as $information):
        generate_shoujo_box($information['id'], constants::shoujo_box_big);
	endforeach;
endif;
?>
<?php require_once 'config/footer-general.php' ?>
<script type="text/javascript">
$(document).ready(function(e) {
	$(".shoujo-configuration").hide()
	$(".shoujo-configuration-name").click(function(e) {
		$(this).parent().parent().nextAll(".shoujo-configuration:first").toggle()
	})
    $('.submit-button').click(function()
    {
        $(this).closest('form').first().submit()
    })
});
</script>