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
				case 5: $r = "navody WHERE (category LIKE '%".$DATA->category."'"; break;
				
			}

			return $r;
		}







		public function find_data($data) {
			//$PD = $this->GD->pagedata();
			$page = $this->GD->pager();

			$queries = array( 'from' => $this->select_from( $data ), 'filter' => $this->create_filter() );
			
			$q = $queries["from"]." AND public = '1') ".$queries["filter"];



			/*$this->count = $this->GD->count_results($q);
			$oddo = $this->GD->oddo( $page, $this->count, $this->ajax_page );


			$this->mysql->query("SELECT * FROM $q "." LIMIT ".$oddo["od"].", ".$oddo["do"]);*/
		
			return $q;
		}





		public function create_filter() {
			$re = "";
			$default_order = "ORDER BY 'create_date' DESC";

			if ( isset($_GET["filter"]) && strlen($_GET["filter"]) > 0 ) {
				
				if ( $filters = $this->verify_filter($_GET["filter"]) ) {

					if ( isset($filters["color"]) && isset($filters["order"]) ) {

						/*if ( is_array($filters["color"]) ) {
							$re = $this->array_from_colors($filters["color"]);

							$r = $re." ".$filters["order"];
						} else 
							$r = 'AND '.$filters["color"]." ".$filters["order"];*/

						$r = $this->array_from_colors($filters["color"])." ".$filters["order"];
					}
					else {

						if ( isset($filters["color"]) ) {

							/*if ( is_array($filters["color"]) ) {
								$re = $this->array_from_colors($filters["color"]);

								$r = $re.") ".$default_order;
							} else
								$r = 'AND '.$filters["color"]." ".$default_order;*/
							$r = $this->array_from_colors($filters["color"])." ".$default_order;
						}


						if ( isset($filters["order"]) ) {

							if ( isset($filters["color"]) ) {
								$r = $this->array_from_colors($filters["color"]).$filters["order"];
							} else
								$r = $filters["order"];
						}
					}
				}
			} else
				$r = $default_order;

			return $r;
		}

		public function array_from_colors($filter, $r = '') {
			if ( is_array($filter) ) {

				if ( count($filter) > 1 ) {
					foreach ($filter as $key => $value) {
						if ( $key == 1 )
							$r .= "AND (".$value;
						else
							$r .= 'OR '.$value;
					}
				} else {
					foreach ($filter as $key => $value) {
						$r .= "AND (".$value;
						
					}
				}
				

				$r .= ")";
			} else {
				$r .= ")";
			}
			/*else {

				/*foreach ($filter as $key => $value) {
					$r = 'AND '.$value;
				}*/
				
				//$r = 'AND '.$filter["color"];
			//}
			return $r;
		}

		public function order_filters($r = '') {
			$this->mysql->q("SELECT * FROM filters WHERE type = 1");

			foreach ($this->mysql->result2() as $key => $value) {
				$r .= $value[$this->CHR->LANG]."|";
			}

			return $r;
		}

		public function verify_filter($filter) {
			$c = $cc = 0;

			if ( isset($filter) ) {

				$filters = explode("-", $filter);
				
				foreach ($filters as $key => $value) {
					
					if ( preg_match('/'.$this->order_filters().'^$/i', strtolower($value) ) ) {
						$type = "order";
						
						if ( $stmt = $this->mysql->query("SELECT * FROM filters WHERE ".$this->CHR->LANG." = '$value' AND type = 1") ) {
								if ( $r = $stmt->fetch_assoc() ) {

									$result[$type] = $r["parameter"];

									$this->filter_order = $value;
									$active[$type] = array('text' => $r["text"], 'url' => $value); 
								}
							}
					}
					else if ( preg_match('/farba|color|^$/i', strtolower($value) ) ) {
						$type = "color";
						$data = explode("_", $value);

						//$colorFilters = array_count_values($data);

						if ( $color = $this->verify_color( $data[1] ) ) {
							//if ( $stmt = $this->mysql->query("SELECT parameter FROM filter_search WHERE id = 5 AND visible = '1'") ) {
								//if ( $r = $stmt->fetch_assoc() )
								//if ( $colorFilters["farba"] == 1 )
								$result[$type][$key] = "colors LIKE '%".$color["id"]."' ";
							
								$this->filter_color[$key] = $value;
								$active[$type][$key] = array('text' => $color["text"], 'url' => $value);
							//}
						} 
					}



/*
					switch ($type) {
						case 'order':
							if ( $stmt = $this->mysql->query("SELECT * FROM filters WHERE ".$this->CHR->LANG." = '$value' AND type = 1") ) {
								if ( $r = $stmt->fetch_assoc() ) {

									$result[$type] = $r["parameter"];

									$this->filter_order = $value;
									$active[$type] = array('text' => $r["text"], 'url' => $value); 
								}
							}
							break;
						case 'color':
							$data = explode("_", $value);

							if ( $color = $this->verify_color( $data[1] ) ) {
								//if ( $stmt = $this->mysql->query("SELECT parameter FROM filter_search WHERE id = 5 AND visible = '1'") ) {
									//if ( $r = $stmt->fetch_assoc() )
										$result[$type] = "colors LIKE '%".$color["id"]."%'";


									$this->filter_color = $value;
									$active[$type] = array('text' => $color["text"], 'url' => $value);
								//}
							} 
							break;
					}*/
					/*
					} else {
						$type = "category";

						$kategoria = explode("__", $value);

						if ( $category = $this->verify_category($kategoria[1]) ) {
							if ( $stmt = $this->mysql->query("SELECT parameter FROM filter_search WHERE id = 5 AND visible = '1'") ) {
								if ( $r = $stmt->fetch_assoc() )
									$result[$type] = sprintf( $r["parameter"], $category["category"] );


								$this->filter_category = $value;
								$active[$type] = array('text' => $category["text"], 'url' => $value);
							}
						} 
					}*/
				}

				$this->active_filters = $active;
				return $result;
			}
		}


		public function verify_category($category) {
			if ( isset($category) ) {

				if ( $stmt = $this->mysql->query("SELECT * FROM kategorie WHERE ".$this->CHR->LANG." = '$category'") )
					return $stmt->fetch_assoc();
			}
		}

		public function verify_color($color) {
			if ( isset($color) ) {

				if ( $stmt = $this->mysql->query("SELECT * FROM filter_colors WHERE ".$this->CHR->LANG." = '$color'") )
					return $stmt->fetch_assoc();
			}
		}

		public function create_filter_bar() {
			
			return '
				<div class="filter-type">
					<a href="#" class="ft show-filter"><span>'.$this->GD->text(139).'</span><div class="i i35"></div></a>
					<div class="filter-menu dH" id="filterO">
						<div class="fm-h">'.$this->GD->text(146).' <button type="button" class="closeb i i38" name="#filterO"></button> </div>
						'.$this->filter_bar_categories().'
					</div>
				</div>

				<div class="filter-type">
					<a href="#" class="ft show-filter"><span>'.$this->GD->text(140).'</span><div class="i i35"></div></a>
					<div class="filter-menu dH" id="filterS">
						<div class="fm-h">'.$this->GD->text(145).' <button type="button" class="closeb i i38" name="#filterS"></button> </div>
						<div style="text-align: left;">
							'.$this->filter_bar_orders().'
						</div>
					</div>
				</div>

				<div class="cleaner"></div>
				'; 

			//filterA
		}


		public function filter_bar_categories($re = "") {
			//$PD = $this->GD->pagedata();
			$l = $this->CHR->LANG;
			
			if ( $stmt = $this->mysql->query("SELECT * FROM kategorie ORDER BY ".$l) ) {
				
				foreach ( $this->mysql->result2() as $key => $value ) {
					$count = $this->count_diy_by_category($value["category"]);

					if ( $count > 0 ) {
						if ( isset($this->filter_order) )
							$link = $this->GD->make_link( sprintf($this->GD->text(142), $value[$l])."-".$this->filter_order, $this->ajax_page );
						else
							$link = $this->GD->make_link( sprintf($this->GD->text(142), $value[$l]), $this->ajax_page );

						$re .= '<a href="'.$link.'" class="fla">'.ucfirst($this->GD->text($value["text"]) ).' <span>('.$count.')</span> </a>';
					}
				}
			}

			if ( isset($this->filter_color) )
				$re .= '<a href="'.$this->GD->make_link( $this->create_filter_link("category"), $this->ajax_page ).'" class="fla fla-reset" style="text-align: center;">'.ucfirst($this->GD->text(154) ).'</a>';

			return $re;
		}

		public function filter_bar_orders($re = "") {
			$l = $this->CHR->LANG;

			if ( $this->mysql->query("SELECT * FROM filter_search WHERE visible = '1' AND type = '1'") ) {
				
				foreach ( $this->mysql->result2() as $key => $value ) {
					
					if ( isset($this->filter_category) )
						$link = $this->GD->make_link( $this->filter_category."-".$value[$l], $this->ajax_page );
					else
						$link = $this->GD->make_link( $value[$l], $this->ajax_page );

					$re .= '<a href="'.$link.'" class="fla">'.ucfirst($this->GD->text($value["text"]) ).'</a>';
				}
			}

			if ( isset($this->filter_order) )
				$re .= '<a href="'.$this->GD->make_link( $this->create_filter_link("order"), $this->ajax_page ).'" class="fla fla-reset">'.ucfirst($this->GD->text(154) ).'</a>';

			/*$types = array(
				'order' => array('text' => $this->GD->text(148), 'link' => $this->create_filter_link("order")  ), 
				'category' => array('text' => $this->GD->text(147), 'link' => $this->create_filter_link("category") ) );*/

			return $re;
		}




		public function create_filter_reset($re = "") {
			
			$types = array(
				'order' => array('text' => $this->GD->text(148), 'link' => $this->create_filter_link("order")  ), 
				'color' => array('text' => $this->GD->text(655) ) );

			if ( isset($this->active_filters) ) {
				foreach ($this->active_filters as $key => $value) {
					//$re .= '<span> <a href="'.$this->GD->make_link($types[$key]["link"], $this->ajax_page).'" title="'.$this->GD->text(150).'">'.sprintf($types[$key]["text"], '<b>'.ucfirst($this->GD->text($value["text"])).'</b>' ).'<div class="iFi i i34"></div></a> </span>';
					if ( $key == "color" ) {
						//$this->create_filter_link("color", )
						foreach ($value as $key2 => $value2) {
							$link = $this->create_filter_link("color", $value2["url"]);

							$re .= '<a href="'.$this->GD->make_link($link, $this->ajax_page).'" class="appliedF" title="'.$this->GD->text(150).'">'.sprintf($types[$key]["text"], '<strong>'.mb_strtoupper($this->GD->text($value2["text"]), "UTF8").'</strong>' ).'<span><i class="fa fa-times" aria-hidden="true"></i></span></a>';
						}
					} else
						$re .= '<a href="'.$this->GD->make_link($types[$key]["link"], $this->ajax_page).'" class="appliedF" title="'.$this->GD->text(150).'">'.sprintf($types[$key]["text"], '<strong>'.mb_strtoupper($this->GD->text($value["text"]), "UTF8").'</strong>' ).'<span><i class="fa fa-times" aria-hidden="true"></i></span></a>';
				}

				return '
				<div class="applied-filters">
					'.$re.'
					<div class="cleaner"></div>
				</div>';
			}
		}

		public function create_filter_link($type, $data = "",  $r = "") {
			$re = "";

			switch ($type) {
				case 'order':

					if ( isset($this->filter_color) ) {
						if ( is_array($this->filter_color) ) {
								
							foreach ($this->filter_color as $key => $value) {
								$re .=  $value != $data ? $value."-" : "";
							}

							$r = substr($re, 0, -1);
						} else {
							$r = $this->filter_color;
						}
					}
					
					/*if ( isset($this->filter_order) )
						$r = $this->filter_order;*/
					break;

				case 'color':
					//if ( isset($this->filter_order) )
						//$r = $this->filter_color;
						if ( is_array($this->filter_color) ) {
							
							foreach ($this->filter_color as $key => $value) {
								$re .=  $value != $data ? $value."-" : "";
							}

							$r = isset($this->filter_order) ? substr($re, 0, -1)."-".$this->filter_order : substr($re, 0, -1);
							//$r = substr($re, 0, -1)."-".$this->filter_order;
						} else {
							$r = $this->filter_color."-".$this->filter_order;
						}
					break;
/*
				case 'color':
					if ( isset($this->filter_order) )
						$r = $this->filter_order;
					break;*/
			}

			return $r;
		}


		public function count_diy_by_category($category) {
			$queries = array( 'default' => $default = '+"'.$this->data.'"', 'data' => $this->keys_query(), 'tags' => $this->tags_query(), 'filter' => $this->create_filter() );

			$stmt = $this->mysql->query("SELECT COUNT(id) as total FROM navody WHERE ( MATCH(".$this->searchin.") AGAINST('".$queries["data"]."' IN BOOLEAN MODE) OR MATCH(".$this->searchin.") AGAINST('".$queries["data"]."' IN BOOLEAN MODE) ) ".$queries["tags"]." AND category = '$category' AND public = '1' ".$queries["filter"] );

			$count = $stmt->fetch_assoc();

			return $count["total"];
		}
	}

