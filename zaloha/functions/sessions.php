<?php
	$SESS = new SESSIONS();

	class SESSIONS {
		
		protected static $instance;
		protected $mysql;
		protected $session;
		//protected $mm;
		
		public function __construct() {
			//$this->mm = MM::init();
			//$this->mysql = SQL::init();

			$this->create_session("mm");
		}
/*
		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new SESSIONS();
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
*/
		public function create_session($name) {
			global $LEVEL;

			if ( $LEVEL != 0 ) {
				$secure_key = $this->random_chars(12);
				$scrypt = $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];

				ini_set( 'session.use_only_cookies', TRUE );				
				ini_set( 'session.use_trans_sid', FALSE );

				$cookie = session_get_cookie_params();
				
				session_set_cookie_params($cookie["lifetime"], "/", $this->domain(), false, true);

				session_name($name); 
				session_start();
				
				$sessionToken = $this->hash( $scrypt, $secure_key );
	 
				$_SESSION['token'] = $sessionToken;
				//$_SESSION['page'] = $this->page();
			}
			/* else {
				$cookie = session_get_cookie_params();
				
				session_set_cookie_params($cookie["lifetime"], "/", $this->mm->domain(), false, true);
				session_name($name);
			}*/
			//session_regenerate_id();
			//session_regenerate_id(true);
			//$_SESSION['HTTP_USER_AGENT'] = $this->hash( $scrypt, $secure_key );

			/*if ( isset( $_SESSION['HTTP_USER_AGENT'] ) && $_SESSION['HTTP_USER_AGENT'] != $this->hash( $scrypt, $secure_key ) ) {
				exit;
			} else {
				$_SESSION['HTTP_USER_AGENT'] = $this->hash( $scrypt, $secure_key );
			}
*/

			

 /*
			if ( $_SESSION['token'] !== $sessionToken ) {
				$_SESSION = array();
			}*/
		}

		public function domain() {
			return  ($_SERVER['HTTP_HOST'] != 'localhost' ) ? $_SERVER['HTTP_HOST'] : false;
		}

		public function hash($value, $secure) {

			if ( isset($secure) )
				return hash_hmac('sha512', $value, $secure);
			else
				return hash('sha512', $value);
		}

		public function random_chars($pocet_znakov, $return = "") {
			$skupina_znakov = "abcdefghijklmopqrstuvwxyz01234567890123456789012345678901234567890";

			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}

	}
