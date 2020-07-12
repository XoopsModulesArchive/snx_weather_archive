<?
if(eregi("^c$",$type)) $t=$degree;
elseif(eregi("^f$",$type)) $t=($degree-32)*0.5555978;
else $t=0;
if($t<30) $tempTextColor="#000000";
else $tempTextColor="#ffffff";
if($t<0) {
        $pourcentage=100-(($t+40)*100/40);
        $r=128;
        $v=223;
        $b=240;
        $r=$r+((255-$r)*$pourcentage/100);
        $v=$v+((255-$v)*$pourcentage/100);
        $b=$b+((255-$b)*$pourcentage/100);
} elseif($t>=0 && $t<10) {
        $pourcentage=(100-$t*10)/1.8;
        $r=50+((255-50)*$pourcentage/100);
        $v=120+((255-120)*$pourcentage/100);
        $b=200+((255-200)*$pourcentage/100);
} elseif($t>=10 && $t<20) {
        $r=148;
        $v=200;
        $b=128;
        $r=$r+($t*5);
        $v=$v+($t*2);
} elseif($t>=20 && $t<30) {
        $pourcentage=(100-(100-($t-20)*10))/1.8;
        $r=250;
        $v=192;
        $b=107;
        $r=$r-((255-$r)*$pourcentage/100);
        $v=$v-((255-$v)*$pourcentage/100);
        $b=$b-((255-$b)*$pourcentage/100);
} elseif($t>=30) {
        $pourcentage=(100-($t-30)*10)/1.8;
        $r=225;
        $v=6;
        $b=6;
        $r=$r+((255-$r)*$pourcentage/100);
        $v=$v+((255-$v)*$pourcentage/100);
        $b=$b+((255-$b)*$pourcentage/100);
}
$tempColor="#".dechex($r).($v<10?"0":"").dechex($v).($b<10?"0":"").dechex($b);
?>