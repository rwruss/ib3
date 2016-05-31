<?php

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$unitDesc = explode("<->", file_get_contents($scnPath.'/units.desc'));
print_r($unitDesc);
$thisDesc = explode("<-->", $unitDesc[$unitDat[10]]);
$thisUpgrades = explode(",", $thisDesc[9]);

echo 'Unit #'.$postVals[1].', Type 1: '.$unitDat[4].', Type 2: '.$unitDat[10].'<br>';

echo '<script>';
for ($i=0; $i<sizeof(array_filter($thisUpgrades)); $i++) {
	$optDesc = explode("<-->", $unitDesc[$thisUpgrades[$i]]);
	echo '
	var uOpt = addDiv("upgradesContent", "stdFloatDiv", document.getElementById("rtPnl"));
	uOpt.innerHTML = "Upgrade to '.$optDesc[0].' -> '.$optDesc[2].'";
	uOpt.addEventListener("click", scrMod("1070,'.$thisUpgrades[$i].'));';
}
echo '</script>';


?>
