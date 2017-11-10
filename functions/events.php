<?php
	
	class EVENTS {
		
		protected $mysql;
		protected $GD;
		protected $type;
		
		public function __construct($type) {
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();

			$this->type = $type;

		}

		public function action($r = "") {
			//global $GD;
			switch ($this->type) {

				case 'resethistory':
					$this->GD->destroy_cookie("mm-history");

					$r = array( "reset" => true );
					break;
			}

			return $r;
		}



	}