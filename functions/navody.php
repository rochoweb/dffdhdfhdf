<?php
	$DIY = new DoItYourself();

	class DoItYourself {

		protected static $instance;
		protected $mysql;
		public $CHR;
		public $GD;
		//protected $instructions;
		//public $online = parent::online;
		public $IP;
		public $SHOP;

		public function __construct() {
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			//$this->instructions = DoItYourself::init();

			//$this->U = $this->GD->USERDATA_info();
			if ( $this->CHR->PD->page == 16 ) {
				$this->IP = $this->GD->ITEMPROFILE_O( "url", $this->mysql->safe($_GET["p"]) );
				//$this->SHOP = $this->GD->SHOPDATA_O( $this->IP->shop_id );
			}
			
		}


		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new DoItYourself();
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


		public function diy_index($count = 0) {
			$news = $return = "";
			$page = $this->GD->pager();

			$count_all = $this->GD->count_results("navody WHERE public = 1");

			$oddo = $this->GD->oddo( $page, $count_all );

			$this->mysql->query("SELECT * FROM navody WHERE public = 1 ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);

			foreach ($this->mysql->result2() as $key => $data) {
				$count += 1; 
				$id = $data["id"];

				$stats = $this->diy_stats( $id );
				$diff = $this->diy_difficulty( $data["difficulty"] );
				$tags = $this->diy_tags( $data["tags"] );
				$date = $this->GD->adddate( $data["create_date"] );

				
				$news .= $this->diy_body_tag( $data["window_type"], $data, $stats, $diff, $tags, $date, $count);
				$return = $news;
			}

			return array('diy' => $return, 'count' => $count_all, 'list' => $oddo["list"]);
		}

		public function diy_items($tags, $actual, $count = 0) {
			$news = $return = $c = "";
			//$page = $this->GD->pager();

			//$count_all = $this->GD->count_results("navody WHERE public = 1");

			//$oddo = $this->GD->oddo( $page, $count_all );
			if ( $tags ) {

				foreach ( array_filter( explode("#", $tags) ) as $key => $value) {
					
					$stmt = $this->mysql->query("SELECT * FROM navody WHERE tags LIKE '%#$value%' AND public = 1 AND system_id != '".$actual."' ORDER BY RAND() LIMIT ".ITEM_RELATED);

					$data = $stmt->fetch_assoc();

					$arrData[$data["id"]] = $data;
				}
			}
			
			foreach ($arrData as $key => $data) {
				$c += 1;

				if ( $c <= ITEM_RELATED - 1 ) {
					//$count += 1; 
					//$id = $data["id"];

					$stats = $this->diy_stats( $data["id"] );
					$diff = $this->diy_difficulty( $data["difficulty"] );
					$tags = $this->diy_tags( $data["tags"] );
					$date = $this->GD->adddate( $data["create_date"] );

					
					$news .= $this->diy_body_items( $data["window_type"], $data, $stats, $diff, $tags, $date, $count);
					$return = $news;
				}
				
			}

			return array( 'diy' => $return );
		}

		public function diy_tag($tagid, $count = 0) {
			$news = $return = "";
			$page = $this->GD->pager();

			$count_all = $this->GD->count_results("navody WHERE tags LIKE '%$tagid%' AND public = 1");

			$oddo = $this->GD->oddo( $page, $count_all );

			$this->mysql->query("SELECT * FROM navody WHERE tags LIKE '%$tagid%' AND public = 1 ORDER BY create_date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);

			foreach ($this->mysql->result2() as $key => $data) {
				$count += 1; 
				$id = $data["id"];

				$stats = $this->diy_stats( $id );
				$diff = $this->diy_difficulty( $data["difficulty"] );
				$tags = $this->diy_tags( $data["tags"] );
				$date = $this->GD->adddate( $data["create_date"] );

				
				$news .= $this->diy_body_tag( $data["window_type"], $data, $stats, $diff, $tags, $date, $count);
				$return = $news;
			}

			return array('diy' => $return, 'count' => $count_all, 'list' => $oddo["list"]);
		}












		public function diy_body($type, $data, $stats, $diff, $tags, $date) {
			
			$timeToLaik = new cas( date("d.m.Y H:i", $data["create_date"]) );

			$imgLink = $this->GD->generate_pictureUrl( $data["file_list"], true );
			$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
			//$link = $this->GD->url( $this->category( $data["category"] )."/".$data["url"] );
			
			//$author = $this->GD->USERDATA_info( $this->GD->USERDATA( $data["author"] ) );
			$author = $this->GD->USERDATA_O( $data["author"] );

			$link_author = $this->GD->url( $author["urlname"] );
			$link_category = $this->GD->url( "kategoria/".$this->GD->category_data( $data["category"], $this->CHR->LANG ));
			$link_url = $this->GD->url( $data["url"] );

			$author = $author->name;

			switch ($type) {
				case 1:	//velky
					$win = "w1";
					break;
				case 2:	//stredny
					$win = "w2";
					break;
			}

			if ( $data["intro_text"] )
				$body_text = '<p class="diyDesc-body">'.ucfirst($data["intro_text"]).'</p>';
				
			switch ($type) {
				case 1:	//velky
				case 2:	//stredny
					$return = '
						<div class="'.$win.' diy">
							<div class="diyContent">
								<a href="'.$link_url.'"> <img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($data["intro_head"]).'"> </a>
								<div class="diyLoad"><div></div></div>

								<div class="diyDescTop">
									<div class="diyStats">
										<div class="diyStat"> <div class="dI i i01"></div> <div class="dC">'.$stats["likes"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(60).'</span></div> </div>
										<div class="diyStat"> <div class="dI i i02"></div> <div class="dC">'.$stats["comments"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(61).'</span></div> </div>
										<div class="diyStat"> <div class="dI i i03"></div> <div class="dC">'.$stats["views"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(62).'</span></div> </div>
										<div class="cleaner"></div>
									</div>
<!--
									<div class="diyDesc-diff" title="Obtiažnosť návodu">
										<div class="diffs">
											<div class="stars">
												'.$diff["stars"].'
												<div class="cleaner"></div>
											</div>

											<div class="diyStat-explain"><div></div><span>'.ucfirst($diff["text"]).'</span></div>
										</div>
										
									</div>-->
								</div>

								<div class="diyPrice">
									'.$price.'
								</div>

								<div class="diyDesc">
									<div class="dD">
										<div class="diyDesc-content">
											<div class="diyDesc-time" title="'.$date.'"> <abbr class="upD" data-livestamp="'.$data["create_date"].'">'.$timeToLaik->result().'</abbr> <span class="i i06"></span> </div>

											<a href="'.$link_url.'" class="diyLink">
												<div class="diyDesc-title"><h3 class="diyDesc-head">'.ucfirst($data["intro_head"]).'</h3></div>
												'.$body_text.'
											</a>
											<div class="diyDesc-info">'.sprintf( $this->GD->text(156), '<a href="'.$link_author.'" class="tdu">'.$this->GD->SHOPDATA_O( $data["shop_id"], "name" ).'</a>', '<a href="'.$link_category.'" class="tdu">'.ucfirst($this->GD->text( $this->GD->category_data($data["category"], "category") )).'</a>' ).'</div>
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
					break;
/*
				case 3: #dvojite
					$return = '
						<div class="w3">
							<div class="diy w3half1">
								<div class="diyContent">
									<a href="#" class="diyLink">
										<img src="'.$this->GD->url_data($imgLink).'" class="diyImg">
										<div class="diyLoad"><div></div></div>

										<div class="diyDescTop">
											<div class="diyStats">
												<div class="diyStats">
													<div class="diyStat"> <div class="dI i i01"></div> <div class="dC">'.$stats["likes"].'</div> <div class="diyStat-explain"><div></div><span>Hodnotenia</span></div> </div>
													<div class="diyStat"> <div class="dI i i02"></div> <div class="dC">'.$stats["comments"].'</div> <div class="diyStat-explain"><div></div><span>Komentáre</span></div> </div>
													<div class="diyStat"> <div class="dI i i03"></div> <div class="dC">'.$stats["views"].'</div> <div class="diyStat-explain"><div></div><span>Zobrazenia</span></div> </div>
													<div class="cleaner"></div>
												</div>
											</div>

											<div class="diyDesc-diff" title="Obtiažnosť návodu">
												<div class="diffs">
													'.$diff["stars"].'
													<div class="cleaner"></div>

													<div class="diyStat-explain"><div></div><span>'.$diff["text"].'</span></div> 
												</div>
												
											</div>
										</div>

										<div class="diyDesc">
											<div class="dD">
												<div class="diyDesc-content">
													<div class="diyDesc-head"><h3>'.$data["intro_head"].' <div class="diyTime"> '.$timeToLaik.' <span class="i i06" title="'.$date.'"></span> </div> </h3></div>
													<div class="diyDesc-body"><p>'.$data["intro_text"].'</p></div>
												</div>

												<div class="diyDesc-footer">
													<div class="diyDesc-menu">
														'.$tags.'
													</div>
												</div>
											</div>
										</div>
									</a>
								</div>
							</div>
							<div class="diy w3half2">
								<div class="diyContent">
									<a href="#" class="diyLink">
										<img src="/imgs/5.jpg" class="diyImg">
										<div class="diyLoad"><div></div></div>

										<div class="diyDesc">
											<div class="dD">
												<div class="diyDesc-content">
													<div class="diyDesc-head">Head</div>
													<div class="diyDesc-body">Body</div>
												</div>
											</div>
										</div>
									</a>
								</div>
							</div>
							<div class="cleaner"></div>
						</div>
					';
					break;
*/
			}


			return $return;
		}

		public function diy_body_tag($type, $data, $stats, $diff, $tags, $date, $count) {
			$price_data = '';

			$timeToLaik = new cas( date("d.m.Y H:i", $data["create_date"]) );

			$imgLink = $this->GD->generate_pictureUrl( $data["file_list"], true );
			$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
			//$link = $this->GD->url( $this->category( $data["category"] )."/".$data["url"] );

			//$author = $this->GD->USERDATA_info( $this->GD->USERDATA( $data["user_id"] ) );
			//$U =  $this->GD->USERDATA_O( $data["user_id"] );
			$author = $this->GD->SHOPDATA_O( $data["shop_id"] );

			$link_author = $this->GD->url( $author->name );
			$link_category = $this->GD->url( "kategoria/".$this->GD->category_data( $data["category"], $this->CHR->LANG ));
			$link_url = $this->GD->url( $data["url"] );

			//$author = $author["nickname"] ? $author["nickname"] : $author["username"];
			$author = $author->name;
///
			/*$this->mysql->query("SELECT * FROM navody");

			foreach ($this->mysql->result2() as $key => $value) {
				$aa = rand( 0, 2000 );
				$cent = rand( 0, 99 );

				$this->mysql->query("UPDATE navody SET price = $aa WHERE id = ".$value["id"]);
				$this->mysql->query("UPDATE navody SET price_cent = $cent WHERE id = ".$value["id"]);
			}*/


			//$dsfsd = number_format( rand( 0, 2000*100 ) / 100, 2 );
			if ( $price = $this->GD->price( $data["price"] ) ) {
				if ( $price != 0 ) {
					$price_data = '
						<div class="diyDesc-price">
							'.$price.'
						</div>

					';
				}
			}
			

///
			$return = '
				<div class="w2 diy">
					<div class="diyContent">
						<a href="'.$link_url.'" class="diyAnch"> <img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($data["intro_head"]).'"> </a>
						<div class="diyLoad"><div></div></div>

						<div class="diyDescTop">
							<div class="diyStats">
								<div class="diyStat"> <div class="dI i i01"></div> <div class="dC">'.$stats["likes"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(60).'</span></div> </div>
								<div class="diyStat"> <div class="dI i i02"></div> <div class="dC">'.$stats["comments"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(61).'</span></div> </div>
								<div class="diyStat"> <div class="dI i i03"></div> <div class="dC">'.$stats["views"].'</div> <div class="diyStat-explain"><div></div><span>'.$this->GD->text(62).'</span></div> </div>
								<div class="cleaner"></div>
							</div>
<!--
							<div class="diyDesc-diff" title="Obtiažnosť návodu">
								<div class="diffs">
									<div class="stars">
										'.$diff["stars"].'
										<div class="cleaner"></div>
									</div>

									<div class="diyStat-explain"><div></div><span>'.ucfirst($diff["text"]).'</span></div> 
								</div>
								
							</div>-->
						</div>

						<div class="diyDesc">
							<div class="dD">
								<div class="diyDesc-content">
									'.$price_data.'

									<a href="'.$link_url.'" class="diyLink">
										<div class="diyDesc-head">
											<div class="diyDesc-title"><h3>'.ucfirst($data["title"]).'</h3></div>

											<div class="diyDesc-time" title="'.$date.'"> <abbr class="upD" data-livestamp="'.$data["create_date"].'">'.$timeToLaik->result().'</abbr> <span class="i i06"></span> </div> </div>
										<p class="diyDesc-body">'.ucfirst($data["description"]).'</p>
									</a>
									<div class="diyDesc-info">'.sprintf( $this->GD->text(156), '<a href="'.$link_author.'" class="tdu">'.$this->GD->SHOPDATA_O( $data["shop_id"], "name" ).'</a>', '<a href="'.$link_category.'" class="tdu">'.ucfirst($this->GD->text( $this->GD->category_data($data["category"], "category") )).'</a>' ).'</div>
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

			return $return;
		}


		public function diy_body_items($type, $data, $stats, $diff, $tags, $date, $count) {
			$price_data = '';

			$timeToLaik = new cas( date("d.m.Y H:i", $data["create_date"]) );

			$imgLink = $this->GD->generate_pictureUrl( $data["file_list"], true );
			$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
			//$link = $this->GD->url( $this->category( $data["category"] )."/".$data["url"] );

			//$author = $this->GD->USERDATA_info( $this->GD->USERDATA( $data["user_id"] ) );
			//$U =  $this->GD->USERDATA_O( $data["user_id"] );
			$author = $this->GD->SHOPDATA_O( $data["shop_id"] );

			$link_author = $this->GD->url( $author->name );
			$link_category = $this->GD->url( "kategoria/".$this->GD->category_data( $data["category"], $this->CHR->LANG ));
			$link_url = $this->GD->url( $data["url"] );

			//$author = $author["nickname"] ? $author["nickname"] : $author["username"];
			$author = $author->name;
///
			/*$this->mysql->query("SELECT * FROM navody");

			foreach ($this->mysql->result2() as $key => $value) {
				$aa = rand( 0, 2000 );
				$cent = rand( 0, 99 );

				$this->mysql->query("UPDATE navody SET price = $aa WHERE id = ".$value["id"]);
				$this->mysql->query("UPDATE navody SET price_cent = $cent WHERE id = ".$value["id"]);
			}*/


			//$dsfsd = number_format( rand( 0, 2000*100 ) / 100, 2 );
			if ( $price = $this->GD->price( $data["price"] ) ) {
				if ( $price != 0 ) {
					$price_data = '
						<div class="diyDesc-price">
							'.$price.'
						</div>

					';
				}
			}
			

///
			$return = '
				<div class="w2 diy">
					<div class="diyContent">
						<a href="'.$link_url.'" class="diyAnch"> <img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($data["intro_head"]).'"> </a>
						<div class="diyLoad"><div></div></div>

						<div class="diyDesc">
							<div class="dD">
								<div class="diyDesc-content">
									'.$price_data.'

									<a href="'.$link_url.'" class="diyLink">
										<div class="diyDesc-head">
											<div class="diyDesc-title"><h3>'.ucfirst($data["title"]).'</h3></div>

											<div class="diyDesc-time" title="'.$date.'"> <abbr class="upD" data-livestamp="'.$data["create_date"].'">'.$timeToLaik->result().'</abbr> <span class="i i06"></span> </div> </div>
					
									</a>

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

			return $return;
		}



		public function diy_stats($id) {
			
			$tables = array('hodnotenia' => "id", 'komentare' => "ic", 'prezretia' => "id");

			foreach ($tables as $key => $value) {
				$stmt = $this->mysql->query("SELECT count(*) as total FROM ".$key." WHERE ".$value." = ".$id);
				$r = $stmt->fetch_assoc();

				$result[$key] = $r["total"];
			}
			
			return array(
				'likes' => $result["hodnotenia"],
				'comments' => $result["komentare"],
				'views' => $result["prezretia"]);
		}


		public function diy_difficulty($type, $r = "", $maxcount = 3) {

			for ($i=0; $i < $maxcount; $i++) { 
				if ( $i < $type )
					$r .= '<div class="dD-icon i i04"></div> ';
				else
					$r .= '<div class="dD-icon i i05"></div> ';
			}

			return array(
				'stars' => $r,
				'text' => $this->GD->text($type));
		}

		public function diy_tags($data) {
			$return = $return1 = $return2 = "";

			$PD = $this->CHR->PD;
			
			$tags = explode("#", $data);

			$count = count( array_filter($tags) );

			foreach ( array_filter($tags) as $key => $value) {
				//if ( strlen($value) > 0 ) {
					$stmt 		= 	$this->mysql->query("SELECT * FROM tagy WHERE id = '$value'");
					$tagData 	= 	$stmt->fetch_assoc();
					$link 		= 	$this->GD->link(8)."/".$tagData[$this->CHR->LANG];


					if ( !$tagData["color"] ) {
						$random = $this->GD->random_color();

						$tagData["color"] = $random;
					}
					
					if ( $count <= 2 )
						$return .= '<a href="'.$link.'" class="diyDMB bL" style="background-color: #'.$tagData["color"].'"><i>#</i>'.ucfirst( $this->GD->text($tagData["id_text"]) ).'</a>';
					else {
						if ( $key <= 2 )
							$return1 .= '<a href="'.$link.'" class="diyDMB bL" style="background-color: #'.$tagData["color"].'"><i>#</i>'.ucfirst( $this->GD->text($tagData["id_text"]) ).'</a>';

						if ( $key > 2)
							$return2 .= '<a href="'.$link.'" class="diyDMB bL" style="background-color: #'.$tagData["color"].'">'.ucfirst( $this->GD->text($tagData["id_text"]) ).'</a>';
					}
				//}
			}


			if ( $count <= 2 ) {
				return $return;
			}
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

		/*public function diy_date($data) {
			$date = $data;

			$den = date("d", $date);
			$mesiac = date("n", $date);

			$mesiac = $this->GD->mesiac($den, $mesiac);

			switch ( $this->CHR->LANG ) {
				case 'en':
					$return = ucfirst($mesiac).date(" d,", $date).date(" Y", $date)." ".$this->GD->text(24)." ".date("H:i", $date);
					break;
				default:
					$return = date("d. ", $date).$mesiac.date(" Y", $date)." ".$this->GD->text(24)." ".date("H:i", $date);
					break;
			}
			
			return $return;
		}*/
	}

