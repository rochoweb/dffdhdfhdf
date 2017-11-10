<?php
	$MEDIA = new MEDIA();
	
	class MEDIA extends GLOBALDATA {

		protected $mysql;
		protected $GD;
		
		public function __construct() {
			$this->mysql = SQL::init();
			$this->GD = GLOBALDATA::init();
		}
		
		public function DIYDATA() {
			$stmt = $this->mysql->query("SELECT * FROM navody WHERE url = '".$_GET["p"]."'");

			return $stmt->fetch_object();
		}



		public function views($diyid) {
			$stmt = $this->mysql->query("SELECT id FROM prezretia WHERE id = $diyid AND ip = '".$this->GD->ip()."'");

			if ( !$stmt->fetch_object() )
				$this->mysql->query("INSERT INTO prezretia (iidd, id, time, ip) VALUES (NULL, $diyid, '".time()."', '".$this->GD->ip()."')");
		}




		/*public function calculate_media($table_from, $table_to) {
			$stmt = $this->mysql->query("SELECT * FROM navody");

			foreach ($this->mysql->result2() as $key => $value) {

				if ( $q = $this->mysql->query("SELECT COUNT(*) as total FROM $table_from WHERE id = ".$value["id"] )) {
					$re = $q->fetch_assoc();

					$this->mysql->query("UPDATE navody SET $table_to = ".$re["total"]." WHERE id = ".$value["id"]);
				}
				//mysql_result($q, 0);
			}
		}*/
	}