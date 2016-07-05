<?php
include("./slotFunctions.php");
include("./unitClass.php");
echo 'This is a resource point for resource tyoe '.$unitDat[10];

// Get list of available labor at this location
fseek($unitFile, $unitDat[15]*400);
$cityDat = unpack('i*', fread($unitFile, 400));

echo 'Got data for city #'.$unitDat[15];

// Get list of player units to check if they can work here
$playerObj = new player($pGameID, $unitFile, 400);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->get('unitSlot'), 40)));

print_r($unitList);
if (sizeof($unitList)>0) {
	foreach ($unitList as $pUnitID) {
		fseek($unitFile, $pUnitID*400);
		$pUnitDat = unpack('i*', fread($unitFile, 400));
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
} else {
	echo 'You don\'t have any units available to work here';
}

echo '<script>
	var orders = addDiv("", "stdFloatDiv", document.getElementById("rtPnl"));
	orders.innerHTML = "Gather Rsc";
	orders.addEventListener("click", function () {scrMod("1094,'.$unitID.'")});
	</script>';

?>
