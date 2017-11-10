<?php

	define('LOCALHOST', false);
	define('DEBUG', false);

	define('DEFAULT_PAGE', "uvod");
  	define('DEF_LANG', 'sk');
  	


	if ( LOCALHOST == true ) {
		define('UPLOAD_FOLDER', 				'../../monamade-admin/files/');

		define('SUBDOMAIN_FILES', 				'admin-monamade.localhost');	//monamade.localhost 192.168.1.104
		define('SUBDOMAIN_FILES2', 				'global.localhost');	//monamade.localhost 192.168.1.104
		define('SUBDOMAIN_CMS', 				'admin-monamade.localhost');
		define('SUBDOMAIN_FOLDER', 				'files/');
		
		define('OFI_PAGE', 						'monamade.localhost');
		
		define('FILES_LOC', 					'');

		define('DEF_UPLOAD_FOLDER', 			'files/');
		define('PHYS_ADRESS_TO_FILES', 			'../monamade-admin/files/');
		define('PHYS_ADRESS_TO_FILES_SWITCH',	'../../monamade-admin/files/');


		define("HEADER_FROM", 					"rocho.web@gmail.com");
		define("HEADER_ADMIN",					"rocho.web@gmail.com");


		/*define('G_CSS_ICONS', 					'../global/css/icons.css');
		define('G_CSS_FA'						'../global/font-awesome/css/font-awesome.min.css')*/

	} else {
		define('UPLOAD_FOLDER', 				'files/');

		define('SUBDOMAIN_FILES', 				'files.monamade.sk');
		define('SUBDOMAIN_FILES2', 				'global.monamade.sk');
		define('SUBDOMAIN_CMS', 				'mm.monamade.sk');
		
		define('SUBDOMAIN_FOLDER', 				'');
		define('OFI_PAGE', 						'monamade.sk');

		define('FILES_LOC', 					'');

		define('DEF_UPLOAD_FOLDER', 			'');
		define('PHYS_ADRESS_TO_FILES', 			'/nfsmnt/hosting1_2/8/1/81fb6d4e-25be-486c-bd77-f60cb8cf2567/monamade.sk/sub/files/');
		define('PHYS_ADRESS_TO_FILES_SWITCH',	PHYS_ADRESS_TO_FILES);



		define("HEADER_FROM", 					"ahoj@monamade.sk");
		define("HEADER_ADMIN",					"monamadesk@gmail.com, monika.pragai@gmail.com, rocho.web@gmail.com");
	}

	define("DEF_TITLE", " | MONAMADE");

	define('MAX_FILES', 5);					//maximalny pocet fotiek pre uzivatela v produkte

	
	define('MIN_USERNAME',		4);
	define('MD_USERNAME',		30);

	define('PSC',				5);
	define('MAX_NUM',			10);

	define('MD_PW',				12);
	//define("MD_RESULT_LIST", 	8);


	
	define('TIME_LOGINTIME', '+1 week');
	define('TIME_INACTIVE', '-1 day');
	define('TIME_DEFAULT', '+1 year');
	define('TIME_LOGOUT', '-1 week');


	//define("FILE_TYPES", [1 => 'image/png', 2 => 'image/gif', 3 => 'image/jpeg', 4 => 'image/pjpeg', 5 => 'image/jpg']);
	define('FILE_MAXSIZE', 5242880); 		//5 MB 5242880

	define('DELIVERY_DAYS', 3);
	define('ORDER_UNTILTO', '12:30');


	define('iTEM_W25', 25);					//item width in results
	define('iTEM_W20', 20);					//item width in results
	define('iTEM_W10', 10);					//item width in resul
	
	//define('MAX_RESULTS_SKLAD', 14);		//sklad   -1 

	define("RESULT_LIST", 12);

	define('ITEM_RELATED', 5);
	define('ITEM_RELATED_SHOW_WHEN', 5);

	define('ITEM_HISTORY', 8);
	define('ITEM_HISTORY_visible', 5);



	define('COOKIE_DEF_TIME', 		'+1 months');
	define('COOKIE_LASTVISIT', 		'+30 minutes');

	define('ORDER_NUM_MIN', 3);
	define('ORDER_NUM_MAX', 4);

	define('ORDER_NUM_MIN_SECOND', 5);
	define('ORDER_NUM_MAX_SECOND', 6);

	define('DPH', 20);
	//define('ITEM')

	//UI

	define('ADRESS_W20', 20);					//

	//define('ORDER_HISTORY', 4);


	define("PAGE_ID",		"777651275588635");
	define("APP_ID", 		"257417734667564");




	define("SOCIAL_FCB",	'https://www.facebook.com/monamadesk/');
	define("SOCIAL_INSTA",	'https://www.instagram.com/monamade.sk/');

	define('BANK_NAME', 	"Slovenská sporiteľňa, a.s.");
	define('BANK_NUM', 		"0322396984/0900");
	define('BANK_IBAN', 	"SK15 0900 0000 0003 2239 6984");
	define('BANK_SWIFT', 	"GIBASKBX");

	define("VOP_NAME",		"Monika Prágaiová");
	define("VOP_STREET",	"Bratislavská 732/82");
	define("VOP_ZIP",		"900 24");
	define("VOP_CITY",		"Veľký Biel");
	define("VOP_STATE",		"Slovenská Republika");

	define("VOP_ICO",		"50 707 965");
	define("VOP_DIC",		"1082 939 462");
	
	define("VOP_MAIL",		"ahoj@monamade.sk");
	define("VOP_PHONE",		"+421 907 171 777");



	define('VOP_FILE',		'vop-monamade.pdf');
	define('VOP_STORNO',	'odstupenie-od-zmluvy-monamade.pdf');