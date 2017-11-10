<?php	
	/**
		* DB connect
	*/

	//$socket = "/tmp/mysql51.sock";
	
	class SQL {
		
		protected $server;
		protected $username;
		protected $password;
		protected $database;
		
		private static $instance;

		private $connection;
		private $query;

		public function __construct() {

			if ( LOCALHOST == true ) {
				$this->server = 'localhost:3306';
				$this->username = 'root';
				$this->password = '';
				$this->database = 'mm-admin';
			} else {
				$this->server = 'mariadb101.websupport.sk:3312';
				$this->username = 'e8bxx7nc';
				$this->password = 'c19283535';
				$this->database = 'e8bxx7nc';
			}

			$this->connection = new mysqli($this->server, $this->username, $this->password, $this->database) or die ('Chyba v pripojeni ' . mysqli_connect_error());
			$this->connection->set_charset('UTF8');
			//mysqli_query($this->connection, 'SET NAMES UTF8');
			//mysqli_query($this->connection, 'SET COLLATION_CONNECTION = UTF8');
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new SQL();
			}

			return self::$instance;
		}

/*
		public function __call($name, $args) {
			if( method_exists($this->connection, $name) ) {
				return call_user_func_array(array($this->connection, $name), $args);
			} else {
				trigger_error('Unknown Method ' . $name . '()', E_USER_WARNING);
				return false;
			}
		}*/


		public function query($query) {
			$this->query = $this->connection->query($query) or die ('Chyba v Query: ' . $query . '<br>Error:<br>' . mysqli_error($this->connection));
   			return $this->query;
		}
		
		public function q($query) {
			$this->query = $this->connection->query($query) or die ('Chyba v Query: ' . $query . '<br>Error:<br>' . mysqli_error($this->connection));
   			return $this->query;
		}

		public function result() {
			return mysqli_fetch_array($this->query, MYSQL_ASSOC);
		}

		public function result2() {
			$arr = array();

			while ($data = mysqli_fetch_array($this->query, MYSQL_ASSOC)) {
				$arr[] = $data;
			}

			return $arr;
		}
/*
		public function result3() {
			$arr = [];

			while ($data = mysqli_fetch_object($this->query, MYSQL_ASSOC)) {
				$arr[] = $data;
			}

			return $arr;
		}*/

		public function resultO() {
			$arr = array();

			while ($data = mysqli_fetch_object($this->query)) {
				$arr[] = $data;
			}

			return $arr;
		}

		public function fetchSingle($query) {
			return mysqli_fetch_object($query);
		}

		public function fetch_assoc() {
			return mysqli_fetch_assoc($this->query);
		}

		public function fetch_array() {
			$arr = array();

			while ($data = mysqli_fetch_array($this->query)) {
				$arr[] = $data;
			}

			return $arr;
		}

		public function safe($data) {
			return trim(mysqli_real_escape_string($this->connection, $data));
		}

		public function __destruct() {
			$this->connection->close();
		}
	}
/*
	if ( $localhost == true ) 
		$mysql = new MySQL('localhost:3306', 'root', '192835', 'monamade');
	else
		$mysql = new MySQL('localhost', 'u115832822_pts', 'c192835', 'u115832822_pts');
*/
		//$mysql = new DB();
