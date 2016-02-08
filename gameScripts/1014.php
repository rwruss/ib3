<?php

include("./slotFunctions.php");
/* old stuff
$townFile = fopen($gamePath."/towns.tn", "rb");
fseek($townFile, 2000*$postVals[1]);
$townDat = fread($townFile, 2000);
fclose($townFile);

$townA = unpack("N", $townDat);

$slotFile = fopen($gamePath."/gameSlots.slt", "rb");
$charList = array_filter(unpack("v*", readSlotData($slotFile, $townA[1], 40)));
fclose($slotFile);

for ($i=1; $i<11; $i++) {
	$rank[$i] = array();
	}
for ($i=0; $i<sizeof($charList)/2; $i++) {
	$rank[$charList[$i*2+2]][] = $charList[$i*2+1];
	}

echo "Town detail for town ".$postVals[1]."<br>Check members at slot ".$townA[1]."<p>";
//print_r($rank);
$charFile = fopen($gamePath."/chars.dat", "rb");
$nameFile = fopen("../games/common/names_1.dat", "rb");
for ($i=1; $i<11; $i++) {
	if (sizeof($rank[$i])>0) {
		echo "Rank: ".$i."<br>";
		}
	foreach($rank[$i] as $value) {
		fseek($charFile, 200*$value);
		$charDat = fread($charFile, 200);
		$charDtl = unpack("C*", substr($charDat, 0, 10));
		$charName = unpack("s*", substr($charDat, 20, 6));
		fseek($nameFile, $charName[1]*20);
		$first = trim(fread($nameFile, 20));
		fseek($nameFile, $charName[2]*20);
		$last = trim(fread($nameFile, 20));
		fseek($nameFile, $charName[3]*20);
		$honor = trim(fread($nameFile, 20));
		echo $first." ".$last." ".$honor."<br>";
		}
	}
fclose($charFile);
fclose($nameFile);
*/

?>