<?php
//***************************************************************************//
// SnX-Weather:                                                              //
// 28/09/2004 FOR XOOPS v2                                           //
// by NGUYEN DINH Quoc-Huy (alias SnAKes) (support@qmel.com)                 //
// http://www.qmel.com                                                       //
/******************************************************************************
 ** Copyright (C) 2004 NGUYEN DINH Quoc-Huy (SnAKes)
 ** 
 ** This library is free software; you can redistribute it and/or
 ** modify it under the terms of the GNU Lesser General Public
 ** License as published by the Free Software Foundation; either
 ** version 2.1 of the License, or (at your option) any later version.
 ** 
 ** This library is distributed in the hope that it will be useful,
 ** but WITHOUT ANY WARRANTY; without even the implied warranty of
 ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 ** Lesser General Public License for more details.
 ** 
 ** You should have received a copy of the GNU Lesser General Public
 ** License along with this library; if not, write to the Free Software
 ** Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **
 ****************************************************************************** 
 ** Copyright (C) 2004 NGUYEN DINH Quoc-Huy (SnAKes)
 ** Cette bibliothèque est libre, vous pouvez la redistribuer et/ou la modifier
 ** selon les termes de la Licence Publique Générale GNU Limitée publiée par la 
 ** Free Software Foundation (version 2 ou bien toute autre version ultérieure 
 ** choisie par vous).
 **
 ** Cette bibliothèque est distribuée car potentiellement utile, mais SANS
 ** AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de 
 ** commercialisation ou d'adaptation dans un but spécifique. Reportez-vous 
 ** à la Licence Publique Générale GNU Limitée pour plus de détails.
 **
 ** Vous devez avoir reçu une copie de la Licence Publique Générale GNU Limitée
 ** en même temps que cette bibliothèque; si ce n'est pas le cas, écrivez à la 
 ** Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 ** MA 02111-1307, États-Unis.
 **
 ******************************************************************************/
include '../../../include/cp_header.php';
include "../dico.php";

if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
	include "../language/".$xoopsConfig['language']."/main.php";
	include "../language/".$xoopsConfig['language']."/modinfo.php";
	include "../language/".$xoopsConfig['language']."/admin.php";
} else {
	include "../language/english/main.php";
	include "../language/english/modinfo.php";
	include "../language/english/admin.php";
}

function lastUpdate() {
	xoops_cp_header();
	$updateURL="http://www.qmel.com/modules/mydownloads/visit.php?cid=2&lid=5";
	$modulesURL="http://www.qmel.com/modules/mydownloads/singlefile.php?cid=2&lid=5";
	include("../functions.php");
	$body=getting($updateURL,"http://www.qmel.com/","","",0);
	if(preg_match("/URL=(.*?)\"/",$body,$match)) {
		$nextURL=eregi_replace("<BR>"," ",$match[1]);
		preg_match("/.*\/snx_weather-(.*?)\.tar\.gz/",$nextURL,$match);
		$newVersion=$match[1];
	}
	echo "<b>"._SnX_WEATHER_CURRENT_VERSION.":</b> SnX-Weather v"._SnX_WEATHER_VERSION."<br>\n	";
	echo "<b>"._SnX_WEATHER_LATEST_VERSION.":</b> SnX-Weather v".$newVersion."<br><br>\n";
	echo "<a href=\"$modulesURL\" target=_blank>Download now</a><br>\n";
}

function setStyleSheet($myStyle) {
	unset($myStyle["op"]);
	unset($myStyle["submit"]);
	$myStyleEncoded=serialize($myStyle);
	$handle=fopen("../stylesheet.dat","w");
	if($handle) {
		fputs($handle,$myStyleEncoded);
		fclose($handle);
		header("Location: index.php?op=stylesheet");
	} else {
		xoops_cp_header();
		echo _SnX_WEATHER_ADM_ERROR_OPEN_STYLE_DAT;
	}
}

