<?php
	$GD = new GLOBALDATA();


	class GLOBALDATA {

		protected static $instance;
		public $m;
		
		public $online = false;
		public $userdata;

		private $user;
		private $diyID;

		public $CHR;
		public $U;

		public function __construct() {
			$this->m = SQL::init();
			$this->CHR = CHR::init();

			//$this->basketid = isset($_COOKIE["mm-uid"]) ? $_COOKIE["mm-uid"] : $this->uid();
			$this->userdata = $this->USERDATA();
			//$this->U = $this->USERDATA_O();

			if ( $this->is_logged() == true ) {
				$this->online = $this->onlinestatus();
				$this->user = $this->USERDATA();
			}
		}
		
		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new GLOBALDATA();
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


		/*public function text($id) {
			
			$stmt = $this->m->q("SELECT sk FROM strings WHERE string = ".$id);
			$r = $stmt->fetch_assoc();

			if ( !$r["sk"] )
				return 'Text „'.$id.'“ neexistuje';
			else
				return $r["sk"];
		}*/
		public function text($id) {

			if ( $id ) {
				$stmt = $this->m->q("SELECT sk FROM strings WHERE string = '".$id."'");
				$r = $stmt->fetch_assoc();

				if ( !$r ) {


					$stmt = $this->m->q("SELECT sk FROM strings_added WHERE string = $id");
					$r = $stmt->fetch_assoc();

					return $r[ "sk" ];
				}
				else
					return $r[ "sk" ];
			}
			
		}

		public function text_($id) {
			
			$stmt = $this->m->q("SELECT sk FROM text WHERE id = $id");
			$r = $stmt->fetch_assoc();

			if ( !$r["sk"] )
				return 'Text „'.$id.'“ neexistuje';
			else
				return $r["sk"];
		}


		public function page_($pageid) {
			if ( $r = $this->page_name($pageid) )
				return $r;
		}

		public function USERDATA( $data = "", $r = false ) {
			if ( $data ) {
				$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = '".$data."'");
				
				$r = $stmt->fetch_object();
			} else {

				if ( $this->online() ) {
					$stmt = $this->m->q("SELECT user FROM online WHERE token = '".$this->online()."'");
					$user = $stmt->fetch_object();

					$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = '".$user->user."'");
					
					$r = $stmt->fetch_object();
				}
			}

			return $r;
		}

		public function USERDATA_O( $id = "" ) {
			/*if ( $data ) {
				$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = '".$id."'");
				
				if ( $r = $stmt->fetch_object() )
					return $r;
				else
					return false;
			} else {

				if ( $this->online() ) {
					$stmt = $this->m->q("SELECT user FROM online WHERE token = '".$this->online()."'");
					$user = $stmt->fetch_object();

					$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = '".$user->user."'");
					
					return $stmt->fetch_object();
				}	
			}	*/
			//$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = ".$id);
			//return $stmt->fetch_object();
		}
/*
		public function SHOPDATA_O( $ID, $select = "", $r = "") {
			//$user = $this->user;

			if ( $select ) {
			
				$stmt1 = $this->m->q("SELECT $select FROM eshops WHERE shopID = ".$ID);
				$r = $stmt1->fetch_object();

				return $r->$select;
			} else {
				$stmt2 = $this->m->q("SELECT * FROM eshops WHERE shopID = ".$ID);
				$r = $stmt2->fetch_object();
			}
			
			return $r;
		}
*/
		public function ITEMPROFILE_O($select, $id) {
			
			$stmt = $this->m->q("SELECT * FROM navody WHERE $select = '".$id."'");
			return $stmt->fetch_object();
		}


		public function onlinestatus($on = false) {
			if ( $this->online() ) {

				$stmt = $this->m->q("SELECT * FROM online WHERE ip = '".$this->ip()."' AND token = '".$this->online()."'");
				if ( $stmt->fetch_assoc() ) $on = true;
			}

			return $on;
		}


		public function device() {
			return $_SERVER["HTTP_USER_AGENT"];
		}

		public function ip() {
			return $_SERVER["REMOTE_ADDR"];
		}

		public function online() {
			if ( $_SERVER['HTTP_HOST'] != 'deleted' )
				return isset($_COOKIE["mm-online"]) ? $_COOKIE["mm-online"] : "";
			else
				return false;
		}

		public function domain() {
			return  ($_SERVER['HTTP_HOST'] != 'localhost' ) ? $_SERVER['HTTP_HOST'] : false;
		}

		/*public function session() {
			return isset( $_COOKIE["uid"] ) ? $_COOKIE["uid"] : '';
		}*/

		public function session() {
			return session_id();
		}

		public function UD() {
			return array(
				'device'	=> $_SERVER["HTTP_USER_AGENT"],
				'ip'		=> $_SERVER["REMOTE_ADDR"],
				'session'	=> isset( $_COOKIE["mm"] ) ? $_COOKIE["mm"] : '',
				'online'	=> isset( $_COOKIE["mm-online"] ) ? $_COOKIE["mm-online"] : '');
		}

		public function MD() {
			return array( 
				'username' 		=> 15, 
				'password' 		=> 12, 
				'result-list'	=> 8);
		}

		/*public function TIME() {
			return array(
				'logintime'		=> '+1 week',
				'inactive' 		=> '-1 day',
				'default'		=> '+1 year',
				'logout'		=> '-1 hour');
		}*/

		public function FD() {
			return array(
				'img-types'		=> array('image/png', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/jpg'));
		}

		public function day($day, $r = '') {
			
			switch ($day) {
				case 1: $r = $this->text(305); break;
				case 2: $r = $this->text(306); break;
				case 3: $r = $this->text(307); break;
				case 4: $r = $this->text(308); break;
				case 5: $r = $this->text(309); break;
				case 6: $r = $this->text(310); break;
				case 7: $r = $this->text(311); break;

			}

			return $r;
		}


		public function link($pageid) {
			
			if ( $r = $this->page_name($pageid) )
				return $this->url( $r );
		}

		public function link_ofi($pageid) {
			
			if ( $r = $this->page_name_ofi($pageid) )
				return $this->ofiurl( $r );
		}

		public function link_a($adress) {
			return $this->url( substr($adress, 1) );
		}

		public function link_to($data, $r = "") {

			if ( is_array($data) ) {
				foreach ($data as $key => $value) {
					$r .= $value;
				}

				//return $this->url( substr($r, 1) );
				return $this->url( $r );
			} else
				return $this->url( $data );
		}

		public function destroy_cookie($cookie) {
			setcookie($cookie, "", time()-3600, "/", $this->domain());
		}

		public function page_name($pageid, $r = "") {
			
			if ( is_array($pageid) ) {
				foreach ($pageid as $key => $value) {
					$q = $stmt = $this->m->q("SELECT name FROM pages WHERE page = '$value' AND lang = '".$this->CHR->LANG."'");
					$re = $stmt->fetch_object();

					$r .= $re->name."";
				}
			}
			else {
				//$q = $stmt = $this->m->q("SELECT name FROM pages WHERE page = $pageid AND lang = '".$this->CHR->LANG."'");
				$stmt = $this->m->q("SELECT * FROM pages WHERE page = ".$pageid);
				$re = $stmt->fetch_object();
				
				if ( $re )
					$r = $re->name.'';
			}

			return $r;
		}


		public function page_name_ofi($pageid) {
			
			if ( $pageid ) {
				$q = $stmt = $this->m->q("SELECT url FROM pages WHERE page = $pageid AND lang = 'sk'");
				$r = $stmt->fetch_object();

				$result = $r->url.'';
			}

			return $result;
		}





		public function url($data) {
			global $PD;

			$data = $data == DEFAULT_PAGE ? "" : $data;

			$return = 'http://'.$_SERVER["HTTP_HOST"].'/'.$data;

			return $return;
		}
		
		public function url_data($data) {

			return 'http://'.$_SERVER["HTTP_HOST"].'/'.$data;
		}

		public function suburl_data($data) {

			return 'http://'.SUBDOMAIN_FILES.'/'.$data;
		}

		public function suburl_data2($data) {

			return 'http://'.SUBDOMAIN_FILES2.'/'.$data;
		}

		public function suburl_cms($data) {

			return 'http://'.SUBDOMAIN_CMS.'/'.$data;
		}

		public function ofiurl($data) {
			global $PD;

			$return = 'http://'.OFI_PAGE.'/'.$data;

			return $return;
		}

		public function random_text($array) {
			return $array[ rand(0, count($array) - 1 ) ];
		}



		public function discount($price, $discount) {
			return number_format( $price * ( (100 - $discount) / 100), 2);
		}



		public function filter($data, $type) {
			$filter = "";

			/*
				1 - nadavky
				2 - rezervovane
				3 - vseobecne
			*/

			if ( strlen($data) >= 1 ) {
				$stmt = $this->m->q("SELECT * FROM filter WHERE type = $type");
				
				foreach ($this->m->result2() as $key => $value) {
					$filter .= $value["text"]."|";
				}
				
				if ( $filter ) {
					if ( preg_match('/'.$filter.'^$/i', mb_strtolower($data, "UTF8") ) )
						return true;
				}
			}
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

		public function text_transform($data) {
			$nazov = $data;

			$nazov = preg_replace('~[^\\pL0-9_]+~u', '-', $nazov);
			$nazov = trim($nazov, "-");
			$nazov = iconv("utf-8", "us-ascii//TRANSLIT", $nazov);
			$nazov = strtolower($nazov);
			$nazov = preg_replace('~[^-a-z0-9_]+~', '', $nazov);

			return $nazov;
		}



		public function random_chars($pocet_znakov) {
			$skupina_znakov = "abcdefghijklmopqrstuvwxyz01234567890123456789012345678901234567890";
			$return = "";
			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}

		public function random_numbers($pocet_znakov) {
			$skupina_znakov = "0123456789";
			$return = "";
			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}

		public function random_num_oddo($od, $do, $return = "") {
			$skupina_znakov = "0123456789";

			$pocet_znakov = mt_rand($od, $do);

			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}

		public function random_chars_oddo($od, $do, $return = "") {
			$skupina_znakov = "abcdefghijklmopqrstuvwxyz01234567890123456789012345678901234567890";

			$pocet_znakov = mt_rand($od, $do);

			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}
		
		public function random_chars_from_group($key_group, $pocet_znakov, $return = "") {
			$skupina_znakov = $key_group;

			$pocet_znaku = strlen($skupina_znakov) - 1;

			for ($i=0; $i < $pocet_znakov; $i++) {
				$return .= $skupina_znakov[mt_rand(0,$pocet_znaku)];
			}

			return $return;
		}
		
		public function hash($value, $secure) {

			if ( isset($secure) )
				return hash_hmac('sha512', $value, $secure);
			else
				return hash('sha512', $value);
		}


		/*function create_session($name) {
			session_name($name);


			$cookie = session_get_cookie_params();
			
	    	session_set_cookie_params($cookie["lifetime"], $cookie["path"], $cookie["domain"], false, true);

			session_start();
			//session_regenerate_id();
		}*/



		public function adddate($data, $time = true) {
			$date = $data;

			$den = date("d", $date);
			$mesiac = date("n", $date);

			$mesiac = $this->mesiac($den, $mesiac);

			$t = $time == true ? " ".$this->text(24)." ".date("H:i", $date) : "";

			switch ( $this->CHR->LANG ) {
				case 'en':
					$return = ucfirst($mesiac).date(" d,", $date).date(" Y", $date).$t;
					break;
				default:
					$return = date("j. ", $date).$mesiac.date(" Y", $date).$t;
					break;
			}
			
			return $return;
		}

		public function EDITOR($text, $r = '') {
			$search  = array( '#icon1' );
			$replace = array( '<i class="fa fa-quote-right" aria-hidden="true"></i>' );


			return str_replace($search, $replace, $text);

			//return $r;
		}




		public function generate_addtobasket($IP, $BA, $r = '') {
			$BD = "";

			$text = $this->text(294);
			$iconn = "iBasket";
			//var_dump($BA);
			if ( $IP && $BA ) {
				if ( strpos($BA->content, ".".$IP->id.".") !== false ) {
					$BD = $this->basket_data($BA);

					if ( $BD['d'] ) {
						$text = '<span class="ii iCheck1 checki" aria-hidden="true"></span>'.sprintf($this->text(511), '<span>'.$BD['d'][$IP->id]["quantity"].'</span>');

						$iconn = "iBasketF";
					}/* else {
						$text = $this->text(294);
						$iconn = "iBasket";
					}*/
				}
			}
			
			/*else {
				$text = $this->text(294);
				$iconn = "iBasket";
			}*/



			//if ( $BD ) {
	

				$defval = isset($BD['d']) ? $BD['d'][$IP->id]["quantity"] : 1;

				$price = $IP->discount ? $this->discount( $IP->price, $IP->discount) : $IP->price;

			 	if ( $IP->availability == 8 ) {
					$r =  '

					<div class="ipr-basket">
						<div class="ipr-basket-content">
							<a href="#" class="addItem addDefault askItem">
								<i class="ii iWarn add-icon" aria-hidden="true"></i>
								<div class="addi-butt">'.$this->text(295).'</div>
							</a>
						</div>
					</div>
					';
				} else {
					$mobile = '
					<div class="mobile-quantity">
						<button href="#" type="button" class="select-switcher showw" data-target=".select-'.$IP->id.'"><span class="ii iDown"></span></button>
						
						<div class="selectdata select-'.$IP->id.'" data-target="#basketquantity-'.$IP->id.'">
							<div class="selecthide"><button href="#" type="button" class="select-switcher" data-target=".select-'.$IP->id.'"><span class="ii iUp"></span></button></div>
							'.$this->mobile_availability($IP->quantity).'
						</div>
					</div>
					';
					$r =  '
					<div class="ipr-basket">
						<div class="ipr-basket-content">
							<div class="addItem addType1">
								<span class="ii '.$iconn.' add-icon" aria-hidden="true"></span>
								<div class="add-body">
									<div class="addb addb-3" data-maxquantity="'.$IP->quantity.'">
										<button type="button" class="qButton qChange qUp" id="qUp" data-target="basketquantity-'.$IP->id.'"><i class="fa fa-plus" aria-hidden="true"></i></button>
										<button type="button" class="qButton qChange qDown" id="qDown" data-target="basketquantity-'.$IP->id.'"><i class="fa fa-minus" aria-hidden="true"></i></button>

										'.$mobile.'
									</div>
									<div class="addb addb-1">
										<div class="addb-body">
											<input type="number" class="dn" value="'.$IP->id.'" name="basketitemid" id="basketitemid-'.$IP->id.'" autocomplete="off" disabled="disabled">
											<input type="number" class="addi-count" min="1" max="'.$IP->quantity.'" value="'.$defval.'" autocomplete="off" name="basketquantity" id="basketquantity-'.$IP->id.'" disabled="disabled">
											<input type="number" class="dn" value="'.$price.'" name="basketitemprice" id="basketitemprice-'.$IP->id.'" autocomplete="off" disabled="disabled">
										</div>
									</div>
									<div class="addb addb-2">
										<a href="#" class="addi-button" id="additem_addbasket" data-lib="basket" data-event="addtobasket" data-data="'.$IP->id.'">'.$text.'</a>
									</div>
									
									<div class="cleaner"></div>
								</div>
								
							</div>
						</div>
					</div>
					';
				}
			
			//<input type="number" class="dn" value="'.$IP->id.'" id="basketitemid" autocomplete="off" disabled="disabled">
			return $r;
		}

		public function mobile_availability($count, $r = '') {

			for ($i=0; $i <= $count; $i++) { 
				$r .= '<button type="button" class="option">'.$i.'</button>';
			}

			return $r;
		}
		public function basket_data($BA, $inBag = false, $r = '') {
			$deleted = '';

			if ( !empty($BA->content) ) {
				$basket = $BA->content;

				$rem = $repair = '';

				if ( strrpos($basket, "#") ) {
					foreach ( array_filter( explode("#", $basket) ) as $key => $value) {
						$detail = explode("*", $value);

						$id = str_replace(".", "", $detail[0]);

						if ( $check = $this->check_basketdata($id, $detail[1]) ) {
							$rem = $detail[0]."*".$detail[1].'*'.$detail[2]."#";

							$repair = str_replace($rem, '', $basket);

							$this->m->q("UPDATE basket SET content = '$repair' WHERE basket = '$BA->basket'");

							$deleted .= $this->not_available($id, $check);
						} else {
							if ( $this->check_all_before_publish( $id ) != false )
								$r[$id] = array("id" => $id, "quantity" => $detail[1], "price" => $detail[2]);
						}	
					}
				} else {
					$ba = explode("*", $basket);

					$id = str_replace(".", "", $ba[0]);

					if ( $check = $this->check_basketdata($id, $ba[1]) ) {
						$rem = $ba[0]."*".$ba[1].'*'.$ba[2];

						$re = str_replace($rem, '', $basket);
						$repair = empty($re) ? "NULL" : "'".$re."'";

						$this->m->q("UPDATE basket SET content = $repair WHERE basket = '$BA->basket'");

						$deleted .= $this->not_available($id, $check);
					} else {
						if ( $this->check_all_before_publish( $id ) != false )
							$r[$id] = array( "id" => $id, "quantity" => $ba[1], "price" => $ba[2] ); 
					}
				}

				//if ( $r )

				return array( 'd' => $r, 'error' => $deleted ? '<div class="itmdel"><div class="itmdel-body">'.$deleted.'</div></div>' : '' );
			}

			
		}


		public function check_basketdata($id, $quantity, $r = false) {
			/*$stmt = $this->m->q("SELECT id FROM navody WHERE id = '$id' AND public = 1 AND availability != 8 AND quantity <= $quantity");
			return $stmt->fetch_object();*/

			$stmt = $this->m->q("SELECT availability, quantity FROM navody WHERE id = '$id' AND public = 1");

			if ( $d = $stmt->fetch_object() ) {

				if ( $d->quantity < $quantity )
					$r = 876;
				else if ( $d->availability == 8 )
					$r = $this->random_text( array(875, 877) );
			}
			
			return $r;
		}

		public function not_available($id, $text, $r = '') {
			if ( $id ) {
				$stmt = $this->m->q("SELECT url, title FROM navody WHERE id = '".$id."'");

				$d = $stmt->fetch_object();

				return '<div class="itmremoved"><p>'.sprintf($this->text($text), '<a href="'.$this->url_data($d->url).'" target="_blank">'.$this->mb_ucfirst($d->title).'</a>').'</p></div>';
			}
			
		}

		public function check_all_before_publish($id, $inputs = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11), $save = false, $r = true ) {
			
			foreach ($inputs as $key => $value) {

				$stmt = $this->m->q("SELECT * FROM cms_inputs WHERE form = ".$value." AND required = 1");

				foreach ( $this->m->resultO() as $key2 => $value2) {
					//$r = true;
					$stmt2 = $this->m->q("SELECT $value2->name FROM navody WHERE id = '$id'");

					if ( $data = $stmt2->fetch_object() ) {

						$na = $value2->name;

						if ( empty( $data->$na ) )
							$r = false;
					}
				}
			}

			if ( $r == false ) {
				$this->m->q("UPDATE navody SET public = '0' WHERE id = '$id'");
			}
			
			return $r;
		}




		public function breadcrumb($page = "", $data = "", $r = '') {
			
			$lis = 	' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
			$it = 	' itemscope itemtype="http://schema.org/Thing" itemprop="item"';
			$pos =	'<meta itemprop="position" content="2" />';

			switch ($page) {
				case 5:
					$lang = $this->CHR->LANG;

					//$r = '<li>'.$this->text(277).'</li>';
					$r = '<li'.$lis.'><a href="'.$this->link_to( $data[0]->$lang ).'"'.$it.'><span itemprop="name">'.$this->text($data[0]->text).'</span></a> <span class="nopp">( '.$data[1].' '.$this->word_ending($data[1], array(634, 635, 635) ).' )</span>'.$pos.'</li>';
					break;

				case 500:
				case 501:
				case 502:
				case 503:
				case 504:
				case 505:
				case 506:
				case 507:
					$r .= '<li'.$lis.'><a href="'.$this->link_to( $_GET["p"] ).'"'.$it.'><span itemprop="name">'.$this->CHR->PD->title.'</span></a>'.$pos.'</li>';
					break;

				default:
					//$r .= '<li><a href="'.$this->GD->link_to($value->$L).'">'.$this->GD->text( $value->text )."</a></li>";
					break;
			}

			return '
				<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
					<li'.$lis.'><i class="ii iHome1" aria-hidden="true"></i><a href="'.$this->link(1).'"'.$it.'><span itemprop="name">'.$this->text(627).'</span></a><meta itemprop="position" content="1" /></li>
					'.$r.'
				</ul>
			';
		}
/*
		public function USERDATA() {
			
			$UD = $this->UD();
			
			$stmt = $this->m->q("SELECT user FROM online WHERE token = '".$UD["online"]."'");
			$user = $stmt->fetch_assoc();

			$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = '".$user["user"]."'");
			
			return $stmt->fetch_assoc();
		}
*/
		
		public function is_logged($r = false) {
			
			$UD = $this->UD();

			if ( isset($_COOKIE["mm-online"]) ) {

				$stmt = $this->m->q("SELECT * FROM online WHERE ip = '".$UD["ip"]."' AND token = '".$_COOKIE["mm-online"]."'");
				$r = $stmt->fetch_assoc();

				if ( $r ) 
					$r = true;
				else
					setcookie("mm-online", "", time()-3600, "/", $this->domain());

			}

			return $r;
		}

		public function is_inactive() {
			
			$UD = $this->UD();
			//$TIME = $this->TIME();

			$stmt = $this->m->q("SELECT last_activity FROM online WHERE token = '".$UD["online"]."' AND ip ='".$UD["ip"]."'");

			if ( $r = $stmt->fetch_object() ) {
				if ( $r->last_activity > strtotime(TIME_LOGOUT) )
					$re = false;
				else
					$re = true;
			}
				

			return $re;
		}

		public function refresh_activity() {
			
			$UD = $this->UD();
			
			$time = time();

			$stmt = $this->m->q("SELECT user FROM online WHERE token = '".$UD["online"]."'");
			$user = $stmt->fetch_assoc();

			if ( $user ) {
				$stmt = $this->m->q("UPDATE online SET last_activity = $time WHERE token = '".$UD["online"]."' AND ip = '".$UD["ip"]."'");
				$stmt = $this->m->q("UPDATE uzivatelia SET last_activity = $time, ip = '".$UD["ip"]."' WHERE userID = ".$user["user"]."");
			}
		}

		public function logout() {
			
			$UD = $this->UD();

			if ( $stmt = $this->m->q("DELETE FROM online WHERE token = '".$UD["online"]."' AND ip = '".$UD["ip"]."'") ) {
				setcookie("mm-online", "", time()-3600, "/", $this->domain());
				return true;
			}
		}


		public function last_systemID($default, $table, $r = '') {
			

			$stmt = $this->m->q("SELECT system_id FROM $table ORDER BY id DESC LIMIT 1");
			$r = $stmt->fetch_assoc();

			if ( !$r )
				$r["system_id"] = $default;

			return $r["system_id"];
		}

		public function random_color() {
			$colors = array("838b93", "969900");

			return $colors[ rand(0, count($colors) - 1) ];
		}

		public function logout_message($message) {

			switch ($message) {
				case 1:
					return '<div>Systém je zablokovaný z dôvodu neaktivity. <span>Je nutné sa znova prihlásiť!</span> </div> <a href="'.$this->link(1).'">- Pokračovať -</a>';
					break;
				case 2:
					return '<div>Systém je zablokovaný. <span>Je nutné sa prihlásiť!</span> </div> <a href="'.$this->link(1).'">- Pokračovať -</a>';
					break;
				case 3:
					return $this->text(166);
					break;
			}
			
		}

		public function date_($data, $time = true, $type = '') {
			
			$date = $data;

			$den = date("d", $date);
			$mesiac = date("n", $date);

			$mesiac = $this->mesiac($den, $mesiac);

			/*switch ($this->CHR->LANG) {
				case 'en':
					$return = ucfirst($mesiac).date(" d,", $date).date(" Y")." ".$GD->text(24)." ".date("H:i", $date);
					break;
				default:
					$return = date("d. ", $date).$mesiac.date(" Y")." ".$GD->text(24)." ".date("H:i", $date);
					break;
			}*/
			$timeText = $time == true ? $this->text(24)." ".date("H:i", $date) : "";

			if ( date("d.m.Y", $date) == date("d.m.Y") ) 
				$r = $this->text(689)." ".$timeText;
			else
				$r = date("j. ", $date).$mesiac.date(" Y")." ".$timeText;
			
			switch ($type) {
				case 1:
					$r = date("j. ", $date).$mesiac.date(" Y");
					break;
				case 2:
					$r = date("j. ", $date).$mesiac.date(" Y")." ".$timeText;
					break;
			}

			return $r;
		}

		public function mesiac($pred, $data) {
			
			$normal = array(
				"1" => $this->text(7),
				"2" => $this->text(8),
				"3" => $this->text(9),
				"4" => $this->text(10),
				"5" => $this->text(11),
				"6" => $this->text(12),
				"7" => $this->text(13),
				"8" => $this->text(14),
				"9" => $this->text(15),
				"10" => $this->text(16),
				"11" => $this->text(17),
				"12" => $this->text(18));

			$plural = array(
				"1" => $this->text(7)."a",
				"2" => $this->text(8)."a",
				"3" => $this->text(19),
				"4" => $this->text(10)."a",
				"5" => $this->text(11)."a",
				"6" => $this->text(12)."a",
				"7" => $this->text(13)."a",
				"8" => $this->text(14)."a",
				"9" => $this->text(20),
				"10" => $this->text(21),
				"11" => $this->text(22),
				"12" => $this->text(23));

			if ( $pred == 1 )
				$return = $normal["$data"];
			else
				$return = $plural["$data"];

			return $return;
		}

		public function check_email($email) {
			// set return value to 0 until the email address has been evaluated 
			$return = 0;
			// Check syntax of the given email address 
			if( (preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) || (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/',$email)) ) 
				{
			// Extraxt domainname from given email address
				list(, $domain) = explode('@', $email);
				$domain = trim($domain);
				// Fill in your personal api key (see http://www.block-disposable-email.com/register.php)  
				$key     = '4ca85a9dd9424f34577f3c71bc63d894';
				$request = 'http://check.block-disposable-email.com/api/json/'.$key.'/'.$domain; 
				if ($response = @file_get_contents($request))
					{
					$dea = json_decode($response);
					// Analyse the domain_status response only if the request_status was successful
					if ($dea->request_status == 'success')
						{
						if ($dea->domain_status == 'ok') $return = 1;
						if ($dea->domain_status == 'block') $return = 0;
						}
					// If MX checks fail also return 0
					elseif ($dea->request_status == 'fail_input_domain') $return = 0;
					// If the API query return some other response accept the given address anyway. 
					// Too high risk to lose one customer! 
					else $return = 1;
					}
				// Wenn Website down ist Registrierung auch zulassen
			// If the service is currently down and the api does not respond also accept the given email address
				else $return = 1;
			}
			return $return;
		}

		public function validateEmail($email) {
			// SET INITIAL RETURN VARIABLES

			$emailIsValid = FALSE;

			// MAKE SURE AN EMPTY STRING WASN'T PASSED

			if ( !empty($email )) {
				// GET EMAIL PARTS

				$domain = ltrim(stristr($email, '@'), '@');
				$user   = stristr($email, '@', TRUE);

				// VALIDATE EMAIL ADDRESS

				if ( empty($user) && !empty($domain) && checkdnsrr($domain) ) {
					$emailIsValid = TRUE;
				}
			}

			// RETURN RESULT

			return $emailIsValid;
		}

		public function form_types($type) {
			

			$stmt = $this->m->q("SELECT * FROM form_types WHERE id = $type");
			$r = $stmt->fetch_assoc();

			return $r;
		}

		public function generate_navigation($r = '') {
			$li = "";

			$stmt = $this->m->q("SELECT * FROM pages WHERE menu = '1'");
			
			foreach ( $this->m->result2() as $key => $value ) {
				
				$active = ($_GET["p"] == $value["name"]) ? ' id="navA"' : "";
				$icon = $value["icon"] ? $value["icon"] : "";
				$text = $value["text"] ? $value["text"] : 0;

				$li .= '<li'.$active.'><a href="'.$this->link($value["page"]).'"><i class="fa '.$icon.'" aria-hidden="true"></i>'.$this->text($text).'</a></li>';
			}

			return '
			<ul>
				'.$li.'
			</ul>
			';
		}
		/*
		<ul>
								<li id="navA"><a href="'.$GD->link(1).'"><i class="fa fa-home" aria-hidden="true"></i>'.$GD->text(198).'</a></li>
								<li><a href="#sklad"><i class="fa fa-tags" aria-hidden="true"></i>'.$GD->text(199).'</a></li>
								<li><a href="#objednakvy"><i class="fa fa-book" aria-hidden="true"></i>'.$GD->text(200).'</a></li>
							</ul>
							*/
		public function login_effect() {
			global $mysql, $USER, $UD;

			$ip = $_SERVER["REMOTE_ADDR"];
			//$name = $USER();

			$inactive_time = strtotime("-10 second");

			$stmt = $this->m->q("SELECT login_time FROM online WHERE token = '".$UD["online"]."' AND ip ='$ip'");
			$last_activity = $stmt->fetch_assoc();

			if ( $last_activity["login_time"] > $inactive_time )
				return '
			<div class="results-line" id="login-complete">
				<div class="container">
					<div class="result">
						<span class="result-body">
							<div class="icon-login i i20"></div> 
							<span class="result-text">
								<b>Prihlásenie bolo úspešné.</b> <br> 
								„ '.ucfirst($USER["firstname"]).' '.ucfirst($USER["lastname"]).' “
							</span>
						</span>
					</div>
				</div>
			</div>
			';
		}

		public function update_version($new_version, $new_time) {
			

			$stmt = $this->m->q("SELECT id, verzia FROM changelog ORDER BY id DESC LIMIT 1");
			
			if ( $last_version = $stmt->fetch_assoc() )
				$stmt = $this->m->q("UPDATE system_info SET version = '$new_version', last_update = '$new_time' WHERE id = 1");
			else
				$stmt = $this->m->q("UPDATE system_info SET version = '1.0.0', last_update = '".time()."' WHERE id = 1");

		}

		function no_data() {
			$texts = array(160, 161, 162);
			
			return $this->text( $texts[ rand(0, count($texts)-1) ] );
		}



		public function og_image($data = "", $r = '') {
			switch ($this->CHR->PD->page) {
				case 16:
					$files = $data->file_list ? $data->file_list : $data->files;

					foreach ( array_filter( explode("#", $files)) as $key => $value) {

						$imgLink = $this->generate_pictureUrl( $value, false );
						//$imgSize = $this->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
						$r .= '<meta property="og:image" content="'.$imgLink["url"].'" />'."\n";
					}
					break;
				
				default:
					/*$stmt = $this->m->q("SELECT id, create_date, files, file_list FROM navody WHERE public = 1 ORDER BY RAND()LIMIT 5");

					if ( $data = $this->m->resultO() ) {

						foreach ( $data as $key => $v) {
							$files = $v->file_list ? $v->file_list : $v->files;

							foreach ( array_filter( explode("#", $files)) as $key => $value) {

								if ( $key == 1 ) {
									$imgLink = $this->generate_pictureUrl( $value, false );
									//$imgSize = $this->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
									$r .= '<meta property="og:image" content="'.$imgLink["url"].'" />'."\n";
								}
								
							}

						}
					}*/
					$stmt = $this->m->q("SELECT og_images FROM settings");

					if ( $data = $stmt->fetch_object() ) {

						if ( isset($data->og_images) ) {
							foreach ( array_filter( explode("#", $data->og_images)) as $key => $v) {

								$imgLink = $this->generate_pictureUrl( $v, false );

								$r .= '<meta property="og:image" content="'.$imgLink["url"].'" />'."\n";
							}
						}
						

						
					}

					break;
			}

			return $r;
		}
/*
		public function og_defaultimages($r = '') {

			$stmt = $this->m->q("SELECT files, file_list FROM navody ORDER BY create_date DESC");

			if ( $data = $this->m->resultO() ) {


				foreach ($data as $key => $v) {
					$files = $v->file_list ? $v->file_list : $v->files;

					foreach ( array_filter( explode("#", $files)) as $key => $value) {

						if ( $key == 0 ) {
							$imgLink = $this->generate_pictureUrl( $value, false );
							//$imgSize = $this->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
							$r .= '<meta property="og:image" content="'.$imgLink["url"].'" />'."\n";
						}
						
					}
					break;
				}
			}
					


			return $r;
		}
*/


		public function category($id) {
			$stmt = $this->m->q("SELECT $this->CHR->LANG FROM kategorie WHERE category = $id");
			$r = $stmt->fetch_object();

			return $r->$this->CHR->LANG;
		}

		public function category_data($id, $select) {
			$stmt = $this->m->q("SELECT * FROM kategorie WHERE category = ".$id);
			$r = $stmt->fetch_assoc();

			return $r[$select];
		}
		
		public function category_data_by_name($data) {
			$stmt = $this->m->q("SELECT * FROM kategorie WHERE ".$this->CHR->LANG." = '$data'");

			return $stmt->fetch_object();
		}

		public function category_data_from_table($table, $id) {
			$stmt = $this->m->q("SELECT * FROM $table WHERE category = ".$id);
			$r = $stmt->fetch_object();

			return $r;
		}

		public function hashtag_data($tag) {
			$stmt = $this->m->q("SELECT * FROM tagy WHERE ".$this->CHR->LANG." = '$tag'");

			return $stmt->fetch_assoc();
		}

/*
		public function generate_from_galery($data, $galery_id, $type, $re = "", $list = "", $listt = "") {
			$user = $this->userdata;

			if ( $data ) {
				foreach ( $this->file_data_from_dbarray( $data ) as $key => $value) {
					$file = $value["folder"].'/'.$value["filename"];

					$list .= '<li class="tempo-img tempo-reserved ui-sortable-handle" id="'.$value["filename"].'"> <img src="'.$this->suburl_data(FILES_LOC.DEF_UPLOAD_FOLDER.$file.".".$value["file_type"]).'" alt="'.$value["filename_orig"].'" title="'.$value["id"].' / '.$value["filename_orig"].' / '.date("d.m.Y H:i", $value["create_date"]).'"> </li>';
				}

				$list .= '<li class="placeholder" style="display: none;">Neboli nájdené žiadne fotografie.</li>';
			} else
				$list = '<li class="placeholder">Neboli nájdené žiadne fotografie.</li>';

			return $re = '
				<ul class="step-added-files drag-boxx" data-galery="'.$galery_id.'" data-type="'.$type.'" id="files-galery">
					'.$list.'
				</ul>
				';
		}

*/
		/*
		public function generate_unused_files($re = "", $list = "", $listt = "") {
			$user = $this->userdata;

			$this->check_before_manipulation();

			$stmt = $this->m->q("SELECT * FROM files_userlists WHERE user = ".$user["userID"]);
			$data = $stmt->fetch_assoc();

			if ( $data["unused_files"] ) {
				foreach ( $this->file_data_from_dbarray( $data["unused_files"] ) as $key => $value) {
					$file = $value["folder"].'/'.$value["filename"];

					$list .= '<li class="tempo-img tempo-reserved sort ui-sortable-handle" id="'.$value["filename"].'"> <img src="'.$this->suburl_data(FILES_LOC.DEF_UPLOAD_FOLDER.$file.".".$value["file_type"]).'" alt="'.$value["filename_orig"].'" title="'.$value["id"].' / '.$value["filename_orig"].' / '.date("d.m.Y H:i", $value["create_date"]).'"> </li>';
				}

				$list .= '<li class="placeholder" style="display: none;">Neboli nájdené žiadne fotografie.</li>';
			} else
				$list = '<li class="placeholder">Neboli nájdené žiadne fotografie.</li>';

			return $re = '
				<ul class="plist" id="files-unused">
					'.$list.'
				</ul>
				';
		}

		public function generate_all_files($re = "", $list = "", $listt = "") {
			$user = $this->userdata;

			$this->check_before_manipulation();

			$stmt = $this->m->q("SELECT * FROM files");
			$data = $stmt->fetch_assoc();

			if ( $data["id"] ) {
				foreach ( $references = $this->m->result2() as $key => $value) {
					$file = $value["folder"].'/'.$value["filename"];

					$list .= '<li class="tempo-img tempo-reserved sort ui-sortable-handle" id="'.$value["filename"].'"> <img src="'.$this->suburl_data(FILES_LOC.DEF_UPLOAD_FOLDER.$file.".".$value["file_type"]).'" alt="'.$value["filename_orig"].'" title="'.$value["id"].' / '.$value["filename_orig"].' / '.date("d.m.Y H:i", $value["create_date"]).'"> </li>';
				}

				$list .= '<li class="placeholder" style="display: none;">Neboli nájdené žiadne fotografie.</li>';
			} else
				$list = '<li class="placeholder">Neboli nájdené žiadne fotografie.</li>';

			return $re = '
				<ul class="plist" id="files-unused">
					'.$list.'
				</ul>
				';
		}*/
/*
		public function generate_filegaleries($userid = "", $re = "", $list = "") {
			
			if ( $userid ) {
				$USER = $userid;
			} else {
				$user = $this->userdata;
				$USER = $user["userID"];
			}
			

			$stmt = $this->m->q("SELECT * FROM our_references ORDER BY id DESC");

			if ( $references = $this->m->result2() ) {
				foreach ( $references as $key => $value) {
					$files = $this->generate_galery_in_table($value["id"], $value["files"], "our_references");
					$tags = $this->generate_tags_in_table($value["id"], $value["tags"]);

					$list .= '

							<tr class="show-b">
								<td class="tac">'.$value["id"].'</td>
								<td class="tac">'.$value["title"].'</td>
								<td class="tac">'.$this->select_location_name( $value["location_id"], false ).'</td>
								<td class="tac">'.date("d.m.Y", $value["date"] ).'</td>
								<td class="tac show-galerr">'.$files["count"].''.$files["galery"].'</td>
								<td class="tac show-galerr">'.$tags["count"].''.$tags["tags"].'</td>
								<td class="tac">'.$this->file_status($value["published"]).'</td>
								<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-galery"></button> </td>
							</tr>

					';
				}

				return $re = '
				<table class="forms-table">
					<tr class="">
						<th class="tac">ID/IČ</th>
						<th class="tac">Názov</th>
						<th class="tac">Miesto výkonu práce</th>
						<th class="tac">Dátum realizácie</th>
						<th class="tac">Fotografie</th>
						<th class="tac">Tagy</th>
						<th class="tac">Zverejnené</th>
						<th class="act-but"></th>
					</tr>

					'.$list.'
					<div class="cleaner"></div>
				</table>';
			} else {
				return '<div class="tac">Neboli pridané žiadne fotogalérie</div>';
			} 
		}



		public function generate_sliders($userid = "", $re = "", $list = "") {
			
			if ( $userid ) {
				$USER = $userid;
			} else {
				$user = $this->userdata;
				$USER = $user["userID"];
			}
			

			$stmt = $this->m->q("SELECT * FROM sliders WHERE user = ".$USER." ORDER BY id DESC");

			if ( $references = $this->m->result2() ) {
				foreach ( $references as $key => $value) {
					$files = $this->generate_galery_in_table($value["id"], $value["files"], "sliders");
					
					$list .= '

							<tr class="show-b">
								<td class="tac">'.$value["id"].'</td>
								<td class="tac">'.$value["name"].'</td>
								<td class="tac">'.$this->slide_type( $value["type"] ).'</td>
								<td class="tac show-galerr">'.$files["count"].''.$files["galery"].'</td>
								<td class="tac">'.$this->file_status($value["published"]).'</td>
								<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-slider"></button> </td>
							</tr>

					';
				}

				return $re = '
				<table class="forms-table">
					<tr class="">
						<th class="tac">ID/IČ</th>
						<th class="tac">Názov</th>
						<th class="tac">Typ zobrazenia</th>
						<th class="tac">Fotografie</th>
						<th class="tac">Zverejnené</th>
						<th class="act-but"></th>
					</tr>

					'.$list.'
					<div class="cleaner"></div>
				</table>';
			} else {
				return '<div class="tac">Neboli pridané žiadne slidre</div>';
			} 
		}


		public function generate_pricelists($userid = "", $re = "", $list = "", $listt = "", $order_data = false ) {
			
			$stmtt = $this->m->q("SELECT orderr FROM pricelists_list WHERE id = '1'");
			
			if ( $order = $stmtt->fetch_assoc() ) {
				$orderr = explode("#", $order["orderr"]);

				foreach ( array_filter($orderr) as $key => $value) {
					$stmt = $this->m->q("SELECT * FROM pricelists WHERE id = ".$value);

					if ( $references = $this->m->result2() ) {
						foreach ( $references as $key => $value) {
							$items = $this->generate_pricelist_in_table($value["items"]);
							
							$listt .= '
							<div data-pricelist="'.$value["id"].'">
								<table class="forms-table">

									<tr class="table-bug">
										<th class="tac"></th>
										<th class="tac"></th>
										<th class="tac"></th>
										<th class="tac"></th>
										<th class="act-but"></th>
									</tr>


									<tr class="show-b">
										<td class="tac">'.$value["id"].'</td>
										<td class="tac">'.$value["name"].'</td>
										<td class="tac show-galerr">'.$items["count"].''.$items["items"].'</td>
										<td class="tac">'.$this->file_status($value["published"]).'</td>
										<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-pricelist"></button> </td>
									</tr>
								</table>
							</div>
							';
						}

					}
				}
			} 

			$stmt = $this->m->q("SELECT * FROM pricelists ORDER BY id DESC");

			if ( $references = $this->m->result2() ) {
				foreach ( $references as $key => $value) {
					$items = $this->generate_pricelist_in_table($value["items"]);
					
					$list .= '
					<div data-pricelist="'.$value["id"].'">
						<table class="forms-table">

							<tr class="table-bug">
								<th class="tac"></th>
								<th class="tac"></th>
								<th class="tac"></th>
								<th class="tac"></th>
								<th class="act-but"></th>
							</tr>


							<tr class="show-b">
								<td class="tac">'.$value["id"].'</td>
								<td class="tac">'.$value["name"].'</td>
								<td class="tac show-galerr">'.$items["count"].''.$items["items"].'</td>
								<td class="tac">'.$this->file_status($value["published"]).'</td>
								<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-pricelist"></button> </td>
							</tr>
						</table>
					</div>
					';
				}

				if ( $listt ) {
					 $rrr = '
							<div class="sortable-table fdd">
									
									<table class="forms-table">
										<tr class="">
											<th class="tac">ID/IČ</th>
											<th class="tac">Názov</th>
											<th class="tac">Položky</th>
											<th class="tac">Zverejnené</th>
											<th class="act-but"></th>
										</tr>

									</table>

									'.$listt.'
							</div>
							';
				} else {
				 $rrr = '
					<div class="sortable-table fdd">
							
							<table class="forms-table">
								<tr class="">
									<th class="tac">ID/IČ</th>
									<th class="tac">Názov</th>
									<th class="tac">Položky</th>
									<th class="tac">Zverejnené</th>
									<th class="act-but"></th>
								</tr>

							</table>

							'.$list.'
					</div>
					';
				}

				return $rrr;
			}  else {
				return '<div class="tac">Neboli pridané žiadné cenníky.</div>';
			}
		}


		public function generate_unused_pricelists_items($userid = "", $re = "", $list = "") {
			
			$data = $this->find_pricelist_items();

			if ( $data["unused"] ) {

				foreach ( $data["unused"] as $key => $r) {

					$stmt = $this->m->q("SELECT * FROM pricelists_items WHERE id = ".$r);
					$value = $this->m->fetch_assoc();
					$discount = $value["discount"] ? $value["discount"]." %" : "NIE";

					$list .= '

							<tr class="show-b">
								<td class="tac">'.$value["id"].'</td>
								<td class="tac">'.$value["name"].'</td>
								<td class="tac">'.$value["size_weight"].'</td>
								<td class="tac">'.$value["size_h"].'</td>
								<td class="tac">'.$value["size_w"].'</td>
								<td class="tac">'.$value["quality"].'</td>
								<td class="tac"><strong>'.$value["price"].'&euro;</strong></td>
								<td class="tac"><strong>'.$discount.'</strong></td>
								<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-pricelistitem"></button> </td>
							</tr>

					';
				}

				return $re = '
				<table class="forms-table">
					<tr class="">
						<th class="tac">ID/IČ</th>
						<th class="tac">Názov</th>
						<th class="tac">Hrúbka</th>
						<th class="tac">Výška</th>
						<th class="tac">Šírka</th>
						<th class="tac">Kvalita</th>
						<th class="tac">Cena</th>
						<th class="tac">Zľava</th>
						<th class="act-but"></th>
					</tr>

					'.$list.'
					<div class="cleaner"></div>
				</table>';
			} else {
				return '<div class="tac">Neboli pridané žiadné položky k cenníkom.</div>';
			}

		}*/
/*
		public function find_pricelist_items() {
			$re1 = $re2 = "";

			$stmt = $this->m->q("SELECT * FROM pricelists_items ORDER BY add_date DESC");

			if ( $results = $this->m->result2() ) {
				foreach ($results as $key => $value) {
					$stmtt = $this->m->q("SELECT id FROM pricelists WHERE items LIKE '%".$value["id"]."%'");
					
					if ( !$data = $stmtt->fetch_assoc() ) {
						$re1[] = $value["id"];
					} else {
						$re2[] = $value["id"];
					}
				}
			}
			
			return array( "unused" => $re1, "used" => $re2  );
		}

		public function generate_used_pricelists_items($userid = "", $re = "", $list = "") {
			
			$data = $this->find_pricelist_items();

			if ( $data["used"] ) {

				foreach ( $data["used"] as $key => $r) {

					$stmt = $this->m->q("SELECT * FROM pricelists_items WHERE id = ".$r);
					$value = $this->m->fetch_assoc();

					$discount = $value["discount"] ? $value["discount"]." %" : "NIE";

					$list .= '

							<tr class="show-b">
								<td class="tac">'.$value["id"].'</td>
								<td class="tac">'.$value["name"].'</td>
								<td class="tac">'.$value["size_weight"].'</td>
								<td class="tac">'.$value["size_h"].'</td>
								<td class="tac">'.$value["size_w"].'</td>
								<td class="tac">'.$value["quality"].'</td>
								<td class="tac">'.$value["type"].'</td>
								<td class="tac"><strong>'.$value["price"].'&euro;</strong></td>
								<td class="tac"><strong>'.$discount.'</strong></td>
								<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-pricelistitem"></button> </td>
							</tr>

					';
				}

				return $re = '
				<table class="forms-table">
					<tr class="">
						<th class="tac">ID/IČ</th>
						<th class="tac">Názov</th>
						<th class="tac">Hrúbka</th>
						<th class="tac">Výška</th>
						<th class="tac">Šírka</th>
						<th class="tac">Kvalita</th>
						<th class="tac">Materiál</th>
						<th class="tac">Cena</th>
						<th class="tac">Zľava</th>
						<th class="act-but"></th>
					</tr>

					'.$list.'
					<div class="cleaner"></div>
				</table>';
			} else {
				return '<div class="tac">Neboli pridané žiadné položky k cenníkom.</div>';
			}

		}


		public function generate_pricelist_in_table($list_id) {
			$r = $re = $max = "";

			if ( $list_id ) {

				$list = explode("#", $list_id);

				$r["count"] = count( array_filter($list) );

				foreach ( array_filter($list) as $key => $rr) {
					if ( $rr ) {

						$stmt = $this->m->q("SELECT * FROM pricelists_items WHERE id = ".$rr);
						$value = $stmt->fetch_assoc();

						$discount = $value["discount"] ? $value["discount"]." %" : "NIE";

						$re .= '
								<tr class="show-b">
									<td class="tac">'.$value["id"].'</td>
									<td class="tac max-width">'.$value["name"].'</td>
									<td class="tac">'.$value["size_weight"].'</td>
									<td class="tac">'.$value["size_h"].'</td>
									<td class="tac">'.$value["size_w"].'</td>
									<td class="tac">'.$value["quality"].'</td>
									<td class="tac">'.$value["type"].'</td>
									<td class="tac">'.$value["price"].'&euro;</td>
									<td class="tac">'.$discount.'</td>
								</tr>

						';
					}
				}

				$r["items"] = '
				<div class="ext-galery cnfnms ext-galery2'.$max.'">
					<table class="forms-table">
						<tr class="">
							<th class="tac">ID/IČ</th>
							<th class="tac max-width">Názov</th>
							<th class="tac">Hrúbka</th>
							<th class="tac">Výška</th>
							<th class="tac">Šírka</th>
							<th class="tac">Kvalita</th>
							<th class="tac">Materiál</th>
							<th class="tac">Cena</th>
							<th class="tac">Zľava</th>
						</tr>

						'.$re.'
						<div class="cleaner cnfnms"></div>
					</table>
				</div>';
			} else {
				$r["count"] = "0";
				$r["items"] = "";
			}

			return array( 'count' => $r["count"], 'items' => $r["items"] );
		}

*/





		public function generate_galery_in_table($galery_id, $files, $table) {
			$r = $re = $max = "";

			$stmt = $this->m->q("SELECT files FROM $table WHERE id = ".$galery_id);

			$r = $stmt->fetch_assoc();

			if ( $r["files"] ) {

				$files = $this->file_data_from_dbarray( $r["files"] );

				$r["count"] = count( $files );

				foreach ( $files as $key => $value) {
					$file = $value["folder"].'/'.$value["filename"];

					if ( $value ) {
						$re .= '<li> <img src="'.$this->suburl_data(FILES_LOC.DEF_UPLOAD_FOLDER.$file.".".$value["file_type"]).'" alt="'.$value["filename_orig"].'" title="'.$value["filename_orig"].' / '.date("d.m.Y H:i", $value["create_date"]).'"> </li>';
					}

					if ( $r["count"] > 12 )
						$max = " max-count12plus"; 
					else {
						if ( $r["count"] > 8 && $r["count"] <= 12 )
							$max = " max-count12"; 
					}

					$r["galery"] = '
					<div class="ext-galery'.$max.'">
						<ol>
							'.$re.'
							<div class="cleaner"></div>
						</ol>
					</div>
					';
				}
			} else {
				$r["count"] = "0";
				$r["galery"] = "";
			}

			return array( 'count' => $r["count"], 'galery' => $r["galery"] );
		}

		public function generate_tags_in_table($galery_id, $tags) {
			$r = $re = $max = "";

			$stmt = $this->m->q("SELECT tags FROM our_references WHERE id = ".$galery_id);

			$r = $stmt->fetch_assoc();

			if ( $r["tags"] ) {

				$tags = explode("#", $r["tags"]);

				$r["count"] = count( array_filter($tags) );

				foreach ( $tags as $key => $value) {
					if ( $value ) {

						$stmt = $this->m->q("SELECT tagname FROM tags WHERE id = ".$value);
						$tagname = $stmt->fetch_assoc();

						$re .= '<li class="tag"> '.$this->text_( $tagname["tagname"] ).' </li>';
					}
				}

				$r["tags"] = '
					<div class="ext-tags'.$max.'">
						<ol>
							'.$re.'
							<div class="cleaner"></div>
						</ol>
					</div>
					';
			} else {
				$r["count"] = "0";
				$r["tags"] = "";
			}

			return array( 'count' => $r["count"], 'tags' => $r["tags"] );
		}


		public function select_location_name($id, $default = true, $r = '', $count = 0) {
			$stmt = $this->m->q("SELECT obec, okres FROM psc_zoznam WHERE id = ".$id );
			$r = $stmt->fetch_assoc();


			//
			$psc = $this->m->q("SELECT psc FROM psc_zoznam WHERE id = ".$id );
			$PSC = $psc->fetch_assoc();

			

			$pscs = $this->m->q("SELECT id FROM psc_zoznam WHERE psc = '".$PSC["psc"]."'" );
			$PSCs = $pscs->fetch_assoc();


			if ( $PSCs ) {
				foreach ($this->m->result2() as $key => $value) {
					$count += 1;
				}
			}
			//	
			if ( $default == false ) {
				if ( $count >= 5 )
					return $r["obec"]." - ".$r["okres"];
				else
					return $r["obec"];
			} else 
				return $r["obec"];
		}

		public function file_data_from_dbarray($data, $array = "") {

			foreach ( explode("#", $data) as $key => $value) {

				if ( $value ) {
					$stmt = $this->m->q("SELECT * FROM files WHERE id = ".$value);
					//$r = $stmt->fetch_assoc();

					$array[ $key ] = $stmt->fetch_assoc();
				}
				
			}
			
			return $array;
		}

		public function file_status($status) {
			
			switch ($status) {
				case 1:
					$r = '<div class="form-status i i13" title="Zverejnené"></div>';
					break;
				
				default:
					$r = '<div class="form-status i i12" title="Nezverejnené"></div>';
					break;
			}

			return $r;
		}

		public function slide_type($status) {
			
			switch ($status) {
				case "random":
					$r = 'náhodné poradie';
					break;
				case "fixed":
					$r = 'fixné poradie';
					break;

				default: $r = "chyba zobrazenia"; break;
			}

			return $r;
		}

		public function check_before_manipulation($re = "", $miss = "" ) {
			$user = $this->userdata;


			$default_files = $this->m->q("SELECT files, unused_files, used_files  FROM files_userlists WHERE user = ".$user["userID"]);
			$r = $default_files->fetch_assoc();

			$defaultData = $defaultData2 = $this->create_array_from_database( $r["files"] );
			$resorted_files =  $this->create_array_from_database( $r["unused_files"] );

			if ( $r["unused_files"] ) {
				
				foreach ( $resorted_files as $key ) $defaultData[$key] = 1;

				foreach ( $resorted_files as $key => $value) {
					
					if ( !empty($value) ) {
						if ( isset( $defaultData[$key] ) ) 
							$re .= "#".$value;
						else {
							$re .= NULL;
							//$miss .= $value;
						}
					}

				}

				// add missing data after change id data from html code (hack)
				foreach ( $defaultData2 as $key => $value) {

					if ( !empty($value) ) {
						if ( !in_array($value, $resorted_files))
							$miss .= "#".$value;
						else
							$miss .= NULL;
					}
				}

				$this->m->q("UPDATE files_userlists SET unused_files = '".$re."' WHERE user = ".$user["userID"]);
				$this->m->q("UPDATE files_userlists SET used_files = '".$miss."' WHERE user = ".$user["userID"]);
			}
			
			//return $result = $re.$miss;
		}








		public function generate_tags($type, $data, $r = "") {
			
			if ( $data ) {

				foreach ($data as $key => $value) {
					$stmt = $this->m->q("SELECT * FROM tags WHERE id = ".$value);

					foreach ($this->m->result2() as $key => $value) {
						$r .= '<li class="tag" title="Potiahnutím presuniete" data-tag="'.$value["id"].'">'.$this->text_( $value["tagname"] ).'</li>';
					}
				}

			}

			$text = ( $type == "drop-tags" ) ? "Neboli priradené žiadne tagy." : "Všetky tagy boli použité.";

			if ( $r )
				$r .= '<li class="placeholder-tag" style="display: none;">'.$text.'</li>';
			else
				$r .= '<li class="placeholder-tag">'.$text.'</li>';

			return '<ul class="'.$type.' draagg">'.$r.'</ul>';
		}



		public function generate_tag_list($id, $r = "") {
			$alltags = $used_tags = $unused_tags = "";


			$stmt = $this->m->q("SELECT id FROM tags");
			$ALLTAGS = $this->create_array_from_keys( $this->m->result2(), "id" );
			
			
			$stmt = $this->m->q("SELECT tags FROM our_references WHERE id = ".$id);
			$temp = $stmt->fetch_assoc();
			$USED_TAGS = $this->explode_data_from_db( $temp["tags"] );


			if ( $USED_TAGS  ) {
				foreach ($ALLTAGS as $key => $value) {

					if ( !in_array($value, $USED_TAGS) ){
						$unused_tags[] = $value;
					}
					else {
						$used_tags[] = $value;
					}
				}
			} else {
				$unused_tags = $ALLTAGS;
			}

			return array(
				"used" => $this->generate_tags( "drop-tags", $used_tags ),
				"unused" => $this->generate_tags( "tag-list", $unused_tags ) );
		}


		public function generate_pricelistitem_list($id, $r = "") {
			$alltags = $used_tags = $unused_tags = "";


			$stmt = $this->m->q("SELECT id FROM pricelists_items ORDER BY add_date DESC");
			$ALLTAGS = $this->create_array_from_keys( $this->m->result2(), "id" );
			
			
			$stmt = $this->m->q("SELECT items FROM pricelists WHERE id = ".$id);
			$temp = $stmt->fetch_assoc();
			$USED_TAGS = $this->explode_data_from_db( $temp["items"] );


			if ( $USED_TAGS  ) {
				foreach ($ALLTAGS as $key => $value) {

					if ( !in_array($value, $USED_TAGS) ){
						$unused_tags[] = $value;
					}
					else {
						$used_tags[] = $value;
					}
				}
			} else {
				$unused_tags = $ALLTAGS;
			}

			return array(
				"used" => $this->generate_pricelistitems( "drop-priceitems", $used_tags ),
				"unused" => $this->generate_pricelistitems( "priceitems-list", $unused_tags ) );
		}


		public function generate_pricelistitems($type, $data, $r = "") {
			
			if ( $data ) {

				foreach ($data as $key => $value) {
					$stmt = $this->m->q("SELECT * FROM pricelists_items WHERE id = ".$value);

					foreach ($this->m->result2() as $key => $value) {
						$r .= '<li class="tag" title="#'.$value["id"].' - '.$value["size_w"].'x'.$value["size_h"].'x'.$value["size_weight"].' - '.$value["price"].' &euro;" data-priceitem="'.$value["id"].'">'.$value["name"].'</li>';
					}
				}

			}

			$text = ( $type == "drop-priceitems" ) ? "Neboli priradené žiadne položky." : "Všetky položky boli použité.";

			if ( $r )
				$r .= '<li class="placeholder-tag" style="display: none;">'.$text.'</li>';
			else
				$r .= '<li class="placeholder-tag">'.$text.'</li>';

			return '<ul class="'.$type.' draagg">'.$r.'</ul>';
		}


		/*public function unused_tags($data, $r = '') {
			# code...
		}
*/


		public function create_DATA($data, $numeric = false, $r = "") {
			
			if ( $numeric == true )
				$special = array('-', '/', '_', ';', '`', '~', '#', '&', '*', '+', '=', '´', '"', "'", '\\', '|');
			else
				$special = array(',', '.', '-', '/', '_', ';', '`', '~', '#', '&', '*', '+', '=', '´', '"', "'", '\\', '|');

			if ( $data ) {
				$r = preg_replace("/\s{2,}/", " ", $data);
				$r = str_replace($special, "", $r);
				$r = ltrim($r);
				$r = rtrim($r);
			}

			return $r;
		}

		public function create_array_from_keys($data, $select, $arr = "", $re = "") {
			
			if ( $data ) {
				foreach ($data as $key => $value) {
					if ( !empty( $value[ $select ] ) )
						$arr .= $value[ $select ]."#";
				}
			} 

			if ( $arr ) {
				$re = explode( "#", substr($arr, 0, -1) );

				return $re;
			}
		}


		public function explode_data_from_db($data, $r = '') {
			if ( $data ) {
				foreach ( explode("#", $data) as $key => $value) {
					if ( $value )
						$r[] = $value;
				}
			}

			return $r;
		}



		public function create_array_from_database( $data, $arr = "" ) {
			if ( $data ) {
				foreach ( explode("#", $data) as $key => $value) {
					if ( $value )
						$arr[$key] = $value;
				}
			}

			return $arr;
		}

		public function create_data_for_database_from_list($data_array, $re = "") {
			if ( $data_array ) {
				foreach ($data_array as $key => $value) {
					$re .= "#".$value;
				}
			}

			return $re;
		}


		public function update_price_date() {
			$time = time();

			$this->m->q("UPDATE system_info SET price_update = '$time'");
		}



		public function generate_availability($r="") {
			
			$this->m->q("SELECT * FROM availability");
			
			foreach ( $this->m->result2() as $value) { 

				if ( $value["order"] == 1 ) {
					$r .= '<a href="#" class="edit-input l" name="'.$this->text( $value["text"] ).'" data-inputtarget="additem-availability">'.$this->text( $value["text"] ).'</a>';
				} else {
					$r .= '<a href="#" class="edit-input l gee1" name="'.$this->text( $value["text"] ).'" data-inputtarget="additem-availability">'.$this->text( $value["text"] ).'</a>';
				}
			}						

			return $r.'<a href="#" class="edit-input l" name="" data-inputtarget="additem-availability">'.$this->text(116).'</a>';
		}

		public function generate_public($r="") {
			
			$status = array(274, 264);

			foreach ( $status as $value) { 
				$r .= '<a href="#" class="edit-input l gee1" name="'.$this->text( $value ).'" data-inputtarget="additem-public">'.$this->text( $value ).'</a>';
			}						

			return $r;
		}

		public function gen_availability($data, $r = "") {
			switch ( $data ) {
				case 1: $r = " iOK"; break;
				case 8: $r = " iNOT"; break;
				default: $r = " iWORK"; break;
			}

			return $r;
		}

		//if ( !function_exists('mb_ucfirst') ) {
			public function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {
				$first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
				$str_end = "";

				if ($lower_str_end) {
					$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
				}
				else {
					$str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
				}
				
				$str = $first_letter . $str_end;
				return $str;
			}
		//}

		public function word_ending($num, $arr, $r = '') {
			
			switch ($num) {
				case 0:
					if ( $this->CHR->PD->page == 5 )
						$r = 636;  
					break;
				case 1:
					$r = $arr[0];
					break;
				case 2:
				case 3:
				case 4:
					$r = $arr[1];
					break;
				default:
					$r = $arr[2];
					break;
			}

			return $this->text( $r );
		}




		public function pagedata($pageID = "") {
			//$PD = $this->pagedata;

			if ( $pageID )
				$select = "page = ".$pageID;
			//else
			//	$select = "name = '".$PD["name"]."'";

			$stmt = $this->m->q("SELECT * FROM pages WHERE ".$select." AND lang = '".$this->CHR->LANG."'");
			
			return $stmt->fetch_object();
		}
		

		public function count_result($count) {
			switch ($count) {
				case 1: $r = $this->text(129); break;
				case 2:
				case 3: 
				case 4: $r = $this->text(128); break;

				default: $r = $this->text(127); break;
			}

			return $r;
		}

		public function count_results($q) {
			$stmt = $this->m->q("SELECT COUNT(id) as count FROM ".$q);
			$count = $stmt->fetch_assoc();

			return $count["count"];
		}

		public function make_link($adress, $ajax_url = "", $r = "") {

			//$PD = $this->CHR->PAGEDATA_TARGET($ajax_url);

			$page = $this->CHR->PD->name;

			switch ($this->CHR->PD->page) {
				case 1: $r = $this->url( $page.$adress ); break;
				case 4: $r = $this->url( $page.'/'.$_GET["q"]."/".$adress ); break;
				case 5: $r = $this->url( $_GET["p"]."/".$adress ); break;
				case 8: $r = $this->url( $page.'/'.$_GET["t"].$adress ); break;
				case 102: $r = $this->url( $page.$adress ); break;
				default: $r = $this->url( $page.'/'.$adress ); break;
			}

			return $r;
		}


		public function price($data, $r = '') {
			
			if ( $data ) {

				$dot = strpos($data, ".");

				if ( $dot ) {
					$price = explode(".", $data);

					if ( is_array($price) ) {

						$pri = number_format($price[0].'.'.$price[1], 2);

						$r = str_replace(".00", ",-", $pri)." €";
					}
					else
						$r = $data[0].', - €';
				} else {
					$r = $data.', - €';
				}
				
			} else {
				$r = "0";
			}
			
			return $r;
		}



		public function generate_pictureUrl($data, $first = false, $AJAX = false, $r='') {
			
			if ( $first == true ) {		
				$fir = explode("#", $data);

				if ( $firsst = array_filter($fir) ) {
					$stmt = $this->m->q("SELECT * FROM files WHERE id = '".$firsst[1]."'");

					if ( $data = $stmt->fetch_assoc() ) {

						$file = $data["folder"].'/'.$data["filename"].".".$data["file_type"];

						return array("url" => $this->suburl_data(DEF_UPLOAD_FOLDER.$file), "alt" => $data["filename_orig"], "url_nohhtp" =>  $AJAX ? PHYS_ADRESS_TO_FILES_SWITCH.$file : PHYS_ADRESS_TO_FILES.$file );
					}
				}
				
			} else {
				$stmt = $this->m->q("SELECT * FROM files WHERE id = ".$data);

				if ( $data = $stmt->fetch_assoc() ) {

					$file = $data["folder"].'/'.$data["filename"].".".$data["file_type"];

					return array("url" => $this->suburl_data(DEF_UPLOAD_FOLDER.$file), "alt" => $data["filename_orig"], "url_nohhtp" => PHYS_ADRESS_TO_FILES.$file);
				}
			}
			

		}

		public function picture_dimension($imageUrl, $imagenoUrl,$r='') {

			if ( file_exists($imagenoUrl) ) {
				list($w, $h) = getimagesize( $imageUrl );

				if ( $w > $h )
					$r = "horisontal";
				else if ( $h > $w )
					$r = "vertical";
				else 
					$r = "default";
			}

			return $r;
		}

		public function USERDATA_info($id) {
			
			$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = 1002");


			if ( !$data ) {
				$stmt = $this->m->q("SELECT * FROM uzivatelia WHERE userID = 1002");

				if ( !$u = $stmt->fetch_assoc() ) 
					$u["userNAME"] = "Unknoown";
			} else {
				$u = $data;
			}
			
			/*return array(	"username" 	=> ucfirst($u["userNAME"]),
							"urlname"	=> $u["userNAME"],
							"nickname" 	=> $u["nickname"],
							"all"		=> $data);*/
		}

		public function gender($type, $r = "") {
			switch ($type) {
				case 1: $r = $this->text(113); break;
				case 2: $r = $this->text(114); break;
			}

			return $r;
		}

		public function oddo($page, $pocet_vysledkov, $ajax_url = '', $maxlistlimit = '', $all = false) {
			global $AJAX;

			$actual = $before = $after = $start = $end = $list = $filter = "";




			if ( isset($_GET["subp"]) && isset($_GET["filter"]) ) {

				if ( isset($_GET["filter"]) )
					$filter = "/".$_GET["subp"]."/".$_GET["filter"]."";
				else
					$filter = "/".$_GET["subp"]."";

			} else {

				if ( isset($_GET["subp"]) )
					$filter = "/".$_GET["subp"]."/";

				if ( isset($_GET["filter"]) )
					$filter = $_GET["filter"]."";
			}

			if ( $maxlistlimit )
				$limit = $maxlistlimit;
			else
				$limit = RESULT_LIST;

			$pocet_stran = ceil( $pocet_vysledkov / $limit );

			$before_after = 3;

			if ( $pocet_stran > 0 ) {

				if ( $page <= 0 || !is_numeric($page) ) {
					if ( $AJAX != true )
						header( "Location: ".$this->make_link( $filter.sprintf($this->text(161), 1), $ajax_url ) );
					else 
						$page = 1;
				}
					
				if ( $page > $pocet_stran ) {

					if ( $AJAX != true )
						header( "Location: ".$this->make_link( $filter.sprintf($this->text(161), $pocet_stran), $ajax_url ) );
					else 
						$page =  $pocet_stran;
				}

				if ( $AJAX == true ) {
/*
					if ( isset($_COOKIE["pager"]) ) {
						if ( $_COOKIE["pager"] > $pocet_stran ) {
							$page = 1;
							
							if ( $all == true )
								$this->mm->reset_pager();
						}
					} else {
						$page = 1;
						$this->mm->reset_pager();
					}*/
					
				}
			}

			if ( is_numeric($page) ) {
				if ( $pocet_stran <= 3 ) {
					for ($i=1; $i < $pocet_stran+1; $i++) {

						if ( $page == $i )
							$list .= '<a href="'.$this->make_link( $filter.sprintf($this->text(161), $i), $ajax_url ).'" id="pagelistA" title="'.$this->text(153).'">'.$i.'</a>';
						else
							$list .= '<a href="'.$this->make_link( $filter.sprintf($this->text(161), $i), $ajax_url ).'" class="pa">'.$i.'</a>';
						
					}
				}
				else {

					 
					if ( $page <= 3 ) # 1 2 3 4 
						$afix = $page - $before_after - 1;
					else # 1 .. 3 4 5 6
						$afix = $page - $before_after + 1;

					for ($i = $afix; $i < $page; $i++) {

						if ( $i > 0 )
							$before .= '<a href="'.$this->make_link( $filter.sprintf($this->text(161), $i), $ajax_url ).'" class="pa">'.$i.'</a>';

					}

					$actual .= '<span id="pagelistA" title="'.$this->text(153).'">'.$page.'</span>';

					for ($i = $page + 1; $i < $page + $before_after; $i++) {

						if ( $i <= $pocet_stran )
							$after .= '<a href="'.$this->make_link( $filter.sprintf($this->text(161), $i), $ajax_url ).'" class="pa">'.$i.'</a>';
					}

					$list = $before.$actual.$after;
				}

				$nex = $page + 1;
				$pre = $page - 1;

				if ( $page > 0 && $page != $pocet_stran )
					$next = '<a href="'.$this->make_link( $filter.sprintf($this->text(161), $nex), $ajax_url ).'" class="paSlider paR" title="'.$this->text(151).'"><i class="ii iRight" aria-hidden="true"></i></a>';
				else
					$next = '<a href="#" class="noSlider paR"><i class="ii iRight" aria-hidden="true"></i></a>';

				if ( $page > 1  )
					$previous = '<a href="'.$this->make_link( $filter.sprintf($this->text(161), $pre), $ajax_url ).'" class="paSlider paL" title="'.$this->text(152).'"><i class="ii iLeft" aria-hidden="true"></i></a> ';
				else
					$previous = '<a href="#" class="noSlider paL"><i class="ii iLeft" aria-hidden="true"></i></a>';

				

				if ( $page >= ($before_after + 1) )
					$start = '<a href="'.$this->make_link( $filter.sprintf($this->text(161), 1), $ajax_url ).'" class="pa" title="'.$this->text(159).'">1</a> <strong>...</strong> ';

				if ( ($pocet_stran - $before_after) >= $page )
					$end = ' <strong>...</strong> <a href="'.$this->make_link( $filter.sprintf($this->text(161), $pocet_stran), $ajax_url ).'" class="pa" title="'.$this->text(160).'">'.$pocet_stran.'</a>';


				$od = ($page * $limit) - $limit;
				$zvysok = ($page * $limit) - $pocet_vysledkov;

				if ( $this->pager() == $pocet_stran )
					$do = $page * $limit - $zvysok;
				else
					$do = $page * $limit;

				$oddo = '<div class="listbar-counter">
					<span>'.($od+1).' - '.$do.'</span> '.$this->text(155).' <span style="color: #b0b0b0;">'.$pocet_vysledkov.'</span>
				</div>';

				$info = sprintf( $this->count_result($pocet_vysledkov), $pocet_vysledkov, isset($this->data) ? $this->data : "" );


				/*if ( $AJAX != true )
					setcookie("pager", $page, strtotime("+1 months"), '/', $this->domain());*/


				if ( $pocet_stran >= 1 )
					return array('list' => '<div class="fsr-paging"><div class="listbar"><span>'.$previous.$start.$list.$end.$next.'</span></div> '.$oddo.'</div><div class="cleaner"></div>', 'pocet-stran' => $pocet_stran, 'od' => $od, 'do' => $limit, 'info' => $info, 'oddo' => $oddo );
				else
					return array('list' => '', 'pocet-stran' => $pocet_stran, 'od' => $od, 'do' => $limit, 'info' => $info);

			}
		}

		public function pager($pager = 1 ) {
			global $AJAX;

			switch ( $this->CHR->LANG ) {
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
			setcookie("pager", "1", strtotime( COOKIE_DEF_TIME ), '/', $this->domain());
		}



		public function generate_mainmenu($name = "navimenu") {
			$r = $categories = $fix = $submenu = "";

			$select = 'category, text, sk, title';

			$stmt = $this->m->q("SELECT category FROM settings WHERE id = 1");
			$sett = $stmt->fetch_object();

			if ( !empty($sett) ) {

				foreach ( array_filter( explode('#', $sett->category ) ) as $key => $v) {
					$stmt = $this->m->q("SELECT COUNT(id) as total FROM navody WHERE category = '".$v."' AND public = 1");
					$d = $stmt->fetch_object();

					if ( $d->total != 0 ) {

						$this->m->q("SELECT $select FROM kategorie WHERE category = ".$v);
						$r[] = $this->m->result();
					}
				}
			}
			else {
				$this->m->q("SELECT $select FROM kategorie ORDER BY ".$this->CHR->LANG." ASC");
				$r = $this->m->result2();
			}


			
			foreach ($r as $key => $value) {
				if ( isset($_GET["p"]) && $name == "navimenu2" && ( $value[ $this->CHR->LANG ] == $_GET["p"] ) ) {
					$active = ' mlA';
					$arrow = '<span class="uii-punt"><span></span></span>';
				} else { $active = $arrow= ""; }

				$stmt = $this->m->q("SELECT id FROM navody WHERE category LIKE '".$value["category"]."' AND public = 1");

				if ( $stmt->fetch_object() )
					$title = isset($value["title"]) ? ' title="'.$this->text($value["title"]).'"' : '';

					$categories .= '<li class="nm-def"><a href="'.$this->link_to( $value[ $this->CHR->LANG ] ).'" class="st'.$active.'"'.$title.'>'.ucfirst($this->text($value["text"])).''.$arrow.'</a></li>';
					//$categories .= '<li class="nm-def'.$active.'"><a href="'.$this->link_to( $value[ $this->CHR->LANG ] ).'" class="st" data-target=".navimenu-subs" data-sub=".sub-'.$value[$this->CHR->LANG].'">'.ucfirst($this->text($value["text"])).'</a></li>';
			}

			return '
				<div class="category-content">
					<div class="container">
						<div class="'.$name.'">
							<ul class="visible-links">
								'.$categories.'
								<li class="link-menu"><button>'.$this->text(626).'<i class="fa fa-bars" aria-hidden="true"></i></button>
									<ul class="hidden-links hidden"></ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
			';
		}

		public function generate_submenuids( $id ) {
			$main = $lvl1 = $lvl2 = $lvl3 = $all = $last = $first = "";

			

				$q1 = $this->m->q("SELECT * FROM kategorie_subs WHERE category = ".$id." ORDER BY ".$this->CHR->LANG." ASC");
			
				foreach ( $this->m->result2() as $key_sub => $value2) {
					
					if ( $key_sub == 0 ) {
						$last = '<span class="lastone-subcat"> <a href="">'.sprintf($this->text(191), $value2["sk"]).'</a> </span>';

						$first = 'AAsub';
					} else
						$first = '';
						 

					$lvl2 = "";
					//$lvl1 .= '<li> <a href="">'.$value2["sk"].'</a> </li>';


					$q2 = $this->m->q("SELECT * FROM kategorie_subs2 WHERE categorysub = ".$value2["categorysub"]);
					foreach ( $this->m->result2() as $key_sub => $value3) {
						$lvl2 .= '<span> <a href="">'.$this->text( $value3["text"] ).'</a> </span>';
					}

					if ( $lvl2 )
						$lvl1 .= '<li class="'.$first.'"> <a href="" class="suba-def">'.$this->text( $value2["text"] ).' <div class="cateicon i i43"> </div> </a> <div class="sdfds"> <div class="sdfds-b"> '.$lvl2.$last.'</div> </div> </li>';
					else
						$lvl1 .= '<li> <a href="" class="suba-def">'.$this->text( $value2["text"] ).'</a> </li>';
				}
				
				//$lvl2 .= $l2["sk"];

				$all .= '
				
					'.$lvl1.'

					<div class="cleaner"></div>
				
				';
			


			return $all;
		}





		public function itemHistory($item) {
			$re = $r = "";



			if ( isset( $_COOKIE["mm-history"] ) ) { 

				$old = $_COOKIE["mm-history"];

				if ( strrpos($old, $item) === false) {
					
					$data = $item.".".$old;
				} else {
					$duplicate = str_replace($item, '', $old);

					$data = $item.".".$duplicate;
				}
				
				foreach ( array_filter( explode('.', $data) ) as $key => $value) {
					
					if ( $key == 0 )
						$re .= $value;
					else
						$re .= ".".$value;
				
				}

			} else $re = $item;



			setcookie("mm-history", $re, strtotime( COOKIE_DEF_TIME ), "/", $this->domain());
		}

		public function check_itemHistory($r = '') {
			
			if ( isset( $_COOKIE["mm-history"] ) ) { 

				foreach ( array_filter( explode('.', $_COOKIE["mm-history"]) ) as $key => $value) {
					
					$stmt = $this->m->q("SELECT id FROM navody WHERE id = $value AND public = '1'");

					if ( $stmt->fetch_object() ) {
						if ( $key == 0 )
							$r .= $value;
						else
							$r .= ".".$value;
					}
				}

			}

			setcookie("mm-history", $r, strtotime( COOKIE_DEF_TIME ), "/", $this->domain());
		}

		public function verify_itemid($id, $referer = "", $r = false) {
			$select = 'id, file_list, url, title, price, discount, quantity, availability';
			
			if ( $referer ) {
				foreach ( array_filter( explode("/", $referer) ) as $key => $value) {
					$stmt = $this->m->q("SELECT $select FROM navody WHERE public = 1 AND url = '".$value."'");

					if ( $data = $stmt->fetch_object() ) {
						$r = $data;
						$this->data->basketitemid = $data->id;
					}
				}

			} else {
				$stmt = $this->m->q("SELECT $select FROM navody WHERE public = 1 AND id = ".$id);

				if ( $data = $stmt->fetch_object() ) {
					$r = $data;
					//$this->data->basketitemid = $data->id;
				}
			}
			

			return $r;
		}

		public function verify_detype($id, $r = false) {
			$stmt = $this->m->q("SELECT * FROM delivery_type WHERE id = ".$id);

			if ( $r = $stmt->fetch_object() )

			return $r;
		}

		public function verify_payment($id, $r = false) {
			$stmt = $this->m->q("SELECT * FROM delivery_payment WHERE id = ".$id);

			if ( $r = $stmt->fetch_object() )

			return $r;
		}

		public function uid($new = false, $r = "") {
			
			if ( $new != true ) {
				if ( isset( $_COOKIE["mm-uid"] ) ) { 
					$r = $_COOKIE["mm-uid"];
				} else
					$r = $this->random_chars_oddo(20, 25);
			} else 
				$r = $this->random_chars_oddo(20, 25);
			

			setcookie("mm-uid", $r, strtotime( COOKIE_DEF_TIME ), "/", $this->domain());

			return $r; 
		}
		


		public function generate_inputs($form, $values = false, $BA = "", $button = "", $placeholder = false, $inputvalue = '', $r='') {
			$val = $icon = "";

			$stmt = $this->m->q("SELECT * FROM inputs WHERE form = ".$form." ORDER BY formpos ASC");

			foreach ($this->m->resultO() as $key => $value) {
				
				$class = $value->class ? $value->class : "baInp";
				$req = $value->required ? '<span class="req">*</span>' : "";

				
				if ( $values == true && $BA ) {
					$target = str_replace("-", "_", $value->idd);

					//if ( $target == $this->basket->$target )

					$va = $value->remove_space ? str_replace(" ", "", $BA->basket->$target) : $BA->basket->$target;
					$va = $value->remove_space ? str_replace("+", "", $va) : $va;
				
					$val = 'value="'.$va.'"';
					
					$icon = $value->required ? '<i class="fa fa-check" aria-hidden="true" style="display:none"></i>' : "";
				} else {

					$icon = "";
					$val = $inputvalue ? 'value="'.$inputvalue.'"' : '';
				}

				switch ($value->type) {
					case 'text':
					case 'password':
					case 'email':
					case 'number':
						$PH = $placeholder ? ' placeholder="'.$this->text($value->text).'"' : "";

						$addatr = $form == 500 || $form == 502 ? ' itemprop="query-input"' : '';

						$input = '<input type="'.$value->type.'" class="defInp '.$class.'" name="'.$value->name.'" id="'.$value->idd.'"'.$val.''.$PH.$addatr.'>';
						break;
				}
				$defIcon = $value->icon ? '<div class="badefIcon '.$value->icon.'"></div>' : "";



				$r .= '
				<div class="inpbox">
					<label for="'.$value->idd.'" class="baLa"><span>'.$this->text($value->text).'</span>'.$req.'</label>
					'.$input.'
					<div class="baStatusIcon">'.$defIcon.$icon.'</div>
					<div class="baStatus">
						<label for="'.$value->idd.'"></label>
					</div>
					'.$button.'
				</div>
				';
			}
			return $r;
		}

		public function filter_colors($category = "", $r = '') {
			$re = "";

			$stmt1 = $this->m->q("SELECT * FROM filter_colors WHERE type = 1");
			$colors_atyp = $this->m->result2();
			$cc = count($colors_atyp);
			$w = floor( 20 / $cc );

			$URL = isset($_GET["filter"]) ? $_GET["filter"] : "";


			$stmt2 = $this->m->q("SELECT * FROM filter_colors ORDER BY id ASC");
			$colors = $this->m->result2();

			foreach ($colors as $key => $value) {

				$q = "navody WHERE category = '".$category->category."' AND colors LIKE '%#".$value["id"]."#%' AND public = 1";

				$stmt = $this->m->q("SELECT id FROM ".$q);
				

				if ( $stmt->fetch_assoc() ) {

					$count_all = $this->count_results($q);

					$color = $value["font"] ? $value["font"] : "ffffff";

					switch ($value["type"]) {
						case "2": $atyp = '<div class="color-atyp ca2"></div>'; break;
						case "3":
							foreach ($colors_atyp as $key2 => $value2) {
								$re .= '<span style="height:'.$w.'px;background:#'.$value2["color"].';"></span>';
							}
							$atyp = '<div class="color-atyp ca">'.$re.'</div>';
							break;
						default: $atyp = ""; break;
					}

					$param = $this->CHR->LANG == "sk" ? "farba_" : "color_";

					if ( $URL ) {

						if ( strpos($URL, $param.$value[ $this->CHR->LANG ]) !== false ) {
							$link = 'name="'.$this->make_link($URL).'"';
						}
						else {
							$link = 'href="'.$this->make_link($URL."-".$param.$value[ $this->CHR->LANG ]).'"'; 
						}
					} else {
						$link = 'href="'.$this->make_link($param.$value[ $this->CHR->LANG ]).'"'; 
					}

					$title = mb_strtoupper($this->text( $value["text"] ), "UTF8").' - '.$count_all.' '.$this->word_ending($count_all, array(634, 635, 635) );
					

					if ( isset($_GET["filter"]) ) {
						$active = strrpos($_GET["filter"], $param.$value[ $this->CHR->LANG ]);

						$dea = $active ? ' fccR': "";
					} else
						$dea = '';
					

					$r .= '<div class="fcc'.$dea.'"><a '.$link.' style="background:#'.$value["color"].';" title="'.$title.'">'.$atyp.'<div class="fcc-count"><span>'.$count_all.'</span></div></a></div>';
				}
				
				//mb_strtoupper($this->text( $value["text"] ), "UTF8")
			}


			if ( $r ) {
				return '<div class="filter fD">
					<button type="button">'.$this->text(637).'<span class="f-down ii iDown"></span><span class="f-up ii iRight"></span></button>

					<div class="filter-content">
						<div class="filter-colors">
							'.$r.'
							<div class="cleaner"></div>
						</div>
					</div>
				</div>';
			}
		}

		public function filter_price($category = "", $r = '') {
			$re = "";
			$default = "fa-square-o";
			$check = "fa-check-square";

			$URL = isset($_GET["filter"]) ? $_GET["filter"] : "";

			$stmt2 = $this->m->q("SELECT * FROM filters WHERE type = 4 AND visible = 1");
			
			foreach ($this->m->result2() as $key => $value) {
				$icon = "";
				$q = "navody WHERE category LIKE '%".$category->category."' AND discount != 'null' AND public = 1";

				$stmt = $this->m->q("SELECT id FROM ".$q);
				
				if ( $value["icon"] ) {
					$default = $value["icon"];
					$check = $value["icon_a"];
				}

				//if ( $stmt->fetch_assoc() ) {
					$icon = $default;
					$count_all = $this->count_results($q);
					if ( $URL ) {


						if ( strpos($URL, $value[ $this->CHR->LANG ]) !== false ) {
							$link = 'name="'.$this->make_link($URL).'"';
							$icon = $check;
						}
						else {
							$link = 'href="'.$this->make_link($URL."-".$value[ $this->CHR->LANG ]).'"'; 
						}
					} else {
						$link = 'href="'.$this->make_link($value[ $this->CHR->LANG ]).'"'; 
					}


					$title = mb_strtoupper($this->text( $value["text"] ), "UTF8").' - '.$count_all.' '.$this->word_ending($count_all, array(634, 635, 635) );
					
					$r .= '<a '.$link.' title="'.mb_strtoupper($this->text( $value["text"] ), "UTF8").'"><i class="'.$icon.'" aria-hidden="true"></i>'.$this->text($value["menutext"]).'</a>';
				//}

				
				
			}


			//if ( $r ) {
				return '<div class="filter fD">
					<button type="button">'.$this->text(638).'<span class="f-down ii iDown"></span><span class="f-up ii iRight"></span></button>

					<div class="filter-content">
						<div class="filter-others df">
							'.$r.'
							<div class="cleaner"></div>
						</div>
					</div>
				</div>';
			//}
		}

		public function filter_availability($category = "", $r = '') {
			$re = "";
			$default = "fa-square-o";
			$check = "fa-check-square";

			$URL = isset($_GET["filter"]) ? $_GET["filter"] : "";

			$stmt2 = $this->m->q("SELECT * FROM filters WHERE type = 5 AND visible = 1");
			
			foreach ($this->m->result2() as $key => $value) {
				$icon = "";
				$q = "navody WHERE category LIKE '%".$category->category."' AND discount != 'null' AND public = 1";

				$stmt = $this->m->q("SELECT id FROM ".$q);
				
				if ( $value["icon"] ) {
					$default = $value["icon"];
					$check = $value["icon_a"];
				}

				//if ( $stmt->fetch_assoc() ) {
					$icon = $default;
					$count_all = $this->count_results($q);
					if ( $URL ) {


						if ( strpos($URL, $value[ $this->CHR->LANG ]) !== false ) {
							$link = 'name="'.$this->make_link($URL).'"';
							$icon = $check;
						}
						else {
							$link = 'href="'.$this->make_link($URL."-".$value[ $this->CHR->LANG ]).'"'; 
						}
					} else {
						$link = 'href="'.$this->make_link($value[ $this->CHR->LANG ]).'"'; 
					}


					$title = mb_strtoupper($this->text( $value["text"] ), "UTF8").' - '.$count_all.' '.$this->word_ending($count_all, array(634, 635, 635) );
					
					$r .= '<a '.$link.' title="'.mb_strtoupper($this->text( $value["text"] ), "UTF8").'"><i class="'.$icon.'" aria-hidden="true"></i>'.$this->text($value["menutext"]).'</a>';
				//}
				
			}


			//if ( $r ) {
				return '<div class="filter fD">
					<button type="button">'.$this->text(670).'<span class="f-down ii iDown"></span><span class="f-up ii iRight"></span></button>

					<div class="filter-content">
						<div class="filter-others df">
							'.$r.'
							<div class="cleaner"></div>
						</div>
					</div>
				</div>';
			//}
		}

		public function filter_order($category = "", $r = '') {
			$re = $URL_ = "";
			//$c = 0;
			$default = "iUncheck";
			$check = "iCheck";

			$URL_ = isset($_GET["filter"]) ? $_GET["filter"] : "";
			$URL = isset($_GET["filter"]) ? $_GET["filter"] : "";

			$q = 'filters WHERE type = 1 AND visible = 1'; 
			$this->m->q("SELECT * FROM ".$q);


			/*foreach ($this->m->result2() as $key => $value) {
				$URL = str_replace($value[ $this->CHR->LANG ]."-", "", $URL_);
				$URL = str_replace( "-".$value[ $this->CHR->LANG ], "", $URL);
			}
			

			$this->m->q("SELECT * FROM ".$q);*/
			foreach ($this->m->result2() as $key => $value) {

				if ( strpos($URL, $value[ $this->CHR->LANG ]) !== false ) {
					$icon = $default;

					if ( $URL ) {
						if ( strpos($URL, $value[ $this->CHR->LANG ]) !== false ) {
							$link = 'name="'.$this->make_link($URL).'"';
							$icon = $check;
						}
						else {
							$link = 'href="'.$this->make_link($URL."-".$value[ $this->CHR->LANG ]).'"';
						}
					} else {
						$link = 'href="'.$this->make_link($value[ $this->CHR->LANG ]).'"'; 
					}
				} else {
					$this->m->q("SELECT * FROM ".$q);
					foreach ($this->m->result2() as $key2 => $value2) {
						$URL_ = str_replace($value2[ $this->CHR->LANG ]."-", "", $URL_);
						$URL_ = str_replace( "-".$value2[ $this->CHR->LANG ], "", $URL_);
						$URL_ = str_replace( $value2[ $this->CHR->LANG ], "", $URL_);
					}

					if ( $URL_ ) {
						$link = 'href="'.$this->make_link($URL_."-".$value[ $this->CHR->LANG ]).'"';
					} else {
						$link = 'href="'.$this->make_link($value[ $this->CHR->LANG ]).'"';
					}
					
					$icon = $default;
					//$icon = $default;
					/*
					if ( $URL ) {
						if ( strpos($URL, $value[ $this->CHR->LANG ]) !== false ) {
							$link = 'name="'.$this->make_link($URL_).'"';
						}
						else {
							$link = 'href="'.$this->make_link($URL_."-".$value[ $this->CHR->LANG ]).'"';
						}
					} else {
						$link = 'href="'.$this->make_link($value[ $this->CHR->LANG ]).'"'; 
					}*/
				}
					


					$r .= '<a '.$link.' title="'.mb_strtoupper($this->text( $value["text"] ), "UTF8").'"><i class="ii '.$icon.'" aria-hidden="true"></i>'.$this->text($value["text"]).'</a>';

			}

			return '
			<div class="filter fD">
				<button type="button">'.$this->text(659).'<span class="f-down ii iDown"></span><span class="f-up ii iRight"></span></button>

				<div class="filter-content">
					<div class="filter-order df">
						'.$r.'
						<div class="cleaner"></div>
					</div>
				</div>
			</div>
			';
		}


		public function ITEMDATA($id, $r = '') {
			$stmt = $this->m->q("SELECT * FROM navody WHERE id = ".$id);

			return $stmt->fetch_object();
		}
		/*public function itemHistory($item) {
			$this->destroy_cookie("mm-history");
		}*/
		/*
		function tentative_delivery_date($shipping_days = 3) {
    $today = time(); //timestamp for current time to be used for current date 
    $day_of_week = date("N", $today); // get the day of week, e.g. 1 = Monday through 7 = Sunday 
 
    if($day_of_week + $shipping_days < 6) {
        // delivery can be made within same business week
        $tentative_delivery_date = strtotime("+$shipping_days days");
    } else if(($day_of_week + $shipping_days) >= 6 && ($day_of_week + $shipping_days) < 15) {
        // delivery is possible next week hence add two days for weekend (Saturday and Sunday)
        $shipping_days += 2;
        $tentative_delivery_date = strtotime("+$shipping_days days");
        // check if new delivery date is falling in second weekend and adjust accordingly
        if(date("N", $tentative_delivery_date) == 6 || date("N", $tentative_delivery_date) == 7) {
            $shipping_days += 2;
            $tentative_delivery_date = strtotime("+$shipping_days days");
        }
    } else {
        // this function does not support shipping time > 7 days
        return "Not supported";
    }
    return date('jS M (D)', $tentative_delivery_date); // Format the date nicely.This format => 1st Jan (Wed) 
}
*/
		public function delivery_date($sendDate = "", $shipping_days = DELIVERY_DAYS, $r = '') {
			
			if ( $sendDate ) {
				//if ( time() <= strtotime( ORDER_UNTILTO ) ) {
					$send = date("d.m.Y", $sendDate);
				//} else {
					//$send = "tomorrow";
				//}
			} else {
				if ( time() <= strtotime( ORDER_UNTILTO ) ) {
					$send = "today";
				} else {
					$send = "tomorrow";
				}
			}
			

			//$shippingDay = date("N", strtotime($send." + ".$shipping_days." days") );
			$shippingDay = date("N", strtotime($send) );

			switch ($shippingDay) {
				case 6:
				case 7:
					$de = strtotime("+ ".$shipping_days." days");
					$shipping = strtotime("next Monday");
					$shipping += ($de - $shipping);

					break;
				
				default:
					$shipping = strtotime($send." + ".$shipping_days." days");
					break;
			}

			switch ( date("N", strtotime($shipping) ) ) {
				case 6:
				case 7:
					$delivery = strtotime("next Monday");
					break;
				
				default:
					$delivery = $shipping;
					break;
			}

			return strtotime( date("d.m.Y", $delivery) );
		}

		public function realDeliveryDate($dt, $numdays)
		{
		    /*$holidays = array("05/30/2011");
		    $checkday = strtotime($dt." +".$numdays." days");
		    // check if it's a holiday
		    while(in_array(date("m/d/Y",$checkday), $holidays)) {
		        $checkday = strtotime(date("m/d/Y",$checkday)." +1 day");
		    }
		    // make sure it's not Saturday
		    if (date("w",$checkday) == 6) {
		        $checkday = strtotime(date("m/d/Y",$checkday)." +2 days");
		    }
		    // make sure it's not Sunday
		    if (date("w",$checkday) == 0) {
		        $checkday = strtotime(date("m/d/Y",$checkday)." +1 day");
		    }
		    // make sure it's not another holiday
		    while(in_array(date("m/d/Y",$checkday), $holidays)) {
		        $checkday = strtotime(date("m/d/Y",$checkday)." +1 day");
		    }
		    return $checkday;*/
		    //Build the days based of weekends
			
		}



		public function about_data() {

			return array(
				504 => array("page" => 504, "text" => 731, "icon" => "iDelivery"), 
				505 => array("page" => 505, "text" => 732, "icon" => "iPay"), 
				501 => array("page" => 501, "text" => 735, "icon" => "iVop"), 
				507 => array("page" => 507, "text" => 736, "icon" => "iCookies"),
				500 => array("page" => 500, "text" => 737, "icon" => "iEmail")
			);

		}
		public function about_menu($type, $ul = true, $r = '') {
			$d = $this->about_data();

			switch ($type) {
				case 1:
					$about = array( $d[504], $d[505] );
					break;
				case 2:
					$about = array( $d[501], $d[507] );
					break;
				case 3:
					$about = array( $d[500] );
					break;
			}

			foreach ($about as $key => $value) {
				
				if ( $page = $this->pagedata($value["page"]) ) {

					if ( $ul == true )
						$active = $this->CHR->PD->name == $page->name ? ' id="aboutA"' : "";
					else
						$active = $this->CHR->PD->name == $page->name ? ' id="aboutAA"' : "";

					$icon = isset($value["icon"]) ? '<span class="ii '.$value["icon"].'"></span>' : "";

					$a = '<a href="'.$this->link($page->page).'"'.$active.'>'.$icon.' '.$this->text($value["text"]).'</a>';

					if ( $ul == true )
						$r .= '<li>'.$a.'</li>';
					else
						$r .= $a;

				}
			}


			if ( $ul == true )
				return '<ul class="aboutnav">'.$r.'</ul>';
			else
				return $r;
		}

		public function about_content($r = '', $re = "") {
			$d = $this->about_data();
			
			switch ( $this->CHR->PD->page ) {
				case 504: //DOprava
					
					$this->m->q("SELECT * FROM delivery_type WHERE active = 1");

					foreach ($this->m->resultO() as $key => $value) {
						$re .= '
						<tr>
							<td><strong>'.$value->name.'</strong></td>
							<td>'.$this->text(743).'</td>
							<td class="apr tar"><strong>'.$this->price($value->price).'</strong></td>
						</tr>
						';
					}

					$r = '
					<div class="aleft">
						<div class="aaco">
							<h2>'.mb_strtoupper( sprintf($this->text(300), "GEIS"), "UTF8").'</h2>
							<div class="aaco-body">'.sprintf($this->text(739), DELIVERY_DAYS).'</div>
						</div>
					</div>

					<div class="aright">
						<div class="aaco">
							<h2>'.mb_strtoupper($this->text(740), "UTF8").'</h2>
							<div class="aaco-body">
								<table class="abtable">
									<tr>
										<th>'.$this->text(741).'</th>
										<th>'.$this->text(536).'</th>
										<th class="tar">'.$this->text(742).'</th>
									</tr>
									'.$re.'
								</table>
							</div>
						</div>
					</div>

					<div class="cleaner"></div>
					';
					break;
				
				case 505: //Platby
					
					$this->m->q("SELECT * FROM delivery_payment WHERE active = 1");

					foreach ($this->m->resultO() as $key => $value) {
						$i = $value->price == 0 ? $this->text(575) : $this->price($value->price);

						$re .= '
						<tr>
							<td><strong>'.$value->name.'</strong></td>
							<td class="apr tar"><strong>'.$i.'</strong></td>
						</tr>
						';
					}

					$r = '
					<div class="aleft">
						<div class="aaco">
							<h2>'.mb_strtoupper( $this->text(743), "UTF8").'</h2>
							<div class="aaco-body">'.$this->text(757).'</div>
						</div>
						<div class="aaco">
							<h3>'.mb_strtoupper( $this->text(619), "UTF8").'</h3>

							<div class="aaco-body">
								<p class="apaydata-bank">'.$this->text(774).'</p>
								<div class="apaydata">
									<div class="fl ap1">
										<p>'.$this->text(621).'</p>
										<p>'.$this->text(622).'</p>
									</div>
									<div class="fl ap2">
										<p><span>'.$this->text(621).'</span><strong>'.BANK_IBAN.'</strong></p>
										<p><span>'.$this->text(622).'</span><strong>'.BANK_SWIFT.'</strong></p>
									</div>
									<div class="cleaner"></div>
								</div>
								<p class="apaydata-info">'.$this->text(775).'</p>
							</div>
						</div>
					</div>

					<div class="aright">
						<div class="aaco">
							<h2>'.mb_strtoupper($this->text(536), "UTF8").'</h2>
							<div class="aaco-body">
								<table class="abtable">
									<tr>
										<th>'.$this->text(758).'</th>
										<th class="tar">'.$this->text(756).'</th>
									</tr>
									'.$re.'
								</table>
							</div>
						</div>
					</div>

					<div class="cleaner"></div>
					';
					break;
				

				case 507: //Cookies
					
					$r = '
					<div class="aleft">
						<div class="aaco">
							<h2>'.mb_strtoupper( $this->text(762), "UTF8").'</h2>
							<div class="aaco-body">'.$this->text(763).'
							'.sprintf($this->text(769), '<a href="https://sk.wikipedia.org/wiki/HTTP_cookie" target="_self">wikipedia</a>', '<a href="http://www.aboutcookies.org.uk/cookies" target="_self">tu</a>').'</div>
						</div>

						<div class="aaco">
							<h3>'.mb_strtoupper( $this->text(764), "UTF8").'</h2>
							<div class="aaco-body">'.sprintf($this->text(768), '<a href="'.$this->link(1).'" target="_self">monamade.sk</a>').'</div>
						</div>
					</div>

					<div class="aright">
						<div class="aaco">
							<h3>'.mb_strtoupper( $this->text(766), "UTF8").'</h2>
							<div class="aaco-body">'.$this->text(767).'</div>
						</div>
						<div class="aaco">
							<h3>'.mb_strtoupper( $this->text(770), "UTF8").'</h2>
							<div class="aaco-body">'.sprintf($this->text(771), '<a href="https://google.sk" target="_self">google.sk</a>').'</div>
						</div>
					</div>

					<div class="cleaner"></div>
					';
					break;

				case 501: //VOP
					
					$r = $this->vop_page();

					break;

				case 500: //kontakt
					
					$r = $this->p_contact();

					break;
			}
			//$bd = '<div class="headers"><h1>'.mb_strtoupper($GD->text( $data->text ), "UTF8").'</h1>'.$GD->breadcrumb($CHR->PD->page, array($data, $R["count"] )).'</div>';

			return '
				<div class="abouthead">
					<h1>'.$this->text($d[ $this->CHR->PD->page ]["text"]).'</h1>
					'.$this->breadcrumb($this->CHR->PD->page).'
				</div>
				<div class="aboutcontent">
					'.$r.'
				</div>
			';
		}

		public function vop_page($r = '') {
			$mm = '<a href="'.$this->link(1).'" target="_self">monamade.sk</a>';
			$l = $r = "";


			$this->m->q("SELECT * FROM vop ORDER BY pos ASC");
			
			foreach ($this->m->resultO() as $key => $v) {
				
				if ( $v->visible == 1 )
					$visible = " aaB";
				else
					$visible = " aaN";

				if ( $v->body_fill )
					$fill = strpos($v->body_fill, "--") === false ? $v->body_fill : explode("--", $v->body_fill);

				if ( $v->link )
					$body = $v->body_fill ? vsprintf($this->text($v->body), $fill) : sprintf($this->text($v->body), $mm);
				else {
					$body = $v->body_fill ? vsprintf($this->text($v->body), $fill) : $this->text($v->body);
				}
				
				if ( $v->id == 4 )
					$body = $this->vop_owner();

				$h = $v->h ? $v->h : "3";

				$re = '
				<div class="aaco'.$visible.'">
					<h'.$h.'><a href="#" class="about-tab">'.mb_strtoupper( $this->text($v->head), "UTF8").'<span class="f-down ii iDown"></span><span class="f-up ii iRight"></span></a></h'.$h.'>

					<div class="aaco-body">
						'.$body.'
					</div>
				</div>
				';


				if ( $v->leftright == "l" )
					$l .= $re;
				else
					$r .= $re;
			}

			return '
			<div class="aleft">
				'.$l.'
			</div>

			<div class="aright">
				'.$r.'
			</div>

			<div class="cleaner"></div>
			';
		}

		public function vop_update() {
			$stmt = $this->m->q("SELECT * FROM cms_eshops WHERE shopID = 1");
			$d = $stmt->fetch_object();

			return $d->vop_update;
		}
		public function vop_owner($r = '') {
			return '
			<div class="apaydata">
				<div class="fl ap1">
					<ul>
						<li><strong>'.VOP_NAME.'</strong></li>
						<li><strong>'.VOP_STREET.'</strong></li>
						<li><strong>'.VOP_ZIP." ".VOP_CITY.'</strong></li>
						<li>'.VOP_STATE.'</li>
					</ul>
				</div>
				<div class="fl ap2">
					<ul>
						<li>'.$this->text(564).': '.VOP_ICO.'</li>
						<li>'.$this->text(565).': '.VOP_DIC.'</li>
						<li>'.$this->text(777).'</li>
					</ul>
				</div>
				<div class="cleaner"></div>

				<ul>
					<li><div class="ii iEmail"></div>'.VOP_MAIL.'</li>
					<li><div class="ii iPhone"></div>'.VOP_PHONE.'</li>
				</ul>
			</div>

			<div class="vop-control">
				'.$this->text(778).'
				<div><strong>'.$this->text(780).'</strong></div>
				'.$this->text(779).'
			</div>

			<div class="vop-update">'.sprintf($this->text(808), date("d.m.Y", $this->vop_update() ) ).'</div>
			';
		}

		public function p_contact($r = '') {
			$mm = '<a href="'.$this->link(1).'" target="_self">monamade.sk</a>';
			$l = $r = "";

			return '

			<div class="gmap-contact"><div id="gmap"></div></div>

			<div class="contactpage">
				<div class="cpl">
					<div class="cpb">
						<div class="cph"><h2>Fakturačné údaje</h2></div>
						<div class="cpc">
							<ul>
								<li><strong>'.VOP_NAME.'</strong></li>
								<li><strong>'.VOP_STREET.'</strong></li>
								<li><strong>'.VOP_ZIP." ".VOP_CITY.'</strong></li>
								<li>'.VOP_STATE.'</li>
							</ul>

							<ul>
								<li>'.$this->text(564).': '.VOP_ICO.'</li>
								<li>'.$this->text(565).': '.VOP_DIC.'</li>
								<li>'.$this->text(777).'</li>
							</ul>

							'.$this->text(778).'

							<p>'.$this->text(815).'</p>

						</div>
					</div>

					
				</div>

				<div class="cpr">

					<div class="cpb">
						<div class="cph"><h2>Kontaktné údaje</h2></div>
						<div class="cpc">
							<ul>
								<li><div class="ii iEmail"></div><strong>'.VOP_MAIL.'</strong></li>
								<li><div class="ii iPhone"></div><strong>'.VOP_PHONE.'</strong></li>
							</ul>
							<ul>
								<li><a href="'.SOCIAL_FCB.'" title="'.sprintf($this->text(817), 'Facebook').'" class="fcb"><i class="fa fa-facebook-square" aria-hidden="true"></i>Facebook</a></li>
								<li><a href="'.SOCIAL_INSTA.'" title="'.sprintf($this->text(817), 'Instagram').'" class="insta"><i class="fa fa-instagram" aria-hidden="true"></i>Instagram</a></li>
							</ul>
						</div>
					</div>

					<div class="cpb">
						<div class="cph"><h3>Bankové údaje</h3></div>
						<div class="cpc">
							<ul>
								<li><span class="ii iBank"></span>'.BANK_NAME.'</li>
							</ul>
							<ul>
								<li>IBAN: <strong>'.BANK_IBAN.'</strong></li>
								<li>BIC (SWIFT): <strong>'.BANK_SWIFT.'</strong></li>
							</ul>
						</div>
					</div>

					<div class="cpb">
						<div class="cph"><h4>'.$this->text(780).'</h4></div>
						<div class="cpc">
							'.$this->text(779).'
						</div>
					</div>
				</div>

				<div class="cleaner"></div>
			</div>

			';
		}

		public function itemscope($type, $r = '') {
			
			switch ($type) {
				case 1:
					$r = '
					<span itemscope itemtype="http://schema.org/Organization">
						<link itemprop="url" href="'.$this->link(1).'">
						<a itemprop="sameAs" href="'.SOCIAL_FCB.'">FB</a>
						<a itemprop="sameAs" href="'.SOCIAL_INSTA.'">Instagram</a>
					</span>
					';
					break;

			}

			return '<div class="dn">'.$r.'</div>';
		}

		public function gen_keywords_from_tags($d, $r = '') {

			if ( $d ) {

				foreach ( array_filter( explode('#', $d) ) as $key => $v) {
					
					$stmt = $this->m->q("SELECT id_text FROM tagy WHERE id = '".$v."'");
					$tag = $stmt->fetch_object();

					$be = $key == 0 || $key == 1 ? '' : ', ';

					$r .= $be.mb_strtolower($this->text( $tag->id_text ), 'UTF8');
				}
			}

			return $r;
		}

		// function to geocode address, it will return false if unable to geocode address
		public function geocode($address) {

			// url encode the address
			$address = urlencode($address);

			// google map geocode api url
			$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

			// get the json response
			$resp_json = file_get_contents($url);

			// decode the json
			$resp = json_decode($resp_json, true);

			// response status will be 'OK', if able to geocode given address 
			if( $resp['status'] == 'OK' ) {

				// get the important data
				$lati = $resp['results'][0]['geometry']['location']['lat'];
				$longi = $resp['results'][0]['geometry']['location']['lng'];
				$formatted_address = $resp['results'][0]['formatted_address'];

				// verify if data is complete
				if( $lati && $longi && $formatted_address ) {
					// put the data in the array
					$data_arr = array();            

					array_push(
						$data_arr, 
						$lati, 
						$longi, 
						$formatted_address
					);

					return $data_arr;

				} else {
					return false;
				}

			} else {
				return false;
			}
		}
	}
	






	class cas extends DateTime {

		protected $strings = array(
			'y' => array(25, 31),
			'm' => array(26, 32),
			'd' => array(27, 33),
			'h' => array(28, 34),
			'i' => array(29, 35),
			's' => array(30, 36)
		);

		public function result() {
			$now = new DateTime('now');

			$diff = $this->diff($now);
			
			foreach ($this->strings as $key => $value) {
				
				if ( ($text = $this->getDiffText($key, $diff)) )
					return $text;
			}

			return $this->strings["s"][0];
		}


		protected function getDiffText($intervalKey, $diff){
			$GD = new GLOBALDATA();

			$pluralKey = 1;
			$value = $diff->$intervalKey;
			
			if( $value > 0 ) {
				if ( $value < 2 )
					$pluralKey = 0;
				
				$text = $GD->text( $this->strings[$intervalKey][$pluralKey] );
				
				return sprintf($text, $value);
			}
		}
	}

