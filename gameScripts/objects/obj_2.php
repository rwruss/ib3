<?php
include("./slotFunctions.php");
include("./cityClass.php");

echo 'This is a resource point (#'.$unitID.') for resource type '.$thisUnit->unitDat[10].' at ('.$thisUnit->unitDat[1].', '.$thisUnit->unitDat[2].')';

// Verify priviedge to look at this site
$cityID = $thisUnit->unitDat[15];
$thisCity = new city([$cityID, $unitFile]);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $thisCity->cityData[19], 40)));
$approved = checkCred($pGameID, $credList);

if (!$approved) {include('../gameScripts/1096.php');}

// Get list of available labor at this location
fseek($unitFile, $thisUnit->unitDat[15]*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, 400));
//print_r($cityDat);

echo 'Got data for city #'.$thisUnit->unitDat[15];

// Get list of player units to check if they can work here
$playerObj = new player($pGameID, $unitFile, 400);


echo '<script>
	var orders = addDiv("", "stdFloatDiv", document.getElementById("rtPnl"));
	orders.innerHTML = "Gather Rsc";
	orders.addEventListener("click", function () {scrMod("1095,'.$unitID.'")});
	</script>';

?>
