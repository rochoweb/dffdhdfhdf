<?php
	
	class VERIFY {
		
		protected static $instance;
		protected $mysql;
		protected $GD;
		
		public function __construct() {
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new VERIFY();
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

		public function action($type, $data, $page = "") {
			$result = $return = $input = $update = "";

			$this->data = $data;
			$this->type = $type;

			switch ($this->type) {


				case 'signup-email':
				case 'psignup-email':
					$result = $this->valid_email( $data ); $input = true;
					break;
				
				case 'signup-password':
				case 'psignup-password':
					$result = $this->valid_signup_password( $data ); $input = true;
					break;
				
				case 'signup-password-confirm':
				case 'psignup-password-confirm':
					$result = $this->valid_signup_password_confirm( $data ); $input = true;
					break;
				
				case 'acc-signup';
				case 'pacc-signup':
					$DATA = $this->data;
					$registered = false;


					$result = $this->signup( $DATA );

					if ( $result )
						$valid = 0;
					else {
						$account = $this->create_account( $DATA );

						if ( $account ) {
							$text = sprintf($this->GD->text(94), '<strong>'.strtoupper($account).'</strong>');
							$valid = 1;

							$registered = 1;

							$link_to_mail = $this->link_to_email($DATA->email);

							if ( $link_to_mail )
								$link_text = sprintf($this->GD->text(100), '<a href="'.$link_to_mail.'" target="_blank" class="bh jsl" title="'.$this->GD->text(101).'">'.$DATA->email.'</a>');
							else
								$link_text = sprintf($this->GD->text(100), $DATA->email);

							$result = $text." <span>".$link_text.'</span><a href="#" class="hib jsl">'.mb_strtoupper($this->GD->text(197), "UTF8").'</a>';
						}
					}

					$htmlD = $this->result($valid);

					$message = '
						<div class="loader-content '.$htmlD[0].'">
							<div>
								<div class="loader-icon"><div class="ii '.$htmlD[1].'"></div></div>
								<p>'.$result.'</p>
							</div>
						</div>
					';

					$return = array("r" 	=> strip_tags($result), "mess"	=> $message, "reg"	=> $registered );
					break;

				case 'acc-login':
					$token = "";

					$DATA = $this->data;
					$USERID = $this->verify_existdata($this->data->email, "uzivatelia", "userEMAIL");
					
					$online = $valid = false;
					//$valid = false;


					//$result = $this->login( $DATA );

					if ( !$result = $this->login( $DATA ) ) {
						
						if ( $validation = $this->validation( $DATA ) ) {

							if ( $token = $this->create_login( $USERID["userID"] ) ) {
								
								$online = $valid = 1;
								$result = sprintf($this->GD->text(99), '<strong>'.strtoupper($validation).'</strong>' );
							}
							else $result = $this->GD->text(102);
						}
						else {
							$this->login_attempts($USERID["userID"]);
							$result = $this->GD->text(98);
						}
					}

					$htmlD = $this->result($valid);

					$message = '
						<div class="loader-content '.$htmlD[0].'">
							<div>
								<div class="loader-icon"><div class="ii '.$htmlD[1].'"></div></div>
								<span>'.$result.'</span>
							</div>
						</div>
					';

					$return = array("r" 	=> strip_tags($result), "mess"	=> $message, "on"	=> $online, "tn"	=> $token );
					break;

				case 'settings-email':
					$UID = $this->USERDATA();

					if ( $UID["userEMAIL"] != $data ) {
						$result = $this->valid_email( $data );

						if ( !$result ) {
							
							$result = $this->update_email( $data );
							$update = true;
						}

						$input = true; 
					}
					break;

				case 'settings-firstname':
					$UID = $this->USERDATA();

					if ( $UID["firstname"] != $data ) {
						$result = $this->valid_firstname( $data );

						if ( !$result ) {
							
							$result = $this->update_firstname( $data );
							$update = true;
						}

						$input = true; 
					}
					break;

				case 'settings-lastname':
					$UID = $this->USERDATA();

					if ( $UID["lastname"] != $data ) {
						$result = $this->valid_lastname( $data );

						if ( !$result ) {
							
							$result = $this->update_lastname( $data );
							$update = true;
						}

						$input = true; 
					}
					break;

				case 'settings-gender':
					$UID = $this->USERDATA();

					if ( $this->gender($UID["gender"]) != $data ) {
						$result = $this->valid_gender( $data );

						if ( !$result ) {
							
							$result = $this->update_gender( $data );
							$update = true;
						}

						$input = true;
					}
					break;

/*
				case 'shop-shopname':
					$result = $this->valid_shop_shopname( $data ); $input = true;
					break;
*/
				/*case 'shop-create':
					$DATA = $this->DATA( $data );
					$UID = $this->USERDATA();

					
					$valid = false;
					//$valid = false;

					if ( !$result = $this->shop( $DATA ) ) {
						
						$stmt = $this->mysql->query("SELECT shopID FROM uzivatelia WHERE userID = ".$UID["userID"]);
						$EXISTSHOP =$stmt->fetch_assoc();

						//$EXISTSHOP = $this->verify_existdata($UID["userID"], "uzivatelia", "ownerID");

						if ( !$EXISTSHOP ) {
							//$result = sprintf($this->GD->text(196), $EXISTESHOP["name"]);

							if ( $shop = $this->create_shop( $DATA, $UID ) ) {
								
								$text = sprintf($this->GD->text(198), '<strong>'.strtoupper($shop).'</strong>');

								$valid = 1;

								$link_to_mail = $this->link_to_email( $UID["userEMAIL"] );

								if ( $link_to_mail )
									$link_text = sprintf($this->GD->text(100), '<a href="'.$link_to_mail.'" target="_blank" class="bh" title="'.$this->GD->text(101).'">'.$UID["userEMAIL"].'</a>');
								else
									$link_text = sprintf($this->GD->text(100), $UID["userEMAIL"]);

								$result = $text." <br> ".$link_text;
							}

						} else {
							$result = sprintf($this->GD->text(196), mb_strtoupper($EXISTESHOP["name"], "utf-8") );
						}
					}
					


					$htmlD = $this->result($valid);

					$message = '
						<div class="loader-content '.$htmlD[0].'">
							<div>
								<div class="loader-icon"> <div class="i '.$htmlD[1].'"></div> </div>
								<span>'.$result.'</span>
							</div>
						</div>
					';

					$return = array("r" 	=> strip_tags($result), "mess"	=> $message );
					break;*/
			}

			




			if ( $input == true ) {

				if ( $result ) {
					$text = '
						<div class="ir-text dH">
		 					<div></div>
		 					<p>'.$result.'</p>
		 				</div>
					';
					$class = "i21 bh ch";
				} else {
					$class = "i20"; $text = "";
				}

				if ( $update ) {
					$text = '
						<div class="ir-text dH">
		 					<div></div>
		 					<p>'.$result.'</p>
		 				</div>
					';
					$class = "i20 bh ch";
				}

				$r = '
					<button class="input-result i '.$class.'"></button>

					'.$text.'
				';
				
				
				$return = array("r" 	=> "$r" );
			}
			
			return $return;
		}



		public function update_email($email) {
			$UID = $this->USERDATA();

			if ( $UID["userEMAIL"] != $email ) {

				if ( $this->mysql->query("UPDATE uzivatelia SET userEMAIL = '$email' WHERE userID = ".$UID["userID"]."") )
					return $this->GD->text(110);
			}
		}




		public function update_firstname($firstname) {
			$UID = $this->USERDATA();

			if ( $UID["firstname"] != $firstname ) {
				if ( $this->mysql->query("UPDATE uzivatelia SET firstname = '$firstname' WHERE userID = ".$UID["userID"]."") )
					return $this->GD->text(110);
			}
		}

		public function update_lastname($lastname) {
			$UID = $this->USERDATA();

			if ( $UID["lastname"] != $lastname ) {
				if ( $this->mysql->query("UPDATE uzivatelia SET lastname = '$lastname' WHERE userID = ".$UID["userID"]."") )
					return $this->GD->text(110);
			}
		}

		public function update_gender($gender) {
			$UID = $this->USERDATA();

			switch ($gender) {
				case $this->GD->text(113):
					$gender = 1;
					break;
				case $this->GD->text(114):
					$gender = 2;
					break;
				default:
					$gender = 0;
					break;
			}

			if ( $UID["gender"] != $gender ) {
				if ( $this->mysql->query("UPDATE uzivatelia SET gender = '$gender' WHERE userID = ".$UID["userID"]."") )
					return $this->GD->text(110);
			}
		}


		public function valid_firstname($user, $r = "") {
			$MD = $this->GD->MD();

			$filter_nadavky = $this->GD->filter( $user, 1 );
			$filter_rezervovane = $this->GD->filter( $user, 2 );
			$filter_global = $this->GD->filter( $user, 3 );

			/*if ( strlen($user) <= 4 ) 
				$r = $this->GD->text(111)." ".$this->GD->text(80);
			else {*/

				if ( strlen($user) > $MD["username"] )
					$r = sprintf($this->GD->text(111)." ".$this->GD->text(97), $MD["username"]);
				else {
					if ( strlen($user) > 0 ) {
						if ( $filter_nadavky == true ) 
							$r = $this->GD->text(111)." ".$this->GD->text(85);
						else if ( $filter_rezervovane == true ) 
							$r = $this->GD->text(111)." ".$this->GD->text(84);
						else if ( $filter_global == true ) 
							$r =  $this->GD->text(111)." ".$this->GD->text(89);
						else if ( is_numeric($user) ) 
							$r = $this->GD->text(88);
					}
				}
			
			//}
			
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
				if ( strlen($user) > $MD["username"] )
					$r = sprintf($this->GD->text(112)." ".$this->GD->text(97), $MD["username"]);
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

		public function valid_gender($gender, $r = "") {
			global $MD;

			
			/*if ( $gender !== $this->GD->text(113) || $gender !== $this->GD->text(114) )
				$r = $this->GD->text(115);*/
			/*if ( strlen($user) <= 4 ) 
				$r = $this->GD->text(112)." ".$this->GD->text(80);
			else {
				if ( strlen($user) > $MD["username"] )
					$r = sprintf($this->GD->text(112)." ".$this->GD->text(97), $MD["username"]);
				else {
					if ( is_numeric($user) ) 
						$r = $this->GD->text(88);
				}
			}*/
			
			return $r;
		}



		/*		PRIHLASENIE		--> */


		public function login($data) {

			foreach ($data as $key => $value) {
				
				switch ($key) {
					case 'email':
						$r[] = $this->valid_login_username($value);
						break;
					case 'password':
						$r[] = $this->valid_login_password($value);
						break;
				}
			}

			foreach ($r as $key => $value) {
				if ( strlen($value) != 0 )
					return $value;
			}
		}


		public function validation($data) {
			if ( isset($data) ) {
				$stmt = $this->mysql->query("SELECT userPASS, secure FROM uzivatelia WHERE userEMAIL = '".$data->email."' ");
				$r = $stmt->fetch_object();

				if ( $r->userPASS == $this->GD->hash($data->password, $r->secure) )
					return $data->email;
			}
			
		}


		public function valid_login_username($user, $r = "") {
			//$MD = $this->GD->MD();

			$user_exist = $this->verify_existdata( $user, "uzivatelia", "userEMAIL" );
			//$user_exist2 = $this->verify_existdata( $user, "uzivatelia", "userNAME" );

			if ( strlen($user) == 0 )
				$r = $this->GD->text(93);
			else {
				if ( !$user_exist ) {
					$r = $this->GD->text(98); //157
				}
			}
			
			return $r;
		}


		public function valid_login_password($pass, $r = "") {
			//$MD = $this->GD->MD();

			if ( strlen($pass) == 0 )
				$r = $this->GD->text(92);
			else {
				if ( strlen( $pass ) <= 4 )
					$r = $this->GD->text(90);
				else if ( strlen($pass) > MD_PW )
					$r = sprintf($this->GD->text(57)." ".$this->GD->text(97), MD_PW);
			}
		
			return $r;
		}


		public function create_login ($userID) {
			//$TIME = $this->GD->TIME();

			if ( isset($_COOKIE["mm"]) ) {
				//$this->mysql->query("DELETE FROM online WHERE session = '".session_id()."'");

				$id = "NULL";
				$time = time();
				$token = $this->GD->random_chars(30);

				$stmt = $this->mysql->q("DELETE FROM online WHERE session = '".session_id()."' AND token = '$token'");

				if ( $stmt ) {
					$this->mysql->q("INSERT INTO online VALUES($id, '$token', '$userID', '".$this->GD->ip()."', '".session_id()."', '$time', '$time', '".$this->GD->device()."')");
					$this->mysql->q("INSERT INTO login_log VALUES($id, '$userID', '".$this->GD->ip()."', '$time', '".$this->GD->device()."')");

					if ( isset($_COOKIE["mm-online"]) ) setcookie("mm-online", "", time()-3600, "/", $this->GD->domain());

					setcookie("mm-online", $token, strtotime( str_replace("-", "", TIME_LOGINTIME ) ), "/", $this->GD->domain(), false, true);

					if ( $this->mysql->query("UPDATE uzivatelia SET last_login = '$time', ip = '".$this->GD->ip()."' WHERE userID = '$userID'") ) 
						return $token;
				}
			}
		}

		public function login_attempts($userID) {
			$time = time();
			$this->mysql->query("INSERT INTO pokusy VALUES('$userID', '$time', '".$this->GD->ip()."')");
		}

		/* <-- 	PRIHLASENIE		*/






		/*		REGISTRACIA		-->	*/

		public function signup($data) {

			foreach ($data as $key => $value) {
				
				switch ($key) {
					case 'email':
						$r[] = $this->valid_email($value);
						break;
					case 'password':
						$r[] = $this->valid_signup_password($value);
						break;
					case 'passwordconfirm':
						$r[] = $this->valid_signup_password_confirm($value);
						break;
				}
			}

			foreach ($r as $key => $value) {
				if ( strlen($value) != 0 )
					return $value;
			}
		}

		public function create_account($data) {
			if ( $data ) {
				
				$ID = $this->last_userID() + 1;
				$reg_date = time();
				$secure = $this->GD->random_chars(16);

				if ( $this->mysql->query("INSERT INTO uzivatelia (id, userID, userEMAIL, userPASS, ip, acctype, registration, secure) VALUES(NULL, $ID, '".$data->email."', '".$this->GD->hash($data->password, $secure)."', '".$this->GD->ip()."', '1', '$reg_date', '$secure')") ) return $data->email;


				$activation_code = $this->GD->random_chars(34);

			 	$this->mysql->query("INSERT INTO uzivatelia_plus VALUES(NULL, $ID, '$activation_code')");
			}
		}



		public function valid_signup_username($user, $r = "") {
			//$MD = $this->GD->MD();

			$user_exist = $this->verify_existdata( $user, "uzivatelia", "userID" );
			$filter_nadavky = $this->GD->filter( $user, 1 );
			$filter_rezervovane = $this->GD->filter( $user, 2 );
			$filter_global = $this->GD->filter( $user, 3 );

			if ( strlen($user) == 0 )
				$r = $this->GD->text(91);
			else {
				if ( $user_exist ) 
					$r = sprintf($this->GD->text(81), strtoupper($user) );
				else {
					if ( strlen($user) <= 4 ) 
						$r = $this->GD->text(79)." ".$this->GD->text(80);
					else {
						if ( strlen($user) > MD_USERNAME )
							$r = sprintf($this->GD->text(79)." ".$this->GD->text(97), MD_USERNAME);
						else {
							if ( $filter_nadavky == true ) 
								$r = $this->GD->text(79)." ".$this->GD->text(85);
							else if ( $filter_rezervovane == true ) 
								$r = $this->GD->text(79)." ".$this->GD->text(84);
							else if ( $filter_global == true ) 
								$r =  $this->GD->text(79)." ".$this->GD->text(89);
							else if ( !preg_match("/^[a-zA-Z0-9-.]+$/", $user) ) 
								$r = $this->GD->text(79)." ".$this->GD->text(82);
							else if ( is_numeric($user) ) 
								$r = $this->GD->text(88);
						}
					}
				}
			}
			
			return $r;
		}


		public function valid_signup_password($pass, $r = "") {
			//$MD = $this->GD->MD();

			if ( strlen($pass) == 0 )
				$r = $this->GD->text(92);
			else {
				if ( strlen( $pass ) <= 4 )
					$r = $this->GD->text(90);
				else if ( strlen($pass) > MD_PW )
					$r = sprintf($this->GD->text(57)." ".$this->GD->text(97), MD_PW);
			}
		
			return $r;
		}

		public function valid_signup_password_confirm($pass, $r = "") {
			//$MD = $this->GD->MD();

			if ( strlen($pass) == 0 )
				$r = $this->GD->text(726);
			else {
				if ( $this->data->password != $this->data->passwordconfirm )
					$r = $this->GD->text(725);
			}
			
			return $r;
		}

		public function valid_email($email, $r = "") {
			$exist = $this->verify_email( $email );
			$check_fake = $this->GD->check_email($email);

			if ( strlen($email) == 0 )
				$r = $this->GD->text(93);
			else {
				if ( !filter_var($email, FILTER_VALIDATE_EMAIL) )
					$r = $this->GD->text(86);
				else if ( $exist == true ) 
					$r = $this->GD->text(87);
				else if ( $check_fake != true )
					$r = $this->GD->text(96);
			}
			
			return $r;
		}


		public function link_to_email($email, $r = "") {
			$domain = explode("@", $email);

			$stmt = $this->mysql->query("SELECT url FROM email_provider WHERE domain = '$domain[1]'");
			$r =$stmt->fetch_assoc();

			if ( $r )
				return $r["url"];
		}
		/*	<--	REGISTRACIA	*/









		# SHOP -->
			
			public function shop($data) {

				foreach ($data as $key => $value) {
					
					switch ($key) {
						case 'shopname':
							$r[] = $this->valid_shop_shopname($value);
							break;
					}
				}

				foreach ($r as $key => $value) {
					if ( strlen($value) != 0 )
						return $value;
				}
			}

			public function valid_shop_shopname($shopname, $r = "") {
				$MD = $this->GD->MD();

				$user_exist = $this->verify_existdata( $shopname, "eshops", "name" );
				$filter_nadavky = $this->GD->filter( $shopname, 1 );
				$filter_rezervovane = $this->GD->filter( $shopname, 2 );
				$filter_global = $this->GD->filter( $shopname, 3 );

				if ( strlen($shopname) == 0 )
					$r = sprintf( $this->GD->text(194),  $this->GD->text(195));
				else {
					if ( $user_exist ) 
						$r = sprintf($this->GD->text(81), strtoupper($shopname) );
					else {
						if ( strlen($shopname) <= 4 ) 
							$r = sprintf($this->GD->text(203), $this->GD->text(195) );
						else {
							if ( strlen($shopname) > $MD["username"] )
								$r = sprintf($this->GD->text(195)." ".$this->GD->text(97), $MD["username"]);
							else {
								if ( $filter_nadavky == true ) 
									$r = $this->GD->text(195)." ".$this->GD->text(85);
								else if ( $filter_rezervovane == true ) 
									$r = $this->GD->text(195)." ".$this->GD->text(84);
								else if ( $filter_global == true ) 
									$r =  $this->GD->text(195)." ".$this->GD->text(89);
								else if ( !preg_match("/^[a-zA-Z0-9-.]+$/", $shopname) ) 
									$r = $this->GD->text(195)." ".$this->GD->text(82);
								else if ( is_numeric($shopname) ) 
									$r = $this->GD->text(88);
							}
						}
					}
				}
				
				return $r;
			}


			/*public function verify_actualeshops($owner) {
				$stmt = $this->$mysql->query("SELECT * FROM eshops WHERE ownerID = $owner")
				return $stmt->fetch_assoc();
			}*/

			public function create_shop($data, $userdata) {
				$TIME = $this->GD->TIME();

				if ( $data ) {
					$id = "NULL";
					$time = time();
					$secure = $this->GD->random_chars(16);

					if ( $this->mysql->query("INSERT INTO eshops VALUES($id, '".$userdata["userID"]."', '".$data["shopname"]."', 0, 0, '$secure', '$time', 0)") ) {

						$stmt = $this->mysql->query("SELECT shopID FROM eshops WHERE secure = '$secure' AND ownerID = ".$userdata["userID"]);
						$shop = $stmt->fetch_assoc();

						if ( $this->mysql->query("UPDATE uzivatela SET shop_id = ".$shop["shopID"]." WHERE userID = ".$userdata["userID"]) );

						if ( $this->mysql->query("INSERT INTO eshops_plus VALUES('".$shop["shopID"]."', '$secure')") )
							return $data["shopname"];
					}
				}
			}
		# <-- SHOP


















		public function result($result) {
			switch ($result) {
				case false:
					return array('l-fail', 'iLight');
					break;
				case true:
					return array('l-ok', 'iCoffe');
					break;
			}
		}

		public function last_userID() {
			$stmt = $this->mysql->query("SELECT userID FROM uzivatelia ORDER BY userID DESC LIMIT 1");
			$r = $stmt->fetch_assoc();

			if ( !$r )
				$r["userID"] = 1000;

			return $r["userID"];
		}




		public function verify_existdata($data, $table, $tableid) {
			$stmt = $this->mysql->query("SELECT * FROM $table WHERE $tableid = '$data'");
			//$stmt = $this->mysql->query("SELECT userID FROM uzivatelia WHERE userNAME = '$data'");
			return $stmt->fetch_assoc();
		}

		function verify_email($data) {
			$stmt = $this->mysql->query("SELECT userEMAIL FROM uzivatelia WHERE userEMAIL = '$data'");

			return $stmt->fetch_assoc();
		}








		
		
	}