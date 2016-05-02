<?php

include('./unitClass.php');
include("./slotFunctions.php");
include("./taskFunctions.php");

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

// Create a new plot and save the parameters
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
$taskIndex = fopen($gamePath.'/tasks.tix', 'rb');
$parameters = pack('i*', 0, 0,1,time(),0,0,6,$_SESSION['selectedItem'],$pGameID,2);
$newTask = createTask($taskFile, $taskIndex, 24*60, $parameters, $gamePath, $slotFile); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
fclose($taskFile);
fclose($taskIndex);

// Add the new plot to the player's list of ongoing plots


//$targetUnit = new unit($_SESSION['selectedUnit'], $unitFile);

echo 'Record this task';

print_r($postVals);
fclose($unitFile);

?>
