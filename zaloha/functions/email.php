<?php
	$MAIL = new EMAIL();
	//require_once ("./functions/inputs.php");

	class EMAIL {
		
		protected static $instance;
		protected $mysql;
		protected $GD;
		protected $CHE;

		protected $type;
		protected $data;

		public $AJAX;



		public function __construct($type = "") {
			$this->mysql = SQL::init();
			$this->GD = GLOBALDATA::init();
			
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new EMAIL();
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


		public function email_type($type, $order, $r = '') {
			switch ($type) {
				case 1:	//prijata objednavka
					$this->send_mail_to_owner($order);
					$r = $this->send_mail_about_order($order);
					break;
				
				case 2:	//odoslaná objednavka
					# code...
					break;
			}

			return $r;
		}

		public function default_subjects() {
			$h  = "From: MONAMADE <".HEADER_FROM.">\r\n";
			$h .= "Reply-To: ".HEADER_FROM."\r\n";
			$h .= "Return-Path: ".HEADER_FROM."\r\n";
			$h .= "MIME-Version: 1.0\r\n";
			$h .= "Content-Type: text/html; charset=utf-8\r\n";

			$h .= "X-Priority: 1 (Highest)\r\n";
			$h .= "X-MSMail-Priority: High\r\n";
			$h .= "Importance: High\r\n";

			return $h;
		}

		/* ORDER PAGE */

		public function send_mail_about_order($id) {
			//$to = MAIL_TO;
			
			$stmt = $this->GD->m->q("SELECT * FROM orders WHERE system_id = '".$id."'");
			$data = $stmt->fetch_object();

			$subject = 'Objednávka - '.$data->system_id.' [PRIJATÁ] | MONAMADE';






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
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px;padding:30px 20px;background:#fcfcfc;">
					<tr>
						<td style="text-align:left;padding:10px 30px;font-weight:bold;text-align:center;border:solid 2px #666;color:#da3610;border-width: 2px 0;">'.$this->GD->text(619).'</td>
					</tr>
					<tr>
						<td style="text-align:left;padding:20px;text-align:center;font-size:14px;">'.$this->GD->text(618).'</td>
					</tr>
					<tr style="width:100%;">
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
								
								<tr>
									<td style="padding-top:5px;"><span style="text-align:left;font-size:12px;display:inline-block;">'.$this->GD->text(621).'</span><span style="margin-left:5px;text-align:right;display:inline;font-weight:bold;font-size:14px;">'.BANK_IBAN.'</span></td>
								</tr>
								
								<tr>
									<td style="padding-top:10px;"><span style="text-align:left;font-size:12px;display:inline-block;">'.$this->GD->text(620).':</span><span style="margin-left:5px;text-align:right;display:inline;font-weight:bold;font-size:14px;">'.$this->GD->price($data->price_all).'</span></td>
								</tr>
								<tr>
									<td style="padding-top:10px;"><span style="text-align:left;font-size:12px;display:inline-block;">'.$this->GD->text(624).'</span><span style="margin-left:5px;text-align:right;display:inline;font-weight:bold;font-size:14px;">'.$data->system_id.'</span></td>
								</tr>
								<tr><td style="padding-top:20px;font-size:12px;">'.$this->GD->text(625).'<td><tr>
							</table>
						</td>
					</tr>
				</table>
				';
			} else
				$bank = "";
			
			$main = '
				<!DOCTYPE html>
				<html>
					<head>
						<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
					</head>

					<style type="text/css">
						@media only screen and (max-width: 520px)  {
							.full { display: block; width:100%; margin: 10px 0; text-align: center; }
							.hide { display: none !important; }
						}
						a { text-decoration:none; color: #444; }
					</style>

					<body style="color:#333;font-size:16px;font-family:Arial;max-width:600px;margin:auto;padding:30px 10px;" border="none">

						<div style="width:100%;padding:0;">
							
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:25px 0;">
								<tr style="width:100%;">
									<td>
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="full" style="display:table;padding: 0 30px;">
											
											<tr>
												<td style="padding-bottom:30px;margin:auto;font-size: 16px;line-height:20px;font-weight:bold;text-align:center;color:#56a502">'.$this->GD->text(604).'</a><div style="width:50px;height:2px; background:#eee;margin:10px auto 0 auto;"></div></td>
											</tr>
											<tr>
												<td style="margin:auto;font-size:18px;">'.sprintf($this->GD->text(829), $data->billing_firstname).'</td>
											</tr>
											<tr>
												<td style="margin:auto;padding-top:10px;font-size: 16px;">'.sprintf($this->GD->text(605), $data->system_id, '<a href="http://monamade.sk" style="text-decoration:underline;">monamade.sk</a>').'</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>

							<div style="padding:15px 30px;border:solid 2px #eee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr style="width:100%;">
										<td>
											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(608), "UTF8")).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$orderDate.'</td></tr>
											</table>

											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(611), "UTF8")).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$this->GD->date_($deliveryDate, false).'</td></tr>
											</table>
										</td>
									</tr>
								</table>
							</div>

							'.$adresses.'

							<div style="margin-top:20px;padding:15px 30px;border:solid 2px #eee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:table;">
									<tr>
										<td>
											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(609), "UTF8")).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$delivery->name.'</td></tr>
												'.$deliveryText.'
											</table>

											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(610), "UTF8")).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$payment->name.'</td></tr>
												'.$paymentText.'
											</table>
										</td>
									</tr>
								</table>
							</div>

							'.$bank.'

							<div style="text-align:left;margin:40px 0 2px 0;padding:15px 0;font-weight:bold;text-align:center;border:solid 2px #eee;border-width:2px 0;font-size:14px;">'.$this->GD->text(616).'<strong style="color:#56a502;margin-left:5px;">#'.$data->system_id.'</strong></div>

							'.$items.'

							<div style="padding:0 30px;background:#f1feee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:20px 0;">
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
													<td style="padding:5px 0 10px 0;"><span style="text-align:left;font-size:16px;color:#555;width:50%;display:inline-block;">'.$this->GD->text(613).'</span><span style="text-align:right;width:50%;display:inline-block;">'.$this->GD->price($data->price_shipping).'</span></td>
												<tr>
												<tr>
													<td style="padding-top:10px;border-top:solid 1px #ccc;color:#56a502;letter-spacing:-1px;"><span style="text-align:left;font-size:18px;width:60%;display:inline-block;font-weight:bold;">'.$this->GD->text(814).'</span><span style="font-size:20px;text-align:right;width:40%;display:inline-block;font-weight:bold;">'.$this->GD->price($data->price_all).'</span></td>
												<tr>
											</table>
										</td>
									</tr>
								</table>
							</div>


							<div style="padding:30px;">
								<div style="margin:0 0 10px 0;font-size:12px;font-weight:bold;">'.$this->GD->text(812).' <a href="'.$this->GD->link(1).'">monamade.sk</a></div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr style="width:100%;">
										<td>
											<table width="40%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
												<tr><td style="padding-top:0;">'.VOP_NAME.'</td></tr>
												<tr><td style="padding-top:3px">'.VOP_STREET.'</td></tr>
												<tr><td style="padding-top:3px">'.VOP_ZIP.' '.VOP_CITY.'</td></tr>
											</table>

											<table width="60%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
												<tr><td style="padding-top:3px">'.$this->GD->text(564).': '.str_replace(" ", "", VOP_ICO).'</td></tr>
												<tr><td style="padding-top:3px">'.$this->GD->text(565).': '.str_replace(" ", "", VOP_DIC).'</td></tr>
												<tr><td style="padding-top:3px;font-size:12px;">'.$this->GD->text(777).'</td></tr>
											</table>
										</td>
									</tr>
								</table>
							</div>

							<div style="text-align:center;padding-top:20px;font-size:12px;border-top:solid 2px #eee;">'.$this->GD->text(813).'</div>
							<a href="'.$this->GD->link(1).'" target="_blank" style="display:block;opacity:.7;"><img src="'.$this->GD->suburl_data( "logo/mm.png" ).'" style="margin:60px auto 0 auto;display:block;width:108px;height:30px;"></a>

							<div style="margin-top:20px;text-align:center;padding:10px 0;font-size:12px;">'.VOP_MAIL.'<span style="margin:0 10px;">|</span>'.VOP_PHONE.'</div>
							<div style="text-align:center;padding:10px 0;font-size:12px;"><a href="'.$this->GD->link(1).'" style="color:#444;">WEB</a><span style="margin:0 10px;">|</span><a href="'.SOCIAL_FCB.'" style="color:#3b5998;">FACEBOOK</a><span style="margin:0 10px;">|</span><a href="'.SOCIAL_INSTA.'" style="color:#8A3AB9;">INSTAGRAM</a></div>
						</div>
					</body>
				</html>
			';

			$mail = LOCALHOST == true ? HEADER_ADMIN : $data->billing_email;



			
			$files = array(
				0 => array('file' => VOP_FILE, 'type' => 'PDF'),
				1 => array('file' => VOP_STORNO, 'type' => 'PDF')
			);

			$path 			= PHYS_ADRESS_TO_FILES_SWITCH;
			
			$uid 			= md5(uniqid(time()));
			$eol 			= PHP_EOL;

			$headers 		= $this->default_subjects_with_file($uid);



			$message 		= "--" . $uid . "\n";
			$message 		.= "Content-Type: text/html; charset=UTF-8\n";
			$message 		.= "Content-Disposition: inline\n";
			$message 		.= "Content-Description: HTML text\n";

			$message 		.= $main . "\n\n";


			foreach ($files as $key => $v) {
				$file      	= $path . $v['file'];
	
				//$file_size = filesize($file);
				$handle    	= fopen($file, "r");
				$content   	= fread($handle, filesize($file) );

				fclose($handle);

				$content 	= chunk_split(base64_encode($content));

				$message 	.= "--" . $uid . "\n";
				$message 	.= "Content-type: application/".$v['type']."; name=\"" . $v['file'] . "\"\n"; // use different content types here
				$message 	.= "Content-Transfer-Encoding: base64\n";
				$message 	.= "Content-Disposition: attachment; FileName=\"" . $v['file'] . "\"\n";
				$message 	.= "Content-Description: Attached file: " . $v['file'] . "\"\n\n";

				$message 	.= $content . "\n";
			}

			$message 		.= "--" . $uid . "--";

			$r = mail( $mail, $subject, $message, $headers );
				
			//for admin
			if ( LOCALHOST == true )
				mail( HEADER_ADMIN, $subject, $message, $headers );

			return $message;
		}

		public function default_subjects_with_file($boundary) {
			$h  = "From: MONAMADE <".HEADER_FROM.">\r\n";
			$h .= "Reply-To: ".HEADER_FROM."\r\n";
			$h .= "Return-Path: ".HEADER_FROM."\r\n";
			$h .= "MIME-Version: 1.0\r\n";
			$h .= "Content-type: Multipart/mixed; boundary=\"" . $boundary . "\"\n";
			$h .= "Content-Description: Multipart message\n";

			$h .= "X-Priority: 1 (Highest)\r\n";
			$h .= "X-MSMail-Priority: High\r\n";
			$h .= "Importance: High\r\n";

			return $h;
		}

		/*
		/*$filename  = "odstupenie-od-zmluvy-monamade.pdf";
			$filename2  = "odstupenie-od-zmluvy-monamade.pdf";
			
			$path      = PHYS_ADRESS_TO_FILES;
			
			$file      = $path . $filename;
			$file2      = $path . $filename2;

			$file_size = filesize($file);
			$file_size2 = filesize($file2);

			$handle    = fopen($file, "r");
			$handle2    = fopen($file2, "r");

			$content   = fread($handle, $file_size);
			$content2   = fread($handle2, $file_size2);

			fclose($handle);
			fclose($handle2);

			$content = chunk_split(base64_encode($content));
			$content2 = chunk_split(base64_encode($content2));

			$uid     = md5(uniqid(time()));
			$name    = basename($file);
			$name2    = basename($file2);

			$eol     = PHP_EOL;

			$headers = $this->default_subjects_with_file($uid);

			$message = "--" . $uid . "\n";
			$message .= "Content-Type: text/html; charset=UTF-8\n";
			$message .= "Content-Disposition: inline\n";
			$message .= "Content-Description: HTML text\n";

			$message .= $main . "\n\n";

			$message .= "--" . $uid . "\n";
			$message .= "Content-type: application/PDF; name=\"" . $filename . "\"\n"; // use different content types here
			$message .= "Content-Transfer-Encoding: base64\n";
			$message .= "Content-Disposition: attachment; FileName=\"" . $filename . "\"\n";
			$message .= "Content-Description: Attached file: " . $filename . "\"\n\n";

			$message .= $content . "\n";

			$message .= "--" . $uid . "\n";
			$message .= "Content-type: application/PDF; name=\"" . $filename2 . "\"\n"; // use different content types here
			$message .= "Content-Transfer-Encoding: base64\n";
			$message .= "Content-Disposition: attachment; FileName=\"" . $filename2 . "\"\n";
			$message .= "Content-Description: Attached file: " . $filename2 . "\"\n\n";

			
			
			$message .= $content2 . "\n";

			$message .= "--" . $uid . "--";*/






		public function send_mail_to_owner($id) {

			$stmt = $this->GD->m->q("SELECT * FROM orders WHERE system_id = '".$id."'");
			$data = $stmt->fetch_object();

			$subject = 'NOVÁ OBJEDNÁVKA - '.$data->system_id.' | MONAMADE';



			//$text = nl2br( $data["form-note"] );
			$orderDate = $this->GD->date_( $data->date, true, 2 );
			
			$deliveryDate = $this->GD->delivery_date( $data->date );

			$adresses = $this->generate_sendorder_adress_delivery($data);
				
			$message = '
				<html>
					<head>
						<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
					</head>

					<style type="text/css">
						@media only screen and (max-width: 520px)  {
							.full { display: block; width:100%; margin: 10px 0; text-align: center; }
							.hide { display: none !important; }
						}
						a { text-decoration:none; color: #444; }
					</style>

					<body style="color:#333;font-size:16px;font-family:Arial;max-width:600px;margin:auto;padding:30px 10px;" border="none">

						<div style="width:100%;padding:0;">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:25px 0;">
								<tr style="width:100%;">
									<td>
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="full" style="display:table;padding: 0 30px;">
											<tr>
												<td style="padding-bottom:30px;margin:auto;font-size: 16px;line-height:20px;font-weight:bold;text-align:center;color:#56a502">'.$this->GD->text(834).'</a><div style="width:50px;height:2px; background:#eee;margin:10px auto 0 auto;"></div></td>

											</tr>
											<tr>
												<td style="margin:auto;padding-top:10px;font-size: 16px;">'.$this->GD->text(836).'</td>
											</tr>
											<tr>
												<td style="padding-top:10px;font-size:12px;">'.sprintf($this->GD->text(837), '<a href="'.$this->GD->suburl_cms( "objednavka/".$data->system_id ).'" style="text-decoration:underline;">'.$this->GD->text(838).'</a>').'</td>
											</tr>
											
										</table>
									</td>
								</tr>
							</table>

							<div style="padding:15px 30px;border:solid 2px #eee;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr style="width:100%;">
										<td>
											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;color:#56a502;">'.$this->GD->text(761).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$data->system_id.' <a href="'.$this->GD->suburl_cms( "objednavka/".$data->system_id ).'" style="text-decoration:underline;font-size:12px;margin-left: 10px;">'.$this->GD->text(835).'</a></td></tr>
											</table>

											<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
												<tr>
													<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(608), "UTF8")).'</td>
												</tr>
												<tr><td style="padding-top:10px;">'.$orderDate.'</td></tr>
											</table>
										</td>
									</tr>
								</table>
							</div>

							'.$adresses.'

							<div style="text-align:center;padding:40px 0 20px 0;font-size:12px;"><a href="'.$this->GD->link(1).'" style="color:#444;">WEB</a><span style="margin:0 10px;">|</span><a href="'.SOCIAL_FCB.'" style="color:#3b5998;">FACEBOOK</a><span style="margin:0 10px;">|</span><a href="'.SOCIAL_INSTA.'" style="color:#8A3AB9;">INSTAGRAM</a></div>
						</div>
					</body>
				</html>
			';
			
			$headers  = $this->default_subjects();
			//$headers .= "Mailed-By: gmail.com\r\n";
			
			mail( HEADER_ADMIN, $subject, $message, $headers );

			return $message;
		}

		public function generate_sendorder_adress($D, $value='') {
		
			$billing = $delivery = $company = "";

			if ( $D->delivery_firstname && $D->delivery_lastname && $D->delivery_phone && $D->delivery_phone && $D->delivery_street && $D->delivery_city && $D->delivery_zip ) {

				$billname = $D->delivery_firstname.' '.$D->delivery_lastname;

				$delivery .= '		
				<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
					<tr>
						<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(550), "UTF8")).'</td>
					</tr>
					<tr><td style="padding-top:10px">'.$billname.'</td></tr>
					<tr><td style="padding-top:5px">'.$D->delivery_street.'</td></tr>
					<tr><td style="padding-top:5px">'.$D->delivery_zip.' '.$D->delivery_city.'</td></tr>
					<tr><td style="padding-top:5px">Slovenská republika</td></tr>

					<tr><td style="padding-top:10px">'.$D->delivery_phone.'</td></tr>
				</table>
				';
			} else {
				$billname = $D->company_company ? $D->company_company : $D->billing_firstname.' '.$D->billing_lastname;

				$delivery .= '
				<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
					<tr>
						<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(550), "UTF8")).'</td>
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
			<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
				<tr>
					<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(586), "UTF8")).'</td>
				</tr>
				<tr><td style="padding-top:10px;">'.$billname.'</td></tr>
				<tr><td style="padding-top:5px">'.$D->billing_street.'</td></tr>
				<tr><td style="padding-top:5px">'.$D->billing_zip.' '.$D->billing_city.'</td></tr>
				<tr><td style="padding-top:5px">Slovenská republika</td></tr>

				'.$company.'

				<tr><td style="padding-top:10px;">'.$D->billing_phone.'</td></tr>
			</table>
			';

			return '
			<div style="margin-top:20px;padding:20px 30px;border:solid 2px #eee;">
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

		public function generate_sendorder_adress_delivery($D, $value='') {
		
			$billing = $delivery = $company = "";

			if ( $D->delivery_firstname && $D->delivery_lastname && $D->delivery_phone && $D->delivery_phone && $D->delivery_street && $D->delivery_city && $D->delivery_zip ) {

				$billname = $D->delivery_firstname.' '.$D->delivery_lastname;

				$delivery .= '		
				<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
					<tr>
						<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(550), "UTF8")).'</td>
					</tr>
					<tr><td style="padding-top:10px">'.$billname.'</td></tr>
					<tr><td style="padding-top:5px">'.$D->delivery_street.'</td></tr>
					<tr><td style="padding-top:5px">'.$D->delivery_zip.' '.$D->delivery_city.'</td></tr>
					<tr><td style="padding-top:5px">Slovenská republika</td></tr>

					<tr><td style="padding-top:10px">'.$D->delivery_phone.'</td></tr>
				</table>
				';
			} else {
				$billname = $D->company_company ? $D->company_company : $D->billing_firstname.' '.$D->billing_lastname;

				$delivery .= '
				<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;font-size:14px;">
					<tr>
						<td style="font-size:14px;color:#56a502;">'.ucfirst(mb_strtolower($this->GD->text(550), "UTF8")).'</td>
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

			return '
			<div style="margin-top:20px;padding:20px 30px;border:solid 2px #eee;">
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

				$num = count($ba['d']);
				
				foreach ( $ba['d'] as $key => $value ) {
					
					$item = $this->GD->verify_itemid( $value["id"] );

					$imgLink = $this->GD->generate_pictureUrl( $item->file_list, true, $this->AJAX );
					$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );
					$link_url = $this->GD->url( $item->url );
					//$PRICE = $item->discount ? $this->GD->discount( $value["prie"], $item->discount) : $item->price;
					$PRICE = $value["price"];
					$PRICE_ALL = $PRICE * $value["quantity"];

					/*$discount = $item->discount ? '<tr><td style="padding-top:5px;"><span style="text-align:left;font-size:14px;color:#da3610;width:50%;display:inline-block;">'.$this->GD->text(506).'</span><span style="text-align:right;width:50%;display:inline-block;color:#da3610;">- '.$this->GD->price( ($item->price - $this->GD->discount( $item->price, $item->discount)) * $value["quantity"] ).'</span></td></tr>' : "&nbsp;";*/

					$bottoLine = $num > 1 ? "border-bottom: solid 1px #eee" : "";

					$r .= '
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:15px 0;'.$bottoLine.'">
						<tr style="width:100%;">
							<td>
								<table width="30%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
									<tr>
										<td style="font-size:14px;color:#56a502;">
											<a href="'.$link_url.'" style="display:block;width:80px;height:80px;overflow:hidden;margin:auto;border:solid 2px #fff;border-radius:50%;" target="_blank" title="'.$link_url.'"><img src="'.$imgLink["url"].'" style="width:150%;" alt=""></a>
										</td>
									</tr>
								</table>

								<table width="70%" border="0" cellspacing="0" cellpadding="0" align="left" class="full" style="display:table;">
									<tr>
										<td style="font-size:18px;"><a href="'.$link_url.'" style="display:block;text-decoration:none;color:#333;" target="_blank" title="'.$link_url.'">'.$this->GD->mb_ucfirst($item->title).'</a></td>
									</tr>
									<tr>
										<td style="padding-top:4px;"><span style="text-align:left;font-size:12px;color:#aaa;width:50%;display:inline-block;">'.$this->GD->text(514).':</span><span style="text-align:right;width:50%;display:inline-block;">'.$this->GD->price( $value["price"] ).'</span></td>
									</tr>
									<tr>
										<td style="padding-top:2px;"><span style="text-align:left;font-size:12px;color:#aaa;width:50%;display:inline-block;">'.$this->GD->text(515).':</span><span style="text-align:right;width:50%;display:inline-block;">'.$value["quantity"].' ks</span></td>
									</tr>
									
									<tr>
										<td style="padding-top:2px;"><span style="text-align:left;font-size:12px;color:#aaa;width:50%;display:inline-block;">'.$this->GD->text(516).':</span><span style="text-align:right;width:50%;display:inline-block;font-weight:bold;">'.$this->GD->price($PRICE_ALL).'</span></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					';
				}
			}

			return '
			<div style="padding: 10px 30px 0 30px;background:#f1feee;">
				'.$r.'
			</div>
			';
		}

	}

