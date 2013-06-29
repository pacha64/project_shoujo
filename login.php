<?php
require_once "config/include-all.php";
$response = "";
if(isset($_SESSION["username"]))
{
	header("Location: ".constants::site_main."/account.php");
    die();
}
if(isset($_POST["username"], $_POST["password"]))
{
	if(user_valid_password($_POST["username"], $_POST["password"]))
	{
        $remember = false;
        if (isset($_POST['remember'])) $remember = true;
		start_session($_POST["username"], user_get_name($_POST["username"]), hash_password($_POST["password"]), true);
		header("Location: ".constants::site_main."/account.php");
        die();
	}
    else
    {
        $response = "Invalid password";
    }
}
include_once "config/header-general.php";
?>
<div class="login-register-container">
	<h2>Login</h2>
    <p class="form-error"><?= $response; ?></p>
	<div class="login-form">
		<form id="form" action="" method="post">
			<div class="input-left"><span>Username:</span></div><div class="input-right"><input autocomplete="off" type="text" name="username" /></div>
			<br style="clear:both;" />
			<div class="input-left"><span>Password:</span></div><div class="input-right"><input autocomplete="off" type="password" name="password" /></div>
			<br style="clear:both;" />
			<div class="input-left"><span>Remember?</span></div><div class="input-right"><input autocomplete="off" type="checkbox" checked="checked" name="remember" /></div>
			<br style="clear:both;" />
			<div class="input-submit-holder"><a class="submit-button">Submit</a></div>
		</form>
	</div>
</div>
<?php
include_once "config/footer-general.php";
?>
<script>
    $(document).ready(function(){
        $(document).keypress(function(e) {
            if(e.which == 13) {
                $('#form').submit()
            }
        });
    })
    $('.submit-button').click(function()
    {
        $('#form').submit()
    })
</script>