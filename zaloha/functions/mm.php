<?php
	
	class MM {
		
		protected static $instance;
		protected $mysql;
		//protected $mm;

		public $online = false;
		public $userdata;
		public $userid;
		
		public $shop;
		public $shopid;

		public $pagedata;
		public $pageid;
		public $pagename;
		public $lang;

		public $PD;

		public function __construct() {
			$this->mysql = SQL::init();
			
			$this->U = $this->USERDATA_O();

			$this->online = $this->onlinestatus();
			$this->user = $this->USERDATA();
			$this->diyID = isset($_COOKIE["DIY"]);
			//$this->PD = $this->PAGEDATA_OBJECT();

			//$this->online = $this->onlinestatus();

			/*if ( $this->online == true ) {
				$this->userdata = $this->USERDATA();
				$this->userid = $this->USERID();

				if ( $this->shop = $this->haveshopie() ) {
					$this->shopid = $this->SHOPID();
				}
			}
*/

			/*$this->pagedata = $this->CHECK();
			$this->pageid = $this->page("page");
			$this->pagename = $this->page("name");
			$this->lang = $this->lang();*/
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new MM();
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

		public function USERDATA_O( $data = "" ) {
			if ( $data ) {
				$stmt = $this->mysql->query("SELECT * FROM cms_uzivatelia WHERE userID = '".$data."'");
				
				if ( $r = $stmt->fetch_object() )
					return $r;
				else
					return false;
			} else {

				if ( $this->online() ) {
					$stmt = $this->mysql->query("SELECT user FROM cms_online WHERE token = '".$this->online()."'");
					$user = $stmt->fetch_object();

					$stmt = $this->mysql->query("SELECT * FROM cms_uzivatelia WHERE userID = '".$user->user."'");
					
					return $stmt->fetch_object();
				}	
			}	
		}

		public function PAGEDATA_OBJECT($r = "") {
			/*$page = isset($_GET["p"]) ? $_GET["p"] : "";

			if ( !$page ) $page = DEFAULT_PAGE;

			$stmt = $this->mysql->query("SELECT * FROM pages WHERE name = '$page'");
			
			return $stmt->fetch_object();*/
			$page = isset($_GET["p"]) ? $_GET["p"] : "";

			if ( !$page ) $page = DEFAULT_PAGE;

			/*if ( $pageID > 0 )
				$select = "page = ".$pageID;
			else*/
				$select = "name = '".$page."'";

			$stmt = $this->mysql->query("SELECT * FROM pages WHERE ".$select." AND lang = '".$this->lang()."'");
			$r = $stmt->fetch_object();

			return $r;
		}

		/*public function CHECK($r = "") {
			global $AJAX, $LEVEL;
			
				if ( !isset($_GET["p"]) )
					$page = "uvod";
				else
					$page = $_GET["p"];


				$this->mysql->query("SELECT * FROM pages WHERE name = '$page'");
				$r = $this->mysql->fetch_assoc();

				if ( isset($lang) )
					$language = $lang["lang"];
				else 
					$language = $r["lang"];

				if ( $AJAX == true ) {
					$this->mysql->query("DELETE FROM user_lang WHERE ip = '".$_SERVER["REMOTE_ADDR"]."'");
					$this->mysql->query("INSERT INTO user_lang VALUES('".$_SERVER["REMOTE_ADDR"]."', '$language')");
				
					setcookie("lang", $language, strtotime("+1 months"), '/', $this->domain());
				}
				

				if ( $r["logged"] ) {
					if ( !$this->online )
						header( "Location: ".$this->link(3) );
				}



				return $r;
			
		}*/

		public function CHECK($r = "") {
			global $AJAX, $LEVEL;
			//var_dump($this->pagedata);
			//if ( $check == true ) {
				if ( !isset($_GET["p"]) )
					$page = "uvod";
				else
					$page = $_GET["p"];

				$q["user"] = $this->mysql->query("SELECT * FROM uzivatelia WHERE userNAME = '".$this->mysql->safe($page)."'");

				$q["item"] = $this->mysql->query("SELECT * FROM navody WHERE url = '".$this->mysql->safe($page)."'");
					
				if ( $q["user"]->fetch_assoc() ) {
					$this->mysql->query("SELECT * FROM pages WHERE page = '15'");
					$r = $this->mysql->fetch_assoc();

				} else if ( $q["item"]->fetch_assoc() ) {
					$this->mysql->query("SELECT * FROM pages WHERE page = '16'");
					$r = $this->mysql->fetch_assoc();

				} else { 
					$this->mysql->query("SELECT * FROM pages WHERE name = '$page'");
					$r = $this->mysql->fetch_assoc();
				}

				

				if ( isset($lang) )
					$language = $lang["lang"];
				else 
					$language = $r["lang"];

				if ( $AJAX == true ) {
					$this->mysql->query("DELETE FROM user_lang WHERE ip = '".$_SERVER["REMOTE_ADDR"]."'");
					$this->mysql->query("INSERT INTO user_lang VALUES('".$_SERVER["REMOTE_ADDR"]."', '$language')");
				
					setcookie("lang", $language, strtotime("+1 months"), '/', $this->domain());
				}
				

				if ( $r["logged"] ) {
					if ( !$this->online )
						header( "Location: ".$this->link(3) );

					if ( $r["for_shop"] ) {
						if ( !$this->shop )
							header( "Location: ".$this->link(100) );
					}
				}

				

				return $r;
			//}
		}


		public function destroySession() {
			/*$params = session_get_cookie_params();
			
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
			session_destroy();*/
			
			session_destroy();
			$_SESSION = array();
/*
			session_destroy();

			$cookieParams = session_get_cookie_params();
			setcookie( session_name(), '', 0, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly'] );
			
			$_SESSION = array();
			session_unset(); 
			session_destroy(); */
		}



		public function device() {
			return $_SERVER["HTTP_USER_AGENT"];
		}

		public function ip() {
			return $_SERVER["REMOTE_ADDR"];
		}

		public function online() {
			if ( $_SERVER['HTTP_HOST'] != 'deleted' )
				return isset($_COOKIE["online"]) ? $_COOKIE["online"] : "";
			else
				return false;
		}

		public function domain() {
			return  ($_SERVER['HTTP_HOST'] != 'localhost' ) ? $_SERVER['HTTP_HOST'] : false;
		}

		public function session() {
			return session_id();
		}

		public function MD() {
			return array( 
				'username' 		=> 15, 
				'password' 		=> 12, 
				'result-list'	=> 8);
		}

		public function TIME() {
			return array(
				'logintime'		=> '+1 week',
				'inactive' 		=> '-1 day',
				'default'		=> '+1 year');
		}

		public function FD() {
			return array(
				'img-types'		=> array('image/png', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/jpg',),
				'max-upload'	=> 1048576 ); //5 MB 5242880
		}

		
		public function lang() {
			$check = $this->pagedata;

			return $check["lang"];
		}

		public function page($type = "name") {
			$check = $this->pagedata;

			return $check[ $type ];
		}

		public function price_format($euro, $cent) {
			$number = number_format( $euro.'.'.$cent, 2 );

			return $number." €";
		}
		
		public function onlinestatus($on = false) {
			if ( $this->online() ) {

				$stmt = $this->mysql->query("SELECT * FROM cms_online WHERE ip = '".$this->ip()."' AND token = '".$this->online()."'");
				if ( $stmt->fetch_assoc() ) $on = true;
			}

			return $on;
		}

		public function haveshopie($shop = false) {
			
			$stmt = $this->mysql->query("SELECT * FROM eshops WHERE ownerID = ".$this->userid);
			
			if ( $r = $stmt->fetch_assoc() ) return $r;
		}
		
		/*public function USERDATA() {
			if ( $this->online() ) {

				$stmt = $this->mysql->query("SELECT * FROM online WHERE ip = '".$this->ip()."' AND token = '".$this->online()."'");
				if ( $userid = $stmt->fetch_assoc() ) {

					$stmt = $this->mysql->query("SELECT * FROM uziatelia WHERE ip = '".$this->ip()."' AND token = '".$this->online()."'");

					$data = $stmt->fetch_assoc();
				}
			}

			return $on;
		}*/

		public function USERDATA( $data = "" ) {
			if ( $data ) {
				$stmt = $this->mysql->query("SELECT * FROM uzivatelia WHERE userID = '".$data."'");
				
				if ( $r = $stmt->fetch_assoc() )
					return $r;
				else
					return false;
			} else {

				if ( $this->online() ) {
					$stmt = $this->mysql->query("SELECT user FROM cms_online WHERE token = '".$this->online()."'");
					$user = $stmt->fetch_assoc();

					$stmt = $this->mysql->query("SELECT * FROM cms_uzivatelia WHERE userID = '".$user["user"]."'");
					

					$shop = $this->mysql->query("SELECT * FROM cms_eshops WHERE ownerID = ".$user["user"]);
					$haveshop = $shop->fetch_assoc();

					$data = $stmt->fetch_assoc();

					if ( $haveshop ) $data["shop"] = $haveshop["shopID"];
					
					return $data;
				}	
			}	
		}

		public function USERID($r='') {
			$r = $this->userdata;

			return $r["userID"];
		}

		public function SHOPID($r='') {
			$r = $this->userdata;

			return $r["shopID"];
		}

		public function pager($pager = 1 ) {
			global $AJAX;

			switch ( $this->lang() ) {
				case 'sk':
					if ( isset($_GET["page"]) )
						header( "Location: ".$this->make_link( sprintf($this->text(161), 1 ) ) );
					/*else if ( !isset($_GET["strana"]) )
						header( "Location: ".$this->make_link( sprintf($this->text(161), 1 ) ) );*/

					/*if ( )
					$_GET["strana"] = 1;*/
					/*if ( isset($_GET["strana"]) )
						$pager = $_GET["strana"];
					else {
						if ( isset($_COOKIE["pager"]) )
							$pager = $_COOKIE["pager"];
						else
							$pager = 1;
					}*/



					/*if ( $AJAX != true ) {
						$pager = $_GET["strana"];
					} else {
						if ( isset($_COOKIE["pager"]) )
							$pager = $_COOKIE["pager"];
						else
							$pager = 1;
					}*/
					if ( $AJAX != true )
						$pager = isset($_GET["strana"]) ? $_GET["strana"] : 1;
					else
						$pager = 1;
					break;
				
				case 'en':
					if ( isset($_GET["strana"]) )
						header( "Location: ".$this->make_link( sprintf($this->text(161), 1 ) ) );
					/*else if ( !isset($_GET["page"]) )
						header( "Location: ".$this->make_link( sprintf($this->text(161), 1 ) ) );*/

					//$_GET["page"] = 1;
					/*if ( isset($_GET["page"]) )
						$pager = $_GET["page"];
					else {
						if ( isset($_COOKIE["pager"]) )
							$pager = $_COOKIE["pager"];
						else
							$pager = 1;
					}*/



					/*if ( $AJAX != true ) {
						$pager = $_GET["page"];
					} else {
						if ( isset($_COOKIE["pager"]) )
							$pager = $_COOKIE["pager"];
						else
							$pager = 1;
					}*/
					if ( $AJAX != true )
						$pager = isset($_GET["page"]) ? $_GET["page"] : 1;
					else
						$pager = 1;
					break;
			}

			/*if ( empty($pager) )
				$pager = $_COOKIE["pager"];*/

			/*if ( $AJAX != true )
				setcookie("pager", $pager, strtotime("+1 months"), '/', $this->domain());*/

			return $pager;
		}

		public function reset_pager() {
			setcookie("pager", "1", strtotime("+1 months"), '/', $this->domain());
		}


		public function pagedata($pageID = "") {
			$PD = $this->pagedata;

			/*
				$q["user"] = $this->mysql->query("SELECT * FROM uzivatelia WHERE userNAME = '".$this->mysql->safe($_GET["p"])."'");

				$r["user"] = $q["user"]->fetch_assoc();

				$type = "user";
			}*/

			/*switch ($type) {
				case 'item':
					//$data = array('header' => $r["item"]["intro_head"] );
					$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = 6 AND lang = '".$this->lang()."'");
					$r = $stmt->fetch_assoc();
					break;
				
				case 'user':
					//$data = array('header' => $r["user"]["userNAME"] );

					$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = 9 AND lang = '".$this->lang()."'");
					$r = $stmt->fetch_assoc();
					break;

				default:
					if ( $pageID > 0 )
						$select = "page = ".$pageID;
					else
						$select = "name = '".$PD["name"]."'";

					$stmt = $this->mysql->query("SELECT * FROM pages WHERE ".$select." AND lang = '".$this->lang()."'");
					$r = $stmt->fetch_assoc();

					break;
			}
*/
			/*
			$q["user"] = $this->mysql->query("SELECT * FROM uzivatelia WHERE userNAME = '".$this->mysql->safe($_GET["p"])."'");

			if ( $r["user"] = $q["user"]->fetch_assoc() ) {
				$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = 6 AND lang = '".$this->lang()."'");
				$r = $stmt->fetch_assoc();

			} else { 
				if ( $pageID > 0 )
					$select = "page = ".$pageID;
				else
					$select = "name = '".$PD["name"]."'";

				$stmt = $this->mysql->query("SELECT * FROM pages WHERE ".$select." AND lang = '".$this->lang()."'");
				$r = $stmt->fetch_assoc();
			}

			

			return "fdfsd";*/


			if ( $pageID > 0 )
				$select = "page = ".$pageID;
			else
				$select = "name = '".$PD["name"]."'";

			$stmt = $this->mysql->query("SELECT * FROM pages WHERE ".$select." AND lang = '".$this->lang()."'");
			$r = $stmt->fetch_assoc();

			return $r;
		}




		public function link($pageid) {
			if ( $r = $this->page_name($pageid) )
				return $this->url( $r );
		}

		public function link_a($adress) {
			return $this->url( substr($adress, 1) );
		}

		public function link_to($data, $r = "") {

			if ( is_array($data) ) {
				foreach ($data as $key => $value) {
					$r .= "/".$value;
				}

				return $this->url( substr($r, 1) );
			}
		}

		

		public function page_($pageid) {
			if ( $r = $this->page_name($pageid) )
				return $r;
		}

		public function page_name($pageid, $result = "", $re = "") {
			$PD = $this->pagedata();

			if ( is_array($pageid) ) {
				foreach ($pageid as $key => $value) {
					$stmt = $this->mysql->query("SELECT name FROM pages WHERE page = $value AND lang = '".$PD["lang"]."'");
					$r = $stmt->fetch_assoc();

					$re .= $r["name"]."/";
				}

				$result = $re;
			}
			else {
				$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = $pageid");
				$r = $stmt->fetch_assoc();
				
				$result = $r["name"];
			}

			return $result;
		}

		public function url($data) {
			return 'http://'.$_SERVER["HTTP_HOST"].'/'.$data;
		}
		
		public function url_data($data) {
			return 'http://'.$_SERVER["HTTP_HOST"].'/'.$data;
		}


		public function random_chars($pocet_znakov, $return = "") {
			$skupina_znakov = "abcdefghijklmopqrstuvwxyz01234567890123456789012345678901234567890";

			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}



		public function chv($data='') {
			return trim( $data );
		}









		public function file_size($bytes)
		{
		    $bytes = floatval($bytes);
		        $arBytes = array(
		            0 => array(
		                "UNIT" => "TB",
		                "VALUE" => pow(1024, 4)
		            ),
		            1 => array(
		                "UNIT" => "GB",
		                "VALUE" => pow(1024, 3)
		            ),
		            2 => array(
		                "UNIT" => "MB",
		                "VALUE" => pow(1024, 2)
		            ),
		            3 => array(
		                "UNIT" => "KB",
		                "VALUE" => 1024
		            ),
		            4 => array(
		                "UNIT" => "B",
		                "VALUE" => 1
		            ),
		        );

		    foreach($arBytes as $arItem)
		    {
		        if($bytes >= $arItem["VALUE"])
		        {
		            $result = $bytes / $arItem["VALUE"];
		            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
		            break;
		        }
		    }
		    return $result;
		}
	}