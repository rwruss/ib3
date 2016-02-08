<?php

include('./taskFunctions.php');
include("./slotFunctions.php");

$unitList = array_slice($postVals, 1);
// unit Vals are indexes 1+
// Confirm unit controller gave the orders
$unitFile = fopen($gamePath.'/unitDat.dat','rb');
$processOrder = false;



foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*400);
	$unitDat = unpack('i*', fread($unitFile, 400));
	echo 'Owner: '.$unitDat[5].', Controller '.$unitDat[6].'<br>';
	if ($unitDat[5] == $pGameID) {
		echo 'Accept Order';
		$processOrder = true;
		$orderList[] = $unitID;
	}
}

if ($processOrder) {
	echo 'Final list of units to process:';
	print_r($orderList);
	
	// Create a new task to be processed.
	$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
	$taskIndex = fopen($gamePath.'/tasks.tix', 'r+b');
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
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
	
	// Record new task in player's ongoing task list
	addDataToSlot($gamePath."/gameSlots.slt", $playerDat[29], pack("N", $newTask), $slotFile);
	
	// Update units to show the current task in progress
	$taskNumBin = pack('i', $newTask);
	foreach ($orderList as $unitID) {
		fseek($unitFile, $unitID*400+11*4);
		fwrite($unitFile, $taskNumBin);
	}
	fclose($slotFile);
}

fclose($unitFile);

?>