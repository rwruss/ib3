<?php

//include('./taskFunctions.php');
include("./slotFunctions.php");

$unitList = array_slice($postVals, 1);
// unit Vals are indexes 1+
// Confirm unit controller gave the orders
$unitFile = fopen($gamePath.'/unitDat.dat','rb');
$processOrder = false;

$forageLocs = [0,0,0];
foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
	echo 'Owner: '.$unitDat[5].', Controller '.$unitDat[6].'<br>';
	
	if ($forageLocs[0] == 0) {
		$forageLocs[0] = $unitDat[1]; // X coordinate
		$forageLocs[1] = $unitDat[2]; // Y coordinate
		$forageLocs[2] = 11; // diameter
	} else {
		if ($forageLocs[0] != $unitDat[1] || $forageLocs[1] != $unitDat[2]) {
			exit('Invalid locations');
		}
	}
	if ($unitDat[5] == $pGameID) {
		echo 'Accept Order';
		$processOrder = true;
		$orderList[] = $unitID;
	} else {
		$processOrder = false;
		break;
	}
}

if ($processOrder) {
	$rowSize = 14400; // (120 degrees x 120 points)
	$leftEdge = $forageLocs[1] - floor($forageLocs[2]/2);
	echo 'Final list of units to process:';
	print_r($orderList);
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
	
	// Load the map and resource data for the affected area
	$terrainDat = "";
	$terrainFile = fopen();
	for ($row = 0; $row<$forageLocs[2]; $row++) {
		fseek($terrainFile, ($forageLocs[1]+$row)*$rowSize+$leftEdge);
		$terrainDat .= fread($terrainFile, $forageLocs[2]);
	}
	
	fclose($terrainFile);
	$terrainInfo = unpack("C*", $terrainDat);
	/*
	
	// Create a new task to be processed.
	$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
	$taskIndex = fopen($gamePath.'/tasks.tix', 'r+b');
	
	$newTask = createTask($taskFile, $taskIndex, 24*60, 0,$gamePath, $slotFile); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
	fclose($taskFile);
	fclose($taskIndex);
	

	// get player Dat for slots
	$playerFile = fopen($gamePath.'/players.plr', 'r+b');
	fseek($playerFile, $pGameID*200);

	$playerDat = unpack('i*', fread($playerFile, 200));
	if ($playerDat[29] == 0) {
		$newSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
		fseek($playerFile, $pGameID*200+112);
		fwrite($playerFile, pack('i', $newSlot));
		$playerDat[29] = $newSlot;
	}
	fclose($playerFile);
	
	// Record new task in player's ongoing task list
	addDataToSlot($gamePath."/gameSlots.slt", $playerDat[29], pack("N", $newTask), $slotFile);

	// Update units to show the current task in progress
	$taskNumBin = pack('i', $newTask);
	foreach ($orderList as $unitID) {
		fseek($unitFile, $unitID*$defaultBlockSize+11*4);
		fwrite($unitFile, $taskNumBin);
	}
	*/
	fclose($slotFile);
}

fclose($unitFile);

?>
