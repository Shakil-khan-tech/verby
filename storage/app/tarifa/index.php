<?php 
$lines = file('ZH.txt'); 
$search1 = $_GET[s1]; 
$s2 =  $_GET[s2];
$search2 = "0".$s2."0000";
foreach($lines as $line) { if(stristr($line,$search1) ){$line = substr($line, 25);
$rogaChk=explode('000000', $line);
$rogaChk=$rogaChk[0];
$tatimi=explode('000', $line);
$tatimi=end($tatimi);
if ($rogaChk > $s2){echo $rogaSelekt;
echo "<br>";
echo $tatimiSelekt;
break;
}
$rogaSelekt=$rogaChk;
$tatimiSelekt=$tatimi/100;
}
} 
 ?> 