<?php
include("./slotFunctions.php");
$unitID = $postVals[1];
echo 'A unit has been clicked ('.$unitID.')';

print_r($postVals);

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $unitID*400);
$unitDat = fread($unitFile, 400);
$unitLoc = unpack('i*', $unitDat);
fclose($unitFile);
$lat = $unitLoc[2]/120;
$long = $unitLoc[1]/120-30;
echo 'Located at '.$unitLoc[1].', '.$unitLoc[2].' = L/L '.$lat.'/'.$long.'<br>';


// Read unit move
$moveFile = fopen($gamePath.'/randomMoveFile.mvf', "rb");
fseek($moveFile, $unitFile*100);
$moveDat = fread($moveFile, 100);

$dirDat = substr($moveDat, 0, 40);
$timeDat = substr($moveDat, 40, 40);

fclose($moveFile);

// Read unit Long Move
$longFile = fopen($gamePath.'/randomLongFile.lmf', 'rb');
$longMoveDat = readSlotDataEndKey($longFile, $unitID*9, 84);
fclose($longFile);

for ($i=0; $i<strlen($longMoveDat)/80; $i++) {
	$dirDat .= substr($longMoveDat, $i*80, 40);
	$timeDat .= substr($longMoveDat, $i*80+40, 40);
}
//print_r($longMove);

$moveDirs = unpack('i*', $dirDat);
$moveTimes = unpack('i*', $timeDat);

echo 'long move size of '.strlen($longMoveDat).'
	<script>loadMove(['.$unitLoc[1].','.$unitLoc[2].'], ['.implode(",", $moveDirs).'], ['.implode(",", $moveTimes).']);</script>';

?>