<?php

include('./unitClass.php');

//print_r($postVals);
$unitID = $postVals[2]*255+$postVals[3];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
/*
fseek($unitFile, $unitID*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
*/
$thisUnit = loadUnit($unitID, $unitFile, 400);
//print_r($thisUnit->);

$_SESSION['selectedItem'] = $unitID;

if ($thisUnit->get('controller') == $pGameID || $thisUnit->get('controller') == 0) {
	include('../gameScripts/objects/obj_'.$thisUnit->get('uType').'.php');
}
else if ($thisUnit->get('owner') == $pGameID) {
	include('../gameScripts/objects/obj_'.$thisUnit->get('uType').'b.php');
}
else {
	//include('../gameScripts/objects/obj_'.$thisUnit->get('uType').'c.php');
	include('../gameScripts/1096.php');
}
fclose($unitFile);
?>
