<?php
	$INV = new INVOICES();

	class INVOICES {
		
		protected $mysql;
		protected $GD;
		
		private $user;
		private $userID;

		public $U;
		//public $shopID;

		public function __construct($ajax_page = "") {
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();

			//$this->page = $this->CHR->PD;

			$this->ajax_page = $ajax_page;
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new INVOICES();
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

		public function action($type, $data = "", $return = "") {




			if ( $this->GD->is_logged() == true ) {
				if ( $this->GD->is_inactive() == false ) {

					switch ($type) {

						
						case 'details':
							$return = array ( 'r' => $this->invoiceDetails($data) );
							break;


						case 'prijata':
						case 'potvrdena':
						case 'vybavujesa':
						case 'odoslana':
						case 'dorucena':
						case 'storno':

							$d = explode("#", $data);

							if ( $this->mysql->q("INSERT INTO orders_timeline (id, system_id, date, type, user) VALUES (NULL, '".$d[1]."', '".time()."', '".$d[0]."', '".$this->GD->U->userID."') ") ) {

								$this->mysql->q("UPDATE orders SET status = '".$d[0]."' WHERE system_id = '".$d[1]."'");

								$return = array ( 'r' => $this->invoiceDetails($d[1]) );

								if ( $this->ajax_page != 50 )
									$return['e'] = $this->gen_invoice_html();

								if ( $this->ajax_page == 50 )
									$return['s'] = $this->GD->show_status($this->ajax_page, $type);
							}

							
							//$return = array ( 'r' => $this->ajax_page );
							break;
					}

				} 
				else {
					$return = array("offline" 	=> $this->logout_message(1) );
					$this->GD->logout();
				}
			} else {
				$return = array("offline" 	=> $this->logout_message(1) );
				$this->GD->logout();
			}

			
			return $return;
		}


		






		public function generate_invoiceitems($concept = false, $count = 0, $MS = MAX_RESULTS_OBJEDNAVKY) {
			$r = $items = $emptyItems = $re = '';
			$c = 1;

			if ( !isset($_GET["subp"]) || !$this->CHR->CHECK_SUBPAGE("page") ) {
				$_GET["p"] = "objeddnavky";
				$_GET["subp"] = "nevybavene";
			}

			$filters = array( 	
				1 	=> array("page" => 40, "text" => 318, "head" => 317, "empty" => 330, "q" => "orders WHERE status != '10' AND status != '20'"),
				2	=> array("page" => 41, "text" => 319, "head" => 271, "empty" => 330, "q" => "orders WHERE status = '1'"),
				/*3 	=> array("page" => 42, "text" => 320, "head" => 317, "empty" => 331, "q" => "orders WHERE status = '2'"),
				4 	=> array("page" => 43, "text" => 321, "head" => 317, "empty" => 331, "q" => "orders WHERE status = '5'"),*/
				5 	=> array("page" => 44, "text" => 322, "head" => 317, "empty" => 330, "q" => "orders WHERE status = '10'"),
				6 	=> array("page" => 45, "text" => 323, "head" => 317, "empty" => 330, "q" => "orders WHERE status = '20'"),
				7 	=> array("page" => 46, "text" => 324, "head" => 317, "empty" => 332, "q" => "orders")
				);

			$pageinfo = $this->GD->generate_page_with_filters($filters, $this->ajax_page);

			if ( !$pageinfo )
				$_GET["subp"] = "nevybavene";

			$page = $this->GD->pager();
			$oddo = $this->GD->oddo( $page, $pageinfo["activeCount"], "", $MS );
			

			$data = $this->mysql->query("SELECT * FROM ".$pageinfo["activeQuery"]." ORDER BY date DESC LIMIT ".$oddo["od"].", ".$oddo["do"]);
			

			if ( $data = $this->mysql->resultO() ) {

				foreach ($data as $key => $v) {
					
					$deliveryAdress = array($v->delivery_firstname, $v->delivery_lastname, str_replace(",", "", $v->delivery_street), $v->delivery_zip, $v->delivery_city);
					$billingAdresss = array($v->billing_firstname, $v->billing_lastname, str_replace(",", "", $v->billing_street), $v->billing_zip, $v->billing_city);

					$timeToLaik = new cas( date('d.m.Y H:i:s', $v->date) );

					//$imgLink = $this->GD->generate_pictureUrl( $value["file_list"], true );
					//$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
					//$adress = $this->client_adress($billingAdresss, $deliveryAdress);
					$status = $this->invoice_status($v->status);

					$re .= '
					<tr>
						<td class="invID">'.$c++.'</td>
						<td class="invAction"><a href="#" class="jdi"data-event="details" data-data="'.$v->system_id.'" title="'.$this->GD->text(333).'"><span class="ii iZoom ti" data-info="'.$this->GD->text(333).'"></span></a></td>
						<td class="invLink"><a href="'.$this->GD->url( 'objednavka/'.$v->system_id ).'">'.$v->system_id.'</a></td>
						<td class="invDate">'.date('d.n.Y H:i', $v->date).'<span data-livestamp="'.$v->date.'">'.$timeToLaik->result().'</span></td>
						<td class="invAdress">'.$this->client_adress($billingAdresss, $deliveryAdress).'</td>
						<td class="invPrice">'.$this->GD->price($v->price_all).'</td>
						<td class="invStatus"><span style="background:#'.$status->background.';color:#'.$status->color.';">'.$this->GD->text_($status->text).'</span></td>
					</tr>
					';

				}

				$r = '
				<table class="invoices">
					<tr>
						<th class="invID">#</th>
						<th>&nbsp;</th>
						<th>'.$this->GD->text(325).'</th>
						<th>'.$this->GD->text(326).'</th>
						<th>'.$this->GD->text(327).'</th>
						<th>'.$this->GD->text(328).'</th>
						<th class="tac">'.$this->GD->text(329).'</th>
					</tr>

					'.$re.'
				</table>
				';
			} else {
				$r = '<div class="invEmpty">'.$pageinfo["emptyText"].'</div>';
			}

			//$r .= $this->generate_empty_fields($count, $empty);

			return array("count" => $count, "menu" => $pageinfo["menu"], "head" => $pageinfo["head"], "list" => '
				<div class="invoiceList">
						<div class="invoiceList-content">
							'.$r.'
						</div>

						'.$oddo["list"].'
					</div>
					');

		}

		public function generate_empty_fields($count, $empty,  $r = "") {
			//if ( $count ) {
				$e = $empty - $count;

				for ($i=0; $i < $e; $i++) { 

					$r .= '<div class="aic aiEmpty">
										<div class="aic-c">
											<div class="aic-h"></div>
											<div class="aic-b"></div>
										</div>
									</div>';
				}
			//}

			return $r;
		}

		public function generate_data($from, $where, $data) {
			$stmt = $this->mysql->query("SELECT * FROM $from WHERE $where = ".$data);
			
			return $stmt;
		}

		public function invoice_status($status, $r = "") {
			$stmt = $this->mysql->q("SELECT * FROM orders_progress WHERE id = '$status'");

			return $stmt->fetch_object();
		}


		public function client_adress($billingAdresss, $deliveryAdress, $r = "") {
			$r1 = $r2 = "";
			$c = 0;

			$patern = "<strong>%s %s</strong>, %s, %s %s";
			// check if delivery adress setted up
			foreach ($deliveryAdress as $key => $value) {

				//if ( $value == $data->$value ) {
					if ( strlen($value) > 0 )
						$c++;
				//}
				
			}

			if ( count($deliveryAdress) == $c )
				$adress = "delivery";
			else
				$adress = "billing";
			//


			if ( $adress == "delivery" ) {
				/*foreach ($deliveryAdress as $key2 => $v2) {
					$r1 .= vprintf() $v2." ";
				}*/
				$r1 = vsprintf($patern, $deliveryAdress);

				return $r1;
			} 

			if ( $adress == "billing" ) {
				/*foreach ($billingAdresss as $key3 => $v3) {
					$r2 .= $v3." ";
				}*/

				$r2 = vsprintf($patern, $billingAdresss);

				return $r2;
			}
		}






		public function invoiceDetails($order, $ajax = true, $r = '') {
			
			$stmt = $this->mysql->q("SELECT * FROM orders WHERE system_id = '$order'");

			if ( $o = $stmt->fetch_object() ) {

				$a = $this->generate_adress($o);

				return '
					<div class="invoiceDetails">
						<div class="inv-left">
							<div class="inv-co">
								<div class="invleft invOrder">
									<div class="invH">'.sprintf($this->GD->text(334), '<span>'.$o->system_id.'</span>').'</div>
									<div class="invB invAdresses">
										<div class="inva-left">
											<div class="invaleft">
												<div class="invaHeader"><span>'.mb_strtoupper($this->GD->text(327), "UTF8").'</span></div>
												<div class="invaBody">
													'.$a["delivery"].'
												</div>
											</div>
										</div>

										<div class="inva-right">
											<div class="invaright">
												<div class="invaHeader"><span>'.mb_strtoupper($this->GD->text(335), "UTF8").'</span></div>
												<div class="invaBody">
													'.$a["billing"].'
												</div>
											</div>
											
										</div>

										<div class="cleaner"></div>
									</div>
								</div>

								<div class="invleft invDetails">
									<div class="invHE">'.$this->GD->text(336).'</div>
									<div class="invB">
										'.$this->gen_itemlist($o, $ajax).'
									</div>
								</div>
							</div>
						</div>

						<div class="inv-right">
							<div class="inv-co">
								<div class="invright invTimeline">
									<div class="invH">'.$this->GD->text(338).'<div class="timeline-loading"><i class="fa fa-cog fa-spin"></i></div></div>

									<div class="invB">
										'.$this->gen_timeline($o, $ajax).'
									</div>

								</div>
							</div>
						</div>

						<div class="cleaner"></div>
					</div>
				';
			}

			//sprintf($this->GD->text(334), '<span>č. '.$o->system_id.'</span>')
		}


		public function generate_adress($v, $r = "") {
			$c = 0;

			$patern = '
			<div class="invName"><strong>%s %s</strong></div>
			<div class="">%s</div> 
			<div class="">%s %s</div>
			<div class="invPhone">%s</div>';

			$deliveryAdress = array($v->delivery_firstname, $v->delivery_lastname, str_replace(",", "", $v->delivery_street), $v->delivery_zip, $v->delivery_city, $v->delivery_phone);
			$billingAdresss = array($v->billing_firstname, $v->billing_lastname, str_replace(",", "", $v->billing_street), $v->billing_zip, $v->billing_city, $v->billing_phone);

			foreach ($deliveryAdress as $key => $value) {
				if ( strlen($value) > 0 ) $c++;
			}

			$billing = vsprintf($patern, $billingAdresss);

			$delivery = count($deliveryAdress) == $c ? vsprintf($patern, $deliveryAdress) : $billing;
			
			return array("delivery" => $delivery, "billing" => $billing);
		}


		public function gen_timeline($d, $ajax = true, $r = "") {
			$c = 0;

			$this->mysql->q("SELECT * FROM orders_progress");
			$data = $this->mysql->resultO();

			foreach ($data as $key => $v) {
					$stmt = $this->mysql->q("SELECT date, user, type FROM orders_timeline WHERE system_id = '$d->system_id' AND type = '$v->id'");
					$y = $stmt->fetch_object();

					$h = $this->GD->text_($v->text);
					
					if ( $y ) {
						$timeToLaik = new cas( date('d.n.Y H:i:s', $y->date) );
						
						switch ($y->user) {
							case '1':
								$user = "SYSTEM";
								$info = "";
								break;
							
							default: 
								$user = $this->GD->USERDATA_O($y->user);
								$info = '<span class="ii iUser ti" data-info="'.sprintf($this->GD->text(340), $this->GD->text_($v->text), $user->nickname).'"></span>';
								break;
						}

						$INP = new INPUTS();

						$input = $y->type == 5 ? $INP->generate_inputs(50, "", "", "", true) : "";

						$head = $h.$info;
						$body = '<p><span data-livestamp="'.$y->date.'">'.$timeToLaik->result().'</span></p><div title="'.date('d.n.Y H:i:s', $y->date).'">'.$this->GD->date_( $y->date, true, 2 ).'</div>';

						
					} else {
						$ajaxx = !$ajax || $this->ajax_page == 50 ? " inpage" : "";

						$head = '<strike>'.$h.'</strike><i class="ii iRe" aria-hidden="true"></i>';
						$body = '<div class="tm-button"><a href="#" class="tmAction jdi'.$ajaxx.'" data-event="'.$v->comment.'" data-data="'.$v->id.'#'.$d->system_id.'">'.sprintf($this->GD->text(339), $h).'</a></div>';

						$input = "";
					}
					
					
					//$iconColor = $y ? $v->background : "e0e0e0";
					$iconType = $y ? $v->icon : "fa-circle";

					$active = $y ? " tmA" : " tmD";
					$lastBG = $y ? ' style="background:#'.$v->background.'"' : '';
					$lastC = $y ? ' style="color:#'.$v->background.'"' : '';

					$r .= '
					<div class="tm'.$active.'">
						<div class="tm-content">
							<div class="tm-head">
								<div class="tm-punt"></div>
								<div class="tm-icon"'.$lastBG.'></div>
								<span'.$lastC.'>'.$head.'</span>
							</div>
							<div class="tm-body">
								
								'.$body.'
							</div>
						</div>
						'.$input.'
					</div>
					';
			}

			return '
			<div class="invTimeline-body">
				'.$r.'
			</div>
			';
		}


		public function gen_itemlist($data, $ajax = true, $r = '') {
			$c = 1;

			if ( $data ) {

				foreach ( array_filter( explode("#", $data->content) ) as $key => $value) {
					$item = explode("*", $value);

					$D = $this->CHR->ITEMDATA( str_replace(".", "", $item[0]) );

					$imgLink = $this->GD->generate_pictureUrl( $D->file_list, true, $ajax );
					$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );

					$link_url = $this->GD->ofiurl( $D->url );

					//$PRICE = $data->price != 0 && in_array("price", $SD) ? $this->gen_price($data) : "";
					$price = $this->GD->price($item[2]*$item[1]);

					$title = ucfirst($D->title);
					$r .= '
						<li>
							<div class="invImage"><a href="'.$link_url.'" target="_blank"><img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.$title.'"></a></div>
							<div class="invItem">
								<div class="inviTitle nowrap"><a href="'.$link_url.'" target="_blank">'.$title.'</a></div>
								<div>'.$this->GD->price($item[2]).' / ks <div class="inviDetail"><span><strong>( '.$item[1].' ks )</strong> x '.$item[2].' = </span> <strong>'.$this->GD->price( $item[2] * $item[1] ).'</strong></div></div>
							</div>
							<div class="cleaner"></div>
						</li>';
				}

				if ( is_numeric($data->price_shipping) ) {
					
					$r .= '
					<li class="invDelivery">
						<div class="invImage"><span class="ii iDelivery" aria-hidden="true"></span></div>
						<div class="invItem">
							<div class="inviTitle nowrap">
								'.$this->GD->text_(518).'
								<div class="inviDetail"><strong>'.$this->GD->price( $data->price_shipping ).'</strong></div>
							</div>
						</div>
						<div class="cleaner"></div>
					</li>';
				}
				

			}

			return '
			<ul class="invItems">
				'.$r.'
			</ul>

			<div class="invAll">
				<span>( '.$this->GD->price($data->price_items).' + '.$this->GD->price($data->price_shipping).' )</span>
				'.$this->GD->price($data->price_all).'
			</div>
			';
		}











		public function gen_invoice_html() {
			$INV = $this->generate_invoiceitems();

			$invBody = array( 's' => $this->GD->text(213), 'p' => $this->GD->text( $INV["head"] ) );

			return '
				<div class="default-step-menu">
					<div class="nsm-content">
						'.$INV["menu"].'
					</div>

					<div class="cleaner"></div>
				</div>

				<div class="default-step-header">
					<p>'.$invBody["p"].'</p>
				</div>

				<div class="default-step-body">
					'.$INV["list"].'
				</div>
			';
		}
	}

