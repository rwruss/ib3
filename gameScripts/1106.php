<?php

print_r($postVals);

include('./unitClass.php');
include('./cityClass.php');
include('./slotFunctions.php');

$srcUnit = loadUnit($postVals[1], $unitFile, 400);
$dstUnit = loadUnit($postVals[2], $unitFile, 400);

// Confirm that units are close enough to each other for the transfer


// Confirm that the army/unit in questions has the resources being transfered.
$passedCheck = 0;
$srcSupply = new itemSlot($srcUnit->get(), $slotFile, 40);
for ($i=3; $i<sizeof($postVals); $i+=2) {
  for ($j=1; $j<sizeof($srcSupply->slotData); $j+=2) {
    if ($postVals[$i] == $srcSupply->slotData[$j] && $postVals[$i+1] <= $srcSupply->slotData[$j+1]) {
      $passedCheck++;
      break;
    }
  }
}

// Confirm that the unit that is receiving the resources has enough space for what is being given.

// Remove the resources from the source listUnitID

// Add the resources to the destination list

?>
