<?php
	$USERPROFIL = new USERPROFIL();

	class USERPROFIL {
		
		protected $mysql;
		protected $GD;
		public $CHR;

		public function __construct() {
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();

			$this->url = isset($_GET["sp"]) ? $_GET["p"]."/".$_GET["sp"] : $_GET["p"];
			$this->pd = $this->CHR->PD;





			$this->orderActual = " AND status = 1 OR status = 2 OR status = 3 OR status = 5";
			$this->orderDone = " AND status = 10";

			$this->orders = $this->order_query();
		}
		
		public function order_query($q = "", $r = '') {
			$stmt = $this->mysql->q("SELECT * FROm orders WHERE billing_email = '".$this->GD->userdata->userEMAIL."'".$q." ORDER BY date DESC");

			return $this->mysql->resultO();
		}







		public function gen_userMenu($r = '') {

			$arr = array(
				"101" => array("text" => 678, "icon" => "iInvoices"),
				"102" => array("text" => 679, "icon" => "iAdress"),
				"103" => array("text" => 680, "icon" => "iSettings")
				);

			foreach ($arr as $key => $value) {
				$url = $_GET["p"]."".
				//$A = d="uiplA"
				$active = $this->pd->page ==  $key ? ' id="uiplA"' : "";

				$r .= '<li'.$active.'><a href="'.$this->GD->link($key).'"><div class="ii '.$value["icon"].'"></div><p>'.$this->GD->text($value["text"]).'</p></a></li>';
			}

			return '
			<div class="uipl-menu">
				<ul>
					'.$r.'
				</ul>
			</div>
			';
		}

		public function gen_subuserMenu($r = '') {
			$menu = $tabs = "";

			$tab = $this->subTabs( $this->pd->page );

			if ( $tab ) {

				if ( $tab["tabs"] ) {
					foreach ($tab["tabs"] as $key => $value) {
						$D = $this->$tab["function"]($value["content"]);

						$menuA = $key == 0 ? ' id="ipTabA"' : "";
						$tabA = $key == 0 ? ' id="ipA"' : "";

						$counter = isset($D["counter"]) ? '<span class="uipr-count">'.$D["counter"].'</span>' : "";

						$menu .= '<li><a href="#'.$this->GD->text($value["name"]).'" data-target="'.$tab["target"].$key.'"'.$menuA.'>'.$this->GD->text($value["name"]).''.$counter.'</a></li>';
						


						if ( isset($value["head"]) || isset($value["title"]) ) {

							if ( isset($value["head"]) && is_array($value["head"]) ) {
								$head = sprintf( $this->GD->text($value["head"][0]), '<strong>'.$this->GD->text($value["head"][1]).'</strong>' );
							} else
								$head = isset($value["head"]) ? $this->GD->text($value["head"]) : " ";


							if ( isset($value["title"]) && is_array($value["title"]) ) {
								$title = "<h4>".sprintf( $this->GD->text($value["title"][0]), $this->GD->text($value["title"][1]) )."</h4>";
							} else
								$title = isset($value["title"]) ? "<h4>".$this->GD->text($value["title"])."</h4>" : "";


							$header = '<div class="uitabs-header">
								<h3>'.mb_strtoupper($head, "UTF8").'</h3>
								'.$title.'
							</div>';
						} else
							$header = "";
						

						$content = isset($D["content"]) ? $D["content"] : " ";

						$tabs .= '
						<div class="uitabs '.$tab["target"].$key.'"'.$tabA.'>
							'.$header.'

							'.$content.'
						</div>
						';
						//<li id="uiprA"><a href="#">'.$GD->text(674).'<span class="uipr-count">10</span></a></li>
					}
				}
			}
			
			$legend = isset($tab["legend"]) == true ? $this->gen_legend() : "";

			if ( $menu )
				return '
				<div class="uipr-menu">
					<ul class="tabs">
						'.$menu.'
					</ul>
				</div>

				<div class="uipr-body">
					'.$legend.'

					'.$tabs.'
				</div>
				';
		}

		public function subTabs($page, $r = '') {
			switch ($page) {
				case 101:
					$r = array(
						"target" => "tO",
						"function" => "tab_orders",
						"tabs" => array(
							0 => array("name" => 675, "head" => array(698, 711), "title" => array(701, 703), "content" => "actual" ),
							1 => array("name" => 676, "head" => array(698, 699), "title" => array(701, 704), "content" => "done"),
							2 => array("name" => 674, "head" => array(698, 700), "title" => array(701, 702), "content" => "all")
							),
						"legend" => true
						);
					break;

				case 102:
					$r = array(
						"target" => "tA",
						"function" => "tab_adress",
						"tabs" => array(
							0 => array("name" => 708, "head" => array(714, 715), "title" => 717, "content" => "billing"),
							1 => array("name" => 709, "head" => array(714, 716), "content" => "delivery") )
						);
			}

			if ( $r )
				return $r;
		}


		public function tab_orders($tab, $r = '') {

			switch ($tab) {
				case 'all':
					$data = $this->order_query( $this->orderActual );

					$r["content"] = $this->gen_orders( $data );
					$r["counter"] = count( $data );
					break;

				case 'actual':
					$data = $this->order_query( $this->orderActual );

					$r["content"] = $this->gen_orders( $data, true );
					$r["counter"] = count( $data );
					break;

				case 'done':
					$data = $this->order_query( $this->orderDone );

					$r["content"] = $this->gen_orders( $data );
					$r["counter"] = count( $data );
					break;
				
			}
			return $r;
		}

		public function tab_adress($tab, $r = '') {

			switch ($tab) {
				case 'billing':
					$data = $this->select_adress( "uzivatelia_billing_adress" );
					$r["content"] = $this->gen_adress( $data, 1 );
					$r["counter"] = count( $data );
					break;

				case 'delivery':
					$data = $this->select_adress( "uzivatelia_delivery_adress" );
					$r["content"] = $this->gen_adress( $data, 2 );
					$r["counter"] = count( $data );
					break;

				case 'done':
					$r = $tab;

					break;
				
			}
			return $r;
		}




		// ORDERS

		public function gen_orders($data, $visible = false, $r = '') {
			$num = 1;

			if ( $data ) {
				foreach ($data as $key => $value) {
					$timeToLaik = new cas( date("d.m.Y H:i", $value->date) );

					$status = $this->order_status( $value->status );

					$visible = $visible ? " sw_d" : "";

					$c = count( array_filter( explode("#", $value->content) ) );

					$r .= '
					<div class="order'.$visible.'">
						<div href="#" class="order-info">
							<span class="oh o-action"><span class="o-show ii iRight"></span><span class="o-hide ii iDown"></span></span>
							<span class="oh o-num">'.$num++.'</span>
							<span class="oh o-id"><strong>'.$value->system_id.'</strong></span>
							<span class="oh o-date" title="'.date("d.m.Y H:i:s", $value->date).'">'.$this->GD->adddate( $value->date, false ).'<span>('.$timeToLaik->result().')</span></span>
							<span class="oh o-price"><strong>'.$this->GD->price( $value->price_all ).'</strong></span>
							<span class="oh o-status"><i class="fa '.$status->icon.'" aria-hidden="true" style="color:#'.$status->background.'"></i>'.$this->GD->text( $status->text ).'</span>
							<span class="cleaner"></span>
						</div>
						<div class="order-details">
							'.$this->gen_items_in_order( $value ).'
							'.$this->gen_items_in_order_empty( $c, iTEM_W25, $value ).'
							<div class="cleaner"></div>
						</div>
					</div>
					';
				}
			}

			return '
			<div class="ui-orders">
				<div class="order-headers">
					<div class="oh o-action">&nbsp;</div>
					<div class="oh o-num">#</div>
					<div class="oh o-id">'.$this->GD->text(681).'</div>
					<div class="oh o-date">'.$this->GD->text(682).'</div>
					<div class="oh o-price">'.$this->GD->text(683).'</div>
					<div class="oh o-status">'.$this->GD->text(688).'<a href="#" title="'.$this->GD->text(692).'" class="toggleButton show" data-target=".uiol-content">(?)</a></div>
					<div class="cleaner"></div>
				</div>
				
				<div class="orders">
					'.$r.'
				</div>
			</div>
			';
		}

		public function gen_items_in_order($data, $r = '') {
			if ( $data ) {

				foreach ( array_filter( explode("#", $data->content) ) as $key => $value) {
					$item = explode("*", $value);

					$id = str_replace(".", "", $item[0]);
					
					$D = $this->GD->ITEMDATA($id);

					$imgLink = $this->GD->generate_pictureUrl( $D->file_list, true );
					$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );

					$link_url = $this->GD->url( $D->url );

					//$PRICE = $data->price != 0 && in_array("price", $SD) ? $this->gen_price($data) : "";
					$price = $this->GD->price($item[2]*$item[1]);

					$r .= '
					<div class="uitem" title="'.$D->title.'">
						<div class="uitem-content">
							<a href="'.$link_url.'"> <img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt="'.ucfirst($D->title).'">
								<div class="uitemInfo">
									<div class="uitemInfo-content">
											<div>'.$this->GD->text(691).' <strong>'.$this->GD->price($item[2]).'</strong></div>
											<div>'.$this->GD->text(690).' <strong>'.$item[1].'x</strong></div>
											<p>'.$item[2].' x '.$item[1].' = <strong>'.$price.'</strong></p>
									</div>
								</div>
							</a>
						</div>
					</div>';
				}


				if ( is_numeric($data->price_shipping) ) {
					$shipping = $data->price_shipping == 0 ? $this->GD->text(575) : "+ ".$this->GD->price($data->price_shipping);
					$r .= '
					<div class="uitem uitemDelivery">
						<div class="uitem-content">
							<span class="ii iDelivery" aria-hidden="true"></span>
							<div class="uitemInfo">
								<div class="uitemInfo-content">
									
									<p>'.$this->GD->text(297).'<strong>'.$shipping.'</strong></p>
								</div>
							</div>
						</div>
					</div>
					';
				}
				
			}

			return $r;
		}

		public function gen_items_in_order_empty($count, $width, $data = "", $r = '') {
			$minus = is_numeric($data->price_shipping) ? 1 : 0;

			$inRow = 100 / $width;
			$rows = ceil($count / $inRow);
			

			$empty = ($rows * $inRow) - $count - $minus;

			for ($i=0; $i < $empty; $i++) { 
				$r .= '
				<div class="uitem uitemEmpty"><div class="uitemCo"></div></div>
				';
			}

			return $r;
		}


		// ORDERS
		//$adress = preg_replace('/[^0-9]+/', '', $value->street).', '.$value->zip.' '.$value->city.', Slovakia';
		//$geo = $this->GD->geocode( $adress );


		public function select_adress($table, $r = '') {
			$this->mysql->q("SELECT * FROM $table WHERE email = '".$this->GD->userdata->userEMAIL."'");

			return $this->mysql->resultO();
		}

		public function gen_adress($data, $type, $r = '') {
			$num = 1;

			if ( $data ) {
				foreach ($data as $key => $value) {
					$num++;

					$NAME = $value->firstname." ".$value->lastname;

					$r .= '
						<div class="uiadress">
							<div class="uiadress-content uiadressActive">
								<div class="uiadress-data">
									<div class="uia-name">'.$NAME.'</div>
									<div class="uia-street">'.$value->street.'</div>
									<div class="uia-zipcity">'.$value->zip.' '.$value->city.'</div>
								</div>
								<div class="uiadress-tools">
									<a href="#" title="'.$this->GD->text(712).'" class="uiatool"><i class="fa fa-times" aria-hidden="true"></i></a><a href="#" title="'.$this->GD->text(713).'" class="uiatool"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								</div>

							</div>
						</div>
					';
				}
			}

			return '
				'.$r.'
				'.$this->gen_adress_addAdress($type).'
				'.$this->gen_adress_in_order_empty($num, ADRESS_W20).'
			';
		}

		public function gen_adress_addAdress($type, $r = '') {
			
			switch ($type) {
				case 1:
					$re = '
					<div class="uiaIcon"><div class="ii iBilling"></div></div>
					<a href="#" class="addAdress addBilling">'.$this->GD->text(718).'</a>
					';
					break;
				
				case 2:
					$re = '
					<div class="uiaIcon"><div class="ii iBilling"></div></div>
					<a href="#" class="addAdress addDelivery">'.$this->GD->text(718).'</a>
					';
					break;
			}

			$r .= '
			<div class="uiadress uiadressAdd">
					<div class="uiadress-content">
						'.$re.'
					</div>
				</div>
			';
			
			return $r;
		}

		public function gen_adress_in_order_empty($count, $width, $r = '') {
			$inRow = 100 / $width;
			$rows = ceil($count / $inRow);
			
			$empty = ($rows * $inRow) - $count - 1;

			for ($i=0; $i < $empty; $i++) { 
				$r .= '
				<div class="uiadress uiadressEmpty">
						<div class="uiadress-content">
							
						</div>
					</div>
				';
			}

			return $r;
		}

		// ADRESS



		// ADRESS

		
















		public function gen_legend($r = '', $r2 = "") {
			
			foreach ($this->order_status("", true) as $key => $value) {
				$a = $key == 0 ? ' uicA"' : "";

				$r .= '
				<li class="fl">
					<a href="#" class="uiCircle'.$a.'" data-target=".ct'.$key.'">
						<span><i class="fa '.$value->icon.'" aria-hidden="true" style="color:#'.$value->background.'"></i>'.$this->GD->text($value->text).'</span><div></div></a>
				</li>
				';

				$r2 .= '
					<div class="circleTabs ct'.$key.$a.'">
						'.$this->GD->text($value->body).'
					</div>
				';
			}

			return '
			<div class="ui-orderLegend">

				<div class="uiol-content">
					<a href="#" class="toggleButton hide" data-target=".uiol-content" data-toggle=".show"><span>'.$this->GD->text(697).'</span><div class="ii iExit"></div></a>

					<ul>
						'.$r.'
						<li class="cleaner"></li>
					</ul>
					
					
					'.$r2.'
				</div>
			</div>
			';
		}

		public function order_status($d, $all = false, $r = '') {

			$q =  $all != true ? "WHERE id = ".$d : "";

			$stmt = $this->mysql->q("SELECT * FROM orders_progress ".$q);

			return $all != true ? $stmt->fetch_object() : $this->mysql->resultO();
		}
	}

