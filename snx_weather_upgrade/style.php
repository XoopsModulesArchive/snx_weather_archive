<?
header("Content-type: text/css");
include("degree2color.php");
	if(file_exists("./stylesheet.dat")) {
	  $handle=fopen("./stylesheet.dat","r");
	  $myStyleEncoded="";
  	  while(!feof($handle)) {
		 $myStyleEncoded.=fgets($handle,2048);
	  }
	  fclose($handle);
   	}
	if($myStyleEncoded!="") $myStyle=unserialize($myStyleEncoded);
	unset($myStyleEncoded);
?>
#weatherPage h1, #weatherPage h2, #weatherPage h3, #weatherPage p {
  margin:0;
  background-repeat:no-repeat;
  background-position:left top;
}

.date-today p.day, #separator-SATIMG, #separator-SKYTEXT, #separator-SKYIMG, #weatherPage p.CITY span, #weatherPage p.SKYIMG span, #weatherPage p.SKYTEXT span, #weatherPage p.SATIMG span {
	display: none
}

#weatherPage #separator-TEMPERATURE , #weatherPage #separator-UV_INDEX, #weatherPage #separator-DEW_POINT, #weatherPage #separator-HUMIDITY, #weatherPage #separator-VISIBILITY, #weatherPage #separator-PRESSURE, #weatherPage #separator-WIND, #weatherPage #separator-MAX_-_MIN, #weatherPage #separator-PRECIP-, #weatherPage #separator-FEELS_LIKE {
	color: <?= $myStyle["textSky"]; ?>;
	float: left;
}

#weatherPage p.TEMPERATURE, #weatherPage p.UV_INDEX, #weatherPage p.DEW_POINT, #weatherPage p.HUMIDITY, #weatherPage p.VISIBILITY, #weatherPage p.PRESSURE, #weatherPage p.WIND, #weatherPage p.MAX_-_MIN, #weatherPage p.PRECIP-, #weatherPage p.FEELS_LIKE {
	float: left;
}

.date, .date-today {
	text-align: left;
	position: relative;
}

.date-today {
	height: 510px;
	text-align: center;
	background-color: <?= $myStyle["bgToday"]; ?>;
}

.date-today div.TEMPERATURE, .date-today div.UV_INDEX, .date-today div.DEW_POINT, .date-today div.HUMIDITY, .date-today div.VISIBILITY, .date-today div.PRESSURE, .date-today div.WIND, .date-today div.FEELS_LIKE {
	background-color: #a3c3e6;
	color: <?= $myStyle["textSky"]; ?>;
	width: 436px;
	text-align: left;
	margin-left: auto;
	margin-right: auto;
	padding-left: 4px;
}

.date-today #TEMPERATURE {
	padding-top: 4px;
}

.date-today #WIND {
	padding-bottom: 4px;
}

.date p.val-SKYTEXT {
	margin-top: 8px;
	margin-bottom: 20px;
	font-weight: bold;
}

.date-today p.val-SKYTEXT {
	margin-top: 8px;
	margin-bottom: 20px;
	font-weight: bold;
	font-size: 18px;
	color: <?= $myStyle["textSky"]; ?>;
}

.date {
	width: 20%;
	height: 120px;
	float: left;
	background-color: <?= $myStyle["bgWeather"]; ?>;
	color: <?= $myStyle["textWeather"]; ?>;
}

.CITY {
	text-align: center;
	background-color: <?= $myStyle["bgCityName"]; ?>;
	font-weight: bold;
	height: 23px;
	padding-top: 5px;
	color: <?= $myStyle["textCityName"]; ?>;
}

.date p.day {
	text-align: center;
	background-color: <?= $myStyle["bgForecast"]; ?>;
	font-weight: bold;
	height: 23px;
	padding-top: 5px;
	color: <?= $myStyle["textForecast"]; ?>; 
}

#weatherPage {
	margin-left: auto;
	margin-right: auto;
	width: 98%;
	background-color: #ffffff;
	height: auto;
	font-family: verdana, arial;
}

.date-today p.val-SATIMG {
	margin-top: 3px;
/*	position: absolute;
	left: 250px;
	top: 0px;*/
	text-align: center;
}

.city-select p.city-title, .city-select p.city-form, .city-search p.city-stitle, .city-search p.city-sform {
	text-align: center;
}

.city-select p.city-title {
	color: <?= $myStyle["textSelectCity"]; ?>;
}

.city-search p.city-stitle {
	color: <?= $myStyle["textSearchCity"]; ?>;
}

h2.SnX-Weather {
	text-align: center;
	color: <?= $myStyle["textModule"]; ?>;
}