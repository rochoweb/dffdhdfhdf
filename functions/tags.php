<?php
	$TAGS = new TAGS();



	class TAGS {
		
		protected $mysql;
		protected $GD;
		
		public function __construct() {
			$this->mysql = SQL::init();
			$this->CHR = CHR::init();
			$this->GD = GLOBALDATA::init();
		}
		
		public function list_of_tags() {
			
			$r1 = $this->tags_body( array('a', 'd', 'g', 'j', 'm', 'p', 's', 'v', 'y') );
			$r2 = $this->tags_body( array('b', 'e', 'h', 'k', 'n', 'q', 't', 'w', 'z') );
			$r3 = $this->tags_body( array('c', 'f', 'i', 'l', 'o', 'r', 'u', 'x') );

			$r4 = $this->tags_body( array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z') );

			return '
				<div class="hashtags">
					<div class="hashtags-default">
						'.$r1.$r2.$r3.'
					</div>
					
					<div class="hashtags-respo">'.$r4.'</div>
					<div class="cleaner"></div>
				</div>';
		}

		public function tags_body($letters, $r = "") {
			foreach ($letters as $key => $value) {
				$tags = $this->select_tags($value);

				

				if ( $tags["tags"] )
					$opacity = "tk";
				else
					$opacity = "tnone";

				//if ( $tags ) {
						$r .= '
					<div class="hashtag-line '.$opacity.'">
						<div class="ht-head">
							<div class="hth"><span>'.strtoupper($value).'</span></div>
						</div>
						<div class="ht-body">
							'.$tags["tags"].'
							<div class="cleaner"></div>
						</div>
					</div>';

					
				//}
			}

			return $return = '<div class="taglist">'.$r.' <div class="cleaner"></div> </div>';
		}

		public function select_tags($letter, $r = "") {
			//$PD = $this->mm->pagedata();

			$stmt = $this->mysql->query("SELECT * FROM tagy WHERE ".$this->CHR->LANG." REGEXP '^[$letter].*$'");


			foreach ($this->mysql->result2() as $key => $value) {
				//var_dump($key);
				$count = $this->diy_count($value["id"]);

				$r .= '<a href="'.$this->GD->link(8)."/".$value[ $this->CHR->LANG ].'" class="l">'.ucfirst( $this->GD->text($value["id_text"]) ).' <span class="c-h" title="'.sprintf($this->GD->text(123), ucfirst( $this->GD->text($value["id_text"]) )).'"> &times; '.$count.'</span> </a>';
			}



			return array('tags' => $r);
		}


		public function diy_count($tagid, $count = 0) {
			
			$stmt = $this->mysql->query("SELECT * FROM navody WHERE tags LIKE '%#$tagid#%' ORDER BY create_date DESC");

			foreach ($this->mysql->result2() as $key => $value) {
				$count += 1;
			}

			return $count;
		}

		//SELECT Name FROM Employees WHERE Name REGEXP '^[B].*$'
	}

