<?php
	//require_once ("/navody.php");
	//require_once ("/filters.php");

	$SE = new SEARCH();

	class SEARCH {
		
		protected $mysql;
		protected $GD;
		protected $instructions;

		private $tags;
		private $ajax_page;
		//protected $pagedata;

		public function __construct($ajax_page = "") {
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			$this->GI = GEN_ITEMS::init();

			//$this->instructions = DoItYourself::init();

			$this->ajax_page = $ajax_page;


			$this->searchin = "url, title, description";
			//$this->ajax_pagedata = $this->GD->CHECK();
		}
		/*
		public function create_DATA($data, $r = "") {
			
			$special = array(',', '.', '-', '/', '_', ';', '`', '~', '#', '&', '*', '+', '=', '´', '"', "'", '\\', '|');

			if ( $data ) {
				$r = preg_replace("/\s{2,}/", " ", $data);
				$r = str_replace($special, "", $r);
				$r = ltrim($r);
				$r = rtrim($r);
			}

			return $r;
		}

*/

		/*
		<div class="found-search-result">
					<div class="fsr-bar">
						<div class="fsr-filter">'.$this->create_filter_bar().'</div>
					</div>

					'.$this->create_filter_reset().'

					
					<div class="fsr-count">
						<div class="fsrc">
							<h2><span>'.$list["info"].'</span></h2>
						</div>
					</div>

					<div class="fsr-default">
						'.$body.'
						<div class="cleaner"></div>
					</div>
					<div class="cleaner"></div>
				</div>

				'.$list["list"].'

				';
				*/
		public function action($type, $data) {

			$this->data = $this->GD->create_DATA($data);

			$data = $this->data;

			if ( $type == "global-search" || $type == "p-global-search" ) {
				
				if ( strlen($data) > 0 ) {

					if ( strlen($data) > 2 ) {
						if ( $keys = $this->create_keywords($data) ) {
							//$this->AJAX = true;
							$this->keywords = $keys;
							$this->tags = $this->check_tags();

							if ( !$search = $this->find_data() )
								$return = array('r' => '

									<div class="none-filter">
									'.$this->create_filter_reset().'
									</div>

									<div class="none-search-result">
										<h2><div><span>'.$this->GD->text(126).'</span></div></h2>
										<h3><p>'.sprintf($this->GD->text(125), $data).'</p></h3>
									</div>', "head"	=> $this->GD->text(126).$this->GD->text(133), "url" => $this->GD->link(4)."/".$data."/");
							else {
								$re = $this->GI->generate_items("search", array("search" => $search, "keys" => $keys), "", $this->keywords, $this->ajax_page );

								/*$results = '
								<div class="found-search-result">
									<div class="fsr-bar">
										<div class="fsr-filter">'.$this->create_filter_bar().'</div>
									</div>

									'.$this->create_filter_reset().'

									<div class="fsr-default">
										'.$re["diy"].'
										<div class="cleaner"></div>
									</div>
									<div class="cleaner"></div>
								</div>
								';*/

								$results = '
								<div class="found-search-result">
									<div class="fsr-bar"><h2>„<strong>'.$data.'</strong>“</h2></div>

									<div class="fsr-default">
										'.$re["diy"].'
										<div class="cleaner"></div>
									</div>
									<div class="cleaner"></div>
								</div>
								';
								//$results = $this->create_results($search, $keys);

								$return = array('r' => $results, "head"	=> sprintf($this->GD->text(130), $this->data), "url" => $this->GD->link(4)."/".$data."/" );
							}
						}
					}
					else {
						$return = array('r' => '

							'.$this->create_filter_reset().'


						<div class="none-search-result">
							<h2><div><span>'.$this->GD->text(126).'</span></div></h2>
							<h3><p>'.sprintf($this->GD->text(125), $data).'</p></h3>
						</div>', "head"	=> $this->GD->text(126).$this->GD->text(133), "url" => $this->GD->link(4)."/".$data."/");
					}
				}
				else {
					//$this->GD->reset_pager();

					$return = array('r' => '');
				}

				return $return;
			}
		}















		public function find_data() {
			//$PD = $this->GD->pagedata();
			$page = $this->GD->pager();

			$queries = array( 'default' => $default = '+"'.$this->data.'"', 'data' => $this->keys_query(), 'tags' => $this->tags_query(), 'filter' => $this->create_filter() );
			
			$q = "navody WHERE ( MATCH(url, title, description) AGAINST('".$queries["data"]."' IN BOOLEAN MODE) OR MATCH(url, title, description) AGAINST('".$queries["data"]."' IN BOOLEAN MODE) ) ".$queries["tags"]." AND public = '1' ".$queries["filter"];

			$this->count = $this->GD->count_results($q);
			$oddo = $this->GD->oddo( $page, $this->count, $this->ajax_page );


			$this->mysql->query("SELECT * FROM $q "." LIMIT ".$oddo["od"].", ".$oddo["do"]);
		
			return $this->mysql->resultO();
		}


	
		public function keys_query($r = "") {
			foreach ($this->keywords as $key => $value) {
				if ( strlen($value) > 2 )
					$r .= '+'.$value.'* ';
			}

			return substr($r, 0, -1);
		}

		public function tags_query($r = "") {
			$count = $this->tags != 0 ? $this->tags : "0";

			if ( $count > 0 ) {
				foreach ($this->tags as $key => $value) {
					if ( $count == 1 )
						$r = "OR tags LIKE '%".$value."%'";
					else
						$r .= "OR tags LIKE '%".$value."%' ";
				}

				return $r;
			}
		}


		public function create_keywords($data, $r = "") {
			
			$array = explode(" ", $data);

			foreach ($array as $key => $value) {

				$text = $this->GD->text_transform($value);

				/*if ( $value == $text ) {
					$r[] = $value;
					$r[] = $text;
				} else {*/
					$r[] = $value;
					$r[] = $text;
				//}

				
			}

			if ( $r ) {
				foreach ($r as $key => $value) {
					$r[] = $this->GD->mb_ucfirst($value);
					$r[] = preg_replace('{(.)\1+}','$1', $value);
				}
			}
			

			return $r; 
		}


		public function check_tags($result = "") {
			
			foreach ($this->keywords as $key => $value) {
				$stmt = $this->mysql->query("SELECT id FROM tagy WHERE ".$this->CHR->LANG." LIKE '%$value%'");
				
				if ( $r = $stmt->fetch_assoc() ) 
					$result[] = $r["id"];
			}

			return $result;
		}







		public function create_filter() {
			
			$default_order = "ORDER BY 'create_date' DESC";

			if ( isset($_GET["filter"]) && strlen($_GET["filter"]) > 0 ) {
				
				if ( $filters = $this->verify_filter($_GET["filter"]) ) {

					if ( isset($filters["category"]) && isset($filters["order"]) ) 
						$r = 'AND '.$filters["category"]." ".$filters["order"];
					else {

						if ( isset($filters["category"]) )
							$r = 'AND '.$filters["category"]." ".$default_order;
						if ( isset($filters["order"]) )
							$r = $filters["order"];
					}
				}
			} else
				$r = $default_order;

			return $r;
		}

		public function verify_filter($filter) {
			
			if ( isset($filter) ) {

				$filters = explode("-", $filter);

				foreach ($filters as $key => $value) {
					
					if ( !preg_match('/kategoria|category|^$/i', strtolower($value) ) ) {
						$type = "order";

						if ( $stmt = $this->mysql->query("SELECT * FROM filter_search WHERE ".$this->CHR->LANG." = '$value' AND visible = '1'") ) {
							if ( $r = $stmt->fetch_assoc() ) {

								$result[$type] = $r["parameter"];

								$this->filter_order = $value;
								$active[$type] = array('text' => $r["text"], 'url' => $value); 
							}
						}
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
					}
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



		/*public function create_results($arr, $keys, $body = "") {
			$page = $this->GD->pager();


			$count = count($arr);

			foreach ($arr as $key => $data) {
				//$body .= $this->result_body($values, $keys);
				$count += 1; 
				$id = $data["id"];

				$stats = $this->instructions->diy_stats( $id );
				$diff = $this->instructions->diy_difficulty( $data["difficulty"] );
				$tags = $this->instructions->diy_tags( $data["tags"] );
				$date = $this->instructions->diy_date( $data["create_date"] );

				
				$body .= $this->instructions->diy_body_tag( $data["window_type"], $data, $stats, $diff, $tags, $date, $count);
				//$return = $news;
			}

			$list = $this->GD->oddo($page, $this->count, $this->ajax_page, true);

			return '
				<div class="found-search-result">
					<div class="fsr-bar">
						<div class="fsr-filter">'.$this->create_filter_bar().'</div>
					</div>

					'.$this->create_filter_reset().'

					
					<div class="fsr-count">
						<div class="fsrc">
							<h2><span>'.$list["info"].'</span></h2>
						</div>
					</div>

					<div class="fsr-default">
						'.$body.'
						<div class="cleaner"></div>
					</div>
					<div class="cleaner"></div>
				</div>

				'.$list["list"].'

				';
		}*/
/*
		public function result_body($data, $keys) {
			$imgLink = "imgs/".$data["id"]."/".$data["intro_img"];

			$timeToLaik = new cas( date("d.m.Y H:i", strtotime($data["date"])) );
			$date = $this->diy_date( $data["date"] );
			$link = $this->url( "diy/".$data["url"] );

			$tags = $this->diy_tags( $data["tags"] );

			$author = $this->USERDATA_info( $this->USERDATA( $data["author"] ) );
			$link_category = $this->url( "kategoria/".$this->category_data( $data["category"], $this->CHR->LANG ));
			$link_author = $this->url( $author["urlname"] );

			$author = $author["nickname"] ? $author["nickname"] : $author["username"];

			return '
				<div class="w2 diy">
					<div class="diyContent fd-content">
						<img src="'.$this->url_data($imgLink).'" class="diyImg" alt="'.ucfirst($data["intro_head"]).'">
						
						<div class="diyDesc">
							<div class="dD">
								<div class="diyDesc-content">
									<div class="diyDesc-time" title="'.$date.'"> <abbr class="upD" data-livestamp="'.strtotime($data["date"]).'">'.$timeToLaik->result().'</abbr> <span class="i i06"></span> </div>

									<a href="'.$link.'" class="diyLink">
										<h3 class="diyDesc-head">'.$this->color_text( ucfirst($data["intro_head"]) ).'</h3>
										<h4><p class="diyDesc-body">'.$this->color_text( ucfirst($data["intro_text"]) ).'</p></h4>
									</a>

									<div class="diyDesc-info">'.sprintf( $this->GD->text(156), '<a href="'.$link_author.'" class="tdu">'.$author.'</a>', '<a href="'.$link_category.'" class="tdu">'.ucfirst($this->GD->text( $this->category_data($data["category"], "text") )).'</a>' ).'</div>
								</div>

								<div class="diyDesc-footer">
									<div class="diyDesc-menu">
										'.$tags.'
									</div>
									<div class="cleaner"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			';
		}
*/
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

			if ( isset($this->filter_category) )
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
				'category' => array('text' => $this->GD->text(147), 'link' => $this->create_filter_link("category") ) );

			if ( isset($this->active_filters) ) {
				foreach ($this->active_filters as $key => $value) {
					$re .= '<span> <a href="'.$this->GD->make_link($types[$key]["link"], $this->ajax_page).'" title="'.$this->GD->text(150).'">'.sprintf($types[$key]["text"], '<b>'.ucfirst($this->GD->text($value["text"])).'</b>' ).'<div class="iFi i i34"></div></a> </span>';
				}

				return '
				<div class="fsr-reset">
					<div class="fsrr-h">'.$this->GD->text(149).'</div>
					'.$re.'
				</div>';
			}
		}

		public function create_filter_link($type, $r = "") {
			
			switch ($type) {
				case 'order':
					if ( isset($this->filter_category) )
						$r = $this->filter_category;
					break;

				case 'category':
					if ( isset($this->filter_order) )
						$r = $this->filter_order;
					break;
			}

			return $r;
		}




		public function diy_tags($data) {
			$return = $return1 = $return2 = "";

			$tags = explode("*", $data);

			$count = count($tags);

			foreach ($tags as $key => $value) {

				if ( strlen($value) > 0 ) {
					$stmt = $this->mysql->query("SELECT * FROM tagy WHERE id = $value");

					$tagData 	= 	$stmt->fetch_assoc();
					$link 		= 	$this->GD->link(8)."/".$tagData[$this->CHR->LANG];


					if ( !$tagData["color"] ) {
						$random = $this->random_color();

						$tagData["color"] = $random;
					}
						

					//$return .= '<button type="button" class="diyDMB" style="background-color: #'.$color["color"].'"><i>#</i>'.ucfirst( $GD->text($value) ).'</button>';
					if ( $count <= 2 )
						$return .= '<a href="'.$link.'" class="diyDMB bL" style="background-color: #'.$tagData["color"].'"><i>#</i>'.ucfirst( $this->GD->text($tagData["id_text"]) ).'</a>';
					else {

						if ( $key <= 1 )
							$return1 .= '<a href="'.$link.'" class="diyDMB bL" style="background-color: #'.$tagData["color"].'"><i>#</i>'.ucfirst( $this->GD->text($tagData["id_text"]) ).'</a>';

						if ( $key > 1)
							$return2 .= '<a href="'.$link.'" class="diyDMB bL" style="background-color: #'.$tagData["color"].'">'.ucfirst( $this->GD->text($tagData["id_text"]) ).'</a>';
					}
				}
			}
			

			if ( $count <= 2 )
				return $return;
			else {
				return '
					'.$return1.'
					<button type="button" class="diyDMB moreTags i i07" title="'.$this->GD->text(37).'"></button>
					<div class="tagContainer dH">
						'.$return2.'

						<div class="tCp"></div>
					</div>
				';
			}
		}



		public function diy_date($data) {
			$date = strtotime($data);
			
			$den = date("d", $date);
			
			$mesiac = date("n", $date);
			$mesiac = $this->mesiac($den, $mesiac);

			switch ( $this->CHR->LANG ) {
				case 'en':
					$return = ucfirst($mesiac).date(" d,", $date).date(" Y")." ".$this->GD->text(24)." ".date("H:i", $date);
					break;
				default:
					$return = date("d. ", $date).$mesiac.date(" Y")." ".$this->GD->text(24)." ".date("H:i", $date);
					break;
			}
			
			return $return;
		}


		public function color_text($text) {
			
			foreach($this->keywords as $word) {

				$text = preg_replace("|($word)|Ui",
				'<strong class="sg01">'."$1".'</strong>', $text );
			}

			return $text;
		}


		public function count_diy_by_category($category) {
			$queries = array( 'default' => $default = '+"'.$this->data.'"', 'data' => $this->keys_query(), 'tags' => $this->tags_query(), 'filter' => $this->create_filter() );

			$stmt = $this->mysql->query("SELECT COUNT(id) as total FROM navody WHERE ( MATCH(".$this->searchin.") AGAINST('".$queries["data"]."' IN BOOLEAN MODE) OR MATCH(".$this->searchin.") AGAINST('".$queries["data"]."' IN BOOLEAN MODE) ) ".$queries["tags"]." AND category = '$category' AND public = '1' ".$queries["filter"] );

			$count = $stmt->fetch_assoc();

			return $count["total"];
		}

		public function count_diy_all() {
			$queries = array( 'default' => $default = '+"'.$this->data.'"', 'data' => $this->keys_query(), 'tags' => $this->tags_query(), 'filter' => $this->create_filter() );

			$stmt = $this->mysql->query("SELECT COUNT(id) as total FROM navody WHERE ( MATCH(".$this->searchin.") AGAINST('".$queries["data"]."' IN BOOLEAN MODE) OR MATCH(".$this->searchin.") AGAINST('".$queries["data"]."' IN BOOLEAN MODE) ) ".$queries["tags"]." AND public = '1' ".$queries["filter"] );
			
			$count = $stmt->fetch_assoc();

			return $count["total"];
		}



		/*
		public function check_tags($keys) {
			global $mysql;
			global $PD;

			$find = explode(" ", $keys);

			foreach ($find as $value) {
				$q = $mysql->query("SELECT id FROM tagy WHERE ".$PD["lang"]." = '$value'");
				$r = $mysql->result($q);

				if ( $r ) $result .= '%'.$r["id"].'%';
			}

			return substr($result, 0, -1);
		}
		*/
	}

