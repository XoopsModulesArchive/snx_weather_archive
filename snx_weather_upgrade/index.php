<?php
//***************************************************************************//
// SnX-Weather:                                                              //
// v0.1.1  28/09/2004 FOR XOOPS v2                                           //
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
include_once("../../mainfile.php");
include_once("header.php");

function make_template($weatherInfos, $language) {
	include("./dico.php");
	$return="<div id=\"weatherPage\">\n";
	$count=0;
	while(list($key,$val)=each($weatherInfos)) {
		if(is_array($val)) {
			$count++;
			$return.="\t<div class=\"date".(($key=="today")?"-today":"")."\">\n";
			$return.="\t\t<p class=\"day\"><span>$key</span></p>\n";
			if($count<=5) {
				while(list($key2,$val2)=each($val)) {
					$return.="\t\t<div class=\"".strtoupper(ereg_replace("[/\.]","-",$key2))."\">\n";
					$return.="\t\t\t<p class=\"".strtoupper(ereg_replace("[/\.]","-",$key2))."\"><span>".ereg_replace("_"," ",$key2)."</span><div id=\"separator-".strtoupper(ereg_replace("[/\.]","-",$key2))."\">:&nbsp;</div></p>\n";
					$return.="\t\t\t<p class=\"val-".strtoupper(ereg_replace("[/\.]","-",$key2))."\"><span>".$val2."</span></p>\n";
					$return.="\t\t</div>\n";
				}
			} else {
				$return.="\t\t<div class=\"EXTENDED_FORECAST\"><a href=\"http://www.weather.com/\" target=\"_blank\"><img src=\"http://image.weather.com/web/common/logos/twclogo_60X45.gif\" alt=\"The Weather Channel\" name=\"The Weather Channel\" border=0></a></div>\n";
			}
			$return.="\t</div>\n";
		} else {
			$return.="\t<div class=\"".strtoupper($key)."\">$val</div>\n";
		}
	}
	if(!$count) return "No result found !";
	if($count<=10) {
		for($i=$count;$i<=10;$i++) {
			$return.="\t<div class=\"date\"></div>\n";
		}
	}
	$return.="</div>";
	if($language!="en") {
		while(list($word,$tab)=each($dico)) {
			$return=ereg_replace($word,$dico[$word][$language],$return);		
		}
	}
	
	return $return;
}

