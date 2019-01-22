<?php

// create the registration object. when this object is created, it will do all registration stuff automatically
// so this single line handles the entire registration process.
$registration = new Registration();

// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
            echo "<div class=\"bg-danger\">$error</div>";
        }
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo $message;
        }
    }
}
?>

<!-- show registration form, but only if we didn't submit already -->
<?php if (!$registration->registration_successful && !$registration->verification_successful) { ?>
<form method="post" action="load.php?page=register" name="registerform">
    <label for="user_name"><?php echo WORDING_REGISTRATION_USERNAME; ?></label>
    <input id="user_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />

    <label for="user_email"><?php echo WORDING_REGISTRATION_EMAIL; ?></label>
    <input id="user_email" type="email" name="user_email" required />

    <label for="user_password_new"><?php echo WORDING_REGISTRATION_PASSWORD; ?></label>
    <input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />

    <label for="user_password_repeat"><?php echo WORDING_REGISTRATION_PASSWORD_REPEAT; ?></label>
    <input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />

    <img src="tools/showCaptcha.php" alt="captcha" />

    <label><?php echo WORDING_REGISTRATION_CAPTCHA; ?></label>
    <input type="text" name="captcha" required />

	<input type="hidden" name="register" value="register">
    <input type="submit" class="btn btn-default" name="register" value="<?php echo WORDING_REGISTER; ?>" />
</form>
<?php } ?>

    <a href="index" class="dynamic btn btn-default"><?php echo WORDING_BACK_TO_LOGIN; ?></a>