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

function make_template_block(&$weatherInfos, $language) {
	include(XOOPS_ROOT_PATH."/modules/snx_weather/dico.php");

	$return="<div id=\"weatherBlock\">\n";
	$return.="\t<p class=\"cityname\"><span>".ereg_replace("^Current Conditions for ","",$weatherInfos["city"])."</span></p>\n";
	$return.="\t<p class=\"val-skyImg\"><span>".$weatherInfos["today"]["skyImg"]."</span></p>\n";
	$return.="\t<p class=\"val-skyText\"><span>".$weatherInfos["today"]["skyText"]."</span></p>\n";
	$return.="\t<p class=\"TEMPERATURE\"><span>Temperature: ".$weatherInfos["today"]["Temperature"]."</span></p>\n";
	$return.="\t<p class=\"HUMIDITY\"><span>Humidity: ".$weatherInfos["today"]["Humidity"]."</span></p>\n";
	$return.="</div>";
	
	if($language!="en") {
		while(list($word,$tab)=each($dico)) {
			$return=ereg_replace($word,$dico[$word][$language],$return);		
		}
	}
	reset($dico);
	return $return;
}

function disp_block_snxweather() {
	if(file_exists(XOOPS_ROOT_PATH."/modules/snx_weather/citylist.dat")) {
   	  $handle=fopen(XOOPS_ROOT_PATH."/modules/snx_weather/citylist.dat","r");
	  $myCityArrayEncoded="";
  	  while(!feof($handle)) {
		 $myCityArrayEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
   	if($myCityArrayEncoded!="") $myCityArray=unserialize($myCityArrayEncoded);
	unset($myCityArrayEncoded);
	
	if(file_exists(XOOPS_ROOT_PATH."/modules/snx_weather/config.dat")) {
   	  $handle=fopen(XOOPS_ROOT_PATH."/modules/snx_weather/config.dat","r");
	  $myConfigEncoded="";
  	  while(!feof($handle)) {
		 $myConfigEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
	if($myConfigEncoded!="") $myConfig=unserialize($myConfigEncoded);
	unset($myConfigEncoded);
	if($myConfig["displayCity"]=="random") {
		srand((double) microtime() * 1000000);
		$rNum = rand(1, count($myCityArray));
		for($i=0;$i<$rNum;$i++) {
			list($cityname,$citycode)=each($myCityArray);
		}
	} else {
		if(is_array($myCityArray)) {
			list($cityname,$citycode)=each($myCityArray);
		} else {
			$cityname="Montpellier";
			$citycode="FRXX0068";
		}
	}
	
	$unit=$myConfig["unit"];
	$block = array();
	$block['title'] = "SnX-Weather";
	$block['content'] = "";
    include_once(XOOPS_ROOT_PATH."/modules/snx_weather/snx_weather.php");
	
	$block["content"].=make_template_block(get_weather_infos($cityname,$citycode,1,$unit), $myConfig["language"]);
	
	preg_match("/([\-]{0,1}[0-9]*?)\&deg;([CFcf])/",ereg_replace("\n","",$block["content"]),$match);
	$degree=$match[1];
	$type=$match[2];
	$block["content"]="<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".XOOPS_URL."/modules/snx_weather/style_bloc.php?degree=$degree&type=$type\" />".$block["content"];
	$block["content"].="<center><a href=\"".XOOPS_URL."/modules/snx_weather/\">"._SnX_WEATHER_FORECAST."</a></center>";
    return $block;
}
?>
