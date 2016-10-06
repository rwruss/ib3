<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');

$thisPlayer = loadPlayer($pGameID, $unitFile, 400);

// Verify that the unit is indeed a city
$playerCity = loadUnit($postVals[1], $unitFile, 400);
echo 'Loaded city ('.$postVals[1].') type:'.get_class($playerCity);


// save as players primary
if (get_class($playerCity) == "settlement") {
  $thisPlayer->save('homeCity', $postVals[1]);
} else {
  echo 'Not a city';
}


fclose($unitFile);

?>
