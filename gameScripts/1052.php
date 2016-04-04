<?php

include("./slotFunctions.php");
// Process training the unit type selected.

$unitType = $postVals[2];
echo '<Script>alert("Train unit type '.$unitType.' at city '.$_SESSION['selectedItem'].'");';


$unitDesc = explode('<-->', file_get_contents($gamePath.'/units.desc'));

// Load the unit dat from the parameters file

// Verify that the building prerequisites for this unit are met


// Verify that the resource prerequisites for this unit are met

// Get player information for slot
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, 200));

// Get city information for where the unit is being trained
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, 400));

// create and save the unit information
if (flock($unitFile, LOCK_EX)) {  // acquire an exclusive lock
	clearstatcache();
	$unitIndex = max(1,filesize($gamePath.'/unitDat.dat')/$defaultBlockSize);

  fseek($unitFile, $unitIndex*$defaultBlockSize+396);
  fwrite($unitFile, pack('i', 0));
  flock($unitFile, LOCK_UN); // release the lock  on the player File
}

// Record specifics for this unit
fseek($unitFile, $unitIndex*$defaultBlockSize);
fwrite($unitFile, pack('i*', $cityDat[1], $cityDat[2], 0, 6, $pGameID, $pGameID, 1, 1, 1, $unitType));

fseek($unitFile, $unitIndex*$defaultBlockSize+104);
fwrite($unitFile, pack('i', time()));

// add the unit to the list of units for this player
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
addDataToSlot($gamePath.'/gameSlots.slt', $playerDat[22], pack('i', $unitIndex), $slotFile);

echo 'Unit '.$unitDesc[$unitType*8].' created';

fclose($slotFile);
fclose($unitFile);



?>
