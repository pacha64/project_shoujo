<?php
require_once 'config/include-all.php';
$response = "";
if (isset($_SESSION['username']))
{
	header("Location: ".constants::site_main."/account.php");
	die();
}
if (isset($_POST["username"], $_POST["password-1"], $_POST["password-2"], $_POST["email"]))
{
	if (user_exists($_POST["username"]))
	{
		$response = "Username ".$_POST["username"]." is already taken";
	}
	else
	{
        if($_POST["password-1"] != $_POST["password-2"])
        {
            $response = "Password doesn't match";
        }
		if (user_create($_POST["username"], $_POST["password-1"], $_POST["email"]))
		{
            $remember = false;
            if (isset($_POST['remember']) && $_POST['remember'] == 1)
            {
                $remember = true;
            }
			start_session(
				$_POST["username"],
				$_POST["username"],
				md5(hash_password($password)),
				$remember
			);
			header("Location: ".constants::site_main."/account.php");
            die();
		}
		else
		{
			$response = "MySQL database error";
		}
	}
}
include_once "config/header-general.php";
?>
<div class="login-register-container">
	<h2>Register</h2>
    <p class="form-error"><?= $response; ?></p>
	<form id="form" action="" method="post">
		<div class="input-left"><span>Username:</span></div><div class="input-right"><input autocomplete="off" type="text" name="username" /></div>
		<br style="clear:both;" />
		<div class="input-left"><span>Password:</span></div><div class="input-right"><input autocomplete="off" type="password" name="password-1" /></div>
		<br style="clear:both;" />
		<div class="input-left"><span>Repeat password:</span></div><div class="input-right"><input autocomplete="off" type="password" name="password-2" /></div>
		<br style="clear:both;" />
		<div class="input-left"><span>Email:</span></div><div class="input-right"><input autocomplete="off" type="text" name="email" /></div>
        <div class="input-left"><span>Remember?</span></div><div class="input-right"><input autocomplete="off" type="checkbox" checked="checked" name="remember" /></div>
		<br style="clear:both;" />
		<div class="input-submit-holder"><button action="submit">Submit</button></div>
	</form>
</div>
<?php
require_once 'config/footer-general.php';
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