<?php
	//require_once ("/navody.php");
	
	//$RF = new RESULT_FILTERS();

	class RESULT_FILTERS {
		
		protected static $instance;
		protected $mysql;
		protected $GD;

		private $ajax_page;
		//protected $pagedata;

		public function __construct($ajax_page = "") {
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			//$this->GI = GEN_ITEMS::init();

			$this->ajax_page = $ajax_page;

			$this->url = isset($_GET["filter"]) ? array_filter(explode("-", $_GET["filter"])) : "";

			//echo $this->check_duplicate_in_url();
			$this->searchin = "url, title, description";
		}
		
		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new RESULT_FILTERS();
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

		/*public function check_duplicate_in_url($r = '') {
			$no = array(1);

			foreach ($no as $key => $type) {
				foreach ($this->url as $key2 => $value) {
					$r += $this->GD->count_results( "filters WHERE ".$this->CHR->LANG." = '$value' AND type = ".$type );
				}
			}
			
			
			return $r;
		}*/


		public function action($page, $data, $r = "") {

			//$this->data = $this->GD->create_DATA($data);

			//$data = $this->data;
			$this->page = $page;

			switch ($page) {
				case 5:
					$r = $this->find_data( $data );
					//$re = $this->GI->generate_items("search", array("search" => $search, "keys" => $keys) );

					//$r = array(
					//	'r' => "",
					//	"head"	=> sprintf($this->GD->text(130), $this->data),
					//	"url" => $this->GD->link(5)."/".$data."/" );
					$r = array( "q" => $r, "filters" => $this->create_filter_reset() );
				break;
			}
			

			return $r;
		}

		public function select_from($DATA, $r = '') {
			
			switch ($this->page) {
				case 5: $r = "navody WHERE category = '".$DATA->category."'"; break;
				
			}

			return $r;
		}


		public function find_data($data) {
			//$PD = $this->GD->pagedata();
			$page = $this->GD->pager();

			$queries = array( 'from' => $this->select_from( $data ), 'filter' => $this->create_data() );
			
			$q = $queries["from"]." AND public = '1' ".$queries["filter"];

			return $q;
		}


		public function create_data($r = "") {
			$COLOR = $ORDER = $DISCOUNT = $PRICE = $STOCK = array();
			$selectG = "text, parameter";
			//$explodeURL = array_filter(explode("-", $_GET["filter"]));
			$lang = $this->CHR->LANG;

			foreach ($this->url as $key => $value) {
				
				if ( preg_match('/'.$this->order_filters().'^$/i', strtolower($value) ) ) {
					
					if ( $stmt = $this->mysql->query("SELECT $selectG FROM filters WHERE ".$lang." = '$value' AND type = 1") ) {
						if ( $re = $stmt->fetch_assoc() ) {
							$ORDER["order"] = array('type' => "order", 'text' => $re["text"], 'url' => $value, "sql" => $re["parameter"]); 
							$this->order_filters = $value;
						}
					}
				}
				else if ( preg_match('/farba|color|^$/i', strtolower($value) ) ) {
					$data = explode("_", $value);

					if ( $color = $this->verify_color( $data[1] ) ) {

						$COLOR[ $color->$lang ] = array('type' => "color", 'text' => $color->text, 'url' => $value, "sql" => "LIKE '%#".$color->id."#%'");

						$this->color_filters[] = $value;
					}
				}
				else if ( preg_match('/so_zlavou|discount|^$/i', strtolower($value) ) ) {
					
					if ( $stmt = $this->mysql->query("SELECT $selectG FROM filters WHERE ".$lang." = '$value'") ) {
						if ( $re = $stmt->fetch_object() ) {
							$DISCOUNT[ "discount" ] = array('type' => "discount", 'text' => $re->text, 'url' => $value, "sql" => $re->parameter); 
							$this->discount_filter = $value;
						}
					}
				}
				else if ( preg_match('/skladom|stock|^$/i', strtolower($value) ) ) {
					
					if ( $stmt = $this->mysql->query("SELECT $selectG FROM filters WHERE ".$lang." = '$value'") ) {
						if ( $re = $stmt->fetch_object() ) {
							$STOCK[ "stock" ] = array('type' => "stock", 'text' => $re->text, 'url' => $value, "sql" => $re->parameter); 
							$this->stock_filter = $value;
						}
					}
				}
				else if ( preg_match('/min|max|^$/i', strtolower($value) ) ) {
					$data = explode("_", $value);

					if ( $stmt = $this->mysql->query("SELECT $selectG, sk, en FROM filters WHERE ".$lang." = '".$data[0]."' AND type = 3") ) {
						if ( $re = $stmt->fetch_assoc() ) {

							$PRICE[ $re->$lang ] = array('type' => $re->$lang, 'price' => $data[1], 'text' => $data[1], 'url' => $value, "sql" => sprintf($re->parameter, $data[1]) );

							$this->price_filters[] = $value;
						}
					}
				}
			}

			$r = array_merge($COLOR, $DISCOUNT, $STOCK, $PRICE, array_filter($ORDER));

			$this->active_filters = $r;

			return $this->create_sql_commands( $this->active_filters );
		}

		public function create_sql_commands($data, $r = '') {
			$count = [];


			foreach ($data as $key => $value) {
				$count[] = $value["type"];


			}

			return $this->filter_query( array_filter($data), array_count_values($count) );
		}


		public function filter_query($data, $count, $r = '') {
			$index = 0;
			$re = "";

			$default_order = " ORDER BY create_date DESC";

			//$selectors = array("color" => "colors");


			foreach ($data as $key => $value) {
				
				if ( $value["type"] == "color" ) {
					$index += 1; 

					if ( $count["color"] >= 1 ) {

						if ( $index == 1 ) {
							$re .= "AND (colors ".$value["sql"];
						} else {
							$re .= " OR colors ".$value["sql"];
						}

						$r = $re.")";
					} else {
						$r .= "AND colors ".$value["sql"];
					}
				}

				if ( $value["type"] == "discount" )
					$r .= " AND ".$value["sql"];

				if ( $value["type"] == "min" )
					$r .= " AND ".$value["sql"];

				if ( $value["type"] == "max" )
					$r .= " AND ".$value["sql"];

				if ( $value["type"] == "stock" )
					$r .= " AND ".$value["sql"];

				if ( $value["type"] == "order" )
					$r .= " ".$value["sql"];
			}

			return $r;
		}

		public function order_filters($r = '') {
			$lang = $this->CHR->LANG;

			$this->mysql->q("SELECT $lang FROM filters WHERE type = 1 AND visible = 1");

			foreach ($this->mysql->resultO() as $key => $value) {
				$r .= $value->$lang."|";
			}

			return $r;
		}

		public function verify_color($color) {
			if ( isset($color) ) {

				if ( $stmt = $this->mysql->query("SELECT id, text, sk, en FROM filter_colors WHERE ".$this->CHR->LANG." = '$color'") )
					return $stmt->fetch_object();
			}
		}











		public function create_filter_reset($re = "") {
			
			$types = array(
				'order' => array('text' => $this->GD->text(148) ), 
				'color' => array('text' => $this->GD->text(655) ),
				'discount' => array('text' => $this->GD->text(658) ),
				'min' => array('text' => $this->GD->text(664) ),
				'max' => array('text' => $this->GD->text(665) ),
				'stock' => array('text' => $this->GD->text(669) ) );

			if ( isset($this->active_filters) ) {
				foreach ($this->active_filters as $key => $value) {
					/*if ( $value["type"] == "color" )
						$link = $this->create_filter_link("color", $value["url"]);
					} else if ( $value["type"] == "order" ) {
						$link = $this->create_filter_link("order", $value["url"]);
					} else if ( $value["type"] == "discount" ) {
						$link = $this->create_filter_link("discount", $value["url"]);
					}
*/
					$link = $this->create_filter_link($value["type"], $value["url"]);
					
					$val = !isset($value["price"]) ? mb_strtoupper($this->GD->text($value["text"]), "UTF8") : $this->GD->price($value["price"]);

					$re .= '<a href="'.$this->GD->make_link($link, $this->ajax_page).'" class="appliedF" title="'.$this->GD->text(150).'">'.sprintf($types[$value["type"]]["text"], '<strong>'.$val.'</strong>' ).'<span><i class="ii iExit"></i></span></a>';
				}

				$re .= '<a href="'.$this->GD->make_link("", $this->ajax_page).'" class="appliedF appAll"><strong>'.$this->GD->text(666).'</strong><span><i class="ii iExit"></i></span></a>';

				return '
				<div class="applied-filters">

					'.$re.'
					<div class="cleaner"></div>
				</div>';
			}
		}

		public function create_filter_link($type, $remove = "",  $r = "") {
			$re = "";

			$count = count($this->active_filters);

			foreach ($this->active_filters as $key => $value) {
				
				if ( $remove != $value["url"] ) {
					$re .= $value["url"]."-";
					//$re .= $value["url"];
				}
			}

			if ( $re ) {
				//if ( $count != 2)
					$r = substr($re, 0, -1);
				//else
					//$r = $re;
			}
			else
				$r = $re;

			return $r;
		}
	}

