<?php
	$SHOP = new SHOP();

	class SHOP {
		
		protected $mysql;
		protected $GD;
		
		private $user;
		private $userID;

		public $U;
		//public $shopID;

		public function __construct() {
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();

			//$this->U = $this->GD->U;
			//$this->userID = $this->GD->U->userID;
			//$this->shopID = $this->GD->U->shopID;
			
			//$this->shop = $this->GD->shopdata();
			//$this->shopID = $this->shopdata( $this->userID, "shopID" );
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new SHOP();
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

		public function action($type, $data, $return = "") {




			if ( $this->is_logged() == true ) {
				if ( $this->is_inactive() == false ) {

					switch ($type) {

						
						case 'diyfiles':
							$return = array("html" 	=> $this->generate_diy_header() );
							break;

/*
						case 'add-step':
							$return = "";
							break;*/

						case "savefiles":
							$return = $this->update_filelist( $this->get_picture_id_from_filename( $data ) );
							//$return = $this->get_picture_id_from_filename( $data );
							break;
					}

				} 
				else {
					$return = array("offline" 	=> $this->logout_message(1) );
					$this->logout();
				}
			} else {
				$return = array("offline" 	=> $this->logout_message(1) );
				$this->logout();
			}

			
			return $return;
		}


		






		public function generate_owneritems($concept = false, $count = 0, $maxresults = MAX_RESULTS_SKLAD) {
			$r = $items = $emptyItems = $reserv = $publish = '';

			if ( !isset($_GET["subp"]) || !$this->CHR->CHECK_SUBPAGE() )
				$_GET["subp"] = "vsetky-veci";

			$filters = array( 	0 	=> array("page" => 30, "text" => 267, "head" => 270, "q" => "navody WHERE shop_id = ".$this->GD->U->shopID),
							 	1	=> array("page" => 31, "text" => 263, "head" => 271, "q" => "navody WHERE public = '1' AND shop_id = ".$this->GD->U->shopID),
							 	2	=> array("page" => 32, "text" => 265, "head" => 272, "q" => "navody WHERE public = '0' AND shop_id = ".$this->GD->U->shopID),
							 	3	=> array("page" => 33, "text" => 268, "head" => 273, "q" => "concepts WHERE title != '' AND shop_id = ".$this->GD->U->shopID));

			$pageinfo = $this->GD->generate_page_with_filters($filters);

			if ( !$pageinfo )
				$_GET["subp"] = "vsetky-veci";

			$page = $this->GD->pager();
			$oddo = $this->GD->oddo( $page, $pageinfo["activeCount"], "", MAX_RESULTS_SKLAD );
			$data = $this->mysql->query("SELECT * FROM ".$pageinfo["activeQuery"]." ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);
			

			if ( $data ) {

				foreach ($this->mysql->result2() as $key => $value) {
					$count++;
					$reserv = "";

					$timeToLaik = new cas( date("d.m.Y H:i", $value["create_date"]) );

					$imgLink = $this->GD->generate_pictureUrl( $value["file_list"], true );
					$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );

					if ( $key == 2 ) {
						$items .= $this->addbutton();

						$maxresults -= 1;
					}
					
					if ( !empty( $value["price"] )  ) {
						$price = '<div class="diyDesc-price">'.$this->GD->price( $value["price"] ).'</div>';
					} else
						$price = "";


					$numm = $this->reserved_item( $value["id"] );
					//$this->GD->word_ending($numm, array(341, 342, 343))
					if ( $numm )
						$reserv = '<div class="ites"><span>'.$this->GD->word_ending($numm, array(341, 342, 343)).' '.$numm.' '.$this->GD->word_ending($numm, array(628, 629, 630) ).'</span><i class="ii iCheck1"></i></div>';

					$IMG = $imgLink ? '<img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($value["title"]).'">' : '<div class="diyImg-nop"><span class="ii iPhoto"></span></div>';


					if (  $pageinfo["activePage"] == 33 ) {
						$conceptIcon = '<div class="ites">'.$this->GD->text(269).'</div>';

						$items .= '
								<div class="aic aiReady">
									<div class="aic-c">

										<div class="aic-cimg">'.$IMG.'</div>

										<div class="item-status">
											<div class="is-co">
												'.$conceptIcon.'
											</div>
										</div>

										<div class="aic-desc">
											<div class="aic-d-body">
												<div class="aic-h">
													'.$price.'
												</div>
												<div class="aic-b">
													<div class="diyDesc-title">'.$this->GD->mb_ucfirst($value["title"]).'</div>
													<div class="diyDesc-time" title=""> <abbr class="upD" data-livestamp="'.$value["create_date"].'">'.$timeToLaik->result().'</abbr> <span class="i i06"></span> </div>
												</div>
											</div>
										</div>

										<div class="aic-menu">
											<div class="aicm-content">
												<a href="'.$this->GD->link_to( array($this->GD->page_(9), $value["system_id"]) ).'" class="aicm1">'.$this->GD->text(209).'</a>
											</div>
										</div>
									</div>
								</div>';

					} else {
						
						if ( $value["public"] != 1 ) {
							$conceptIcon = '<div class="ites ite_nopublic">'.$this->GD->text(265).'</div>';
							$publish = '';
						}
						else {
							$conceptIcon = "";
							
							$conceptIcon .= '<div class="ites ite_public" title="'.$this->GD->text(263).'"><i class="ii iGlobe"></i></div>';
							

							if ( $value["availability"] != 8 ) {
								$conceptIcon .= '<div class="ites">'.sprintf($this->GD->text(344), ' <span>'.$value["quantity"]).' '.$this->GD->word_ending($value["quantity"], array(628, 629, 630)).'</span></div>';
							} else
								$conceptIcon .= '<div class="ites itesNE">'.mb_strtoupper($this->GD->text(293)).'</div>';

							$publish = '<a href="'.$this->GD->ofiurl( $value["url"] ).'" class="aicm1" target="_blank">'.$this->GD->text(183).'</a>';
						}

						
						$items .= '
								<div class="aic aiReady">
									<div class="aic-c">

										<div class="aic-cimg"><img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($value["title"]).'"></div>

										<div class="item-status">
											<div class="is-co">
												'.$conceptIcon.'
											</div>
										
											<div class="item-reservation">
												'.$reserv.'
											</div>
										</div>

										<div class="aic-desc">
											<div class="aic-d-body">
												<div class="aic-h">
													'.$price.'
												</div>
												<div class="aic-b">
													<div class="diyDesc-title">'.$this->GD->mb_ucfirst($value["title"]).'</div>
													<div class="diyDesc-time" title=""> <abbr class="upD" data-livestamp="'.$value["create_date"].'">'.$timeToLaik->result().'</abbr> <span class="i i06"></span> </div>
												</div>
											</div>
										</div>

										<div class="aic-menu">
											<div class="aicm-content">
												'.$publish.'
												<a href="'.$this->GD->link_to( array($this->GD->page_(10), $value["system_id"]."__".$value["url"]) ).'" class="aicm1">'.$this->GD->text(209).'</a>
											</div>
										</div>
									</div>
								</div>';
					}
					
				}

				$r = $items;
			} else {
				$r = "";
			}

			$r .= $this->generate_empty_fields($count, $maxresults, RESULTS_WIDTH);

			return array("count" => $count, "menu" => $pageinfo["menu"], "head" => $pageinfo["head"], "list" => '<div class="itemlist">
						<div class="add-item-content">
							
							'.$r.'
							<div class="cleaner"></div>
						</div>
						'.$oddo["list"].'
					</div>
					');

		}

		public function generate_empty_fields($count, $empty, $width,  $r = "") {

			$ee = $empty - $count;
			//$inRow = 100 / $width;
			//$rows = ceil($count / $inRow);
			

			//$e = $empty - ($rows * $inRow);

			for ($i=0; $i < $ee; $i++) { 

				if ( $i == 2 - $count) {
					
					$r .= $this->addbutton();
				}

				$r .= '<div class="aic aiEmpty">
									<div class="aic-c">
										<div class="aic-h"></div>
										<div class="aic-b"></div>
									</div>
								</div>';
			}

			return $r;
		}

		public function addbutton() {
			return '<div class="aic add-item aiFirst">
						<div class="aic-c">
							<div class="aic-h">
								<a href="'.$this->GD->link(9).'" class="d-b d-b-ai"> <div class="aic-hh"><span class="ii iRe"></span>'.$this->GD->text(207).'</div></a>
							</div>
							
						</div>
					</div>';
		}


		public function generate_data($from, $where, $data) {
			$stmt = $this->mysql->query("SELECT * FROM $from WHERE $where = ".$data);
			
			return $stmt;
		}


		public function reserved_item($id, $r = '') {
			
			$stmt = $this->mysql->q("SELECT system_id, content FROM orders WHERE content LIKE '%.$id.%' AND status != '10' AND status != '20'");

			if ( $re = $this->mysql->resultO() ) {

				
				foreach ( $re as $key => $v ) {
					$r = "";
					$d[$v->system_id] = $this->basket_content( $v->content );


					foreach ( $d as $k => $ve) {
						if ( isset( $ve[$id] ) )
							$r += $ve[$id]["quantity"];
					}
				}
			}

			return $r;
		}

		public function basket_content($content, $r = '') {
			
			$arr = explode("#", $content);

			if ( strrpos($content, "#")) {
				foreach ( array_filter( explode("#", $content) ) as $key => $value) {
					$detail = explode("*", $value);

					$id = str_replace(".", "", $detail[0]);
					$r[$id] = array("id" => $id, "quantity" => $detail[1], "price" => $detail[2]);
				}

			} else {
				$ba = explode("*", $content);

				$id = str_replace(".", "",  $ba[0]);
				$r[$id] = array( "id" => $id, "quantity" => $ba[1], "price" => $ba[2] );
			}

			return $r;
		}
/*
		public function shopdata( $ID, $r = "") {
			$user = $this->user;

			if ( $ID ) {
				$stmt = $this->mysql->query("SELECT $return FROM eshops WHERE ownerID = ".$user["userID"]);
				$re = $stmt->fetch_assoc();

				$r = $re[ $return ];
			} else {
				$stmt = $this->mysql->query("SELECT * FROM eshops WHERE ownerID = ".$ID);
				$r = $stmt->fetch_assoc();
			}
			

			return $r; 
		}*/


/*
		public function verify_new_diy() {
			
			if ( isset($_GET["id"]) ) {
				
				$urlID = $_GET["id"];
				
				if ( $urldiy = $this->verify_diy($urlID) ) {
					setcookie("DIY", $urldiy, strtotime("+1 months"), '/', $this->domain());
				} else {
					$this->diyID = $this->new_diy();
				}

			} else {
				$this->diyID = $this->new_diy();
			}
		}

		public function new_diy() {
			$user = $this->GD->userdata;
			$new = strtoupper( $this->random_chars_oddo(10, 20) );
			$time = time();

			if ( $this->mysql->query("INSERT INTO concepts VALUES (NULL, '$new', ".$user["userID"].", $time, '', '', '')") ) {
				setcookie("DIY", $new, strtotime("+1 months"), '/', $this->domain());

				header( "Location: ".$this->GD->link(12)."/".$new );
			}
		}

		public function verify_diy($data) {
			$user = $this->GD->userdata;

			$stmt = $this->mysql->query("SELECT system_id FROM concepts WHERE system_id = '$data' AND user_id = ".$user["userID"]);

			if ( $r = $stmt->fetch_assoc() )
				return $r["system_id"];
		}*/
		/*public function generate_page_with_filters($filters, $r = "") {
			$activeQuery = $activeCount = $activePage = $activeHead = "";

			foreach ($filters as $key => $value) {
				
				$stmt = $this->mysql->query("SELECT count(id) as total FROM ".$value["q"]);
				$re = $stmt->fetch_assoc();

				if ( $this->CHR->CHECK_SUBPAGE() == $value["page"] ) {
					$r .= '<a href="'.$this->GD->link($value["page"]).'" id="ns-A">'.$this->GD->text($value["text"]).'<div class="ns-counter"><span>'.$re["total"].'</span></div></a>';
					$activeQuery = $value["q"];
					$activeCount = $re["total"];
					$activePage = $value["page"];
					$activeHead = $value["head"];
				}
				else
					$r .= '<a href="'.$this->GD->link($value["page"]).'">'.$this->GD->text($value["text"]).'<div class="ns-counter"><span>'.$re["total"].'</span></div></a>';
			}

			return array("menu" => $r, "activeQuery" => $activeQuery, "activeCount" => $activeCount, "activePage" => $activePage, "head" => $activeHead);
		}*/

		/*public function check_subpage($pagename, $r = '') {
			$stmt = $this->mysql->query("SELECT page FROM cms_pages WHERE name = '$pagename'");
			if ( $r = $stmt->fetch_object() ) return $r->page;
		}
*/
		public function shop_menu($page, $r = "", $active = "") {
			
			$pages = array(
				101 => array("text" => 199,
							"icon" => " i i121"),
				102 => array("text" => 200,
							"icon" => " i i122"),
				103 => array("text" => 201,
							"icon" => " i i123"),
				104 => array("text" => 202,
							"icon" => ""),
				);

			
			foreach ($pages as $key => $value) {

				$active = $page == $key ? " shopmenu-ready" : " shopmenu";

				$r .= '
					<div class="pbl'.$active.'">
						<a href="'.$this->GD->link($key).'"><div class="pbl-icon'.$value["icon"].'"></div><div class="pbl-title">'.$this->GD->text( $value["text"] ).'<div class="pbl-punt i i120"></div></div></a>
					</div>
				';
			}
			/*
			$r = '
			<div class="pbl shopmenu-ready">
				<a href=""><div class="pbl-icon"></div><div class="pbl-title">'.$GD->text(199).'<div class="pbl-punt i i120"></div></div></a>
			</div>
			<div class="pbl shopmenu">
				<a href=""><div class="pbl-icon"></div><div class="pbl-title">'.$GD->text(200).'</div></a>
			</div>
			<div class="pbl shopmenu">
				<a href=""><div class="pbl-icon"></div><div class="pbl-title">'.$GD->text(201).'</div></a>
			</div>
			<div class="pbl shopmenu">
				<a href=""><div class="pbl-icon"></div><div class="pbl-title">culpa qui officia deserunt</div></a>
			</div>
			';
*/
			return $r;
		}
	}

