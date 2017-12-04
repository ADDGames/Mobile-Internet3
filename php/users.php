<?php
    include '../include/connection.php';

    $docenten = [];

    //docenten
	public function get_docenten_database() {
        $query = "SELECT docent.DOC_id,docent.DOC_naam,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id";
        $result = mysql_query($query);
        while($docent = mysql_fetch_array($result)) {
            array_push($docenten, new docent($docent['DOC_id'],$docent['DOC_naam'],$docent['DOC_GEB_id'],$docent['GEB_username'],$docent['GEB_naam'],$docent['GEB_voornaam'],$docent['GEB_wachtwoord'],$docent['GEB_email']));
        }
        echo json_encode($docenten);
    }
	public function add_docent($newDOC_id,$new_DOC_naam,$new_DOC_GEB_id,$new_GEB_username,$new_GEB_naam,$new_GEB_voornaam,$new_GEB_wachtwoord,$new_GEB_email) {
        $query = "INSERT INTO `gebruiker`(`GEB_id`, `GEB_username`, `GEB_naam`, `GEB_voornaam`, `GEB_wachtwoord`, `GEB_email`) VALUES ($new_DOC_GEB_id,`$new_GEB_username`,`$new_GEB_naam`,`$new_GEB_voornaam`,`$new_GEB_wachtwoord`,`$new_GEB_email`)"
        $con->query($query);
        $query = "INSERT INTO `docent`(`DOC_id`, `DOC_naam`, `DOC_gebruiker_id`) VALUES ($newDOC_id,`$new_DOC_naam`,$new_DOC_GEB_id,)"
        $con->query($query);
        $docenten = [];
        get_docenten_database();
    }
    class docent {
		private $DOC_id;
        private $DOC_naam;
        private $DOC_GEB_id;
        private $GEB_username;
        private $GEB_naam;
        private $GEB_voornaam;
        private $GEB_wachtwoord;
        private $GEB_email;
        public function __construct($newDOC_id,$new_DOC_naam,$new_DOC_GEB_id,$new_GEB_username,$new_GEB_naam,$new_GEB_voornaam,$new_GEB_wachtwoord,$new_GEB_email) {
           	$this->DOC_id = $new_DOC_id;
            $this->DOC_naam = $new_DOC_naam;
            $this->DOC_GEB_id = $new_DOC_GEB_id;
            $this->GEB_username = $new_GEB_username;
            $this->GEB_naam = $new_GEB_naam;
            $this->GEB_voornaam = $new_GEB_voornaam;
            $this->GEB_wachtwoord = $new_GEB_wachtwoord;
            $this->GEB_email = $new_GEB_email;
        }
        function get_DOC_id() {
			return $this->DOC_id;
		}
        function get_DOC_naam() {
			return $this->DOC_naam;
		}
        function get_DOC_GEB_id() {
			return $this->DOC_GEB_id;
		}
        function get_GEB_username() {
			return $this->GEB_username;
		}
        function get_GEB_naam() {
			return $this->GEB_naam;
		}
        function get_GEB_voornaam() {
			return $this->GEB_voornaam;
		}
        function get_GEB_wachtwoord() {
            return $this->GEB_wachtwoord;
        }
        function get_GEB_email() {
            return $this->GEB_email;
        }
        function set_DOC_id($new_DOC_id) {
       		$this->DOC_id = $new_DOC_id;
        }
        function set_DOC_naam($new_DOC_naam) {
            $this->DOC_naam = $new_DOC_naam;
        }
        function set_DOC_GEB_id($new_DOC_GEB_id) {
            $this->DOC_GEB_id = $new_DOC_GEB_id;
        }
        function set_GEB_username($new_GEB_username) {
            $this->GEB_username = $new_GEB_username;
        }
        function set_GEB_naam($new_GEB_naam) {
            $this->GEB_naam = $new_GEB_naam;
        }
        function set_GEB_voornaam($new_GEB_voornaam) {
            $this->GEB_voornaam = $new_GEB_voornaam;
        }
        function set_GEB_wachtwoord($new_GEB_wachtwoord) {
            $this->GEB_wachtwoord = $new_GEB_wachtwoord;
        }
        function set_GEB_email($new_GEB_email) {
            $this->GEB_email = $new_GEB_email;
        }
	}
?>
