<?php
	
	class ACTIONS {
		
		protected $mysql;
		protected $GD;
		protected $type;
		
		public function __construct($type) {
			$this->GD = GLOBALDATA::init();
			$this->mysql = SQL::init();

			$this->type = $type;

		}

		public function action($return = "") {
			//global $GD;
			switch ($this->type) {

				/*ase 'subscribe':
					$return = '

					 <div class="subs-head">
					 	<img src="/pics/logo/mm3.png">
					 	<span>'.$this->GD->text( $this->GD->random_text( array(40, 41) ) ).'</span>
					 </div>

					 <div class="subs-body">
					 	
					 	<div class="subs-form">
					 		<div class="subs-input">
					 			<input type="text" class="ss-text" id="subs-email">
					 			<label for="subs-email"><span>'.$this->GD->text(67).'</span></label>
					 			<div class="ss-icon i i09"></div>
					 		</div>

					 		<div class="subs-input ssAd">
					 			<input type="text" class="ss-text" id="subs-category">
					 			<label for="subs-category"><span>'.$this->GD->text(66).' <i>- '.$this->GD->text(68).' -</i></span></label>
					 			<div class="ss-icon i i10"></div>
					 		</div>

					 		<div class="subs-send ss-s al">
					 			<div>
					 				<button type="button" class="ss-send">'.$this->GD->text(65).'</button>
				 				</div>
					 		</div>
					 	</div>
					 </div>
					';
					break;
*/
/*				case 'signup':
				//<div class="input-desc"><p>'.$this->GD->text(70).'</p><div></div></div>
					$return = '
					<div class="fc-def">
						<div class="utools-head">
							<div> <span>'.$this->GD->text(42).'</span> </div>
						</div>

						<div class="utools-body">
							<form>
								'.$this->GD->generate_inputs(503).'
								<div class="subs-send ss-s al">
									<div>
										<button type="button" class="ss-send l" id="acc-signup">'.$this->GD->text(55).'</button>
									</div>
								</div>
							</form>
						</div>
					</div>

					<div class="utools-footer">
						<div> '.$this->GD->text(47).' <a href="'.$this->GD->link(3).'" class="fc-show l" name="login">'.$this->GD->text(48).'</a> </div>
					</div>

					<div class="signup-sucess">
					</div>
					';
					break;
*/
				case 'login':
					$return = '
					<div class="fc-def">
						<div class="utools-head">
							<div> <span>'.$this->GD->text(43).'</span> </div>
						</div>

						<div class="utools-body">
							<form>
							 	'.$this->GD->generate_inputs(100).'

								<div class="subs-send ss-s al">
									<div>
										<button type="button" class="ss-send" id="acc-login">'.$this->GD->text(49).'</button>
									</div>
								</div>
							</form>
						</div>
					</div>


					';

					/*
					$return = '
					<div class="fc-def">
						<div class="utools-head">
							<div> <span>'.$this->GD->text(43).'</span> </div>
						</div>

						<div class="utools-body">
							<form>
							 	'.$this->GD->generate_inputs(100).'

								<div class="subs-send ss-s al">
									<div>
										<button type="button" class="ss-send" id="acc-login">'.$this->GD->text(49).'</button>
									</div>
								</div>

								<div class="login-forgot">
									<a href="#" class="fc-show l" name="forgot">'.$this->GD->text(50).'</a> '.$this->GD->text(51).'
								</div>
							</form>
						</div>
					</div>
						<div class="utools-footer">
							<div> '.$this->GD->text(44).'<a href="'.$this->GD->link(2).'" class="fc-show l" name="signup">'.$this->GD->text(45).'</a></div>
						</div>

					';

*/
					break;

				case 'forgot':
					$return = '
					<div class="fc-def">
						<div class="utools-head">
							<div> <span>'.$this->GD->text(52).'</span> </div>
							<p>'.$this->GD->text(54).'</p>
						</div>

						<div class="utools-body">
							<div class="subs-input">
								<input type="text" class="ss-text" id="forgot-email">
								<label for="forgot-email"><span>'.$this->GD->text(64).'</span></label>
								<div class="ss-icon i i13"></div>
								<div class="input-desc"><p>'.$this->GD->text(73).'</p><div></div></div>
							</div>

							<div class="subs-send ss-s al">
								<div>
									<button type="button" class="ss-send" id="acc-forgot">'.$this->GD->text(53).'</button>
								</div>
							</div>
						</div>
					</div>

					<div class="utools-footer">
						<div> <a href="#" class="fc-show l" name="login">'.$this->GD->text(49).'</a> </div>
					</div>
					';
					break;

				case 'search':
					$button = '<button type="submit" class="subs-input-send is">'.$this->GD->text(158).'</button>';

					$return = '
					<div class="fc-def">
						<div class="utools-head"><div><span>'.$this->GD->text(75).'</span></div></div>

						<form method="get" action="'.$this->GD->link(4).'/" class="search-submit">
							'.$this->GD->generate_inputs(501, false, "", $button, true).'
							<!--<div class="utools-body">
								<div class="subs-input gS">
										<input type="text" name="q" class="ss-text global-search" id="global-search">
										<label for="global-search"><span>'.$this->GD->text(74).'</span></label>
										<div class="ss-icon ii iZoom"></div>
										<button type="reset" class="input-reset i i08" title="'.$this->GD->text(76).'"></button>
										<div class="input-desc"><p>'.$this->GD->text(77).'</p><div></div></div>
									
								</div>
							</div>
							-->
						</form>
					</div>

					<div class="search-result"></div>
					';
					break;

				/*case 'settings':
					$UID = $this->USERDATA();

					$return = '
					<div class="fc-def">
						<div class="utools-head">
							<div> <span>'.$this->GD->text(106).'</span> </div>
						</div>

						<div class="utools-body settings-ui">

							<div class="subs-input">
								<label for="settings-email"><span>'.$this->GD->text(64).'</span></label>
								<input type="text" class="ss-text" id="settings-email" value="'.$UID["userEMAIL"].'">
								<div class="ss-icon i i13"></div>
								<div class="resultbar"></div>
							</div>
							

							<div class="subs-input">
								<label for="settings-firstname"><span>'.$this->GD->text(107).'</span></label>
								<input type="text" class="ss-text" id="settings-firstname" value="'.$UID["firstname"].'">
								
								<div class="ss-icon"></div>
								<div class="resultbar"></div>
							</div>


							<div class="subs-input">
								<label for="settings-lastname"><span>'.$this->GD->text(108).'</span></label>
								<input type="text" class="ss-text" id="settings-lastname" value="'.$UID["lastname"].'">
								
								<div class="ss-icon"></div>
								<div class="resultbar"></div>
							</div>

								

							<div class="subs-input">
								<label for="settings-gender"><span>'.$this->GD->text(109).'</span></label>
								<input type="text" class="ss-text show-select" id="settings-gender" value="'.$this->gender($UID["gender"]).'" readonly>
								
								<div class="ss-icon i i32"></div>
								<div class="resultbar"></div>
								<div class="select-menu dH">
									<a href="#" class="gender-type l" name="'.$this->GD->text(113).'">'.$this->GD->text(113).'</a>
									<a href="#" class="gender-type l" name="'.$this->GD->text(114).'">'.$this->GD->text(114).'</a>
									<a href="#" class="gender-type l" name="">'.$this->GD->text(116).'</a>
								</div>
							</div>

							<div class="cleaner"></div>
						</div>
					</div>
					';
					break;

*/
				/*case 'imguploader':
					$return = '
					
					<div class="uploader-content">
						<div class="uploader-head"><strong>'.$this->GD->text(252).'</strong><p>'.$this->GD->text(253).'</p></div>
						<form action="'.$this->url_data("temp/upload.php").'" id="uploadData" method="POST" enctype="multipart/form-data">
							<div class="uploader-body dropArea">
								<span class="uploadarea-icon i i110"></span>
								<span class="uploadarea-title">'.$this->GD->text(171).'</span>
								<span class="alternative-upload">
									<span class="alternative-upload-head">'.$this->GD->text(172).'</span>
									<button type="button" class="alternative-upload-button et-a">'.$this->GD->text(175).'</button>
								</span>

								<div class="upload-info">'.$this->GD->text(254).'<p>'.$this->GD->text(260).'</p></div>
							</div>

							<div class="uploader-previews"></div>

							<input type="file" id="fileUploader" name="filess[]" multiple="multiple" accept="image/x-png, image/gif, image/jpeg" value="">
						</form>

						<div class="uploader-footer">
							<button type="button" class="uploader-upload et-a fr">'.$this->GD->text(176).'</button>
							<button type="button" class="uploader-close et-a fr">'.$this->GD->text(177).'</button>
							<div class="cleaner"></div>
						</div>

						<div class="upload-loading">
							<img src="/pics/icons/load4.gif">
						</div>
					</div>
					';
					break;*/
			}

			return $return;
		}

		public function windowsSize() {
			switch ($this->type) {
				case 'search':
				case 'subscribe':
					$r = $this->type;
					break;
				default:
					$r = "default";
					break;
			}

			return $r;
		}
	}