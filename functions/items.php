<?php
	//require_once ("./functions/filters.php");
	//require_once ("/basket.php");

	$GI = new GEN_ITEMS();

	class GEN_ITEMS {

		protected static $instance;
		protected $mysql;
		public $CHR;
		public $GD;
		public $RF;
		//protected $instructions;
		//public $online = parent::online;
		public $IP;
		//public $SHOP;
		public $AJAX;

		public function __construct() {
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			//$this->RF = RESULT_FILTERS::init();
			$this->BA = BASKET::init();
			//$this->instructions = DoItYourself::init();
			$this->AJAX = false;
			//$this->U = $this->GD->USERDATA_info();
			/*if ( $this->CHR->PD->page == 16 ) {
				$this->IP = $this->GD->ITEMPROFILE_O( $this->mysql->safe($_GET["p"]) );
				$this->SHOP = $this->GD->SHOPDATA_O( $this->IP->shop_id );
			}
			*/
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new GEN_ITEMS();
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


		public function create_query_id($data, $selectname, $r = '') {
			
			if ( $data ) {
				$arr = array_filter( explode(".", $data ));
				$c = count($arr);

				foreach ( $arr as $key => $value) {
				
					if ( $c == 1 ) {
						$r = $selectname.' = '.$value;
					} else {
						if ( $key == 0 )
							$r .= $selectname.' = '.$value;
						else 
							$r .= ' OR '.$selectname.' = '.$value;
					}
				}

				return $r;
			}
		}

		public function create_query_tags($tags, $r = '') {
			
			if ( $tags ) {
				$data = array_filter( explode("#", $tags) );

				$c = count($data);

				foreach ( array_filter( explode("#", $tags) ) as $key => $value) {
				
					if ( $c == 1 ) {
						$r = $value;
					} else {
						if ( $key == 1 )
							$r .= " ".$value."|";
						else 
							$r .= $value."|";;
					}
				}

				return "REGEXP '".$r."'";
			}
		}

		public function generate_items($page, $DATA = "", $BASKET = "", $keywords = '', $AJAX = false, $r = '') {

			//$this->BA = new BASKET();

			if ( $AJAX )
				$this->AJAX = true;

			$selectGeneral = 'id, file_list, url, title, description, price, discount, quantity, availability, create_date, tags';
			
			$nodata_content = '<p class="nr-head">'.$this->GD->text(738).'</p>';
			$R = $nothing = "";

			$this->basket = isset($BASKET) ? $BASKET : "";

			$c = $count_all = $oddo = $showEmpty = 0;
			$rE = $filter = "";
			$show =  array("price", "desc", "discount");
			//$show =  array("price", "stats", "desc", "tags", "discount");

			$this->w = iTEM_W25;

			$showEmpty = true;
			$type = 1;

			switch ($page) {
				case 'index':
					$count_all = $this->GD->count_results("navody WHERE public = 1 AND availability = '1'");

					$oddo = $this->GD->oddo( $this->GD->pager(), $count_all );

					$this->mysql->query("SELECT $selectGeneral FROM navody WHERE public = 1 AND availability = 1 ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);

					$R = $this->mysql->resultO();

					if ( !$R ) {
						$this->mysql->query("SELECT $selectGeneral FROM navody WHERE public = 1 AND availability = 8 ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);

						$R = $this->mysql->resultO();
					}

					$this->w = iTEM_W20;
					break;

				case "itemrelated":
					$this->w = iTEM_W20;

					$q = $DATA["tags"] ? "tags ".$this->create_query_tags($DATA["tags"])." AND" : "";

					$this->mysql->query("SELECT $selectGeneral FROM navody WHERE ".$q." public = 1 AND availability = 1 AND id != '".$DATA["id"]."' ORDER BY RAND() LIMIT ".ITEM_RELATED);

					if ( !$related = $this->mysql->resultO() ) {
						$stmt2 = $this->mysql->query("SELECT $selectGeneral FROM navody WHERE public = 1 AND availability != 8 AND availability != 0 AND id != '".$DATA["id"]."' ORDER BY RAND() LIMIT ".ITEM_RELATED);
						$R = $this->mysql->resultO();
					} else {
						$R = $related;
					}

					$showEmpty = false;
					$show = array("price", "discount", "desc");
					break;

				case "itemhistory":
					$cc = 0;
					$this->w = iTEM_W20;
					
					foreach ( array_filter( explode(".", $DATA["ids"]) ) as $key => $value) {
						$cc += 1;

						if ( $cc <= ITEM_HISTORY ) {
							$stmt = $this->mysql->query("SELECT $selectGeneral FROM navody WHERE id = '".$value."' AND public = 1");

							if ( $re = $stmt->fetch_object() )
								$R[] = $re;
						}
					}

					$showEmpty = false;
					$show = array("price", "discount");
					break;

				case "tags":
					
					$count_all = $this->GD->count_results("navody WHERE tags LIKE '%#".$DATA["tags"]."#%' AND public = 1");

					$oddo = $this->GD->oddo( $this->GD->pager(), $count_all );

					$this->mysql->query("SELECT $selectGeneral FROM navody WHERE tags LIKE '%#".$DATA["tags"]."#%' AND public = 1 ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);


					$R = $this->mysql->resultO();
					break;

				case "search":
					$this->keywords = $keywords;

					$R = $DATA["search"];

					break;

				case 'category':
					if ( isset($_GET["filter"]) ) {
						$this->RF = RESULT_FILTERS::init();

						$re = $this->RF->action($this->CHR->PD->page, $DATA);

						$count_all = $this->GD->count_results( $re["q"] );
						$oddo = $this->GD->oddo( $this->GD->pager(), $count_all );

						$this->mysql->query("SELECT $selectGeneral FROM ".$re["q"]." LIMIT ".$oddo["od"].", ".$oddo["do"]);
						$R = $this->mysql->resultO();

						$filter = $re["filters"];
						
						$nodata_content  = '
						<p class="nr-head">'.sprintf($this->GD->text(719), '<strong>'.$this->GD->text(721).'</strong>' ).'</p>
						<p class="nr-info">'.$this->GD->text(720).'</p>';
					}
					else {
						$count_all = $this->GD->count_results("navody WHERE public = 1 AND category = '".$DATA->category."'");
						$oddo = $this->GD->oddo( $this->GD->pager(), $count_all );

						$this->mysql->query("SELECT $selectGeneral FROM navody WHERE public = 1 AND category = '".$DATA->category."' ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);

						$R = $this->mysql->resultO();

						$nodata_content  = '
						<p class="nr-head">'.sprintf($this->GD->text(719), '<strong>'.$this->GD->text(721).'</strong>' ).'</p>';
					}

					$this->w = iTEM_W20;
					break;
			}


			if ( $R ) {
				foreach ($R as $key => $d) {
					$c++; 
				
					$r .= $this->gen_content( $d, $show, $c, $type);
					
				}


				$rE = $showEmpty == true ? $this->gen_empty($c, $this->w) : "";
			} else
				$r = $this->empty_results($page, $nodata_content);

			return array('diy' => $r.$rE, 'count' => $count_all, 'list' => $oddo["list"], 'filters' => $filter);
		}



		public function empty_results($page, $content, $r = '') {
			
			switch ($page) {
				/*case 'index':
					
					break;
				*/
				default:
					$r = '
						<div class="noResults">
							<div class="nr-content">
								<div class="ii iLight"></div>
								'.$content.'
							</div>
						</div>
					';
					# code...
					break;
			}
			return $r;
		}


		public function gen_empty($count, $width, $r = '') {

			$inRow = 100 / $this->w;
			$rows = ceil($count / $inRow);
			

			$empty = ($rows * $inRow) - $count;

			for ($i=0; $i < $empty; $i++) { 
				$r .= '
				<div class="w2 diyEmpty"><div class="diyContent"></div></div>
				';
			}

			return $r;
		}
		public function gen_price($data, $r = '') {
			$re = "";

			$prefix = array(628, 629, 630);
			$prefix2 = array(631, 632, 633);

			switch ( $data->availability ) {
				case 1:
					$price = $this->GD->price($data->price);
					$nu = number_format($data->price, 2, '.', '');


					if ( $data->discount ) {
						$re = '<del>'.$price.'</del><span itemprop="price" content="'.$nu.'" itemprop="priceCurrency" content="EUR">'.$this->GD->price( $this->GD->discount($data->price, $data->discount) ).'</span>';
					} else {
						$re = '<span itemprop="price" content="'.$nu.'">'.$price.'</span>';
					}

					/*$r .= '<span class="diyDesc-pieces">'.sprintf( $this->GD->text(631), $data->quantity).' '.$this->GD->word_ending($data->quantity, $prefix).'</span>';*/

					$r = '<div class="dipp" itemprop="priceCurrency" content="EUR">'.$re.'</div>';
				break;

				case 8: 
					$r = '<div class="dipt">'.$this->GD->text(293).'</div>';
				break;

				default: 
					$r = '<div class="dipt">'.$this->GD->text(507).'</div>';
				break;
			}

			return '<div class="diyDesc-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">'.$r.'</div>';
		}

		public function gen_content($data, $SD, $count, $type = 1, $showEmpty = false, $r = "") {
			
			$DESC = $BASKET = $NEW = '';

			$timeToLaik = new cas( date("d.m.Y H:i", $data->create_date) );

			$imgLink = $this->GD->generate_pictureUrl( $data->file_list, true, $this->AJAX );
			$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
			

		//$link_category = $data["category"] ? $this->GD->url( "kategoria/".$this->GD->category_data( $data["category"], $this->CHR->LANG )) : "";

			$link_url = $this->GD->url( $data->url );
			
			$PRICE = $data->price != 0 && in_array("price", $SD) ? $this->gen_price($data) : "";

			//$st =  $this->gen_stats($data->id);
			//$STATS = $st && in_array("stats", $SD) ? $this->show_stats( $st ) : "";

			if ( $data->description && in_array("desc", $SD) ) {

				//$desc = mb_strimwidth( ucfirst($data->description), 0, 100 );
				$desc = ucfirst($data->description);

				if ( isset($this->keywords) ) {

					if ( $find = $this->color_text( $desc ) )
						$DESC = '<p class="diyDesc-body nowrap"><span itemprop="description">'.$find.'</span></p>';
					else {
						$DESC = '<p class="diyDesc-body nowrap"><span itemprop="description">'.$desc.'</span></p>';
					}
				}
				else
					$DESC = '<p class="diyDesc-body nowrap"><span itemprop="description">'.$desc.'</span></p>';
			}

			//$DESC = $data->description && in_array("desc", $SD) ? '<p class="diyDesc-body nowrap">'.mb_strimwidth(ucfirst($data->description), 0, 100).'</p>' : "";

			$TAGS = $data->tags && in_array("tags", $SD) ? '
					<div class="diyDesc-footer">
						<div class="diyDesc-menu">
							'.$this->show_tags( explode("#", $data->tags) ).'
						</div>
						<div class="cleaner"></div>
					</div>' : "";

			$AVAIL = $data->availability ? $this->GD->gen_availability( $data->availability ) : "";

			$DISCOUT = $data->discount && in_array("discount", $SD) ? '<div class="diyDiscount" title="'.$this->GD->text(506).'"><span class="ii iDisco"></span><strong> - '.$data->discount.'%</strong></div>' : "";

			$brb = $this->GD->basket_data($this->BA->basket);

			if ( isset($brb['d'][$data->id]) ) {

				$count = $data->id == $brb['d'][$data->id]["id"] ? $brb['d'][$data->id]["quantity"] : "";

				$num = sprintf($this->GD->text(710), $brb['d'][$data->id]["quantity"]." ".$this->GD->word_ending($count, array(628, 629, 630) ));

				$BASKET = strrpos($this->BA->basket->content, $data->id) !== false ? '<div class="diyBasket"><span class="ii iBag3"></span><div>'.$count.'</div><strong>'.$num.'</strong></div>' : "";
			}

			if ( isset($_COOKIE["mm-lastvisit"]) )
				$NEW = $data->create_date >= $_COOKIE["mm-lastvisit"] ? '<div class="newitem"><span>nové</span></div>' : '';
			

			if ( isset($this->keywords) ) {
				if ( $f2 = $this->color_text( $this->GD->mb_ucfirst($data->title) ) )
					$h2 = $f2;
				else
					$h2 = $this->GD->mb_ucfirst($data->title);
			} else 
				$h2 = $this->GD->mb_ucfirst($data->title);
			
			

			//$h2 = isset($this->keywords) &&  ? $this->color_text( $this->GD->mb_ucfirst($data->title) ) : $this->GD->mb_ucfirst($data->title);

			return '
				<div class="w2 diy'.$AVAIL.'" data-id="'.$data->id.'" itemscope itemtype="http://schema.org/Product">
					<div class="diyContent">
						<a href="'.$link_url.'" class="diyAnch" itemprop="url"></a>
						<img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($data->title).'" itemprop="image">

						'.$NEW.'

						'.$BASKET.'
						
						<div class="diyDesc">
							<div class="dD">
								<div class="diyDesc-content">
									'.$PRICE.'

									'.$DISCOUT.'

									<div class="diyLink">
										<div class="diyDesc-head">
											<div class="diyDesc-title nowrap" itemprop="name"><h2>'.$h2.'</h2></div>
										</div>

										'.$DESC.'

										<div class="diyDesc-time" title="'.$this->GD->adddate( $data->create_date ).'"><abbr class="upD" data-livestamp="'.$data->create_date.'">'.$timeToLaik->result().'</abbr></div>
									</div>
								</div>

								'.$TAGS.'
							</div>
						</div>
					</div>
				</div>
			';
		}

		public function color_text($text, $r = '') {
			
			foreach($this->keywords as $word) {

				if ( strrpos($text, $word) !== false )
					$r = preg_replace("|($word)|", '<strong class="sg01">'."$1".'</strong>', $text );
			}

			return $r;
		}


		public function gen_itemhistory($r = '') {
			
			if ( isset($_COOKIE["mm-history"]) ) {	
				$c = count( explode(".", $_COOKIE["mm-history"]) );

				$R = $this->generate_items("itemhistory", array("ids" => $_COOKIE["mm-history"]) );

				$slider = '<a href="#" class="sLR posLeft"><div class="ii iLeft" aria-hidden="true"></div></a>
							<a href="#" class="sLR posRight"><div class="ii iRight" aria-hidden="true"></div></a>';

				return '
					<div class="itemResults itemHistory">
						<div class="itemR-head">'.mb_strtoupper($this->GD->text(508), "UTF8").'</div>

						<div class="itemR-content slider" id="itemhistory">
							'.$slider.'

							<div class="itemR-body">
								<div class="sliderItems">
									'.$R["diy"].'
								</div>
							</div>
						</div>
						
						<div class="itemR-tools">
							<a href="#" class="jdi" data-lib="event" data-event="resethistory">'.$this->GD->text(509).'</a>
						</div>

						<div class="cleaner"></div>
					</div>
				';
			}
			
		}

		public function show_stats($d, $r='') {
			return '
			<div class="diyDescTop">
				<div class="diyStats">
					<div class="diyStat"> <div class="dI i i01"></div> <div class="dC">'.$d["likes"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(60).'</span></div> </div>
					<div class="diyStat"> <div class="dI i i02"></div> <div class="dC">'.$d["comments"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(61).'</span></div> </div>
					<div class="diyStat"> <div class="dI i i03"></div> <div class="dC">'.$d["views"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(62).'</span></div> </div>
					<div class="cleaner"></div>
				</div>
			</div>
			';
		}


		public function show_tags($d) {
			$return = $return1 = $return2 = "";

			$N = 1;

			if ( $d ) {

				$count = count( array_filter($d) );

				foreach ( array_filter($d) as $key => $value) {
					//if ( strlen($value) > 0 ) {
						$stmt 		= 	$this->mysql->query("SELECT sk, color, id_text FROM tagy WHERE id = '$value'");
						$tagData 	= 	$stmt->fetch_object();
						$link 		= 	$this->GD->link(8)."/".$tagData[$this->CHR->LANG];


						if ( !$tagData->color ) {
							$random = $this->GD->random_color();

							$tagData->color = $random;
						}
						
						if ( $count <= $N )
							$return .= '<a href="'.$link.'" class="diyDMB bL"><i>#</i>'.ucfirst( $this->GD->text($tagData->id_text) ).'</a>';
						else {
							if ( $key <= $N )
								$return1 .= '<a href="'.$link.'" class="diyDMB bL"><i>#</i>'.ucfirst( $this->GD->text($tagData->id_text) ).'</a>';

							if ( $key > $N )
								$return2 .= '<a href="'.$link.'" class="diyDMB bL"><i>#</i>'.ucfirst( $this->GD->text($tagData->id_text) ).'</a>';
						}
					//}
				}


				if ( $count <= $N ) {
					return $return;
				}
				else {
					return '
						'.$return1.'
						<button type="button" class="diyDMB moreTags" title="'.$this->GD->text(37).'"><span class="ii iTags"></span></button>
						<div class="tagContainer dH">
							'.$return2.'

							<div class="tCp"></div>
						</div>

					
					
					';
				}

			}
		}








		public function gen_stats($id, $r = "") {
			
			$tables = array('prezretia' => "id");

			foreach ($tables as $key => $value) {
				$stmt = $this->mysql->query("SELECT count(id) as total FROM ".$key." WHERE ".$value." = ".$id);
				

				if ( $re = $stmt->fetch_object() )
					$result[$key] = $re->total;
			}
			

			if ( $result )
				return array(
					/*'likes' => $result["hodnotenia"],*/
					/*'comments' => $result["komentare"],*/
					'views' => $result["prezretia"]);
		}



	}

