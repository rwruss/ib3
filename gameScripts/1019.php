<?php
/*
$unitID = $postVals[2]*255+$postVals[3];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $unitID*400);
$unitDat = fread($unitFile, 400);
fclose($unitFile);

$unitInf = unpack('i*', $unitDat);
//print_r(unpack('i*', $unitDat));

echo 'Unit Details for unit '.$unitID.'<br>
	Type: '.$unitInf[4].'<br>
	Owner: '.$unitInf[5].'<br>
	Controller: '.$unitInf[6].'<br>
	Status: '.$unitInf[7].'<br>
	Space: '.$unitInf[8].'<br>
	<div style="position:absolute; bottom:60; left:0;" onclick="setClick(['.$unitID.',2], \'auto\');">Est. RSC Point</div>
	<div style="position:absolute; bottom:60; left:0;" onclick="makeBox(\'unit\', 1020, 500, 500, 200, 50);">Move to Loc</div>
	<div style="position:absolute; bottom:40; left:0;" onclick="setClick(['.$unitID.',1],\'progress\')">Move to Loc</div>
	<div style="position:absolute; bottom:20; left:0;" onclick="scrMod(\'1018,'.$unitID.'\', \'scrBox\');">show Move</div>
	<div style="position:absolute; bottom:0; left:0;">hideMove</div>';
*/
if ($postVals[1] > 36) {
	include('../gameScripts/cl'.$postVals[1].'.php');
	//echo "include something";
} else {
	include('../gameScripts/'.$postVals[9].'.php');
}
?>