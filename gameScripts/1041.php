<?php

include("./slotFunctions.php");
echo 'Assign labor to project #'.$postVals[1].'<br>';

// get project information
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
fseek($taskFile, $postVals[1]*200);
$taskDat = unpack('i*', fread($taskFile, 200));
fclose($taskFile);

// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'r+b');
fseek($unitFile, $taskDat[8]*400);
$cityDat = unpack('i*', fread($unitFile, 400));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);

if ($approved) {
	// Get list of labor available at the location of this project
	echo 'List of units available at city #'.$taskDat[8];
	fseek($unitFile, $taskDat[8]);
	$cityDat = unpack('i*', fread($unitFile, 400));
	
	$unitsHere = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[18], 40)));
	print_r($unitsHere);
}



fclose($unitFile);

?>