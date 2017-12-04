<?php
    include '../include/connection.php';

    $query = "SELECT * FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id";
	$result = mysql_query($query);
    class docent {
		private $DOC_id;
        private $DOC_naam;
        private $DOC_gebruiker_id;
        private $GEB_id;
        private $GEB_username;
        private $GEB_naam;
        private $GEB_voornaam;
        private $GEB_wachtwoord;
        private $GEB_email;
	}
?>
