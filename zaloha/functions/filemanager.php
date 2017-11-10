<?php
	$FM = new FILEMANAGER("");

	class FILEMANAGER {
		
		public static $instance;

		protected $mysql;
		protected $GD;
		protected $editor;
		protected $files;
		protected $diyID;

		private $user;
		private $userID;

		private $result = "";
		private $err = false;

		public $table;
		private $identificator;

		public function __construct( $files = "" ) {
			$this->mysql = SQL::init();
			$this->GD = GLOBALDATA::init();
			

			if ( isset($_COOKIE["DIY"]) )
				$this->diyID = $_COOKIE["DIY"];

			$this->files = $files;

			$this->table = $this->product_location();

			if ( $this->table == "navody" ) {
				//$this->identificator = "url";
				$this->identificator = "system_id";
			} else {
				$this->identificator = "system_id";
			}



			$this->watermarks = array(
				//0 => array( 'wm' => WATERMARK, 'pos' => 1 ),
				1 => array( 'wm' => WATERMARK_URL, 'pos' => 4 )
			);

			//2 => array( 'wm' => WATERMARK_LOGO, 'pos' => 5 )
		}

		public static function init() {
			if( is_null(self::$instance) ) {
				self::$instance = new FILEMANAGER();
			}

			return self::$instance;
		}

		public function verify_upload( $result="" ) {
			
			if ( $this->GD->is_logged() == true ) {
				if ( $this->GD->is_inactive() == false ) {

					if ( $this->files ) {

						if ( $result = $this->verify() ) {
							//$this->upload();
						}

					} else {
						$result = '<div class="tac">'.$this->GD->text(180).'</div>';
					}


					return $result;
				}
			}
		}






		public function verify() {
			$r = $data = "";
			$avaiablePhotos = MAX_FILES - $this->count_alreadyfiles();

			$FD = $this->GD->FD();

			$galeryid = !isset($_COOKIE["DIY"]) ? strtoupper( $this->GD->random_chars_oddo(10, 20) ) : $_COOKIE["DIY"];
			$dir = $this->GD->random_chars_from_group($galeryid, 10);

			foreach ($this->files as $key => $value) {
				$re = "";

				if ( !in_array( strtolower($_FILES[$key]["type"]), $FD["img-types"]) ) {
					$re = sprintf( $this->GD->text(179), $_FILES[$key]["name"] );
				}

				if ( $_FILES[$key]["size"] > FILE_MAXSIZE ) {
					$re = sprintf( $this->GD->text(178), $this->GD->file_size( FILE_MAXSIZE ) );
				}

				if ( $this->count_alreadyfiles() >= MAX_FILES || !$avaiablePhotos )
					$re = sprintf( $this->GD->random_text( $arrayName = array($this->GD->text(250), $this->GD->text(255), $this->GD->text(256)) ), MAX_FILES );

			
				if ( $re ) {
					
					$system[$key] = "no";
					$client[$key] = $re;
					
					$data .=  '
						<div class="file-error">
							<div><strong>'.$_FILES[$key]["name"].'</strong></div>
							<div>'.$this->GD->file_size( $_FILES[$key]["size"] ).'</div>
							<span>'.$re.'</span>
						</div>';
				}
				else { 
					
					$client[$key] = $system[$key] = "OK";
					$system[$key] = "OK";

					$data .=  '
						<div class="file-uploaded">
							<div><strong>'.$_FILES[$key]["name"].'</strong></div>
							<div>'.$this->GD->file_size( $_FILES[$key]["size"] ).'</div>
							<span>'.$this->GD->random_text( $arrayName = array($this->GD->text(251), $this->GD->text(257), $this->GD->text(258), $this->GD->text(259)) ).'</span>
						</div>';

					//$dir = $this->GD->random_chars_from_group($galeryid, 10);

					$this->upload( $_FILES[$key], $galeryid, $dir );
				}
			}

			return $data;
		}


		public function upload($newfile, $galeryid, $dir, $newFiles = "") {
			
			$folder = AJAX_PHYS_ADRESS_TO_FILES.$dir."/";

			if ( !file_exists($folder) ) $this->create_folder($folder);

			$time 				= time();
			$file_name          = strtolower($newfile['name']);
			$file_ext           = substr($file_name, strrpos($file_name, '.'));
			
			$filename      		= strtoupper( $this->GD->random_chars(10) );
			
			$filename_new       = $filename.$file_ext;

			if ( move_uploaded_file($newfile['tmp_name'], $folder.$filename_new) ) {
				$newFiles .= "#".$this->last_id_files();
				$fileType = substr($file_ext, 1);

				$this->mysql->query("INSERT INTO files VALUES (NULL, '$galeryid', ".$this->GD->U->userID.", '$dir', '$filename', '$file_name', '$fileType', $time)");

				if ( $this->upravObrazok($folder, array($newfile["type"]), $filename, $file_ext, FILE_WIDTH, FILE_HEIGHT) ) {
					$bbb = $folder."/".$filename.$file_ext;
			
					$this->add_watermark($bbb, $newfile["type"]);
				}
			} else {
				//echo ('error uploading File!');
			}
			

			$this->update_concept_files( $this->GD->U->userID, $galeryid, $newFiles );
		}

		public function upravObrazok($folder, $fileInfo, $newName, $ext, $maxWidth, $maxHeight) {
			$filename = $folder."/".$newName.$ext;
		
			switch ($fileInfo[0]) {
				case 'image/jpeg':
				case 'image/jpg':
					$image = imagecreatefromjpeg($filename);
					break;
				case 'image/png':
					$image = imagecreatefrompng($filename);
					break;
			}
			
			header('Content-Type: '.$fileInfo[0]);


			// Get sizes
			list($width, $height) = getimagesize($filename);

			//pomer index
			$pomer_sirka = $width / $maxWidth;
			$pomer_vyska = $height / $maxHeight;

			
			if ($pomer_sirka > $pomer_vyska)
				$pomer = $pomer_sirka;
			else
				$pomer = $pomer_vyska;

			if ($pomer < 1)
				$pomer = 1; 

			//new size
			$newWidth = (int)$width / $pomer;
			$newHeight = (int)$height / $pomer;

			// Resize
			$image_p = imagecreatetruecolor($newWidth, $newHeight);

			// Output
			$data = $newName.".jpg";

			
			switch ($fileInfo[0]) {
				case 'image/jpeg':
				case 'image/jpg':
					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					
					imagejpeg($image_p, $folder."/".$data, FILE_QUALITY);
					imagedestroy($image);

					return true;
					break;

				case 'image/png':
					imagealphablending( $image_p, false );
					imagesavealpha( $image_p, true );

					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					
					imagejpeg($image_p, $folder."/".$data, FILE_QUALITY);
					imagedestroy($image);

					return true;
					break;
			}
		}

		public function add_watermark( $ima, $type ) {

			foreach ( $this->watermarks as $key => $v) {
				
				$stamp = imagecreatefrompng( $v["wm"] );

				switch ($type) {
					case 'image/jpeg':
					case 'image/jpg':
						$image = imagecreatefromjpeg($ima);
						break;
					case 'image/png':
						$image = imagecreatefrompng($ima);
						break;
				}

				header('Content-type: '.$type);
				
				$marge_right = 20;
				$marge_bottom = 20;

				$sx = imagesx($stamp);
				$sy = imagesy($stamp);
				

				switch ( $v["pos"] ) {
					case 1:
						imagecopy($image, $stamp, $marge_right, $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
						break;
					case 2:
						imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
						break;
					case 3:
						imagecopy($image, $stamp, $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
						break;
					
					case 4:
					default:
						imagecopy($image, $stamp, imagesx($image) - $sx - $marge_right, imagesy($image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
						break;

					case 5:
						$x = (imagesx($image) - $sx) / 2;
						$y = (imagesy($image) - $sy) / 2;

						imagecopy($image, $stamp, $x, $y, 0, 0, imagesx($stamp), imagesy($stamp));
						break;
				}

				switch ($type) {
					case 'image/jpeg':
					case 'image/jpg':
						imagejpeg($image, $ima);
						break;
					case 'image/png':
						imagepng($image, $ima);
						break;
				}
			}

			
			


			$this->add_copyrights_metadata($ima);
		}

		public function add_copyrights_metadata($path, $data = '') {
			$iptc = array(
				'2#116' => 'http://monamade.sk'
			);

			foreach($iptc as $tag => $string) {
				$tag = substr($tag, 2);
				$data .= $this->iptc_make_tag(2, $tag, $string);
			}

			// Embed the IPTC data
			$content = iptcembed($data, $path);

			// Write the new image data out to the file.
			$fp = fopen($path, "wb");
			fwrite($fp, $content);
			fclose($fp);
		}

		public function iptc_make_tag($rec, $data, $value) {
			$length = strlen($value);
			$retval = chr(0x1C) . chr($rec).chr($data);

			if($length < 0x8000)
				$retval .= chr($length >> 8).chr($length & 0xFF);
			else
				$retval .= chr(0x80).chr(0x04).chr(($length >> 24) & 0xFF).chr(($length >> 16) & 0xFF).chr(($length >> 8) & 0xFF).chr($length & 0xFF);
			
			return $retval.$value;
		}
		
		
		public function pic_remove($removePic, $r = '') {
			
			$stmt = $this->mysql->query("SELECT files, file_list FROM $this->table WHERE $this->identificator = '".$this->diyID."'");

			if ( $oldfiles = $stmt->fetch_object() ) {
				$filedata = $this->fileinfo( $removePic );
				$folder = AJAX_PHYS_ADRESS_TO_FILES.$filedata->folder;



				foreach ( $oldfiles as $key => $value ) {
					
					$updated = str_replace("#".$removePic, "", $value);

					$this->mysql->query("UPDATE $this->table SET $key = '".$updated."' WHERE $this->identificator = '".$this->diyID."'");
				}

				if ( unlink($folder."/".$filedata->filename.".".$filedata->file_type) ) {
					$this->mysql->q("DELETE FROM files WHERE folder = '".$filedata->folder."' AND filename = '".$filedata->filename."'");
					
					$this->delete_empty_folder($folder);
				}
			}

		}

		public function create_folder($data) {
			mkdir($data, 0777, true);
		}

		public function delete_empty_folder($dir) {
			$fi = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
			
			$c = iterator_count($fi);

			if ( $c == 0 ) 
				rmdir($dir);
			
		}


		public function fileinfo($id, $r='') {
			$stmt = $this->mysql->query("SELECT * FROM files WHERE id = ".$id." AND galery_id = '".$this->diyID."'");

			if ( $file = $stmt->fetch_object() ) { 
				return $file;
			}
		}


		public function last_id_files() {
			$stmt = $this->mysql->query("SHOW TABLE STATUS LIKE 'files'");
			$re2 = $stmt->fetch_object();
			
			$r = $re2->Auto_increment;

			return $r;
		}




		public function update_concept_files($user_id, $id, $files) {
			

			$stmt = $this->mysql->query("SELECT * FROM $this->table WHERE $this->identificator = '$id'");

			if ( !$oldfiles = $stmt->fetch_assoc() ) {

				$this->mysql->query("UPDATE $this->table SET files = '$files' WHERE $this->identificator = '$id'");

			} else {
				$news = $oldfiles["files"].$files;
				$news_fileList = $oldfiles["file_list"].$files;

				$this->mysql->query("UPDATE $this->table SET files = '$news', file_list = '$news_fileList' WHERE $this->identificator = '$id'");
			}

		}


		public function count_alreadyfiles($r = '') {
			$stmt = $this->mysql->query("SELECT files, file_list FROM $this->table WHERE $this->identificator = '".$this->diyID."'");

			if ( $data = $stmt->fetch_assoc() ) {

				$alreadyFiles = explode("#", $data["files"]);

				$r = count( array_filter($alreadyFiles) );
			}

			return $r;
		}



		public function product_location($r='') {
			
			$stmt = $this->mysql->query("SELECT id FROM navody WHERE system_id = '".$this->diyID."'");

			if ( $full = $stmt->fetch_assoc() ) {
				$r = "navody";
			} else {
				$stmt1 = $this->mysql->query("SELECT id FROM concepts WHERE system_id = '".$this->diyID."'");

				if ( $concept = $stmt1->fetch_assoc() )
					$r = "concepts";
			}

			return $r;
		}
	}
