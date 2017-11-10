<?php
	class UPLOAD extends GLOBALDATA {
		
		protected $mysql;
		protected $GL;
		//protected $editor;
		protected $files;

		private $result = false;
		private $err = false;
		
		private $oWidth;
		private $oHeight;

		public function __construct( $files ) {
			$this->GL = GLOBALDATA::init();
			$this->mysql = SQL::init();
			//$this->editor = DIYEDITOR::init();

			$this->files = $files;
		}
		
		
//$_COOKIE["DIY"]
		public function verify_upload() {
			
			if ( $this->files ) {

				if ( $verify = $this->verify() ) {
					$result = $this->upload( $verify );
				}

			} else {
				$result = $this->text(180);
			}


			//if ( $this->result )
			return $result;
		}








		public function verify( $r = "" ) {
			$FD = $this->GL->FD();

			foreach ($this->files as $key => $value) {
				$re = "";

				if ( !in_array( strtolower($_FILES[$key]["type"]), $FD["img-types"]) ) {
					$re[] = sprintf( $this->text(179), $_FILES[$key]["name"] );
				}

				if ( $_FILES[$key]["size"] > $FD["max-upload"] ) {
					$re[] = sprintf( $this->text(178), $_FILES[$key]["name"] );
				}

				if ( $re ) {
					$client[$key] = $re;
					$system[$key] = "no";
				}
				else { 
					$client[$key] = $system[$key] = "ok";
				}
			}

			$this->uploadQuery = $system;

			return $client;
		}


		public function upload($verifyData, $newFiles = "", $uploaded = "") {
			$user = $this->GL->userdata;

			if ( !isset($_COOKIE["DIY"]) )
				$galeryid = strtoupper( $this->random_chars_oddo(10, 20) );
			else
				$galeryid = $_COOKIE["DIY"];

			$dir = $this->random_chars_from_group($galeryid, 10);



			$folder = UPLOAD_FOLDER.$dir."/";

			if ( !file_exists($folder) ) $this->create_folder($folder);

			foreach ($this->files as $key => $value) {

				if ( $this->uploadQuery[$key] == "ok" ) {

					$time 				= time();
					$file_name          = strtolower($_FILES[$key]['name']);
					$file_ext           = substr($file_name, strrpos($file_name, '.'));
					
					$filename      		= strtoupper( $this->random_chars(10) );
					
					$filename_new        = $filename.$file_ext;

					if ( $info = move_uploaded_file($_FILES[$key]['tmp_name'], $folder.$filename_new) ) {
						
						$newFiles .= "#".($this->last_id_files() + 1);
						$fileType = substr($file_ext, 1);

						$this->mysql->query("INSERT INTO files VALUES (NULL, '$galeryid', ".$user["userID"].", '$dir', '$filename', '$file_name', '$fileType', $time)");
						

						$this->setImage( $folder.$filename_new );

						$this->oWidth = imagesx( $this->image );
						$this->oHeight = imagesy( $this->image );

						$resize = new ResizeImage( $folder.$filename_new );

						if ( $this->oWidth <= 800 && $this->oHeight <= 800 )
							$resize->resizeTo( $this->oWidth, $this->oHeight );
						else {
							$resize->resizeTo(800, 800);
						}

						$resize->saveImage( $folder.$filename_new );

						$uploaded[$key] = array(
							'name' 		=> $_FILES[$key]['name'],
							'width'		=> $this->oWidth,
							'height'	=> $this->oHeight );
					}

				} else {
					$uploaded[$key] = array(
							'name' 		=> $_FILES[$key]['name'],
							'error'		=> implode("#", $verifyData[$key]) );
				}
			}


			$this->update_filedata_after_upload( $user["userID"], $newFiles );
			$this->update_concept_files( $user["userID"], $galeryid, $newFiles );

			return $uploaded;
		}


		public function create_folder($data) {
			mkdir($data, 0777, true);
		}




		public function update_filedata_after_upload($user_id, $newFiles) {
			
			$stmt = $this->mysql->query("SELECT * FROM files_userlists WHERE user = $user_id");

			if ( !$oldfiles = $stmt->fetch_assoc() ) {
				//$unused_files = $newFiles
				$this->mysql->query("INSERT INTO files_userlists VALUES (NULL, ".$user_id.", '$newFiles', '$newFiles', '')");

			} else {
				$news = $oldfiles["files"].$newFiles;
				$unused_files = $oldfiles["unused_files"].$newFiles;

				$this->mysql->query("UPDATE files_userlists SET files = '$news', unused_files = '$news' WHERE user = $user_id");
				$this->mysql->query("UPDATE files_userlists SET unused_files = '$unused_files' WHERE user = $user_id");
			}

		}



		public function last_id_files() {
			
			$stmt = $this->mysql->query("SELECT id FROM files ORDER BY id DESC LIMIT 1");
			
			if ( $r = $stmt->fetch_assoc() )
				return $r["id"];
			else 
				return 1;
		}




		public function update_concept_files($user_id, $id, $files) {
			

			$stmt = $this->mysql->query("SELECT * FROM concepts WHERE system_id = '$id' AND user_id = $user_id");

			if ( !$oldfiles = $stmt->fetch_assoc() ) {

				$this->mysql->query("UPDATE concepts SET files = '$files' WHERE system_id = '$id' AND user_id = $user_id");

			} else {
				$news = $oldfiles["files"].$files;
				$news_fileList = $oldfiles["file_list"].$files;

				$this->mysql->query("UPDATE concepts SET files = '$news', file_list = '$news_fileList' WHERE system_id = '$id' AND user_id = $user_id");
			}

		}

		private function setImage( $filename )
		{
			$size = getimagesize($filename);
			$this->ext = $size['mime'];

			switch($this->ext)
		    {
		    	// Image is a JPG
		        case 'image/jpg':
		        case 'image/jpeg':
		        	// create a jpeg extension
		            $this->image = imagecreatefromjpeg($filename);
		            break;

		        // Image is a GIF
		        case 'image/gif':
		            $this->image = @imagecreatefromgif($filename);
		            break;

		        // Image is a PNG
		        case 'image/png':
		            $this->image = @imagecreatefrompng($filename);
		            break;

		        // Mime type not found
		        default:
		            throw new Exception("File is not an image, please use another file type.", 1);
		    }

		    $this->origWidth = imagesx($this->image);
		    $this->origHeight = imagesy($this->image);
		}
	}







	class ResizeImage
	{
		private $ext;
		private $image;
		private $newImage;
		private $origWidth;
		private $origHeight;
		private $resizeWidth;
		private $resizeHeight;

		/**
		 * Class constructor requires to send through the image filename
		 *
		 * @param string $filename - Filename of the image you want to resize
		 */
		public function __construct( $filename )
		{
			if(file_exists($filename))
			{
				$this->setImage( $filename );
			} else {
				throw new Exception('Image ' . $filename . ' can not be found, try another image.');
			}
		}

		/**
		 * Set the image variable by using image create
		 *
		 * @param string $filename - The image filename
		 */
		private function setImage( $filename )
		{
			$size = getimagesize($filename);
			$this->ext = $size['mime'];

			switch($this->ext)
		    {
		    	// Image is a JPG
		        case 'image/jpg':
		        case 'image/jpeg':
		        	// create a jpeg extension
		            $this->image = imagecreatefromjpeg($filename);
		            break;

		        // Image is a GIF
		        case 'image/gif':
		            $this->image = @imagecreatefromgif($filename);
		            break;

		        // Image is a PNG
		        case 'image/png':
		            $this->image = @imagecreatefrompng($filename);
		            break;

		        // Mime type not found
		        default:
		            throw new Exception("File is not an image, please use another file type.", 1);
		    }

		    $this->origWidth = imagesx($this->image);
		    $this->origHeight = imagesy($this->image);
		}

		/**
		 * Save the image as the image type the original image was
		 *
		 * @param  String[type] $savePath     - The path to store the new image
		 * @param  string $imageQuality 	  - The qulaity level of image to create
		 *
		 * @return Saves the image
		 */
		public function saveImage($savePath, $imageQuality="80", $download = false)
		{
		    switch($this->ext)
		    {
		        case 'image/jpg':
		        case 'image/jpeg':
		        	// Check PHP supports this file type
		            if (imagetypes() & IMG_JPG) {
		                imagejpeg($this->newImage, $savePath, $imageQuality);
		            }
		            break;

		        case 'image/gif':
		        	// Check PHP supports this file type
		            if (imagetypes() & IMG_GIF) {
		                imagegif($this->newImage, $savePath);
		            }
		            break;

		        case 'image/png':
		            $invertScaleQuality = 9 - round(($imageQuality/100) * 9);

		            // Check PHP supports this file type
		            if (imagetypes() & IMG_PNG) {
		                imagepng($this->newImage, $savePath, $invertScaleQuality);
		            }
		            break;
		    }

		    if($download)
		    {
		    	header('Content-Description: File Transfer');
				header("Content-type: application/octet-stream");
				header("Content-disposition: attachment; filename= ".$savePath."");
				readfile($savePath);
		    }

		    imagedestroy($this->newImage);
		}

		/**
		 * Resize the image to these set dimensions
		 *
		 * @param  int $width        	- Max width of the image
		 * @param  int $height       	- Max height of the image
		 * @param  string $resizeOption - Scale option for the image
		 *
		 * @return Save new image
		 */
		public function resizeTo( $width, $height, $resizeOption = 'default' )
		{
			switch(strtolower($resizeOption))
			{
				case 'exact':
					$this->resizeWidth = $width;
					$this->resizeHeight = $height;
				break;

				case 'maxwidth':
					$this->resizeWidth  = $width;
					$this->resizeHeight = $this->resizeHeightByWidth($width);
				break;

				case 'maxheight':
					$this->resizeWidth  = $this->resizeWidthByHeight($height);
					$this->resizeHeight = $height;
				break;

				default:
					if($this->origWidth > $width || $this->origHeight > $height)
					{
						if ( $this->origWidth > $this->origHeight ) {
					    	 $this->resizeHeight = $this->resizeHeightByWidth($width);
				  			 $this->resizeWidth  = $width;
						} else if( $this->origWidth < $this->origHeight ) {
							$this->resizeWidth  = $this->resizeWidthByHeight($height);
							$this->resizeHeight = $height;
						}  else {
							$this->resizeWidth = $width;
							$this->resizeHeight = $height;	
						}
					} else {
			            $this->resizeWidth = $width;
			            $this->resizeHeight = $height;
			        }
				break;
			}

			$this->newImage = imagecreatetruecolor($this->resizeWidth, $this->resizeHeight);
	    	imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->origWidth, $this->origHeight);
		}

		/**
		 * Get the resized height from the width keeping the aspect ratio
		 *
		 * @param  int $width - Max image width
		 *
		 * @return Height keeping aspect ratio
		 */
		private function resizeHeightByWidth($width)
		{
			return floor(($this->origHeight/$this->origWidth)*$width);
		}

		/**
		 * Get the resized width from the height keeping the aspect ratio
		 *
		 * @param  int $height - Max image height
		 *
		 * @return Width keeping aspect ratio
		 */
		private function resizeWidthByHeight($height)
		{
			return floor(($this->origWidth/$this->origHeight)*$height);
		}
	}