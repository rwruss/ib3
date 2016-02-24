<?php
include("./slotFunctions.php");
echo 'Receive a move order<br>';
print_r($postVals);

$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$xDir = [0,-1,0,1,-1,0,1,-1,0,1];
$yDir = [0,1,1,1,0,0,0,-1,-1,-1];
if ($unitDat[5] == $pGameID || $unitDat[6] == $pGameID) {
  $moveList = str_split($postVals[2]);
  //  Calculate current slot & new Slot
  $oldSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);
  $newLoc[1] = $unitDat[1];
  $newLoc[2] = $unitDat[2];

  // Adjust x/y coordinates for unit.
  $moves = sizeof($moveList);
  for ($i=0; $i<$moves; $i++) {
    $newLoc[1] += $xDir[$moveList[$i]];
    $newLoc[2] += $yDir[$moveList[$i]];
    echo 'Step to ('.$newLoc[1].', '.$newLoc[2].')<br>';
  }
  $newSlot = floor($newLoc[2]/120)*120+floor($newLoc[1]/120);

  // if slot has changed, make adjustment
  echo 'Old Slot: '.$oldSlot.', New Slot: '.$newSlot;

  if ($oldSlot != $newSlot && $unitDat[26] != 0) {
    // Remove from old slot file and add to new slot file
    $mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
    $oldDat = array_filter(unpack('i*', readSlotDataEndKey($mapSlotFile, $oldSlot, 404)));
    $newDat = array_filter(unpack('i*', readSlotDataEndKey($mapSlotFile, $newSlot, 404)));

    echo 'Old Slot:<br>';
    print_r($oldDat);

    echo '<p>New Slot<br>';
    print_r($newDat);

    removeFromEndSlot($mapSlotFile, $oldSlot, 404, $postVals[1]); //removeFromEndSlot($slotFile, $startSlot, $slot_size, $targetVal)
    addtoSlotGen($gamePath.'/mapSlotFile.slt', $newSlot, pack('i', $postVals[1]), $mapSlotFile, 404); //addtoSlotGen($gamePath.'/mapSlotFile.slt', $mapSlot, pack('i', $newID), $mapSlotFile, 404);
    fclose($mapSlotFile);
  }
  else if ($unitDat[26] == 0) {
    // Record this unit in the slot file
    $mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
    addtoSlotGen($gamePath.'/mapSlotFile.slt', $newSlot, pack('i', $postVals[1]), $mapSlotFile, 404);
    fclose($mapSlotFile);
  }


  // Record new location of unit
  fseek($unitFile, $postVals[1]*$defaultBlockSize);
  fwrite($unitFile, pack('i*', $newLoc[1], $newLoc[2]));

} else {
  echo 'Not allowed to make this order';
}

fclose($unitFile);


?>
