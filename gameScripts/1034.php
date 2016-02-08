<?php

// get unit Data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*400);
$unitDat = unpack('i*', fread($unitFile, 400));

echo 'Unit Detail for unit #'.$postVals[1];

if ($unitDat[5] == $pGameID) {
	// Get information for the owner with full and true information
	include("../gameScripts/1034a.php");
} 
else if ($unitDat[6] == $pGameID) {
	// get informaiton for the controller with full and maybe true infomration
	include("../gameScripts/1034b.php");
} else {
	// get information for non-owners/controllers
	include("../gameScripts/1034c.php");
}

fclose($unitFile);

?>