function styleSheet() {
	xoops_cp_header();
	if(file_exists("../stylesheet.dat")) {
   	  $handle=fopen("../stylesheet.dat","r");
	  $myStyleEncoded="";
  	  while(!feof($handle)) {
		 $myStyleEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
	if($myStyleEncoded!="") $myStyle=unserialize($myStyleEncoded);
	unset($myStyleEncoded);
	
	$stylesBlock=array(
		"textBlockCityName" => "City name text color",
		"bgBlockImgSky" => "Image background color",
		"textBlockSky" => "Sky conditions text color"
	);
	
	$styles=array(
		"textModule" => "Module title color",
		"textSearchCity" => "City search title color",
		"textSelectCity" => "City select title color",
		"bgCityName" => "City name background color",
		"textCityName" => "City name text color",
		"bgToday" => "Today area background color",
		"textSky" => "Sky conditions text color",
		"bgForecast" => "Forecast date background color",
		"textForecast" => "Forecast date text color",
		"bgWeather" => "Forecast weather informations background color",
		"textWeather" => "Forecast weather informations text color"
	);
	
	
	echo "<form name=\"stylesheet\" method=\"post\" action=\"index.php\">\n";
	echo "<b>Block:</b><br>";
	echo "<table border=0>\n";
	while(list($code,$desc)=each($stylesBlock))	{
		if(ereg("^text",$code)) {
			echo "<tr><td>$desc</td><td><input name=\"$code\" value=\"".$myStyle[$code]."\"></td></tr>\n";
		} elseif (ereg("^bg",$code)) {
			echo "<tr><td>$desc</td><td><input name=\"$code\" value=\"".$myStyle[$code]."\"></td></tr>\n";
		}
	}
	echo "</table><br><br>\n";
	
	echo "<b>Main:</b><br>";
	echo "<table border=0>\n";
	while(list($code,$desc)=each($styles))	{
		if(ereg("^text",$code)) {
			echo "<tr><td>$desc</td><td><input name=\"$code\" value=\"".$myStyle[$code]."\"></td></tr>\n";
		} elseif (ereg("^bg",$code)) {
			echo "<tr><td>$desc</td><td><input name=\"$code\" value=\"".$myStyle[$code]."\"></td></tr>\n";
		}
	}
	echo "</table><br><br>\n";
	echo "<input type=submit name=\"submit\" value=\"OK\">\n";
	echo "<input type=\"hidden\" name=\"op\" value=\"setStyleSheet\">\n";
	echo "</form>\n";
}

function setPreferences($displayCity,$unit,$language,$advert) {
	$myConfig["displayCity"]=$displayCity;
	$myConfig["unit"]=$unit;
	$myConfig["language"]=$language;
	$myConfig["advert"]=$advert;
	$myConfigEncoded=serialize($myConfig);
	$handle=fopen("../config.dat","w");
	if($handle) {
		fputs($handle,$myConfigEncoded);
		fclose($handle);
		header("Location: index.php?op=prefs");
	} else {
		xoops_cp_header();
		echo _SnX_WEATHER_ADM_ERROR_OPEN_CONFIG_DAT;
	}
}

function showPreferences() {
	global $availLang;
	
	xoops_cp_header();
	echo "<h4>"._SnX_WEATHER_ADM_PREFS."</h4>\n";
	if(file_exists("../config.dat")) {
   	  $handle=fopen("../config.dat","r");
	  $myConfigEncoded="";
  	  while(!feof($handle)) {
		 $myConfigEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
	if($myConfigEncoded!="") $myConfig=unserialize($myConfigEncoded);
	unset($myConfigEncoded);
	echo "<form name=\"pref_bloc\" method=\"post\" action=\"index.php\">";
	echo "<input type=\"hidden\" name=\"op\" value=\"setPrefs\">\n";
	echo "<b>"._SnX_WEATHER_ADM_GENERAL.": </b><br>\n";
	echo _SnX_WEATHER_ADM_LANG.": ";
	echo "<select name=\"language\">\n";
	while(list($lang,$code)=each($availLang)) {
		if($code) {
			echo "<option value=\"$code\" ".(($code==$myConfig["language"])?"SELECTED":"").">$lang\n";
		}
	}
	echo "</select><br><br>";
	echo _SnX_WEATHER_ADM_UNIT.": ";
	echo "<input type=radio name=\"unit\" value=\"metric\" ".(($myConfig["unit"]=="metric")?"CHECKED":"").">&deg;C\n";
	echo "<input type=radio name=\"unit\" value=\"\" ".(($myConfig["unit"]=="")?"CHECKED":"").">&deg;F<br><br>\n";	
	echo _SnX_WEATHER_ADM_ADVERT.": ";
	echo "<input type=radio name=\"advert\" value=\"full\" ".(($myConfig["advert"]=="full")?"CHECKED":"").">"._SnX_WEATHER_ADM_ADVERT_FULL."\n";
	echo "<input type=radio name=\"advert\" value=\"mini\" ".(($myConfig["advert"]=="mini")?"CHECKED":"").">"._SnX_WEATHER_ADM_ADVERT_MINI."\n";	
	echo "<input type=radio name=\"advert\" value=\"none\" ".(($myConfig["advert"]=="none")?"CHECKED":"").">"._SnX_WEATHER_ADM_ADVERT_NONE."<br><br>\n";	
	echo "<b>"._SnX_WEATHER_ADM_BLOC_VIEW.": </b><br>";
	echo _SnX_WEATHER_ADM_BLOC_VIEW_DISPLAY_CITY.": ";
	echo "<input type=radio name=\"displayCity\" value=\"first\" ".(($myConfig["displayCity"]=="first")?"CHECKED":"").">"._SnX_WEATHER_ADM_BLOC_VIEW_FIRST_CITY."\n";
	echo "<input type=radio name=\"displayCity\" value=\"random\" ".(($myConfig["displayCity"]=="random")?"CHECKED":"").">"._SnX_WEATHER_ADM_BLOC_VIEW_RANDOM_CITY."<br>\n";
	echo "<br><input type=submit name=\"submit\" value=\"OK\">\n";
	echo "</form>";
	
}

function modifyCities($cities) {
	$cityEntries=split("\n",$cities);
	foreach($cityEntries as $cityEntry) {
		list($cityName,$cityCode)=split(",",$cityEntry);
		if($cityName!="" && $cityCode!="") {
			$cityName=ucfirst($cityName);
			while(ereg("[\ \-][a-z]",$cityName)) {
	      		ereg("([A-Za-z].*)(\ |\-)([a-z].*)",$cityName,$regs);
	  	   		$cityName=$regs[1].$regs[2].ucfirst($regs[3]);
	 		}
	   		if(!ereg("[A-Za-zçáéíóúãõâêîôû\ ]")) $cityName=utf8_decode($cityName);
	   		// Replace all accents by non accents
	   		$search  = array ('ç', 'á', 'é', 'í', 'ó', 'ú', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û');
	   		$replace = array ('c', 'a', 'e', 'i', 'o', 'u', 'a', 'o', 'a', 'e', 'i', 'o', 'u');
	   		$cityName   = str_replace($search, $replace, $cityName);

	   		// Clean city name, we only want letters and spaces
	   		$cityName=ereg_replace("[^A-Za-z\ ]","",$cityName);
			$cityCode=ereg_replace("[^A-Za-z0-9]","",$cityCode);
			$myCityArray[$cityName]=$cityCode;
		}
	}

	$myCityArrayEncoded=serialize($myCityArray);
	$handle=fopen("../citylist.dat","w");
	if($handle) {
		fputs($handle,$myCityArrayEncoded);
		fclose($handle);
		header("Location: index.php?op=citymenu");
	} else {
		xoops_cp_header();
		echo _SnX_WEATHER_ADM_ERROR_OPEN_CITYARRAY_DAT;
	}
}

function cityMenu() {
	xoops_cp_header();
	echo "<h4>"._SnX_WEATHER_ADM_CURRENT_CITY_LIST."</h4>\n";
	echo _SnX_WEATHER_ADM_ADD_CITY_DESC."<br><br>";
	if(file_exists("../citylist.dat")) {
   	  $handle=fopen("../citylist.dat","r");
	  $myCityArrayEncoded="";
  	  while(!feof($handle)) {
		 $myCityArrayEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
   	if($myCityArrayEncoded!="") $myCityArray=unserialize($myCityArrayEncoded);
	unset($myCityArrayEncoded);
	echo "<form name=\"citymenu\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"op\" value=\"modifyCities\">\n";
	echo "<textarea name=\"cities\" rows=\"10\">\n";
	if(!count($myCityArray)) echo "City Name, CITY_CODE";
	else {
		while(list($city,$code)=each($myCityArray)) {
			echo "$city, $code\n";
		}
	}	
	echo "</textarea><br>\n";
	echo "<input type=\"submit\" name=\"submit\" value=\""._SnX_WEATHER_ADM_MODIFY_CITIES."\">";
}

if(!isset($HTTP_POST_VARS['op'])) {
	$op = isset($HTTP_GET_VARS['op']) ? $HTTP_GET_VARS['op'] : 'main';
} else {
	$op = $HTTP_POST_VARS['op'];
}
switch ($op) {
	case "citymenu":
		cityMenu();
	break;

	case "stylesheet":
		styleSheet();
	break;
	
	case "setStyleSheet":
		setStyleSheet($_POST);
	break;
		
	case "prefs":
		showPreferences();
	break;
	
	case "setPrefs":
		setPreferences($_POST["displayCity"],$_POST["unit"],$_POST["language"],$_POST["advert"]);
	break;
	
	case "modifyCities":
		modifyCities($_POST["cities"]);
	break;
	
	case "lastUpdate":
		lastUpdate();
	break;

	default:
		xoops_cp_header();
		echo "<h4>SnX-Weather</h4><table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\"> - <b>";
		echo "<a href='index.php?op=citymenu'>"._SnX_WEATHER_ADMENU1."</a></b><br /><br />\n";
		echo "- <b><a href='index.php?op=stylesheet'>"._SnX_WEATHER_ADMENU2."</a></b><br /><br />\n";
		echo "- <b><a href='index.php?op=prefs'>"._SnX_WEATHER_ADMENU3."</a></b><br /><br />\n";
		echo "- <b><a href='index.php?op=lastUpdate'>"._SnX_WEATHER_ADMENU4."</a></b>\n";
		echo "</td></tr></table>\n";
	break;
}
xoops_cp_footer();
?>
