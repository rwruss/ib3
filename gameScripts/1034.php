<?php

// get unit Data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

print_r($unitDat);

//echo 'Unit Detail for unit #'.$postVals[1];
$_SESSION['selectedItem'] = $postVals[1];
echo 'Unit #'.$postVals[1].', Type '.$unitDat[4].'/'.$unitDat[10].'<br>';
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
