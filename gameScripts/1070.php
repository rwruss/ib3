<?php

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$unitDesc = explode("<->", file_get_contents($scnPath.'/units.desc'));
$thisDesc = explode("<-->", $unitDesc[$unitDat[10]]);
$thisUpgrades = explode(",", $typeDesc[9]);

$upgradeDesc = explode("<-->", $unitDesc[$postVals[1]]);
$upgradeReqs = explode(",", $upgradeDesc[2]);

echo '<script>';
if (array_search($postVals[1], $thisUpgrades) {
	echo 'Can proceed';
	if ($upgradeReqs[1] <= $unitDat[14]) {
		echo 'Process the upgrade';
		fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize+36);
		fwrite($unitFile, pack('i', $postVals[1]));
		echo 'confirmBox ("Upgrade completed", "", 1, "", "", "Ok");';
	} else {
		echo 'confirmBox ("Not enough experience", "", 1, "", "", "Ok");';
	}
} else {
	echo 'confirmBox ("Invalid Selection", "", 1, "", "", "Ok");';
}
echo '</sciprt>';

?>
