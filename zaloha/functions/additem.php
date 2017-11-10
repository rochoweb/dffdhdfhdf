<?php
	$EDITOR = new DIYEDITOR();

	class DIYEDITOR {
		
		public static $instance;

		protected $m;
		protected $CHR;
		protected $GD;
		
		//private $user;
		private $diyID;
		private $userID;
		
		private $FM;
		public $table;
		public $identificator;
		public $titleVerify;
		
		public function __construct($AJAX = false) {
			$this->m = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			$this->INP = INPUTS::init();

			$this->FM = FILEMANAGER::init();

			if ( $AJAX == true ) {
				if ( isset($_COOKIE["DIY"]) ) $this->diyID = $_COOKIE["DIY"];
				$this->ajax = true;
			}
			else {
				if ( isset($_GET["id"])) $this->diyID = $this->CHR->ITEM_ID($_GET["id"]);
				$this->ajax = false;
			}

			$this->table = $this->product_location();

			if ( $this->table == "navody" ) {
				//$this->identificator = "url";
				$this->identificator = "system_id";
				$this->titleVerify = "url";

				$this->ITEM = $this->CHR->ITEM_INFO($this->table, $this->identificator, $this->diyID);
			} else {
				$this->identificator = "system_id";
				$this->titleVerify = "system_id";
			}

			$this->diyNAME = $this->item_name();


			$this->check_filedata();


			$this->inputArray = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new DIYEDITOR();
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


		public function item_name($r='') {
			$stmt = $this->m->q("SELECT title FROM $this->table WHERE $this->identificator = '$this->diyID' ");

			if ( $r = $stmt->fetch_assoc() ) return $r["title"];
		}

		public function action($type, $data, $return = "") {




			if ( $this->GD->is_logged() == true ) {
				if ( $this->GD->is_inactive() == false ) {

					/*$USER = $this->user;
					$this->GD->U->userID = $USER["userID"];*/



					switch ($type) {

						case 'diyfiles':
							$return = array("html" 	=> $this->generate_diy_files(), "buttons" 	=> $this->generate_diy_buttons() );
							break;

						case 'diyfiles_header':
							$return = array("html" 	=> $this->generate_diy_header(), "buttons" 	=> $this->generate_diy_buttons() );
							break;


						case 'additem-title':
						case 'additem-description':

						case 'additem-size_lenght':
						case 'additem-size_width':
						case 'additem-size_height':
						case 'additem-size_average':
						case 'additem-size_mass':

						case 'additem-price':
						case 'additem-discount':
						case 'additem-availability':
						case 'additem-quantity':
						case 'additem-public':
						case 'additem-category':

						case 'additem-fcbmessage':
							//$INP = new INPUTS($this->diyID);

							//if ( $type == "additem-public" )
								

							$typee = explode("-", $type);

							if ( count($typee) == 2 ) {

								$check = $this->INP->action($typee[1], $data);
								$input = $this->GD->inputData($type);



								$icon = $check ? '<i class="'.$this->input_icon(false).'" aria-hidden="true"></i>' : '<i class="'.$this->input_icon(true).'" aria-hidden="true"></i>';

								if ( $check ) {
									
									if ( is_array($check) )
										$return = $check;
									else {
										$return = array( 
										"icon" 	=> $icon,
										"result"	=> '<span class="staBAD"><label for="'.$input->idd.'">'.$check.'</label></span>'
										);
									}
								} else {
									$return = array( 
									"icon" 	=> $icon,
									"result"	=> $this->GD->show_status(1, $this->GD->text($input->text) ) );
								}

								$this->update_date();
							}
							
							$errors = $this->check_all_before_publish();


							if ( $type == 'additem-public') {

								
								if ( $errors ) {
									if ( $this->ITEM->quantity == 0 )
										$return["errors"] = '<span class="staBAD"><label for="'.$type.'">(!) '.$this->GD->text(371).'</label></span>';
									else
										$return["errors"] = $errors["error"];
								} else
									$return["errors"] = '';

								//if ( $this->ITEM->public )
								//$return["buttons"] = $this->generate_diy_buttons();
							}
							
							$return["buttons"] = $this->generate_diy_buttons();
							//return $this->check_all_before_publish( $this->inputArray );
							break;

						case 'remove-photo':
							$this->FM->pic_remove( $data );

							//$this->FM->update_filedata_after_upload( $this->GD->U->userID, "" );
							$this->FM->update_concept_files( $this->GD->U->userID, $this->diyID, "" );

							$return = array("html" 	=> $this->generate_diy_header(), "buttons" 	=> $this->generate_diy_buttons() );
							break;


						case 'additem-tags':
							$input = $this->GD->inputData($type);

							if ( $data ) {

								if ( $tagver = $this->verify_tags($data) ) {

									if ( $tagver == "none" )
										$this->update_tags( "" );
									else
										$this->update_tags( $tagver );
								}
							} else {
								$this->update_tags( "" );
							}
							
							$return = array('r' => $this->verify_tags($data), 'result'	=> $this->GD->show_status(1, $this->GD->text($input->text) ), "buttons" => $this->generate_diy_buttons()  );

							$errors = $this->check_all_before_publish();

							if ( $errors ) 
								$return["errors"] = $errors["error"];
							break;

						case 'additem-colors':
							$input = $this->GD->inputData($type);

							if ( $data ) {

								if ( $tagver = $this->verify_colors($data) ) {

									if ( $tagver == "none" )
										$this->update_colors( "" );
									else
										$this->update_colors( $tagver );
								}
							} else {
								$this->update_colors( "" );
							}
							
							

							$return = array('r' => $this->verify_colors($data), 'result'	=> $this->GD->show_status(1, $this->GD->text($input->text) ), "buttons"	=> $this->generate_diy_buttons() );



							$errors = $this->check_all_before_publish();

							if ( $errors ) 
								$return["errors"] = $errors["error"];
							break;

						case 'additem-categories':
						case 'additem-tagkeywords':
						case 'additem-colorkeywords':

							if ( $type == "additem-tagkeywords" ) {
								if ( $data != "ALL" ) {
									$this->data = $this->GD->create_DATA($data["tags"], false);
									$data = $this->data;
								}
								
								$function = "find_data";
								$function2 = "create_results";
							} else if ( $type  == "additem-colorkeywords") {
								if ( $data != "ALL" ) {
									$this->data = $this->GD->create_DATA($data["colors"], false);
									$data = $this->data;
								}
								
								$function = "find_data_colors";
								$function2 = "create_results2";
							} else if ( $type  == "additem-categories") {
								if ( $data != "ALL" ) {
									$this->data = $this->GD->create_DATA($data["category"], false);
									$data = $this->data;
								}
								
								$function = "find_data_category";
								$function2 = "create_results_category";
							}

							if ( strlen($data) > 1 || $data == "ALL") {

								if ( $keys = $this->create_keywords($data) ) {
									
									if ( $data == "ALL" )
										$this->keywords = "ALL";
									else
										$this->keywords = $keys;

									$this->columns = array("sk", "en");
									
									if ( !$search = $this->$function() )
										$return = array('r' => '<div class="tac">'.$this->GD->text(245).'</div>');
									else {
										$results = $this->$function2($search, $keys);

										$return = array('r' => $results);
									}
								}
							}
							else {
								$return = array('r' => '');
							}

							return $return;
						
							break;

						case 'additem-continue':
							$check = $this->check_all_before_publish();

							if ( $this->table == "concepts" && $check == false )
								$return = $this->transfer();
							else
								$return = $check;
							break;

						case "savefiles":
							$return = $this->update_filelist( $this->get_picture_id_from_filename( $data ) );

							$return = array('result'	=> $this->GD->show_status(1, $this->GD->text(353) ), "buttons" 	=> $this->generate_diy_buttons() );
							break;

						case "savecategory":
							$save = '';
							
							foreach ($data as $key => $value) {
								$save .= '#'.$value;
							}

							if ( $save )
								$this->m->q("UPDATE settings SET category = '$save' WHERE id = 1");

							$return = array('result'	=> $this->GD->show_status(1, $this->GD->text(364) ) );
							break;


						case "sharefacebook":

							if ( $this->table == "navody" && $this->check_all_before_publish() == false ) {

								if ( $this->ITEM->public == 1 ) {

									//$this->share_item( $this->ITEM );

									if ( $share = $this->share_as_link( $this->ITEM ) ) {
										$return = array("r" 	=> $this->generate_post_links() );
									}
								}
								else
									$return = array("error" 	=> "not public" );

							} else {
								$return = array("error" 	=> "can't share" );
							}
							break;

						case "deletepost":

							if ( $this->delete_link( $data ) )
								$return = array("r" 	=> $this->generate_post_links() );

							break;

						case "addcategory":
						case "addtag":

							$dd = array(
								'addcategory' => array('select' => 'category', 'from' => 'kategorie', 'where' => 'category, text, sk', 'error' => 357, 'ok' => 358),
								'addtag' => array('select' => 'id', 'from' => 'tagy', 'where' => 'id, id_text, sk', 'error' => 361, 'ok' => 362)
							);
							
							if ( $check = $this->INP->action($type, $data) ) {
								$re = sprintf($check, $type);
								//$input = $this->GD->inputData($type);
							} else {
								$name = $this->GD->text_transform($data[$type]);

								$stmt = $this->m->q("SELECT ".$dd[$type]["select"]." FROM ".$dd[$type]["from"]." WHERE ".$this->CHR->LANG." LIKE '".mb_strtolower($data[$type], 'UTF8')."' OR ".$this->CHR->LANG." LIKE '".mb_strtolower($name, 'UTF8')."'");

								if ( $re = $stmt->fetch_object() )
									$re = $this->GD->show_result(0, sprintf($this->GD->text($dd[$type]["error"]), '<strong>'.$data[$type].'</strong>') );
								else {
									$stmt2 = $this->m->q("INSERT INTO strings_added (string, sk) VALUES (NULL, '".$this->GD->mb_ucfirst($data[$type])."')");

									if ( $textid = mysqli_insert_id($this->m->conn) ) {
										if ( $this->m->q("INSERT INTO ".$dd[$type]["from"]." (".$dd[$type]["where"].") VALUES (NULL, $textid, '".$name."')") )


											if ( $type == 'addcategory' ) {
												$newid = mysqli_insert_id($this->m->conn);

												$stmt = $this->m->q("SELECT category FROM settings WHERE id = 1");

												if ( $sett = $stmt->fetch_object() ) {

													$this->m->q("UPDATE settings SET category = '".$sett->category.'#'.$newid."'");
												}
											}
											

											$re = $this->GD->show_result(1, sprintf($this->GD->text($dd[$type]["ok"]), '<strong>'.$data[$type].'</strong>') );
									}
								}
							}

							$return = array("re" 	=> $re );

							break;

						/*case "addtag":
							
							if ( $check = $this->INP->action($type, $data) ) {
								$re = sprintf($check, $type);
								//$input = $this->GD->inputData($type);
							} else {
								$name = $this->GD->text_transform($data[$type]);

								$stmt = $this->m->q("SELECT id FROM tagy WHERE ".$this->CHR->LANG." LIKE '".mb_strtolower($data[$type], 'UTF8')."' OR ".$this->CHR->LANG." LIKE '".mb_strtolower($name, 'UTF8')."'");

								if ( $re = $stmt->fetch_object() )
									$re = $this->GD->show_result(0, sprintf($this->GD->text(361), $data[$type]) );
								else {
									$stmt2 = $this->m->q("INSERT INTO strings (string, sk) VALUES (NULL, '".$this->GD->mb_ucfirst($data[$type])."')");

									if ( $textid = mysqli_insert_id($this->m->conn) ) {
										if ( $this->m->q("INSERT INTO tagy (id, id_text, sk) VALUES (NULL, $textid, '".$name."')") )
											$re = $this->GD->show_result(1, sprintf($this->GD->text(362), $data[$type]) );
									}
								}
							}

							$return = array("re" 	=> $re );

							break;*/
					}

				} 
				else {
					$return = array("offline" 	=> $this->GD->logout_message(1) );
					$this->logout();
				}
			} else {
				$return = array("offline" 	=> $this->GD->logout_message(1) );
				$this->logout();
			}

			
			return $return;
		}


		public function fbaccess($r = '') {
			require_once '../fcb/Facebook/autoload.php';

			$fb = new Facebook\Facebook(array(  
				'app_id' => APP_ID,
				'app_secret' => APP_SECRET,
				'default_graph_version' => 'v2.2'
			));  


			$longLivedToken = $fb->getOAuth2Client()->getLongLivedAccessToken(APP_TOKEN);

			$fb->setDefaultAccessToken($longLivedToken);

			$response = $fb->sendRequest('GET', PAGE_ID, ['fields' => 'access_token'])
			->getDecodedBody();

			$foreverPageAccessToken = $response['access_token'];

			/*$sharedData = array(
				"message" => $data->title,
				"link" => $this->GD->ofiurl( $data->url ),
				"picture" => $pic["url"],
				"name" => $data->title,
				"caption" => "www.monamade.sk",
				"description" => $data->description
			);
*/
			$fb->setDefaultAccessToken($foreverPageAccessToken);

			return $fb;
		}

		public function share_as_link($data, $r = '') {
			
			$fb = $this->fbaccess();


			$files = $data->file_list ? $data->file_list : $data->files;
			$pic = $this->GD->generate_pictureUrl( $files, true );

			$sended = $fb->sendRequest('POST', PAGE_ID."/feed", [
				"message" => $data->fcbmessage ? $data->fcbmessage : $data->title,
				"link" => $this->GD->ofiurl( $data->url ),
				"picture" => $pic["url"],
				"name" => $data->title,
				"caption" => "www.monamade.sk",
				"description" => $data->description
			])->getDecodedBody();
			/*$fb->setDefaultAccessToken($foreverPageAccessToken);
			$fb->sendRequest('POST', PAGE_ID."/photos", [
				"message" => $data->title,
				'source' => $fb->fileToUpload( $pic["url"] )
			]);*/
			
			if ( $sended ) {
				$this->update_share("fb_shared_link", $sended["id"], "link" );

				return $sended;
			}
		}

		public function delete_link($id, $r = '') {
			$fb = $this->fbaccess();

			$post = $fb->sendRequest('DELETE', '/'.$id)->getDecodedBody();

			//if ( $post ) {		
				$del = explode("_", $id);

				$this->m->q("DELETE FROM fb_sharedata WHERE id = '".$del[1]."' AND type = 'link'");
				return true;
			//}
		}

		/*public function get_data_about_share($table, $r = '') {
			$stmt = $this->m->q("SELECT * FROM $table WHERE $this->identificator = '$this->diyID'");

			if ( $re = $stmt->fetch_object() )
				$r = $re;
			
			return $r;
		}
*/
		public function update_share($select, $set, $type, $r = '') {

			$fbid = explode("_", $set);

			$this->m->q("INSERT INTO fb_sharedata (system_id, id, date, type) VALUES ('".$this->diyID."','".$fbid[1]."', '".time()."', '".$type."') ");
		}

		public function generate_post_links($r = '') {
			$i = 1;

			$stmt = $this->m->q("SELECT * FROM fb_sharedata WHERE system_id = '$this->diyID' ORDER BY date DESC");

			//if ( $data = $this->get_data_about_share() ) {
			if ( $data = $this->m->resultO() ) {
				foreach ( $data as $key => $value) {
					$r .= '
					<tr>
						<td>'.$i++.'</td>
						<td><a href="https://www.facebook.com/permalink.php?story_fbid='.$value->id.'&id='.PAGE_ID.'" class="defE" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
						<td>'.date("d.m.Y H:i:s", $value->date).'</td>
						<td><button type="button" class="dltshare jdi" data-event="deletepost" data-data="'.PAGE_ID.'_'.$value->id.'" data-forresult=".share-data1"><i class="ii iExit" aria-hidden="true"></i></button></td>
					</tr>';
				}

				$re = '
				<table class="sharedTable">
					<tr>
						<th>#</th>
						<th>'.$this->GD->text(311).'</th>
						<th>'.$this->GD->text(309).'</th>
						<th>'.$this->GD->text(310).'</th>
					</tr>
					'.$r.'
				</table>';

			} else {
				$re = '<div class="noshare">'.mb_strtoupper($this->GD->text(308), "UTF8").'</div>';
			}	
				
			//}
			

			return $re;
		}

		public function input_icon($re, $r = "") {
			switch ($re) {
				case true:
					$r = 'fa fa-check baOK';
					break;
				case false:
					$r = 'ii iExit baBAD';
					break;
			}

			return $r;
		}
		



		public function verify_tags($data, $r = '') {
			// filter duplicates
			if ( $data ) { 
				foreach ($data as $key => $val) {    
					$filterDupl[$val] = true; 
				} 
				
				foreach ( array_keys($filterDupl) as $key => $value) {
					
					$stmt1 = $this->m->q("SELECT * FROM tagy WHERE id = '".$value."'");

					if ( $tag = $stmt1->fetch_assoc() ) {
						$r .= "#".$value;
					}
				}

				$r .= "#";
			} else
				$r = "none";
			

			return $r;
		}

		public function update_tags($data) {
			$stmt = $this->m->q("UPDATE $this->table SET tags = '".$data."' WHERE $this->identificator = '".$this->diyID."'");
		}



		public function verify_colors($data, $r = '') {
			// filter duplicates
			if ( $data ) { 
				foreach ($data as $key => $val) {    
					$filterDupl[$val] = true; 
				} 
				

				foreach ( array_keys($filterDupl) as $key => $value) {
					
					$stmt1 = $this->m->q("SELECT * FROM filter_colors WHERE id = '".$value."'");

					if ( $tag = $stmt1->fetch_assoc() ) {
						$r .= "#".$value;
					}
				}

				$r .= "#";
			} else
				$r = "none";
			

			return $r;
		}

		public function update_colors($data) {
			$stmt = $this->m->q("UPDATE $this->table SET colors = '".$data."' WHERE $this->identificator = '".$this->diyID."'");
		}


		public function update_date() {
			$stmt = $this->m->q("UPDATE $this->table SET create_date_edit = '".time()."' WHERE $this->identificator = '".$this->diyID."'");
		}

		public function transfer($r = '') {
			$addnext = array("files", "file_list");


			if ( $this->table == "concepts" ) {

				$stmt = $this->m->q("SELECT * FROM concepts WHERE system_id = '".$this->diyID."'");
				$data = $stmt->fetch_object();

				$urlText = $this->GD->text_transform( $data->title );


				$stmt = $this->m->q("SELECT * FROM navody WHERE url = '".$urlText."' ");

				$duplicate = $stmt->fetch_assoc();

				if ( $duplicate["id"] ) {
					//$shop = $this->shop;

					$transfer = $this->generate_unique( $urlText );
				} else 
					$transfer = $urlText;

				if ( $transfer ) {

					if ( $this->m->q("INSERT INTO navody (id, url, system_id, shop_id, user_id, create_date) VALUES (NULL, '".$transfer."','".$data->system_id."', '".$data->shop_id."', '".$data->user_id."', '".time()."')") ) {

						foreach ($this->inputArray as $key => $value) {
							$stmt = $this->m->q("SELECT * FROM cms_inputs WHERE form = ".$value);

							foreach ( $this->m->resultO() as $key2 => $value2) {
								
								$stmt2 = $this->m->q("SELECT $value2->name FROM $this->table WHERE system_id = '$this->diyID'");

								$data2 = $stmt2->fetch_object();
										
								$th = $value2->name;
								$this->m->q("UPDATE navody SET $th = '".$data2->$th."' WHERE system_id = '$this->diyID'");
							}
						}

						foreach ($addnext as $key => $value) {
							$this->m->q("UPDATE navody SET $value = '".$data->$value."' WHERE system_id = '$this->diyID'");
						}


						$this->m->q("UPDATE navody SET public = '0' WHERE system_id = '$this->diyID'");
						$this->m->q("UPDATE navody SET discount = NULL WHERE system_id = '$this->diyID'");

						$this->m->q("DELETE FROM concepts WHERE system_id = '".$this->diyID."' ");

						return array( "redir" => $this->GD->link(10).$this->diyID."__".$transfer );
					} else
						return "insert error";
					
				}
			}
		}



/*
		public function verify_duplicate($where, $data, $r = false) {
			$stmt = $this->m->q("SELECT $where FROM $this->table WHERE $where = '".$data."'");
			
			if ( $stmt->fetch_object() ) {
				$r = true;
			}

			return $r;
		}*/

		public function generate_unique($data) {
			return $data."-".$this->GD->random_numbers(5);
		}
//<div class="et-aa"> <img class="selfile" data-file="

		public function check_filedata($r = '') {
			
			foreach ( array('files', 'file_list') as $key => $select) {
				$r = '';

				$stmt = $this->m->q("SELECT $select FROM $this->table WHERE $this->identificator = '$this->diyID'");

				if ( $data = $stmt->fetch_object() ) {
					foreach ( array_filter( explode("#", $data->$select) ) as $key => $value) {
						
						$stmt = $this->m->q("SELECT id FROM files WHERE id = '$value'");

						if ( $stmt->fetch_object() ) {
							$r .= '#'.$value;
						}

					}
				} 

				$this->m->q("UPDATE $this->table SET $select = '$r' WHERE $this->identificator = '$this->diyID'");
			}

		}

		public function generate_diy_files($r = '') {

			if ( $list = $this->get_concept_data( "file_list", "files" ) )
				$file_list = $list;
			else
				$file_list = $this->get_concept_data( "files", "files" );

			if ( $file_list )
				return $this->generate_pictureList( $file_list );
		}


		public function generate_diy_header($r = "") {

			if ( $list = $this->get_concept_data( "file_list", "files" ) )
				$file_list = $list;
			else
				$file_list = $this->get_concept_data( "files", "files" );

			if ( empty($file_list) ) {

				$r = '
					<div class="photoloader-intro">
						

						<div class="welkome-button">
							<div class="pl-icon i i104"></div>
							<a href="#" class="pl-intro defE jdi" data-lib="action" data-event="imguploader">
								<div class="i i102 pl-left"></div> <span>'.$this->GD->text(173).'</span> <div class="i i103 pl-right"></div> 
							</a>
						</div>
						
					</div>
				';

			} else {

				$r = '
					<div class="photoloader-progress">

						'.$this->generate_pictureList( $file_list ).'
					</div>
				';
				/*
				<div class="mdiy-header-topmenu diy-container">
							<!-- <a href="#" class="upload-button et-a fc-show" name="imguploader">'.$this->GD->text(181).' <div class="i i101 bn-icon"></div></a> -->
							<!--<a href="#" '.$inactive[0].'>'.$this->GD->text(182).' <div class="i i105 bn-icon"></div></a>-->
							'.$publish_button.'
							<a href="#" '.$inactive[1].'>'.$this->GD->text(183).'</a>
							<div class="cleaner"></div>
						</div>
						*/
				//<a href="#" class="upload-button et-a fc-show" name="imguploader">'.$this->GD->text(181).' <div class="i i101 bn-icon"></div></a>
			}

			return $r;
		}


		public function generate_diy_buttons($r = '') {
			$mess = $publish_button = '';

			if ( $this->table == 'navody') {

				$check = $this->check_all_before_publish();

				if ( $check == false ) {
					$inactive[0] = 'class="continue-button et-a defE"';

					$publish_button = '<a href="'.$this->GD->ofiurl( $this->ITEM->url ).'" '.$inactive[0].' target="_blank">'.$this->GD->text(183).'<div class="ii iGlobe bn-icon"></div></a>';
				} else {
					$inactive[0] = 'class="continue-button et-a inactive" disabled="disabled"';
					$mess = '<div class="bfinfo"><label for="'.$check["d"]->idd.'">'.sprintf( $this->GD->text(355), '<strong>'.$this->GD->text($check["d"]->text).'</strong>' ).'</label></div>';

					$publish_button = $mess.'<a href="'.$this->GD->ofiurl( $this->ITEM->url ).'" '.$inactive[0].' target="_blank">'.$this->GD->text(183).'<div class="ii iGlobe bn-icon"></div></a>';
				}

			} else {
				if ( $this->check_all_before_publish() == false ) {
					$inactive[0] = 'class="continue-button et-a doevent"';
					$inactive[1] = 'class="show-preview et-a"';

					$publish_button = '<a href="#" '.$inactive[0].' data-event="additem-continue">'.$this->GD->text(266).'<div class="i i105 bn-icon"></div></a>';
				} else {
					$inactive[0] = 'class="continue-button et-a inactive" disabled="disabled"';
					$inactive[1] = 'class="show-preview et-a inactive" disabled="disabled"';

					$publish_button = '<a href="#" '.$inactive[0].'>'.$this->GD->text(266).'<div class="i i105 bn-icon"></div></a>';
				}
			}

			return '<div class="mdiy-header-topmenu">

							'.$publish_button.'

							<!--<div class="mdiy-autosave">'.$this->GD->text(276).'</div>-->
							<div class="cleaner"></div>
						</div>';

		}

		public function check_all_before_publish($save = false, $r = array()) {
			
			foreach ($this->inputArray as $key => $value) {

				$stmt = $this->m->q("SELECT * FROM cms_inputs WHERE form = ".$value." AND required = 1");

				foreach ( $this->m->resultO() as $key2 => $value2) {
					
					$stmt2 = $this->m->q("SELECT $value2->name FROM $this->table WHERE $this->identificator = '$this->diyID'");

					if ( $data = $stmt2->fetch_object() ) {

						$na = $value2->name;

						/*if ( empty($data->$na) )
							$r[$na] = $this->GD->text( $value2->text );*/
 
						//$check = $this->INP->action( $value2->name, $data->$na);
						//if ( $check = $this->INP->action( $value2->name, $data->$na, true) )
 						if ( empty($data->$na) ) {
 							$check = $this->INP->action( $value2->name, $data->$na, true);

							$r = array("d" => $value2, "error" => '<span class="staBAD"><label for="'.$value2->idd.'">(!) '.$check.'</label></span>');
 						} else {

 							///check
 						}
					}
				}
			}

			if ( count($r) > 0 ) {
				$this->m->q("UPDATE navody SET public = '0' WHERE $this->identificator = '$this->diyID'");

				$re = $r;
			} else
				$re = false;

			return $re;
		}







/*

		public function get_picture_list($ids, $r = "") {
			
			if ( $ids ) {
				foreach ($data as $key => $value) {
					$stmt .= $this->m->q("SELECT * FROM files WHERE galery_id = '$ids' ORDER BY create_date DESC");
				}
			
				$r = $stmt->fetch_assoc();
			}
			
			return $r;
		}*/

		public function get_picture_id_from_filename($ids, $r = "") {
			
			if ( $ids ) {
				foreach ($ids as $key => $value) {
					$stmt = $this->m->q("SELECT * FROM files WHERE filename = '$value' ORDER BY create_date DESC");
					$re = $stmt->fetch_assoc();

					$r[] = $re["id"];
				}
			}
			
			return $r;
		}

		public function update_filelist( $resorted_files, $update = "" ) {
			if ( $resorted_files ) {

				$update = $this->verify_default_files_with_updated_files( $resorted_files );

				//$update = $this->GD->create_DATA_for_database_from_list( $resorted_files );

				$this->m->q("UPDATE $this->table SET file_list = '$update' WHERE $this->identificator = '$this->diyID' ");


				
			}

			//return $rrr;
		}

		public function verify_default_files_with_updated_files( $resorted_files, $re = "", $miss = "" ) {
			
			$default_files = $this->m->q("SELECT files FROM $this->table WHERE $this->identificator = '$this->diyID' ");
			$r = $default_files->fetch_assoc();

			$defaultData = $defaultData2 = $this->create_array_from_database( $r["files"] );

			foreach ( $resorted_files as $key ) $defaultData[$key] = 1;

			foreach ( $resorted_files as $key => $value) {
				
				if ( !empty($value) ) {
					if ( isset( $defaultData[$key] ) ) 
						$re .= "#".$value;
					else {
						$re .= "";
						//$miss .= $value;
					}
				}

			}

			// add missing data after change id data from html code (hack)
			if ( $defaultData2 ) {
				foreach ( $defaultData2 as $key => $value) {
					if ( !empty($value) ) {
						if ( !in_array($value, $resorted_files))
							$miss .= "#".$value;
						else
							$miss .= "";
					}
				}
			}
			

			return $re.$miss;
		}
		

		public function generate_pictureList( $data, $smaller = "" ) {
			$list = $listTemporary = $primPic = "";
			$temporary = 0;
			
			if ( $data ) {

				foreach ($data as $key => $value) {

					$file = $value["folder"].'/'.$value["filename"];
					//$filename = explode(".", $value["filename"]);

					//$imgLink = $this->GD->generate_pictureUrl( $data->file_list, true, $this->AJAX );
					$imgLink = $this->GD->generate_pictureUrl( $value["id"], false, $this->ajax );
					$imgSize = $this->GD->picture_dimension( $imgLink["url"], $imgLink["url_nohhtp"] );

					if ($key == 2 )
						$classes = "tempo-img tempo-reserved sort";
					else
						$classes = "tempo-img tempo-reserved sort";

					if ( $key == 0 )
						$primPic = '<div class="picPrimary">'.$this->GD->text(249).'</div>';
					else 
						$primPic = "";
					
					$list .= '
							<li class="'.$classes.'" id="'.$value["filename"].'"> 
								<img src="'.$this->GD->suburl_data(DEF_UPLOAD_FOLDER.$file.".".$value["file_type"]).'" class="img'.$imgSize.'" alt="'.$value["filename_orig"].'">
								<div class="temp-img-menu">
									<a href="#" class="t-i-m-delete remove-preview doevent" data-event="remove-photo" data-eventinfo="'.$value["id"].'"><span>ZMAZAŤ FOTKU</span><i class="ii iExit" aria-hidden="true"></i></a>
								</div>
								'.$primPic.'

							</li>';

				}

				//if ( count($data) >= 6 ) $smaller = " toomuch-";
				$temporary = MAX_FILES - count($data);

				$ee = count($data);
				for ($i=0; $i < $temporary; $i++) {
					if ( $i == 0 ) {
						$listTemporary .= '
							<li class="tempo-img tempo-empty tempo-img-placeholder default-file"> 
								<a href="#" class="defa-imgg defE jdi" data-lib="action" data-event="imguploader">
									<div class="defaa"><div class="ii iCamera"></div>'.$this->GD->text(248).'</div>
								</a>
							</li>';
					} else {
						$ee = $i + count($data) +1;

						$listTemporary .= '
							<li class="tempo-img tempo-empty tempo-img-placeholder default-file"><div class="cee">'.$ee.'</div></li>';
					}
				}

				return '

				<div class="ui-widget ui-helper-clearfix"></div>
				<div class="diy-container">
					<div class="picture-list'.$smaller.'">
						<!--<div class="diy-container">
							<div class="pl-menu">
								<a href="#" class="upload-button et-a fc-show" name="imguploader">'.$this->GD->text(219).'<div class="i i101 bn-icon"></div></a>
							</div>
						</div>-->
						
						<ul class="plist" id="files-sortable">
							'.$list.'
							'.$listTemporary.'
							<div class="cleaner"></div>
						</ul>
					</div>
				</div>
				';
			}
		}


		public function get_concept_data($select, $table, $arr = "") {

			if ( $stmt = $this->m->q("SELECT $select FROM $this->table WHERE $this->identificator = '$this->diyID' ") ) {

				$files = $stmt->fetch_assoc();

				foreach ( array_filter( explode("#", $files[ $select ]) ) as $key => $value) {
					if ( !empty($value) ) {


						$stmt = $this->m->q("SELECT * FROM $table WHERE id = ".$value);
						
						$arr[] = $stmt->fetch_assoc();
					}
				}
			}

			return $arr;
		}


		public function generate_categories() {
			$r = $categories = $submenu1 = $submenu2 = "";

			//$LANG = $this->mm->lang();

			$q = $this->m->q("SELECT * FROM kategorie");
			
			foreach ($this->m->result2() as $key => $value) {
				$lvl1 = "";
				$lvl2 = "";

				$categories .= '<a href="#" class="edit-input l" name="'.$value["sk"].'" data-inputtarget="additem-category" data-datainfo="cat'.$value["category"].'">'.ucfirst( $this->GD->text_( $value["text"] ) ).'</a>';

			}
			

			return '
				
					<div class="addi-input selecat">
						<div class="addi-head">
							<label for="additem-category"><span class="i-req">'.$this->GD->text(240).'</span><i class="fa fa-snowflake-o" aria-hidden="true"></i></label>
						</div>
						<div class="addi-with-select">
							<div class="addi-body"> 
								<input type="text" class="adi-text show-select selectcategories" name="category" data-showtarget="#found-category" id="additem-category" value="'.$this->INP->get_data("category").'" data-data="" readonly>
							</div>
							<div class="addi-select">
								<a href="#" class="show-tags show-tar update-data et-a" data-showtarget="all-availcategory" data-updatetarget="all-availcategory" data-updateevent="additem-categories" data-updateinfo="ALL">'.$this->GD->text(246).'</a>
							</div>


							<div class="select-menu addtoinput dH" id="all-availcategory"><div class="s-m-container finddata-select-re"><div class="loaa-icon"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div></div></div>

							<div class="cleaner"></div>
						</div>
					</div>
			';
			/*
			<div class="addi-input selecat">
						<div class="addi-head">
							<label for="additem-category"><span class="i-req">'.$this->GD->text(240).'</span><i class="fa fa-snowflake-o" aria-hidden="true"></i></label>
						</div>
						
						<div class="addi-body add-i"> 
							<input type="text" class="adi-text show-select selectcategories" id="additem-category" name="category" value="'.$this->INP->get_data("category").'" data-data="" readonly>
							<div class="ss-icon"><i class="fa fa-chevron-down" aria-hidden="true"></i></div>
							<div class="select-menu dH">
								<div class="s-m-container">
									'.$categories.'<a href="#" class="edit-input-hide l" name="" data-inputtarget="additem-category" title="'.$this->GD->text(350).'"><span class="ii iExit"></span></a>

									<a href="#" class="edit-input-action jdi defE l" name="" data-inputtarget="additem-category" data-lib="action" data-event="addcategory">'.$this->GD->text(349).'</a>
								</div>
							</div>
						</div>
					</div>
					*/
		}





		public function generate_keywords($r = "") {

			$q = $this->m->q("SELECT * FROM tagy");
			
			foreach ($this->m->result2() as $key => $value) {
				$r .= '<a href="#" class="edit-input l" name="'.$this->GD->text( $value["id_text"] ).'" data-inputtarget="additem-keywords">'.ucfirst( $this->GD->text( $value["id_text"] ) ).'</a>';
			}

			return $r;
		}

		public function generate_tags($r = "", $data = "") {
			$stmt = $this->m->q("SELECT tags FROM $this->table WHERE $this->identificator = '".$this->diyID."' ");
			
			if ( $re = $stmt->fetch_assoc() ) {

				if ( !empty($re["tags"]) ) {

					$data = explode("#", $re["tags"]);

					if ( count($data) != 0 ) {
						foreach ( array_filter($data) as $key => $value) {
							
							$stmt1 = $this->m->q("SELECT * FROM tagy WHERE id = '".$value."'");
				
							if ( $tag = $stmt1->fetch_assoc() ) {
								$r .= '<li class="tag-added" data-type="additem-tags" data-id="'.$tag["id"].'"><div>'.mb_strtoupper( $this->GD->text_( $tag["id_text"] ), "utf8" ).'</div><a href="#" class="additag-delete" title="'.sprintf($this->GD->text( 247 ), ucfirst($this->GD->text_( $tag["id_text"] ))).'"><i class="ii iExit" aria-hidden="true"></i></a></li>';
							} else {

								$repair = str_replace("#".$value, '', $re["tags"]);

								$this->m->q("UPDATE $this->table SET tags = '$repair' WHERE $this->identificator = '".$this->diyID."' ");
							}

							
						}
					}
				}
				
			}
			
			return  $r;
		}
		

		public function generate_availableAllTags($duplicates = false, $r = "") {
			//$LANG = $this->mm->lang();

			$stmt = $this->m->q("SELECT tags FROM $this->table WHERE $this->identificator = '$this->diyID' ");
			$alreadyTags = $stmt->fetch_assoc();
			$alreadyTags = explode("#", $alreadyTags["tags"]);

			$this->m->q("SELECT * FROM tagy ORDER BY ".$LANG);

			foreach ($this->m->result2() as $key => $value) {
				if ( !array_search($value["id"], array_filter($alreadyTags)) ) {
					$r .= '<a href="#" class="edit-input" name="'.mb_strtoupper( $this->GD->text_( $value["id_text"] ) ).'" data-id="'.$value["id"].'">'.ucfirst( $this->GD->text_( $value["id_text"] ) ).'</a>';
				}
			}
			
			return $r;
		}

		public function generate_colorkeywords($r = "") {

			$q = $this->m->q("SELECT * FROM filter_colors");
			
			foreach ($this->m->result2() as $key => $value) {
				$r .= '<a href="#" class="edit-input l" name="'.$this->GD->text_( $value["text"] ).'" data-inputtarget="additem-colors">'.ucfirst( $this->GD->text_( $value["text"] ) ).'</a>';
			}

			return $r;
		}

		public function generate_colors($r = "", $data = "") {
			$stmt = $this->m->q("SELECT colors FROM $this->table WHERE $this->identificator = '".$this->diyID."' ");
			
			if ( $re = $stmt->fetch_assoc() ) {

				if ( !empty($re["colors"]) ) {

					$data = explode("#", $re["colors"]);

					if ( count($data) != 0 ) {
						foreach ( array_filter($data) as $key => $value) {
							
							$stmt1 = $this->m->q("SELECT * FROM filter_colors WHERE id = '".$value."'");
				
							$tag = $stmt1->fetch_assoc();

							$color = $tag["font"] ? $tag["font"] : "FFFFFF";

							$r .= '<li class="tag-added" data-type="additem-colors" data-id="'.$tag["id"].'"><div style="background:#'.$tag["color"].';color:#'.$color.'">'.mb_strtoupper( $this->GD->text_( $tag["text"] ), "utf8" ).'</div><a href="#" class="additag-delete" title="'.sprintf($this->GD->text( 247 ), ucfirst($this->GD->text_( $tag["text"] ))).'"><i class="ii iExit" aria-hidden="true"></i></a></li>';
						}
					}
				}
				
			}
			
			return  $r;
		}
		
		public function generate_availableAllColors($duplicates = false, $r = "") {
			$stmt = $this->m->q("SELECT colors FROM $this->table WHERE $this->identificator = '$this->diyID' ");
			$alreadyTags = $stmt->fetch_assoc();
			$alreadyTags = explode("#", $alreadyTags["tags"]);

			$this->m->q("SELECT * FROM filter_colors ORDER BY ".$LANG);

			foreach ($this->m->result2() as $key => $value) {
				if ( !array_search($value["id"], array_filter($alreadyTags)) ) {
					$r .= '<a href="#" class="edit-input" data-inputtarget="additem-colors" name="'.mb_strtoupper( $this->GD->text_( $value["text"] ) ).'" data-id="'.$value["id"].'">'.ucfirst( $this->GD->text_( $value["text"] ) ).'</a>';
				}
			}
			
			return $r;
		}

/*
		public function verify_tags($data, $r = '') {
			// filter duplicates
			if ( $data ) { 
				foreach ($data as $key => $val) {    
					$filterDupl[$val] = true; 
				} 
				
				foreach ( array_keys($filterDupl) as $key => $value) {
					
					$stmt1 = $this->m->q("SELECT * FROM tagy WHERE id = '".$value."'");

					if ( $tag = $stmt1->fetch_assoc() ) {
						$r .= "#".$value;
					}
				}
			} else
				$r = "none";
			

			return $r;
		}
		*/
		/* SEARCH SCRIPTs */
		
		public function find_data($r = "") {
			
			$stmt = $this->m->q("SELECT tags FROM $this->table WHERE $this->identificator = '$this->diyID' ");
			$alreadyTags = $stmt->fetch_assoc();
			$alreadyTags = explode("#", $alreadyTags["tags"]);


			$queries = array( 'data' => $this->keys_query() );
			$this->m->q("SELECT * FROM tagy ".$queries["data"]);
			//$data = $this->m->result2();
			foreach ($this->m->result2() as $key => $value) {
				if ( !array_search($value["id"], array_filter($alreadyTags)) ) {
					$r[] = $value;
				}
			}
			

			return $r;
		}

		public function find_data_colors($r = "") {
			
			$stmt = $this->m->q("SELECT colors FROM $this->table WHERE $this->identificator = '$this->diyID' ");
			$alreadyTags = $stmt->fetch_assoc();
			$alreadyTags = explode("#", $alreadyTags["colors"]);


			$queries = array( 'data' => $this->keys_query() );
			$this->m->q("SELECT * FROM filter_colors ".$queries["data"]);
			//$data = $this->m->result2();
			foreach ($this->m->result2() as $key => $value) {
				if ( !array_search($value["id"], array_filter($alreadyTags)) ) {
					$r[] = $value;
				}
			}
			

			return $r;
		}

		public function find_data_category($r = "") {
			
			$this->m->q("SELECT * FROM kategorie");
			//$data = $this->m->result2();
			foreach ($this->m->result2() as $key => $value) {
				//if ( !array_search($value["id"], array_filter($alreadyTags)) ) {
					$r[] = $value;
				//}
			}
			

			return $r;
		}

		public function keys_query( $r = "") {
			$nums = count($this->columns) - 1;
			
			if ( $this->keywords == "ALL" ) {
				$r = " ORDER BY sk";

				return $r;
			} else {
				foreach ($this->columns as $columnkey => $column) {
					foreach ($this->keywords as $key => $value) {
						$r .= " ".$column." LIKE '%".$value."%' OR ";
					}
				}

				return " WHERE ".substr($r, 0, -4);
			}
		}


		public function create_keywords($data, $r = "") {
			
			$array = explode(" ", $data );

			foreach ($array as $key => $value) {

				$text = $this->GD->text_transform($value);

				if ( $value == $text )
					$r[] = $value;
				else {
					$r[] = $value;
					$r[] = $text;
				}
			}

			return $r; 
		}

		public function create_results($arr, $keys, $r = "") {
			
			foreach ($arr as $key => $data) {
				$r .= '<a href="#" class="edit-input" data-target="additem-tags" name="'.mb_strtoupper( $this->GD->text_( $data["id_text"] ) ).'" data-id="'.$data["id"].'">'.ucfirst( $this->GD->text_( $data["id_text"] ) ).'</a>';
				//$r .= var_dump($data);
			}

			return $r.'<a href="#" class="edit-input-hide l" name="" data-inputtarget="additem-category" title="'.$this->GD->text(350).'"><span class="ii iExit"></span></a>'.'<a href="#" class="edit-input-action jdi defE l" name="" data-inputtarget="additem-tags" data-lib="action" data-event="addtag">'.$this->GD->text(360).'</a>';
		}

		public function create_results2($arr, $keys, $r = "") {
			
			foreach ($arr as $key => $data) {
				$color = $data["font"] ? $data["font"] : "FFFFFF";

				$r .= '<a href="#" class="edit-input" data-target="additem-colors" name="'.mb_strtoupper( $this->GD->text_( $data["text"] ) ).'" data-id="'.$data["id"].'" style="background:#'.$data["color"].';color:#'.$color.'">'.ucfirst( $this->GD->text_( $data["text"] ) ).'</a>';
				//$r .= var_dump($data);
			}

			return $r.'<a href="#" class="edit-input-hide l" name="" data-inputtarget="additem-category" title="'.$this->GD->text(350).'"><span class="ii iExit"></span></a>';
		}

		public function create_results_category($arr, $keys, $r = "") {
			
			foreach ($arr as $key => $data) {
				$r .= '<a href="#" class="edit-input l" name="'.$data["sk"].'" data-inputtarget="additem-category" data-datainfo="cat'.$data["category"].'">'.ucfirst( $this->GD->text_( $data["text"] ) ).'</a>';

				//$r .= var_dump($data);
			}

			return $r.'<a href="#" class="edit-input-hide l" name="" data-inputtarget="additem-category" title="'.$this->GD->text(350).'"><span class="ii iExit"></span></a>'.'<a href="#" class="edit-input-action jdi defE l" name="" data-inputtarget="additem-tags" data-lib="action" data-event="addcategory">'.$this->GD->text(349).'</a>';
		}

		/* SEARCH SCRIPTs */


		/* PICTURES */
/*
		public function pic_remove($id, $value='') {
			# code...
		}*/

		/* PICTURES */


		public function create_array_from_database( $data, $arr = "" ) {
			if ( $data ) {
				foreach ( explode("#", $data) as $key => $value) {
					$arr[$key] = $value;
				}
			}

			return $arr;
		}

		public function create_data_for_database_from_list($data_array, $re = "") {
			if ( $data_array ) {
				foreach ($data_array as $key => $value) {
					$re .= "#".$value;
				}
			}

			return $re;
		}


		public function verify_product( $page ) {

			switch ($page) {
				case 9:	//add item
					
					if ( isset($_GET["id"]) ) {
						
						//$urlID = $_GET["id"];
						
						if ( $urldiy = $this->verify_diy($page, $_GET["id"]) ) {
							setcookie("DIY", $urldiy, strtotime("+1 months"), '/', $this->GD->domain());
						} else {
							$this->diyID = $this->new_diy();
						}

					} else {
						$this->diyID = $this->new_diy();
					}

					break;
				
				case 10:	//edit item
					
					//if ( isset($_GET["id"]) ) {
						
						//$urlID = $_GET["id"];
						
						if ( $urldiy = $this->verify_diy($page,  $this->CHR->ITEM_ID($_GET["id"])) ) {
							setcookie("DIY", $urldiy, strtotime("+1 months"), '/', $this->GD->domain());
						} else {
							$this->diyID = $this->new_diy();
						}

					/*} else {
						$this->diyID = $this->new_diy();
					}*/
			}

			return array("table" => $this->table, "item" => $this->diyID);
		}

		public function new_diy() {
			//$user = $this->mm->userdata;
			//if ( $this->table == "concepts" ) {
				$new = strtoupper( $this->GD->random_chars_oddo(10, 20) );
				$time = time();

				if ( $this->m->q("INSERT INTO concepts (id, system_id, shop_id, user_id, create_date) VALUES (NULL, '$new', ".$this->GD->U->shopID.", ".$this->GD->U->userID.", $time)") ) {
					setcookie("DIY", $new, strtotime("+1 months"), '/', $this->GD->domain());

					header( "Location: ".$this->GD->link(9).$new );
				}
			//}
		}

		public function verify_diy($page, $data) {
			
			switch ($page) {
				case 9:	//add item

					$stmt = $this->m->q("SELECT system_id FROM concepts WHERE system_id = '$data' ");
					if ( $r = $stmt->fetch_object() ) return $r->system_id;

					break;
				
				case 10:	//edit item

					$stmt = $this->m->q("SELECT system_id, url FROM navody WHERE system_id = '$data' ");
					if ( $r = $stmt->fetch_object() ) return $r->system_id;

					break;
			}
		}




		public function product_location($r='') {
			
			$stmt = $this->m->q("SELECT id FROM navody WHERE system_id = '".$this->diyID."'");

			if ( $full = $stmt->fetch_assoc() ) {
				$r = "navody";
			} else {
				//$stmt1 = $this->m->q("SELECT id FROM concepts WHERE system_id = '".$this->diyID."'");

				//if ( $concept = $stmt1->fetch_assoc() )
					$r = "concepts";
			}

			return $r;
		}
	}

