<?php
	//$func = new BASKET();
	//require_once ("./functions/inputs.php");

	class BASKET {
		
		protected static $instance;
		protected $mysql;
		protected $GD;
		protected $CHE;

		protected $type;
		protected $data;

		public $AJAX;



		public function __construct($type = "") {
			$this->GD = GLOBALDATA::init();
			$this->CHE = CHECKINPUTS::init();

			$this->m = SQL::init();

			$this->AJAX = false;
			$this->shipping = 0;
			$this->payment = 0;

			$this->basketid = isset($_COOKIE["mm-uid"]) ? $_COOKIE["mm-uid"] : $this->GD->uid();
			$this->basket = $this->checkbasket( $this->basketid );
			//$this->basketItems = $this->GD->basket_data( $this->basket );
			
			$this->order_data();
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new BASKET();
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

		public function order_data($r = '') {
			
			$this->total_shipping = 0;
			$this->price = 0;
			$this->price_nodph = 0;
			
			$this->shipping = 0;
			$this->payment = 0;
			
			$this->dph = 0;
			$this->total = 0;
			$this->quantity = 0;

			$this->update_delivery(true);
			if ( $d = $this->GD->basket_data( $this->basket ) ) {

				//if ( $d['d'] ) {
					foreach ($d['d'] as $key => $value) {
						
						//$item = $this->GD->verify_itemid( $value["id"] );

						if ( $item = $this->GD->verify_itemid( $value["id"] ) ) {
							$imgLink = $this->GD->generate_pictureUrl( $item->file_list, true );
							$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
							$link_url = $this->GD->url( $item->url );
							$PRICE = $item->discount ? $this->GD->discount( $item->price, $item->discount) : $item->price;
							$PRICE_ALL = $PRICE * $value["quantity"];

							$this->quantity += $value["quantity"];
							$this->price += $PRICE_ALL;
						}
					}
				//}
				
					
				$this->update_delivery(true);
				//$this->dph = $this->price * (DPH / 100);
				//$this->price_nodph = $this->price - $this->dph;
				$this->price_nodph = $this->price;

				if ( isset($d['error']) )
					return $d['error'];
			}

			
		}
/*
		public function order_details($id, $r = false) {
			$stmt = $this->m->q("SELECT * FROM orders WHERE system_id = '".$id."'");

			if ( $order = $stmt->fetch_object() )
				$r = $order;
			
			return $r;
		}*/


		public function check_basket($value='') {
			if ( $this->basket->content && $this->basket->payment && $this->basket->payment && $this->basket->billing_firstname && $this->basket->billing_lastname && $this->basket->billing_phone && $this->basket->billing_email && $this->basket->billing_street && $this->basket->billing_city && $this->basket->billing_zip )
				return true;
			else
				return false;
		}

		public function action($type, $data, $r = "") {
			//global $GD;
			//$this->data = $this->create_data( $data );
			$this->data = $data;
			$this->type = $type;

			switch ($this->type) {

				case 'addtobasket':
					$this->data = $this->create_data( $data );

					if ( $this->verify_data_switch() != false ) {

						$re = $this->basket_update();

						$this->basket = $this->checkbasket($this->basketid);

						$this->order_data();

						$r = array( 
						"basket" => $this->gen_basket(), 
						"addbutton" => $this->GD->generate_addtobasket( $this->GD->verify_itemid( $this->data->basketitemid ), $this->basket) );
					} else {
						$r = array(
						"addbutton" => $this->GD->generate_addtobasket( $this->GD->verify_itemid( $this->data->basketitemid, $_SERVER["HTTP_REFERER"] ), $this->basket) );
					}

					
					break;

				case 'editbasket':
					$this->data = $this->create_data( $data );

					$this->AJAX = true;

					if ( $this->verify_data_switch() != false ) {

						$re = $this->basket_update();

						$this->basket = $this->checkbasket($this->basketid);

						$this->order_data();


						$r = array( 
							"basket" 		=> $this->gen_basket(), 

							"addbutton" 	=> $this->GD->generate_addtobasket( $this->GD->verify_itemid( $this->data->basketitemid ), $this->basket),
							"cartsummary" 	=> $this->cart_summary(),
							"cartlist" 		=> $this->basket_items());
					}

				 	else {
						$r = array(
						"addbutton" => $this->GD->generate_addtobasket( $this->GD->verify_itemid( $this->data->basketitemid, $_SERVER['HTTP_REFERER'] ), $this->basket) );
					}
					
					break;

				case 'updatedetype':
					//$this->data = $data;

					if ( $delivery = $this->GD->verify_detype( $this->data ) ) {
						$this->m->q("UPDATE basket SET shipping = '".$delivery->id."' WHERE basket = '". $this->basket->basket."'");

						//reset payment
						$this->m->q("UPDATE basket SET payment = NULL WHERE basket = '". $this->basket->basket."'");

						$this->shipping = $this->data;

						$this->payment = 0;

						$this->update_delivery();
					}

					$r = array( 
						"cartsummary" 	=> $this->cart_summary());
					break;

				case 'updatepayment':
					//$this->data = $data;
				//$this->order_data();
					if ( $this->basket->shipping ) {
						if ( $payment = $this->GD->verify_payment( $this->data ) ) {
							$this->m->q("UPDATE basket SET payment = '".$payment->id."' WHERE basket = '". $this->basket->basket."'");

							$this->payment = $this->data;

							$this->update_delivery();
						}
					}
					
					$r = array( 
						"cartsummary" 	=> $this->cart_summary());
					break;







				case 'billing-firstname':
				case 'billing-lastname':
				case 'billing-phone':
				case 'billing-email':
				case 'billing-street':
				case 'billing-city':
				case 'billing-zip':

				case 'delivery-firstname':
				case 'delivery-lastname':
				case 'delivery-phone':
				case 'delivery-street':
				case 'delivery-city':
				case 'delivery-zip':

				case 'company-company':
				case 'company-cid':
				case 'company-tin':
				case 'company-tax':
					//$this->data = $data;

					//$stmt = $this->m->q("SELECT * FROM inputs WHERE idd = '$this->type'");
					

					//if ( $req = $stmt->fetch_object() ) {
						//if ( $req->required == true ) {
							$check = $this->CHE->action($this->type, $this->data);



							$icon = $check ? '<i class="'.$this->input_icon(false).'" aria-hidden="true"></i>' : '<i class="'.$this->input_icon(true).'" aria-hidden="true"></i>';

							if ( $check ) {
								
								$r = array( 
									"icon" 	=> $icon,
									"error"	=> $check
									);
								
							} else {
								$r = array( 
								"icon" 	=> $icon,
								"error"	=> "false" );
							}
						//}
					//}
					
					

					

					break;


				case 'basketcontinue':

					$entry_types = array("billing", "delivery", "company");

					$check_billing = $check_delivery = $check_company = false;


					if ( $this->basket->content ) {
						if ( !$dd = $this->check_delivery() ) {
							if ( $separated = $this->create_separated_data($data) ) {

								$rq = $this->reguired_data($separated);

								if ( !$errors = $this->show_errors($rq) ) {

									//$this->save_basket( $this->data );
									if ( $this->save_basket( $this->data ) ) {
										$r = array("continue" => $this->GD->link(18) );
									}
									else {
										$r = array("errors" => $this->data);
									}
									
								} else {
									$r = array("error" => $this->GD->text(584), "scroll" => "deliveryF", "errors" => $errors, "scroll" => "userdataF");
								}
							}
						} else {
							$r = array("error" => $dd, "scroll" => "deliveryF");
						}
					} else {
						$r = array("error" => $this->GD->random_text( array($this->GD->text(580), $this->GD->text(581), $this->GD->text(582)) ), "scroll" => "basketF");
					}
					

					//$re = array("billing" => $check_billing, "delivery" => $check_delivery, "company" => $check_company);

					//$r = $re;
					break;

				case "sendorder":

					if ( $r = $this->send_order($this->basket) ) {

						$mail = new EMAIL();

						$mail->email_type(1, $r);
						$this->GD->destroy_cookie("mm-uid");
						$this->GD->uid(true);

						$r = array( "complete" 	=> $this->generate_ordercomplete($r) );

						$this->update_availablity_aftersend($this->basket->content);
					}
					break;
			}

			return $r;
		}

		public function generate_ordercomplete($id, $r = '') {
			
			return '
			<div class="purchased">
				<div class="purchased-content">
					<div class="purchased-head"><h2><span>'.$this->GD->text(599).'</span></h2></div>
					<p>'.sprintf($this->GD->text(600), '<span>'.$id.'</span>').'</p>
					<p>'.sprintf($this->GD->text(603), '<span>'.$this->basket->billing_email.'</span>').'</p>
					<a href="'.$this->GD->link(1).'" class="ls05">'.$this->GD->text(602).'</a>
				</div>
			</div>
			';
		}

		// BASKET CONTINUE
		public function create_separated_data($data, $r='') {
			foreach ($data as $key => $value) {
				
				$type = explode("-", $key);

				
				$stmt = $this->m->q("SELECT * FROM inputs WHERE idd = '$key'");
				
				if ( $req = $stmt->fetch_object() ) {
					if ( $req->required == true || strlen($value) > 0 ) {
						if ( $re = $this->CHE->action($key, (object) [$type[1] => $value]) )
							$r[$type[0]][$key] = $re;
						else
							$r[$type[0]][$key] = "OK";
					}
				}
			
			}

			return $r;
		}


		public function reguired_data($data, $r='') {

			if ( $data ) { 
				foreach ($data as $key => $value) {

					if ( in_array("OK", $value) )
						$r[$key] = $value;
					else {

						if ( $key == "billing" )
							$r[$key] = $value;
						else
							$r[$key] = false;
					}
				}
			}
			

			return $r;
		}


		public function show_errors($data, $r='') {
			foreach ($data as $key => $value) {

				if ( is_array($value) ) {
					
					foreach ($value as $k => $v) {
						
						if ( $v != "OK" )
							$r[$k] = $v;
					}
				}
				
			}

			return $r;
		}
		
		public function check_delivery($r = '') {
			
			if ( !$this->basket->shipping /*!$this->GD->verify_detype($this->basket->shipping*/ ) {
				$r = $this->GD->random_text( array($this->GD->text(578), $this->GD->text(583)) );
			} else {
				if ( !$this->basket->payment /*!$this->GD->verify_payment($this->basket->payment)*/) {
					$r = $this->GD->text(579);
				}
			}

			return $r;
		}
		// BASKET CONTINUE

		public function input_icon($re, $r = "") {
			switch ($re) {
				case true:
					$r = 'fa fa-check baOK';
					break;
				case false:
					$r = 'fa fa-times baBAD';
					break;
			}

			return $r;
		}

		public function create_data($data, $r = '') {
			
			$object = new stdClass();

			foreach ($data as $key => $value) {
				$d = explode("-", $key);

				$object->$d[0] = $value;
			}
			/*foreach ($this->data as $key => $value) {
				if ( is_numeric($key) )
					$r[$key] = array("basketitemid" => $key, "basketquantity" => $value, "basketitemprice" => $value);
				else
					$r[$key] = $value;
			}*/

			return $object;
		}

		public function verify_data_switch($r = false) {
			
			foreach ($this->data as $key => $value) {
				switch ($key) {

					case 'basketitemid':
						$r = $this->GD->verify_itemid( $this->data->basketitemid );
						break;

					case 'basketquantity':
						$r = $this->verify_quantity();
						break;

					case 'basketitemprice':
						$r = $this->verify_price();
						break;

				}
			}

			return $r;
		}

		


		public function save_basket($data, $r='') {
			
			if ( $data ) {
				foreach ($data as $key => $value) {
					$set = str_replace("-", "_", $key);

					if ( $value ) {
						$this->m->query("UPDATE basket SET $set = '".$this->edit_value($set, $value)."' WHERE id = ".$this->basket->id);
						$r = true;
					} else {
						$this->m->query("UPDATE basket SET $set = '' WHERE id = ".$this->basket->id);
					}
				}
			}

			return $r;
		}


		public function send_order($data, $r = '') {

			if ( $data ) {

				$rok = date("Y");

				$ID = $this->GD->random_num_oddo(ORDER_NUM_MIN, ORDER_NUM_MAX).$rok;

				$stmt = $this->m->q("SELECT system_id FROM orders WHERE system_id = '".$ID."'");

				if ( !$stmt->fetch_object() )
					$new = $ID;
				else
					$new = $this->GD->random_num_oddo(ORDER_NUM_MIN_SECOND, ORDER_NUM_MAX_SECOND).$rok;



				if ( $this->m->q("INSERT INTO orders (id, system_id) VALUES (NULL, '$new')") ) {
					$this->m->q("INSERT INTO orders_info (id, system_id) VALUES (NULL, '$new')");

					$date = time();

					foreach ($data as $key => $value) {
						$d = $this->firstletterupper($key, $value);

						if ( $key != "id" )
							$this->m->q("UPDATE orders SET $key = '".$d."' WHERE system_id = '".$new."'");
					}

					$additonalData = array(
						"date"	=> $date,
						"price_all" => $this->total,
						"price_items" => $this->price,
						"price_shipping" => $this->total_shipping,
						"status" => "1"
					);

					foreach ($additonalData as $key => $value) {
						$this->m->q("UPDATE orders SET $key = '".$value."' WHERE system_id = '".$new."'");
					}

					
					$this->m->q("INSERT INTO orders_timeline (id, system_id, date, type) VALUES (NULL, '$new', '$date', '1')");
					

					return $new;
				}
			}
		}

		public function update_availablity_aftersend($bacontent, $r = '') {

			foreach ($this->basket_data2($bacontent) as $key => $v) {
				$up = $update = '';

				$where = 'WHERE id = '.$v['id'];

				$stmt = $this->m->q("SELECT quantity FROM navody ".$where);

				if ( $d = $stmt->fetch_object() ) {

					$up = $d->quantity - $v['quantity'];

					$update = $up <= 0 ? 0 : $up;

					if ( $update == 0 ) {
						//$q = "availability = '8' AND quantity = '0'";
						$this->m->q("UPDATE navody SET availability = 8 ".$where);
						$this->m->q("UPDATE navody SET quantity = 1 ".$where);
					}
					else
						$this->m->q("UPDATE navody SET quantity = '".$update."' ".$where);

				}
			}
			
		}

		public function basket_data2($bacontent, $r = '') {
			
			$basket = $bacontent;

			$rem = $repair = '';

			if ( strrpos($basket, "#") ) {

				foreach ( array_filter( explode("#", $basket) ) as $key => $value) {
					$detail = explode("*", $value);

					$id = str_replace(".", "", $detail[0]);


					$r[$id] = array("id" => $id, "quantity" => $detail[1], "price" => $detail[2]);
				}

			} else {
				$ba = explode("*", $basket);

				$id = str_replace(".", "", $ba[0]);

				$r[$id] = array( "id" => $id, "quantity" => $ba[1], "price" => $ba[2] ); 
			}

			return $r;
		}


		public function firstletterupper($key, $d, $r = '') {
			$arr = array(
				'billing_firstname',
				'billing_lastname',
				'billing_street',
				'billing_city',
				'delivery_firstname',
				'delivery_lastname',
				'delivery_street',
				'delivery_city'
			);

			foreach ($arr as $k => $value) {
				
				if ( $value == $key )
					$r = $this->GD->mb_ucfirst($d, "UTF8");
				else
					$r = $d;
			}

			return $r;
		}

		public function edit_value($type, $data, $r = '') {

			switch ($type) {
				case 'billing_firstname':
				case 'billing_lastname':
				case 'billing_street':
				case 'billing_city':
				case 'delivery_firstname':
				case 'delivery_lastname':
				case 'delivery_street':
				case 'delivery_city':
					$r = $this->GD->mb_ucfirst($data, "UTF8");
					break;

				case 'billing_email':
					$r = mb_strtolower($data, "UTF8");
					break;

				case 'billing_phone':
				case 'delivery_phone':
					$prefix = false;

					$edit = preg_replace('#\s+#', '', $data);

					switch ( strlen($edit) ) {
						case 10:
							$temp = str_split($edit);
							$pn = "";

							for ( $x = count($temp) - 1; $x>=0; $x-- ) {
							    if ($x === count($temp) - 4 || $x === count($temp) - 7 || $x === count($temp) - 11) {
							        $pn = " " . $pn;
							    }
							    $pn = $temp[$x] . $pn;
							}

							$r = $pn;

							$r = str_replace('0', '+421 ', substr($pn, 0, 1)).substr($pn, 1);
							break;

						case 12:
						case 13:
							if ( strpos($edit, "+") !== false ) {
								$re = str_replace("+", "", $edit);

								$r = "+".wordwrap($re, 3, ' ', true);
							}
							else
								$r = wordwrap($edit, 3, ' ', true);

							break;

						default:
							$r = $edit;
							break;
					}
					break;
				
				case "billing_zip":
				case "delivery_zip":
					$r = wordwrap($data, 3, ' ', true);
					break;

				default:
					$r = $data;
					break;
			}

			return $r;
		}
		/*
		//$r = wordwrap($data, 3, ' ', true );
					$raw_phone = preg_replace('/\D/', '', $data);
					$temp = str_split($raw_phone);
					$phone_number = "";
					for ($x=count($temp)-1;$x>=0;$x--) {
					    if ($x === count($temp) - 4 || $x === count($temp) - 8 || $x === count($temp) - 11) {
					        $phone_number = "-" . $phone_number;
					    }
					    $phone_number = $temp[$x] . $phone_number;
					}
					$r = $phone_number;
					*/

		/*public function verify_itemid($id, $referer = "", $r = false) {
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
		}*/

		public function verify_quantity($r = false) {
			
			$stmt = $this->m->q("SELECT quantity FROM navody WHERE public = 1 AND id = ".$this->data->basketitemid);

			if ( $d = $stmt->fetch_object() ) {
				
				if ( $this->data->basketquantity <= 0 )
					$this->data->basketquantity = 0;
				else {
					if ( $this->data->basketquantity <= $d->quantity )
						$r = $this->data->basketquantity;
					else {
						//$r = $d->quantity;
						$this->data->basketquantity = $d->quantity;
					}
				}
			}

			return $r;
		}

		public function verify_price($r = false) {
			
			$stmt = $this->m->q("SELECT price, discount FROM navody WHERE public = 1 AND id = ".$this->data->basketitemid);

			if ( $d = $stmt->fetch_object() ) {
				
				$PRICE = $d->discount ? $this->GD->discount( $d->price, $d->discount) : $d->price;
				
				if ( $this->data->basketitemprice != $PRICE )
					$this->data->basketitemprice = $PRICE;
				else
					$r = $PRICE;


			}

			return $r;
		}
/*
		public function verify_detype($id, $r = false) {
			$stmt = $this->m->q("SELECT * FROM delivery_type WHERE id = ".$id);

			if ( $r = $stmt->fetch_object() )

			return $r;
		}

		/*public function verify_payment($id, $r = false) {
			$stmt = $this->m->q("SELECT * FROM delivery_payment WHERE id = ".$id);

			if ( $r = $stmt->fetch_object() )

			return $r;
		}
*/



		public function update_delivery($load = false, $r='') {

			//if ( $this->shipping && $this->payment )
			if ( $load == true ) {
				$this->shipping = empty($this->basket->shipping) ? 0 : $this->basket->shipping;
				$this->payment = empty($this->basket->payment) ? 0 : $this->basket->payment;;
			}
			

			$sd = $this->shipping_data("delivery_type", $this->shipping);
			$sp = $this->shipping_data("delivery_payment", $this->payment);
			
			if ( $sd && $sp ) {
				$this->total = $this->price + $sd->price + $sp->price;
				$this->total_shipping = $sd->price + $sp->price;
			}
			else {
				if ( $sd ) {
					$this->total = $this->price + $sd->price;
					$this->total_shipping = $sd->price;
				}
				else if ( $sp ){
					$this->total = $this->price + $sp->price;
					$this->total_shipping =  $sp->price;
				}
				else{
					$this->total = $this->price;
					$this->total_shipping = 0;
				}
			}


			
		}


		public function checkbasket($id, $r = '') {
			
			$stmt = $this->m->q("SELECT * FROM basket WHERE basket = '".$id."'");

			if ( $r = $stmt->fetch_object() ) {
			} else {
				$this->m->q("INSERT INTO basket (id, basket) VALUES(NULL, '$id')");

				/*if ( $last_id = $this->m->conn->insert_id ) {
					$stmt = $this->m->q("SELECT * FROM basket WHERE basket = '".$last_id."'");
					$r = $stmt->fetch_object();
				}*/
			}

			return $r;
		}

		//1#2001#
		/*public function basket_data($r = '') {

			if ( $basket = $this->basket->content ) {
				
				if ( strrpos($basket, "#")) {
					//$items = array_filter( explode("#", $basket) );

					foreach ( array_filter( explode("#", $basket) ) as $key => $value) {
						$detail = explode(".", $value);

						$r[$detail[0]] = array("id" => $detail[0], "quantity" => $detail[1]);
					}

				} else {
					$ba = explode(".", $basket);

					$r[$ba[0]] = array( "id" => $ba[0], "quantity" => $ba[1] );
				}
			}

			return $r;
		}*/

		//1000#1#
		public function basket_update($r = '') {
			$re = "";

			if ( $data = $this->GD->basket_data( $this->basket ) ) {

				//$count = count($data);

				//if ( count($data) != 1 ) {
				//if ( $this->data->basketitemid != 0 ) {
				

				foreach ($data['d'] as $key => $value) {

					if ( $value["quantity"] != 0 ) {
						if ( $value["id"] == $this->data->basketitemid ) {

							if ( $value["quantity"] == $this->data->basketquantity )
								$re[$key] = array( "id" => $value["id"], "quantity" => $value["quantity"], "price" => $value["price"] );
							else
								$re[$key] = array( "id" => $value["id"], "quantity" => $this->data->basketquantity, "price" => $value["price"] );
						}
						else {

							$re[ $this->data->basketitemid ] = array("id" => $this->data->basketitemid, "quantity" => $this->data->basketquantity, "price" => $this->data->basketitemprice );
							$re[$key] = array("id" => $value["id"], "quantity" => $value["quantity"], "price" => $value["price"]);
						}

					}
				}
				
				
				
				if ( $re )
					$r = $this->create_content_for_basket( $re );

			} else {
				if ( $this->data->basketquantity != 0 )
					$r = ".".$this->data->basketitemid.".*".$this->data->basketquantity."*".$this->data->basketitemprice;
				//$this->m->q("UPDATE basket SET content = '".$r."' WHERE basket = '". $this->basket->basket."'");
			}
			
			$this->m->q("UPDATE basket SET content = '".$r."' WHERE basket = '". $this->basket->basket."'");

			//return $r;
		}


		public function create_content_for_basket($data, $r = '') {
			
			if ( $data ) {
				$count = count($data);

				foreach ($data as $key => $value) {
					
					if ( $value["quantity"] != 0 ) {
						if ( $count == 1 ) {
							$r .= ".".$value["id"].".*".$value["quantity"]."*".$value["price"];
						} else {
							$r .= ".".$value["id"].".*".$value["quantity"]."*".$value["price"]."#";
						}
					}
					
				}
			}

			return $r;
		}











		public function gen_basket($r = '') {

			//$this->items = array_filter( explode("#", $basket->content ) );
			if ( $basket = $this->checkbasket($this->basketid) ) {
				$c = count( array_filter( explode("#", $basket->content ) ) );

				//$test_cart = array( number_format( (rand(0, 100000*100) / 100), 2)." €", mb_strtoupper($this->GD->text(192), "utf8") );
				//$testCart = array_rand($test_cart);

				$icon = $c == 0 ? "iCart" : "iCartF";

				$BI = $this->basket_info();

				$r = '
					<a href="'.$this->GD->link(17).'" class="lfs p01 tac">
						<div class="rm-icon">
							<div class="ii '.$icon.'">
								<div class="cart-counter">'.$c.'</div>
							</div>
						</div> 
						<div class="rm-cart"><strong>'.$BI->price.'</strong></div>
					</a> 
				';
			}
			

			return $r;
		}

		public function basket_info($r = '') {
			$count = $price = "";

			if ( $d = $this->GD->basket_data( $this->basket ) ) {

				if ( $d['d'] ) {
					foreach ($d['d'] as $key => $value) {
						$IP = $this->GD->ITEMPROFILE_O( "id", $value["id"] );

						if ( $IP->discount )
							$r +=  $value["quantity"] * $this->GD->discount($IP->price, $IP->discount);
						else
							$r +=  $value["quantity"] * $IP->price;

		
						//$count += $value["quantity"];
					}
					$price = $this->GD->price($r);
				} else
					$price = $this->GD->text(510);
				
			} else 
				$price = $this->GD->text(510);
			

			return (object) array("price" => $price/*, "count" => $count*/); 
		}



		public function gen_price($data, $r = '') {

			switch ( $data->availability ) {
				case 1:
					$price = $this->GD->price($data->price);

					if ( $data->discount ) {
						$r = $this->GD->price( $this->GD->discount($data->price, $data->discount) ).'<del>'.$price.'</del>';
					} else {
						$r = $price;
					}
				break;

				case 8: 
					$r = '<div>'.$this->GD->text(293).'</div>';
				break;

				default: 
					$r = '<div>'.$this->GD->text(507).'</div>';
				break;
			}

			return '<div class="diyDesc-price">'.$r.'</div>';
		}
		
		public function basket_items($editable = true, $r = '') {
			
			$actions = $actionsH = "";
			$e_text = $this->GD->text( $this->GD->random_text( array(723, 878) ));

			if ( $d = $this->GD->basket_data( $this->basket ) ) {

				if ( $d['d'] ) {
					foreach ($d['d'] as $key => $value) {
						
						$item = $this->GD->verify_itemid( $value["id"] );


						if ( $item ) {
							$imgLink = $this->GD->generate_pictureUrl( $item->file_list, true, $this->AJAX );
							$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
							$link_url = $this->GD->url( $item->url );
							$PRICE = $item->discount ? $this->GD->discount( $item->price, $item->discount) : $item->price;
							$PRICE_ALL = $PRICE * $value["quantity"];

							if ( $item->discount ) {
								$discount = "- ".$this->GD->price( ($item->price - $this->GD->discount( $item->price, $item->discount)) * $value["quantity"] );
								$diss = "";
							} else {
								$discount = "&nbsp;";
								$diss = " nodiscount";
							}
							
							$qButtons = $this->quantity_buttons($value["quantity"], $item->quantity, $item->id);

							if ( $editable == true ) {
								
								$changeQ = '
								<div class="ba-quantity" data-maxquantity="'.$item->quantity.'">
									<div class="baq-c">
										<input type="number" class="dn" value="'.$item->id.'" name="basketitemid" id="basketitemid-'.$item->id.'" autocomplete="off" disabled="disabled">
										<input type="text" class="ba-qty" id="basketquantity-'.$item->id.'" name="basketquantity" value="'.$value["quantity"].'" min="1" max="'.$item->quantity.'" data-lib="basket" data-event="editbasket" data-data="'.$item->id.'" autocomplete="off" disabled="disabled">
										<input type="number" class="dn" value="'.$PRICE.'" name="basketitemprice" id="basketitemprice-'.$item->id.'" autocomplete="off" disabled="disabled">

										<div class="mobile-quantity">
											<button href="#" type="button" class="select-switcher showw" data-target=".select-'.$item->id.'"><span class="ii iDown"></span></button>
											
											<div class="selectdata select-'.$item->id.'" data-target="#basketquantity-'.$item->id.'">
												<div class="selecthide"><button href="#" type="button" class="select-switcher" data-target=".select-'.$item->id.'"><span class="ii iUp"></span></button></div>
												'.$this->GD->mobile_availability($item->quantity).'
											</div>
										</div>

										
									</div>
									<div class="cleaner"></div>
								</div>
								';

								$actions = '<div class="ba-actions"><button type="button" class="deleteItem" data-target="basketquantity-'.$item->id.'" title="'.$this->GD->text(722).'"><span class="ii iTrash"></span></button></div>';
								$actionsH = '<div class="ba-actions"></div>';
							} else {
								$changeQ = '
								<div class="ba-quantity" data-maxquantity="'.$item->quantity.'">
									<input type="text" class="ba-qty" name="basketquantity" value="'.$value["quantity"].'" autocomplete="off" disabled="disabled">
								</div>
								';
							}

							$r .= '
								<div class="ba-item">

									<div class="ba-details">
										<div class="ba-info"><div class="ba-img"><a href="'.$link_url.'" class="diyAnch"><img src="'.$imgLink["url"].'" class="diyImg img'.$imgSize.'" alt=""></a></div></div>
										<div class="ba-data">
											<div class="ba-in">
												<div class="ba-head nowrap"><a href="'.$link_url.'">'.$this->GD->mb_ucfirst($item->title).'</a></div>
											</div>

											<div class="ba-perone">'.$this->GD->price( $item->price ).'<span> / '.$this->GD->text(628).'</span></div>
											
											'.$changeQ.'

											<div class="ba-prices">
												<div class="ba-discount'.$diss.'">'.$discount.'</div>
												<div class="ba-price"><strong>'.$this->GD->price($PRICE_ALL).'</strong></div>
												<div class="cleanerr"></div>
											</div>
											'.$actions.'
										</div>
									</div>

									<div class="cleaner"></div>
								</div>

							';
						}
					}
				} else {
					return '
					<div class="basketNoitems">
						<div class="ii iBasket"></div>
						<p>'.$e_text.'</p>
					</div>';
				}
				

				return '
				<div class="ba-tabhead">
					<div class="ba-info">&nbsp;</div>
					<div class="ba-in">'.$this->GD->text(594).'</div>
					<div class="ba-perone">'.$this->GD->text(514).'</div>
					<div class="ba-quantity">'.$this->GD->text(515).'</div>
					<div class="ba-discount">'.$this->GD->text(506).'</div>
					<div class="ba-price">'.$this->GD->text(516).'</div>
					'.$actionsH.'
					<span class="cleaner"></span>
				</div>
				'.$r.'';

			} else {
				return '
				<div class="basketNoitems">
					<div class="ii iBasket"></div>
					<p>'.$e_text.'</p>
				</div>';
			}

			
		}

		public function quantity_buttons($a, $max, $ID, $r = '') {
			$arr = array();

			$inacMinus = $a == 1 ? " bIn" : "";
			$inacPlus = $a == $max ? " bIn" : "";

			$arr["minus"] = '<button type="button" class="qty qChange qDown'.$inacMinus.'" data-target="basketquantity-'.$ID.'"><i class="fa fa-minus" aria-hidden="true"></i></button>';
			
			$arr["plus"] = '<button type="button" class="qty qChange qUp'.$inacPlus.'" data-target="basketquantity-'.$ID.'"><i class="fa fa-plus" aria-hidden="true"></i></button>';
			
			return $arr;
		}

		public function select_quantity($count = 1, $r = '') {
			
			for ($i=1; $i < $count + 1; $i++) { 
				$r .= '
					<option value="'.$i.'">'.$i.'</option>
				';
			}

			return $r;
		}

		public function basket_shipping($r = '') {
			
			if ( $d = $this->basket ) {

				//foreach ($d as $key => $value) {
					
					$r .= '
						<div class="ba-subitem">
							'.$d->shipping.'
							<div class="cleaner"></div>
						</div>

					';
				//}

			}

			return '
			
			'.$r.'';
		}


		public function cart_summary($r = '') {
			
			//$this->order_data();

			//$shipping = $this->shipping != 0 ? $this->GD->price($this->shipping) : '<a href="#doprava-a-platba">'.$this->GD->text(530).'</a>';

			/*$delivery = '<div class="cart-shipping"><p>'.$this->GD->text(518).'</p><span><strong>'.$shipping.'</strong></span></div>';*/

			$delivery = $this->gen_shipping_details($this->shipping, $this->payment);

			return '
					<div class="cart-subtotal"><p>'.$this->GD->text(517).'</p><span><strong>'.$this->GD->price($this->price).'</strong></span></div>

					'.$delivery.'

					<div class="cart-total">
						<p>'.$this->GD->text(520).'</p><span><strong>'.$this->GD->price($this->total).'</strong></span>
					</div>
			';
		}

		public function gen_shipping_details($transtype, $payment, $r="", $re='') {
			
			$def_trans = $transtype_detail = array("name" => "", "val" => '<a href="#doprava-a-platba">'.$this->GD->text(537).'</a>');
			$def_pay = $payment_detail = array("name" => "", "val" => '<a href="#doprava-a-platba">'.$this->GD->text(537).'</a>');

			if ( isset($transtype) ) {
				$data = $this->shipping_data("delivery_type", $transtype);

				if ( $data )
					$transtype_detail = array("name" => " <strong>".$data->name."</strong>", "val" => $data->price != 0 ? "+ ".$this->GD->price($data->price) : "-" );
				else
					$transtype_detail = $def_trans;
			} else 
				$transtype_detail = $def_trans;

			if ( isset($payment) ) {
				$data = $this->shipping_data("delivery_payment", $payment);

				if ( $data )
					$payment_detail = array("name" => " <strong>".$data->name."</strong>", "val" => $data->price != 0 ? $this->GD->price($data->price) : '-' );
				else
					$payment_detail = $def_pay;
			} else
				$payment_detail = $def_pay;


			return '

			<div class="cart-shipping"><p>'.$this->GD->text(518).$transtype_detail["name"].'</p><span><strong>'.$transtype_detail["val"].'</strong></span></div>

			<div class="cart-payment"><p>'.$this->GD->text(536).$payment_detail["name"].'</p><span><strong>'.$payment_detail["val"].'</strong></span></div>
			';

			//return $r;
		}


		public function shipping_data($table, $select, $r = 0) {

			if ( $select ) {
				$stmt = $this->m->q("SELECT * FROM $table WHERE id = ".$select);

				if ( $d = $stmt->fetch_object() )
					$r =  $d;
			}
			

			return $r;
		}





		/*public function generate_inputs($form, $values = false, $r='') {
			$val = $icon = "";

			$stmt = $this->m->q("SELECT * FROM inputs WHERE form = ".$form." ORDER BY formpos ASC");

			foreach ($this->m->result2() as $key => $value) {
				
				$class = $value["class"] ? $value["class"] : "baInp";
				$req = $value["required"] ? '<span class="req">*</span>' : "";

				
				if ( $values == true ) {
					$target = str_replace("-", "_", $value["idd"]);

					//if ( $target == $this->basket->$target )
					$val = 'value="'. $this->basket->$target.'"';
					
					if ( $this->basket->$target )
						$icon = '<i class="fa fa-check baOK" aria-hidden="true" style="display:none"></i>';
					else 
						$icon = "";
				}

				$r .= '
				<div class="inpbox">
					<label for="'.$value["idd"].'" class="baLa"><span>'.$this->GD->text($value["text"]).'</span>'.$req.'</label>
					<input type="text" class="'.$class.'" name="'.$value["name"].'" id="'.$value["idd"].'"'.$val.'>
					<div class="baStatusIcon">'.$icon.'</div>
					<div class="baStatus">
						<label for="'.$value["idd"].'"></label>
					</div>
				</div>
				';
			}
			return $r;
		}*/


		public function select_delivery_type($r = '') {

			$stmt = $this->m->q("SELECT * FROM delivery_type WHERE active = 1");

			//if ( $re = $this->m->resultO() ) {
				foreach ($this->m->resultO() as $key => $value) {
					$key += 1;

					$delAct = $this->basket->shipping == $value->id ? ' id="delivery-type"' : '';


					$dd = $this->GD->delivery_date($value->days);

					$icon = $value->icon ? '<span class="ii '.$value->icon.'"></span>' : '';
					$logo = $value->logo ? '<span class="ii '.$value->logo.'"></span>' : '';

					$p = $value->days ? sprintf('<p>'.$this->GD->text(529).'</p>', '<strong>'.date("d.m.Y", $dd).'</strong>') : "";

					$price = $value->price ? '<strong>+ '.$value->price." €".'</strong>' : '<strong class="delivery-free">'.$this->GD->text(575).'</strong>';

					$text = $value->text ? '<div class="choice-detail">'.$this->GD->text($value->text).'</div>' : '';
					
					$r .= '
					<div class="choice"'.$delAct.'>
						<button type="button" class="delivery-choice" data-payment="'.$value->payment.'" data-lib="basket" data-event="updatedetype" data-data="'.$value->id.'">
							<span>'.$icon.''.$value->name.$price.'</span>
						
							'.$p.'
						</button>

						'.$text.'
					</div>';
				}
			//}

			return $r;
		}

		public function select_delivery_payment($r='') {
			//$key = 0;
			if ( $this->basket->shipping ) {
				$stmt = $this->m->q("SELECT * FROM delivery_type WHERE id = ".$this->basket->shipping);

				$shipi = $stmt->fetch_object();

				foreach ( explode("#", $shipi->payment) as $key => $value) {
					$shippingMethods[] = $value;
				}
			}

			$stmt = $this->m->q("SELECT * FROM delivery_payment WHERE active = 1");

			//if ( $r = $stmt->fetch_object() ) {
				foreach ($this->m->result2() as $key => $value) {
					$key += 1;

					$delAct = $this->basket->payment == $value["id"] ? ' id="delivery-payment"' : "";

					if ( $this->basket->shipping ) {
						$disabled = $hidden = '';

						if ( in_array($value["id"], $shippingMethods) )
							$style = 'display: block;';
						else
							$style = 'display: none;';

					} else {
						$disabled = 'disabled="disabled"';
						$hidden = ' choiceDis';
						$style = 'display: block;';
					}

					$icon = $value["icon"] ? '<span class="ii '.$value["icon"].'"></span>' : "";

					$p = $value["comment"] ? '<p>'.$this->GD->text($value["comment"]).'</p>' : "";

					//$price = $value["price"] ? $value["price"]." €" : $this->GD->text(575);
					$price = $value["price"] ? '<strong>+ '.$value["price"]." €".'</strong>' : '<strong class="delivery-free">'.$this->GD->text(575).'</strong>';
					$text = $value["text"] ? '<div class="choice-detail">'.$this->GD->text($value["text"]).'</div>' : "";
					
					$r .= '
					<div class="choice pays payment-'.$value["id"].$hidden.'" style="'.$style.'"'.$delAct.'>
						<button type="button" class="delivery-choice" data-lib="basket" data-event="updatepayment" data-data="'.$value["id"].'"'.$disabled.'>
							<span>'.$icon.''.$value["name"].$price.'</span>
						</button>

						'.$p.'
						

						'.$text.'
					</div>
					';
				}
			//}

			return $r;
		}

/*
		public function shipping_payments ($type, $r='') {
			$stmt = $this->m->q("SELECT * FROM delivery_type WHERE id = ".$type);
		}
*/


		/* ORDER PAGE */

		public function generate_adresss ($r='') {
			
			$billing = $delivery = $company = "";

			
			if ( $this->basket->delivery_firstname && $this->basket->delivery_lastname && $this->basket->delivery_phone && $this->basket->delivery_phone && $this->basket->delivery_street && $this->basket->delivery_city && $this->basket->delivery_zip ) {

				$billname = $this->basket->delivery_firstname.' '.$this->basket->delivery_lastname;

				$delivery .= '
				<div class="och-adress deliAdress">
					<div class="ocha-content">
						<div class="ocha-head"><i class="ii iAdress" aria-hidden="true"></i>'.$this->GD->text(550).'</div>
						<div class="ocha-body">
							<p>'.$billname.'</p>
							<p>'.$this->basket->delivery_street.'</p>
							<p>'.$this->basket->delivery_zip.' '.$this->basket->delivery_city.'</p>
							<p class="notimp">Slovenská republika</p>

							<div class="ocha-contact">
								<p><i class="ii iPhone" aria-hidden="true"></i>'.$this->basket->delivery_phone.'</p>
							</div>
						</div>
					</div>
				</div>
				';
			} else {
				$billname = $this->basket->company_company ? $this->basket->company_company : $this->basket->billing_firstname.' '.$this->basket->billing_lastname;

				$delivery .= '
				<div class="och-adress deliAdress">
					<div class="ocha-content">
						<div class="ocha-head"><i class="ii iAdress" aria-hidden="true"></i>'.$this->GD->text(550).'</div>
						<div class="ocha-body">
							<p>'.$billname.'</p>
							<p>'.$this->basket->billing_street.'</p>
							<p>'.$this->basket->billing_zip.' '.$this->basket->billing_city.'</p>
							<p class="notimp">Slovenská republika</p>

							<div class="ocha-contact">
								<p><i class="ii iPhone" aria-hidden="true"></i>'.$this->basket->billing_phone.'</p>
							</div>
						</div>
					</div>
				</div>
				';
			}

			

			$ico = $this->basket->company_cid ? '<p><strong>'.$this->GD->text(564).'</strong>: '.$this->basket->company_cid.'</p>' : "";
			$dic = $this->basket->company_tin ? '<p><strong>'.$this->GD->text(565).'</strong>: '.$this->basket->company_tin.'</p>' : "";
			$icdph = $this->basket->company_tax ? '<p><strong>'.$this->GD->text(587).'</strong>: '.$this->basket->company_tax.'</p>' : "";

			if ( $ico || $dic || $icdph ) {
				$company = '
				<div class="ocha-company">
					'.$ico.'
					'.$dic.'
					'.$icdph.'
				</div>
				';
			}

			$billname = $this->basket->company_company ? $this->basket->company_company : $this->basket->billing_firstname.' '.$this->basket->billing_lastname;

			$billing .= '
			<div class="och-adress billAdress">
				<div class="ocha-content">
					<div class="ocha-head"><i class="ii iHome1" aria-hidden="true"></i>'.$this->GD->text(586).'</div>
					<div class="ocha-body">
						<p>'.$billname.'</p>
						<p>'.$this->basket->billing_street.'</p>
						<p>'.$this->basket->billing_zip.' '.$this->basket->billing_city.'</p>
						<p class="notimp">Slovenská republika</p>
						
						'.$company.'

						<div class="ocha-contact">
							<p><i class="ii iPhone" aria-hidden="true"></i>'.$this->basket->billing_phone.'</p>
							<p><i class="ii iEmail" aria-hidden="true"></i>'.$this->basket->billing_email.'</p>
						</div>
					</div>
				</div>
			</div>
			';

			return $billing.$delivery;
		}

		/* ORDER PAGE */

		public function basket_header($r='') {
			
			if ( $this->quantity == 1 )
				return sprintf($this->GD->text(523), '<strong>'.$this->quantity.'</strong>');
			else
				return sprintf($this->GD->text(512), '<strong>'.$this->quantity.'</strong>');
		}
/*
		public function send_mail_about_order($id) {
			//$to = MAIL_TO;
			$stmt = $this->m->q("SELECT * FROM orders WHERE system_id = ".$id);
			$data = $stmt->fetch_object();

			

			//$text = nl2br( $data["form-note"] );
			$orderDate = $this->GD->date_( $data->date, true, 2 );
			
			$delivery = $this->GD->verify_detype( $data->shipping );
				$deliveryText = $delivery->text ? '<tr><td style="font-size:12px;display:block;color:#666;padding-top:10px;">'.$this->GD->text($delivery->text).'</td></tr>' : "";

			$payment = $this->GD->verify_payment( $data->payment );
				$paymentText = $payment->text ? '<tr><td style="font-size:12px;display:block;color:#666;padding-top:10px;">'.$this->GD->text($payment->text).'</td></tr>' : "";

			$deliveryDate = $this->GD->delivery_date( $data->date );

			$adresses = $this->generate_sendorder_adress($data);
			$items = $this->generate_sendorder_items($data);
			

			if ( $data->payment == 2) {
				$bank = '
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:30px;background:#fafafa;">
					<tr>
						<td style="text-align:left;padding:10px 30px;font-weight:bold;text-align:center;border:solid 2px #666;color:#da3610;border-width: 2px 0;">'.$this->GD->text(619).'</td>
					</tr>
					<tr>
						<td style="text-align:left;padding:30px 20px;text-align:center;">'.$this->GD->text(618).'</td>
					</tr>
					<tr style="width:100%;">
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
								
								<tr>
									<td style="padding-top:5px;"><span style="text-align:left;font-size:14px;width:150px;display:inline-block;">'.$this->GD->text(621).':</span><span style="text-align:right;display:inline-block;font-weight:bold;">'.BANK_IBAN.'</span></td>
								</tr>
								
								<tr>
									<td style="padding-top:10px;"><span style="text-align:left;font-size:14px;;width:150px;display:inline-block;">'.$this->GD->text(620).':</span><span style="text-align:right;display:inline-block;font-weight:bold;">'.$this->GD->price($data->price_all).'</span></td>
								</tr>
								<tr>
									<td style="padding-top:10px;"><span style="text-align:left;font-size:14px;width:150px;display:inline-block;">'.$this->GD->text(624).':</span><span style="text-align:right;display:inline-block;font-weight:bold;">'.$data->system_id.'</span></td>
								</tr>
								<tr><td style="padding-top:10px;font-size:12px;">'.$this->GD->text(625).'<td><tr>
							</table>
						</td>
					</tr>
				</table>
				';
			} else
				$bank = "";
			
			$message = '
				<html>
					<head>
						<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
					</head>

					<style type="text/css">
						@media only screen and (max-width: 520px)  {
							.full { display:block; width:100%; margin: 10px 0; text-align: center; }
							.hide { display: none !important; }
						}
					</style>

					<body style="color:#333;font-size:16px;font-family:Arial;max-width:800px;margin:auto;padding:20px;" border="none">
						<div style="width:100%;">
							
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
								<tr style="width:100%;">
									<td>
										<table width="30%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
											<tr>
												<td style="">
													<a href="'.$this->GD->link(1).'" target="_blank" style="display:block;"><img src="'.$this->GD->suburl_data( "logo/mm4.png" ).'" style="margin:auto;display:block;width:150px;height:125px"></a>
												</td>
											</tr>
										</table>

										<table width="70%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;padding:20px;">
											<tr>
												<td style="padding-bottom:15px;margin:auto;font-size: 26px;line-height:30px;letter-spacing:-2px;font-weight:bold;color:#56a502">'.$this->GD->text(604).'</a></td>
											</tr>
											<tr>
												<td style="margin:auto;margin-top: 30px; font-size: 16px;">'.sprintf($this->GD->text(605), $data->billing_firstname, $data->system_id).'</td>
											</tr>
											<tr>
												<td style="padding-top:5px;margin-top:10px;font-size:14px;">'.$this->GD->text(606).'</td>
											</tr>
											
										</table>
									</td>
								</tr>
							</table>

							<div style="margin-top:20px;padding:20px 30px;border:solid 2px #eee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr style="width:100%;">
										<td>
											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(608).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$orderDate.'</td></tr>
											</table>

											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(611).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$this->GD->date_($deliveryDate, false).'</td></tr>
											</table>
										</td>
									</tr>
								</table>
							</div>

							'.$adresses.'

							<div style="margin-top:25px;padding:20px 30px;border:solid 2px #eee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:table;">
									<tr>
										<td>
											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(609).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$delivery->name.'</td></tr>
												'.$deliveryText.'
											</table>

											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(610).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$payment->name.'</td></tr>
												'.$paymentText.'
											</table>
										</td>
									</tr>
								</table>
							</div>

							'.$bank.'


							<div style="text-align:left;margin-top:50px;padding:15px 30px;font-weight:bold;text-align:center;border:solid 2px #eee;border-width:5px 0;">'.$this->GD->text(616).'<strong style="color:#666;margin-left:5px;">#'.$data->system_id.'</strong></div>

							'.$items.'

							<div style="margin-top:25px;padding:0 30px;background:#eee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:30px 0;">
									<tr style="width:100%;">
										<td>
											<table width="30%" border="0" cellspacing="0" cellpadding="0" align="left" class="full hide" style="display:table;">
												<tr>
													<td>&nbsp;</td>
												</tr>
											</table>

											<table width="70%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="padding:5px 0;"><span style="text-align:left;font-size:16px;color:#555;width:50%;display:inline-block;">'.$this->GD->text(517).'</span><span style="text-align:right;width:50%;display:inline-block;">'.$this->GD->price($data->price_items).'</span></td>
												<tr>
												<tr>
													<td style="padding:5px 0 20px 0;"><span style="text-align:left;font-size:16px;color:#555;width:50%;display:inline-block;">'.$this->GD->text(613).'</span><span style="text-align:right;width:50%;display:inline-block;">'.$this->GD->price($data->price_shipping).'</span></td>
												<tr>
												<tr>
													<td style="padding-top:20px;border-top:solid 1px #aaa;color:#56a502;letter-spacing:-1px;"><span style="text-align:left;font-size:18px;width:60%;display:inline-block;font-weight:bold;">'.$this->GD->text(614).'</span><span style="font-size:20px;text-align:right;width:40%;display:inline-block;font-weight:bold;">'.$this->GD->price($data->price_all).'</span></td>
												<tr>
											</table>
										</td>
									</tr>
								</table>
							</div>

							<div style="text-align:right;margin: 25px;">'.$this->GD->text(617).'</div>
						</div>
					</body>
				</html>
			';
			
			$subject = 'Objednávka - '.$data->system_id.' | MONAMADE';

			$headers  = "From: MonaMade <".HEADER_FROM.">\r\n";
			$headers .= "Reply-To: ".HEADER_FROM."\r\n";
			$headers .= "Return-Path: ".HEADER_FROM."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";

			$headers .= "X-Priority: 1 (Highest)\r\n";
			$headers .= "X-MSMail-Priority: High\r\n";
			$headers .= "Importance: High\r\n";
			//$headers .= "Mailed-By: gmail.com\r\n";
			
			//$r = mail($to, $subject, $message, $headers);
			$mail = LOCALHOST == true ? HEADER_ADMIN : $data->billing_email;

			$r = mail( $mail, $subject, $message, $headers );
				
			//for admin
			if ( LOCALHOST != true)
				mail( HEADER_ADMIN, $subject, $message, $headers );

			return $r;
		}

		public function generate_sendorder_adress($D, $value='') {
		
			$billing = $delivery = $company = "";

			if ( $D->delivery_firstname && $D->delivery_lastname && $D->delivery_phone && $D->delivery_phone && $D->delivery_street && $D->delivery_city && $D->delivery_zip ) {

				$billname = $D->delivery_firstname.' '.$D->delivery_lastname;

				$delivery .= '		
				<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
					<tr>
						<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(550).'</td>
					</tr>
					<tr><td style="padding-top:10px">'.$billname.'</td></tr>
					<tr><td style="padding-top:10px">'.$D->delivery_street.'</td></tr>
					<tr><td style="padding-top:10px">'.$D->delivery_zip.' '.$D->delivery_city.'</td></tr>
					<tr><td style="padding-top:10px">Slovenská republika</td></tr>

					<tr><td style="padding-top:10px">'.$D->delivery_phone.'</td></tr>
				</table>
				';
			} else {
				$billname = $D->company_company ? $D->company_company : $D->billing_firstname.' '.$D->billing_lastname;

				$delivery .= '
				<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
					<tr>
						<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(550).'</td>
					</tr>
					<tr><td style="padding-top:10px">'.$billname.'</td></tr>
					<tr><td style="padding-top:5px">'.$D->billing_street.'</td></tr>
					<tr><td style="padding-top:5px">'.$D->billing_zip.' '.$D->billing_city.'</td></tr>
					<tr><td style="padding-top:5px">Slovenská republika</td></tr>

					<tr><td style="padding-top:10px">'.$D->billing_phone.'</td></tr>
				</table>
				';
			}

			

			$ico = $D->company_cid ? '<tr><td><strong>'.$this->GD->text(564).'</strong>: '.$D->company_cid.'</td></tr>' : "";
			$dic = $D->company_tin ? '<tr><td><strong>'.$this->GD->text(565).'</strong>: '.$D->company_tin.'</td></tr>' : "";
			$icdph = $D->company_tax ? '<tr><td><strong>'.$this->GD->text(587).'</strong>: '.$D->company_tax.'</td></tr>' : "";

			if ( $ico || $dic || $icdph ) {
				$company = '
				<div style="">
					'.$ico.'
					'.$dic.'
					'.$icdph.'
				</div>
				';
			}

			$billname = $D->company_company ? $D->company_company : $D->billing_firstname.' '.$D->billing_lastname;

			$billing = '
			<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
				<tr>
					<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">'.$this->GD->text(586).'</td>
				</tr>
				<tr><td style="padding-top:10px;">'.$billname.'</td></tr>
				<tr><td style="padding-top:5px">'.$D->billing_street.'</td></tr>
				<tr><td style="padding-top:5px">'.$D->billing_zip.' '.$D->billing_city.'</td></tr>
				<tr><td style="padding-top:5px">Slovenská republika</td></tr>

				'.$company.'

				<tr><td style="padding-top:10px">'.$D->billing_phone.'</td></tr>
			</table>
			';

			return '
			<div style="margin-top:25px;padding:20px 30px;border:solid 2px #eee;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr style="width:100%;">
						<td>
							'.$billing.$delivery.'
						</td>
					</tr>
				</table>
			</div>
			';
		}


		public function generate_sendorder_items($content, $r = '') {
			
			$actions = $actionsH = "";

			if ( $ba = $this->GD->basket_data( $content ) ) {

				$num = count($ba);

				foreach ($ba as $key => $value) {
					
					$item = $this->GD->verify_itemid( $value["id"] );

					$imgLink = $this->GD->generate_pictureUrl( $item->file_list, true, $this->AJAX );
					$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
					$link_url = $this->GD->url( $item->url );
					$PRICE = $item->discount ? $this->GD->discount( $item->price, $item->discount) : $item->price;
					$PRICE_ALL = $PRICE * $value["quantity"];

					$discount = $item->discount ? '<tr><td style="padding-top:5px;"><span style="text-align:left;font-size:14px;color:#da3610;width:50%;display:inline-block;">'.$this->GD->text(506).'</span><span style="text-align:right;width:50%;display:inline-block;color:#da3610;">- '.$this->GD->price( ($item->price - $this->GD->discount( $item->price, $item->discount)) * $value["quantity"] ).'</span></td></tr>' : "&nbsp;";

					$bottoLine = $num > 1 ? "border-bottom: solid 1px #eee" : "";

					$r .= '
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:25px 0;'.$bottoLine.'">
						<tr style="width:100%;">
							<td>
								<table width="30%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
									<tr>
										<td style="font-size:14px;letter-spacing:-0.5px;color:#56a502;font-weight:bold;">
											<a href="'.$link_url.'" style="display:block;width:125px;height:125px;overflow:hidden;margin:auto;border:solid 3px #fff;border-radius:50%;" target="_blank" title="'.$link_url.'"><img src="'.$imgLink["url"].'" style="width:100%;" alt=""></a>
										</td>
									</tr>
								</table>

								<table width="70%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
									<tr>
										<td style="font-size:20px;"><a href="'.$link_url.'" style="display:block;text-decoration:none;color:#333;" target="_blank" title="'.$link_url.'">'.$this->GD->mb_ucfirst($item->title).'</a></td>
									</tr>
									<tr>
										<td style="padding-top:10px;"><span style="text-align:left;font-size:14px;color:#aaa;width:50%;display:inline-block;">'.$this->GD->text(514).':</span><span style="text-align:right;width:50%;display:inline-block;">'.$this->GD->price( $item->price ).'</span></td>
									</tr>
									<tr>
										<td style="padding-top:5px;"><span style="text-align:left;font-size:14px;color:#aaa;width:50%;display:inline-block;">'.$this->GD->text(515).':</span><span style="text-align:right;width:50%;display:inline-block;">'.$value["quantity"].' ks</span></td>
									</tr>
									'.$discount.'
									<tr>
										<td style="padding-top:5px;"><span style="text-align:left;font-size:14px;color:#aaa;width:50%;display:inline-block;">'.$this->GD->text(516).':</span><span style="text-align:right;width:50%;display:inline-block;font-weight:bold;">'.$this->GD->price($PRICE_ALL).'</span></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					';
				}
			}

			return '
			<div style="padding: 25px 30px 0 30px;background:#fcfcfc;">
				'.$r.'
			</div>
			';
		}*/

	}

