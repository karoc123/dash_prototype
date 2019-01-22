<?php
// include the config
require_once('config/config.php');

// include the to-be-used language, english by default. feel free to translate your project and include something else
require_once('translations/en.php');

// include the PHPMailer library
require_once('libraries/PHPMailer.php');

// load the login class
require_once('classes/Login.php');

// load the registration class
require_once('classes/Registration.php');

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process.
$login = new Login();

if(DEBUG) print_r($_POST);

// Hier die angeforderte view ausliefern
// TODO: Sicherheitscheck
if(!isset($_POST['page']) AND !isset($_GET['page'])) die("keine Variablen uebergeben");
if(isset($_POST['page'])) $page = $_POST['page']; else $page = $_GET['page'];

# Suchmuster mit ODER-Bedinnung
$suchmuster = "/\.html$|\.htm$/";

# Ersetzen des Suchmusters durch nix
$page = preg_replace($suchmuster, "", $page);

if(file_exists('views/'.$page.'.php'))
include('views/'.$page.'.php');
else echo 'There is no such page!';
?>