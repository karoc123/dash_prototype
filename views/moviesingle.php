<?php
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
if(isset($_GET['id'])) $movies = $movie->printAllMovies(intval($_GET['id']));
else if(isset($_SESSION['user_id'])) $movies = $movie->printAllMovies($_SESSION['user_id']);
else echo "Keine Filme hier, bitte gehen sie weiter";

if ($login->isUserLoggedIn() == true) {
	if(isset($_GET['id'])){
		echo "<h4 class=\"shareLink\">SHARE: <a href=\"" . BASEURL . "index.php?site=moviesshow&id=" . $_GET['id'] . "\"> Direktlink</a></h4>";
	} else {
		echo "<h4 class=\"shareLink\">SHARE: <a href=\"" . BASEURL . "index.php?site=moviesshow&id=" . $_SESSION['user_id'] . "\"> Direktlink</a></h4>";
	}
}
?>