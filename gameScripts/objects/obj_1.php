<?php
// Detail for a town

include("./slotFunctions.php");
include("./cityClass.php");

// Verify that player is authorized to view the resources at this city
$cityID = $_SESSION['selectedItem'];
// Verify that the person giving the order has the proper credintials
//fseek($unitFile, $cityID*$defaultBlockSize);
$thisCity = new city([$cityID, $unitFile, $thisUnit->unitDat]);
//$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $thisCity->cityData[19], 40)));
$approved = checkCred($pGameID, $credList);
//$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>';

if ($approved) {
	echo 'Unit Details for unit '.$unitID.'<br>
	  Type: '.$thisCity->cityData[4].'<br>
	  Owner: '.$thisCity->cityData[5].'<br>
	  Controller: '.$thisCity->cityData[6].'<br>
	  Status: '.$thisCity->cityData[7].'<br>
	  Space: '.$thisCity->cityData[8].'<br>
	  Map Object ID '.$thisCity->cityData[23].'<br>
		Population '.$thisCity->cityData[25].'<br>
		Action Points '.$thisCity->aps().'
	  <div style="position:absolute; bottom:220; left:0;" onclick="makeBox(\'rscSummary\', 1068, 500, 500, 200, 50);">Leave Town</div>
	  <div style="position:absolute; bottom:200; left:0;" onclick="makeBox(\'rscSummary\', 1063, 500, 500, 200, 50);">Show Resources</div>
	  <div style="position:absolute; bottom:180; left:0;" onclick="scrMod(1047);">City Buildings</div>
	  <div style="position:absolute; bottom:160; left:0;" onclick="makeBox(\'unit\', 2001, 500, 500, 200, 50);">Add Resources</div>
	  <div style="position:absolute; bottom:140; left:0;" onclick="scrMod(1029,'.$unitID.');">City Projects</div>
	  <div style="position:absolute; bottom:120; left:0;" onclick="makeBox(\'cityMan\', 1021, 500, 500, 200, 50);">Characters Present</div>
	  <div style="position:absolute; bottom:100; left:0;" onclick="makeBox(\'cityProd\', 1022, 500, 500, 200, 50);">Manage Production</div>


	  <div style="position:absolute; bottom:80; left:0;" onclick="makeBox(\'garrison\', 1042, 500, 500, 200, 50);">Garrison at town</div>
	  <div style="position:absolute; bottom:60; left:0;" onclick="makeBox(\'unit\', 1020, 500, 500, 200, 50);">Run an external script</div>
	  <div style="position:absolute; bottom:40; left:0;" onclick="setClick(['.$unitID.',1],\'progress\')">Move to Loc</div>
	  <div style="position:absolute; bottom:20; left:0;" onclick="scrMod(\'1018,'.$unitID.'\', \'scrBox\');">show Move</div>


	  <div style="position:absolute; bottom:0; left:0;">hideMove</div>';
} else {
	// Show basics about city
	include('../gameScripts/1096.php');
	//echo '<div style="position:absolute; bottom:20; left:0;" onclick="scrMod(\'1096,'.$unitID.'\');">Intelligence</div>';

}


fclose($slotFile);
?>
