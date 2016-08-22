<?php
include('./slotFunctions.php');
include('./unitClass.php');
// Get slot for units for this group
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
//fseek($unitFile, $postVals[1]*$defaultBlockSize);
//$groupDat = unpack('i*', fread($unitFile, $unitBlockSize));

$thisGroup = loadUnit($postVals[1], $unitFile, 400);

// Read all units in this group
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//readSlotData($file, $slot_num, $slot_size)
//$unitList = array_filter(unpack("N*", readSlotData($slotFile, $groupDat[14], 40)));
$unitList = new itemSlot($thisGroup->get('unitListSlot'), $slotFile, 40);


foreach($unitList->slotData as $listUnitID) {
	fseek($unitFile, $listUnitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
}

// Show orders available for this army group based on the unit types present

// Load resources carried by this army
if ($thisGroup->get('carrySlot') > 0) {
	$rscSlot = new blockSlot($thisGroup->get('carrySlot'), $slotFile, 40);
	print_r($rscSlot->slotData);
} else {
	echo 'Carrying nothing';
}

echo '<script>
	dropOpt = textBlob("", "rtPnl", "Drop Resources");
	dropOpt.addEventListener("click", function() {scrMod("1105,'.$postVals[1].'")});
	addOpt = textBlob("", "rtPnl", "Add/Drop Units");
	addOpt.addEventListener("click", function() {scrMod("1105,'.$postVals[1].'")});
</script>';

fclose($slotFile);
?>
