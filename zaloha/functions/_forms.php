<?php
	$FORM = new FORMS();

	class FORMS extends GLOBALDATA {
		
		protected static $instance;
		protected $mysql;
		
		public function __construct() {
			$this->mysql = SQL::init();
		}
		
		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new FORMS();
			}

			return self::$instance;
		}
/*
		public function __call($name, $args) {
			if( method_exists($this->connection, $name) ) {
				return call_user_func_array(array($this->connection, $name), $args);
			} else {
				trigger_error('Unknown Method ' . $name . '()', E_USER_WARNING);
				return false;
			}
		}
*/

		public function forms_result($type) {
			
			$head=''; $body='';

			if ( $type ) {
		
				$data = $this->forms_body($type);

				switch ($type) {
					case 'formular':
						$head = '
							<tr class="five">
								<th class="tac">ID/IČ</th>
								<th class="tal">Dátum prijatia</th>
								<th class="tal">Meno &amp; Priezvisko</th>
								<th class="tal">E-mail</th>
								<th class="tal">Telefón</th>
								<th class="tac">STATUS</th>
							</tr>
						';
						break;

					case 'feedback':
						$head = '
							<tr class="three">
								<th class="tac">ID/IČ</th>
								<th class="tal">Dátum prijatia</th>
								<th class="tal">E-mail</th>
								<th class="tac">STATUS</th>
							</tr>
						';
						break;

					case 'cms_changelog':
						$head = '
							<tr class="five">
								<th class="tac">ID/IČ</th>
								<th class="tal">VERZIA SYSTÉMU</th>
								<th class="tal">DATUM ZVEREJNENIA</th>
								<th class="tal">POPIS</th>
								<th class="tac">STATUS</th>
							</tr>
						';
						break;

					case 'text':
						$data = $this->forms_texty($type);

						$head = '
							<tr class="five">
								<th class="tac">ID/IČ</th>
								<th class="tal">SK verzia</th>
								<th class="tal">EN verzia</th>
							</tr>
						';
						break;

				}

				if ( $data["count"] > 0 ) {
					$return = '
						<div class="formsbox-table">
							<div class="formsbox-count iHelp" title="Počet dostupných údajov">'.$data["count"].'</div>
							<table class="forms-table">
								'.$head.'
								'.$data["data"].'
							</table>
						</div>
					';
				} else
					$return = '<div class="noFormData">'.$this->no_data().'</div>';
				

				return $return;
			}
		}

		public function list_table($type) {
			
			$head=''; $body='';

			if ( $type ) {
		
				switch ($type) {
					case 'text':
						$data = $this->forms_texty($type);

						$head = '
							<tr class="">
								<th class="tac">ID/IČ</th>
								<th class="tal">ZÁKLADNÁ VERZIA TEXTU</th>
								<th class="tal">UPRAVENÁ VERZIA TEXTU</th>
								<th class="tac">STRÁNKA </th>
								<th class="act-but"></th>
							</tr>
						';
						break;

				}

				if ( $data["count"] > 0 ) {
					$return = '
						<div class="formsbox-table">
							<div class="formsbox-count iHelp" title="Počet dostupných údajov">'.$data["count"].'</div>
							<table class="forms-table">
								'.$head.'
								'.$data["data"].'
							</table>
						</div>
					';
				} else
					$return = '<div class="noFormData">'.$this->no_data().'</div>';
				

				return $return;
			}
		}



		public function forms_body($table, $r='', $count = 0) {
			global $mysql;

			$stmt = $this->mysql->query("SELECT * FROM $table ORDER BY create_date DESC");

			foreach ( $this->mysql->result2() as $key => $value) {
				$count += 1; 

				$timeToLaik = new cas( date( "d.m.Y H:i", $value["create_date"] ) );
				
				$status = $this->form_status( $value["precitane"] );

				switch ($table) {
					case "formular":
						$r .= '
							<tr>
								<td><a href="#" class="show-tool fc-show" name="fullscreen-tools" data-type="message" data-data="'.$value["typ"].'-'.$value["system_id"].'">'.$value["system_id"].'</a></td>
								<td class="dateswitch">
									<a href="#" class="show-tool fc-show" name="fullscreen-tools" data-type="message" data-data="'.$value["typ"].'-'.$value["system_id"].'">
										<div class="ntfcn-date-complete title="'.$this->date_($value["create_date"]).'">'.$timeToLaik->result().'</div> <div class="ntfcn-date-short">'.$this->date_($value["create_date"]).'</div>
									</a>
								</td>
								<td>'.ucwords($value["name"]).'</td>
								<td> <a href="mailto:'.$value["email"].'">'.$value["email"].'</a></td>
								<td>'.$value["mobile"].'</td>
								<td>'.$status.'</td>
							</tr>
						';
					break;

					case "feedback":
						$r .= '
							<tr>
								<td><a href="#" class="show-tool fc-show" name="fullscreen-tools" data-type="message" data-data="'.$value["typ"].'-'.$value["system_id"].'">'.$value["system_id"].'</a></td>
								<td class="dateswitch">
									<a href="#" class="show-tool fc-show" name="fullscreen-tools" data-type="message" data-data="'.$value["typ"].'-'.$value["system_id"].'">
										<div class="ntfcn-date-complete title="'.$this->date_($value["create_date"]).'">'.$timeToLaik->result().'</div> <div class="ntfcn-date-short">'.$this->date_($value["create_date"]).'</div>
									</a>
								</td>
								<td> <a href="mailto:'.$value["email"].'">'.$value["email"].'</a></td>
								<td>'.$status.'</td>
							</tr>
						';
						break;

					case "cms_changelog":
						$r .= '
							<tr>
								<td><a href="#" class="show-tool fc-show" name="fullscreen-tools" data-type="message" data-data="'.$value["typ"].'-'.$value["system_id"].'">'.$value["system_id"].'</a></td>
								<td>'.ucwords($value["verzia"]).'</td>
								<td class="dateswitch">
									<a href="#" class="show-tool fc-show" name="fullscreen-tools" data-type="message" data-data="'.$value["typ"].'-'.$value["system_id"].'">
										<div class="ntfcn-date-complete title="'.$this->date_($value["create_date"]).'">'.$timeToLaik->result().'</div> <div class="ntfcn-date-short">'.$this->date_($value["create_date"]).'</div>
									</a>
								</td>
								<td>'.$value["head"].'</td>
								<td>'.$status.'</td>
							</tr>
						';
						break;

					case "text":
						$r .= '
							<tr>
								<td>'.$value["id"].'</td>
								<td>'.$value["sk"].'</td>
								<td>'.$value["edited"].'</td>
								<td class="tac">'.$this->text_place( $value["page"] ).'</td>

							</tr>
						';
						break;
				}
			}

			return array('data' => $r, 'count' => $count);
		}



		public function forms_texty($table, $r='', $count = 0) {
			global $mysql;

			$stmt = $this->mysql->query("SELECT * FROM $table ORDER BY id ASC");

			foreach ( $this->mysql->result2() as $key => $value) {
				$count += 1; 

				switch ($table) {
					case "text":
						$r .= '
							<tr class="show-b">
								<td>'.$value["id"].'</td>
								<td><div class="vpqfv">'.$value["sk"].'</div></td>
								<td><div class="vpqfv">'.$value["edited"].'</div></td>
								<td class="tac">'.$this->text_place( $value["page"] ).'</td>
								<td class="act-but"> <button type="button" class="edit-galery fc-show ii i114" name="fullscreen-tools" data-type="'.$value["id"].'" data-info="edit-text"></button> </td>
							</tr>
						';
						break;
				}
			}

			return array('data' => $r, 'count' => $count);
		}


		public function form_status($status) {
			
			switch ($status) {
				case 1:
					$r = '<div class="form-status i i13" title="Prečítané"></div>';
					break;
				
				default:
					$r = '<div class="form-status i i12" title="Neprečítané"></div>';
					break;
			}

			return $r;
		}






		public function text_place($text, $r = '', $count = 0) {
			
			$arr = explode( "#", $text );

			if ( $arr ) {
				$array = array_filter( $arr );

				$co = count($array);

				foreach ($array as $key => $value) {
					
					$count += 1;


					$stmt = $this->mysql->query("SELECT * FROM pages WHERE page = ".$value );

					$page = $stmt->fetch_assoc();

					if ( $page["name"] ) {
						if ( $co == $count ){
							$r .= '<a href="'.$this->link_ofi($value).'" target="_blank">'.mb_strtoupper($page["name"], 'UTF-8').'</a>';
						} else {
							$r .= '<a href="'.$this->link_ofi($value).'" target="_blank">'.mb_strtoupper($page["name"], 'UTF-8').'</a>, ';
						}
					}
				}
			}

			if ( $r )
				return $r;
			else
				return "SYSTÉM";
		}


	}
