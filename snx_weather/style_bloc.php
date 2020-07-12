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
#weatherBlock {
	width: 99%;
	text-align: center;
}

#weatherBlock p.skyImg, #weatherBlock p.skyText, #weatherBlock p.satImg {
	display: none;
}

#weatherBlock p.val-skyImg, #weatherBlock p.val-skyText {
	background-color: <?= $myStyle["bgBlockImgSky"]; ?>;
	margin-top: 0px;
	margin-bottom: 0px;
	color: <?= $myStyle["textBlockSky"]; ?>;
}

.cityname {
	color: <?= $myStyle["textBlockCityName"]; ?>;
	margin-bottom: 0px;
}

#weatherBlock p.TEMPERATURE, #weatherBlock p.HUMIDITY {
	background-color: <?= $tempColor; ?>;
	margin-top: 0px;
	margin-bottom: 0px;
	color: <?= $tempTextColor; ?>;
}

h2.SnX-Weather {
	text-align: center;
}