<?php

include('./unitClass.php');

$_SESSION['selectedItem'] = $postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

$thisChar = loadUnit($postVals[1], $unitFile, 400);

if ($thisChar->get('owner') == $pGameID) {
	include('../gameScripts/1074a.php');
}
else if ($thisChar->get('controller') == $pGameID) {
	include('../gameScripts/1074b.php');
}
else {
	//include('../gameScripts/1074c.php');
	include('../gameScripts/1096.php');
}

?>
