<?php

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$unitDesc = explode("<->", file_get_contents($scnPath.'/units.desc'));
$thisDesc = explode("<-->", $unitDesc[$unitDat[10]]);
$thisUpgrades = explode(",", $typeDesc[9]);

echo '<script>';
for ($i=0; $i<sizeof(array_filter($thisUpgrades)); $i++) {
	$optDesc = explode("<-->", $unitDesc[$thisUpgrades[$i]]);
	echo '
	var uOpt = addDiv("upgradesContent", "stdFloatDiv", document.getElementById("rtPnl"));
	uOpt.innerHTML = "Upgrade to '.$optDesc.'";
	uOpt.addEventListener("click", scrMod("1070,'.$thisUpgrades[$i].'));';
}
echo '</script>';


?>