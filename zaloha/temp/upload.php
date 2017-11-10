<?php
	$LEVEL = 0;
	$AJAX = 0;

	require_once ("../config/defines.php");
	require_once ("../config/db.php");
	
	require_once ("../functions/mm.php");
	require_once ("../functions/sessions.php");

	require_once ("../functions/global.php");
	require_once ("../functions/actions.php");

	//require_once ("../functions/diyeditor.php");
	require_once ("../functions/filemanager.php");

	$UP = new FILEMANAGER( $_FILES );

	if ( $return = $UP->verify_upload() ) echo json_encode( [$return] );
		