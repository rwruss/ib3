<?php
/*
include('./unitClass.php');

$_SESSION['selectedUnit'] = $postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

$thisChar = new char($postVals[1], $unitFile, 400);

if ($thisChar->get('owner') == $pGameID) {

}
else if ($thisChar->get('controller') == $pGameID {

}
else {}
*/
echo 'Character plot options';

// Load plot data
$plotData = explode('<->', file_get_contents($scnPath.'/plots.desc'));
echo '<script>';
for ($i=1; $i<sizeof($plotData); $i++) {
	$plotItem = explode('<-->', $plotData[$i]);
	echo 'charTaskOpt('.$plotItem[0].', "plotContent", "'.$plotItem[1].'");';
}
echo '</script>';
?>
