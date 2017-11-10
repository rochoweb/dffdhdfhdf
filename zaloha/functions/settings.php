<?php
	$SET = new SETTINGS();

	class SETTINGS {
		
		protected $m;
		protected $GD;
		
		private $user;
		private $userID;

		public $U;
		//public $shopID;

		public function __construct() {
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
			$this->m = SQL::init();

		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new SETTINGS();
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


		public function generate_submenu($ajax_page = "", $r = "") {
			
			$filters = array( 	0 	=> array("page" => 71, "text" => 363, "head" => 270, 'content' => 'gen_settings' )
							 	);

			foreach ($filters as $key => $value) {
				
				//$stmt = $this->m->q("SELECT count(id) as total FROM ".$value["q"]);
				//$re = $stmt->fetch_object();

				$title = mb_strtoupper($this->GD->text($value["text"]), "UTF8");

				$P = $ajax_page ? $ajax_page : $this->CHR->CHECK_SUBPAGE("page", $ajax_page);

				if ( $P == $value["page"] ) {

					$r .= '<a href="'.$this->GD->link($value["page"]).'" id="ns-A">'.$title.'</a>';
				}
				else {

					$r .= '<a href="'.$this->GD->link($value["page"]).'">'.$title.'</a>';
				}

				$body = $this->$value['content']();
			}

			return '
				<div class="default-step-menu">
					<div class="nsm-content">
						'.$r.'
					</div>

					<div class="cleaner"></div>
				</div>

				<div class="default-step-body">
					'.$body.'
				</div>
				';
		}




		public function gen_settings($r = '') {

			$stmt = $this->m->q("SELECT category FROM settings WHERE id = 1");

			if ( $re1 = $stmt->fetch_object() ) {

				foreach ( array_filter( explode('#', $re1->category) ) as $key => $value) {
					$stmt2 = $this->m->q("SELECT category, text FROM ".T_CATEGORY." WHERE category = '$value'");

					$data[] = $stmt2->fetch_object();
				}
			}
			else {
				$this->m->q("SELECT * FROM ".T_CATEGORY);
				$data = $this->m->resultO();
			}

			foreach ($data as $key => $v) {
				$stmt = $this->m->q("SELECT COUNT(id) as total FROM ".T_ITEMS." WHERE category = '".$v->category."' AND public = 1");
				$count = $stmt->fetch_object();

				if ( $count->total != 0 ) {
					$c = ' class="yy"';
					$t = '<strong>['.$count->total.']</strong>';
				} else
					$c = $t = '';
				

				$r .= '<li id="'.$v->category.'"><span'.$c.'>'.$this->GD->text($v->text).' '.$t.'</span></li>';
			}

			return '

			<div class="set-content">
				<div class="set-head">
					<div><strong>'.$this->GD->text(365).'</strong></div>
					<p>'.$this->GD->text(366).'</p>
				</div>
				
				<div class="set-body">
					<div class="movi mi-def">
						<ul class="moul">'.$r.'</ul>
					</div>
				</div>

				<div class="set-info">
					<p><span class="ii iWarn"></span>'.$this->GD->text(367).'</p>
				</div>
			</div>
			
			';
		}
	}

