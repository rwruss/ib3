<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$warFile = fopen($gamePath.'/wars.war', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

echo '<script>
useDeskTop.newPane("warOpts");
thisDiv = useDeskTop.getPane("warOpts");';

// Verify that the viewing player is an owner of the war
fseek($warFile, $postVals[1]*100);
$warDat = unpack('i*', fread($warFile, 100));

$sideSwitch = 1;
if ($warDat[6] == $pGameID) {
  // player is the defender
  $sideSwitch = -1;
}



// Show details for the war and the war status

// Show the conditions of ending the war and options as requested
switch($postVals[2]) {
  case 1:
    echo 'textBlob("", thisDiv, "You are choosing to surrender - you will have the following terms enforced upon you. PROCEED?");
    ';
  break;

  case 2:
  echo 'textBlob("", thisDiv, "Offer terms<br>Term 1<br>Term 2<br>Term 3")';
  break;

  case 3:
  if ($warDat[7]*$sideSwitch > 100) {
    echo 'textBlob("", thisDiv, "Can do")';
  } else echo 'textBlob("", thisDiv, "Cant do")';

  break;
}

// Submit button

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>