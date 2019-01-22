<?php
if ($login->isUserLoggedIn() == true) {
?>
<form action="" method="post">
Movie DB ID
<input name="movie" type="text" size="50" maxlength="1000">
Typ:
<input type="radio" name="type" value="film" checked>
Film
<input type="radio" name="type" value="serie">
Serie (disabled)
Notiz
<textarea name="note" cols="50" rows="10" maxlength="1000"></textarea>
<input type="submit" value=" Hinzufügen ">
<input type="reset" value=" Abbrechen">
</form>

<?php //neuen Film hinzufügen

	if (isset($_POST["movie"])) {

		$note = "";

		if (isset($_POST["note"])) {

			$note = $_POST["note"];

		}

		echo '<hr /><strong>Hinzugefügt: </strong><br />';

		$movie = $_POST["movie"];

		$configResponse = file_get_contents('http://api.themoviedb.org/3/configuration?api_key=75eec553ba2ea9de16aef1947c833909');

		$movieResponse = file_get_contents('http://api.themoviedb.org/3/movie/' . $movie . '?api_key=75eec553ba2ea9de16aef1947c833909');



		//search for trailer

		$trailer = '';

		$movieTrailerResponse = file_get_contents('http://api.themoviedb.org/3/movie/' . $movie . '/videos?api_key=75eec553ba2ea9de16aef1947c833909');

		$movieTrailer = json_decode($movieTrailerResponse, true);

		if (isset($movieTrailer['results']['0'])) {

			if ($movieTrailer['results']['0']['type'] == 'Trailer' && $movieTrailer['results']['0']['site'] == 'YouTube') {

				$trailerLink = '<a href="https://www.youtube.com/watch?v=' . $movieTrailer['results']['0']['key'] . '">Trailer (YT)</a><br />';

				$trailer = 'https://www.youtube.com/watch?v=' . $movieTrailer['results']['0']['key'];

			}

		}

		$config = json_decode($configResponse, true);

		$movie = json_decode($movieResponse, true);

		echo 'Titel: ' . $movie["title"] . '<br />';

		echo 'Laufzeit: ' . $movie["runtime"] . '<br />';

		echo 'Rating: ' . $movie["vote_average"] . '<br />';

		echo 'ID: ' . $movie["id"] . '<br />';

		echo 'IMDB-ID: ' . $movie["imdb_id"] . '<br />';

		echo $trailerLink;

		echo "<img src='" . $config['images']['base_url'] . $config['images']['poster_sizes'][2] . $movie['poster_path'] . "'/><br />";

		copy($config['images']['base_url'] . $config['images']['poster_sizes'][2] . $movie['poster_path'], 'img/movies/' . $movie['poster_path']);



		//Film einfügen

		$stmt = $pdo -> prepare('INSERT INTO movies (themoviedb_id, poster_path, title, runtime, rating, imdb_id, trailer, note) 

								VALUES (:themoviedb_id, :poster_path, :title, :runtime, :rating, :imdb_id, :trailer, :note)');

		$array = array(':themoviedb_id' => $movie["id"], ':poster_path' => $movie["poster_path"], ':title' => $movie["title"], ':runtime' => $movie["runtime"], ':rating' => $movie["vote_average"], ':imdb_id' => $movie["imdb_id"], ':trailer' => $trailer, ':note' => htmlentities($note, ENT_QUOTES, "UTF-8"));

		if ($stmt -> execute($array)) {

			echo ' -> Datenbank erfolgreich!';

		} else {

			echo "\nPDOStatement::errorInfo():\n";

			$arr = $stmt -> errorInfo();

			print_r($arr);

			echo 'fehler';

		}

	}



	} else {

	echo 'nicht eingeloggt';

	}
<?php
} else {
	die();
}