
    </div> <!-- /container -->
<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/users/353054&amp;color=00cc11&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
</div>
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script>
		//Ajax fuer Links
		function makeDynamic() {
			$("body").on("click", ".dynamic", function(event) {
				event.preventDefault(); // stop the browser from following the link
				var url = $(this).attr('href');
				var url = url.replace(".html", "");
				$('#content').load("load.php", {"page": url}); // load the html response into a DOM element
				window.history.pushState('object', url+".html", url+".html");
				$("#content").trigger('create');
			});
	
			//Ajax fuer formulare
			$("body").on("submit", "form", (function(event) {
				// Das eigentliche Absenden verhindern
				event.preventDefault();
				
				// Das sendende Formular und die Metadaten bestimmen
				var form = $(this); // Dieser Zeiger $(this) oder $("form"), falls die ID form im HTML exisitiert
				var action = form.attr("action"), // attr() kann enweder den aktuellen Inhalt des gennanten Attributs auslesen, oder setzt ein neuen Wert, falls ein zweiter Parameter gegeben ist
					method = form.attr("method"),
					data   = form.serializeArray(); // baut die Daten zu einem String nach dem Muster vorname=max&nachname=Müller&alter=42 ... zusammen
					
				// Der eigentliche AJAX Aufruf
				$.ajax({
					url : action,
					type : method,
					data : data
				}).done(function (data) {
					$('#content').html(data).trigger('create');
					$("body").trigger("create");
				}).fail(function() {
					// Bei Fehler
					alert("Fehler!");
				}).always(function() {
					// Immer
				});
			}));
		}
		$(document).ready(function() {
			makeDynamic();
		});
		
	</script>
</body>
</html>
