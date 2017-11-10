<?php
	$ACC = new ACCOUNT();

	class ACCOUNT {
		
		protected $mysql;
		protected $GD;
		
		public function __construct() {
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();


			if ( $this->GD->is_logged() == true )
				$this->user = isset($this->GD->userdata->firstname) || isset($this->GD->userdata->lastname) ? $this->GD->userdata->firstname." ".$this->GD->userdata->lastname : $this->GD->userdata->userEMAIL;
		}

		/*function __construct($type, $data) {
			$this->type = $type;
			$this->data = $data;
		}*/
/*
		public function DATA($data) {
			
			$return = array();

			foreach ($data as $key => $value) {
				
				$explode = explode("-", $key);

				$return[$explode[1]] = $value;
			}

			return $return;
		}*/

		public function action($type, $data, $return = "") {


			if ( $this->GD->is_logged() == true ) {
				if ( $this->GD->is_inactive() == false ) {

					switch ($type) {

						case 'refresh':
							$inactive = $this->GD->refresh_activity();

							if ( $inactive == true ) {
								$return = array("mess" 	=> $this->GD->logout_message(1) );

								$this->GD->logout();
							}

							break;

						case 'logout':
							//$return = array("mess" 	=> $this->GD->logout_message(3) );
							$message = '
								<div class="loader-content l-ok">
									<div>
										<div class="loader-icon"><div class="ii iCoffe"></div></div>
										<p>'.$this->GD->logout_message(3).'</p>
									</div>
								</div>
							';

							$return = array( "mess" 	=> $message );

							$this->GD->logout();
							break;
					}

				} 
				else {
					//$return = array("mess" 	=> $this->GD->logout_message(1) );

					$this->GD->logout();
				}
			} else {
				$return = array("mess" 	=> $this->GD->logout_message(1) );
				$this->GD->logout();
			}
			/*else {
				$return = array("offline" 	=> $this->logout_message(1) );

				$this->logout();
			}*/
			//}
			/*else
				$return = array("offline" 	=> $this->logout_message(2) );*/


			switch ($type) {

				case 'ui':
					
					$ui = $this->UI();

					$return = array( "menu1"	=> $ui["menu1"], "menu2"	=> $ui["menu2"] );

					break;
			}


			
			
			
			return $return;
		}










		



		public function UI() {
			
			$data = $this->GD->userdata;

			
			if ( $this->GD->is_logged() == true ) {

				$fullname = "";
				
				if ( $data->firstname && $data->lastname )
					$fullname = "<div>".$data->firstname." ".$data->lastname."</div>";

			
				$namee = "<span>- ".strtoupper($data->userEMAIL)." -<span>".$fullname;


				$r = array( "menu1" => '
				<div class="user-menu fl">
					<div class="defarc">
						<div class="menu-loader">
							<div class="user-online">
								<a href="#" class="lfs show-menu p01 jsl">
									<div class="rm-icon"><div class="ii iUser"></div></div>
									<p>'.mb_strtoupper($this->GD->text(105), "utf8").'</p>
								</a>

								<div class="user-interface dH uiL">
									<div class="ui-punt"><div></div></div>

									

									<div class="ui-content">

										<div class="ui-first ui-li">
											<div class="ui-default">
												<a href="'.$this->GD->link(101).'" class="ma"><div class="ii iInvoices"></div>'.$this->GD->text(678).'</a>
											</div>
										</div>
										<div class="ui-second ui-li">
											<div class="ui-default">
												<a href="'.$this->GD->link(102).'" class="ma"><div class="ii iAdress"></div>'.$this->GD->text(679).'</a>
											</div>
										</div>
										<div class="ui-second ui-li">
											<div class="ui-default">
												<a href="'.$this->GD->link(103).'" class="ma"><div class="ii iSettings"></div>'.$this->GD->text(680).'</a>
											</div>
										</div>
										<div class="cleaner"></div>
									</div>

									<div class="ui-logout">
										<a href="#" class="menu-action ma jsl" name="logout">'.mb_strtoupper($this->GD->text(104), "UTF8").'</a>
										<div class="cleaner"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				', "menu2" => '

					<a href="#" class="show-menu ui-tools i i17 bh jsl"></a>

					<div class="user-interface dH uim uiL">
						<div class="ui-punt"></div>

						<div class="ui-content">
							<div class="ui-default">
								<div class="ui-he tac">'.strtoupper($data->userEMAIL).'</div>
								<a href="'.$this->GD->link(100).'" class="ma fc-show jsl" name="settings"><div class="i i26"></div>'.$this->GD->text(103).'</a>
								<a href="#" class="menu-action ma jsl" name="logout"><div class="i i25"></div>'.$this->GD->text(104).'</a>
							</div>
						</div>

						<div class="ui-logout">
							<a href="#" class="menu-action ma jsl" name="logout"><div class="i i255"></div>'.$this->GD->text(104).'</a>
						</div>
					</div>
				');

			} else {

				$r = array( "menu1" => '
				<div class="user-default">
					<a href="'.$this->GD->link(3).'" class="fc-show jsl" name="login" title="'.$this->GD->text(168).'">'.$this->GD->text(58).'<div class="brk"></div></a>
				</div>
				', "menu2" => '

					<a href="#" class="show-menu ui-tools i i17 bh jsl"></a>

					<div class="user-interface dH uim uiL">
						<div class="ui-punt"></div>

						<div class="ui-content">
							<div class="ui-default">
								<a href="'.$this->GD->link(3).'" class="fc-show jsl" name="login" title="'.$this->GD->text(168).'">'.$this->GD->text(58).'</a>
								<a href="'.$this->GD->link(2).'" class="fc-show jsl" name="signup" title="'.$this->GD->text(169).'">'.$this->GD->text(59).'</a>
							</div>
						</div>
					</div>

					<div class="cleaner"></div>
				');

				/*
				$r = array( "menu1" => '
				<div class="user-default">
					<a href="'.$this->GD->link(3).'" class="fc-show jsl" name="login" title="'.$this->GD->text(168).'">'.$this->GD->text(58).'<div class="brk"></div></a><a href="'.$this->GD->link(2).'" class="fc-show jsl" name="signup" title="'.$this->GD->text(169).'">'.$this->GD->text(59).'</a>
				</div>
				', "menu2" => '

					<a href="#" class="show-menu ui-tools i i17 bh jsl"></a>

					<div class="user-interface dH uim uiL">
						<div class="ui-punt"></div>

						<div class="ui-content">
							<div class="ui-default">
								<a href="'.$this->GD->link(3).'" class="fc-show jsl" name="login" title="'.$this->GD->text(168).'">'.$this->GD->text(58).'</a>
								<a href="'.$this->GD->link(2).'" class="fc-show jsl" name="signup" title="'.$this->GD->text(169).'">'.$this->GD->text(59).'</a>
							</div>
						</div>
					</div>

					<div class="cleaner"></div>
				');
				*/
			}

			return $r;
		}




	}

