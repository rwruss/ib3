<?php

// pVals - item #, slot #, option #
include("./slotFunctions.php");

// Get unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Get player information
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
if ($postVals[3] == 1) {
  //Equiping the item to a unit

  // Verify that player has this weapon in his inventory
  $equipSlot = new itemSlot($playerDat[33], $slotFile, 40);
  //$pItemList = unpack('i*', readSlotData($slotFile, $playerDat[33], 40));
  $itemSpot = array_search($postVals[1], $equipSlot->slotdata);

  if ($itemSpot) { // Item found in inventroy
	// If found, Save the new item to the correct inventory slot
	fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize+64+4*$postVals[2]);
	fwrite($unitFile, pack('i', $postVals[1]));
	
	// Remove item from inventory slot
	$equipSlot.deleteItemAtSpot($itemSpot);
	$equipSlot.save();

	  echo '
	  <script>
	  document.getElementById("w'.$postVals[1].'").innerHTML = "Unit '.$_SESSION['selectedUnit'].'";
	  </script>';
  }
  
} else {
	
	$equipSlot = new itemSlot($playerDat[33], $slotFile, 40);
	
  // Remove the item from the correct equipment location for the unit
  fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize+64+4*$postVals[2]);
  fwrite($unitFile, pack('i', 0));

  // Add to players inventory slot
  $equipSlot.addItem($unitDat[17+$postVals[2]], $slotFile, $gamePath.'/gameSlots.slt');
  $equipSlot.save($slotFile);

  // Clear out the icons and descriptions for currently equipped items
  echo '
  <script>
  eqBox = document.getElementById("w'.$postVals[2].'");
  eqBox.innerHTML = "0";
  document.getElementById("eq_tab'.$postVals[2].'").innerHTML = "";
  document.getElementById("eq_header").innerHTML = "";
  </script>';
}
fclose($slotFile);
fclose($unitFile);


?>
