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

if ($postVals[3] == 1) {
  //Equiping the item to a unit

  // Verify that player has this weapon in his inventory
  $slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
  $pItemList = unpack('i*', readSlotData($slotFile, $playerDat[33], 40));

  // Save the new item to the correct inventory slot
  fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize+64+4*$postVals[2]);
  fwrite($unitFile, pack('i', $postVals[1]));

  echo '
  <script>
  document.getElementById("w'.$postVals[1].'").innerHTML = "Unit '.$_SESSION['selectedUnit'].'";
  </script>';
  fclose($slotFile);
} else {
  // Remove the item from the correct inventory slotFile
  fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize+64+4*$postVals[2]);
  fwrite($unitFile, pack('i', 0));

  // Add to players inventory slot

  // Clear out the icons and descriptions for currently equipped items
  echo '
  <script>
  eqBox = document.getElementById("w'.$postVals[2].'");
  eqBox.innerHTML = "0";
  document.getElementById("eq_tab'.$postVals[2].'").innerHTML = "";
  document.getElementById("eq_header").innerHTML = "";
  //eqBox.addEventListener("click", function() {makeBox("eqItem", "1054,0", 500, 500, 700, 50)});
  </script>';
}

fclose($unitFile);


?>
