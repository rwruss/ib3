<?php
//print_r($postVals);
$unitID = $postVals[2]*255+$postVals[3];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $unitID*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

//$unitInf = unpack('i*', $unitDat);
//print_r(unpack('i*', $unitDat));
echo 'Unit '.$unitID.'  - type '.$unitDat[4];


$_SESSION['selectedItem'] = $unitID;
include('../gameScripts/objects/obj_'.$unitDat[4].'.php');
fclose($unitFile);
/*
switch($unitInf[4]) {
	case 1: // a city/town
	echo 'Unit Details for unit '.$unitID.'<br>
		Type: '.$unitInf[4].'<br>
		Owner: '.$unitInf[5].'<br>
		Controller: '.$unitInf[6].'<br>
		Status: '.$unitInf[7].'<br>
		Space: '.$unitInf[8].'<br>
		<div style="position:absolute; bottom:100; left:0;" onclick="makeBox(\'cityProd\', 1022, 500, 500, 200, 50);">Manage Production</div>
		<div style="position:absolute; bottom:120; left:0;" onclick="makeBox(\'cityMan\', 1021, 500, 500, 200, 50);">Characters Present</div>
		<div style="position:absolute; bottom:140; left:0;" onclick="makeBox(\'cityMan\', \'1029,'.$unitID.'\', 500, 500, 200, 50);">City Projects</div>
		<div style="position:absolute; bottom:80; left:0;" onclick="makeBox(\'garrison\', 1042, 500, 500, 200, 50);">Garrison at town</div>
		<div style="position:absolute; bottom:60; left:0;" onclick="makeBox(\'unit\', 1020, 500, 500, 200, 50);">Run an external script</div>
		<div style="position:absolute; bottom:40; left:0;" onclick="setClick(['.$unitID.',1],\'progress\')">Move to Loc</div>
		<div style="position:absolute; bottom:20; left:0;" onclick="scrMod(\'1018,'.$unitID.'\', \'scrBox\');">show Move</div>
		<div style="position:absolute; bottom:160; left:0;" onclick="makeBox(\'unit\', 2001, 500, 500, 200, 50);">Add Resources</div>
		<div style="position:absolute; bottom:0; left:0;">hideMove</div>';
	break;

	case 2:
		echo 'This is a resource point';

	break;

	case 3:
		echo 'This is an army';
	break;
}
*/
?>
