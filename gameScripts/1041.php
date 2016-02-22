<?php

include("./slotFunctions.php");
echo 'Assign labor to project #'.$postVals[1].'<br>';

// get project information
$unitFile = fopen($gamePath.'/unitDat.dat' ,'r+b');
//$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$taskDat = unpack('i*', fread($unitFile, $jobBlockSize));


// Verify that the person giving the order has the proper credintials

fseek($unitFile, $taskDat[8]*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);

if ($approved) {
	// Get list of labor available at the location of this project
	echo 'List of units available at city #'.$taskDat[8];
	fseek($unitFile, $taskDat[8]*$defaultBlockSize);
	$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

	$unitsHere = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[18], 40)));
	print_r($unitsHere);
}

fclose($unitFile);

?>
