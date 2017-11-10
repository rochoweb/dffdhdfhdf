<?php
	date_default_timezone_set("Europe/Bratislava");
	/*

    #######################################|
    ################ CONFIG ###############:)  
    #######################################|

    -->  Autor:		RoCHo
    -->  Verzia:	1.0.0 / 2015
    
	--------------------------------------------------------------------------------------------


    ########                       #########     ####**   ###                   ######  ##   ##
    #########                     ###########    ####*   ####                     ##    #######
    ####//####      ########     #####    ###   *####    ####     ########        ##   ##  #  ##
    ####//####    ############   ####      #   **#####**#####   ############      **   **     **
    #########     ####////####   ###          ***############   ####////####	    proDuction
    ########      ####////####   ###         ****#####**####   ####////####
    ####  ####    ####////####   ####       ***  ####    ####   ####////####
    ####   ####   ############   #############   ####    ####   ############
    ####    ####    ########      ##########     ###*    *###     ########
  	

  	--------------------------------------------------------------------------------------------

	*/
  	
  	

	# MySQL -> 

  		if ( $LEVEL != 0 ) {
  			require_once ("./config/defines.php");
			require_once ("./config/db.php");
			require_once ("./config/chr.php");

			require_once ("./functions/sessions.php");
			require_once ("./functions/global.php");

			require_once ("./functions/filters.php");
			require_once ('./functions/email.php');
			require_once ('./functions/basket.php');
			require_once ('./functions/inputs.php');
			require_once ('./functions/account.php');
		}

	# <- MySQL


	if ( DEBUG == true ) {
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
	}



	if ( $LEVEL != 0 ) {
		
		if ( !isset($_COOKIE["mm-lastvisit"]) )
			setcookie("mm-lastvisit", time(), strtotime( COOKIE_LASTVISIT ), "/", $GD->domain());
		else
			setcookie("mm-lastvisit", $_COOKIE["mm-lastvisit"], strtotime( COOKIE_LASTVISIT ), "/", $GD->domain());

		$BA = new BASKET();

		$UI = $ACC->UI();

		$HEADER = array(
			
			'title' 					=> $CHR->PD->title,

			'meta'	=> array(
				'description' 			=> "",
				'keywords' 				=> "",
				'author' 				=> "MONAMADE",
			),

			'link'	=> array(
				'css-main' 				=> $GD->url_data('css/main.css'),
				'css-media'				=> $GD->url_data("css/media.css"),
				//'css-diyeditor'			=> $GD->url_data("css/diyeditor.php"),
				//'css-newshop'			=> $GD->url_data("css/newshop.php"),
				//'css-additem'			=> $GD->url_data("css/additem.php"),

				'css-icons'				=> $GD->suburl_data2("css/icons.css"),
				'css-fa'				=> $GD->suburl_data2("fa/css/font-awesome.min.css"),

				'css-ui'				=> $GD->url_data("css/ui/jquery-ui.min.css"),
				'css-ui-structure'		=> $GD->url_data("css/ui/jquery-ui.structure.min.css"),

				'js-jquery'				=> $GD->suburl_data2("js/jquery.js"),
				'js-plugin-moment' 		=> $GD->suburl_data2("js/plugin_moment.js"),
				'js-plugin-timestamp' 	=> $GD->suburl_data2("js/plugin_timestamp.js"),
				'js-plugin-mobile'		=> $GD->suburl_data2("js/mobile/jquery.mobile-1.4.5.min.js"),
				'js-main'				=> $GD->url_data("js/main.js"),
				'js-diyeditor'			=> $GD->url_data("js/diyeditor.js"),
				/*'js-shopie'				=> $GD->url_data("js/shopie.js"),*/
				'js-basket'				=> $GD->url_data("js/basket.js"),

				'js-ui'					=> $GD->suburl_data2("js/ui/jquery-ui.min.js"),

				'favicon'				=> $GD->url_data("pics/favicon8.png")
			)

		);

		$STRUCTURE = array(
			'head' => "structure/head.php",
			'body' => "structure/body.php",
			'maps' => "structure/maps.php"
		);

		/*switch ($LEVEL) {
			case 1:
				$DIY = new DoItYourself();
				break;

			case 2:
				break;
		}
*/
		$content = './content/'.$CHR->PD->content;



		$html_id = $CHR->PD->html_id ? ' id="p-'.$CHR->PD->html_id.'"' : "";
		
		if ( $CHR->PD->page == 1 )
			$HEADER["title"] = $CHR->PD->title;
		else
			$HEADER["title"] = $CHR->PD->title.DEF_TITLE;

		$HEADER["meta"]["description"] = $GD->text( $CHR->PD->desc );
		$HEADER["meta"]["keywords"] = $GD->text( $CHR->PD->keywords );

		if ( $CHR->PD->logged == 1 && !$GD->online )
			header( "Location: ".$GD->link(4) );

		$GD->check_itemHistory();
		//require_once ('./functions/items.php');
		switch ($CHR->PD->page) {
			case 1:
				//require_once ('/functions/navody.php');

				//$R = $DIY->diy_index();

				require_once ('./functions/items.php');

				$R = $GI->generate_items("index", "", $BA->basket);
				break;

			case 4:	//search
				require_once ('./functions/items.php');
				require_once ('./functions/tags.php');

				if ( isset($_GET["q"]) && strlen($_GET["q"]) > 0 )
					$HEADER["title"] = sprintf($GD->text(130), $_GET["q"]);
				break;

			case 5:		//CATEGORIES
				require_once ('./functions/items.php');

				$data = $GD->category_data_by_name($_GET["p"]);
				$R = $GI->generate_items("category", $data, $BA->basket);

				$HEADER["title"] = !$data->title ? sprintf($CHR->PD->title, $GD->text($data->text).DEF_TITLE ) : $GD->text($data->title).DEF_TITLE;

				$filters = array(
					$GD->filter_order($data),
					$GD->filter_colors($data),
					$GD->filter_price($data),
					$GD->filter_availability($data)
				);

				$HEADER["meta"]["description"] = $GD->text( $data->desc );
				$HEADER["meta"]["keywords"] = $GD->text( $data->keys );

				break;
			/*case 6:	//navody
				require_once ('/functions/media.php');

				$DIY = $MEDIA->DIYDATA();

				$MEDIA->views($DIY["id"]);

				//echo $DIY["id"];
				$MEDIA->calculate_media("hodnotenia", "count_likes");
				$MEDIA->calculate_media("komentare", "count_comments");
				$MEDIA->calculate_media("prezretia", "count_views");
				$DIY["url"];
				break;*/

			case 7:	//tagy
				require_once ('./functions/tags.php');

				$HEADER["meta"]["description"] = $GD->text( $CHR->PD->desc ).$GD->text( 857 );
				break;

			case 8:	//tag
				//$DIY = new DoItYourself();
				require_once ('./functions/items.php');

				$HT = $GD->hashtag_data( $_GET["t"] );

				if ( isset($_GET["t"]) && isset($HT) ) {

					/*if ( isset($HT) )
						$R = $DIY->diy_tag( $HT["id"] );*/
					if ( isset($HT) )
						$R = $GI->generate_items("tags", array("tags" => $HT["id"]), $BA->basket);

					//if ( strlen($_GET["t"]) > 0 )
					//	$HEADER["title"] = sprintf($GD->text(131), ucfirst($GD->text($HT["id_text"])) );
				} else
					header( "Location: ".$GD->link(7) );

				/*if ( !$HT )
					header("Location: ".$GD->link(7) );
				else
					$R = $DIY->diy_tag( $HT["id"] );*/
				$tagname = $GD->text($HT["id_text"]);

				$HEADER["title"] = sprintf($CHR->PD->title, $tagname).DEF_TITLE;
				$HEADER["meta"]["description"] = sprintf($GD->text( $CHR->PD->desc ), $tagname).$GD->text( 857 );
				$HEADER["meta"]["keywords"] = mb_strtolower($tagname, 'UTF8');

				break;

			case 10:	//nastavenia
				if ( !$MM->online )
					$content = "./content/signin.php";
				break;

			case 15:
				require_once ('./functions/user-profil.php');

				$UP = $USERPROFIL->userprofile();

				$HEADER["title"] = sprintf($CHR->PD->title, $UP["userNAME"]);
				break;
			case 16:	//ITEM PROFILE
				require_once ('./functions/media.php');
				require_once ('./functions/items.php');
				require_once ('./functions/item-profil.php');
				
				if ( $IP->IP->public != 1 )
					header( "Location: ".$GD->link(1) );

				$GD->itemHistory( $IP->IP->id );

				//$MEDIA->views( $IP->IP->id );
				//$MEDIA->calculate_media("hodnotenia", "count_likes");
				//$MEDIA->calculate_media("komentare", "count_comments");
				$MEDIA->calculate_media("prezretia", "count_views");

				$HEADER["title"] = sprintf($CHR->PD->title, $GD->mb_ucfirst( $IP->IP->title ).DEF_TITLE );
				$HEADER["meta"]["description"] = $IP->IP->description;
				//$R = $GI->generate_items("itemrelated", array("tags" => $IP->IP->tags, "id" => $IP->IP->id));
				$HEADER["meta"]["keywords"] = $GD->gen_keywords_from_tags( $IP->IP->tags );
				break;

			case 17: //basket
				require_once ('./functions/items.php');
				$CHB = $BA->order_data();
				
				break;

			case 18: //order
				$BA->order_data();

				if ( $BA->check_basket() == true ) {
					if ( $BA->basket->delivery_firstname && $BA->basket->delivery_lastname && $BA->basket->delivery_phone && $BA->basket->delivery_street && $BA->basket->delivery_city && $BA->basket->delivery_zip )
						$adress = preg_replace('/[^0-9]+/', '', $BA->basket->delivery_street).', '.$BA->basket->delivery_zip.' '.$BA->basket->delivery_city.', Slovakia';
					else
						$adress = preg_replace('/[^0-9]+/', '', $BA->basket->billing_street).', '.$BA->basket->billing_zip.' '.$BA->basket->billing_city.', Slovakia';

					$geo = $GD->geocode( $adress );
				} else
					header( "Location: ".$GD->link(17) );
				
				break;

			case 100:
			case 101:
			case 102:
			case 103:

				require_once ('./functions/user-profil.php');

				if ( !$CHR->CHECK_SUBPAGE() )
					header( "Location: ".$GD->link(1) );

				break;

			case 500:
				
					$adress = 'Bratislavská 732/82, 900 24 Veľký Biel, Slovakia';

					$geo = $GD->geocode( $adress );
					
					if ( !is_array($geo) )
						$geo = $GD->geocode( $adress );
				

			case 501:
			case 502:
			case 504:
			case 505:
			case 507:
			case 507:
				require_once ('./functions/items.php');
				break;
		}

	}


	if ( DEBUG == true ) {

		error_reporting(-1);
		assert_options(ASSERT_ACTIVE, 1);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_BAIL, 0);
		assert_options(ASSERT_QUIET_EVAL, 0);
		assert_options(ASSERT_CALLBACK, 'assert_callcack');
		/*set_error_handler('error_handler');
		set_exception_handler('exception_handler');
		register_shutdown_function('shutdown_handler');*/

		function assert_callcack($file, $line, $message) {
			throw new Customizable_Exception($message, null, $file, $line);
		}

		function error_handler($errno, $error, $file, $line, $vars) {
			if ($errno === 0 || ($errno & error_reporting()) === 0) {
				return;
			}

			throw new Customizable_Exception($error, $errno, $file, $line);
		}

		function exception_handler(Exception $e) {
			// Do what ever!
			echo '<pre>', print_r($e, true), '</pre>';
			exit;
		}

		function shutdown_handler() {
			try {
				if (null !== $error = error_get_last()) {
					throw new Customizable_Exception($error['message'], $error['type'], $error['file'], $error['line']);
				}
			} catch (Exception $e) {
				exception_handler($e);
			}
		}

		class Customizable_Exception extends Exception {
			public function __construct($message = null, $code = null, $file = null, $line = null) {
				if ($code === null) {
				parent::__construct($message);
			} else {
				parent::__construct($message, $code);
			}
				if ($file !== null) $this->file = $file;

				if ($line !== null) $this->line = $line;
			}
		}

	}