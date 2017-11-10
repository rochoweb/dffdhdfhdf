	<?php
	if ( $CHR->PD->page == 1)
		$ogurl = '';
	else
		$ogurl = isset($_GET["p"]) ? $_GET["p"] : $CHR->PD->name;

	$ogimage = isset($IP) ? $IP->IP : "";

	echo '
	<head>
		<title>'.$HEADER["title"].'</title>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">

		<meta name="description" content="'.$HEADER["meta"]["description"].'">
		<meta name="keywords" content="'.$HEADER["meta"]["keywords"].'">
		<meta name="author" content="'.$HEADER["meta"]["author"].'">
		<meta name="robots" content="index, follow">
		<meta name="language" content="sk">

		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">

		<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-main"].'" media="all">
		<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-media"].'" media="all">
		<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-icons"].'" media="all">
		<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-fa"].'" media="all">
		';

		echo '
		<meta property="fb:app_id" content="'.APP_ID.'" />
		
		<meta property="og:title" content="'.$HEADER["title"].'" />
		<meta property="og:description" content="'.$HEADER["meta"]["description"].'" />
		<meta property="og:site_name" content="MONAMADE.sk" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="http://monamade.sk/'.$ogurl.'" />
		
		
		';
		echo $GD->og_image( $ogimage );


		/*$cssui = '
				<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-ui"].'">
				<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-ui-structure"].'">';
*/
		/*switch ( $CHR->PD->page ) {
			case 16:
				echo $cssui.'<link rel="stylesheet" type="text/css" href="'.$HEADER["link"]["css-diyeditor"].'">';
				break;
		}
	*/
		echo '

		<link rel="shortcut icon" href="'.$HEADER["link"]["favicon"].'">

		<script type="text/javascript">';
			/*if ( isset($_COOKIE["online"]) )
				echo 'var PID = '.$PD["page"]["page"].', UID = "'.$UD["session"].'", UON = "'.$_COOKIE["online"].'";'; 
			else
				echo 'var PID = '.$PD["page"]["page"].', UID = "'.$UD["session"].'";';*/
			//echo 'var PID = '.$PD["page"]["page"].', UID = "'.$MM->session().'";';
				if ( $CHR->PD->page == 14 )
					echo 'var PID = '.$CHR->PD->page.', UID = "'.$GD->session().'", DIY = "'.$_GET["id"].'";';
				else
					echo 'var PID = '.$CHR->PD->page.', UID = "'.$GD->session().'";';
		echo '</script>
		
	</head>';


