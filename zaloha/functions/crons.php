<?php
	$CRON = new CRONS();


	class CRONS {

		protected static $instance;
		protected $m;
		
		public function __construct() {
			$this->m = SQL::init();
			$this->CHR = CHR::init();
		}
		
		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new CRONS();
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





		public function default_cleaning($r = '') {
			
			$this->m->q("DELETE FROM basket WHERE content IS NULL OR content = ''");

			$this->m->q("DELETE FROM concepts WHERE files IS NULL AND title IS NULL");
		}
	}

