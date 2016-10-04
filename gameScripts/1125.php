<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$warFile = fopen($gamePath.'/wars.war', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

echo 'Show war status for war '.$postVals[1];

// Verify that the viewing player is an owner of the war
fseek($warFile, $postVals[1]*100);
$warDat = unpack('i*', fread($warFile, 100));

$sideSwith = 1;
if ($warDat[6] == $pGameID) {
  // player is the defender
  $sideSwith = -1;
}

if ($warDat[8]*$sideSwitch >= 100) {
  //You can enforce the demands of the war
  echo 'Enforce your demands'
}



// Show details for the war and the war status

// Show the conditions of ending the war and options as needed

// Submit button

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
