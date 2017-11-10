<?php
	
	//require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';

	define(	"FB_POST",	"1659013917727587");
	define(	"FB_TOKEN", "EAADqHryiPSwBAA0c4LEVVYedKl2pbmfunuQMZBHwRa6rt7r9w0kpinCYpMSyHOCxq9ZCNlqNpgqXNQQPppHVte1mSec39LzlZCvnBbiLYDZAZCpLy0OMdTWrGmubKKFByfZCH3a5OZCGp7qUyD2JqX1E4Vrz84pAgzC6lNVPq4JBQZDZD");

/*

	$fb = new Facebook\Facebook([
		'app_id' => '{257417734667564}',
		'app_secret' => '{0587704c137b8c089f46b8e03eb5ac10}',
		'default_graph_version' => 'v2.8',
	]);

 	*/

	function fb_data($r = '') {

		$re = "";
		$count = 0;

		$ch = curl_init('https://graph.facebook.com/v2.8/1502630170032630_1659013917727587/comments?limit=10000&access_token=EAADqHryiPSwBAA0c4LEVVYedKl2pbmfunuQMZBHwRa6rt7r9w0kpinCYpMSyHOCxq9ZCNlqNpgqXNQQPppHVte1mSec39LzlZCvnBbiLYDZAZCpLy0OMdTWrGmubKKFByfZCH3a5OZCGp7qUyD2JqX1E4Vrz84pAgzC6lNVPq4JBQZDZD');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$response = curl_exec($ch);
		//var_dump($response);
		//var_dump(json_decode($response)); // if you want an array.

		if ( $response ) {
			
			if ( $FB_DATA = json_decode($response) ) { 

				if ( $FB_DATA ) {
					foreach ($FB_DATA->data as $POST) {
						$count += 1; 

						if ( strtotime( $POST->created_time ) < strtotime("9.4.2017 22:00:00") )
						$arr[$POST->from->id] = array(
							"created_time" 	=> $POST->created_time,
							"reviewer_name" => $POST->from->name,
							"message"		=> $message = $POST->message);

						
						//$names[$count] = array("name" => $POST->from->id);
					}



				}
			}
			
		} else {
			echo "No data for api";
		}

		if ( $arr ) {
			$count = 0;

			foreach ($arr as $key => $value) {
				$random[] = $value["reviewer_name"];
			}

			$winner = rand(0, count($random));

			foreach ($arr as $key => $value) {
				$count += 1;

				if ( $count == $winner )
					$win = ' id="W"';
				else
					$win = "";

				//if ( )
				$re .= '
					<tr'.$win.'>
						<td>'.$count.'</td>
						<td>'.date("d.m.Y", strtotime($value["created_time"])).' <strong>'.date("H:i:s", strtotime($value["created_time"])).'</strong> </td>
						<td>'.$value["reviewer_name"].'</td>
						<td class="coment">'.$value["message"].'</td>
					</tr>
				';
			}
		}
		




/*
		$random = "";
		foreach ($arr as $key => $value) {
			$random[] = $value["reviewer_name"];
		}

		$number = rand(0, count($random));
*/
		


		$r = '
			
			<h1>„ JARNÁ ZDIEĽAČKA “</h1>
			<h2>MONAMADE</h2>
<!--
			<div class="winner">
				<h3>VÝHERCA</h3> 
				<div>ID: <strong>'.$winner.'</strong></div>
				<div>MENO: <strong>'.$random[$winner-1].'</strong></div>
			</div>-->
			<div class="winner">
				<h3>VÝHERCA</h3> 
				<div>ID: <strong>57</strong></div>
				<div>MENO: <strong>Erika Gabrielova</strong></div>
			</div>
			<div class="info">
				<div>Počet <strong>platných</strong> komentárov: <span><strong>'.$count.'</strong></span> ( viacpočetné komentáre rovnakého užívateľa sa nepočítajú ) </div>
			</div>

			<table>
				<tr>
					<th>ID</th>
					<th>DÁTUM A ČAS</th>
					<th>MENO</th>
					<th class="coment">KOMENTÁR</th>
				</tr>

				'.$re.'
			</table>

			<div class="h2">
				<div>Dátum a čas výpisu</div>
				<p>'.date("d.m.Y", strtotime("now")).' <strong>'.date("H:i:s", strtotime("now")).'</strong></p>
			</div>
		';

		return $r;
	}
