<?php

/**
 * Handles the user registration
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 */
class Movies
{
    /**
     * @var object $db_connection The database connection
     */
    private $db_connection            = null;
    /**
     * @var array collection of error messages
     */
    public  $errors                   = array();
    /**
     * @var array collection of success / neutral messages
     */
    public  $messages                 = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {

        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST["addmovie"])) {
			if(isset($_POST['isserie'])) $this->createNewMovie($_POST['moviedbid'], $_POST['notiz'], 1);
			else $this->createNewMovie($_POST['moviedbid'], $_POST['notiz'], 0);
        // if we have such a GET request, call the verifyNewUser() method
        } else if (isset($_GET["movieid"])) {
            //$this->verifyNewUser($_GET["id"], $_GET["verification_code"]);
			// Film ausgeben?
        }
    }

    /**
     * Checks if database connection is opened and open it if not
     */
    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                // Generate a database connection, using the PDO connector
                // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
                // Also important: We include the charset, as leaving it out seems to be a security issue:
                // @see http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers#Connecting_to_MySQL says:
                // "Adding the charset to the DSN is very important for security reasons,
                // most examples you'll see around leave it out. MAKE SURE TO INCLUDE THE CHARSET!"
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = MESSAGE_DATABASE_ERROR;
                return false;
            }
        }
    }

    /**
     * handles the entire registration process. checks all error possibilities, and creates a new user in the database if
     * everything is fine
     */
    private function createNewMovie($movie, $note, $isserie)
    {
		// Schlecht hier Ausgabe zu machen!!
		echo '<h2>Hinzugefügt: </h2><br />';
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
		if(isset($trailerLink)) echo $trailerLink;
		echo 'Tagline: ' . $movie["tagline"] . '<br />';
		echo 'release_date: ' . strtotime($movie['release_date']) . '<br />';
		echo "<img src='" . $config['images']['base_url'] . $config['images']['poster_sizes'][2] . $movie['poster_path'] . "'/><br />";
		copy($config['images']['base_url'] . $config['images']['poster_sizes'][2] . $movie['poster_path'], 'img/movies/' . $movie['poster_path']);		

		if ($this->databaseConnection()) {
			// write new users data into database
			$query_new_movies_insert = $this->db_connection->prepare('INSERT INTO movies (user_id, adding_date, themoviedb_id, poster_path, title, runtime, rating, imdb_id, trailer, note, format, tagline, release_date) 
																				VALUES(:user_id, NOW(), :themoviedb_id, :poster_path, :title, :runtime, :rating, :imdb_id, :trailer, :note, :format, :tagline, :release_date)');
			$query_new_movies_insert->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':themoviedb_id', $movie['id'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':poster_path', $movie['poster_path'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':title', $movie['title'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':runtime', $movie['runtime'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':rating', $movie['vote_average'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':imdb_id', $movie['imdb_id'], PDO::PARAM_STR);
			if(isset($trailerLink)) $query_new_movies_insert->bindValue(':trailer', $trailer, PDO::PARAM_STR);
			else $query_new_movies_insert->bindValue(':trailer', "", PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':note', htmlentities($note, ENT_QUOTES, "UTF-8"), PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':format', "DVD", PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':tagline', $movie['tagline'], PDO::PARAM_STR);
			$query_new_movies_insert->bindValue(':release_date', date("Y-m-d", strtotime($movie['release_date'])), PDO::PARAM_STR);
			$query_new_movies_insert->execute();

			// id of new movie
			$movie_id = $this->db_connection->lastInsertId();
			$this->messages[] = "Film hinzugefügt";
			
        } else {
			$this->errors[] = "Database Error";
		}
    }
	
	public function printAllMovies($user_id)
	{
		if ($this->databaseConnection()) {
			$query_movies = $this->db_connection->prepare('SELECT id, user_id, adding_date, themoviedb_id, poster_path, title, runtime, rating, imdb_id, trailer, note, format, tagline, release_date FROM movies WHERE user_id = :user_id');
			$query_movies->bindValue(':user_id', $user_id, PDO::PARAM_STR);
			$query_movies->execute();
				echo '<div class="movies">';
				while ($row = $query_movies->fetch()) {
					echo '<a data-toggle="modal" data-target="#' . $row['id'] . '"><img src="img/movies/' . $row['poster_path'] . '"></a>';
					?>				
<!-- Modal -->
<div class="modal fade" id="<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $row['id']; ?>Label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="<?php echo $row['id']; ?>Label"><?php echo $row['title']; ?></h4>
      </div>
      <div class="modal-body">
		<div class="container-fluid">
        <img class="movieImg" src="img/movies/<?php echo $row['poster_path']; ?>">
		<p class="tagline"><?php echo $row['tagline']; ?></p>
		<p class="movieInfos"><strong>Laufzeit:</strong> <?php echo $row['runtime']; ?> min.</p>
		<p class="movieInfos"><strong>Erschienen am:</strong> <?php echo date("j. M Y", strtotime($row['release_date'])); ?></p>
		<p class="movieInfos"><strong>Hinzugefügt am:</strong> <?php echo date("j. M Y", strtotime($row['adding_date'])); ?></p>
		<?php if(isset($row['trailer'])){?>
			<p class="movieInfos"><strong>Trailer:</strong> <a href="<?php echo $row['trailer']; ?>">Youtube</a></p>
		<?php } ?>
		<p class="movieInfos"><strong>Notiz:</strong> <?php echo $row['note']; ?></p>
		</div>
      </div>
    </div>
  </div>
</div>
					<?php
				}
				echo '</div>';
		} else {
			$this->errors[] = "Database Error";
		}
	}
}
