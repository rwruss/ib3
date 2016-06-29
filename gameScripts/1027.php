<?php
include('./slotFunctions.php');
// Get slot for units for this group
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$groupDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Read all units in this group
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//readSlotData($file, $slot_num, $slot_size)
//$unitList = array_filter(unpack("N*", readSlotData($slotFile, $groupDat[14], 40)));
$unitList = new itemSlot($groupDat[14], $slotFile, 40);
fclose($slotFile);

foreach($unitList->slotData as $listUnitID) {
	fseek($unitFile, $listUnitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
}

// Show orders available for this army group based on the unit types present
print_r($unitList->slotData);
?>
