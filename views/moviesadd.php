<?php
if ($login->isUserLoggedIn() == true) {

// load the movies class
require_once('classes/Movies.php');
$movie = new Movies();

// Fehler ausgeben
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
?>
<h1>Film hinzufügen</h1>
<form role="form" method="post" action="load.php?page=moviesadd" name="movieform">
    <label for="moviedbid">MovieDB-ID</label>
    <input id="moviedbid" class="form-control" type="text" name="moviedbid" required />
    <label for="notiz">Notiz</label>
    <input id="notiz" class="form-control" type="text" name="notiz" autocomplete="off" />
	<div class="checkbox"><label for="isserie"><input type="checkbox" id="isserie" name="isserie" value="1" /> Serie? </label></div>
	<input type="hidden" name="addmovie" value="addmovie">
    <input type="submit" class="btn btn-default" name="addmovie" value="Hinzufügen" />
</form>
<?php
} else {
	die();
}