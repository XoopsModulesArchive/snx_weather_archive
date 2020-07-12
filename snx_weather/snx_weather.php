<?
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
?>
<? 
$debug=0;
include_once(XOOPS_ROOT_PATH."/modules/snx_weather/functions.php");

/* ***************************************************** */
/*                  	MAIN				 */
/* ***************************************************** */

function check_cookies(&$cookie_header) {
	global $cookies;

	if($cookies["set-cookie"]) {
             $c=split(";",$cookies["set-cookie"]);
             $cc=split("=",$c[0]);
             $cookie_name=$cc[0];
             $cookie_value=$cc[1];
             $cookie_header=$cookie_name."=".$cookie_value;
        }
}

function get_weather_infos($cityName,&$cityCode,$block=0,$unit="",$whatprefs="",$what="WeatherLocalUndeclared") {
	global $cookies;
	$cookie_header="";
	if($cityCode=="") {
		$what=($whatprefs!=""?$whatprefs:$what);
		$args["PostValues"]=array(
	        "where" => $cityName,
			"what" => $what,
			"lswe" => $cityName,
			"lswa" => $what,
			"search" => "Search",
			"whatprefs" => $whatprefs,
	        );
		echo "<!-- 1 POSTING http://www.weather.com/search/enhanced -->\n";
		$body=posting("http://www.weather.com/search/enhanced","",$args,"Your Search");
		if(!$body) {
	  		return "Error posting 1";
		}																							   
		check_cookies($cookie_header);
	
		$tabBody=split("\n",$body);
		foreach($tabBody as $line) {
			if(preg_match("/\<B\>1\..*?\<A HREF=\"(.*)\/(.*?)\?from=search_city\"\>.*?\<\/A\>/",$line,$match)) {
				$cityCode=eregi_replace("<BR>"," ",$match[2]);
				$infosPath=$match[1];
			}
		}
	}
	echo "<!-- 2 GETTING http://www.weather.com/outlook/travel/businesstraveler/local/$cityCode?from=search_city -->\n";

	$body=getting("http://www.weather.com/outlook/travel/businesstraveler/local/$cityCode?from=search_city","","html",$cookie_header,0);
	if(!$body) {
  		return "Error getting 2";
	}

	check_cookies($cookie_header);

	$body=ereg_replace("\n","",$body);
	if(preg_match("/URL=(.*?)\"/",$body,$match)) {
    		$nextURL=eregi_replace("<BR>"," ",$match[1]);
    	} elseif(preg_match("/HREF=\"(.*?)\"/",$body,$match)) {
		$nextURL=($match[1]);
	}
	if($nextURL) {
	    echo "<!-- 3 GETTING $nextURL -->\n";
	    $body=getting($nextURL,"","Current Conditions",(($unit=="metric")?"UserPreferences=%7C%20%7C%7C%7C%7C1%7C1%7C1%7C1%7C%7C%7C%7C%7C%7C%7C1":$cookie_header),0);
    	    if(!$body) {
    	        return "Error getting 3";
	    }	
	    $nextURL="";
	    check_cookies($cookie_header);

	    $body2=ereg_replace("\n","",$body);
    	    if(preg_match("/URL=(.*?)\"/",$body2,$match)) {
		echo "<!-- URL -->\n";
    		$nextURL=eregi_replace("<BR>"," ",$match[1]);
    	    } elseif(preg_match("/HREF=\"(.*?)\"/",$body2,$match)) {
                $nextURL=($match[1]);
    	    }
	}
	$tabBody=split("\n",$body);
	$weatherInfos=array();
	$state="current";
//	echo "<pre>";	
	for($i=0;$i<count($tabBody);$i++) {
//		echo htmlspecialchars($tabBody[$i])."<br>\n";
//		echo htmlspecialchars($tabBody[$i])."\n";
//		if(ereg("\<\!-- begin loop --\>",$tabBody[$i])) $state="forecast";
		if(preg_match("/mw_propsObj\(('http.*?)\);/",$tabBody[$i],$match)) {
			list($a,$b)=split(",",$match[1]);
			list($a,$satImg)=split("'",$b);
			$weatherInfos["today"]["satImg"]="<img src=\"$satImg\">";		
		}
		
		switch(true) {
			case preg_match("/\<H2 CLASS=\"moduleTitleBarGML\"\>\<B\>Right Now for\<\/B\>\<BR\>(.*?)\<BR\>/",$tabBody[$i],$match):
				if(!$weatherInfos["city"]) $weatherInfos["city"]=$match[1];
				break;
				
			case preg_match("/\<B CLASS=obsTempTextA\>(.*?)\<\/B\>\<BR\>\<B CLASS=obsTextA\>Feels Like\<BR\> (.*?)\<\/B\>\<\/DIV\>\<\/TD\>/",$tabBody[$i],$match):
				$weatherInfos["today"]["Temperature"]=$match[1];
				$weatherInfos["today"]["Feels_like"]=$match[2];
                		break;

			case preg_match("/\<IMG SRC=(.*?) WIDTH=52 HEIGHT=52 BORDER=0 ALT=\>\<BR\>\<B CLASS=obsTextA\>(.*?)\<\/B\>\<\/TD\>/",$tabBody[$i],$match):
				$weatherInfos["today"]["skyText"]=$match[2];
				$weatherInfos["today"]["skyImg"]="<img src=\"".$match[1]."\">";
				break;
				
			case preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>Dew Point\:\<\/td\>/",$tabBody[$i],$match):
				$i=$i+2;
				preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>(.*?)\<\/td\>/",$tabBody[$i],$match);
				$weatherInfos["today"]["Dew_point"]=$match[1];
				break;

			case preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>Humidity\:\<\/td\>/",$tabBody[$i],$match):
				$i=$i+2;
				preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>(.*?)\<\/td\>/",$tabBody[$i],$match);
				$weatherInfos["today"]["Humidity"]=$match[1];
				break;

			case preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>Pressure\:\<\/td\>/",$tabBody[$i],$match):
				$i=$i+3;
				preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>(.*?)\./",$tabBody[$i],$match);
				$pressure=$match[1];
				$i=$i+2;
				preg_match("/(\<IMG SRC=\".*?\"\>)\<\/td\>/",$tabBody[$i],$match);
				$weatherInfos["today"]["Pressure"]=$pressure."".$match[1];
				break;

			case preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>Wind\:\<\/td\>/",$tabBody[$i],$match):
				$i=$i+2;
				preg_match("/\<TD VALIGN=\"top\"  CLASS=\"obsTextA\"\>(.*?)\<\/td\>/",$tabBody[$i],$match);
				$weatherInfos["today"]["Wind"]=$match[1];
				break;	

			default:
				break;					
		}
	}
	echo "<!-- 5 GETTING $nextURL -->\n";
    $body=getting("http://www.weather.com/weather/mpdwcr/tenday?locid=$cityCode&channel=dailytraveler&datapoint=htempdp&adprodname=pif_btrav_tenday_long","","mpd",(($unit=="metric")?"UserPreferences=%7C%20%7C%7C%7C%7C1%7C1%7C1%7C1%7C%7C%7C%7C%7C%7C%7C1":$cookie_header),0);
    if(!$body) {
       return "error getting 5";
    }
	$tabBody=split("\n",$body);
	
	for($i=0;$i<count($tabBody);$i++) {
		if(preg_match("/new mpdFDObj\(new Date\('(.*?)','(.*?)','(.*?)','.*?','.*?','.*?'\),'(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)', '(.*?)'\);/",$tabBody[$i],$match)) {
			$time=mktime(0,0,0,$match[3],$match[2],$match[1]);
			$day=$match[4];
			$hi=$match[5];
			$low=$match[6];
			$icon=$match[8];
			$skyText=$match[9];
			$wind=$match[12];
			$precip=$match[13];
			$date=$day.", ".date("M d",$time);
			$weatherInfos[$date]["skyImg"]="<img src=\"http://image.weather.com/web/common/wxicons/31/$icon.gif\">";
			$weatherInfos[$date]["skyText"]=$skyText;
			$weatherInfos[$date]["Max_/_Min"]="$hi&deg;/$low&deg;";
			$weatherInfos[$date]["Precip."]=$precip."&#37;";
		}
	}
	
	return $weatherInfos;
}
?>
