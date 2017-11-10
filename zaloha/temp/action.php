<?php
	$AJAX = 1;

	//$PD["lang"] = $_POST["l"];
	require_once ("../config/defines.php");
	require_once ("../config/db.php");
	require_once ("../config/chr.php");
	
	require_once ("../functions/sessions.php");

	require_once ("../functions/global.php");
	require_once ("../functions/actions.php");

	$type = $_POST["t"];

	if ( strlen($type) < 20 ) {

		$func = new ACTIONS($type);

		$action = $func->action();
		$windowsSIze = $func->windowsSize();

		if ( isset($action) )
			echo json_encode( array("r" 	=> "$action", "ws"	=> $windowsSIze ) );

	}