?>

<html>
	<head>
		<title>Zdielačka api - MONAMADE</title>

		<meta http-equiv="content-type" content="text/html;charset=UTF8">

		<meta name="description" content="">
		<meta name="keywords" content="">
		
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
	</head>		

	<style>
		/* Eric Meyer's Reset */
html, body, div, span, applet, object, iframe,h1, h2, h3, h4, h5, h6, p, blockquote, pre,a, abbr, acronym, address, big, cite, code,del, dfn, em, img, ins, kbd, q, s, samp,small, strike, strong, sub, sup, tt, var,b, u, i, center,dl, dt, dd, ol, ul, li,fieldset, form, label, legend,table, caption, tbody, tfoot, thead, tr, th, td,article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary,time, mark, audio, video {	margin: 0;	padding: 0;	border: 0;	font-size: 100%;	font: inherit;	vertical-align: baseline;} article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {	display: block;}body {	line-height: 1;}ol, ul {	list-style: none;}blockquote, q {	quotes: none;}blockquote:before, blockquote:after,q:before, q:after {	content: '';	content: none;}table {	border-collapse: collapse;	border-spacing: 0;}

		
	@font-face {
		font-family: 'OS';
		src: local('☺'), url('/fonts/OpenSans-Light.ttf') format('truetype');
		font-weight: 200;
		font-style: normal;
	}

	@font-face {
		font-family: 'OS';
		src: local('☺'), url('/fonts/OpenSans-LightItalic.ttf') format('truetype');
		font-weight: 200;
		font-style: italic;
	}

	@font-face {
		font-family: 'OS';
		src: local('☺'), url('/fonts/OpenSans-Regular.ttf') format('truetype');
		font-weight: 300;
		font-style: normal;
	}

	@font-face {
		font-family: 'OS';
		src: local('☺'), url('/fonts/OpenSans-Semibold.ttf') format('truetype');
		font-weight: 400;
		font-style: normal;
	}

	@font-face {
		font-family: 'OS';
		src: local('☺'), url('/fonts/OpenSans-Bold.ttf') format('truetype');
		font-weight: 600;
		font-style: bold;
	}
	@font-face {
		font-family: 'OS';
		src: local('☺'), url('/fonts/OpenSans-SemiboldItalic.ttf') format('truetype');
		font-weight: 500;
		font-style: italic;
	}

	body { font: 300 16px/20px "OS"; }
		
		h1, h2 { text-align: center; }
			h1 { font-size: 30px; color: #39b54a; font-weight: 200; }
			h2 { font-size: 16px; margin-top: 15px; visibility: hidden; }

		table { text-align: center; width: 100%; line-height: 100%; }
			th, td { padding: 10px 20px; border: solid 1px #fff; }
		th { font-weight: bold; background: #39b54a; color: #fff; }
		td { align-items: center; }
			.coment { width: 300px; }

		tr:nth-child(even) { background: #e5e5e5; }
		tr:nth-child(odd) { background: #f5f5f5; }
		tr:hover, tr:focus { background: #428bca; color: #fff; }
		strong { font-weight: bold; }


		.temporary-monamade { max-width: 1000px; margin: auto; }
			.cont { padding: 80px 40px 80px 40px; }

				.h2 { text-align: center; margin: 40px; }
				.h2 div { font-size: 14px; font-style: italic; }
				.h2 p { font-size: 18px; margin-top: 5px; }

				.info { margin: auto; font-size: 14px; text-align: center; margin: 0 0 40px 0; background: #fafafa; padding: 15px 30px; }
				.info span { margin: 0 5px; color: #428bca; }
				.info div { font-size: 14px; }

				.winner { text-align: center; background: #fafafa; padding: 30px 20px; margin: 40px 0; }
				.winner div { margin: 5px; }
					h3 { font-size: 20px; color: #d34831; margin-bottom: 20px; }
				

				#W { background: #d34831; color: #fff; }
		
	</style>

	<body>
		<div class="temporary-monamade">
			<div class="cont">
				<?= fb_data(); ?>
			</div>
		</div>
	</body>
</html>