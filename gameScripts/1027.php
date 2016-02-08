<?php

// Get slot for units for this group
$unitFile = fopen($gamePath./'unitDat.dat', 'rb');
fseek($unitFile, $unitID*400);
$groupDat = unpack('i*', fread($unitFile, 400));

// Read all units in this group
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//readSlotData($file, $slot_num, $slot_size)
$unitList = array_filter(unpack("N*", readSlotData($slotFile, <--index to read-->, 40)));
fclose($slotFile);

foreach($unitList as $listUnitID) {
	fseek($unitFile, $listUnitID*400);
	$unitDat = unpack('i*', fread($unitFile, 400));
}

// Show orders available for this army group based on the unit types present

?>