<?php
	$IP = new ITEMPROFIL();



	class ITEMPROFIL {
		
		protected $mysql;
		protected $GD;
		
		//public $shop;
		//private $shopID;
		public $IP;
		public $SHOP;
		private $price;

		public function __construct() {
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GI = GEN_ITEMS::init();
			$this->BA = BASKET::init();
			/*$this->shop = $this->shopdata();
			$this->shopID = $this->shopdata( "shopID" );*/
			$this->IP = $this->GD->ITEMPROFILE_O( "url", $this->mysql->safe($_GET["p"]) );
			//$this->SHOP = $this->GD->SHOPDATA_O( $this->IP->shop_id );

			if ( !$this->GD->check_all_before_publish($this->IP->id) )
				header( "Location: ".$this->GD->link(1) );


			if ( $this->IP->discount ) {
				$this->price = $this->GD->discount( $this->IP->price, $this->IP->discount );
			} else 
				$this->price = $this->IP->price;

			$this->itemlink = $this->GD->url( $this->IP->url );
		}
		
		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new ITEMPROFIL();
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




		public function generate_pictures($r = "", $arr = false) {
			//$arr = array();

			if ( $FL = $this->IP->file_list ) {

				$IDs = explode("#", $FL);

				foreach (array_filter($IDs) as $key => $value) {
					
					$stmt = $this->mysql->query("SELECT * FROM files WHERE id = ".$value);

					if ( $data = $stmt->fetch_object() ) $arr[$key] = $data;
				}

			}

			return $arr;
		}


		public function generate_galery($r = "", $re = "") {
			$count = 0;
			$uu = 1;

			$em = "";

			if ( $data = $this->generate_pictures() ) {
				foreach ($data as $key => $value) {
					$count += 1;

					$file = $value->folder.'/'.$value->filename.".".$value->file_type;
					$url = $this->GD->suburl_data(DEF_UPLOAD_FOLDER.$file);

					$imgSize = $this->GD->picture_dimension( $url, PHYS_ADRESS_TO_FILES.$file );

					

					//$a = '<div class="ipImg"'.$first.'><a href="#"><img src="'.$url.'" class="img'.$imgSize.'" alt=""></a></div>';

					if ( $key == 1 ) {
						$this->firstimage = $url;

						$r .= '
						<div class="ip-introImg">
							'.$this->imgLink($url, $imgSize, "", "zoompls ", $count).'
							<div class="introImgSlider" id="sliR">
								<div class="is-left ImgSlider">
									<div><span class="ii iLeft" aria-hidden="true"></span></div>
								</div>
								<div class="is-mid ImgShow jdi2" data-target="#imgzoom" data-info=".ipImg">
									<div><span class="ii iZoom2" aria-hidden="true"></span></div>
								</div>
								<div class="is-right ImgSlider">
									<div><span class="ii iRight" aria-hidden="true"></span></div>
								</div>
								<div class="cleaner"></div>
							</div>
						</div>';
					}

					$re .= $this->imgLink($url, $imgSize, ($key == 1) ? ' id="imgA"' : "", "zoompls ", $count, true);
				}

				// empty images
				for ($i=0; $i < MAX_FILES - $count; $i++) { 
					$uu = $count + $i + 1;
					$em .= '<div class="ipImgEmpty imm'.$uu.'"><div></div></div>';
				}

				$picList = '<div class="ip-imgList Li'.$count.'">'.$re.$em.' <div class="cleaner"></div></div>';
			}
			else {
				$r = "";
			}

			return $r.$picList;
		}

		public function generate_addDate($r = '') {

			$timeToLaik = new cas( date("d.m.Y H:i", $this->IP->create_date) );

			$r = '
				<div class="ipr-createDate"><p><span>'.sprintf($this->GD->text(501), $timeToLaik->result()).'</span><strong>'.sprintf($this->GD->text(501), $this->GD->adddate($this->IP->create_date) ).'</strong></p></div>
			';
			
			return $r;
		}

		public function imgLink ($url, $imgSize, $class = '', $classImg = '', $number = "", $prop = false) {
			$num = ' imm'.$number;
			$pr = $prop ? ' itemprop="image"' : '';

			return '<div class="ipImg'.$num.'"'.$class.'><a href="#"><img src="'.$url.'" class="'.$classImg.'img'.$imgSize.'" alt=""  id="img_'.$number.'"'.$pr.'></a></div>';
		}


		public function generate_promo($r = '') {
			
			if ( $this->IP->discount ) {
				$r = '
				<div class="ips-itemPromo">
					<div class="ips-proicons">
						<div class="proIcon proDiscount"><span>'.$this->GD->text(506).'</span><strong>- '.$this->IP->discount.'%</strong></div>
					</div>
				</div>
				';
			}
			
			return $r;
		}

		public function generate_breadcrumb($r = '') {
			
			$L = $this->CHR->LANG;

			if ( $this->IP->category ) {

				foreach ($this->item_category() as $key => $value) {
					$r .= '<li><a href="'.$this->GD->link_to($value->$L).'">'.$this->GD->text( $value->text )."</a></li>";
				}
			}

			return '
				<ul class="breadcrumb">
					<li><i class="ii iHome1" aria-hidden="true"></i><a href="'.$this->GD->link(1).'">'.$this->GD->text(627).'</a></li>
					'.$r.'
				</ul>
				<div class="cleaner"></div>
			';
		}


		public function item_category($r = '') {
			
			$tables = array(0 => "kategorie", 1 => "kategorie_subs", 2 => "kategorie_subs2");


			if ( $this->IP->category ) {
				$data = explode("#", $this->IP->category );

				if ( is_array($data) ) {

					foreach ( array_filter($data) as $key => $value) {
						
						if ( $cat = $this->GD->category_data_from_table($tables[$key], $value) )
							$r[] = $cat;
					}

				}

			}

			return $r;
		}


		public function generate_price($r = '') {

			$nu = number_format($this->price, 2, '.', '');

			if ( $this->IP->discount ) {
				$r .= '
					<div class="ipr-currPrice fl w50">
						<span class="price" itemprop="price" content="'.$nu.'">'.$this->GD->price( $this->price ).'</span>
						<span class="price-vat"><span>'.$this->GD->text(290).'</span></span>
						<div class="cleaner"></div>
					</div>

					<div class="ipr-oldPrice fl w50">
						<span class="price"><del>'.$this->GD->price($this->IP->price).'</del></span>
						<span class="price-vat">'.$this->GD->text(291).'</span>
						<div class="cleaner"></div>
					</div>
				';
			} else {
				$r .= '
					<div class="ipr-currPrice">
						<span class="price" itemprop="price" content="'.$nu.'">'.$this->GD->price($this->IP->price).'</span>
						<span class="price-vat"><span>'.$this->GD->text(290).'</span></span>
						<div class="cleaner"></div>
					</div>
				';
			}

			return $r;
		}

		public function generate_availability($r = '') {
			$data = $this->availability_data( $this->IP->availability );

			switch ( $this->IP->availability ) {
				case 1:
					//$class = " availOK";
					$r = '<span><link itemprop="availability" href="http://schema.org/InStock" />'.$this->GD->text($data->text).'</span>'.$this->IP->quantity." KS";
					break;
				case 8:
					//$class = " availNOT";
					$r = '<span>! '.$this->GD->text($data->text).' !</span>';
					break;
				default:
					//$class = " availWORK";
					$r = '<span>'.sprintf($this->GD->text($data->text), $this->IP->quantity).'</span>';
					
					break;
			}

			if ( $r ) {
				return '
				<div class="ipr-availability">
					'.$r.'
				</div>
				';
			} else return "";
		}


		public function generate_delivery_date($r = '') {



			if ( $this->IP->availability != 8 ) {

				$orderuntil = ( time() <= strtotime( ORDER_UNTILTO ) && date("N") != 6 && date("N") != 7 ) ? '<span>'.sprintf($this->GD->text(304).'</span>', ORDER_UNTILTO) : "";
				
				$date = $this->GD->delivery_date();
				//date("d.m.Y", $date)
				return '
					<div class="ipr-deliveryDate">
						
						<p class="iprd-title">'.$this->GD->text(303).''.$orderuntil.'</p>
						<p class="iprd-date"><strong>'.date("d.m.Y", $date).'</strong><span>( '.$this->GD->day(date("N", $date)).' )</span></p>
						<div class="cleaner"></div>
					</div>
				';

			}
		}

		public function generate_info($r = '') {
			
			return '
			<div class="ipri">
				<div class="li">
					<span class="ipri-title"><span class="ii iDelivery" aria-hidden="true"></span>'.$this->GD->text(297).'</span>
					<span class="ipri-info">'.sprintf($this->GD->text(300), "Geis").'</span>
					<span class="ipri-value"><strong>+ '.$this->GD->price("3.50").'</strong></span>
					<div class="cleaner"></div>
				</div>
				<div class="li">
					<span class="ipri-title"><span class="ii iPay" aria-hidden="true"></span>'.$this->GD->text(298).'</span>
					<span class="ipri-info">'.$this->GD->text(301).'</span>
					<div class="cleaner"></div>
				</div>
				<div class="cleaner"></div>
			</div>

			';
		}


		public function generate_sizes($r = '') {
			
			$th = $td = $tables = "";
			$other = $this->IP->size_other ? '<p>'.$this->IP->size_other.'</p>' : "";

			$table = array(0 => "ipSizes ipS1", 1 => "ipSizes ipS2");
			
			$tableData = array( array(
				0 => array("text" => 233, "data" => $this->IP->size_lenght, "unit" => "cm"), 
							array("text" => 234, "data" => $this->IP->size_width, "unit" => "cm"),
							array("text" => 232, "data" => $this->IP->size_height, "unit" => "cm")),
				1 => array(
							array("text" => 500, "data" => $this->IP->size_average, "unit" => "cm"),
							array("text" => 503, "data" => $this->IP->size_mass, "unit" => "mg")));
/*
			$tableData2 = array(
				array("text" => 233, "data" => $this->IP->size_lenght, "unit" => "mm"), 
				array("text" => 234, "data" => $this->IP->size_width, "unit" => "mm"),
				array("text" => 232, "data" => $this->IP->size_height, "unit" => "mm"),
				array("text" => 500, "data" => $this->IP->size_average, "unit" => "mm"),
				array("text" => 503, "data" => $this->IP->size_mass, "unit" => "kg"));
*/

			foreach ($table as $key => $val) {
				$th = $td = "";

				foreach ($tableData[$key] as $key => $value) {
					if ( isset($value["data"]) && strlen($value["data"]) > 0 ) {
						$th .= '<th>'.$this->GD->text( $value["text"] ).'<div>'.$value["unit"].'</div></th>';
						$td .= '<td>'.$value["data"].'</td>';
					}
				}

				$tables .= '
				<table>
					<tr>
						'.$th.'
					</tr>

					<tr>'.$td.'</tr>
				</table>
				';
			}

			if ( $th ) {
				return '
				<div class="ipSizes">
					'.$tables.'
					<div class="cleaner"></div>
					'.$other.'
				</div>';
			}
			
		}

		//' target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.$this->itemlink.';src=sdkpreparse"'
		public function generate_shareButtons($r = '') {
			
			$sn = array(
					array(
						"class" 	=> "fcb", 
						"title" 	=> "Facebook", 
						"icon" 		=> '<i class="fa fa-facebook" aria-hidden="true"></i>',
						'custom'	=> ' href="https://www.facebook.com/sharer/sharer.php?u='.$this->itemlink.';src=sdkpreparse" onclick="javascript:window.open(this.href,'."''".','."'".'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'."'".');return false;'.'"'),
					array(
						"class" 	=> "pin", 
						"title" 	=> "Pinterest", 
						"icon" 		=> '<i class="fa fa-pinterest-p" aria-hidden="true"></i>', 
						'custom'	=> ' data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url='.$this->itemlink.'/&media='.$this->firstimage.'&description='.$this->GD->mb_ucfirst($this->IP->title).'" data-pin-custom="true"'),
					array(
						"class" 	=> "gp", 
						"title" 	=> "Google+", 
						"icon" 		=> '<i class="fa fa-google-plus" aria-hidden="true"></i>',
						'custom'	=> ' href="https://plus.google.com/share?url='.$this->itemlink.'" onclick="javascript:window.open(this.href,'."''".','."'".'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'."'".');return false;'.'"')

				);
			/*
			'custom'	=> ' data-pin-do="buttonPin" href="https://www.pinterest.com/pin/create/button/?url=http://www.foodiecrush.com/2014/03/filet-mignon-with-porcini-mushroom-compound-butter/&media=https://s-media-cache-ak0.pinimg.com/736x/17/34/8e/17348e163a3212c06e61c41c4b22b87a.jpg&description=So%20delicious!" data-pin-custom="true"')
			*/
			foreach ($sn as $key => $value) {
				$custom = isset($value['custom']) ? $value['custom'] : '';

				$r .= '<a class="share-'.$value["class"].'" title="'.sprintf($this->GD->text(502), $value["title"]).'"'.$custom.'>'.$value["icon"].'</a>';
			}

			return '<div class="ipr-share">'.$r.'<div class="cleaner"></div></div>';
		}

		public function generate_tags($r = '') {
			$warns = '';

			$l = $this->CHR->LANG;

			if ( $this->IP->tags ) {

				foreach ( array_filter( explode("#", $this->IP->tags) ) as $key => $value) {
					
					$stmt = $this->mysql->query("SELECT * FROM tagy WHERE id = ".$value);
					$tag = $stmt->fetch_object();

					$color 	= $tag->color ? ' style="background:#'.$tag->color.'"' : "";
					$link 	= $this->GD->link(8)."/".$tag->$l;

					$r .= '<a href="'.$link.'"'.$color.'><span class="ii iTags"></span>'.$this->GD->mb_ucfirst( $this->GD->text($tag->id_text) ).'</a>';

					if ( $tag->w_text ) {

						if ( $tag->w_icon )
							$warns[] = array('text' => $tag->w_text, 'icon' => $tag->w_icon);
						else
							$warns[] = array('text' => $tag->w_text);


						$this->warnings = $warns;
					}
				}
			}
			
			if ( $r )
				return '
					<div class="ipr-hashtags">'.$r.'<div class="cleaner"></div></div>
				';

		}

		public function generate_descriptions($prop = false, $r = '') {
			$lis = $tabs = $addDate = "";

			$data = array(
				array("data" => nl2br( $this->IP->description ), "head" => 312, "icon" => '<i class="fa fa-circle-thin" aria-hidden="true"></i>'),
				array("data" => $this->generate_sizes(), "head" => 313, "icon" => '<i class="fa fa-circle-thin" aria-hidden="true"></i>'),
				);

			foreach ($data as $key => $value) {
				
				$addDate = $key == 0 ? $this->generate_addDate() : "";

				if ( $value["data"] ) {

					$actLi = $key == 0 ? ' id="ipTabA"' : "";
					$actTab = $key == 0 ? ' id="ipA"' : "";

					$lis .= '<li><a href="#"'.$actLi.' data-target="ipTab'.$key.'">'.$this->GD->text($value["head"]).'</a></li>';

					$pr = $prop ? ' itemprop="description"' : '';

					$tabs .= '
					<div class="ipTab ipTab'.$key.'"'.$actTab.'>
						<div class="ipTab-content">
							<p'.$pr.'>'.$this->GD->EDITOR($value["data"]).'</p>
						</div>

						'.$addDate.'
					</div>
					';
				}
			}



			if ( $lis ) {

				$r = '
				<ul class="ipTabMenu">
					'.$lis.'
				</ul>

				<div class="ipTabs">
					'.$tabs.'
				</div>

				'.$this->generate_warnings().'
				';
			}


			return $r;
		}




		public function gen_itemrelated($r = '') {
			
			//if ( $this->IP->tags ) {
			if ( isset($_COOKIE["mm-history"]) ) {
				

				$R = $this->GI->generate_items("itemrelated", array("tags" => $this->IP->tags, "id" => $this->IP->id), $this->BA);  

					$c = count( array_filter( explode(".", $_COOKIE["mm-history"]) ) );

					if ( $c >= ITEM_RELATED_SHOW_WHEN ) {
						$slider = '<a href="#" class="sLR posLeft"><div class="ii iLeft" aria-hidden="true"></div></a>
								<a href="#" class="sLR posRight"><div class="ii iRight" aria-hidden="true"></div></a>';

						return '
							<div class="itemResults itemRelated">
								<div class="itemR-head">
									'.mb_strtoupper($this->GD->text(505), "UTF8").'
								</div>
								
								<div class="itemR-content slider" id="itemrelated">
									'.$slider.'

									<div class="itemR-body">
										<div class="sliderItems">
											'.$R["diy"].'
										</div>
									</div>

									
								</div>
								
								<div class="cleaner"></div>
							</div>
						';
					}
					
				

			}
		}




		public function generate_warnings($r = '') {

			if ( isset($this->warnings) ) {
				$c = count($this->warnings);

				foreach ($this->warnings as $key => $v) {
					$icon = isset($v['icon']) ? $v['icon'] : 'iWarn';

					$r .= '
					<div class="iwarn">
						<span class="ii '.$icon.'"></span>
						<div class="iwarn-content"><div></div><p>'.$this->GD->text($v['text']).'</p></div>
						<div class="cleaner"></div>
					</div>
					';
				}

				$head = $c == 1 ? 890 : 891;

				return '
				<div class="ipr-warnings">
					'.$r.'

				</div>
				';
			}
		}



		public function availability_data($id, $r = '') {
			$stmt = $this->mysql->query("SELECT * FROM availability WHERE id =".$id);
			return $stmt->fetch_object();
		}

	}

