<?php
	$AJAX = 1;

	//$PD["lang"] = $_POST["l"];
	require_once ("../config/defines.php");
	require_once ("../config/db.php");
	require_once ("../config/chr.php");
	
	require_once ("../functions/sessions.php");

	require_once ("../functions/global.php");
	require_once ("../functions/actions.php");
	require_once ('../functions/check.php');

	require_once ("../functions/inputs.php");
	require_once ("../functions/filters.php");
	require_once ('../functions/email.php');
	require_once ('../functions/basket.php');
	require_once ('../functions/items.php');

	$library = $_POST["lib"];
	$type = $_POST["t"];
	
	if ( isset($_POST["d"]) )
		$data = $_POST["d"];
	else
		$data = '';

	$page = isset($_POST["p"]) ? $_POST["p"] : "";

	if ( isset($library) && isset($type) ) {

		switch ($library) {
			case 'verify':
				require_once ("../functions/verify.php");
				$func = new VERIFY();
				break;
			
			case 'account':
				require_once ("../functions/account.php");
				$func = new ACCOUNT();
				break;

			case 'search':
				require_once ("../functions/search.php");
				$func = new SEARCH($page);
				break;

			case 'event':
				require_once ("../functions/events.php");
				$func = new EVENTS($type);
				break;

			case 'action':
				require_once ("../functions/search.php");
				$func = new ACTIONS($type);
				break;

			case 'basket':
				//require_once ("../functions/inputs.php");
				//require_once ("../functions/basket.php");
				$func = new BASKET();
				//$func = new BASKET($type);
				break;
/*
			case 'additem':
				require_once ("../functions/filemanager.php");
				require_once ("../functions/additem.php");
				$func = new DIYEDITOR();
				break;*/
		}

		$action = $func->action($type, json_decode(json_encode($data)));

		if ( isset($action) )
			echo json_encode( $action );

	}