<?php
	$V = isset($_GET["v"]) ? $_GET["v"] : "";
	require_once("../config/defines.php");

	header("Content-type: text/css; charset: UTF-8"); 
?>

/*
	MonaMade.sk CSS
*/

@font-face {
  font-family: 'mm';
  src:  url('/fonts/mm.eot?1phpa2');
  src:  url('/fonts/mm.eot?1phpa2#iefix') format('embedded-opentype'),
    url('/fonts/mm.ttf?1phpa2') format('truetype'),
    url('/fonts/mm.woff?1phpa2') format('woff'),
    url('/fonts/mm.svg?1phpa2#mm') format('svg');
  font-weight: normal;
  font-style: normal;
}

.ii {
  /* use !important to prevent issues with browser extensions that change fonts */
  font-family: 'mm' !important;
  speak: none;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  text-transform: none;
  line-height: 1;

	display: inline-flex;
	align-items: center;
	justify-content: center;
	vertical-align: middle;

  /* Better Font Rendering =========== */
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.iPass:before {
  content: "\79";
}
.iExit:before {
  content: "\78";
}
.iUser:before {
  content: "\77";
}
.iCircle:before {
  content: "\76";
}
.iCheck2:before {
  content: "\75";
}

.iZoom2:before {
  content: "\74";
}

.iCheck1:before {
  content: "\73";
}
.iCheck:before {
  content: "\72";
}

.iGeis:before {
  content: "\71";
}

.iBank:before {
  content: "\70";
}
.iBasket:before {
  content: "\69";
}
.iDelivery:before {
  content: "\68";
}
.iPay:before {
  content: "\67";
}
.iTags:before {
  content: "\66";
}
.iZoom:before {
  content: "\65";
}
.iLeft:before {
  content: "\64";
}
.iRight:before {
  content: "\63";
}
.iCart:before {
  content: "\62";
}
.iCartF:before {
  content: "\61";
}
.iBag:before {
  content: "\60";
}
.iBag2:before {
  content: "\59";
}
.iBag3:before {
  content: "\58";
}
.iDown2:before {
  content: "\57";
}
.iRight2:before {
  content: "\56";
}
.iBilling:before {
  content: "\55";
}
.iDelivery2:before {
  content: "\54";
}
.iUp2:before {
  content: "\53";
}
.iLight:before {
  content: "\52";
}
.iTrash:before {
  content: "\51";
}
.iCoffe:before {
  content: "\50";
}
.iBasketF:before {
  content: "\49";
}

.iCookies:before {
  content: "\48";
}
.iPrivacy:before {
  content: "\47";
}
.iRe:before {
  content: "\46";
}
.iEmail:before {
  content: "\45";
}

.iHome:before {
  content: "\44";
}
.iHome1:before {
  content: "\43";
}

.iUp:before {
  content: "\36";
}
.iDown:before {
  content: "\35";
}
.iHelp:before {
  content: "\34";
}
.iInvoices:before {
  content: "\33";
}
.iAdress:before {
  content: "\32";
}
.iSettings:before {
  content: "\31";
}
.iUser2:before {
  content: "\30";
}
.iVop:before {
  content: "\29";
}
.iPhone:before {
  content: "\28";
}