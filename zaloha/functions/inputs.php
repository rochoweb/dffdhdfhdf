<?php
	
	class CHECKINPUTS {
		
		protected static $instance;
		protected $mysql;
		protected $GD;
		
		public function __construct() {
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new CHECKINPUTS();
			}

			return self::$instance;
		}

		public function DATA($data) {
			
			$return = array();

			foreach ($data as $key => $value) {
				
				$explode = explode("-", $key);

				$return[$explode[1]] = $value;
			}

			return $return;
		}

		public function action($type, $data, $r = "") {
			
			switch ($type) {

				case "billing-firstname":
				case "delivery-firstname":
					$r = $this->valid_general($data->firstname, $this->GD->text(111), MIN_USERNAME, MD_USERNAME);
					break;

				case "billing-lastname":
				case "delivery-lastname":
					$r = $this->valid_general($data->lastname, $this->GD->text(112), MIN_USERNAME, MD_USERNAME);
					break;

				case "billing-phone":
				case "delivery-phone":
					$r = $this->valid_phone( preg_replace('#\s+#', '', $data->phone) );
					break;

				case "billing-email":
					$r = $this->valid_email($data->email);
					break;

				case 'billing-street':
				case 'delivery-street':
					$r = $this->valid_street($data->street);

					break;

				case 'billing-city':
				case 'delivery-city':
					$r = $this->valid_general($data->city, $this->GD->text(545), MIN_USERNAME, MD_USERNAME);
					break;

				case 'billing-zip':
				case 'delivery-zip':
					$r = $this->valid_zip( preg_replace('#\s+#', '', $data->zip), PSC, MAX_NUM);
					break;


				case "company-company":
					$r = $this->valid_general($data->company, $this->GD->text(571), MIN_USERNAME, MD_USERNAME);
					break;
				case 'company-cid':
					$r = $this->valid_ico($data->cid);
					break;
				case 'company-tin':
					$r = $this->valid_dic($data->tin);
					break;
				case 'company-tax':
					$r = $this->valid_icdph($data->tax);
					break;

				default:
					$r = "dev";
			}

			

			return $r;
		}


		public function valid_grobian($data, $inputName, $r = "") {
			/*$filter_nadavky = $this->GD->filter( $data, 1 );
			$filter_rezervovane = $this->GD->filter( $data, 2 );
			$filter_global = $this->GD->filter( $data, 3 );
*/

			if ( $this->GD->filter( $data, 1 ) ) 
				$r = $inputName." ".$this->GD->text(85);
			else if ( $this->GD->filter( $data, 2 ) ) 
				$r = $inputName." ".$this->GD->text(84);
			else if ( $this->GD->filter( $data, 3 ) ) 
				$r = $inputName." ".$this->GD->text(89);

			return $r;
		}


		public function valid_general($data, $inputName, $minChars, $maxChars, $r = "") {
			
			if ( $g = $this->valid_grobian($data, $inputName) ) 
				$r = $g;
			else {
				if ( strlen($data) == 0 )
					$r = $this->GD->random_text( array($inputName." ".$this->GD->text(539), $inputName." ".$this->GD->text(540), $inputName." ".$this->GD->text(577)) );
				else {
					if ( strlen($data) > $maxChars )
						$r = sprintf($inputName." ".$this->GD->text(97), $maxChars);
					else {

						if ( strlen($data) < $minChars )
							$r = sprintf($inputName." ".$this->GD->text(547), $minChars);
						else if ( is_numeric($data) )
							$r = $this->GD->text(88);
						
					}
				}
				
			}
			
			
			
			return $r;
		}
		



		public function valid_lastname($user, $r = "") {
			$MD = $this->GD->MD();

			$filter_nadavky = $this->GD->filter( $user, 1 );
			$filter_rezervovane = $this->GD->filter( $user, 2 );
			$filter_global = $this->GD->filter( $user, 3 );


			/*if ( strlen($user) <= 4 ) 
				$r = $this->GD->text(112)." ".$this->GD->text(80);
			else {*/
				if ( strlen($user) > MD_USERNAME )
					$r = sprintf($this->GD->text(112)." ".$this->GD->text(97), MD_USERNAME);
				else {
					if ( $filter_nadavky == true ) 
						$r = $this->GD->text(112)." ".$this->GD->text(85);
					else if ( $filter_rezervovane == true ) 
						$r = $this->GD->text(112)." ".$this->GD->text(84);
					else if ( $filter_global == true ) 
						$r =  $this->GD->text(112)." ".$this->GD->text(89);
					else if ( is_numeric($user) ) 
						$r = $this->GD->text(88);
				}
			//}
			
			return $r;
		}

		public function valid_street($data, $r='') {

			if ( !preg_match('#[\d]#', $data) ) {
				$r = $this->GD->text(569);
			}

			return $r;
		}


		public function valid_phone($data, $r = '') {
			$inputName = $this->GD->text(538);

			if ( $g = $this->valid_grobian($data, $inputName) ) 
				$r = $g;
			else {
				if ( strlen($data) == 0 )
					$r = $this->GD->random_text( array($inputName." ".$this->GD->text(539), $inputName." ".$this->GD->text(540), $inputName." ".$this->GD->text(577)) );
				else {
					if ( !preg_match("/^[0-9]{10}+$/", $data) && !preg_match("/^[+]?[0-9]{12}+$/", $data) && !preg_match("/^[+]?[0-9]{13}+$/", $data))
						$r = $this->GD->text(590);
					else {
						if ( strlen( $data ) <= 6 )
							$r = $this->GD->text(542);
					}
				}
			}
			return $r;
		}

		public function valid_email($data, $r = '') {
			$inputName = $this->GD->text(543);

			$check_fake = $this->GD->check_email($data);

			if ( $g = $this->valid_grobian($data, $inputName) ) 
				$r = $g;
			else {

				if ( strlen($data) == 0 )
						$r = $this->GD->random_text( array($inputName." ".$this->GD->text(539), $inputName." ".$this->GD->text(540), $inputName." ".$this->GD->text(577)) );
				else {
					//if ( !$this->GD->validateEmail() )

					/*if ( !filter_var($data, FILTER_VALIDATE_EMAIL) )
						$r = $this->GD->text(86);*/
					if ( $check_fake != true )
						$r = $this->GD->text(96);
				}
			}
			
			return $r;
		}


		public function valid_zip($data, $minChars, $maxChars, $r = '') {
			$inputName = $this->GD->text(546);

			/*if ( $g = $this->valid_grobian($data, $inputName) ) 
				$r = $g;
			else {
				if ( strlen($data) == 0 )
						$r = $this->GD->random_text( array($inputName." ".$this->GD->text(539), $inputName." ".$this->GD->text(540)) );
				else {

					if ( !preg_match('# [\d]#', $data) ) {
						$r = $this->GD->text(569);
					} else {

						$check = preg_replace('#\s+#', '', $data);

						$stmt = $this->mysql->q("SELECT psc FROM zip WHERE psc = ".$check);

						if ( $stmt->fetch_object() )
							$r = $this->GD->text(573);
					}
				}
			}*/
			if ( !is_numeric( $data) )
				$r = $this->GD->text(546)." ".$this->GD->text(541);
			else {
				if ( !preg_match('#^\d{5}$#', $data) )
					$r = $this->GD->text(573);
				/*else {


					$check = preg_replace('#\s+#', '', $data);

					$stmt = $this->mysql->q("SELECT psc FROM zip WHERE psc = ".$check);

					if ( !$stmt->fetch_object() )
						$r = $this->GD->text(573);
				}*/
			} 
			
			return $r;
		}


		public function valid_ico($data, $r='') {
			
			$edit = preg_replace('#\s+#', '', $data);

			if ( !preg_match('#^\d{8}$#', $edit) )
				$r = sprintf($this->GD->text(570), $this->GD->text(564));
			
			return $r;
		}

		public function valid_dic($data, $r='') {
			
			$edit = preg_replace('#\s+#', '', $data);

			if ( !preg_match("/^[0-9]{10}$/", $edit) )
				$r = sprintf($this->GD->text(570), $this->GD->text(565));

			return $r;
		}

		public function valid_icdph($data, $r='') {
			
			$edit = preg_replace('#\s+#', '', $data);

			if ( !preg_match("/^SK[0-9]{10}$/", $edit) )
				$r = sprintf($this->GD->text(570), $this->GD->text(587));

			return $r;
		}





		public function result($re, $r = "") {
			switch ($re) {
				case true:
					$r = 'fa fa-check';
					break;
				case false:
					$r = 'fa fa-times';
					break;
			}

			return $r;
		}
		
	}