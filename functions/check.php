<?php
//require ("../config/db.php");
	//$CHECK = new CHECK();

	
	class CHECK {
		
		private $mysql;
		private $mm;
		
		public function __construct() {
			$this->mysql = new SQL();
			$this->mm = new MM();
		}



		public function CHECK() {
			
			if ( isset($_GET["p"]) )
				$page = "uvod";
			else
				$page = $_GET["p"];
			
			$this->mysql->query("SELECT * FROM pages WHERE name = '$page'");
			$r = $this->mysql->result();

			if ( !$r )
				header("Location: /404"); 
			else {
				if ( $r["page"] == 50 ) {
					$this->mysql->query("SELECT lang FROM user_lang WHERE ip = '".$this->mm->ip()."'");
					$lang = $this->mysql->result();
				}
			}



			/*$this->mysql->query("SELECT lang FROM pages WHERE name = '$page'");
			$r = $this->mysql->result();*/
			if ( isset($lang) )
				$language = $lang["lang"];
			else 
				$language = $r["lang"];




			
			$this->mysql->query("DELETE FROM user_lang WHERE ip = '".$_SERVER["REMOTE_ADDR"]."'");
			$this->mysql->query("INSERT INTO user_lang VALUES('".$_SERVER["REMOTE_ADDR"]."', '$language')");
			
			setcookie("lang", $language, strtotime("+1 months"), '/', ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false);

			return $r;
		}






		public function CHECK_PAGE() {
			
			//$page = $_GET["p"];

			if ( isset($_GET["p"]) )
				$page = "uvod";
			else
				$page = $_GET["p"];
			
			$this->mysql->query("SELECT page, name FROM pages WHERE name = '$page'");
			$r = $this->mysql->result();

			if ( !$r )
				header("Location: /404");

			return array( "page" => $r["page"], "name" => $r["name"] );
		}

		public function CHECK_SUBPAGE() {
			global $mysql;
			
			$page = $_GET["sp"];

			$this->mysql->query("SELECT page, name FROM pages WHERE name = '$page'");
			$r = $this->mysql->result();

			if ( !$r )
				header("Location: /404");

			return array( "page" => $r["page"], "name" => $r["name"] );
		}

		public function CHECK_LANG($page) {
			
			$this->mysql->query("SELECT lang FROM pages WHERE name = '$page'");
			$r = $this->mysql->result();

			setcookie("lang", $r["lang"], strtotime("+1 months"), '/', ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false);

			//$this->lang = $r["lang"];
			return $r["lang"];
		}


		public function PAGEDATA($PID, $lang) {
			
			$this->mysql->query("SELECT * FROM pages WHERE page = '$PID' AND lang = '$lang'");
			$r = $this->mysql->result();

			return $r;
		}

		public function lang_afix($lang) {
			
			if ( !empty($lang) ) {
				$this->mysql->query("DELETE FROM user_lang WHERE ip = '".$_SERVER["REMOTE_ADDR"]."'");
				$this->mysql->query("INSERT INTO user_lang VALUES('".$_SERVER["REMOTE_ADDR"]."', '$lang')");
			}
		}

		public function p404() {
			
			$this->mysql->query("SELECT lang FROM user_lang WHERE ip = '".$_SERVER["REMOTE_ADDR"]."'");
			$r = $this->mysql->result();

			return $r["lang"];
		}

	}