<?php
	$CHR = new CHR();


	class CHR {

		protected static $instance;
		protected $mysql;
		//public $PDD;
		//public $PDI;

		
		public $PD;
		public $LANG;

		public function __construct() {
			$this->mysql = SQL::init();
			
			//$this->CHECK = $this->CHECK_PAGE();
			//$this->LANG = $this->CHECK_LANG( $this->CHECK->name );

			$this->PD = $this->PAGEDATA_DEFAULT();

			$this->LANG = $this->CHECK_LANG( $this->PD->name );
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new CHR();
			}

			return self::$instance;
		}

		public function __call($name, $args) {
			if( method_exists($this->connection, $name) ) {
				return call_user_func_array(array($this->connection, $name), $args);
			} else {
				trigger_error('Unknown Method ' . $name . '()', E_USER_WARNING);
				return false;
			}
		}

		public function CHECK_SUBPAGE() {

			$page = isset($_GET["sp"]) ? $_GET["p"]."/".$_GET["sp"] : $_GET["p"];

			$stmt = $this->mysql->query("SELECT * FROM pages WHERE name = '$page'");
			
			return $stmt->fetch_object();

			//else
				//header("Location: /404");
		}


		public function CHECK_LANG($page) {
			
			
			$stmt = $this->mysql->query("SELECT lang FROM pages WHERE name = '$page'");
			$r = $stmt->fetch_object();

			//setcookie("lang", $r->lang, strtotime("+2 months"), "/");

			return $r->lang;
		}


		public function PAGEDATA($PID, $lang) {
			
			$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = '$PID' AND lang = '$lang'");
			$r = $stmt->fetch_assoc();

			return $r;
		}


		public function PAGEDATA_DEFAULT() {

			if ( isset($_GET["p"]) && isset($_GET["sp"]))
				$page = $_GET["p"]."/".$_GET["sp"];
			else {
				$page = isset($_GET["p"]) ? $_GET["p"] : "";
			}

			if ( !$page ) $page = DEFAULT_PAGE;


			$stmt1 = $this->mysql->query("SELECT * FROM pages WHERE name = '$page'");

			if ( !$r = $stmt1->fetch_object() ) {

				$stmt2 = $this->mysql->query("SELECT * FROM navody WHERE url = '$page'");
				if ( $stmt2->fetch_object() ) {
					$stmt3 = $this->mysql->query("SELECT * FROM pages WHERE page = 16");


					$r = $stmt3->fetch_object();
				}

				if ( !$r ) {

					$stmt4 = $this->mysql->query("SELECT * FROM kategorie WHERE sk = '$page'");
					if ( $stmt4->fetch_object() ) {
						$stmt5 = $this->mysql->query("SELECT * FROM pages WHERE page = 5");

						$r = $stmt5->fetch_object();
					}
				}
			}
			
			if ( $r )
				return $r;
			else
				header("Location: /404");
		}

		public function PAGEDATA_TARGET($pageID = "", $r ="") {
			
			if ( isset($pageID) )
				$select = "page = ".$pageID;
			else
				$select = "name = '".$this->PD->name."'";

			$stmt = $this->mysql->query("SELECT * FROM pages WHERE ".$select." AND lang = '".$this->LANG."'");
			
			return $stmt->fetch_object();
		}

		public function PAGEDATA_INFO($PID, $lang) {
			//$page = isset($_GET["p"]) ? $_GET["p"] : "";

			//if ( !$page ) $page = DEFAULT_PAGE;

			$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = '$PID' AND lang = '$lang'");
			
			return $stmt->fetch_object();
		}

		public function ITEM_INFO($table, $identificator, $item) {

			$stmt = $this->mysql->query("SELECT * FROM $table WHERE $identificator = '$item'");
			
			return $stmt->fetch_object();
		}

		public function ITEM_ID($url) {
			$r = explode("__", $url);

			if ( is_array($r))
				return $r[0];
			else
				return $url;
		}


		public function USERDATA( $data = "" ) {
			if ( $data ) {
				$stmt = $this->mysql->query("SELECT * FROM uzivatelia WHERE userID = '".$data."'");
				
				if ( $r = $stmt->fetch_assoc() )
					return $r;
				else
					return false;
			} else {

				if ( $this->online() ) {
					$stmt = $this->mysql->query("SELECT user FROM online WHERE token = '".$this->online()."'");
					$user = $stmt->fetch_assoc();

					$stmt = $this->mysql->query("SELECT * FROM uzivatelia WHERE userID = '".$user["user"]."'");
					

					//$shop = $this->mysql->query("SELECT * FROM eshops WHERE ownerID = ".$user["user"]);
					//$haveshop = $shop->fetch_assoc();

					//$data = $stmt->fetch_assoc();

					//if ( $haveshop ) $data["shop"] = $haveshop["shopID"];
					
					return $data;
				}	
			}	
		}

	}
