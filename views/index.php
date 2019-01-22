<?php
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
		?>
        <div class="alert alert-error">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<strong>Fehler! </strong><?php echo $error; ?>
		</div>
		<?php
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
		?>
        <div class="alert alert-success">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<strong>Erfolg! </strong><?php echo $message; ?>
		</div>
		<?php
        }
    }
}
if ($login->isUserLoggedIn() == true) {
// if you need the user's information, just put them into the $_SESSION variable and output them here
echo WORDING_YOU_ARE_LOGGED_IN_AS . $_SESSION['user_name'] . "<br />";
//echo WORDING_PROFILE_PICTURE . '<br/><img src="' . $login->user_gravatar_image_url . '" />;
echo WORDING_PROFILE_PICTURE . '<br/>' . $login->user_gravatar_image_tag;
// show potential errors / feedback (from login object)
?>

<div>
    <a href="index.php?logout" class="btn btn-default"><?php echo WORDING_LOGOUT; ?></a>
    <a href="edit" class="dynamic btn btn-default"><?php echo WORDING_EDIT_USER_DATA; ?></a>
</div>
<?php
} else 
{
?>
<h1>Login</h1>
<form method="post" action="load.php?page=index" name="loginform">
    <label for="user_name"><?php echo WORDING_USERNAME; ?></label>
    <input id="user_name" type="text" name="user_name" required />
    <label for="user_password"><?php echo WORDING_PASSWORD; ?></label>
    <input id="user_password" type="password" name="user_password" autocomplete="off" required />
    <input type="checkbox" id="user_rememberme" name="user_rememberme" value="1" />
    <label for="user_rememberme"><?php echo WORDING_REMEMBER_ME; ?></label>
	<input type="hidden" name="login" value="login">
    <input type="submit" name="login" value="<?php echo WORDING_LOGIN; ?>" />
</form>

<a href="register" class="dynamic btn btn-default"><?php echo WORDING_REGISTER_NEW_ACCOUNT; ?></a>
<a href="password_reset" class="dynamic btn btn-default"><?php echo WORDING_FORGOT_MY_PASSWORD; ?></a>
<?php
}