function SnXWeatherDisplay($cityname,$citycode,$unit="") {
   global $snxweather_encoding;
   
   if(file_exists("./config.dat")) {
   	  $handle=fopen("./config.dat","r");
	  $myConfigEncoded="";
  	  while(!feof($handle)) {
		 $myConfigEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
	if($myConfigEncoded!="") $myConfig=unserialize($myConfigEncoded);
	unset($myConfigEncoded);
   
   $myCityArray=array();
   if(file_exists("./citylist.dat")) {
		$handle=fopen("./citylist.dat","r");
		$myCityArrayEncoded="";
		while(!feof($handle)) {
			$myCityArrayEncoded.=fgets($handle,2048);
		}
		fclose($handle);
   }
   if($myCityArrayEncoded) $myCityArray=unserialize($myCityArrayEncoded);
   unset($myCityArrayEncoded);
   
   if($cityname=="" && $citycode=="") {
		if(count($myCityArray)) {
			list($cityname,$citycode)=each($myCityArray);
		} else {
			$cityname="Montpellier";
			$citycode="FRXX0068";
		}
	}
   if($unit=="") {
		$unit=$myConfig["unit"];
   }
   include_once(XOOPS_ROOT_PATH."/modules/snx_weather/snx_weather.php");
   $myWeather="";
   $myWeather.="<h2 class=\"SnX-Weather\"><span>SnX-Weather</span></h2>\n";
   $myWeather.="<div class=\"city-search\">\n";
   $myWeather.="\t<p class=\"city-stitle\"><span>"._SnX_WEATHER_CITY_SEARCH.":</span></p>\n";
   $myWeather.="<p class=\"city-sform\"><span><form action=\"index.php\" method=get>\n";
   $myWeather.="<input name=\"scity\">\n";
   $myWeather.="<input type=submit name=\"submit\" value=\"search\">\n";
   $myWeather.="<input type=hidden name=\"op\" value=\"SnXWeatherSearch\">\n";
   $myWeather.="</form></center></span></p>\n";
   $myWeather.="</div>\n";
   
   $myWeather.="<div class=\"city-select\">\n";
   $myWeather.="\t<p class=\"city-title\"><span>"._SnX_WEATHER_CITY_SELECT.":</span></p>\n";
   $myWeather.="<p class=\"city-form\"><span><form action=\"index.php\" method=get>\n";
   $myWeather.="<select name=\"citycode\" onchange=\"submit()\">\n";
   reset($myCityArray);
   while(list($city,$code)=each($myCityArray)) {
		$myWeather.="<option value=\"$code\" ".(($code==$citycode)?"SELECTED":"").">$city\n";
   }
   $myWeather.="</select>\n";
   $myWeather.="</form></center></span></p>\n";
   $myWeather.="</div>\n";
   
   $myWeather.=make_template(get_weather_infos($cityname,$citycode,0,$unit),$myConfig["language"]);
   
   preg_match("/([\-]{0,1}[0-9]*?)\&deg;([CFcf])/",ereg_replace("\n","",$myWeather),$match);
   $degree=$match[1];
   $type=$match[2];
   echo "<style type=\"text/css\" title=\"currentStyle\">@import \"style.php?degree=$degree&type=$type\";</style>";
   echo $myWeather;
   echo "<center><br><br><b>SnX-Weather module by SnAKes</b><br>\n";
   if($myConfig["advert"]=="full") {
   		echo "<a href=\"http://www.qmel.com/\">"._SnX_WEATHER_QMEL_FULL."</a><br>\n";
		echo "<a href=\"http://www.ntica.com/\">"._SnX_WEATHER_NTICA_FULL."</a><center>\n";
   } elseif($myConfig["advert"]=="mini") {
   		echo "<a href=\"http://www.qmel.com/\">"._SnX_WEATHER_QMEL_MINI."</a> & <a href=\"http://www.ntica.com/\">"._SnX_WEATHER_NTICA_MINI."</a><center>\n";
   }
   return $citycode;
}

function SnXWeatherSearch( $scity, $sunit )
{
   global $xoopsDB;
   // Make first characters upper case: new york => New York
   $scity=ucfirst($scity);

   while(ereg("[\ \-][a-z]",$scity)) {
      ereg("([A-Za-z].*)(\ |\-)([a-z].*)",$scity,$regs);
  	   $scity=$regs[1].$regs[2].ucfirst($regs[3]);
 	}
   if(!ereg("[çáéíóúãõâêîôû]",$scity)) $scity=utf8_decode($scity);
   // Replace all accents by non accents
   $search  = array ('ç', 'á', 'é', 'í', 'ó', 'ú', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û');
   $replace = array ('c', 'a', 'e', 'i', 'o', 'u', 'a', 'o', 'a', 'e', 'i', 'o', 'u');
   $scity   = str_replace($search, $replace, $scity);

   // Clean city name, we only want letters and spaces
   $scity=ereg_replace("[^A-Za-z\ \-]","",$scity);
   
   // Load city array file
   $myCityArrayEncoded="";
   if(file_exists("./cache/cityarray.dat")) {
   	  $handle=fopen("./cache/cityarray.dat","r");
  	  while(!feof($handle)) {
		 $myCityArrayEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   }
   // Create city cache array
   if($myCityArrayEncoded) $myCityArray=unserialize($myCityArrayEncoded);

   if($myCityArray[$scity]) {
   	  $scitycode=$myCityArray[$scity];
   }
  
   $citycode=SnXWeatherDisplay($scity, $scitycode, $sunit);
   
   if($citycode!="" && !$myCityArray[$scity]) {
	    // Insert into the cache array
		$myCityArray[$scity]=$citycode;

		// Encode the city array cache
		$myCityArrayEncoded=serialize($myCityArray);

	   // Save into a file	   
	   $myCityArrayEncoded=serialize($myCityArray);
	   $handle=fopen("./cache/cityarray.dat","w");
	   if($handle) {
		   fputs($handle,$myCityArrayEncoded);
		   fclose($handle);
	   } else {
	   		echo "Error opening city array cache<br>";
	   }
   }
}


/**********************************************************
*  MAIN SECTION
* *********************************************************/

if ( $xoopsConfig['startpage'] == $xoopsModule->dirname() ) {
	$xoopsOption['show_rblock'] =1;
	include(XOOPS_ROOT_PATH."/header.php");
	if ( empty($HTTP_GET_VARS['start']) ) {
		make_cblock();
		echo "<br />";
	}
} else {
	$xoopsOption['show_rblock'] =0;
	include(XOOPS_ROOT_PATH."/header.php");
}

global $HTTP_POST_VARS,$HTTP_GET_VARS;

if (isset($HTTP_GET_VARS['op'])) $op=$HTTP_GET_VARS['op'];
	elseif (isset($HTTP_POST_VARS['op'])) $op=$HTTP_POST_VARS['op'];
		else $op="";
	
if (isset($HTTP_GET_VARS['unit'])) $unit=$HTTP_GET_VARS['unit'];
	elseif (isset($HTTP_POST_VARS['unit'])) $unit=$HTTP_POST_VARS['unit'];
		else $unit="";
	
if (isset($HTTP_GET_VARS['sunit'])) $sunit=$HTTP_GET_VARS['sunit'];
	elseif (isset($HTTP_POST_VARS['sunit'])) $sunit=$HTTP_POST_VARS['sunit'];
		else $sunit="";
	
if (isset($HTTP_GET_VARS['cityname'])) $cityname=$HTTP_GET_VARS['cityname'];
	elseif (isset($HTTP_POST_VARS['cityname'])) $cityname=$HTTP_POST_VARS['cityname'];
		else $cityname="";
	
if (isset($HTTP_GET_VARS['citycode'])) $citycode=$HTTP_GET_VARS['citycode'];
	elseif (isset($HTTP_POST_VARS['citycode'])) $citycode=$HTTP_POST_VARS['citycode'];
		else $citycode="";
	
if (isset($HTTP_POST_VARS['scity'])) $scity=$HTTP_POST_VARS['scity'];
	elseif (isset($HTTP_GET_VARS['scity'])) $scity=$HTTP_GET_VARS['scity'];
		else $scity="";


switch ( $op ) {
   case "SnXWeatherSearch":
		SnXWeatherSearch($scity,$sunit);
   break;

   default:
		SnXWeatherDisplay($cityname, $citycode, $unit);
}

include_once (XOOPS_ROOT_PATH."/footer.php");
?>
