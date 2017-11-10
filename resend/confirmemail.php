<?php
	$PAGE = 0;
	
	require_once ("../config/defines.php");
	require_once ("../config/db.php");
	require_once ("../config/chr.php");
	require_once ("../functions/global.php");
	require_once ('../functions/email.php');

	echo $MAIL->send_mail_about_order('02622017');
