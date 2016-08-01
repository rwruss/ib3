<?php

include("./slotFunctions.php");
include("./cityClass.php");
echo 'This is the garrison at the town<br>';
$cityID = $_SESSION['selectedItem'];
// Verify selection is a town
$unitFile = fopen($gamePath.'/unitDat.dat' ,'r+b');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

if ($cityDat[4] == 1) {

	// Verify credentials to view this town
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

	//$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
	//$approved = array_search($pGameID, $credList);

	$credSlot = new itemSlot($cityDat[19], $slotFile, 40);
	$approved = checkCred($pGameID, $credSlot->slotData);

	// show units or show intelligence for town
	if ($approved) {
		$unitList = array_filter(unpack("N*", readSlotData($slotFile, $cityDat[18], 40)));
		echo 'Show the list';
		print_r($unitList);
		foreach($unitList as $unitID) {
			echo '<div onclick="makeBox(\'cityMan\', \'1028,'.$unitID.'\', 500, 500, 200, 50);">Unit '.$unitID.'</div>';
		}
	} else {
		echo 'You are not authorized to view this information';
	}
} else {
	echo 'This is an invalid selection';
	}

?>
