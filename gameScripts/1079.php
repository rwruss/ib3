<?php

include('./unitClass.php');
include("./slotFunctions.php");
include("./taskFunctions.php");

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

// Create a new plot task and save the parameters
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
$taskIndex = fopen($gamePath.'/tasks.tix', 'rb');
$parameters = pack('i*', 0, 0,1,time(),0,0,6,$_SESSION['selectedItem'],$pGameID,2);
$newTask = createTask($taskFile, $parameters); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
fclose($taskFile);
fclose($taskIndex);

// Add the new plot to the player's list of ongoing plots
fseek($unitFile, $pGameID*$defaulBlockSize);
$playerDat = unpack('i*', fread($unitFile, 200));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
if ($playerDat[20] == 0) {
	$newSlot = startASlot($slotFile, $gamePath.'/gameSlots.slt');
	fseek($unitFile, $pGameID*$defaulBlockSize+76);
	fwrite($unitFile, pack('i', $newSlot));
	$playerDat[20] = $newSlot;
}

$plotSlot = new itemSlot($playerDat[20], $slotFile, 40);
fclose($slotFile);

//$targetUnit = new unit($_SESSION['selectedUnit'], $unitFile);

echo 'Record this task';

print_r($postVals);
fclose($unitFile);

?>
