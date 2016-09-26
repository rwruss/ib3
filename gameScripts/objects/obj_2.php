<?php
include("./slotFunctions.php");

echo 'This is a resource point (#'.$unitID.') for resource type '.$thisUnit->unitDat[10];

// Get list of available labor at this location
fseek($unitFile, $thisUnit->unitDat[15]*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, 400));
//print_r($cityDat);

echo 'Got data for city #'.$thisUnit->unitDat[15];

// Get list of player units to check if they can work here
$playerObj = new player($pGameID, $unitFile, 400);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//$unitList = new itemSlot($playerObj->get('unitSlot'), $slotFile, 40);
//$farmList = new itemSlot($cityDat[10], $slotFile, 40);
//$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->get('unitSlot'), 40)));


//print_r($unitList);

echo '<script>
	var orders = addDiv("", "stdFloatDiv", document.getElementById("rtPnl"));
	orders.innerHTML = "Gather Rsc";
	orders.addEventListener("click", function () {scrMod("1095,'.$unitID.'")});
	</script>';

//print_r($farmList->slotData);
/*
if (sizeof($unitList->slotData)>0) {
	foreach ($unitList->slotData as $pUnitID) {
		if ($pUnitID > 0) {
			//echo 'Unit '.$pUnitID.'<br>';
			fseek($unitFile, $pUnitID*$defaultBlockSize);
			$pUnitDat = unpack('i*', fread($unitFile, $unitBlockSize));
			//print_r($pUnitDat);
			if ($pUnitDat[4] == 8 && $pUnitDat[12] == $unitDat[15]) {
				if ($pUnitDat[14] != 0) {
					$expList = array_filter(unpack("i*", readSlotData($slotFile, $pUnitDat[14], 40)));
				} else {
					$experience = 0;
				}
				echo '<div>Unit '.$pUnitID.'<br>
				Current Task is '.$pUnitDat[11].'<br>
				Experience (From slot '.$pUnitDat[14].')for this task is: '.$experience.'<br>
				<div onclick="scrMod(\'1044,'.$pUnitID.','.$unitID.'\')">Start work here</div></div>';
			}
			//echo '<div onclick="makeBox(\'unitDetail\', \'1034,'.$unitID.'\', 500, 500, 200, 50);">Unit #'.$unitID.'</div>';
		}
	}
} else {
	echo 'You don\'t have any units available to work here';
}
*/

?>
